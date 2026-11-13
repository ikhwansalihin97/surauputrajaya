<?php

namespace App\Http\Controllers\Surau;

use App\Http\Controllers\Controller;
use App\Models\Masjid;
use App\Models\Surau;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Kemaskini terus maklumat rasmi surau oleh Pentadbir Surau.
 *
 * Perubahan disimpan terus ke jadual suraus (tanpa kelulusan) dan direkodkan
 * dalam log aktiviti. Jadual applications tidak disentuh.
 */
class MaklumatController extends Controller
{
    public function edit()
    {
        $surau   = $this->ownedSurau();
        $masjids = Masjid::where('is_active', true)->orderBy('nama_masjid')->get();

        return view('surau.maklumat', compact('surau', 'masjids'));
    }

    public function update(Request $request)
    {
        $surau = $this->ownedSurau();

        $data = $request->validate([
            'nama_surau'              => ['required', 'string', 'max:255'],
            'kategori_surau'          => ['required', Rule::in(['Surau Jumaat', 'Surau Komuniti'])],
            'masjid_id'               => ['required', 'exists:masjids,id'],
            'no_tel_surau'            => ['nullable', 'regex:/^[0-9]+$/', 'max:30'],
            'alamat_surau'            => ['required', 'string'],
            'alamat_surat'            => ['required', 'string'],
            'kawasan_surau'           => ['required', 'string', 'max:255'],
            'kawasan_lain'            => ['nullable', 'required_if:kawasan_surau,Lain-lain', 'string', 'max:255'],
            'jarak_surau_berdekatan'  => ['required', 'numeric', 'min:0', 'max:999'],
            'nama_surau_berdekatan'   => ['required', 'string', 'max:255'],
            'taraf_tapak'             => ['required', 'string', 'max:255'],
            'no_lot_geran'            => ['nullable', 'string', 'max:255'],
            'jenis_bangunan'          => ['required', 'string', 'max:255'],
            'tingkat_bangunan'        => ['required', 'string', 'max:255'],
            'tahun_didirikan'         => ['required', 'integer', 'min:1800', 'max:' . date('Y')],
            'jumlah_perbelanjaan'     => ['required', 'numeric', 'min:0'],
            'nama_bank'               => ['required', 'string', 'max:255'],
            'no_akaun_bank'           => ['required', 'regex:/^[0-9]+$/', 'max:30'],
            'alamat_bank'             => ['required', 'string'],
            'penama_akaun'            => ['required', 'string'],
            'sumber_kewangan'         => ['nullable', 'array'],
            'sumber_kewangan.*'       => ['nullable', 'string', 'max:255'],
            'penduduk_lelaki'         => ['required', 'integer', 'min:0'],
            'penduduk_perempuan'      => ['required', 'integer', 'min:0'],
            'kelas_pengajian_sebulan' => ['nullable', 'integer', 'min:0'],
            'ceramah_sebulan'         => ['nullable', 'integer', 'min:0'],
            'kelas_kanak_sebulan'     => ['nullable', 'integer', 'min:0'],
        ], [
            'no_tel_surau.regex'       => 'No. telefon surau hanya boleh mengandungi nombor.',
            'no_akaun_bank.regex'      => 'No. akaun bank hanya boleh mengandungi nombor.',
            'kawasan_lain.required_if' => 'Sila nyatakan butiran tapak bagi kawasan Lain-lain.',
        ]);

        $data['sumber_kewangan'] = array_values(array_filter($data['sumber_kewangan'] ?? [], fn ($v) => trim((string) $v) !== ''));
        if ($data['kawasan_surau'] !== 'Lain-lain') {
            $data['kawasan_lain'] = null;
        }
        foreach (['kelas_pengajian_sebulan', 'ceramah_sebulan', 'kelas_kanak_sebulan'] as $field) {
            $data[$field] = (int) ($data[$field] ?? 0);
        }

        $surau->fill($data);

        if (!$surau->isDirty()) {
            return redirect()->route('surau.maklumat.edit')
                ->with('success', 'Tiada perubahan untuk disimpan.');
        }

        $surau->save();

        return redirect()->route('surau.maklumat.edit')
            ->with('success', 'Maklumat surau telah berjaya dikemaskini.');
    }

    private function ownedSurau(): Surau
    {
        $surau = Auth::user()->activeSurau();

        abort_unless($surau, 404, 'Tiada rekod surau berdaftar di bawah akaun anda.');

        return $surau;
    }
}
