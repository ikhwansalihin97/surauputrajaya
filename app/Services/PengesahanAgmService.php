<?php

namespace App\Services;

use App\Mail\PengesahanAgmMail;
use App\Models\EmailLog;
use App\Models\Surau;
use App\Models\SurauAgmMesyuarat;
use App\Models\SurauAgmPengesahan;
use App\Models\SurauCommittee;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

/**
 * Pengesahan JAWI bagi Keputusan Mesyuarat AGM.
 *
 * 1. Surau melengkapkan Keputusan (minit, AJK, pilihan pentadbir / laporan kewangan) dan menghantar.
 * 2. JAWI meluluskan atau menolak setiap item. Item yang ditolak dibuka semula kepada surau;
 *    item yang diluluskan kekal diluluskan dalam pusingan seterusnya.
 * 3. Apabila semua item diluluskan (DISAHKAN) dan pentadbir baharu dipilih, permintaan serah tugas
 *    barulah dihantar kepada calon. Calon masih perlu log masuk dan menerima.
 */
class PengesahanAgmService
{
    public function __construct(private PertukaranPentadbirService $pertukaran)
    {
    }

    /** Senarai perkara yang belum lengkap sebelum boleh dihantar */
    public function kekurangan(SurauAgmMesyuarat $mesyuarat, Surau $surau): array
    {
        $kurang = [];
        if (!$mesyuarat->minit_path) {
            $kurang[] = 'Minit mesyuarat belum dimuat naik.';
        }
        if ($mesyuarat->bilangan === 2 && !$mesyuarat->laporan_kewangan_path) {
            $kurang[] = 'Laporan kewangan belum dimuat naik.';
        }
        foreach ($mesyuarat->itemBelumDibetulkan() as $i) {
            $kurang[] = SurauAgmPengesahan::ITEM_LABEL[$i] . ' yang ditolak JAWI belum dibetulkan.';
        }
        if ($mesyuarat->bilangan === 1) {
            $ajk = $this->ajkSesi($mesyuarat);
            $adaWajib = collect(SurauCommittee::JAWATAN_WAJIB)->every(fn ($j) => $ajk->contains('jawatan', $j));
            if (!$adaWajib) {
                $kurang[] = 'Senarai AJK sesi ini belum lengkap (Pengerusi, Timbalan Pengerusi, Setiausaha dan Bendahari).';
            }
            if (!$mesyuarat->pentadbir_pilihan) {
                $kurang[] = 'Pilihan Pentadbir Surau belum disimpan.';
            } elseif ($mesyuarat->pentadbir_pilihan === 'BAHARU') {
                $email = strtolower((string) ($mesyuarat->pentadbir_calon['email'] ?? ''));
                if (!$ajk->contains(fn ($c) => strtolower(trim((string) $c->email)) === $email)) {
                    $kurang[] = 'Calon Pentadbir Surau tidak lagi dalam senarai AJK. Sila pilih semula.';
                } elseif ($email === strtolower((string) $surau->user?->email)) {
                    $kurang[] = 'Calon Pentadbir Surau ialah pentadbir semasa. Sila pilih "Kekalkan pentadbir semasa".';
                }
            }
        }

        return $kurang;
    }

    /** Surau menghantar (atau menghantar semula) untuk pengesahan JAWI */
    public function hantar(SurauAgmMesyuarat $mesyuarat, Surau $surau, User $oleh): SurauAgmPengesahan
    {
        return DB::transaction(function () use ($mesyuarat, $surau, $oleh) {
            $mesyuarat = SurauAgmMesyuarat::lockForUpdate()->findOrFail($mesyuarat->id);

            if (!$mesyuarat->bolehHantarPengesahan()) {
                throw ValidationException::withMessages(['pengesahan' => 'Keputusan mesyuarat ini telah dihantar untuk pengesahan JAWI.']);
            }
            if ($kurang = $this->kekurangan($mesyuarat, $surau)) {
                throw ValidationException::withMessages(['pengesahan' => $kurang]);
            }

            // Kemas kini salinan calon daripada baris AJK terkini (baris AJK dicipta semula setiap kali disimpan)
            if ($mesyuarat->pentadbir_pilihan === 'BAHARU') {
                $email = strtolower($mesyuarat->pentadbir_calon['email']);
                $ajk = $this->ajkSesi($mesyuarat)->first(fn ($c) => strtolower(trim((string) $c->email)) === $email);
                $mesyuarat->pentadbir_calon = self::salinanCalon($ajk);
            }

            $lama = $mesyuarat->pengesahanTerkini;
            $item = [];
            foreach ($mesyuarat->itemPengesahan() as $i) {
                $diluluskan = $lama?->status($i) === 'DILULUSKAN';
                $item["{$i}_status"]  = $diluluskan ? 'DILULUSKAN' : 'MENUNGGU';
                $item["{$i}_catatan"] = $diluluskan ? $lama->catatan($i) : null;
            }

            $pusingan = $mesyuarat->pengesahan()->create($item + [
                'pusingan'          => ($lama?->pusingan ?? 0) + 1,
                'pentadbir_pilihan' => $mesyuarat->pentadbir_pilihan,
                'pentadbir_calon'   => $mesyuarat->pentadbir_calon,
                'dihantar_oleh'     => $oleh->id,
                'dihantar_at'       => now(),
            ]);

            $mesyuarat->update(['status_pengesahan' => 'MENUNGGU', 'dihantar_pengesahan_at' => now()]);

            $this->log($surau, $oleh, $mesyuarat, $pusingan->pusingan > 1
                ? "Keputusan dihantar semula untuk pengesahan JAWI (pusingan {$pusingan->pusingan})"
                : 'Keputusan dihantar untuk pengesahan JAWI');

            return $pusingan;
        });
    }

