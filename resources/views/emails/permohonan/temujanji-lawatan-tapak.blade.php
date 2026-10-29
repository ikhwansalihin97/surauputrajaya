<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Temujanji Lawatan Tapak Permohonan Surau</title>
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
                            <h2 style="margin:0; font-size:22px; font-weight:bold; color:#ffffff;">Temujanji Lawatan Tapak Ditetapkan</h2>
                            <div style="font-size:13px; margin-top:4px; color:#e0f2fe;">Pemakluman Kepada Pemohon &amp; Pentadbir Zon</div>
                        </td>
                    </tr>

                    <!-- CONTENT -->
                    <tr>
                        <td style="padding:30px;">
                            <p style="margin-top:0;">Assalamu'alaikum WBT dan Salam Sejahtera,</p>
                            <p>Dimaklumkan bahawa <strong>Pentadbir JAWI</strong> telah menetapkan temujanji lawatan tapak bagi permohonan pendaftaran surau seperti butiran berikut:</p>

                            <!-- APPOINTMENT DETAILS TABLE -->
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
                                    <td style="background-color:#f0f7fc; color:#1e3a8a; font-weight:600;">Pemohon</td>
                                    <td>{{ $application->pemohon_nama }}</td>
                                </tr>
                                <tr style="border-bottom:1px solid #e2e8f0; background-color:#eff6ff;">
                                    <td style="color:#1e3a8a; font-weight:700;">📅 Tarikh Temujanji</td>
                                    <td><strong style="color:#1e40af; font-size:15px;">{{ $appointment->tarikh_temujanji ? $appointment->tarikh_temujanji->format('d/m/Y') : '-' }}</strong></td>
                                </tr>
                                <tr style="border-bottom:1px solid #e2e8f0; background-color:#eff6ff;">
                                    <td style="color:#1e3a8a; font-weight:700;">⏰ Masa</td>
                                    <td><strong style="color:#1e40af; font-size:15px;">{{ $appointment->masa_temujanji ? \Carbon\Carbon::parse($appointment->masa_temujanji)->format('h:i A') : '-' }}</strong></td>
                                </tr>
                                <tr style="border-bottom:1px solid #e2e8f0;">
                                    <td style="background-color:#f0f7fc; color:#1e3a8a; font-weight:600;">Tempat / Lokasi</td>
                                    <td><strong>{{ $appointment->tempat }}</strong></td>
                                </tr>
                                @if(!empty($appointment->catatan))
                                <tr>
                                    <td style="background-color:#f0f7fc; color:#1e3a8a; font-weight:600;">Catatan / Arahan</td>
                                    <td>{{ $appointment->catatan }}</td>
                                </tr>
                                @endif
                            </table>

                            <div style="background-color:#f0f7fc; border-left:4px solid #1e40af; border-top:1px solid #e2e8f0; border-right:1px solid #e2e8f0; border-bottom:1px solid #e2e8f0; padding:16px 18px; margin:20px 0; border-radius:0 8px 8px 0;">
                                <strong style="color:#1e3a8a; font-size:14px;">Arahan &amp; Tindakan:</strong>
                                <p style="margin:6px 0 0; font-size:14px; color:#475569;">
                                    <strong>Pemohon:</strong> Sila bersedia di lokasi temujanji pada tarikh dan masa yang ditetapkan berserta dokumen sokongan yang berkaitan.<br>
                                    <strong>Pentadbir Zon:</strong> Sila ambil maklum tarikh temujanji ini untuk persediaan pemeriksaan lawatan tapak.
                                </p>
                            </div>

                            <!-- ACTION BUTTONS -->
                            <div style="text-align:center; margin:30px 0;">
                                @if(!empty($googleCalendarUrl))
                                <a href="{{ $googleCalendarUrl }}" target="_blank" rel="noopener noreferrer" style="background-color:#0284c7; color:#ffffff; padding:13px 24px; text-decoration:none; border-radius:8px; font-weight:bold; font-size:14px; display:inline-block; margin:5px; box-shadow:0 4px 12px rgba(2,132,199,0.25);">
                                    📅 Tambah ke Google Calendar
                                </a>
                                @endif
                                <a href="{{ route('login') }}" style="background-color:#1e40af; color:#ffffff; padding:13px 24px; text-decoration:none; border-radius:8px; font-weight:bold; font-size:14px; display:inline-block; margin:5px; box-shadow:0 4px 12px rgba(30,64,175,0.25);">
                                    Log Masuk Ke Portal e-Surau &rarr;
                                </a>
                            </div>

                            <div style="background-color:#f8fafc; border:1px dashed #cbd5e1; border-radius:8px; padding:12px 16px; margin-bottom:25px; text-align:center; font-size:13px; color:#64748b;">
                                💡 <strong>Petua Kalendar:</strong> E-mel ini turut dilampirkan bersama fail jemputan kalendar (<code>.ics</code>). Aplikasi Gmail atau peranti telefon anda akan mengesannya secara automatik untuk disimpan ke dalam kalendar.
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
