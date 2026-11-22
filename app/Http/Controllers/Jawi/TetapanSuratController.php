<?php

namespace App\Http\Controllers\Jawi;

use App\Http\Controllers\Controller;
use App\Models\Tetapan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Tetapan surat rasmi JAWI: kepala surat (pilihan), no. rujukan fail, salinan kepada dan pautan garis panduan.
 */
class TetapanSuratController extends Controller
{
    private const KUNCI = ['surat.no_rujukan_fail', 'surat.bil_seterusnya', 'surat.pautan_garis_panduan', 'surat.salinan_kepada'];

    public function edit()
    {
        $tetapan = collect(self::KUNCI)->mapWithKeys(fn ($k) => [$k => Tetapan::ambil($k)])->all();
        $kepalaSurat = Tetapan::ambil('surat.kepala_surat_path');

        return view('admin.jawi.tetapan-surat', compact('tetapan', 'kepalaSurat'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'no_rujukan_fail'      => ['required', 'string', 'max:100'],
            'bil_seterusnya'       => ['required', 'integer', 'min:1', 'max:999999'],
            'pautan_garis_panduan' => ['nullable', 'url', 'max:255'],
            'salinan_kepada'       => ['nullable', 'string', 'max:3000'],
            'kepala_surat'         => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:4096'],
            'buang_kepala_surat'   => ['nullable', 'boolean'],
        ], [], [
            'no_rujukan_fail'      => 'No. rujukan fail',
            'bil_seterusnya'       => 'Bilangan seterusnya',
            'pautan_garis_panduan' => 'Pautan garis panduan',
            'salinan_kepada'       => 'Salinan kepada',
            'kepala_surat'         => 'Kepala surat',
        ]);

        Tetapan::simpan('surat.no_rujukan_fail', trim($data['no_rujukan_fail']));
        Tetapan::simpan('surat.bil_seterusnya', (string) $data['bil_seterusnya']);
        Tetapan::simpan('surat.pautan_garis_panduan', $data['pautan_garis_panduan'] ?? '');
        Tetapan::simpan('surat.salinan_kepada', trim((string) ($data['salinan_kepada'] ?? '')));

        $lama = Tetapan::ambil('surat.kepala_surat_path');
        if ($request->hasFile('kepala_surat') || $request->boolean('buang_kepala_surat')) {
            if ($lama) {
                Storage::disk('public')->delete($lama);
            }
            Tetapan::simpan('surat.kepala_surat_path', $request->hasFile('kepala_surat')
                ? $request->file('kepala_surat')->store('tetapan', 'public')
                : null);
        }

        activity()->useLog('jawi')->causedBy(Auth::user())->log('Tetapan surat JAWI dikemaskini.');

        return redirect()->route('admin.jawi.tetapan-surat.edit')->with('success', 'Tetapan surat telah disimpan.');
    }
}