    /**
     * JAWI memutuskan item yang menunggu. $keputusan = [item => ['status' => DILULUSKAN|DITOLAK, 'catatan' => ?string]]
     */
    public function putuskan(SurauAgmMesyuarat $mesyuarat, array $keputusan, User $jawi): SurauAgmMesyuarat
    {
        $mesyuarat = DB::transaction(function () use ($mesyuarat, $keputusan, $jawi) {
            $mesyuarat = SurauAgmMesyuarat::with('sesi.surau.user')->lockForUpdate()->findOrFail($mesyuarat->id);
            abort_unless($mesyuarat->status_pengesahan === 'MENUNGGU', 409, 'Keputusan mesyuarat ini tidak menunggu pengesahan.');

            $pusingan = $mesyuarat->pengesahanTerkini;
            $surau    = $mesyuarat->sesi->surau;

            foreach ($mesyuarat->itemPengesahan() as $i) {
                if ($pusingan->status($i) !== 'MENUNGGU') {
                    continue;
                }
                $pusingan->{"{$i}_status"}  = $keputusan[$i]['status'];
                $pusingan->{"{$i}_catatan"} = trim((string) ($keputusan[$i]['catatan'] ?? '')) ?: null;
            }

            // Pentadbir baharu dipilih daripada senarai AJK; tidak boleh diluluskan jika AJK ditolak
            if ($pusingan->pentadbir_pilihan === 'BAHARU' && $pusingan->ajk_status === 'DITOLAK' && $pusingan->pentadbir_status === 'DILULUSKAN') {
                throw ValidationException::withMessages([
                    'keputusan.pentadbir.status' => 'Pentadbir baharu dipilih daripada senarai AJK. Sila tolak juga Pentadbir Surau jika Senarai AJK ditolak.',
                ]);
            }

            $pusingan->disemak_oleh = $jawi->id;
            $pusingan->disemak_at   = now();
            $pusingan->save();

            $semuaLulus = collect($mesyuarat->itemPengesahan())->every(fn ($i) => $pusingan->status($i) === 'DILULUSKAN');
            $mesyuarat->update(['status_pengesahan' => $semuaLulus ? 'DISAHKAN' : 'PERLU_PEMBETULAN']);

            $ditolak = collect($mesyuarat->itemPengesahan())
                ->filter(fn ($i) => $pusingan->status($i) === 'DITOLAK')
                ->map(fn ($i) => SurauAgmPengesahan::ITEM_LABEL[$i])->implode(', ');
            $this->log($surau, $jawi, $mesyuarat, $semuaLulus ? 'Keputusan disahkan oleh JAWI' : "Keputusan dikembalikan oleh JAWI untuk pembetulan ({$ditolak})");

            if ($semuaLulus && $mesyuarat->bilangan === 1) {
                $this->laksanaPentadbir($mesyuarat, $surau, $pusingan, $jawi);
            }

            return $mesyuarat;
        });

        // No. rujukan & penandatangan surat pentauliahan ditetapkan semasa disahkan
        if ($mesyuarat->bolehCetakPentauliahan()) {
            app(SuratPentauliahanService::class)->pastikanMaklumat($mesyuarat->fresh());
        }

        // Selepas keputusan disimpan: maklumkan Pentadbir Surau semasa
        $this->emelKeputusan($mesyuarat);

        return $mesyuarat;
    }

