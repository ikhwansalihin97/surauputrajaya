<?php

namespace App\Http\Controllers\Surau;

use App\Http\Controllers\Controller;
use App\Models\Surau;
use App\Models\SurauAgmKehadiran;
use App\Models\SurauAgmMesyuarat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Rekod kehadiran Mesyuarat Agung Tahunan.
 * Kehadiran boleh ditambah secara manual (nama & e-mel) atau terus daripada senarai jemputan.
 */
class KehadiranAgmController extends Controller
{
    public function index(SurauAgmMesyuarat $mesyuarat)
    {
        $surau = $this->authorizeMesyuarat($mesyuarat);
        $mesyuarat->load(['sesi', 'jemputan', 'kehadiran']);

        // Jemputan yang belum direkodkan hadir (dipadankan melalui pautan jemputan atau e-mel)
        $hadirJemputanIds = $mesyuarat->kehadiran->pluck('agm_jemputan_id')->filter()->all();
        $hadirEmel = $mesyuarat->kehadiran->pluck('email')->filter()->map(fn ($e) => strtolower($e))->all();
        $jemputanBelumHadir = $mesyuarat->jemputan
            ->reject(fn ($j) => in_array($j->id, $hadirJemputanIds) || in_array(strtolower($j->email), $hadirEmel, true))
            ->values();

        return view('surau.agm.kehadiran', compact('surau', 'mesyuarat', 'jemputanBelumHadir'));
    }

    /** Tambah kehadiran secara manual */
    public function store(Request $request, SurauAgmMesyuarat $mesyuarat)
    {
        $surau = $this->authorizeMesyuarat($mesyuarat);

        $rows = collect($request->input('kehadiran', []))
            ->filter(fn ($r) => filled($r['nama'] ?? null) || filled($r['email'] ?? null))
            ->map(fn ($r) => [
                'nama'  => trim((string) ($r['nama'] ?? '')),
                'email' => filled($r['email'] ?? null) ? strtolower(trim((string) $r['email'])) : null,
            ])
            ->values();
        $request->merge(['kehadiran' => $rows->all()]);

        $attributes = [];
        foreach ($rows as $i => $r) {
            $attributes["kehadiran.$i.nama"]  = 'Nama (baris ' . ($i + 1) . ')';
            $attributes["kehadiran.$i.email"] = 'E-mel (baris ' . ($i + 1) . ')';
        }

        $request->validate([
            'kehadiran'         => ['required', 'array', 'min:1', 'max:200'],
            'kehadiran.*.nama'  => ['required', 'string', 'max:255'],
            'kehadiran.*.email' => ['nullable', 'email', 'max:255', 'distinct'],
        ], [
            'kehadiran.required' => 'Sila isi sekurang-kurangnya seorang yang hadir.',
            'kehadiran.max'      => 'Maksimum 200 orang bagi setiap simpanan.',
            'required'           => ':attribute wajib diisi.',
            'email'              => ':attribute tidak sah.',
            'distinct'           => ':attribute berulang dalam senarai.',
            'max'                => ':attribute terlalu panjang.',
        ], $attributes);

        $emel = $rows->pluck('email')->filter();
        $sedia = $emel->isEmpty() ? [] : $mesyuarat->kehadiran()->whereIn('email', $emel)->pluck('email')->all();
        if ($sedia) {
            $errors = [];
            foreach ($rows as $i => $r) {
                if ($r['email'] && in_array($r['email'], $sedia, true)) {
                    $errors["kehadiran.$i.email"] = "{$r['email']} telah direkodkan hadir.";
                }
            }
            throw ValidationException::withMessages($errors);
        }

        // Pautkan kepada jemputan jika e-mel sepadan
        $jemputan = $mesyuarat->jemputan()->whereIn('email', $emel)->pluck('id', 'email');

        DB::transaction(function () use ($mesyuarat, $rows, $jemputan) {
            foreach ($rows as $r) {
                $mesyuarat->kehadiran()->create($r + [
                    'agm_jemputan_id' => $r['email'] ? ($jemputan[$r['email']] ?? null) : null,
                    'created_by'      => Auth::id(),
                ]);
            }
        });

        $this->log($surau, $mesyuarat, "{$rows->count()} kehadiran direkodkan");

        return redirect()->route('surau.agm.kehadiran.index', $mesyuarat)
            ->with('success', "{$rows->count()} orang telah direkodkan hadir.");
    }

    /** Tambah kehadiran terus daripada senarai jemputan */
    public function storeDariJemputan(Request $request, SurauAgmMesyuarat $mesyuarat)
    {
        $surau = $this->authorizeMesyuarat($mesyuarat);

        $request->validate(['jemputan_ids' => ['required', 'array', 'min:1'], 'jemputan_ids.*' => ['integer']], [
            'jemputan_ids.required' => 'Sila pilih sekurang-kurangnya seorang jemputan.',
        ]);

        $sediaJemputan = $mesyuarat->kehadiran()->whereNotNull('agm_jemputan_id')->pluck('agm_jemputan_id')->all();
        $sediaEmel = $mesyuarat->kehadiran()->whereNotNull('email')->pluck('email')->map(fn ($e) => strtolower($e))->all();

        $senarai = $mesyuarat->jemputan()->whereIn('id', $request->input('jemputan_ids'))->get()
            ->reject(fn ($j) => in_array($j->id, $sediaJemputan) || in_array(strtolower($j->email), $sediaEmel, true));

        DB::transaction(function () use ($mesyuarat, $senarai) {
            foreach ($senarai as $j) {
                $mesyuarat->kehadiran()->create([
                    'agm_jemputan_id' => $j->id,
                    'nama'            => $j->nama,
                    'email'           => strtolower($j->email),
                    'created_by'      => Auth::id(),
                ]);
            }
        });

        $this->log($surau, $mesyuarat, "{$senarai->count()} kehadiran direkodkan daripada senarai jemputan");

        return redirect()->route('surau.agm.kehadiran.index', $mesyuarat)->with(
            $senarai->isEmpty() ? 'error' : 'success',
            $senarai->isEmpty() ? 'Jemputan yang dipilih telah direkodkan hadir.' : "{$senarai->count()} jemputan telah direkodkan hadir."
        );
    }

    public function destroy(SurauAgmMesyuarat $mesyuarat, SurauAgmKehadiran $kehadiran)
    {
        $surau = $this->authorizeMesyuarat($mesyuarat);
        abort_if((int) $kehadiran->agm_mesyuarat_id !== (int) $mesyuarat->id, 404);

        $kehadiran->delete();
        $this->log($surau, $mesyuarat, "Kehadiran {$kehadiran->nama} dibuang");

        return redirect()->route('surau.agm.kehadiran.index', $mesyuarat)
            ->with('success', "{$kehadiran->nama} telah dibuang daripada rekod kehadiran.");
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
