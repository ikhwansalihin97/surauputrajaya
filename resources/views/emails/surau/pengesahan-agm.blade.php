@php
    $m        = $mesyuarat;
    $p        = $pusingan;
    $surau    = $m->sesi->surau;
    $disahkan = $m->status_pengesahan === 'DISAHKAN';
    [$tajuk, $warna] = $disahkan
        ? ['Keputusan Mesyuarat Disahkan JAWI', '#16a34a']
        : ['Keputusan Mesyuarat Perlu Pembetulan', '#dc2626'];
    $label = \App\Models\SurauAgmPengesahan::ITEM_LABEL;
    $calon = $p->pentadbir_calon ?? [];
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
                            <p>Tuan/Puan <strong>{{ $surau->user?->name }}</strong>,</p>
                            <p>
                                Pentadbir JAWI telah menyemak Keputusan <strong>Mesyuarat Agung Tahunan Bil. {{ $m->bilangan }} ({{ $m->tahun }})</strong>
                                sesi {{ $m->sesi->label }} yang diadakan pada {{ $m->tarikhMelayu() }}. Status pengesahan:
                                <strong style="color:{{ $warna }};">{{ $disahkan ? 'DISAHKAN' : 'PERLU PEMBETULAN' }}</strong>.
                            </p>

                            <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0; border-radius:8px; border-collapse:separate; margin:18px 0; font-size:13px;">
                                @foreach($m->itemPengesahan() as $item)
                                    @php
                                        $st = $p->status($item);
                                        [$stLabel, $stWarna] = $st === 'DILULUSKAN' ? ['Diluluskan', '#16a34a'] : ($st === 'DITOLAK' ? ['Ditolak', '#dc2626'] : ['Menunggu', '#d97706']);
                                    @endphp
                                    <tr>
                                        <td style="padding:10px 14px; {{ $loop->last ? '' : 'border-bottom:1px solid #e2e8f0;' }} vertical-align:top;">
                                            <strong>{{ $label[$item] }}</strong>
                                            @if($item === 'pentadbir')
                                                <div style="color:#64748b;">
                                                    {{ $p->pentadbir_pilihan === 'KEKAL' ? 'Kekalkan pentadbir semasa' : 'Pentadbir baharu: ' . ($calon['nama'] ?? '-') . ' (' . ($calon['jawatan'] ?? '') . ')' }}
                                                </div>
                                            @endif
                                            @if($p->catatan($item))
                                                <div style="margin-top:4px; color:{{ $st === 'DITOLAK' ? '#b91c1c' : '#64748b' }};">Catatan JAWI: {{ $p->catatan($item) }}</div>
                                            @endif
                                        </td>
                                        <td style="padding:10px 14px; {{ $loop->last ? '' : 'border-bottom:1px solid #e2e8f0;' }} text-align:right; vertical-align:top; white-space:nowrap;">
                                            <strong style="color:{{ $stWarna }};">{{ $stLabel }}</strong>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>

                            @if($disahkan)
                                @if($m->bilangan === 1 && $p->pentadbir_pilihan === 'BAHARU')
                                    <p>
                                        Permintaan serah tugas telah dihantar kepada <strong>{{ $calon['nama'] ?? '-' }}</strong> ({{ $calon['email'] ?? '' }}).
                                        Tuan/puan kekal sebagai Pentadbir Surau sehingga calon log masuk dan menerima serah tugas.
                                    </p>
                                @endif
                                @if($m->bilangan === 1)
                                    <p><strong>Surat Pentauliahan Anggota Jawatankuasa</strong> kini boleh dicetak di halaman Status Pengesahan.</p>
                                @elseif($p->pentadbir_pilihan !== 'BAHARU')
                                    <p>Tiada tindakan lanjut diperlukan.</p>
                                @endif
                            @else
                                <p>Sila log masuk, betulkan item yang <strong>ditolak</strong> di halaman Keputusan Mesyuarat dan hantar semula untuk pengesahan JAWI.</p>
                            @endif

                            <div style="text-align:center; margin:28px 0;">
                                <a href="{{ $url }}" style="background-color:#1e40af; color:#ffffff; padding:14px 34px; text-decoration:none; border-radius:8px; font-weight:bold; font-size:15px; display:inline-block;">
                                    Lihat Status Pengesahan
                                </a>
                            </div>

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
