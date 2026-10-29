<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Borang Pendaftaran Surau - {{ $application->reference_no }}</title>
    <style>
        @page {
            size: a4 portrait;
            margin: 12mm 18mm 12mm 18mm;
        }

        body {
            font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #000;
        }

        .page {
            page-break-after: always;
            position: relative;
            min-height: 100%;
        }

        .page:last-child {
            page-break-after: avoid;
        }

        /* Header Style */
        .header-container {
            text-align: center;
            margin-bottom: 12px;
        }

        .jata-logo {
            width: 65px;
            height: auto;
            margin: 0 auto 6px auto;
            display: block;
        }

        .header-title-1 {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            letter-spacing: 0.5px;
        }

        .header-title-2 {
            font-size: 10.5pt;
            font-weight: bold;
            margin: 2px 0 0 0;
        }

        .header-title-3 {
            font-size: 11pt;
            font-weight: bold;
            margin: 10px 0 0 0;
            letter-spacing: 0.5px;
        }

        .ref-badge {
            text-align: right;
            font-size: 8.5pt;
            color: #444;
            margin-bottom: 4px;
        }

        .nota-box {
            margin: 8px 0 14px 0;
            font-size: 9.5pt;
            line-height: 1.35;
            text-align: justify;
        }

        .nota-label {
            font-weight: bold;
            float: left;
            width: 55px;
        }

        .nota-text {
            margin-left: 55px;
        }

        /* Form item styling */
        .form-row {
            margin-bottom: 8px;
            font-size: 9.5pt;
        }

        .form-label {
            display: inline;
        }

        .dotted-fill {
            border-bottom: 1px dotted #333;
            display: inline;
            padding: 0 4px;
            font-weight: bold;
            color: #0b2545;
        }

        .dotted-line {
            border-bottom: 1px dotted #777;
            display: block;
            height: 18px;
            margin-top: 2px;
        }

        .fill-text {
            font-weight: bold;
            color: #0b2545;
        }

        /* Checkbox styling */
        .checkbox-box {
            display: inline-block;
            width: 17px;
            height: 17px;
            border: 1.2px solid #000;
            text-align: center;
            vertical-align: middle;
            font-size: 11pt;
            line-height: 16px;
            font-weight: bold;
            margin-left: 6px;
        }

        table.clean-table {
            width: 100%;
            border-collapse: collapse;
        }

        table.clean-table td {
            padding: 2.5px 0;
            vertical-align: middle;
            font-size: 9.5pt;
        }

        /* Page 3 Declaration & Signatures */
        .declaration-text {
            font-style: italic;
            font-size: 9pt;
            text-align: justify;
            margin: 12px 0 18px 0;
            line-height: 1.35;
            padding: 0 5px;
        }

        .sig-section {
            margin-top: 14px;
            padding-top: 6px;
        }

        .sig-box {
            border: 1px solid #ccc;
            padding: 8px 12px;
            background-color: #fafafa;
            border-radius: 4px;
            margin-top: 4px;
        }

        .office-header {
            text-align: center;
            font-weight: bold;
            font-size: 11pt;
            margin-top: 40px;
            margin-bottom: 30px;
            text-decoration: underline;
            letter-spacing: 0.5px;
        }

        .footer-page-num {
            position: absolute;
            bottom: 5px;
            right: 10px;
            font-size: 8pt;
            color: #777;
        }

        .stamp-box {
            border: 1.5px dashed #0284c7;
            background: #f0f9ff;
            color: #0369a1;
            padding: 6px 10px;
            font-size: 8pt;
            border-radius: 4px;
            display: inline-block;
            margin-top: 4px;
        }
    </style>
