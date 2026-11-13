<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pentadbir Surau</title>
</head>
<body style="margin:0; padding:0; background-color:#f0f7fc; font-family:'Segoe UI', Arial, sans-serif; line-height:1.6; color:#334155;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f0f7fc; padding:30px 10px;">
        <tr>
            <td align="center">
                <table width="100%" style="max-width:650px; background-color:#ffffff; border-radius:12px; overflow:hidden; border:1px solid #dbeafe; text-align:left;">
                    <tr>
                        <td style="background: linear-gradient(135deg, #1e3a8a 0%, #0369a1 100%); padding:28px 30px; text-align:center; color:#ffffff;">
                            <div style="font-size:11px; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; color:#bfdbfe; margin-bottom:6px;">SISTEM PENGURUSAN E-SURAU JAWI</div>
                            <h2 style="margin:0; font-size:22px; font-weight:bold; color:#ffffff;">
                                {{ $activationUrl ? 'Pautan Pengaktifan Akaun Pengguna' : 'Pendaftaran Pentadbir Surau' }}
                            </h2>
                            <div style="font-size:13px; margin-top:4px; color:#e0f2fe;">Peranan: Pentadbir Surau</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px;">
                            <p style="margin-top:0;">Assalamu'alaikum WBT dan Salam Sejahtera,</p>
                            <p>Tuan/Puan <strong>{{ $user->name }}</strong>,</p>
                            <p>Dimaklumkan bahawa anda telah didaftarkan oleh <strong>Jabatan Agama Islam Wilayah Persekutuan (JAWI)</strong> sebagai <strong>Pentadbir Surau</strong> bagi surau berikut di dalam <strong>Sistem e-Surau JAWI</strong>.</p>

                            <table width="100%" cellpadding="10" cellspacing="0" style="border-collapse:collapse; border:1px solid #e2e8f0; border-radius:8px; font-size:14px; margin:20px 0;">
                                <tr style="border-bottom:1px solid #e2e8f0;">
                                    <td style="background-color:#f0f7fc; color:#1e3a8a; font-weight:600; width:40%;">Nama Surau</td>
                                    <td><strong>{{ $surau->nama_surau }}</strong></td>
                                </tr>
                                <tr style="border-bottom:1px solid #e2e8f0;">
                                    <td style="background-color:#f0f7fc; color:#1e3a8a; font-weight:600;">No. Rujukan JAWI</td>
                                    <td>{{ $surau->no_rujukan_jawi ?: '-' }}</td>
                                </tr>
                                <tr style="border-bottom:1px solid #e2e8f0;">
                                    <td style="background-color:#f0f7fc; color:#1e3a8a; font-weight:600;">Kariah Masjid</td>
                                    <td>{{ $surau->masjid?->nama_masjid ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="background-color:#f0f7fc; color:#1e3a8a; font-weight:600;">E-mel (ID Log Masuk)</td>
                                    <td>{{ $user->email }}</td>
                                </tr>
                            </table>

                            @if($activationUrl)
                                <p>Sila gunakan pautan di bawah untuk mengaktifkan akaun anda dan menetapkan kata laluan.</p>
                                <div style="text-align:center; margin:30px 0;">
                                    <a href="{{ $activationUrl }}" style="background-color:#16a34a; color:#ffffff; padding:14px 34px; text-decoration:none; border-radius:8px; font-weight:bold; font-size:15px; display:inline-block;">
                                        ✓ Aktifkan Akaun Saya
                                    </a>
                                </div>
                                <p style="font-size:13px; color:#64748b; margin-bottom:5px;">Jika butang di atas tidak berfungsi, salin pautan berikut ke pelayar web anda:</p>
                                <div style="background-color:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; padding:10px 14px; font-family:monospace; font-size:12px; word-break:break-all; color:#1e40af; margin-bottom:20px;">
                                    {{ $activationUrl }}
                                </div>
                            @else
                                <p>Surau ini telah ditambah ke akaun anda yang sedia ada. Sila log masuk menggunakan akaun anda dan pilih surau ini di papan pemuka.</p>
                                <div style="text-align:center; margin:30px 0;">
                                    <a href="{{ route('login') }}" style="background-color:#1e40af; color:#ffffff; padding:14px 34px; text-decoration:none; border-radius:8px; font-weight:bold; font-size:15px; display:inline-block;">
                                        Log Masuk
                                    </a>
                                </div>
                            @endif

                            <div style="background-color:#eff6ff; border-left:4px solid #2563eb; padding:16px 18px; margin:20px 0; border-radius:0 8px 8px 0; color:#1e40af;">
                                <strong style="font-size:14px;">Tindakan Seterusnya:</strong>
                                <ol style="margin:8px 0 0; padding-left:20px; font-size:13px; color:#1e3a8a; line-height:1.7;">
                                    <li>Rekodkan <strong>Mesyuarat Agung Tahunan (AGM)</strong> surau mengikut sesi (cth. 2026/2027), termasuk tarikh, masa dan lokasi mesyuarat.</li>
                                    <li>Lengkapkan <strong>Maklumat Surau</strong> (alamat, tapak, bank, aktiviti).</li>
                                    <li>Lengkapkan <strong>Maklumat Jawatankuasa (AJK)</strong> surau bagi sesi AGM tersebut.</li>
                                </ol>
                                <div style="margin-top:8px; font-size:12px; color:#1e40af;">
                                    * Senarai AJK hanya boleh diisi selepas Mesyuarat Agung Tahunan direkodkan.
                                </div>
                            </div>

                            <p style="margin-top:30px; margin-bottom:5px;">Sekian, terima kasih.</p>
                            <p style="margin-top:0;">
                                <strong>Bahagian Pengurusan Masjid &amp; Surau</strong><br>
                                Jabatan Agama Islam Wilayah Persekutuan (JAWI)
                            </p>
                        </td>
                    </tr>
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
