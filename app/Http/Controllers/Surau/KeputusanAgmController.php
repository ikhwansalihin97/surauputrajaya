<?php

namespace App\Http\Controllers\Surau;

use App\Http\Controllers\Controller;
use App\Models\Surau;
use App\Models\SurauAgmMesyuarat;
use App\Models\SurauCommittee;
use App\Services\PertukaranPentadbirService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Keputusan Mesyuarat AGM (selepas kehadiran direkodkan).
 *
 * Bil. 1: minit mesyuarat, senarai AJK sesi dan pilihan Pentadbir Surau (serah tugas).
 * Bil. 2: minit mesyuarat dan laporan kewangan. AJK tidak dikemaskini.
 */
class KeputusanAgmController extends Controller
{
    public function __construct(private PertukaranPentadbirService $pertukaran)
    {
    }

    public function index(SurauAgmMesyuarat $mesyuarat)
    {
        $surau = $this->authorizeMesyuarat($mesyuarat);

        if (!$mesyuarat->kehadiran()->exists()) {
            return redirect()->route('surau.agm.kehadiran.index', $mesyuarat)
                ->with('error', 'Sila rekodkan kehadiran mesyuarat terlebih dahulu. Keputusan Mesyuarat dibuka selepas mesyuarat diadakan.');
        }

        $mesyuarat->load('sesi');
        $sesiSemasa = $surau->sesiSemasa();
        $bolehUbahAjk = $mesyuarat->bilangan === 1 && $sesiSemasa?->id === $mesyuarat->agm_sesi_id;
        $surau->load(['committees', 'user']);
        $pertukaranMenunggu = $surau->pertukaranMenunggu();

        return view('surau.agm.keputusan', compact('surau', 'mesyuarat', 'bolehUbahAjk', 'pertukaranMenunggu'));
    }

    /** Muat naik minit (Bil. 1 & 2) dan laporan kewangan (Bil. 2) */
    public function dokumen(Request $request, SurauAgmMesyuarat $mesyuarat)
    {
        $surau = $this->authorizeMesyuarat($mesyuarat);
        $bil2  = $mesyuarat->bilangan === 2;

        $request->validate([
            'fail_minit'            => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'fail_laporan_kewangan' => [$bil2 ? 'nullable' : 'prohibited', 'file', 'mimes:pdf', 'max:10240'],
        ], [
            'mimes'      => ':attribute mestilah fail PDF.',
            'max'        => 'Saiz :attribute tidak boleh melebihi 10MB.',
            'prohibited' => 'Laporan kewangan hanya bagi Mesyuarat Bil. 2.',
        ], [
            'fail_minit'            => 'Minit mesyuarat',
            'fail_laporan_kewangan' => 'Laporan kewangan',
        ]);

        if (!$request->hasFile('fail_minit') && !$request->hasFile('fail_laporan_kewangan')) {
            return back()->withErrors(['fail_minit' => 'Sila pilih fail untuk dimuat naik.']);
        }

        $dikemaskini = [];
        foreach (['fail_minit' => ['minit_path', 'agm_minit', 'Minit mesyuarat'], 'fail_laporan_kewangan' => ['laporan_kewangan_path', 'agm_laporan_kewangan', 'Laporan kewangan']] as $input => [$kolum, $folder, $label]) {
            if ($request->hasFile($input)) {
                if ($mesyuarat->{$kolum}) {
                    Storage::disk('public')->delete($mesyuarat->{$kolum});
                }
                $mesyuarat->{$kolum} = $request->file($input)->store($folder, 'public');
                $dikemaskini[] = $label;
            }
        }
        $mesyuarat->save();

        $senarai = implode(' dan ', $dikemaskini);
        $this->log($surau, $mesyuarat, "{$senarai} dimuat naik");

        return redirect()->route('surau.agm.keputusan.index', $mesyuarat)->with('success', "{$senarai} telah dimuat naik.");
    }

