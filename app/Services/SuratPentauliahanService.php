<?php

namespace App\Services;

use App\Models\SurauAgmMesyuarat;
use App\Models\SurauCommittee;
use App\Models\Tetapan;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * Surat Pentauliahan Anggota Jawatankuasa Surau (selepas Keputusan Mesyuarat AGM Bil. 1 disahkan JAWI).
 *
 * No. rujukan, tarikh dan penandatangan ditetapkan sekali sahaja (semasa disahkan, atau semasa
 * surat pertama kali dijana) supaya setiap cetakan semula menghasilkan surat yang sama.
 */
class SuratPentauliahanService
{
    private const BULAN_HIJRI = [
        1 => 'Muharam', 2 => 'Safar', 3 => 'Rabiulawal', 4 => 'Rabiulakhir', 5 => 'Jamadilawal', 6 => 'Jamadilakhir',
        7 => 'Rejab', 8 => 'Syaaban', 9 => 'Ramadan', 10 => 'Syawal', 11 => 'Zulkaedah', 12 => 'Zulhijjah',
    ];

    public const BLOK_TANDATANGAN_LALAI = "b.p. Pengarah\nJabatan Agama Islam Wilayah Persekutuan";

    /** Tetapkan no. rujukan, tarikh dan penandatangan jika belum ditetapkan */
    public function pastikanMaklumat(SurauAgmMesyuarat $mesyuarat): SurauAgmMesyuarat
    {
        if ($mesyuarat->surat_no_rujukan) {
            return $mesyuarat;
        }

        $pusingan = $mesyuarat->pengesahanTerkini;
        $mesyuarat->update([
            'surat_no_rujukan'          => Tetapan::noRujukanSeterusnya(),
            'surat_tarikh'              => ($pusingan?->disemak_at ?? now())->toDateString(),
            'surat_ditandatangani_oleh' => $pusingan?->disemak_oleh,
        ]);

        return $mesyuarat;
    }

    public function pdf(SurauAgmMesyuarat $mesyuarat)
    {
        abort_unless($mesyuarat->bolehCetakPentauliahan(), 404, 'Surat pentauliahan hanya tersedia selepas Keputusan Mesyuarat Bil. 1 disahkan JAWI.');

        $mesyuarat = $this->pastikanMaklumat($mesyuarat)->load(['sesi.surau.masjid', 'penandatanganSurat']);
        $surau = $mesyuarat->sesi->surau;
        $penandatangan = $mesyuarat->penandatanganSurat;
        $ajk = SurauCommittee::where('agm_sesi_id', $mesyuarat->agm_sesi_id)->orderBy('id')->get();

        $alamatBaris = collect(preg_split('/\r\n|\r|\n/', (string) ($surau->alamat_surat ?: $surau->alamat_surau)))
            ->map(fn ($l) => trim($l, " \t,"))->filter()->values();

        $data = [
            'mesyuarat'     => $mesyuarat,
            'surau'         => $surau,
            'ajk'           => $ajk,
            'alamatBaris'   => $alamatBaris,
            'alamatSebaris' => $alamatBaris->implode(', '),
            'tarikhMasihi'  => $mesyuarat->surat_tarikh->locale('ms')->translatedFormat('j F Y'),
            'tarikhHijri'   => $this->tarikhHijri($mesyuarat->surat_tarikh),
            'tempohMula'    => $mesyuarat->tarikh->locale('ms')->translatedFormat('j F Y'),
            'tempohTamat'   => '31 Disember ' . $mesyuarat->sesi->tahun_tamat,
            'pautan'        => Tetapan::ambil('surat.pautan_garis_panduan'),
            'salinan'       => $this->salinanKepada($surau),
            'kepalaSurat'   => $this->gambar(Tetapan::ambil('surat.kepala_surat_path'), 'public'),
            'jata'          => $this->gambar(public_path('images/jata-negara.png')),
            'logo'          => $this->gambar(public_path('images/surat/logo-jawi.png')),
            'tulisanJawi'   => $this->gambar(public_path('images/surat/tulisan-jawi.png')),
            'penandatangan' => $penandatangan,
            'tandatangan'   => $this->gambar($penandatangan?->tandatangan_path, 'local'),
            'blok'          => $this->blokTandatangan($penandatangan),
        ];

        $pdf = Pdf::loadView('pdf.surat-pentauliahan-ajk', $data)->setPaper('a4');

        // No. rujukan di bahagian atas setiap halaman surat selepas halaman pertama (bukan pada Lampiran)
        $pdf->render();
        $rujukan = $mesyuarat->surat_no_rujukan;
        $pdf->getDomPDF()->getCanvas()->page_script(function ($halaman, $jumlah, $canvas, $fontMetrics) use ($rujukan) {
            if ($halaman > 1 && $halaman < $jumlah) {
                $font = $fontMetrics->getFont('Helvetica');
                $lebar = $fontMetrics->getTextWidth($rujukan, $font, 10);
                $canvas->text($canvas->get_width() - 60 - $lebar, 34, $rujukan, $font, 10.5, [0, 0, 0]);
            }
        });

        return $pdf;
    }

