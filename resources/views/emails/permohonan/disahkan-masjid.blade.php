<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permohonan Disahkan Oleh Pentadbir Masjid</title>
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
                            <h2 style="margin:0; font-size:22px; font-weight:bold; color:#ffffff;">Permohonan Disahkan Oleh Masjid</h2>
                            <div style="font-size:13px; margin-top:4px; color:#e0f2fe;">Menunggu Semakan &amp; Kelulusan JAWI</div>
                        </td>
                    </tr>

                    <!-- CONTENT -->
                    <tr>
                        <td style="padding:30px;">
                            <p style="margin-top:0;">Assalamu'alaikum WBT dan Salam Sejahtera,</p>
                            <p>Tuan/Puan <strong>Pentadbir JAWI</strong>,</p>
                            <p>Dimaklumkan bahawa permohonan pendaftaran surau berikut telah <strong>DISAHKAN &amp; DISOKONG</strong> di peringkat Pentadbir Masjid dan kini diserahkan kepada pihak JAWI untuk semakan dokumen dan keputusan selanjutnya:</p>

                            <!-- SUMMARY TABLE -->
                            <table width="100%" cellpadding="10" cellspacing="0" style="border-collapse:collapse; margin:20px 0; font-size:14px; border:1px solid #e2e8f0; border-radius:8px; overflow:hidden;">
                                <tr style="border-bottom:1px solid #e2e8f0;">
                                    <td width="38%" style="background-color:#f0f7fc; color:#1e3a8a; font-weight:600;">No. Rujukan</td>
                                    <td><strong style="color:#1e40af; font-family:monospace; font-size:15px; background:#eff6ff; padding:2px 8px; border-radius:4px; border:1px solid #bfdbfe;">{{ $application->reference_no }}</strong></td>
                                </tr>
                                <tr style="border-bottom:1px solid #e2e8f0;">
                                    <td style="background-color:#f0f7fc; color:#1e3a8a; font-weight:600;">Nama Surau</td>
                                    <td><strong>{{ $application->nama_surau }}</strong></td>
                                </tr>
                                <tr style="border-bottom:1px solid #e2e8f0;">
                                    <td style="background-color:#f0f7fc; color:#1e3a8a; font-weight:600;">Kariah Masjid</td>
                                    <td>{{ $application->masjid?->nama_masjid ?? $application->kariah_masjid }}</td>
                                </tr>
                                <tr>
                                    <td style="background-color:#f0f7fc; color:#1e3a8a; font-weight:600;">Status Semasa</td>
                                    <td><span style="background-color:#dcfce7; color:#15803d; padding:4px 12px; border-radius:6px; font-weight:700; font-size:12px; border:1px solid #86efac;">DISAHKAN MASJID &rarr; MENUNGGU JAWI</span></td>
                                </tr>
                            </table>

                            <p>Sila log masuk ke dalam <strong>Sistem Pengurusan e-Surau JAWI</strong> untuk menyemak dokumen, minit mesyuarat, dan menetapkan keputusan pengesahan.</p>

                            <!-- CTA BUTTON -->
                            <div style="text-align:center; margin:30px 0;">
                                <a href="{{ route('login') }}" style="background-color:#1e40af; color:#ffffff; padding:13px 32px; text-decoration:none; border-radius:8px; font-weight:bold; font-size:15px; display:inline-block; box-shadow:0 4px 12px rgba(30,64,175,0.25);">
                                    Log Masuk Ke Panel JAWI &rarr;
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