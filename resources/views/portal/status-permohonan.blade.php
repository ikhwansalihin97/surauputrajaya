@extends('layouts.portal')

@section('content')

@php
    /*
    |--------------------------------------------------------------------------
    | DATA & HELPER STATUS PERMOHONAN
    |--------------------------------------------------------------------------
    */
    $minitTerkini = $application->meetingMinutes
        ? $application->meetingMinutes->sortByDesc('created_at')->first()
        : null;

    $isMinitDitolak = $minitTerkini && strtoupper(trim($minitTerkini->status_pengesahan ?? '')) === 'DITOLAK';
    $isMinitPending = $minitTerkini && in_array(strtoupper(trim($minitTerkini->status_pengesahan ?? '')), ['DALAM_SEMAKAN', 'PENDING']);

    $confirmations = $application->confirmations ?? collect();

    $getLatestConfirmation = function ($peringkat) use ($confirmations, $application) {
        if ($peringkat === 'MASJID' && $application->masjidConfirmation) {
            return $application->masjidConfirmation;
        }
        if ($peringkat === 'JAWI' && $application->jawiConfirmation) {
            return $application->jawiConfirmation;
        }
        if ($peringkat === 'ZON' && $application->zonConfirmation) {
            return $application->zonConfirmation;
        }

        return $confirmations
            ->filter(fn($c) => strtoupper(trim($c->peringkat ?? '')) === strtoupper($peringkat))
            ->sortByDesc('id')
            ->first();
    };

    $masjidConf = $getLatestConfirmation('MASJID');
    $jawiConf   = $getLatestConfirmation('JAWI');
    $zonConf    = $getLatestConfirmation('ZON');

    $statusMasjid = strtoupper(trim($masjidConf?->status ?? 'MENUNGGU'));
    $statusJawi   = strtoupper(trim($jawiConf?->status ?? 'MENUNGGU'));
    $statusZon    = strtoupper(trim($zonConf?->status ?? 'MENUNGGU'));

    $masjidLulus   = in_array($statusMasjid, ['DISAHKAN', 'APPROVED']);
    $masjidDitolak = in_array($statusMasjid, ['DITOLAK', 'REJECTED']);

    $jawiLulus   = in_array($statusJawi, ['DISAHKAN', 'APPROVED']);
    $jawiDitolak = in_array($statusJawi, ['DITOLAK', 'REJECTED']);

    $zonLulus   = in_array($statusZon, ['DISAHKAN', 'APPROVED']);
    $zonDitolak = in_array($statusZon, ['DITOLAK', 'REJECTED']);

    $isOverallApproved = $zonLulus || (strtoupper(trim($application->status ?? '')) === 'APPROVED');
    $isOverallRejected = $masjidDitolak || $jawiDitolak || $zonDitolak || (strtoupper(trim($application->status ?? '')) === 'REJECTED');
@endphp

