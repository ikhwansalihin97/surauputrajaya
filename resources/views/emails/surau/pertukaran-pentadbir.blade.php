@php
    $p     = $pertukaran;
    $surau = $p->surau;
    [$tajuk, $warna] = match ($jenis) {
        'DITERIMA' => ['Serah Tugas Pentadbir Surau Diterima', '#16a34a'],
        'DITOLAK'  => ['Serah Tugas Pentadbir Surau Ditolak', '#dc2626'],
        default    => ['Permintaan Serah Tugas Pentadbir Surau', '#1e40af'],
    };
@endphp
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $tajuk }}</title>
</head>
<body style="margin:0; padding:0; background-color:#f0f7fc; font-family:'Segoe UI', Arial, sans-serif; line-height:1.6; color:#334155;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f0f7fc; padding:30px 10px;">
        <tr>
            <td align="center">
                <table width="100%" style="max-width:650px; background-color:#ffffff; border-radius:12px; overflow:hidden; border:1px solid #dbeafe; text-align:left;">
                    <tr>
                        <td style="background: linear-gradient(135deg, #1e3a8a 0%, #0369a1 100%); padding:28px 30px; text-align:center; color:#ffffff;">
                            <div style="font-size:11px; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; color:#bfdbfe; margin-bottom:6px;">SISTEM PENGURUSAN E-SURAU JAWI</div>
                            <h2 style="margin:0; font-size:21px; font-weight:bold; color:#ffffff;">{{ $tajuk }}</h2>
                            <div style="font-size:13px; margin-top:4px; color:#e0f2fe;">{{ $surau->nama_surau }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px; font-size:14px;">
                            <p style="margin-top:0;">Assalamualaikum dan Salam Sejahtera,</p>

                            @if($jenis === 'PERMINTAAN')
                                <p>Tuan/Puan <strong>{{ $p->calon_nama }}</strong>,</p>
                                <p>
                                    Susulan Mesyuarat Agung Tahunan, tuan/puan telah dipilih untuk menggalas tugas sebagai
                                    <strong>Pentadbir Surau</strong> bagi <strong>{{ $surau->nama_surau }}</strong> dalam Sistem e-Surau JAWI,
                                    menggantikan <strong>{{ $p->dari?->name ?? 'pentadbir semasa' }}</strong>.
                                </p>
                                @if($perluAktif)
                                    <p>Akaun Sistem e-Surau JAWI telah dicipta untuk tuan/puan menggunakan e-mel <strong>{{ $p->calon_email }}</strong>. Langkah seterusnya:</p>
                                    <ol style="padding-left:20px; margin:0 0 16px;">
                                        <li>Klik butang di bawah untuk <strong>mengaktifkan akaun</strong> dan menetapkan kata laluan.</li>
                                        <li>Selepas akaun diaktifkan, halaman <strong>Serah Tugas Pentadbir Surau</strong> akan dipaparkan.</li>
                                        <li>Klik <strong>Terima Serah Tugas</strong> (atau Tolak).</li>
                                    </ol>
                                @else
                                    <p>Tuan/puan telah mempunyai akaun dalam sistem (<strong>{{ $p->calon_email }}</strong>). Sila <strong>log masuk</strong> dan
                                        terima atau tolak serah tugas ini di halaman <strong>Serah Tugas Pentadbir Surau</strong>.</p>
                                @endif
                                <div style="text-align:center; margin:28px 0;">
                                    <a href="{{ $url }}" style="background-color:#1e40af; color:#ffffff; padding:14px 34px; text-decoration:none; border-radius:8px; font-weight:bold; font-size:15px; display:inline-block;">
                                        {{ $perluAktif ? 'Aktifkan Akaun' : 'Log Masuk' }}
                                    </a>
                                </div>
                                <p style="font-size:13px; color:#64748b;">
                                    Permintaan ini sah sehingga <strong>{{ $p->tamat_at?->locale('ms')->translatedFormat('j F Y, g:i A') }}</strong>.
                                    Pentadbir surau semasa kekal mentadbir surau ini sehingga tuan/puan menerima serah tugas.
                                </p>
                                <div style="background-color:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; padding:10px 14px; font-family:monospace; font-size:12px; word-break:break-all; color:#1e40af;">
                                    {{ $url }}
                                </div>
                            @elseif($jenis === 'DITERIMA')
                                <p>Tuan/Puan <strong>{{ $p->dari?->name }}</strong>,</p>
                                <p>
                                    Dimaklumkan bahawa <strong>{{ $p->calon_nama }}</strong> telah <strong style="color:{{ $warna }};">menerima</strong>
                                    serah tugas sebagai Pentadbir Surau bagi <strong>{{ $surau->nama_surau }}</strong>.
                                </p>
                                <p>Akses tuan/puan kepada surau ini dalam Sistem e-Surau JAWI telah ditamatkan. Terima kasih atas khidmat tuan/puan sepanjang tempoh pentadbiran.</p>
                            @else
                                <p>Tuan/Puan <strong>{{ $p->dari?->name }}</strong>,</p>
                                <p>
                                    Dimaklumkan bahawa <strong>{{ $p->calon_nama }}</strong> telah <strong style="color:{{ $warna }};">menolak</strong>
                                    serah tugas sebagai Pentadbir Surau bagi <strong>{{ $surau->nama_surau }}</strong>.
                                </p>
                                <p>Tuan/puan kekal sebagai Pentadbir Surau. Sila log masuk untuk memilih calon lain jika perlu.</p>
                            @endif

                            <p style="margin-top:28px; margin-bottom:5px;">Sekian, terima kasih.</p>
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
