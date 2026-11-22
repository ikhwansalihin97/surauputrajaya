<?php

namespace App\Http\Controllers\Surau;

use App\Http\Controllers\Controller;
use App\Models\Surau;
use App\Models\SurauAgmMesyuarat;
use App\Services\PengesahanAgmService;
use App\Services\PertukaranPentadbirService;
use App\Services\SuratPentauliahanService;
use Illuminate\Support\Facades\Auth;

/**
 * Langkah 5: Status Pengesahan JAWI bagi Keputusan Mesyuarat AGM, dan
 * status serah tugas Pentadbir Surau selepas disahkan.
 */
class PengesahanAgmController extends Controller
{
    public function __construct(
        private PengesahanAgmService $pengesahan,
        private PertukaranPentadbirService $pertukaran,
    ) {
    }

    public function index(SurauAgmMesyuarat $mesyuarat)
    {
        $surau = $this->authorizeMesyuarat($mesyuarat);

        if (!$mesyuarat->status_pengesahan) {
            return redirect()->route('surau.agm.keputusan.index', $mesyuarat)
                ->with('error', 'Hantar Keputusan Mesyuarat untuk pengesahan JAWI terlebih dahulu.');
        }

        $mesyuarat->load(['sesi', 'pengesahan.penghantar', 'pengesahan.penyemak']);
        $surau->load('user');
        $pertukaran = $mesyuarat->bilangan === 1
            ? $mesyuarat->pertukaranPentadbir()->with('kepada')->first()
            : null;
        $bolehPilihSemula = $this->bolehPilihSemula($mesyuarat);

        return view('surau.agm.pengesahan', compact('surau', 'mesyuarat', 'pertukaran', 'bolehPilihSemula'));
    }

    public function hantar(SurauAgmMesyuarat $mesyuarat)
    {
        $surau = $this->authorizeMesyuarat($mesyuarat);
        $pusingan = $this->pengesahan->hantar($mesyuarat, $surau, Auth::user());

        return redirect()->route('surau.agm.pengesahan.index', $mesyuarat)->with('success', $pusingan->pusingan > 1
            ? 'Keputusan mesyuarat telah dihantar semula untuk pengesahan JAWI.'
            : 'Keputusan mesyuarat telah dihantar untuk pengesahan JAWI.');
    }

    /** Surat Pentauliahan AJK (selepas Keputusan Mesyuarat Bil. 1 disahkan JAWI) */
    public function suratPentauliahan(SurauAgmMesyuarat $mesyuarat, SuratPentauliahanService $surat)
    {
        $this->authorizeMesyuarat($mesyuarat);

        return $surat->pdf($mesyuarat)->stream($surat->namaFail($mesyuarat));
    }

    public function hantarSemulaSerahTugas(SurauAgmMesyuarat $mesyuarat)
    {
        $surau = $this->authorizeMesyuarat($mesyuarat);
        $p = $surau->pertukaranMenunggu();
        abort_unless($p && (int) $p->agm_mesyuarat_id === (int) $mesyuarat->id, 404);

        $this->pertukaran->hantarSemula($p);

        return redirect()->route('surau.agm.pengesahan.index', $mesyuarat)
            ->with('success', "Permintaan serah tugas telah dihantar semula ke {$p->calon_email}.");
    }

    /** Batal serah tugas / pilih semula selepas serah tugas ditolak atau tamat tempoh */
    public function pilihSemula(SurauAgmMesyuarat $mesyuarat)
    {
        $this->authorizeMesyuarat($mesyuarat);
        abort_unless($this->bolehPilihSemula($mesyuarat), 409, 'Pilihan Pentadbir Surau tidak boleh dibuka semula.');

        $p = $mesyuarat->pertukaranPentadbir()->first();
        $sebab = match (true) {
            $p?->status === 'DITOLAK'                         => "Serah tugas ditolak oleh {$p->calon_nama}.",
            $p?->status === 'MENUNGGU' && $p->telahTamat()    => "Permintaan serah tugas kepada {$p->calon_nama} telah tamat tempoh.",
            $p?->status === 'MENUNGGU'                        => "Serah tugas kepada {$p->calon_nama} dibatalkan oleh surau.",
            default                                           => 'Serah tugas tidak berjaya.',
        };

        $this->pengesahan->bukaSemulaPentadbir($mesyuarat, Auth::user(), $sebab . ' Sila pilih semula Pentadbir Surau dan hantar untuk pengesahan JAWI.');

        return redirect()->route('surau.agm.keputusan.index', $mesyuarat)->withFragment('pentadbir')
            ->with('success', 'Pilihan Pentadbir Surau dibuka semula. Pilih pentadbir dan hantar semula untuk pengesahan JAWI.');
    }

    /** Selepas disahkan dengan pentadbir baharu, serah tugas belum/tidak diterima */
    private function bolehPilihSemula(SurauAgmMesyuarat $mesyuarat): bool
    {
        if ($mesyuarat->bilangan !== 1 || $mesyuarat->status_pengesahan !== 'DISAHKAN'
            || $mesyuarat->pengesahanTerkini?->pentadbir_pilihan !== 'BAHARU') {
            return false;
        }
        $p = $mesyuarat->pertukaranPentadbir()->first();

        return !$p || in_array($p->status, ['MENUNGGU', 'DITOLAK', 'DIBATALKAN'], true);
    }

    private function authorizeMesyuarat(SurauAgmMesyuarat $mesyuarat): Surau
    {
        $surau = Auth::user()->activeSurau();

        abort_unless($surau && (int) $mesyuarat->sesi?->surau_id === (int) $surau->id, 403, 'Rekod mesyuarat ini bukan milik surau anda.');

        return $surau;
    }
}