    /** Bil. 1: kekalkan pentadbir semasa atau minta serah tugas kepada ahli AJK */
    public function pentadbir(Request $request, SurauAgmMesyuarat $mesyuarat)
    {
        $surau = $this->authorizeMesyuarat($mesyuarat);
        abort_unless($mesyuarat->bilangan === 1, 403, 'Pemilihan Pentadbir Surau hanya bagi Mesyuarat Bil. 1.');

        $request->validate(['calon' => ['required']], ['calon.required' => 'Sila pilih Pentadbir Surau.']);

        if ($request->input('calon') === 'KEKAL') {
            if ($menunggu = $surau->pertukaranMenunggu()) {
                $this->pertukaran->batal($menunggu, Auth::user());
            }
            $this->log($surau, $mesyuarat, 'Pentadbir Surau dikekalkan');

            return redirect()->route('surau.agm.keputusan.index', $mesyuarat)
                ->with('success', 'Pentadbir Surau semasa dikekalkan.');
        }

        // Calon mesti ahli AJK sesi semasa surau ini
        $ajk = $surau->committees()->whereKey((int) $request->input('calon'))->first();
        abort_unless($ajk instanceof SurauCommittee, 422, 'Calon tidak ditemui dalam senarai AJK.');

        $emelCalon = strtolower(trim((string) $ajk->email));
        if ($emelCalon !== '' && $emelCalon === strtolower((string) $surau->user?->email)) {
            return redirect()->route('surau.agm.keputusan.index', $mesyuarat)
                ->with('success', "{$ajk->nama} ialah Pentadbir Surau semasa. Tiada perubahan dibuat.");
        }

        $p = $this->pertukaran->minta($surau, $ajk, Auth::user(), $mesyuarat);

        return redirect()->route('surau.agm.keputusan.index', $mesyuarat)->with('success',
            ($p->akaun_baharu
                ? "Akaun baharu telah dicipta untuk {$p->calon_nama} dan e-mel pengaktifan dihantar ke {$p->calon_email}."
                : "Permintaan serah tugas telah dihantar ke {$p->calon_email}. {$p->calon_nama} perlu log masuk untuk menerima.")
            . ' Anda kekal sebagai Pentadbir Surau sehingga serah tugas diterima.');
    }

    public function hantarSemula(SurauAgmMesyuarat $mesyuarat)
    {
        $surau = $this->authorizeMesyuarat($mesyuarat);
        $p = $surau->pertukaranMenunggu();
        abort_unless($p, 404);

        $this->pertukaran->hantarSemula($p);

        return redirect()->route('surau.agm.keputusan.index', $mesyuarat)
            ->with('success', "Permintaan serah tugas telah dihantar semula ke {$p->calon_email}.");
    }

    public function batal(SurauAgmMesyuarat $mesyuarat)
    {
        $surau = $this->authorizeMesyuarat($mesyuarat);
        $p = $surau->pertukaranMenunggu();
        abort_unless($p, 404);

        $this->pertukaran->batal($p, Auth::user());

        return redirect()->route('surau.agm.keputusan.index', $mesyuarat)
            ->with('success', "Permintaan serah tugas kepada {$p->calon_nama} telah dibatalkan.");
    }

    private function authorizeMesyuarat(SurauAgmMesyuarat $mesyuarat): Surau
    {
        $surau = Auth::user()->activeSurau();

        abort_unless($surau && (int) $mesyuarat->sesi?->surau_id === (int) $surau->id, 403, 'Rekod mesyuarat ini bukan milik surau anda.');

        return $surau;
    }

    private function log(Surau $surau, SurauAgmMesyuarat $mesyuarat, string $message): void
    {
        activity()->useLog('surau')->causedBy(Auth::user())->performedOn($surau)
            ->log("{$message} bagi Mesyuarat Agung Tahunan Bil. {$mesyuarat->bilangan} ({$mesyuarat->tahun}) surau '{$surau->nama_surau}'.");
    }
}
