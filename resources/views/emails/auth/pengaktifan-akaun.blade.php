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
                <table width="100%" style="max-width:650px; background-color:#ffffff; border-radius:12px; overflow:hidden; border:1px solid #dbeafe; text-align:left; box-shadow:0 4px 16px rgba(30,64,175,0.06);">
                    <!-- HEADER -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #1e3a8a 0%, #0369a1 100%); padding:28px 30px; text-align:center; color:#ffffff;">
                            <div style="font-size:11px; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; color:#bfdbfe; margin-bottom:6px;">SISTEM PENGURUSAN E-SURAU JAWI</div>
                            <h2 style="margin:0; font-size:22px; font-weight:bold; color:#ffffff;">Pautan Pengaktifan Akaun Pengguna</h2>
                            <div style="font-size:13px; margin-top:4px; color:#e0f2fe;">Peranan: Pentadbir Surau</div>
                        </td>
                    </tr>

                    <!-- CONTENT -->
                    <tr>
                        <td style="padding:30px;">
                            <p style="margin-top:0;">Assalamu'alaikum WBT dan Salam Sejahtera,</p>
                            <p>Tuan/Puan <strong>{{ $user->name }}</strong>,</p>
                            <p>Susulan kelulusan permohonan pendaftaran bagi <strong>{{ $application->nama_surau }}</strong> (No. Rujukan: <strong>{{ $application->reference_no }}</strong>), akaun pengguna anda telah didaftarkan ke dalam <strong>Sistem e-Surau JAWI</strong> sebagai <strong>Pentadbir Surau</strong>.</p>
                            <p>Sila gunakan pautan di bawah untuk mengaktifkan akaun anda dan seterusnya menetapkan pengesahan keselamatan 2FA (Two-Factor Authentication).</p>

                            <!-- MAKLUMAT AKAUN TABLE -->
                            <table width="100%" cellpadding="10" cellspacing="0" style="border-collapse:collapse; margin:20px 0; font-size:14px; border:1px solid #e2e8f0; border-radius:8px; overflow:hidden;">
                                <tr style="border-bottom:1px solid #e2e8f0;">
                                    <td width="38%" style="background-color:#f0f7fc; color:#1e3a8a; font-weight:600;">Nama Pengguna</td>
                                    <td><strong>{{ $user->name }}</strong></td>
                                </tr>
                                <tr style="border-bottom:1px solid #e2e8f0;">
                                    <td style="background-color:#f0f7fc; color:#1e3a8a; font-weight:600;">E-mel / ID Log Masuk</td>
                                    <td><strong style="color:#1e40af; font-family:monospace;">{{ $user->email }}</strong></td>
                                </tr>
                                <tr style="border-bottom:1px solid #e2e8f0;">
                                    <td style="background-color:#f0f7fc; color:#1e3a8a; font-weight:600;">Nama Surau</td>
                                    <td><strong>{{ $application->nama_surau }}</strong></td>
                                </tr>
                                <tr>
                                    <td style="background-color:#f0f7fc; color:#1e3a8a; font-weight:600;">Peranan Sistem</td>
                                    <td><span style="background-color:#dbeafe; color:#1e40af; padding:4px 10px; border-radius:6px; font-weight:700; font-size:12px; border:1px solid #bfdbfe;">Pentadbir Surau</span></td>
                                </tr>
                            </table>

                            <!-- CTA BUTTON -->
                            @php
                                $finalActivationUrl = $activationUrl ?? route('account.activate', ['token' => $activationToken]);
                            @endphp
                            <div style="text-align:center; margin:30px 0;">
                                <a href="{{ $finalActivationUrl }}" style="background-color:#16a34a; color:#ffffff; padding:14px 34px; text-decoration:none; border-radius:8px; font-weight:bold; font-size:15px; display:inline-block; box-shadow:0 4px 12px rgba(22,163,74,0.25);">
                                    <i style="margin-right:6px;">✓</i> Aktifkan Akaun Saya
                                </a>
                            </div>

                            <p style="font-size:13px; color:#64748b; margin-bottom:5px;">Jika butang di atas tidak berfungsi, sila salin dan tampal pautan berikut ke pelayar web anda:</p>
                            <div style="background-color:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; padding:10px 14px; font-family:monospace; font-size:12px; word-break:break-all; color:#1e40af; margin-bottom:20px;">
                                {{ $finalActivationUrl }}
                            </div>

                            <!-- INSTRUCTIONS BOX -->
                            <div style="background-color:#eff6ff; border-left:4px solid #2563eb; border-top:1px solid #dbeafe; border-right:1px solid #dbeafe; border-bottom:1px solid #dbeafe; padding:16px 18px; margin:20px 0; border-radius:0 8px 8px 0; color:#1e40af;">
                                <strong style="font-size:14px;">Langkah Seterusnya Selepas Mengaktifkan Akaun:</strong>
                                <ol style="margin:8px 0 0; padding-left:20px; font-size:13px; color:#1e3a8a; line-height:1.7;">
                                    <li>Klik pautan pengaktifan di atas untuk mengaktifkan akaun dan terus log masuk ke sistem.</li>
                                    <li>Sediakan pengesahan 2FA (Two-Factor Authentication) menggunakan aplikasi <strong>Google Authenticator</strong> pada telefon pintar anda bagi menjamin keselamatan akaun.</li>
                                    <li>Selepas melengkapkan 2FA, anda boleh mengakses modul surau, memuat naik fail minit mesyuarat, dan mengemaskini maklumat surau.</li>
                                </ol>
                            </div>

                            <div style="background-color:#fffbeb; border:1px solid #fef3c7; border-radius:8px; padding:12px 16px; margin:20px 0; font-size:12px; color:#92400e;">
                                ⚠️ <strong>Peringatan Keselamatan:</strong> Pautan ini adalah khusus dan sulit untuk kegunaan pemohon utama sahaja. Jangan kongsikan e-mel ini kepada pihak yang tidak berkaitan.
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