    public function namaFail(SurauAgmMesyuarat $mesyuarat): string
    {
        $bahagian = 'Surat-Pentauliahan-AJK-' . $mesyuarat->sesi->surau->nama_surau . '-' . $mesyuarat->sesi->label;

        return trim(preg_replace('/[^A-Za-z0-9]+/', '-', $bahagian), '-') . '.pdf';
    }

    /** Cth. "26 Zulhijjah 1447H" (takwim Umm al-Qura) */
    public function tarikhHijri(Carbon $tarikh): string
    {
        $f = new \IntlDateFormatter('en_US@calendar=islamic-umalqura', \IntlDateFormatter::NONE, \IntlDateFormatter::NONE,
            'Asia/Kuala_Lumpur', \IntlDateFormatter::TRADITIONAL, 'd|M|y');
        [$h, $b, $t] = explode('|', $f->format($tarikh->copy()->setTime(12, 0)));

        return (int) $h . ' ' . self::BULAN_HIJRI[(int) $b] . ' ' . $t . 'H';
    }

    /** Baris blok tandatangan di bawah nama pegawai */
    public function blokTandatangan(?User $u): array
    {
        $teks = $u?->blok_tandatangan ?: trim(($u?->jawatan ? $u->jawatan . "\n" : '') . self::BLOK_TANDATANGAN_LALAI);

        return collect(preg_split('/\r\n|\r|\n/', $teks))->map(fn ($l) => trim($l))->filter()->values()->all();
    }

    /** Blok salinan kepada (dipisahkan baris kosong) + Pengerusi masjid kariah */
    private function salinanKepada($surau): array
    {
        $blok = collect(preg_split('/\R\s*\R/', trim((string) Tetapan::ambil('surat.salinan_kepada'))))
            ->map(fn ($b) => array_values(array_filter(array_map('trim', preg_split('/\R/', $b)))))
            ->filter()->values()->all();

        if ($masjid = $surau->masjid) {
            $alamat = array_values(array_filter(array_map(fn ($l) => trim($l, " \t,"), preg_split('/\R/', (string) $masjid->alamat))));
            $blok[] = array_merge(['Pengerusi', $masjid->nama_masjid], $alamat);
        }

        return $blok;
    }

    /** Imej sebagai data URI (dompdf tidak perlu akses rangkaian) */
    private function gambar(?string $laluan, ?string $disk = null): ?string
    {
        if (!$laluan) {
            return null;
        }
        $penuh = $disk ? Storage::disk($disk)->path($laluan) : $laluan;
        if (!is_file($penuh)) {
            return null;
        }
        $mime = mime_content_type($penuh) ?: 'image/png';

        return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($penuh));
    }

    /** No. K/P 12 digit -> 851119-01-6751 */
    public static function formatKp(?string $ic): string
    {
        $d = preg_replace('/\D+/', '', (string) $ic);

        return strlen($d) === 12 ? substr($d, 0, 6) . '-' . substr($d, 6, 2) . '-' . substr($d, 8) : (string) $ic;
    }
}
