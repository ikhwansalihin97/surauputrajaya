<?php

namespace App\Http\Controllers\Surau;

use App\Http\Controllers\Controller;
use App\Models\Surau;
use App\Models\SurauAgmMesyuarat;
use App\Models\SurauAgmSesi;
use App\Models\SurauCommittee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * Mesyuarat Agung Tahunan (AGM) surau.
 *
 * Setiap sesi meliputi dua tahun (cth. 2026/2027) dengan mesyuarat Bil. 1 (2026) dan
 * Bil. 2 (2027). Sesi diwujudkan secara automatik apabila mesyuarat pertamanya direkodkan.
 * Senarai AJK hanya boleh diisi selepas surau mempunyai sekurang-kurangnya satu sesi.
 */
class AgmController extends Controller
{
    public function index()
    {
        $surau = $this->activeSurau();
        $sesiList = $surau->agmSesi()
            ->with(['mesyuarat' => fn ($q) => $q->withCount(['jemputan', 'kehadiran', 'jemputan as jemputan_dihantar_count' => fn ($j) => $j->where('status_hantar', 'SENT')])])
            ->withCount('committees')
            ->get();

        return view('surau.agm.index', compact('surau', 'sesiList'));
    }

    public function create(Request $request)
    {
        $surau = $this->activeSurau();
        $mesyuarat = new SurauAgmMesyuarat([
            'bilangan' => (int) $request->query('bilangan', 1),
        ]);
        $sesiSedia = $this->sesiSedia($surau);

        // Lalai: sesi yang diminta, atau sesi pertama yang boleh dipilih
        $tahunMula = (int) $request->query('sesi', 0);
        if (!$tahunMula || $this->sesiBertindih($sesiSedia, $tahunMula)) {
            // Utamakan sesi sedia ada yang belum lengkap (cth. Bil. 2 belum direkodkan)
            $belumLengkap = collect($sesiSedia)->filter(fn ($bil) => count($bil) < 2)->keys()->sortDesc()->first();
            $tahunMula = $belumLengkap ?? collect(range(now()->year + 1, now()->year - 10))
                ->first(fn ($t) => !$this->sesiBertindih($sesiSedia, $t) && count($sesiSedia[$t] ?? []) < 2)
                ?? now()->year;
        }

        return view('surau.agm.form', compact('surau', 'mesyuarat', 'tahunMula', 'sesiSedia'));
    }

    /** [tahun_mula => [bilangan yang telah direkodkan]] bagi semua sesi surau */
    private function sesiSedia(Surau $surau): array
    {
        return $surau->agmSesi()->with('mesyuarat:id,agm_sesi_id,bilangan')->get()
            ->mapWithKeys(fn ($s) => [$s->tahun_mula => $s->mesyuarat->pluck('bilangan')->map(fn ($b) => (int) $b)->all()])
            ->all();
    }

    /** Sesi bertindih: tahun mula berbeza tetapi dalam lingkungan setahun daripada sesi sedia ada */
    private function sesiBertindih(array $sesiSedia, int $tahunMula): ?int
    {
        foreach (array_keys($sesiSedia) as $t) {
            if ($t !== $tahunMula && abs($t - $tahunMula) <= 1) {
                return $t;
            }
        }
        return null;
    }

    public function store(Request $request)
    {
        $surau = $this->activeSurau();
        $data  = $this->validateMesyuarat($request, $surau);

        $mesyuarat = DB::transaction(function () use ($surau, $data) {
            $adaSesi = $surau->agmSesi()->exists();

            $sesi = SurauAgmSesi::firstOrCreate(
                ['surau_id' => $surau->id, 'tahun_mula' => $data['tahun_mula']],
                ['tahun_tamat' => $data['tahun_mula'] + 1, 'created_by' => Auth::id()]
            );

            // Sesi pertama: AJK sedia ada (sebelum AGM direkodkan) menjadi AJK sesi ini
            if (!$adaSesi) {
                SurauCommittee::where('surau_id', $surau->id)->whereNull('agm_sesi_id')
                    ->update(['agm_sesi_id' => $sesi->id]);
            }

            return $sesi->mesyuarat()->create([
                'bilangan'   => $data['bilangan'],
                'tahun'      => $sesi->tahunBagi($data['bilangan']),
                'tarikh'     => $data['tarikh'],
                'masa'       => $data['masa'],
                'lokasi'     => $data['lokasi'],
                'created_by' => Auth::id(),
            ]);
        });

        $this->log($surau, "Mesyuarat Agung Tahunan Bil. {$mesyuarat->bilangan} ({$mesyuarat->tahun}) sesi {$mesyuarat->sesi->label} direkodkan.");

        // Langkah seterusnya: jemputan mesyuarat
        return redirect()->route('surau.agm.jemputan.index', $mesyuarat)
            ->with('success', "Mesyuarat Agung Tahunan Bil. {$mesyuarat->bilangan} ({$mesyuarat->tahun}) bagi sesi {$mesyuarat->sesi->label} telah direkodkan. Sila tambah dan hantar jemputan mesyuarat.");
    }

    public function edit(SurauAgmMesyuarat $mesyuarat)
    {
        $surau = $this->activeSurau();
        $this->authorizeMesyuarat($surau, $mesyuarat);
        $tahunMula = $mesyuarat->sesi->tahun_mula;
        $sesiSedia = $this->sesiSedia($surau);

        return view('surau.agm.form', compact('surau', 'mesyuarat', 'tahunMula', 'sesiSedia'));
    }

