<?php

namespace App\Http\Controllers\Surau;

use App\Http\Controllers\Controller;
use App\Models\Surau;
use App\Models\SurauAgmSesi;
use App\Models\SurauCommittee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Senarai Ahli Jawatankuasa (AJK) surau, dikemaskini terus oleh Pentadbir Surau.
 * Juga mengendalikan pertukaran surau aktif bagi pengguna yang mentadbir beberapa surau.
 */
class JawatankuasaController extends Controller
{
    public function edit(Request $request)
    {
        $surau = $this->activeSurau();
        $sesi  = $this->sesiSemasaAtauLencong($surau);
        if (!$sesi instanceof SurauAgmSesi) {
            return $sesi;
        }

        $surau->load('committees');
        $sesiList = $surau->agmSesi()->get();

        // Sejarah: AJK sesi terdahulu (baca sahaja)
        $sesiDilihat = null;
        if ($request->filled('sesi') && (int) $request->query('sesi') !== $sesi->id) {
            $sesiDilihat = $surau->agmSesi()->with('committees')->findOrFail((int) $request->query('sesi'));
        }

        return view('surau.jawatankuasa', compact('surau', 'sesi', 'sesiList', 'sesiDilihat'));
    }

    public function update(Request $request)
    {
        $surau = $this->activeSurau();
        $sesi  = $this->sesiSemasaAtauLencong($surau);
        if (!$sesi instanceof SurauAgmSesi) {
            return $sesi;
        }

        $wajib   = SurauCommittee::JAWATAN_WAJIB;
        $maksAjk = SurauCommittee::MAKS_AJK;

        $ahli = [
            'nama'   => ['required', 'string', 'max:255'],
            'ic_no'  => ['required', 'regex:/^[0-9]+$/', 'max:12'],
            'no_tel' => ['required', 'regex:/^[0-9]+$/', 'max:30'],
            'alamat' => ['nullable', 'string'],
            'email'  => ['nullable', 'email', 'max:255'],
        ];

        $rules = [
            'utama' => ['required', 'array', 'size:' . count($wajib)],
            'ajk'   => ['nullable', 'array', 'max:' . $maksAjk],
        ];
        foreach ($ahli as $field => $rule) {
            $rules["utama.*.$field"] = $rule;
            $rules["ajk.*.$field"]   = $rule;
        }
        // Setiap jawatan utama mesti ada (seorang bagi setiap jawatan)
        foreach (array_keys($wajib) as $i) {
            $rules["utama.$i"]      = ['required', 'array'];
            $rules["utama.$i.nama"] = $ahli['nama'];
        }

        // Nama medan yang mudah difahami dalam mesej ralat (cth. "Nama Penuh Pengerusi")
        $label = ['nama' => 'Nama Penuh', 'ic_no' => 'No. K/P', 'no_tel' => 'No. Telefon', 'alamat' => 'Alamat', 'email' => 'E-mel'];
        $attributes = [];
        foreach ($wajib as $i => $jawatan) {
            $attributes["utama.$i"] = "Maklumat $jawatan";
            foreach ($label as $field => $text) {
                $attributes["utama.$i.$field"] = "$text $jawatan";
            }
        }
        foreach (array_keys((array) $request->input('ajk', [])) as $n => $i) {
            foreach ($label as $field => $text) {
                $attributes["ajk.$i.$field"] = "$text AJK " . ($n + 1);
            }
        }

        $request->validate($rules, [
            'utama.required' => 'Maklumat Pengerusi, Timbalan Pengerusi, Setiausaha dan Bendahari wajib diisi.',
            'utama.size'     => 'Maklumat Pengerusi, Timbalan Pengerusi, Setiausaha dan Bendahari wajib diisi.',
            'ajk.max'        => "Maksimum {$maksAjk} Ahli Jawatankuasa sahaja dibenarkan.",
            'required'       => ':attribute wajib diisi.',
            'regex'          => ':attribute hanya boleh mengandungi nombor.',
            'email'          => ':attribute tidak sah.',
            'max'            => ':attribute terlalu panjang.',
        ], $attributes);

        $pick = fn (array $row, string $jawatan) => [
            'nama'    => trim($row['nama']),
            'ic_no'   => $row['ic_no'],
            'jawatan' => $jawatan,
            'alamat'  => $row['alamat'] ?? null,
            'no_tel'  => $row['no_tel'],
            'email'   => $row['email'] ?? null,
        ];

        $rows = collect($wajib)
            ->map(fn ($jawatan, $i) => $pick($request->input("utama.$i"), $jawatan))
            ->concat(collect($request->input('ajk', []))->values()->map(fn ($row) => $pick($row, 'AJK')));

        // Hanya AJK sesi semasa diganti; AJK sesi terdahulu dikekalkan sebagai sejarah
        DB::transaction(function () use ($surau, $sesi, $rows) {
            SurauCommittee::where('surau_id', $surau->id)->where('agm_sesi_id', $sesi->id)->delete();
            foreach ($rows as $row) {
                $surau->semuaCommittees()->create($row + ['agm_sesi_id' => $sesi->id]);
            }
        });

        activity()
            ->useLog('surau')
            ->causedBy(Auth::user())
            ->performedOn($surau)
            ->withProperties(['ajk' => $rows->map(fn ($r) => $r['nama'] . ' (' . $r['jawatan'] . ')')->all()])
            ->log("Senarai AJK surau '{$surau->nama_surau}' sesi {$sesi->label} telah dikemaskini ({$rows->count()} orang).");

        // Kembali ke halaman asal (cth. Keputusan Mesyuarat) jika ia laluan AGM dalaman
        $kembali = (string) $request->input('kembali', '');
        $sasaran = str_starts_with($kembali, url('/surau/agm/')) ? $kembali : route('surau.jawatankuasa.edit');

        return redirect()->to($sasaran)
            ->with('success', "Senarai Ahli Jawatankuasa sesi {$sesi->label} telah berjaya dikemaskini.");
    }

    /**
     * Tukar surau aktif bagi pengguna yang mentadbir lebih daripada satu surau.
     */
    public function pilih(Request $request, Surau $surau)
    {
        abort_unless((int) $surau->user_id === (int) Auth::id(), 403, 'Anda bukan Pentadbir Surau bagi surau ini.');

        session(['active_surau_id' => $surau->id]);

        return redirect()->back(fallback: route('dashboard'))
            ->with('success', "Surau aktif ditukar kepada '{$surau->nama_surau}'.");
    }

    /**
     * AJK hanya boleh diisi selepas sesi AGM direkodkan. Pulangkan sesi semasa,
     * atau lencongan ke halaman AGM jika tiada sesi.
     */
    private function sesiSemasaAtauLencong(Surau $surau)
    {
        return $surau->sesiSemasa()
            ?? redirect()->route('surau.agm.index')
                ->with('info', 'Sila rekodkan Mesyuarat Agung Tahunan (AGM) terlebih dahulu sebelum mengisi senarai Ahli Jawatankuasa.');
    }

    private function activeSurau(): Surau
    {
        $surau = Auth::user()->activeSurau();

        abort_unless($surau, 404, 'Tiada rekod surau berdaftar di bawah akaun anda.');

        return $surau;
    }
}
