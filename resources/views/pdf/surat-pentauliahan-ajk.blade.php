@php
    use App\Services\SuratPentauliahanService as S;
    $namaSurau = $surau->nama_surau;
    $tajukSurau = mb_strtoupper($namaSurau . ($alamatSebaris ? ', ' . $alamatSebaris : ''));
    $singkatan = fn ($j) => $j === 'Timbalan Pengerusi' ? 'Timb. Pengerusi' : $j;
    $namaPegawai = $penandatangan ? mb_strtoupper($penandatangan->name) : null;
@endphp
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Surat Pentauliahan AJK - {{ $namaSurau }}</title>
    <style>
        @page { margin: 2.3cm 2.1cm 1.6cm 2.4cm; }
        .kepala-wrap { margin-top: -1.1cm; }
        body { font-family: Helvetica, Arial, sans-serif; font-size: 11.5pt; line-height: 1.32; color: #000; }
        p { margin: 0 0 12px; text-align: justify; }
        .b { font-weight: bold; }
        .i { font-style: italic; }
        .kepala td { vertical-align: top; }
        .kecil { font-size: 7.5pt; line-height: 1.25; color: #222; }
        .rujukan td { padding: 0; font-size: 11.5pt; vertical-align: top; }
        .lampiran th, .lampiran td { border: 1px solid #000; padding: 3px 6px; font-size: 10.5pt; }
        .lampiran th { font-weight: bold; text-align: center; }
        .pecah { page-break-before: always; }
        .tak-pecah { page-break-inside: avoid; }
        .blok-ttd { line-height: 1.3; }
    </style>
</head>
<body>

    {{-- ── KEPALA SURAT ── --}}
    <div class="kepala-wrap">
    @if($kepalaSurat)
        <img src="{{ $kepalaSurat }}" style="width:100%; margin-bottom:14px;">
    @else
        <table class="kepala" width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td style="width:66px; padding-top:4px;"><img src="{{ $jata }}" style="width:62px;"></td>
                <td style="padding-left:6px; white-space:nowrap;">
                    <img src="{{ $tulisanJawi }}" style="height:22px; margin:0 0 1px 1px;">
                    <div style="font-size:10.8pt; line-height:1.05;">JABATAN AGAMA ISLAM WILAYAH PERSEKUTUAN</div>
                    <div style="font-size:8.6pt; font-style:italic; line-height:1.1;">FEDERAL TERRITORY ISLAMIC AFFAIRS DEPARTMENT</div>
                    <div style="font-size:7.8pt; line-height:1.2; margin-top:1px;">Kompleks Pusat Islam<br>Jalan Perdana<br>50676 Kuala Lumpur</div>
                </td>
                <td style="width:112px; padding:20px 0 0 8px; white-space:nowrap;" class="kecil">
                    Tel &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: +603-2274 9333<br>
                    Faks &nbsp;&nbsp;: +603-2273 1575<br>
                    E-mail : info@jawi.gov.my<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;aduan@jawi.gov.my<br>
                    Web &nbsp;&nbsp;: www.jawi.gov.my
                </td>
                <td style="width:58px; text-align:right;"><img src="{{ $logo }}" style="width:56px;"></td>
            </tr>
        </table>
        <div style="border-bottom:1.2px solid #000; margin:6px 0 18px;"></div>
    @endif
    </div>

    {{-- ── RUJUKAN & TARIKH ── --}}
    <table class="rujukan" cellpadding="0" cellspacing="0" style="margin-left:52%; margin-bottom:26px;">
        <tr><td style="text-align:right; padding-right:4px;">Ruj. Kami</td><td>: {{ $mesyuarat->surat_no_rujukan }}</td></tr>
        <tr><td style="text-align:right; padding-right:4px;">Tarikh</td><td>: {{ $tarikhMasihi }}M</td></tr>
        <tr><td></td><td>&nbsp;&nbsp;{{ $tarikhHijri }}</td></tr>
    </table>

    {{-- ── PENERIMA ── --}}
    <div style="margin-bottom:14px;">
        Pengerusi<br>
        {{ $namaSurau }}<br>
        @foreach($alamatBaris as $baris){{ $baris }}<br>@endforeach
    </div>

    <p class="i" style="margin-bottom:14px;">Assalamualaikum warahmatullah wbt.</p>
    <p>Tuan,</p>

    <p class="b" style="margin-bottom:16px;">PELANTIKAN SEBAGAI ANGGOTA JAWATANKUASA {{ $tajukSurau }}</p>

    <p>Dengan hormatnya saya menarik perhatian tuan kepada perkara di atas.</p>

    <p>2.&nbsp;&nbsp;&nbsp;&nbsp;Sukacita dimaklumkan bahawa Jabatan Agama Islam Wilayah Persekutuan (JAWI) bersetuju dengan pelantikan anggota Jawatankuasa
        <span class="b">{{ $namaSurau }}{{ $alamatSebaris ? ', ' . $alamatSebaris : '' }}</span>
        berkuat kuasa mulai <span class="b">{{ $tempohMula }} hingga {{ $tempohTamat }}</span>.
        Senarai AJK <span class="b">{{ $namaSurau }}</span> adalah seperti di <span class="b">LAMPIRAN.</span></p>

    <p>3.&nbsp;&nbsp;&nbsp;&nbsp;Sehubungan dengan itu, anggota jawatankuasa <span class="b">{{ $namaSurau }}</span> dikehendaki mentadbir surau sejajar dengan
        kehendak Majlis Agama Islam Wilayah Persekutuan dan Jabatan Agama Islam Wilayah Persekutuan termasuk mengurus kewangan surau secara
        teratur selaras dengan <span class="b">Garis Panduan Pengurusan Kewangan Masjid dan Surau Wilayah Persekutuan dan Garis Panduan
        Pentadbiran Undang-Undang Islam Wilayah-Wilayah Persekutuan (Jawatankuasa Surau)</span> yang boleh diakses melalui pautan berikut:</p>

    @if($pautan)
        <p style="text-decoration:underline;">{{ $pautan }}</p>
    @endif

    <p>4.&nbsp;&nbsp;&nbsp;&nbsp;Untuk makluman tuan, bagi urusan penukaran penandatangan akaun hendaklah dilaksanakan melalui keputusan Mesyuarat
        Jawatankuasa selaras Garis Panduan Pengurusan Kewangan Masjid dan Surau Wilayah Persekutuan Perkara 29.2 memperuntukkan seperti berikut:</p>

    <div class="tak-pecah" style="margin:6px 0 14px;">
        <div class="i" style="text-decoration:underline;">Kaedah 29.2:</div>
        <div class="i b" style="text-align:justify;">&ldquo;Penukaran penandatangan cek hendaklah mendapat kelulusan daripada Jawatankuasa Kariah&rdquo;</div>
    </div>

    <p>Perhatian dan kerjasama tuan berhubung perkara ini amatlah dihargai dan didahului dengan ucapan terima kasih.</p>

    <div class="tak-pecah">
        <p class="b" style="margin-bottom:14px;">&ldquo;MALAYSIA MADANI&rdquo;</p>
        <p class="b" style="margin-bottom:14px;">&ldquo;BERKHIDMAT UNTUK NEGARA&rdquo;</p>
        <div>Saya yang menjalankan amanah,</div>
        <div style="height:58px;">
            @if($tandatangan)<img src="{{ $tandatangan }}" style="height:56px; margin-left:20px;">@endif
        </div>
        <div class="blok-ttd">
            <span class="b">({{ $namaPegawai ?? str_repeat('.', 40) }})</span><br>
            @foreach($blok as $baris){{ $baris }}<br>@endforeach
        </div>
    </div>

    @if($salinan)
        <div class="tak-pecah" style="margin-top:34px;">
            <div style="margin-bottom:12px;">s.k.:</div>
            @foreach($salinan as $penerima)
                <div style="margin-bottom:12px;">@foreach($penerima as $baris){{ $baris }}<br>@endforeach</div>
            @endforeach
        </div>
    @endif

    {{-- ── LAMPIRAN ── --}}
    <div class="pecah"></div>
    <div style="text-align:right; margin:22px 0 34px;">LAMPIRAN</div>
    <div style="text-align:center; line-height:1.3; margin-bottom:18px;">
        ANGGOTA JAWATANKUASA SURAU<br>
        WILAYAH-WILAYAH PERSEKUTUAN KUALA LUMPUR/PUTRAJAYA/LABUAN
    </div>

    <table cellpadding="0" cellspacing="0" style="margin-bottom:6px;">
        <tr><td class="b" style="width:132px; vertical-align:top; white-space:nowrap;">Nama Surau</td><td style="width:14px; vertical-align:top;">:</td><td class="b">{{ $namaSurau }}</td></tr>
        <tr><td class="b" style="vertical-align:top;">Alamat</td><td style="vertical-align:top;">:</td><td class="b">{{ $alamatSebaris ?: '-' }}</td></tr>
        <tr><td class="b" style="vertical-align:top; white-space:nowrap;">Tempoh Lantikan</td><td style="vertical-align:top;">:</td><td class="b">{{ $tempohMula }} &ndash; {{ $tempohTamat }}</td></tr>
    </table>

    <table class="lampiran" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin-bottom:26px;">
        <thead>
            <tr><th style="width:8%;">Bil.</th><th>Nama</th><th style="width:22%;">No. K/P</th><th style="width:21%;">Jawatan</th></tr>
        </thead>
        <tbody>
            @foreach($ajk as $c)
                <tr>
                    <td style="text-align:center;">{{ $loop->iteration }}.</td>
                    <td>{{ $c->nama }}</td>
                    <td style="text-align:center;">{{ S::formatKp($c->ic_no) }}</td>
                    <td style="text-align:center;">{{ $singkatan($c->jawatan) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="tak-pecah">
        <div>Disahkan oleh :</div>
        <div style="height:50px;">
            @if($tandatangan)<img src="{{ $tandatangan }}" style="height:48px; margin-left:20px;">@endif
        </div>
        <div class="blok-ttd">
            <span class="b">({{ $namaPegawai ?? str_repeat('.', 40) }})</span><br>
            @foreach($blok as $baris){{ $baris }}<br>@endforeach
        </div>
        <div style="margin-top:22px;">Tarikh : &nbsp;&nbsp;&nbsp;{{ $mesyuarat->surat_tarikh->format('d/m/Y') }}</div>
    </div>

</body>
</html>
