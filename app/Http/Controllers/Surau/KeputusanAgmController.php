<?php

namespace App\Http\Controllers\Surau;

use App\Http\Controllers\Controller;
use App\Models\Surau;
use App\Models\SurauAgmMesyuarat;
use App\Models\SurauCommittee;
use App\Services\PengesahanAgmService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Keputusan Mesyuarat AGM (selepas kehadiran direkodkan).
 *
 * Bil. 1: minit mesyuarat, senarai AJK sesi dan pilihan Pentadbir Surau.
 * Bil. 2: minit mesyuarat dan laporan kewangan. AJK tidak dikemaskini.
 *
 * Pilihan pentadbir hanya disimpan di sini. Serah tugas dimulakan selepas JAWI
 * mengesahkan keputusan (Langkah 5, lihat PengesahanAgmService).
 */
class KeputusanAgmController extends Controller
{
    public function __construct(private PengesahanAgmService $pengesahan)
    {
    }

    public function index(SurauAgmMesyuarat $mesyuarat)
    {
        $surau = $this->authorizeMesyuarat($mesyuarat);

        if (!$mesyuarat->kehadiran()->exists()) {
            return redirect()->route('surau.agm.kehadiran.index', $mesyuarat)
                ->with('error', 'Sila rekodkan kehadiran mesyuarat terlebih dahulu. Keputusan Mesyuarat dibuka selepas mesyuarat diadakan.');
        }

        $mesyuarat->load(['sesi', 'pengesahanTerkini']);
        $sesiSemasa = $surau->sesiSemasa();
        $sesiAktif = $sesiSemasa?->id === $mesyuarat->agm_sesi_id;
        $bolehUbahAjk = $mesyuarat->bilangan === 1 && $sesiAktif && $mesyuarat->bolehUbah('ajk');
        $surau->load(['committees', 'user']);
        $ajkSesi = SurauCommittee::where('agm_sesi_id', $mesyuarat->agm_sesi_id)->orderBy('id')->get();
        $kekurangan = $mesyuarat->bolehHantarPengesahan() ? $this->pengesahan->kekurangan($mesyuarat, $surau) : [];

        return view('surau.agm.keputusan', compact('surau', 'mesyuarat', 'sesiAktif', 'bolehUbahAjk', 'ajkSesi', 'kekurangan'));
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

        foreach (['fail_minit' => 'minit', 'fail_laporan_kewangan' => 'laporan_kewangan'] as $input => $item) {
            if ($request->hasFile($input) && !$mesyuarat->bolehUbah($item)) {
                return back()->withErrors([$input => 'Dokumen ini telah dihantar untuk pengesahan JAWI dan tidak boleh diganti.']);
            }
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
        foreach (['fail_minit' => 'minit', 'fail_laporan_kewangan' => 'laporan_kewangan'] as $input => $item) {
            if ($request->hasFile($input)) {
                $mesyuarat->tandaDibetulkan($item);
            }
        }

        $senarai = implode(' dan ', $dikemaskini);
        $this->log($surau, $mesyuarat, "{$senarai} dimuat naik");

        return redirect()->route('surau.agm.keputusan.index', $mesyuarat)->with('success', "{$senarai} telah dimuat naik.");
    }

    /** Bil. 1: kekalkan pentadbir semasa atau minta serah tugas kepada ahli AJK */
    public function pentadbir(Request $request, SurauAgmMesyuarat $mesyuarat)
    {
        $surau = $this->authorizeMesyuarat($mesyuarat);
        abort_unless($mesyuarat->bilangan === 1, 403, 'Pemilihan Pentadbir Surau hanya bagi Mesyuarat Bil. 1.');

        if (!$mesyuarat->bolehUbah('pentadbir')) {
            return back()->withErrors(['calon' => 'Pilihan Pentadbir Surau telah dihantar untuk pengesahan JAWI dan tidak boleh diubah.']);
        }

        $request->validate(['calon' => ['required']], ['calon.required' => 'Sila pilih Pentadbir Surau.']);

        if ($request->input('calon') === 'KEKAL') {
            $mesyuarat->update(['pentadbir_pilihan' => 'KEKAL', 'pentadbir_calon' => null]);
            $mesyuarat->tandaDibetulkan('pentadbir');
            $this->log($surau, $mesyuarat, 'Pilihan Pentadbir Surau disimpan (kekal pentadbir semasa)');

            return redirect()->route('surau.agm.keputusan.index', $mesyuarat)
                ->with('success', 'Pilihan disimpan: Pentadbir Surau semasa dikekalkan. Hantar keputusan untuk pengesahan JAWI.');
        }

        // Calon mesti ahli AJK sesi mesyuarat ini
        $ajk = SurauCommittee::where('surau_id', $surau->id)->where('agm_sesi_id', $mesyuarat->agm_sesi_id)
            ->whereKey((int) $request->input('calon'))->first();
        abort_unless($ajk instanceof SurauCommittee, 422, 'Calon tidak ditemui dalam senarai AJK.');

        $emelCalon = strtolower(trim((string) $ajk->email));
        if (!filter_var($emelCalon, FILTER_VALIDATE_EMAIL)) {
            return back()->withErrors(['calon' => "E-mel {$ajk->nama} diperlukan untuk serah tugas Pentadbir Surau. Sila kemaskini senarai AJK terlebih dahulu."]);
        }
        if ($emelCalon === strtolower((string) $surau->user?->email)) {
            return back()->withErrors(['calon' => "{$ajk->nama} ialah Pentadbir Surau semasa. Pilih \"Kekalkan pentadbir semasa\"."]);
        }

        $mesyuarat->update(['pentadbir_pilihan' => 'BAHARU', 'pentadbir_calon' => PengesahanAgmService::salinanCalon($ajk)]);
        $mesyuarat->tandaDibetulkan('pentadbir');
        $this->log($surau, $mesyuarat, "Pilihan Pentadbir Surau disimpan ({$ajk->nama}, {$ajk->jawatan})");

        return redirect()->route('surau.agm.keputusan.index', $mesyuarat)->with('success',
            "Pilihan disimpan: {$ajk->nama} sebagai Pentadbir Surau baharu. Serah tugas akan dihantar kepada calon selepas disahkan oleh JAWI.");
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
