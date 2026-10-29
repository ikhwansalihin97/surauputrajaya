<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaktifan Akaun Pentadbir Surau</title>
</head>
<body style="margin:0; padding:0; background-color:#f0f7fc; font-family:'Segoe UI', Arial, sans-serif; line-height:1.6; color:#334155;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f0f7fc; padding:30px 10px;">
        <tr>
            <td align="center">
                <table width="100%" style="max-width:600px; background-color:#ffffff; border-radius:12px; overflow:hidden; border:1px solid #dbeafe; text-align:left; box-shadow:0 4px 16px rgba(30,64,175,0.06);">
                    <!-- HEADER -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #1e3a8a 0%, #0369a1 100%); padding:28px 30px; text-align:center; color:#ffffff;">
                            <div style="font-size:11px; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; color:#bfdbfe; margin-bottom:6px;">JABATAN AGAMA ISLAM WILAYAH PERSEKUTUAN (JAWI)</div>
                            <h2 style="margin:0; font-size:22px; font-weight:bold; color:#ffffff;">Pengaktifan Akaun Pentadbir Surau</h2>
                            <div style="font-size:13px; margin-top:4px; color:#e0f2fe;">Sistem Pengurusan e-Surau JAWI</div>
                        </td>
                    </tr>

                    <!-- CONTENT -->
                    <tr>
                        <td style="padding:30px;">
                            <p style="margin-top:0;">Assalamu'alaikum WBT dan Salam Sejahtera,</p>
                            <p>Tuan/Puan <strong>{{ $user->name }}</strong>,</p>
                            <p>Permohonan pendaftaran bagi <strong>{{ $app->nama_surau }}</strong> (No. Rujukan: <code style="background:#eff6ff; color:#1e40af; padding:3px 8px; border-radius:4px; font-family:monospace; font-weight:bold; border:1px solid #bfdbfe;">{{ $app->reference_no }}</code>) telah <strong>DISAHKAN</strong> oleh pihak JAWI.</p>

                            <!-- CREDENTIALS BOX -->
                            <div style="background-color:#f0f7fc; border-left:4px solid #1e40af; border-top:1px solid #e2e8f0; border-right:1px solid #e2e8f0; border-bottom:1px solid #e2e8f0; padding:18px; margin:20px 0; border-radius:0 8px 8px 0;">
                                <h4 style="margin:0 0 10px 0; color:#1e3a8a; font-size:15px; font-weight:700;">Maklumat Log Masuk Akaun Pentadbir Surau:</h4>
                                <table width="100%" cellpadding="4" cellspacing="0" style="font-size:14px;">
                                    <tr>
                                        <td width="35%" style="color:#64748b;">Alamat E-Mel:</td>
                                        <td><strong>{{ $user->email }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td style="color:#64748b;">Katalaluan Sementara:</td>
                                        <td><code style="background:#e2e8f0; color:#0f172a; padding:3px 8px; border-radius:4px; font-family:monospace; font-weight:bold;">{{ $password }}</code></td>
                                    </tr>
                                </table>
                            </div>

                            <p>Sila klik butang di bawah untuk mengaktifkan akaun anda dan menetapkan pengesahan keselamatan 2-Faktor (2FA):</p>

                            <!-- CTA BUTTON -->
                            <div style="text-align:center; margin:30px 0;">
                                <a href="{{ $activationUrl }}" style="background-color:#1e40af; color:#ffffff; padding:13px 32px; text-decoration:none; border-radius:8px; font-weight:bold; font-size:15px; display:inline-block; box-shadow:0 4px 12px rgba(30,64,175,0.25);">
                                    Aktifkan Akaun &amp; Log Masuk &rarr;
                                </a>
                            </div>

                            <div style="background-color:#fffbeb; color:#92400e; padding:14px 16px; border-radius:8px; font-size:13px; margin-top:20px; border:1px solid #fde68a;">
                                <strong>⚠️ Nota Keselamatan:</strong> Semasa log masuk kali pertama, anda diwajibkan untuk mengaktifkan <strong>Google Authenticator (2FA)</strong> bagi melindungi akaun pentadbir anda.
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