    /** E-mel status pengesahan kepada Pentadbir Surau semasa */
    public function emelKeputusan(SurauAgmMesyuarat $mesyuarat): bool
    {
        $mesyuarat->load(['sesi.surau.user', 'pengesahanTerkini']);
        $kepada = $mesyuarat->sesi->surau->user?->email;
        if (!filter_var($kepada, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $mail = new PengesahanAgmMail($mesyuarat, $mesyuarat->pengesahanTerkini);
        try {
            Mail::to($kepada)->send($mail);
            $status = 'SENT';
            $error  = null;
        } catch (\Throwable $e) {
            Log::error("Gagal hantar e-mel pengesahan AGM (mesyuarat {$mesyuarat->id}) kepada {$kepada}: " . $e->getMessage());
            $status = 'FAILED';
            $error  = $e->getMessage();
        }

        EmailLog::create([
            'application_id' => null,
            'mail_type'      => 'PENGESAHAN_AGM_' . $mesyuarat->status_pengesahan,
            'to_email'       => json_encode([$kepada]),
            'subject'        => $mail->envelope()->subject,
            'status'         => $status,
            'error_message'  => $error,
            'sent_at'        => $status === 'SENT' ? now() : null,
        ]);

        return $status === 'SENT';
    }

    /**
     * Serah tugas tidak berjaya selepas disahkan (ditolak / dibatalkan / tamat tempoh):
     * buka semula item Pentadbir Surau supaya surau boleh memilih semula dan menghantar semula.
     */
    public function bukaSemulaPentadbir(SurauAgmMesyuarat $mesyuarat, User $oleh, string $sebab): void
    {
        DB::transaction(function () use ($mesyuarat, $oleh, $sebab) {
            $mesyuarat = SurauAgmMesyuarat::with('sesi.surau')->lockForUpdate()->findOrFail($mesyuarat->id);
            abort_unless($mesyuarat->bilangan === 1 && $mesyuarat->status_pengesahan === 'DISAHKAN', 409);

            if ($menunggu = $mesyuarat->sesi->surau->pertukaranMenunggu()) {
                $this->pertukaran->batal($menunggu, $oleh);
            }

            $mesyuarat->pengesahanTerkini->update(['pentadbir_status' => 'DITOLAK', 'pentadbir_catatan' => $sebab]);
            $mesyuarat->update(['status_pengesahan' => 'PERLU_PEMBETULAN']);

            $this->log($mesyuarat->sesi->surau, $oleh, $mesyuarat, 'Pilihan Pentadbir Surau dibuka semula');
        });
    }

    private function laksanaPentadbir(SurauAgmMesyuarat $mesyuarat, Surau $surau, SurauAgmPengesahan $pusingan, User $jawi): void
    {
        $menunggu = $surau->pertukaranMenunggu();

        if ($pusingan->pentadbir_pilihan === 'KEKAL') {
            if ($menunggu) {
                $this->pertukaran->batal($menunggu, $jawi);
            }

            return;
        }

        $calon = $pusingan->pentadbir_calon ?? [];
        $ajk = new SurauCommittee([
            'nama'    => $calon['nama'] ?? '',
            'ic_no'   => $calon['ic_no'] ?? null,
            'jawatan' => $calon['jawatan'] ?? null,
            'no_tel'  => $calon['no_tel'] ?? null,
            'email'   => $calon['email'] ?? null,
        ]);

        // minta() membatalkan permintaan menunggu yang lain dan menghantar e-mel kepada calon
        $peminta = $pusingan->penghantar ?? $jawi;
        $this->pertukaran->minta($surau, $ajk, $peminta, $mesyuarat);
    }

    private function ajkSesi(SurauAgmMesyuarat $mesyuarat)
    {
        return SurauCommittee::where('agm_sesi_id', $mesyuarat->agm_sesi_id)->orderBy('id')->get();
    }

    public static function salinanCalon(SurauCommittee $ajk): array
    {
        return [
            'nama'    => $ajk->nama,
            'ic_no'   => $ajk->ic_no,
            'jawatan' => $ajk->jawatan,
            'no_tel'  => $ajk->no_tel,
            'email'   => strtolower(trim((string) $ajk->email)),
        ];
    }

    private function log(Surau $surau, User $oleh, SurauAgmMesyuarat $mesyuarat, string $mesej): void
    {
        activity()->useLog('surau')->causedBy($oleh)->performedOn($surau)
            ->log("{$mesej} bagi Mesyuarat Agung Tahunan Bil. {$mesyuarat->bilangan} ({$mesyuarat->tahun}) surau '{$surau->nama_surau}'.");
    }
}
