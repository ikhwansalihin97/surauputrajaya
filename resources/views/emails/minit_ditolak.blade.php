<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minit Mesyuarat Ditolak / Perlu Pembetulan</title>
</head>
<body style="margin:0; padding:0; background-color:#f0f7fc; font-family:'Segoe UI', Arial, sans-serif; line-height:1.6; color:#334155;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f0f7fc; padding:30px 10px;">
        <tr>
            <td align="center">
                <table width="100%" style="max-width:650px; background-color:#ffffff; border-radius:12px; overflow:hidden; border:1px solid #dbeafe; text-align:left; box-shadow:0 4px 16px rgba(30,64,175,0.06);">
                    <!-- HEADER -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #1e3a8a 0%, #0369a1 100%); padding:28px 30px; text-align:center; color:#ffffff;">
                            <div style="font-size:11px; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; color:#bfdbfe; margin-bottom:6px;">SISTEM PENGURUSAN E-SURAU JAWI</div>
                            <h2 style="margin:0; font-size:22px; font-weight:bold; color:#ffffff;">Semakan Minit Mesyuarat Ditolak</h2>
                            <div style="font-size:13px; margin-top:4px; color:#e0f2fe;">Tindakan Pembetulan &amp; Muat Naik Semula Diperlukan</div>
                        </td>
                    </tr>

                    <!-- CONTENT -->
                    <tr>
                        <td style="padding:30px;">
                            <p style="margin-top:0;">Assalamu'alaikum WBT dan Salam Sejahtera,</p>
                            <p>Tuan/Puan <strong>{{ $application->pemohon_nama }}</strong>,</p>
                            <p>Merujuk kepada permohonan pendaftaran surau bagi <strong>{{ $application->nama_surau }}</strong> (No. Rujukan: <code style="background:#eff6ff; color:#1e40af; padding:3px 8px; border-radius:4px; font-family:monospace; font-weight:bold; border:1px solid #bfdbfe;">{{ $application->reference_no }}</code>).</p>

                            <p>Dukacita dimaklumkan bahawa fail <strong>Minit Mesyuarat Penubuhan / AGM</strong> (Tahun {{ $minit->tahun_minit ?? date('Y') }}) yang dimuat naik didapati <strong style="color:#dc2626;">DITOLAK / TIDAK DISAHKAN</strong> oleh pihak JAWI atas sebab berikut:</p>

                            <!-- ULASAN BOX -->
                            <div style="background-color:#fef2f2; border-left:4px solid #dc2626; border-top:1px solid #fecaca; border-right:1px solid #fecaca; border-bottom:1px solid #fecaca; padding:16px 18px; margin:20px 0; border-radius:0 8px 8px 0; color:#991b1b;">
                                <strong style="font-size:14px;">Ulasan Pegawai Semakan JAWI:</strong>
                                <p style="margin:6px 0 0; font-size:14px; color:#7f1d1d;">{{ $catatan ?? $minit->catatan_semakan ?? 'Minit mesyuarat tidak lengkap atau tidak memenuhi syarat yang ditetapkan.' }}</p>
                            </div>

                            <p>Sehubungan dengan itu, mohon pihak tuan/puan untuk <strong>membuat pembetulan dan memuat naik semula</strong> fail PDF Minit Mesyuarat yang terbaharu melalui Portal Pengurusan e-Surau JAWI.</p>

                            <!-- CTA BUTTON -->
                            <div style="text-align:center; margin:30px 0;">
                                <a href="{{ route('permohonan.semak') }}" style="background-color:#1e40af; color:#ffffff; padding:13px 32px; text-decoration:none; border-radius:8px; font-weight:bold; font-size:15px; display:inline-block; box-shadow:0 4px 12px rgba(30,64,175,0.25);">
                                    Muat Naik Semula Minit Mesyuarat &rarr;
                                </a>
                            </div>

                            <p style="margin-top:30px; margin-bottom:5px;">Sekian, terima kasih.</p>
                            <p style="margin-top:0;">
                                <strong>Bahagian Pengurusan Masjid &amp; Surau</strong><br>
                                Jabatan Agama Islam Wilayah Persekutuan (JAWI)
                            </p>
                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td style="background-color:#f8fafc; padding:16px 30px; text-align:center; font-size:12px; color:#64748b; border-top:1px solid #e2e8f0;">
                            E-mel ini dijana secara automatik oleh <strong>Sistem e-Surau JAWI</strong>.<br>
                            Sila jangan balas e-mel ini.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>