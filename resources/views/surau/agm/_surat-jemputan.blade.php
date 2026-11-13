{{--
    Kandungan surat jemputan Mesyuarat Agung Tahunan (dikongsi oleh e-mel dan lampiran PDF).
    Pemboleh ubah: $mesyuarat, $jemputan, $pengirim (nama, jawatan, no_tel, email)
--}}
@php
    $sesi   = $mesyuarat->sesi;
    $surau  = $sesi->surau;
    $tajuk  = "Mesyuarat Agung Tahunan {$surau->nama_surau} Bil. {$mesyuarat->bilangan} Tahun {$mesyuarat->tahun}";
    $akhir  = $mesyuarat->tarikhAkhirPengesahan();
    $label  = 'width:110px; padding:3px 0; vertical-align:top;';
    $titik  = 'width:18px; padding:3px 0; vertical-align:top;';
    $nombor = 'width:34px; vertical-align:top; padding:0 0 14px;';
    $perenggan = 'vertical-align:top; padding:0 0 14px; text-align:justify;';
@endphp

<p style="margin:0 0 18px; font-weight:bold; text-transform:uppercase; line-height:1.5;">
    Jemputan ke {{ $tajuk }}
</p>

<p style="margin:0 0 14px;">Assalamualaikum dan Salam Sejahtera,</p>
<p style="margin:0 0 14px;">Tuan/Puan {{ $jemputan->nama }},</p>
<p style="margin:0 0 14px; text-align:justify;">Dengan segala hormatnya saya merujuk kepada perkara tersebut di atas.</p>

<table cellpadding="0" cellspacing="0" style="width:100%; border-collapse:collapse;">
    <tr>
        <td style="{{ $nombor }}">2.</td>
        <td style="{{ $perenggan }}">
            Untuk makluman, {{ $tajuk }} bagi sesi {{ $sesi->label }} akan diadakan seperti ketetapan berikut:
            <table cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin:12px 0 0 0;">
                <tr>
                    <td style="{{ $label }}">Tarikh</td>
                    <td style="{{ $titik }}">:</td>
                    <td style="padding:3px 0; vertical-align:top; font-weight:bold;">{{ $mesyuarat->tarikhMelayu() }}</td>
                </tr>
                <tr>
                    <td style="{{ $label }}">Masa</td>
                    <td style="{{ $titik }}">:</td>
                    <td style="padding:3px 0; vertical-align:top; font-weight:bold;">{{ $mesyuarat->masaMelayu() }}</td>
                </tr>
                <tr>
                    <td style="{{ $label }}">Tempat</td>
                    <td style="{{ $titik }}">:</td>
                    <td style="padding:3px 0; vertical-align:top; font-weight:bold;">{{ $mesyuarat->lokasi }}</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td style="{{ $nombor }}">3.</td>
        <td style="{{ $perenggan }}">
            Sehubungan dengan itu, tuan/puan adalah dengan segala hormatnya dijemput hadir ke mesyuarat ini
            dan dimohon untuk membuat pengesahan kehadiran kepada pihak urus setia
            @if($akhir)
                selewat-lewatnya pada {{ $mesyuarat->tarikhMelayu($akhir) }}, 12.00 tengah hari.
            @else
                secepat mungkin.
            @endif
        </td>
    </tr>
    <tr>
        <td style="{{ $nombor }}">4.</td>
        <td style="{{ $perenggan }}">
            Kerjasama dan perhatian tuan/puan dalam perkara ini amatlah dihargai dan didahului dengan ucapan ribuan terima kasih.
        </td>
    </tr>
</table>

<p style="margin:6px 0 30px;">Sekian, terima kasih.</p>

<p style="margin:0; line-height:1.5;">
    <strong>{{ $pengirim['nama'] }}</strong><br>
    {{ $pengirim['jawatan'] }}<br>
    {{ $surau->nama_surau }}@if($surau->masjid), Kariah {{ $surau->masjid->nama_masjid }}@endif<br>
    @if(!empty($pengirim['no_tel'])){{ $pengirim['no_tel'] }}<br>@endif
    @if(!empty($pengirim['email'])){{ $pengirim['email'] }}@endif
</p>