<style>
    /* ── Hero Banner Status ── */
    .hero-status-card {
        background: linear-gradient(135deg, #1e3a8a 0%, #0369a1 60%, #0284c7 100%);
        color: #ffffff;
        border-radius: 12px;
        position: relative;
        overflow: hidden;
        border: 1px solid #bfdbfe;
    }
    .hero-shape-1 {
        position: absolute;
        right: -30px;
        top: -40px;
        width: 190px;
        height: 190px;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .hero-shape-2 {
        position: absolute;
        right: 90px;
        bottom: -50px;
        width: 140px;
        height: 140px;
        background: radial-gradient(circle, rgba(56, 189, 248, 0.22) 0%, rgba(56, 189, 248, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .stepper-node {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        margin: 0 auto 8px;
    }

    .detail-label {
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 2px;
    }
    .detail-value {
        font-size: 0.88rem;
        font-weight: 600;
        color: #0f172a;
    }

    .hover-lift {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .hover-lift:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(2, 132, 199, 0.15) !important;
    }

    .print-only-header,
    .print-only-footer {
        display: none;
    }

    /* ══════════════════════════════════════════════════════
       OPTIMASI KHAS CETAKAN A4 (1 PAGE ONLY PRINT STYLES)
    ══════════════════════════════════════════════════════════ */
    @page {
        size: A4 portrait;
        margin: 6mm 8mm;
    }

    @media print {
        /* Sembunyikan navbar, footer web, butang & alert */
        nav,
        footer,
        .navbar,
        #navbarPortal,
        .d-print-none,
        .btn,
        .alert,
        #formReupload,
        .hero-shape-1,
        .hero-shape-2 {
            display: none !important;
        }

        body {
            background-color: #ffffff !important;
            color: #000000 !important;
            font-size: 10pt !important;
            font-family: 'Segoe UI', Arial, sans-serif !important;
            margin: 0 !important;
            padding: 0 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .container {
            max-width: 100% !important;
            width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        /* Tunjukkan Letterhead Rasmi A4 */
        .print-only-header {
            display: block !important;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 6px;
            margin-bottom: 8px;
        }

        .print-only-footer {
            display: block !important;
            border-top: 1px dashed #94a3b8;
            padding-top: 5px;
            margin-top: 8px;
            font-size: 7.5pt !important;
            color: #475569 !important;
        }

        /* Padatkan Hero Banner untuk Cetakan A4 */
        .hero-status-card {
            background: #0369a1 !important;
            color: #ffffff !important;
            padding: 10px 14px !important;
            border-radius: 6px !important;
            border: 1px solid #cbd5e1 !important;
            box-shadow: none !important;
            margin-bottom: 8px !important;
        }

        .hero-status-card h4 {
            font-size: 13pt !important;
            margin: 0 !important;
        }

        .hero-status-card .badge {
            border: 1px solid #ffffff !important;
            font-size: 8pt !important;
            padding: 2px 6px !important;
        }

        /* Card Kandungan */
        .main-slip-card {
            border: 1px solid #cbd5e1 !important;
            box-shadow: none !important;
            border-radius: 6px !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
            margin-bottom: 0 !important;
        }

        /* Stepper Dipadatkan */
        .stepper-container {
            padding: 6px 8px !important;
            background-color: #f8fafc !important;
            border-bottom: 1px solid #cbd5e1 !important;
        }
        .stepper-card-item {
            padding: 4px !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 4px !important;
        }
        .stepper-node {
            width: 24px !important;
            height: 24px !important;
            font-size: 8.5pt !important;
            margin-bottom: 2px !important;
        }
        .stepper-title {
            font-size: 7.5pt !important;
            line-height: 1.1 !important;
        }
        .stepper-badge {
            font-size: 6.5pt !important;
            padding: 2px 4px !important;
            margin-top: 2px !important;
        }

        /* Grid Maklumat Dipadatkan */
        .info-section {
            padding: 8px 10px !important;
        }
        .info-box {
            padding: 6px 8px !important;
            margin-bottom: 4px !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 4px !important;
            background-color: #ffffff !important;
        }
        .detail-label {
            font-size: 6.8pt !important;
            margin-bottom: 1px !important;
        }
        .detail-value {
            font-size: 8pt !important;
            line-height: 1.2 !important;
        }

        .appointment-box {
            padding: 5px 8px !important;
            margin-top: 6px !important;
            font-size: 7.5pt !important;
            border: 1px solid #cbd5e1 !important;
        }

        * {
            page-break-inside: avoid;
            break-inside: avoid;
        }
    }
</style>

<div class="container py-4">

    <!-- ── 1. BAR ALATAN ATAS (CARIAN RUJUKAN & BUTANG TINDAKAN - WEB SAHAJA) ── -->
    <div class="d-print-none mb-3">
        <div class="card border-0 shadow-sm rounded-3 bg-white" style="border-left: 5px solid #0284c7 !important;">
            <div class="card-body p-3">
                <div class="row g-2 align-items-center justify-content-between">
                    {{-- Form Carian Rujukan Lain --}}
                    <div class="col-lg-7">
                        <form action="{{ route('permohonan.semak') }}" method="GET" class="d-flex align-items-center gap-2">
                            <span class="fw-bold text-dark small text-nowrap">
                                <i class="fa-solid fa-magnifying-glass me-1" style="color: #0284c7;"></i> Semak Rujukan Lain:
                            </span>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light text-muted font-monospace"><i class="fa-solid fa-ticket"></i></span>
                                <input type="text" name="reference_no" class="form-control font-monospace text-uppercase fw-bold" placeholder="SURAU-XXXXXX" value="{{ $application->reference_no }}" required>
                                <button type="submit" class="btn btn-primary fw-semibold" style="background-color:#0284c7; border-color:#0284c7;">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Butang Cetak A4 & Kembali Ke Portal --}}
                    <div class="col-lg-5 text-lg-end d-flex gap-2 justify-content-lg-end">
                        <a href="{{ route('portal') }}" class="btn btn-sm btn-outline-secondary px-3">
                            <i class="fa-solid fa-arrow-left me-1"></i> Utama
                        </a>
                        <button onclick="window.print()" class="btn btn-sm btn-primary fw-bold px-3.5 shadow-sm hover-lift" style="background-color: #0284c7; border-color: #0284c7;">
                            <i class="fa-solid fa-print me-1.5"></i> Cetak Slip (1 Muka Surat A4)
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── NOTIFIKASI SUCCESS & ERROR (WEB SAHAJA) ── -->
    <div class="d-print-none">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    <!-- ── LETTERHEAD KHAS CETAKAN (MUNCUL SEMASA PRINT SAHAJA) ── -->
    <div class="print-only-header">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <div style="font-size: 22pt; color: #0284c7;"><i class="fa-solid fa-kaaba"></i></div>
                <div>
                    <div style="font-size: 12pt; font-weight: 800; letter-spacing: 0.04em; color: #0f172a; line-height: 1.1;">
                        JABATAN AGAMA ISLAM WILAYAH PERSEKUTUAN (JAWI)
                    </div>
                    <div style="font-size: 8.5pt; font-weight: 600; color: #475569;">
                        SISTEM PENGURUSAN SURAU (E-SURAU JAWI) — SLIP PENGESAHAN STATUS
                    </div>
                </div>
            </div>
            <div class="text-end" style="font-size: 7.5pt; color: #64748b;">
                <div>Tarikh Cetakan: <strong>{{ now()->translatedFormat('d F Y, h:i A') }}</strong></div>
                <div class="font-monospace">Ref: <strong>{{ $application->reference_no }}</strong></div>
            </div>
        </div>
    </div>

    <!-- ── 2. HERO STATUS PERMOHONAN (DIREKA SEMULA DENGAN KEMAS & MENARIK) ── -->
    <div class="hero-status-card p-4 mb-4 shadow-sm">
        <div class="hero-shape-1"></div>
        <div class="hero-shape-2"></div>
        <div class="row align-items-center position-relative">
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                    <span class="badge bg-white bg-opacity-25 px-2.5 py-1 text-white fw-semibold" style="font-size:0.72rem; letter-spacing:0.04em;">
                        <i class="fa-solid fa-shield-halved me-1"></i> STATUS PERMOHONAN SURAU
                    </span>
                    <span class="badge bg-white text-primary font-monospace px-2.5 py-1 fw-bold" style="font-size:0.75rem;">
                        <i class="fa-solid fa-ticket me-1"></i> {{ $application->reference_no }}
                    </span>
                    <span class="badge bg-white bg-opacity-25 px-2.5 py-1 text-white" style="font-size:0.72rem;">
                        <i class="fa-regular fa-calendar-check me-1"></i> {{ $application->created_at ? $application->created_at->format('d/m/Y h:i A') : '-' }}
                    </span>
                </div>
                <h3 class="fw-bold text-white mb-1" style="letter-spacing: -0.01em;">{{ $application->nama_surau }}</h3>
                <p class="mb-0 text-white-50" style="font-size: 0.85rem;">
                    Kariah Masjid: <strong class="text-white">{{ $application->masjid?->nama_masjid ?? $application->kariah_masjid ?? '-' }}</strong>
                    <span class="mx-1.5">•</span>
                    Kategori: <strong class="text-white">{{ $application->kategori_surau ?? '-' }}</strong>
                </p>
            </div>

            {{-- Lencana Status Utama di Sebelah Kanan --}}
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                @if($isOverallApproved)
                    <div class="d-inline-flex align-items-center gap-2 bg-white text-success px-3.5 py-2.5 rounded-3 shadow-sm">
                        <i class="fa-solid fa-circle-check fs-4"></i>
                        <div class="text-start">
                            <div class="fw-bold font-monospace" style="font-size: 0.90rem; line-height: 1.1;">LULUS &amp; DISAHKAN</div>
                            <small class="text-muted" style="font-size: 0.70rem;">Pendaftaran Selesai</small>
                        </div>
                    </div>
                @elseif($isMinitDitolak)
                    <div class="d-inline-flex align-items-center gap-2 bg-danger text-white px-3.5 py-2.5 rounded-3 shadow-sm">
                        <i class="fa-solid fa-file-circle-xmark fs-4"></i>
                        <div class="text-start">
                            <div class="fw-bold font-monospace" style="font-size: 0.90rem; line-height: 1.1;">PEMBETULAN MINIT</div>
                            <small class="text-white-50" style="font-size: 0.70rem;">Perlu Muat Naik Semula</small>
                        </div>
                    </div>
                @elseif($isOverallRejected)
                    <div class="d-inline-flex align-items-center gap-2 bg-white text-danger px-3.5 py-2.5 rounded-3 shadow-sm">
                        <i class="fa-solid fa-circle-xmark fs-4"></i>
                        <div class="text-start">
                            <div class="fw-bold font-monospace" style="font-size: 0.90rem; line-height: 1.1;">PERMOHONAN DITOLAK</div>
                            <small class="text-muted" style="font-size: 0.70rem;">Tidak Diluluskan</small>
                        </div>
                    </div>
                @else
                    <div class="d-inline-flex align-items-center gap-2 bg-white px-3.5 py-2.5 rounded-3 shadow-sm" style="color: #0284c7;">
                        <i class="fa-solid fa-clock-rotate-left fs-4"></i>
                        <div class="text-start">
                            <div class="fw-bold font-monospace" style="font-size: 0.90rem; line-height: 1.1;">DALAM PROSES</div>
                            <small class="text-muted" style="font-size: 0.70rem;">Menunggu Pengesahan</small>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- ── 3. KAD KANDUNGAN SLIP STATUS (STEPPER & BUTIRAN PERMOHONAN) ── -->
    <div class="card border-0 shadow-sm rounded-3 main-slip-card overflow-hidden mb-4">

        <!-- STEPPER ALIRAN PERINGKAT PENGESAHAN -->
        <div class="stepper-container p-3 bg-light border-bottom">
            <div class="text-uppercase fw-bold text-muted mb-2 small" style="letter-spacing: 0.05em; font-size: 0.75rem;">
                <i class="fa-solid fa-diagram-project me-1" style="color:#0284c7;"></i> Aliran Peringkat Pengesahan Pendaftaran Surau:
            </div>
            <div class="row text-center g-2">
                <!-- STEP 1: Permohonan Dihantar -->
                <div class="col-3">
                    <div class="p-2 border rounded-2 bg-white h-100 stepper-card-item border-top border-3" style="border-top-color: #0284c7 !important;">
                        <div class="stepper-node text-white shadow-sm" style="background-color: #0284c7;">
                            <i class="fa-solid fa-file-signature"></i>
                        </div>
                        <div class="fw-bold stepper-title text-dark">1. Permohonan</div>
                        <small class="text-muted d-block font-monospace d-print-none" style="font-size: 0.70rem;">{{ $application->created_at ? $application->created_at->format('d/m/Y') : '-' }}</small>
                        <span class="badge border stepper-badge" style="background-color: #e0f2fe; color: #0369a1; border-color: #bae6fd !important;">SELESAI</span>
                    </div>
                </div>

                <!-- STEP 2: Masjid Kariah -->
                <div class="col-3">
                    <div class="p-2 border rounded-2 bg-white h-100 stepper-card-item border-top border-3 {{ $masjidLulus ? 'border-success' : ($masjidDitolak ? 'border-danger' : '') }}" style="{{ !$masjidLulus && !$masjidDitolak ? 'border-top-color: #0284c7 !important;' : '' }}">
                        <div class="stepper-node {{ $masjidLulus ? 'bg-success text-white' : ($masjidDitolak ? 'bg-danger text-white' : 'text-white') }} shadow-sm" style="{{ !$masjidLulus && !$masjidDitolak ? 'background-color: #0284c7;' : '' }}">
                            <i class="fa-solid {{ $masjidLulus ? 'fa-check' : ($masjidDitolak ? 'fa-xmark' : 'fa-mosque') }}"></i>
                        </div>
                        <div class="fw-bold stepper-title text-dark">2. Kariah Masjid</div>
                        <small class="text-muted d-block text-truncate d-print-none" style="font-size: 0.70rem;">{{ $application->masjid?->nama_masjid ?? 'Masjid' }}</small>
                        @if($masjidLulus)
                            <span class="badge bg-success text-white stepper-badge"><i class="fa-solid fa-check"></i> DISAHKAN</span>
                        @elseif($masjidDitolak)
                            <span class="badge bg-danger text-white stepper-badge"><i class="fa-solid fa-xmark"></i> DITOLAK</span>
                        @else
                            <span class="badge border stepper-badge" style="background-color: #e0f2fe; color: #0369a1; border-color: #bae6fd !important;"><i class="fa-solid fa-clock"></i> PROSES</span>
                        @endif
                    </div>
                </div>

                <!-- STEP 3: JAWI & Minit -->
                <div class="col-3">
                    <div class="p-2 border rounded-2 bg-white h-100 stepper-card-item border-top border-3 {{ $jawiLulus ? 'border-success' : ($isMinitDitolak || $jawiDitolak ? 'border-danger' : 'border-secondary') }}">
                        <div class="stepper-node {{ $jawiLulus ? 'bg-success text-white' : ($isMinitDitolak || $jawiDitolak ? 'bg-danger text-white' : 'bg-secondary text-white') }} shadow-sm">
                            <i class="fa-solid {{ $jawiLulus ? 'fa-check' : ($isMinitDitolak ? 'fa-file-circle-xmark' : ($jawiDitolak ? 'fa-xmark' : 'fa-building-columns')) }}"></i>
                        </div>
                        <div class="fw-bold stepper-title text-dark">3. Semakan JAWI</div>
                        <small class="text-muted d-block d-print-none" style="font-size: 0.70rem;">Dokumen &amp; Minit</small>
                        @if($jawiLulus)
                            <span class="badge bg-success text-white stepper-badge"><i class="fa-solid fa-check"></i> DISAHKAN</span>
                        @elseif($isMinitDitolak)
                            <span class="badge bg-danger text-white stepper-badge"><i class="fa-solid fa-xmark"></i> DITOLAK</span>
                        @elseif($jawiDitolak)
                            <span class="badge bg-danger text-white stepper-badge"><i class="fa-solid fa-xmark"></i> DITOLAK</span>
                        @else
                            <span class="badge bg-secondary text-white stepper-badge"><i class="fa-solid fa-clock"></i> MENUNGGU</span>
                        @endif
                    </div>
                </div>

                <!-- STEP 4: Pentadbir Zon -->
                <div class="col-3">
                    <div class="p-2 border rounded-2 bg-white h-100 stepper-card-item border-top border-3 {{ $zonLulus ? 'border-success' : ($zonDitolak ? 'border-danger' : 'border-secondary') }}">
                        <div class="stepper-node {{ $zonLulus ? 'bg-success text-white' : ($zonDitolak ? 'bg-danger text-white' : 'bg-secondary text-white') }} shadow-sm">
                            <i class="fa-solid {{ $zonLulus ? 'fa-award' : ($zonDitolak ? 'fa-xmark' : 'fa-map-location-dot') }}"></i>
                        </div>
                        <div class="fw-bold stepper-title text-dark">4. Kelulusan Zon</div>
                        <small class="text-muted d-block d-print-none" style="font-size: 0.70rem;">Kelulusan Akhir</small>
                        @if($zonLulus)
                            <span class="badge bg-success text-white stepper-badge"><i class="fa-solid fa-check"></i> DILULUSKAN</span>
                        @elseif($zonDitolak)
                            <span class="badge bg-danger text-white stepper-badge"><i class="fa-solid fa-xmark"></i> DITOLAK</span>
                        @else
                            <span class="badge bg-secondary text-white stepper-badge"><i class="fa-solid fa-clock"></i> MENUNGGU</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- BUTIRAN LENGKAP PERMOHONAN (2 KOLUM SEIMBANG) -->
        <div class="info-section p-3 p-md-4">
            <div class="row g-3">

                {{-- Kolum Kiri: Maklumat Surau & Lokasi --}}
                <div class="col-md-6">
                    <div class="card border-0 bg-light rounded-2 p-3 h-100 info-box">
                        <div class="fw-bold text-dark mb-2 pb-1 border-bottom d-flex align-items-center gap-1.5" style="font-size: 0.85rem; color: #0284c7 !important;">
                            <i class="fa-solid fa-kaaba"></i> Maklumat Surau &amp; Kariah
                        </div>

                        <div class="row g-2">
                            <div class="col-6">
                                <div class="detail-label">Nama Surau</div>
                                <div class="detail-value text-truncate" title="{{ $application->nama_surau }}">{{ $application->nama_surau }}</div>
                            </div>
                            <div class="col-6">
                                <div class="detail-label">Kategori Surau</div>
                                <div class="detail-value">{{ $application->kategori_surau ?? '-' }}</div>
                            </div>
                            <div class="col-12">
                                <div class="detail-label">Masjid Kariah</div>
                                <div class="detail-value">{{ $application->masjid?->nama_masjid ?? $application->kariah_masjid ?? '-' }}</div>
                            </div>
                            <div class="col-12">
                                <div class="detail-label">Alamat / Lokasi Surau</div>
                                <div class="detail-value small" style="line-height: 1.3;">
                                    {{ $application->alamat_surau ?? '-' }}
                                    @if($application->kawasan_surau)
                                        <br><span class="text-muted">Kawasan: {{ $application->kawasan_surau }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="detail-label">Taraf Tapak</div>
                                <div class="detail-value">{{ $application->taraf_tapak ?? '-' }}</div>
                            </div>
                            <div class="col-6">
                                <div class="detail-label">No. Lot / Geran</div>
                                <div class="detail-value font-monospace">{{ $application->no_lot_geran ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Kolum Kanan: Maklumat Pemohon & Akaun Bank --}}
                <div class="col-md-6">
                    <div class="card border-0 bg-light rounded-2 p-3 h-100 info-box">
                        <div class="fw-bold text-dark mb-2 pb-1 border-bottom d-flex align-items-center gap-1.5" style="font-size: 0.85rem; color: #0284c7 !important;">
                            <i class="fa-solid fa-user-check"></i> Maklumat Pemohon &amp; Pengurusan
                        </div>

                        <div class="row g-2">
                            <div class="col-12">
                                <div class="detail-label">Nama Pemohon</div>
                                <div class="detail-value">{{ $application->pemohon_nama ?? '-' }}</div>
                            </div>
                            <div class="col-6">
                                <div class="detail-label">No. Kad Pengenalan</div>
                                <div class="detail-value font-monospace">{{ $application->pemohon_ic ?? '-' }}</div>
                            </div>
                            <div class="col-6">
                                <div class="detail-label">Jawatan Pemohon</div>
                                <div class="detail-value">{{ $application->pemohon_jawatan ?? 'Pemohon' }}</div>
                            </div>
                            <div class="col-6">
                                <div class="detail-label">No. Telefon</div>
                                <div class="detail-value font-monospace">{{ $application->pemohon_tel ?? $application->no_tel_surau ?? '-' }}</div>
                            </div>
                            <div class="col-6">
                                <div class="detail-label">Emel</div>
                                <div class="detail-value text-truncate" title="{{ $application->pemohon_email }}">{{ $application->pemohon_email ?? '-' }}</div>
                            </div>
                            <div class="col-12 pt-1 border-top mt-1">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="detail-label">Nama Bank Surau</div>
                                        <div class="detail-value">{{ $application->nama_bank ?? '-' }}</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="detail-label">No. Akaun Bank</div>
                                        <div class="detail-value font-monospace">{{ $application->no_akaun_bank ?? '-' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- INFO TEMUJANJI PEMERIKSAAN (JIKA ADA) --}}
            @if($application->appointment)
                <div class="card border-0 rounded-2 p-2.5 mt-2.5 bg-white border border-info appointment-box" style="border-left: 4px solid #0284c7 !important; background-color: #f0f9ff !important;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-calendar-check fs-5 text-primary"></i>
                        <div class="flex-grow-1">
                            <strong class="d-block" style="color: #0369a1; font-size: 0.85rem;">Temujanji Pemeriksaan Fizikal Surau:</strong>
                            <span class="small text-dark">
                                Tarikh &amp; Masa: <strong>{{ \Carbon\Carbon::parse($application->appointment->tarikh_temujanji)->format('d/m/Y') }}@if($application->appointment->masa_temujanji) ({{ \Carbon\Carbon::parse($application->appointment->masa_temujanji)->format('h:i A') }})@endif</strong>
                                @if($application->appointment->tempat || $application->appointment->lokasi)
                                    | Lokasi: <strong>{{ $application->appointment->tempat ?? $application->appointment->lokasi }}</strong>
                                @endif
                                @if($application->appointment->catatan)
                                    <span class="text-muted ms-1">— <em>"{{ $application->appointment->catatan }}"</em></span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Catatan Penolakan Minit (Jika Ada) --}}
            @if($isMinitDitolak && $minitTerkini?->catatan_semakan)
                <div class="card border-0 rounded-2 p-2.5 mt-2 bg-danger bg-opacity-10 border border-danger border-opacity-25">
                    <div class="small text-danger">
                        <strong><i class="fa-solid fa-triangle-exclamation me-1"></i> Ulasan Semakan JAWI:</strong>
                        <span class="text-dark">{{ $minitTerkini->catatan_semakan }}</span>
                    </div>
                </div>
            @endif

        </div>

    </div>

    <!-- ── 4. BORANG MUAT NAIK SEMULA MINIT (WEB SAHAJA JIKA DITOLAK) ── -->
    @if($isMinitDitolak)
        <div class="card border-danger border-opacity-50 shadow-sm rounded-3 mb-4 d-print-none">
            <div class="card-header bg-danger text-white fw-bold py-2.5 px-3">
                <i class="fa-solid fa-triangle-exclamation me-1.5"></i> Tindakan Pembetulan: Muat Naik Semula Minit Mesyuarat
            </div>
            <div class="card-body p-3">
                <form action="{{ route('permohonan.uploadSemulaMinit', $application->id) }}" method="POST" enctype="multipart/form-data" id="formReupload" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label for="minit_mesyuarat" class="form-label fw-bold text-dark small">
                            <i class="fa-solid fa-file-pdf text-danger me-1"></i> Pilih Fail Minit Mesyuarat Baharu (PDF Sahaja, Maks 5MB) <span class="text-danger">*</span>
                        </label>
                        <input type="file" name="minit_mesyuarat" id="minit_mesyuarat" class="form-control" accept=".pdf" required>
                    </div>

                    {{-- CAPTCHA --}}
                    <div class="border rounded-2 bg-light p-2.5 mb-3">
                        <div class="fw-bold text-dark small mb-1"><i class="fa-solid fa-shield-halved me-1 text-primary"></i> Pengesahan Keselamatan:</div>
                        <div class="d-flex align-items-center gap-2">
                            <canvas id="captchaCanvas" width="140" height="40" class="border rounded bg-white shadow-sm"></canvas>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="btnRefreshCaptcha" title="Tukar Kod">
                                <i class="fa-solid fa-rotate"></i>
                            </button>
                            <input type="text" id="captchaInput" name="captcha" class="form-control form-control-sm font-monospace text-center fw-bold fs-6" style="max-width: 130px;" placeholder="4 Angka" maxlength="4" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-danger w-100 fw-bold py-2 shadow-sm">
                        <i class="fa-solid fa-paper-plane me-1"></i> Hantar Minit Mesyuarat Pembetulan
                    </button>
                </form>
            </div>
        </div>
    @endif

    <!-- ── FOOTER KHAS CETAKAN (MUNCUL SEMASA PRINT SAHAJA) ── -->
    <div class="print-only-footer">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <div>Dokumen ini dijana secara automatik oleh <strong>Sistem Pengurusan Surau (e-Surau JAWI)</strong>.</div>
                <div>Sebarang pertanyaan, sila hubungi Bahagian Pengurusan Masjid / Surau JAWI.</div>
            </div>
            <div class="text-end font-monospace">
                <div>Cetakan Berkomputer</div>
                <div>Tidak Perlu Tandatangan Fizikal</div>
            </div>
        </div>
    </div>

    <!-- ── 5. BUTANG BAWAH (WEB SAHAJA) ── -->
    <div class="d-flex justify-content-between align-items-center d-print-none pt-2">
        <a href="{{ route('portal') }}" class="btn btn-outline-secondary fw-semibold px-3">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali Ke Portal Awam
        </a>
        <button onclick="window.print()" class="btn btn-primary fw-bold px-4 shadow-sm" style="background-color: #0284c7; border-color: #0284c7;">
            <i class="fa-solid fa-print me-1.5"></i> Cetak Slip Permohonan (1 Muka Surat A4)
        </button>
    </div>

</div>

<!-- CAPTCHA JAVASCRIPT -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const formReupload = document.getElementById('formReupload');
        if (!formReupload) return;

        const canvas = document.getElementById('captchaCanvas');
        const ctx = canvas ? canvas.getContext('2d') : null;
        const btnRefresh = document.getElementById('btnRefreshCaptcha');
        const captchaInput = document.getElementById('captchaInput');
        let generatedCode = '';

        function generateCaptcha() {
            generatedCode = Math.floor(1000 + Math.random() * 9000).toString();
            if (!canvas || !ctx) return;

            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.fillStyle = '#f0f9ff';
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            for (let i = 0; i < 4; i++) {
                ctx.strokeStyle = '#bae6fd';
                ctx.beginPath();
                ctx.moveTo(Math.random() * canvas.width, Math.random() * canvas.height);
                ctx.lineTo(Math.random() * canvas.width, Math.random() * canvas.height);
                ctx.stroke();
            }

            ctx.font = 'bold 22px monospace';
            ctx.fillStyle = '#0284c7';
            ctx.textBaseline = 'middle';

            for (let i = 0; i < generatedCode.length; i++) {
                const x = 16 + (i * 28);
                const y = 20 + (Math.random() * 4 - 2);
                ctx.fillText(generatedCode[i], x, y);
            }

            if (captchaInput) captchaInput.value = '';
        }

        generateCaptcha();

        if (btnRefresh) {
            btnRefresh.addEventListener('click', generateCaptcha);
        }

        formReupload.addEventListener('submit', function (e) {
            e.preventDefault();

            const fileInput = document.getElementById('minit_mesyuarat');
            if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'warning', title: 'Pilih Fail Minit', text: 'Sila pilih fail PDF Minit Mesyuarat baharu.', confirmButtonColor: '#dc3545' });
                } else {
                    alert('Sila pilih fail PDF Minit Mesyuarat baharu.');
                }
                return;
            }

            const userCode = captchaInput ? captchaInput.value.trim() : '';
            if (userCode !== generatedCode) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'error', title: 'CAPTCHA Tidak Tepat', text: 'Nombor pengesahan yang dimasukkan tidak tepat.', confirmButtonColor: '#dc3545' });
                } else {
                    alert('Nombor pengesahan CAPTCHA tidak tepat.');
                }
                generateCaptcha();
                return;
            }

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'question',
                    title: 'Hantar Minit Mesyuarat?',
                    text: 'Adakah anda pasti ingin menghantar fail minit mesyuarat baharu ini?',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hantar',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#0284c7',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (result.isConfirmed) {
                        HTMLFormElement.prototype.submit.call(formReupload);
                    }
                });
            } else {
                if (confirm('Adakah anda pasti ingin menghantar fail minit mesyuarat baharu ini?')) {
                    HTMLFormElement.prototype.submit.call(formReupload);
                }
            }
        });
    });
</script>

@endsection