</head>
<body>

    {{-- =========================================================================
         MUKA SURAT 1
         ========================================================================= --}}
    <div class="page">
        <div class="ref-badge">
            No. Rujukan: <strong>{{ $application->reference_no }}</strong>
        </div>

        <div class="header-container">
            @if(!empty($jataBase64))
                <img src="{{ $jataBase64 }}" class="jata-logo" alt="Jata Negara">
            @endif
            <div class="header-title-1">JABATAN AGAMA ISLAM WILAYAH PERSEKUTUAN</div>
            <div class="header-title-2">(JABATAN PERDANA MENTERI)</div>
            <div class="header-title-3">BORANG PENDAFTARAN SURAU / MADRASAH</div>
        </div>

        <div class="nota-box">
            <div class="nota-label">NOTA:</div>
            <div class="nota-text">
                Borang ini hendaklah diisi dalam ruangan-ruangan berkenaan dengan lengkap, terang dan jelas mengikut butir-butir yang dikehendaki. Borang yang tidak lengkap, terang dan jelas tidak akan dilayan.
            </div>
            <div style="clear: both;"></div>
        </div>

        {{-- 1. Nama Surau --}}
        <div class="form-row">
            1. Nama Surau/Madrasah : 
            <span class="dotted-fill">{{ $application->nama_surau ?? '-' }}</span>
        </div>

        {{-- 2. Alamat Surau --}}
        <div class="form-row">
            2. Alamat Surau/Madrasah: 
            <span class="dotted-fill">{{ $application->alamat_surau ?? '-' }}</span>
        </div>

        {{-- 3. Alamat surat menyurat --}}
        <div class="form-row">
            3. Alamat surat menyurat : 
            <span class="dotted-fill">{{ $application->alamat_surat ?? '-' }}</span>
        </div>

        {{-- 4. Dalam kariah masjid --}}
        <div class="form-row">
            <table class="clean-table" style="width: 100%;">
                <tr>
                    <td style="width: 62%;">
                        4. Dalam kariah masjid : 
                        <span class="dotted-fill">{{ $application->kariah_masjid ?? $application->masjid?->nama ?? '-' }}</span>
                    </td>
                    <td style="width: 38%; text-align: right;">
                        No. Telefon (Jika Ada): 
                        <span class="dotted-fill">{{ $application->no_tel_surau ?? '-' }}</span>
                    </td>
                </tr>
            </table>
        </div>

        {{-- 5. Kawasan Surau/Madrasah didirikan --}}
        <div class="form-row" style="margin-top: 6px;">
            5. Kawasan Surau/Madrasah didirikan :-
            <table class="clean-table" style="margin-left: 20px; width: 92%; margin-top: 4px;">
                @php
                    $kawasan = trim($application->kawasan_surau ?? '');
                    $isTaman = ($kawasan === 'Taman Perumahan');
                    $isKampung = ($kawasan === 'Perkampungan Tetap');
                    $isSetinggan = ($kawasan === 'Setinggan');
                    $isFlat = ($kawasan === 'Rumah Pangsa/Flat' || str_contains(strtolower($kawasan), 'flat') || str_contains(strtolower($kawasan), 'pangsa'));
                    $isLain = ($kawasan === 'Lain-lain' || (!empty($application->kawasan_lain) && !$isTaman && !$isKampung && !$isSetinggan && !$isFlat));
                @endphp
                <tr>
                    <td style="width: 75%;">a) Kawasan Taman Perumahan</td>
                    <td style="width: 25%; text-align: right;"><div class="checkbox-box">{!! $isTaman ? '&#10003;' : '&nbsp;' !!}</div></td>
                </tr>
                <tr>
                    <td>b) Kawasan Perkampungan Tetap</td>
                    <td style="text-align: right;"><div class="checkbox-box">{!! $isKampung ? '&#10003;' : '&nbsp;' !!}</div></td>
                </tr>
                <tr>
                    <td>c) Kawasan Setinggan</td>
                    <td style="text-align: right;"><div class="checkbox-box">{!! $isSetinggan ? '&#10003;' : '&nbsp;' !!}</div></td>
                </tr>
                <tr>
                    <td>d) Unit Rumah Pangsa/Flat</td>
                    <td style="text-align: right;"><div class="checkbox-box">{!! $isFlat ? '&#10003;' : '&nbsp;' !!}</div></td>
                </tr>
                <tr>
                    <td colspan="2">
                        e) Lain-lain Tempat (Nyatakan) : 
                        @if($isLain)
                            <span class="dotted-fill">{{ $application->kawasan_lain ?? $application->kawasan_surau }}</span>
                        @else
                            <span style="border-bottom: 1px dotted #777; display: inline-block; width: 320px;">&nbsp;</span>
                        @endif
                    </td>
                </tr>
            </table>
            <div style="text-align: center; font-weight: bold; margin: 4px 0 8px 0; font-size: 8.5pt;">
                (Tandakan ( &#10003; ) mana yang perkenaan)
            </div>
        </div>

        {{-- 6. Jarak dengan surau berdekatan --}}
        <div class="form-row">
            6. Jarak dengan surau/madrasah berdekatan 
            <span class="dotted-fill">{{ $application->jarak_surau_berdekatan ? $application->jarak_surau_berdekatan . ' k.m.' : '-' }}</span> 
            dan nyatakan nama surau/madrasah berkenaan : 
            <span class="dotted-fill">{{ $application->nama_surau_berdekatan ?? '-' }}</span>
        </div>

        {{-- 7. Kedudukan taraf tapak bangunan --}}
        <div class="form-row" style="margin-top: 6px;">
            7. Kedudukan taraf tapak bangunan :-
            @php
                $taraf = trim($application->taraf_tapak ?? '');
                $isWakaf = ($taraf === 'Diwakafkan');
                $isKhas = ($taraf === 'Dikhaskan');
                $isKebenaran = ($taraf === 'Kebenaran');
            @endphp
            <table class="clean-table" style="margin-left: 20px; width: 92%; margin-top: 4px;">
                <tr>
                    <td style="width: 75%;">a) Diwakafkan</td>
                    <td style="width: 25%; text-align: right;"><div class="checkbox-box">{!! $isWakaf ? '&#10003;' : '&nbsp;' !!}</div></td>
                </tr>
                <tr>
                    <td>b) Dikhaskan</td>
                    <td style="text-align: right;"><div class="checkbox-box">{!! $isKhas ? '&#10003;' : '&nbsp;' !!}</div></td>
                </tr>
                <tr>
                    <td>c) Kebenaran</td>
                    <td style="text-align: right;"><div class="checkbox-box">{!! $isKebenaran ? '&#10003;' : '&nbsp;' !!}</div></td>
                </tr>
            </table>
            <div style="font-size: 8.5pt; margin-top: 4px; line-height: 1.35; text-align: justify;">
                <strong>(Tandakan ( &#10003; ) mana yang berkenaan dan sertakan salinan surat surat yang berkaitan serta no lot tanah atau salinan geran tanah).</strong>
                @if(!empty($application->no_lot_geran))
                    <div style="margin-top: 2px;">
                        No. Lot / Salinan Geran: <span class="dotted-fill">{{ $application->no_lot_geran }}</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="footer-page-num">Muka Surat 1/4</div>
    </div>


    {{-- =========================================================================
         MUKA SURAT 2
         ========================================================================= --}}
    <div class="page">
        <div class="ref-badge">
            No. Rujukan: <strong>{{ $application->reference_no }}</strong>
        </div>

        {{-- 8. Bangunan --}}
        <div class="form-row">
            8. Bangunan :-
            <table class="clean-table" style="margin-left: 20px; width: 95%; margin-top: 4px;">
                <tr>
                    <td style="width: 32%;">a) Kayu/Batu</td>
                    <td style="width: 68%;">: <span class="dotted-fill">{{ $application->jenis_bangunan ?? '-' }}</span></td>
                </tr>
                <tr>
                    <td>b) Setingkat/Dua Tingkat</td>
                    <td>: <span class="dotted-fill">{{ $application->tingkat_bangunan ?? '-' }}</span></td>
                </tr>
                <tr>
                    <td>c) Tahun Didirikan</td>
                    <td>: <span class="dotted-fill">{{ $application->tahun_didirikan ?? '-' }}</span></td>
                </tr>
                <tr>
                    <td>d) Jumlah Perbelanjaan Pembinaan</td>
                    <td>: <span class="dotted-fill">RM {{ number_format($application->jumlah_perbelanjaan ?? 0, 2) }}</span></td>
                </tr>
            </table>
        </div>

        {{-- 9. Maklumat Bank --}}
        <div class="form-row" style="margin-top: 8px;">
            9. Nama dan alamat bank serta nombor kira-kira dan penama-penama (jika ada):-
            <div style="margin-left: 20px; margin-top: 4px;">
                <table class="clean-table" style="width: 95%;">
                    <tr>
                        <td style="width: 30%;">Nama Bank</td>
                        <td style="width: 70%;">: <span class="dotted-fill">{{ $application->nama_bank ?? '-' }}</span></td>
                    </tr>
                    <tr>
                        <td>Nombor Akaun</td>
                        <td>: <span class="dotted-fill">{{ $application->no_akaun_bank ?? '-' }}</span></td>
                    </tr>
                    <tr>
                        <td>Alamat Cawangan Bank</td>
                        <td>: <span class="dotted-fill">{{ $application->alamat_bank ?? '-' }}</span></td>
                    </tr>
                    <tr>
                        <td>Penama-Penama Akaun</td>
                        <td>: <span class="dotted-fill">{{ $application->penama_akaun ?? '-' }}</span></td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- 10. Tempoh Mesyuarat Agung --}}
        <div class="form-row" style="margin-top: 8px;">
            10. Tempoh setiap mesyuarat agong pemilihan Ahli jawatankuasa baru diadakan 
            @php
                $minitTerkini = $application->meetingMinutes?->first();
                $tempohAgm = $application->tempoh_mesyuarat_agung ?? ($minitTerkini?->tahun_minit ? 'Setiap 2 Tahun' : 'Setiap 2 Tahun');
            @endphp
            <span class="dotted-fill">{{ $tempohAgm }}</span>
            dan nyatakan tarikh genapnya tempuh tersebut 
            <span class="dotted-fill">{{ $application->tarikh_genap_tempoh ? \Carbon\Carbon::parse($application->tarikh_genap_tempoh)->format('d/m/Y') : '-' }}</span>
        </div>

        {{-- 11. Jumlah Penduduk Islam --}}
        <div class="form-row" style="margin-top: 8px;">
            11. Jumlah penduduk Islam dalam kawasan/madrasah :
            <table class="clean-table" style="margin-left: 20px; width: 70%; margin-top: 4px;">
                <tr>
                    <td style="width: 35%;">a) Lelaki</td>
                    <td style="width: 65%;">: <span class="dotted-fill">{{ number_format($application->penduduk_lelaki ?? 0) }} orang</span></td>
                </tr>
                <tr>
                    <td>b) Perempuan</td>
                    <td>: <span class="dotted-fill">{{ number_format($application->penduduk_perempuan ?? 0) }} orang</span></td>
                </tr>
            </table>
        </div>

        {{-- 12. Aktiviti Agama --}}
        <div class="form-row" style="margin-top: 8px;">
            12. Aktiviti-aktiviti agama :-
            <table class="clean-table" style="margin-left: 20px; width: 90%; margin-top: 4px;">
                <tr>
                    <td style="width: 35%;">a) Kelas Pengajian</td>
                    <td style="width: 65%;">: <span class="dotted-fill">{{ $application->kelas_pengajian_sebulan ?? 0 }}</span> kali sebulan</td>
                </tr>
                <tr>
                    <td>b) Ceramah/Syarahan Umum</td>
                    <td>: <span class="dotted-fill">{{ $application->ceramah_sebulan ?? 0 }}</span> kali sebulan</td>
                </tr>
                <tr>
                    <td>c) Kelas Didikan Kanak-kanak</td>
                    <td>: <span class="dotted-fill">{{ $application->kelas_kanak_sebulan ?? 0 }}</span> kali sebulan</td>
                </tr>
            </table>
        </div>

        {{-- 13. Sumber Kewangan --}}
        <div class="form-row" style="margin-top: 8px;">
            13. Sumber Kewangan Surau/Madrasah:
            @php
                $sumberList = $application->sumber_kewangan;
                if (is_string($sumberList)) {
                    $decoded = json_decode($sumberList, true);
                    $sumberList = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : array_map('trim', explode(',', $sumberList));
                }
                $sumberList = is_array($sumberList) ? array_values(array_filter($sumberList)) : [];
                $romanNumerals = ['i', 'ii', 'iii', 'iv', 'v'];
            @endphp
            <table class="clean-table" style="margin-left: 20px; width: 95%; margin-top: 4px;">
                @for($k = 0; $k < 5; $k++)
                    <tr>
                        <td style="width: 5%;">{{ $romanNumerals[$k] }}.</td>
                        <td style="width: 95%;">
                            @if(isset($sumberList[$k]))
                                <span class="dotted-fill">{{ $sumberList[$k] }}</span>
                            @else
                                <span style="border-bottom: 1px dotted #888; display: inline-block; width: 90%;">&nbsp;</span>
                            @endif
                        </td>
                    </tr>
                @endfor
            </table>
        </div>

        {{-- 14. Dokumen Wajib --}}
        <div class="form-row" style="margin-top: 8px;">
            14. Sertakan <strong>DOKUMEN-DOKUMEN WAJIB</strong> seperti berikut :-
            @php
                $hasMinit = $application->meetingMinutes && $application->meetingMinutes->count() > 0;
                $hasAjk = $application->committees && $application->committees->count() > 0;
                $hasDocs = $application->documents && $application->documents->count() > 0;
            @endphp
            <table class="clean-table" style="margin-left: 20px; width: 95%; margin-top: 4px;">
                <tr>
                    <td style="width: 90%;">a) Minit mesyuarat terkini</td>
                    <td style="width: 10%; text-align: right;"><div class="checkbox-box">{!! $hasMinit ? '&#10003;' : '&nbsp;' !!}</div></td>
                </tr>
                <tr>
                    <td>b) Senarai Ahli Jawatankuasa beserta alamat lengkap, No. Kad Pengenalan dan No. Telefon.</td>
                    <td style="text-align: right;"><div class="checkbox-box">{!! $hasAjk ? '&#10003;' : '&nbsp;' !!}</div></td>
                </tr>
                <tr>
                    <td>c) Salinan kad pengenalan Pengerusi, Setiausaha dan Bendahari</td>
                    <td style="text-align: right;"><div class="checkbox-box">{!! $hasDocs ? '&#10003;' : '&nbsp;' !!}</div></td>
                </tr>
                <tr>
                    <td>d) Salinan surat pengesahan arah kiblat daripada Pejabat Mufti Wilayah Persekutuan</td>
                    <td style="text-align: right;"><div class="checkbox-box">{!! $hasDocs ? '&#10003;' : '&nbsp;' !!}</div></td>
                </tr>
            </table>
        </div>

        <div class="footer-page-num">Muka Surat 2/4</div>
    </div>


    {{-- =========================================================================
         MUKA SURAT 3
         ========================================================================= --}}
    <div class="page">
        <div class="ref-badge">
            No. Rujukan: <strong>{{ $application->reference_no }}</strong>
        </div>

        {{-- 15. Maklumat Pemohon & Perakuan --}}
        <div class="form-row">
            15. Nama yang membuat permohonan ini serta jawatan di surau/madrasah.
            <table class="clean-table" style="margin-left: 20px; width: 95%; margin-top: 4px;">
                <tr>
                    <td style="width: 25%;">Nama</td>
                    <td style="width: 75%;">: <span class="dotted-fill">{{ $application->pemohon_nama ?? '-' }}</span></td>
                </tr>
                <tr>
                    <td>Jawatan</td>
                    <td>: <span class="dotted-fill">{{ $application->pemohon_jawatan ?? '-' }}</span></td>
                </tr>
                <tr>
                    <td>No. K.P</td>
                    <td>: <span class="dotted-fill">{{ $application->pemohon_ic ?? '-' }}</span></td>
                </tr>
                <tr>
                    <td>No. Telefon</td>
                    <td>: <span class="dotted-fill">{{ $application->pemohon_tel ?? '-' }}</span></td>
                </tr>
            </table>

            <div class="declaration-text">
                “Dengan ini saya memperakukan bahawa segala keterangan butir-butir yang dinyatakan adalah benar dan sah, dan saya bersetuju sekiranya didapati salah dan tidak benar oleh pihak Jabatan Agama Islam Wilayah Persekutuan, maka permohonan ini patut ditolak atau dibatalkan pendaftarannya”
            </div>

            <table class="clean-table" style="width: 100%; margin-top: 10px;">
                <tr>
                    <td style="width: 50%; vertical-align: bottom;">
                        Tarikh : <span class="dotted-fill">{{ $application->created_at ? $application->created_at->format('d/m/Y') : '....................' }}</span>
                    </td>
                    <td style="width: 50%; text-align: center; vertical-align: bottom;">
                        <div style="border-bottom: 1px dotted #333; width: 200px; margin: 0 auto; height: 35px; line-height: 50px; font-size: 8.5pt; color: #555;">
                            {{ $application->pemohon_nama }}
                        </div>
                        <div style="font-size: 9pt; margin-top: 10px;">Tandatangan Pemohon</div>
                    </td>
                </tr>
            </table>
        </div>

        <hr style="border: none; border-top: 1px solid #ccc; margin: 14px 0 10px 0;">

        {{-- 16. Pengesahan dan Sokongan Masjid --}}
        @php
            $masjidConf = $application->masjidConfirmation;
            $masjidPegawai = $masjidConf?->confirmedBy;
        @endphp
        <div class="form-row sig-section" style="margin-top: 8px;">
            16. Pengesahan dan Sokongan Masjid
            <table class="clean-table" style="width: 100%; margin-top: 6px;">
                <tr>
                    <td style="width: 60%; vertical-align: top;">
                        <table class="clean-table" style="width: 100%;">
                            <tr>
                                <td style="width: 25%;">Nama</td>
                                <td style="width: 75%;">: <span class="dotted-fill">{{ $masjidPegawai?->name ?? '..................................................' }}</span></td>
                            </tr>
                            <tr>
                                <td>No. K/P</td>
                                <td>: <span class="dotted-fill">{{ $masjidPegawai?->no_kad_pengenalan ?? '..................................................' }}</span></td>
                            </tr>
                            <tr>
                                <td>Alamat</td>
                                <td>: <span class="dotted-fill">{{ $application->masjid?->alamat ?? $application->kariah_masjid ?? '..................................................' }}</span></td>
                            </tr>
                            <tr>
                                <td>Jawatan</td>
                                <td>: <span class="dotted-fill">{{ $masjidPegawai?->jawatan ?? ($masjidConf ? 'Pentadbir Masjid Kariah' : '..................................................') }}</span></td>
                            </tr>
                            <tr>
                                <td>Tarikh</td>
                                <td>: <span class="dotted-fill">{{ $masjidConf?->confirmed_at ? $masjidConf->confirmed_at->format('d/m/Y') : '..................................................' }}</span></td>
                            </tr>
                        </table>
                        @if($masjidConf?->catatan)
                            <div style="margin-top: 4px; font-size: 8.5pt; color: #444;">
                                Catatan Masjid: <em>"{{ $masjidConf->catatan }}"</em>
                            </div>
                        @endif
                    </td>
                    <td style="width: 40%; text-align: center; vertical-align: middle;">
                        @if(($masjidConf?->status ?? '') === 'APPROVED')
                            <div class="stamp-box">
                                <strong>&#10003; DISAHKAN &amp; DISOKONG</strong><br>
                                Pentadbir Masjid Kariah<br>
                                <span style="font-size: 7.5pt;">{{ $masjidConf->confirmed_at?->format('d/m/Y h:i A') }}</span>
                            </div>
                        @else
                            <div style="height: 50px; border-bottom: 1px dotted #555; width: 150px; margin: 0 auto;"></div>
                            <div style="font-size: 8.5pt; margin-top: 4px;">Tandatangan &amp; Cop Rasmi</div>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <hr style="border: none; border-top: 1px solid #ccc; margin: 14px 0 10px 0;">

        {{-- 17. Pengesahan Pejabat Pusat Zon --}}
        @php
            $zonConf = $application->zonConfirmation;
            $zonPegawai = $zonConf?->confirmedBy;
        @endphp
        <div class="form-row sig-section" style="margin-top: 8px;">
            17. Pengesahan Pejabat Pusat Zon
            <div style="margin-top: 4px;">
                Ulasan : 
                @if($zonConf?->catatan)
                    <span class="dotted-fill">{{ $zonConf->catatan }}</span>
                @else
                    <span style="border-bottom: 1px dotted #777; display: inline-block; width: 85%;">&nbsp;</span>
                @endif
            </div>

            <table class="clean-table" style="width: 100%; margin-top: 8px;">
                <tr>
                    <td style="width: 60%; vertical-align: top;">
                        <table class="clean-table" style="width: 100%;">
                            <tr>
                                <td style="width: 25%;">Nama</td>
                                <td style="width: 75%;">: <span class="dotted-fill">{{ $zonPegawai?->name ?? '..................................................' }}</span></td>
                            </tr>
                            <tr>
                                <td>Jawatan</td>
                                <td>: <span class="dotted-fill">{{ $zonPegawai?->jawatan ?? ($zonConf ? 'Pegawai Pusat Zon JAWI' : '..................................................') }}</span></td>
                            </tr>
                            <tr>
                                <td>Tarikh</td>
                                <td>: <span class="dotted-fill">{{ $zonConf?->confirmed_at ? $zonConf->confirmed_at->format('d/m/Y') : '..................................................' }}</span></td>
                            </tr>
                        </table>
                    </td>
                    <td style="width: 40%; text-align: center; vertical-align: middle;">
                        @if(($zonConf?->status ?? '') === 'APPROVED')
                            <div class="stamp-box">
                                <strong>&#10003; DISAHKAN ZON</strong><br>
                                Pejabat Pentadbiran Zon<br>
                                <span style="font-size: 7.5pt;">{{ $zonConf->confirmed_at?->format('d/m/Y h:i A') }}</span>
                            </div>
                        @else
                            <div style="height: 50px; border-bottom: 1px dotted #555; width: 150px; margin: 0 auto;"></div>
                            <div style="font-size: 8.5pt; margin-top: 4px;">Tandatangan &amp; Cop Rasmi</div>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <div class="footer-page-num">Muka Surat 3/4</div>
    </div>


    {{-- =========================================================================
         MUKA SURAT 4 (UNTUK KEGUNAAN PEJABAT)
         ========================================================================= --}}
    <div class="page">
        <div class="ref-badge">
            No. Rujukan: <strong>{{ $application->reference_no }}</strong>
        </div>

        <div class="office-header">
            (UNTUK KEGUNAAN PEJABAT)
        </div>

        @php
            $jawiConf = $application->jawiConfirmation;
            $jawiPegawai = $jawiConf?->confirmedBy;
        @endphp

        <table class="clean-table" style="width: 100%; margin: 25px auto 40px auto; font-size: 10.5pt;">
            <tr>
                <td style="width: 40%; padding: 12px 0;">1. Tarikh Permohonan Diterima</td>
                <td style="width: 60%; padding: 12px 0;">
                    : <span style="border-bottom: 1px solid #000; display: inline-block; width: 80%; padding-bottom: 2px; font-weight: bold;">
                        {{ $application->created_at ? $application->created_at->format('d/m/Y') : '' }}
                    </span>
                </td>
            </tr>
            <tr>
                <td style="padding: 12px 0;">2. Didaftarkan Di Bawah Sirial</td>
                <td style="padding: 12px 0;">
                    : <span style="border-bottom: 1px solid #000; display: inline-block; width: 80%; padding-bottom: 2px; font-weight: bold;">
                        {{ $application->reference_no ?? '' }}
                    </span>
                </td>
            </tr>
            <tr>
                <td style="padding: 12px 0;">3. Tarikh Didaftarkan</td>
                <td style="padding: 12px 0;">
                    : <span style="border-bottom: 1px solid #000; display: inline-block; width: 80%; padding-bottom: 2px; font-weight: bold;">
                        {{ $jawiConf?->confirmed_at ? $jawiConf->confirmed_at->format('d/m/Y') : '' }}
                    </span>
                </td>
            </tr>
        </table>

        <div style="margin-top: 50px;">
            <table class="clean-table" style="width: 100%;">
                <tr>
                    <td style="width: 55%; vertical-align: top;">
                        <div style="font-size: 10pt; font-weight: bold; margin-bottom: 10px;">
                            Pengesahan Pegawai,
                        </div>

                        <div style="margin-top: 55px; border-top: 1px solid #000; width: 280px; padding-top: 6px;">
                            ( <span style="font-weight: bold; padding: 0 10px;">{{ $jawiPegawai?->name ?? '                                                            ' }}</span> )
                        </div>
                        <div style="font-size: 9pt; color: #444; margin-top: 3px;">
                            Jawatan: {{ $jawiPegawai?->jawatan ?? 'Pegawai Bahagian Pengurusan Masjid JAWI' }}
                        </div>
                        @if($jawiConf?->catatan)
                            <div style="font-size: 8.5pt; color: #555; margin-top: 6px;">
                                Catatan: <em>"{{ $jawiConf->catatan }}"</em>
                            </div>
                        @endif
                    </td>
                    <td style="width: 45%; vertical-align: top; text-align: right;">
                        <div style="font-size: 10pt;">
                            Tarikh : <span style="border-bottom: 1px solid #000; display: inline-block; width: 140px; text-align: center; font-weight: bold;">
                                {{ $jawiConf?->confirmed_at ? $jawiConf->confirmed_at->format('d/m/Y') : '' }}
                            </span>
                        </div>

                        @if(($jawiConf?->status ?? '') === 'APPROVED')
                            <div style="margin-top: 35px;">
                                <div class="stamp-box" style="text-align: center; display: inline-block;">
                                    <strong style="color: #15803d; font-size: 9.5pt;">&#10003; PERMOHONAN DISAHKAN</strong><br>
                                    Jabatan Agama Islam Wilayah Persekutuan<br>
                                    <span style="font-size: 7.5pt;">Tarikh: {{ $jawiConf->confirmed_at?->format('d/m/Y') }}</span>
                                </div>
                            </div>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <div class="footer-page-num">Muka Surat 4/4</div>
    </div>

</body>
</html>
