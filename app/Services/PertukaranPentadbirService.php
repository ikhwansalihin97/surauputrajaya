<?php

namespace App\Services;

use App\Mail\PertukaranPentadbirMail;
use App\Models\Application;
use App\Models\EmailLog;
use App\Models\Role;
use App\Models\Surau;
use App\Models\SurauAgmMesyuarat;
use App\Models\SurauCommittee;
use App\Models\SurauPertukaranPentadbir;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Serah tugas Pentadbir Surau.
 *
 * 1. Pentadbir semasa memilih calon daripada senarai AJK. Calon dipadankan melalui e-mel sahaja.
 *    - E-mel sudah berdaftar  : permintaan dipautkan kepada akaun tersebut.
 *    - E-mel belum berdaftar  : akaun dicipta (tanpa peranan) dan e-mel pengaktifan dihantar.
 * 2. Pentadbir semasa kekal mempunyai akses sepanjang permintaan menunggu.
 * 3. Calon log masuk dan menerima serah tugas dalam sistem -> surau dipindahkan; akses pentadbir lama ditarik balik.
 */
class PertukaranPentadbirService
{
    /** Cipta permintaan serah tugas kepada ahli AJK yang dipilih */
    public function minta(Surau $surau, SurauCommittee $ajk, User $peminta, ?SurauAgmMesyuarat $mesyuarat = null): SurauPertukaranPentadbir
    {
        $email = strtolower(trim((string) $ajk->email));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw ValidationException::withMessages([
                'calon' => "E-mel {$ajk->nama} diperlukan untuk serah tugas Pentadbir Surau. Sila kemaskini senarai AJK terlebih dahulu.",
            ]);
        }
        if ($surau->user && strtolower($surau->user->email) === $email) {
            throw ValidationException::withMessages([
                'calon' => "{$ajk->nama} ialah Pentadbir Surau semasa.",
            ]);
        }

        $pertukaran = DB::transaction(function () use ($surau, $ajk, $peminta, $mesyuarat, $email) {
            // Hanya satu permintaan menunggu bagi setiap surau
            foreach ($surau->pertukaranPentadbir()->menunggu()->get() as $lama) {
                $this->tamatkan($lama, 'DIBATALKAN');
            }

            $akaun  = User::whereRaw('LOWER(email) = ?', [$email])->first();
            $baharu = !$akaun;
            $akaun ??= $this->ciptaAkaun($ajk, $email);

            $pertukaran = SurauPertukaranPentadbir::create([
                'surau_id'         => $surau->id,
                'agm_mesyuarat_id' => $mesyuarat?->id,
                'dari_user_id'     => $surau->user_id,
                'kepada_user_id'   => $akaun->id,
                'akaun_baharu'     => $baharu,
                'calon_nama'       => $ajk->nama,
                'calon_ic'         => preg_replace('/\D+/', '', (string) $ajk->ic_no) ?: null,
                'calon_email'      => $akaun->email,
                'calon_no_tel'     => preg_replace('/\D+/', '', (string) $ajk->no_tel) ?: null,
                'calon_jawatan'    => $ajk->jawatan,
                'status'           => 'MENUNGGU',
                'tamat_at'         => now()->addDays(SurauPertukaranPentadbir::TEMPOH_HARI),
                'diminta_oleh'     => $peminta->id,
            ]);

            $this->log($surau, $peminta, "Permintaan serah tugas Pentadbir Surau kepada {$ajk->nama} ({$ajk->jawatan})"
                . ($baharu ? ' dihantar; akaun baharu dicipta.' : ' dihantar.'));

            return $pertukaran;
        });

        $this->emel($pertukaran, 'PERMINTAAN');

