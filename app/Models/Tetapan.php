<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Tetapan sistem (kunci/nilai), cth. tetapan surat JAWI.
 */
class Tetapan extends Model
{
    protected $table = 'tetapan';

    protected $fillable = ['kunci', 'nilai'];

    /** Nilai lalai tetapan surat pentauliahan */
    public const LALAI = [
        'surat.kepala_surat_path'      => null,
        'surat.no_rujukan_fail'        => 'JAWI.K.100-7/3 Jld.12',
        'surat.bil_seterusnya'         => '1',
        'surat.pautan_garis_panduan'   => 'https://www.jawi.gov.my/penyelidikan/garis-panduan',
        'surat.salinan_kepada'         => "Pengarah\nJabatan Agama Islam Wilayah Persekutuan\n\nKetua Penolong Pengarah Kanan\nBahagian Pengurusan Masjid\nJabatan Agama Islam Wilayah Persekutuan",
    ];

    public static function ambil(string $kunci, ?string $lalai = null): ?string
    {
        $nilai = static::where('kunci', $kunci)->value('nilai');

        return $nilai ?? $lalai ?? (self::LALAI[$kunci] ?? null);
    }

    public static function simpan(string $kunci, ?string $nilai): void
    {
        static::updateOrCreate(['kunci' => $kunci], ['nilai' => $nilai]);
    }

    /**
     * No. rujukan surat seterusnya, cth. "JAWI.K.100-7/3 Jld.12 (11)". Bilangan dinaikkan secara atomik.
     */
    public static function noRujukanSeterusnya(): string
    {
        return DB::transaction(function () {
            $baris = static::lockForUpdate()->firstOrCreate(
                ['kunci' => 'surat.bil_seterusnya'],
                ['nilai' => self::LALAI['surat.bil_seterusnya']]
            );
            $bil = max(1, (int) $baris->nilai);
            $baris->update(['nilai' => (string) ($bil + 1)]);

            return trim(static::ambil('surat.no_rujukan_fail')) . " ({$bil})";
        });
    }
}