    public function update(Request $request, SurauAgmMesyuarat $mesyuarat)
    {
        $surau = $this->activeSurau();
        $this->authorizeMesyuarat($surau, $mesyuarat);

        // Sesi & bilangan tidak boleh diubah selepas direkodkan
        $request->merge([
            'tahun_mula' => $mesyuarat->sesi->tahun_mula,
            'bilangan'   => $mesyuarat->bilangan,
        ]);
        $data = $this->validateMesyuarat($request, $surau, $mesyuarat);

        $mesyuarat->update([
            'tarikh' => $data['tarikh'],
            'masa'   => $data['masa'],
            'lokasi' => $data['lokasi'],
        ]);

        $this->log($surau, "Mesyuarat Agung Tahunan Bil. {$mesyuarat->bilangan} ({$mesyuarat->tahun}) sesi {$mesyuarat->sesi->label} dikemaskini.");

        return redirect()->route('surau.agm.index')
            ->with('success', "Mesyuarat Bil. {$mesyuarat->bilangan} ({$mesyuarat->tahun}) telah dikemaskini.");
    }

    public function destroy(SurauAgmMesyuarat $mesyuarat)
    {
        $surau = $this->activeSurau();
        $this->authorizeMesyuarat($surau, $mesyuarat);

        $sesi  = $mesyuarat->sesi;
        $label = "Bil. {$mesyuarat->bilangan} ({$mesyuarat->tahun}) sesi {$sesi->label}";

        if ($mesyuarat->minit_path) {
            Storage::disk('public')->delete($mesyuarat->minit_path);
        }
        $mesyuarat->delete();

        // Sesi tanpa mesyuarat dan tanpa AJK tidak perlu disimpan
        if (!$sesi->mesyuarat()->exists() && !$sesi->committees()->exists()) {
            $sesi->delete();
        }

        $this->log($surau, "Mesyuarat Agung Tahunan {$label} dipadam.");

        return redirect()->route('surau.agm.index')->with('success', "Mesyuarat {$label} telah dipadam.");
    }

    private function validateMesyuarat(Request $request, Surau $surau, ?SurauAgmMesyuarat $mesyuarat = null): array
    {
        $maksTahun = now()->year + 1;

        $data = $request->validate([
            'tahun_mula' => ['required', 'integer', 'min:2000', "max:{$maksTahun}"],
            'bilangan'   => ['required', Rule::in([1, 2])],
            'tarikh'     => ['required', 'date'],
            'masa'       => ['required', 'date_format:H:i'],
            'lokasi'     => ['required', 'string', 'max:255'],
        ], [
            'required'          => ':attribute wajib diisi.',
            'integer'           => ':attribute tidak sah.',
            'tahun_mula.min'    => 'Sesi tidak sah.',
            'tahun_mula.max'    => "Sesi tidak boleh melebihi {$maksTahun}/" . ($maksTahun + 1) . '.',
            'in'                => ':attribute tidak sah.',
            'date'              => ':attribute tidak sah.',
            'date_format'       => ':attribute tidak sah (format JJ:MM).',
            'lokasi.max'        => 'Lokasi terlalu panjang.',
        ], [
            'tahun_mula' => 'Sesi',
            'bilangan'   => 'Bilangan mesyuarat',
            'tarikh'     => 'Tarikh mesyuarat',
            'masa'       => 'Masa mesyuarat',
            'lokasi'     => 'Lokasi mesyuarat',
        ]);

        $data['bilangan'] = (int) $data['bilangan'];
        $mula  = (int) $data['tahun_mula'];
        $tahun = $mula + $data['bilangan'] - 1;
        $label = $mula . '/' . ($mula + 1);
        $errors = [];

        // Tarikh mesti dalam tahun mesyuarat (Bil. 1 = tahun mula, Bil. 2 = tahun tamat)
        if ((int) date('Y', strtotime($data['tarikh'])) !== $tahun) {
            $errors['tarikh'] = "Tarikh Mesyuarat Bil. {$data['bilangan']} sesi {$label} mestilah dalam tahun {$tahun}.";
        }

        if (!$mesyuarat) {
            // Sesi tidak boleh bertindih dengan sesi lain (cth. 2026/2027 dan 2027/2028)
            $bertindih = $surau->agmSesi()
                ->where('tahun_mula', '!=', $mula)
                ->whereBetween('tahun_mula', [$mula - 1, $mula + 1])
                ->first();
            if ($bertindih) {
                $errors['tahun_mula'] = "Sesi {$label} bertindih dengan sesi {$bertindih->label} yang telah direkodkan.";
            }

            $sedia = $surau->agmSesi()->where('tahun_mula', $mula)->first();
            if ($sedia && $sedia->mesyuarat()->where('bilangan', $data['bilangan'])->exists()) {
                $errors['bilangan'] = "Mesyuarat Bil. {$data['bilangan']} bagi sesi {$label} telah direkodkan. Sila kemaskini rekod tersebut.";
            }
        }

        if ($errors) {
            throw \Illuminate\Validation\ValidationException::withMessages($errors);
        }

        return $data;
    }

    private function authorizeMesyuarat(Surau $surau, SurauAgmMesyuarat $mesyuarat): void
    {
        abort_unless((int) $mesyuarat->sesi?->surau_id === (int) $surau->id, 403, 'Rekod mesyuarat ini bukan milik surau anda.');
    }

    private function log(Surau $surau, string $message): void
    {
        activity()->useLog('surau')->causedBy(Auth::user())->performedOn($surau)->log("{$message} Surau: '{$surau->nama_surau}'.");
    }

    private function activeSurau(): Surau
    {
        $surau = Auth::user()->activeSurau();

        abort_unless($surau, 404, 'Tiada rekod surau berdaftar di bawah akaun anda.');

        return $surau;
    }
}