        return $pertukaran;
    }

    /** Hantar semula e-mel permintaan dan lanjutkan tempoh */
    public function hantarSemula(SurauPertukaranPentadbir $pertukaran): void
    {
        $pertukaran->update(['tamat_at' => now()->addDays(SurauPertukaranPentadbir::TEMPOH_HARI)]);
        $this->emel($pertukaran, 'PERMINTAAN');
    }

    public function batal(SurauPertukaranPentadbir $pertukaran, User $oleh): void
    {
        DB::transaction(fn () => $this->tamatkan($pertukaran, 'DIBATALKAN'));
        $this->log($pertukaran->surau, $oleh, "Permintaan serah tugas Pentadbir Surau kepada {$pertukaran->calon_nama} dibatalkan.");
    }

    /**
     * Calon (pengguna log masuk) menerima serah tugas.
     */
    public function terima(SurauPertukaranPentadbir $pertukaran, User $calon): void
    {
        DB::transaction(function () use ($pertukaran, $calon) {
            $pertukaran = SurauPertukaranPentadbir::lockForUpdate()->findOrFail($pertukaran->id);
            abort_unless($pertukaran->kepada_user_id === $calon->id, 403);
            abort_unless($pertukaran->status === 'MENUNGGU' && !$pertukaran->telahTamat(), 410, 'Permintaan serah tugas ini tidak lagi sah.');

            $surau = Surau::lockForUpdate()->findOrFail($pertukaran->surau_id);
            $lama  = $surau->user;

            $peranan = Role::firstOrCreate(['slug' => 'surau'], ['name' => 'Pentadbir Surau']);
            $calon->roles()->syncWithoutDetaching([$peranan->id]);

            // Pindah surau kepada pentadbir baharu (akses pentadbir lama ditarik balik di sini)
            $surau->update(['user_id' => $calon->id]);

            // Peranan Pentadbir Surau pentadbir lama ditarik hanya jika tiada lagi surau/permohonan aktif
            if ($lama && $lama->id !== $calon->id) {
                $masihPerlu = $lama->suraus()->exists()
                    || Application::withDrafts()->where('user_id', $lama->id)->whereNotIn('status', ['APPROVED', 'REJECTED'])->exists();
                if (!$masihPerlu) {
                    $lama->roles()->detach($peranan->id);
                }
            }

            $pertukaran->update(['status' => 'DITERIMA', 'diputuskan_at' => now()]);

            $this->log($surau, $calon, "Serah tugas Pentadbir Surau diterima oleh {$calon->name}"
                . ($lama ? " (menggantikan {$lama->name})" : '') . '.');
        });

        $this->emel($pertukaran->fresh(), 'DITERIMA');
    }

    /** Calon (pengguna log masuk) menolak serah tugas */
    public function tolak(SurauPertukaranPentadbir $pertukaran, User $calon): void
    {
        abort_unless($pertukaran->kepada_user_id === $calon->id, 403);
        abort_unless($pertukaran->status === 'MENUNGGU', 410, 'Permintaan serah tugas ini tidak lagi sah.');

        $pertukaran->update(['status' => 'DITOLAK', 'diputuskan_at' => now()]);
        $this->log($pertukaran->surau, $calon, "Serah tugas Pentadbir Surau ditolak oleh {$calon->name}.");
        $this->emel($pertukaran, 'DITOLAK');
    }

    /** PERMINTAAN -> calon; DITERIMA / DITOLAK -> pentadbir lama */
    public function emel(SurauPertukaranPentadbir $pertukaran, string $jenis): bool
    {
        $pertukaran->loadMissing(['surau.masjid', 'dari', 'kepada']);
        $kepada = $jenis === 'PERMINTAAN' ? $pertukaran->calon_email : $pertukaran->dari?->email;
        if (!filter_var($kepada, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $mail = new PertukaranPentadbirMail($pertukaran, $jenis);
        try {
            Mail::to($kepada)->send($mail);
            $status = 'SENT';
            $error  = null;
        } catch (\Throwable $e) {
            Log::error("Gagal hantar e-mel serah tugas pentadbir ({$jenis}) kepada {$kepada}: " . $e->getMessage());
            $status = 'FAILED';
            $error  = $e->getMessage();
        }

        EmailLog::create([
            'application_id' => null,
            'mail_type'      => 'PERTUKARAN_PENTADBIR_' . $jenis,
            'to_email'       => json_encode([$kepada]),
            'subject'        => $mail->envelope()->subject,
            'status'         => $status,
            'error_message'  => $error,
            'sent_at'        => $status === 'SENT' ? now() : null,
        ]);

        return $status === 'SENT';
    }

    /** Akaun baharu (tanpa peranan) untuk calon yang belum berdaftar; diaktifkan melalui pautan e-mel */
    private function ciptaAkaun(SurauCommittee $ajk, string $email): User
    {
        $ic = preg_replace('/\D+/', '', (string) $ajk->ic_no);
        $icBolehGuna = strlen($ic) === 12
            && !User::whereRaw("REPLACE(no_kad_pengenalan, '-', '') = ?", [$ic])->exists();

        return User::create([
            'name'                => $ajk->nama,
            'no_kad_pengenalan'   => $icBolehGuna ? $ic : null,
            'email'               => $email,
            'no_telefon_bimbit'   => preg_replace('/\D+/', '', (string) $ajk->no_tel) ?: null,
            'jawatan'             => $ajk->jawatan,
            'status_perkhidmatan' => 'LAIN_LAIN',
            'password'            => Hash::make(Str::random(40)),
            'is_active'           => true,
            'is_first_login'      => true,
            'activation_token'    => Str::random(64),
        ]);
    }

    /**
     * Tamatkan permintaan menunggu. Akaun yang dicipta khusus untuk permintaan ini dan belum
     * diaktifkan / tiada peranan dipadam supaya tiada akaun tergantung.
     */
    private function tamatkan(SurauPertukaranPentadbir $pertukaran, string $status): void
    {
        $pertukaran->update(['status' => $status, 'diputuskan_at' => now()]);

        // Akaun mungkin dicipta oleh permintaan surau lain (calon yang sama dipilih oleh beberapa surau)
        $akaun = $pertukaran->kepada;
        if (!$akaun || empty($akaun->activation_token)
            || !SurauPertukaranPentadbir::where('kepada_user_id', $akaun->id)->where('akaun_baharu', true)->exists()) {
            return;
        }

        $masihDiguna = $akaun->roles()->exists()
            || $akaun->suraus()->exists()
            || Application::withDrafts()->where('user_id', $akaun->id)->exists()
            || SurauPertukaranPentadbir::where('kepada_user_id', $akaun->id)->menunggu()->exists();

        if (!$masihDiguna) {
            $akaun->delete();
        }
    }

    private function log(Surau $surau, ?User $oleh, string $mesej): void
    {
        $log = activity()->useLog('surau')->performedOn($surau);
        if ($oleh) {
            $log->causedBy($oleh);
        }
        $log->log("{$mesej} Surau: '{$surau->nama_surau}'.");
    }
}
