<?php

namespace App\Http\Controllers\Jawi;

use App\Http\Controllers\Controller;
use App\Models\SurauAgmMesyuarat;
use App\Models\SurauAgmPengesahan;
use App\Models\SurauCommittee;
use App\Services\PengesahanAgmService;
use App\Services\SuratPentauliahanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Pentadbir JAWI mengesahkan Keputusan Mesyuarat AGM surau:
 * minit mesyuarat, senarai AJK dan Pentadbir Surau (Bil. 1); minit dan laporan kewangan (Bil. 2).
 */
class PengesahanAgmController extends Controller
{
    public function __construct(private PengesahanAgmService $pengesahan)
    {
    }

    public function index(Request $request)
    {
        $status = $request->query('status', 'MENUNGGU');
        $status = array_key_exists($status, SurauAgmMesyuarat::STATUS_PENGESAHAN) || $status === 'SEMUA' ? $status : 'MENUNGGU';
        $search = trim((string) $request->query('search'));

        $mesyuarat = SurauAgmMesyuarat::with(['sesi.surau.masjid', 'pengesahanTerkini'])
            ->whereNotNull('status_pengesahan')
            ->when($status !== 'SEMUA', fn ($q) => $q->where('status_pengesahan', $status))
            ->when($search, fn ($q) => $q->whereHas('sesi.surau', fn ($q) => $q->where('nama_surau', 'like', "%{$search}%")))
            ->orderByDesc('dihantar_pengesahan_at')
            ->paginate(20)
            ->withQueryString();

        $bilangan = SurauAgmMesyuarat::whereNotNull('status_pengesahan')
            ->selectRaw('status_pengesahan, count(*) as jumlah')->groupBy('status_pengesahan')
            ->pluck('jumlah', 'status_pengesahan');

        return view('admin.jawi.agm.index', compact('mesyuarat', 'status', 'search', 'bilangan'));
    }

    public function show(SurauAgmMesyuarat $mesyuarat)
    {
        abort_unless($mesyuarat->status_pengesahan, 404);

        $mesyuarat->load(['sesi.surau.masjid', 'sesi.surau.user', 'pengesahan.penghantar', 'pengesahan.penyemak', 'kehadiran']);
        $surau = $mesyuarat->sesi->surau;
        $ajk = SurauCommittee::where('agm_sesi_id', $mesyuarat->agm_sesi_id)->orderBy('id')->get();
        $pusingan = $mesyuarat->pengesahan->first();
        $pertukaran = $mesyuarat->bilangan === 1 ? $mesyuarat->pertukaranPentadbir()->first() : null;

        return view('admin.jawi.agm.show', compact('mesyuarat', 'surau', 'ajk', 'pusingan', 'pertukaran'));
    }

    public function suratPentauliahan(SurauAgmMesyuarat $mesyuarat, SuratPentauliahanService $surat)
    {
        return $surat->pdf($mesyuarat)->stream($surat->namaFail($mesyuarat));
    }

    public function putuskan(Request $request, SurauAgmMesyuarat $mesyuarat)
    {
        abort_unless($mesyuarat->status_pengesahan === 'MENUNGGU', 409, 'Keputusan mesyuarat ini tidak menunggu pengesahan.');

        $pusingan = $mesyuarat->pengesahanTerkini;
        $rules = [];
        $attributes = [];
        foreach ($mesyuarat->itemPengesahan() as $item) {
            if ($pusingan->status($item) !== 'MENUNGGU') {
                continue;
            }
            $rules["keputusan.$item.status"]  = ['required', Rule::in(['DILULUSKAN', 'DITOLAK'])];
            $rules["keputusan.$item.catatan"] = ["required_if:keputusan.$item.status,DITOLAK", 'nullable', 'string', 'max:2000'];
            $attributes["keputusan.$item.status"]  = SurauAgmPengesahan::ITEM_LABEL[$item];
            $attributes["keputusan.$item.catatan"] = 'Catatan ' . SurauAgmPengesahan::ITEM_LABEL[$item];
        }

        $data = $request->validate($rules, [
            'keputusan.*.status.required'     => 'Sila pilih keputusan bagi :attribute.',
            'keputusan.*.catatan.required_if' => ':attribute wajib diisi jika ditolak.',
        ], $attributes);

        $mesyuarat = $this->pengesahan->putuskan($mesyuarat, $data['keputusan'] ?? [], Auth::user());

        $mesej = $mesyuarat->status_pengesahan === 'DISAHKAN'
            ? 'Keputusan mesyuarat telah disahkan.'
              . ($mesyuarat->bilangan === 1 && $mesyuarat->pengesahanTerkini->pentadbir_pilihan === 'BAHARU'
                  ? ' Permintaan serah tugas telah dihantar kepada calon Pentadbir Surau.' : '')
            : 'Keputusan telah dikembalikan kepada surau untuk pembetulan.';
        $emel = $mesyuarat->sesi->surau->user?->email;
        if ($emel) {
            $mesej .= " E-mel status pengesahan telah dihantar kepada Pentadbir Surau ({$emel}).";
        }

        return redirect()->route('admin.jawi.agm.show', $mesyuarat)->with('success', $mesej);
    }
}
