<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permohonan Ditolak</title>
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
                            <h2 style="margin:0; font-size:22px; font-weight:bold; color:#ffffff;">Status Permohonan: Tidak Disahkan</h2>
                            <div style="font-size:13px; margin-top:4px; color:#e0f2fe;">Peringkat Keputusan Akhir JAWI</div>
                        </td>
                    </tr>

                    <!-- CONTENT -->
                    <tr>
                        <td style="padding:30px;">
                            <p style="margin-top:0;">Assalamu'alaikum WBT dan Salam Sejahtera,</p>
                            <p>Tuan/Puan <strong>{{ $application->pemohon_nama }}</strong>,</p>
                            <p>Dukacita dimaklumkan bahawa permohonan pendaftaran surau anda telah <strong>DITOLAK / TIDAK DISAHKAN</strong> di peringkat keputusan akhir Pentadbir JAWI selepas lawatan tapak.</p>

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
                                <tr>
                                    <td style="background-color:#f0f7fc; color:#1e3a8a; font-weight:600;">Status Permohonan</td>
                                    <td><span style="background-color:#fee2e2; color:#991b1b; padding:4px 12px; border-radius:6px; font-weight:700; font-size:12px; border:1px solid #fca5a5;">DITOLAK PENTADBIR ZON</span></td>
                                </tr>
                            </table>

                            @php
                                $confirmation = $application->confirmations
                                    ->where('peringkat', 'ZON')
                                    ->sortByDesc('id')
                                    ->first();
                            @endphp

                            @if($confirmation && $confirmation->catatan)
                                <div style="background-color:#fef2f2; border-left:4px solid #dc2626; border-top:1px solid #fecaca; border-right:1px solid #fecaca; border-bottom:1px solid #fecaca; padding:16px 18px; margin:20px 0; border-radius:0 8px 8px 0; color:#991b1b;">
                                    <strong style="font-size:14px;">Sebab / Ulasan Pentadbir JAWI:</strong>
                                    <p style="margin:6px 0 0; font-size:14px; color:#7f1d1d;">{{ $confirmation->catatan }}</p>
                                </div>
                            @endif

                            <p>Sila log masuk ke Portal e-Surau JAWI untuk menyemak maklumat lanjut atau hubungi Pejabat JAWI untuk sebarang pertanyaan.</p>

                            <!-- CTA BUTTON -->
                            <div style="text-align:center; margin:30px 0;">
                                <a href="{{ route('permohonan.semak') }}" style="background-color:#1e40af; color:#ffffff; padding:13px 32px; text-decoration:none; border-radius:8px; font-weight:bold; font-size:15px; display:inline-block; box-shadow:0 4px 12px rgba(30,64,175,0.25);">
                                    Semak Status Permohonan &rarr;
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
