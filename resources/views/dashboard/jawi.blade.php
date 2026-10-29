@extends('layouts.app')

@section('content')

{{-- ── 1. Hero Welcome Banner Korporat ── --}}
<div class="hero-welcome-card p-4 mb-4 shadow-sm">
    <div class="hero-shape-1"></div>
    <div class="hero-shape-2"></div>
    <div class="row align-items-center position-relative">
        <div class="col-lg-8">
            <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                <span class="badge bg-white bg-opacity-25 px-2.5 py-1 text-white fw-semibold" style="font-size:0.72rem; letter-spacing:0.04em;">
                    <i class="fa-solid fa-kaaba me-1"></i> PORTAL PENTADBIRAN JAWI
                </span>
                <span class="badge bg-white text-primary px-2.5 py-1 fw-bold" style="font-size:0.72rem;">
                    <i class="fa-solid fa-calendar-day me-1"></i> {{ now()->translatedFormat('l, d F Y') }}
                </span>
                <span class="badge bg-success bg-opacity-25 text-white border border-success border-opacity-50 px-2.5 py-1" style="font-size:0.72rem;">
                    <i class="fa-solid fa-shield-halved me-1"></i> Sesi Pentadbir Aktif
                </span>
            </div>
            <h4 class="fw-bold text-white mb-1">Selamat Datang, {{ Auth::user()->name }}!</h4>
            <p class="mb-0 text-white-50" style="font-size: 0.85rem; max-width: 650px;">
                Pusat kawalan pendaftaran surau Wilayah Persekutuan — memantau status kelulusan kariah, verifikasi dokumen statutori minit AGM, dan jadual temujanji lawatan tapak.
            </p>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <div class="d-inline-flex flex-column flex-sm-row gap-2 justify-content-lg-end">
                <a href="{{ route('admin.jawi.pengesahan.index') }}" class="btn btn-light fw-bold px-3 py-2 shadow-sm rounded-2 text-primary hover-lift" style="font-size:0.82rem;">
                    <i class="fa-solid fa-file-circle-check me-1 text-warning"></i> Semak Pending ({{ $permohonanPending ?? 0 }})
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ── 2. Top KPI Cards (6 Kad Prestasi Eksekutif) ── --}}
<div class="d-flex align-items-center justify-content-between mb-2">
    <div class="section-title mb-0">
        <i class="fa-solid fa-chart-line me-1"></i> Petunjuk Prestasi Utama (KPI) JAWI
    </div>
    <span class="badge bg-white text-secondary border shadow-sm small font-monospace px-2 py-1">
        <i class="fa-solid fa-rotate me-1 text-primary"></i>Data Terkini
    </span>
</div>

<div class="row g-3 mb-4">
    {{-- Jumlah Keseluruhan --}}
    <div class="col-xxl-2 col-md-4 col-sm-6">
        <div class="card border-0 shadow-sm rounded-3 kpi-card hover-lift h-100" style="border-left:4px solid #1e40af !important;">
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <div class="section-title mb-1">Jumlah Permohonan</div>
                        <div class="fw-bold font-monospace" style="font-size:1.85rem;color:#0f172a;line-height:1.1;">
                            {{ $totalPermohonan ?? 0 }}
                        </div>
                        <div style="font-size:0.73rem;color:#64748b;margin-top:4px;">
                            <i class="fa-solid fa-layer-group me-1 text-primary"></i>Semua rekod surau
                        </div>
                    </div>
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm"
                         style="width:42px;height:42px;background-color:#eff6ff;border:1px solid #dbeafe;">
                        <i class="fa-solid fa-file-invoice" style="color:#1e40af;font-size:1.05rem;"></i>
                    </div>
                </div>
                <div class="kpi-progress mt-2">
                    <div class="kpi-progress-bar bg-primary" style="width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Dalam Semakan JAWI --}}
    <div class="col-xxl-2 col-md-4 col-sm-6">
        <div class="card border-0 shadow-sm rounded-3 kpi-card hover-lift h-100" style="border-left:4px solid #d97706 !important;">
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <div class="section-title mb-1">Dalam Semakan JAWI</div>
                        <div class="fw-bold font-monospace" style="font-size:1.85rem;color:#0f172a;line-height:1.1;">
                            {{ $permohonanPending ?? 0 }}
                        </div>
                        <div style="font-size:0.73rem;color:#d97706;font-weight:600;margin-top:4px;">
                            <i class="fa-solid fa-clock me-1"></i>Perlu tindakan segera
                        </div>
                    </div>
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm"
                         style="width:42px;height:42px;background-color:#fffbeb;border:1px solid #fef3c7;">
                        <i class="fa-solid fa-clock" style="color:#d97706;font-size:1.05rem;"></i>
                    </div>
                </div>
                @php
                    $pendingPercent = ($totalPermohonan > 0) ? round(($permohonanPending / $totalPermohonan) * 100) : 0;
                @endphp
                <div class="kpi-progress mt-2">
                    <div class="kpi-progress-bar bg-warning" style="width: {{ $pendingPercent }}%;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Permohonan Disahkan --}}
    <div class="col-xxl-2 col-md-4 col-sm-6">
        <div class="card border-0 shadow-sm rounded-3 kpi-card hover-lift h-100" style="border-left:4px solid #16a34a !important;">
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <div class="section-title mb-1">Permohonan Lulus/Sah</div>
                        <div class="fw-bold font-monospace" style="font-size:1.85rem;color:#0f172a;line-height:1.1;">
                            {{ $permohonanLulus ?? 0 }}
                        </div>
                        <div style="font-size:0.73rem;color:#16a34a;font-weight:600;margin-top:4px;">
                            <i class="fa-solid fa-circle-check me-1"></i>Telah disahkan
                        </div>
                    </div>
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm"
                         style="width:42px;height:42px;background-color:#f0fdf4;border:1px solid #bbf7d0;">
                        <i class="fa-solid fa-circle-check" style="color:#16a34a;font-size:1.05rem;"></i>
                    </div>
                </div>
                @php
                    $lulusPercent = ($totalPermohonan > 0) ? round(($permohonanLulus / $totalPermohonan) * 100) : 0;
                @endphp
                <div class="kpi-progress mt-2">
                    <div class="kpi-progress-bar bg-success" style="width: {{ $lulusPercent }}%;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Permohonan Ditolak --}}
    <div class="col-xxl-2 col-md-4 col-sm-6">
        <div class="card border-0 shadow-sm rounded-3 kpi-card hover-lift h-100" style="border-left:4px solid #dc2626 !important;">
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <div class="section-title mb-1">Permohonan Ditolak</div>
                        <div class="fw-bold font-monospace" style="font-size:1.85rem;color:#0f172a;line-height:1.1;">
                            {{ $permohonanTolak ?? 0 }}
                        </div>
                        <div style="font-size:0.73rem;color:#dc2626;font-weight:600;margin-top:4px;">
                            <i class="fa-solid fa-circle-xmark me-1"></i>Tidak melepasi syarat
                        </div>
                    </div>
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm"
                         style="width:42px;height:42px;background-color:#fef2f2;border:1px solid #fecaca;">
                        <i class="fa-solid fa-circle-xmark" style="color:#dc2626;font-size:1.05rem;"></i>
                    </div>
                </div>
                @php
                    $tolakPercent = ($totalPermohonan > 0) ? round(($permohonanTolak / $totalPermohonan) * 100) : 0;
                @endphp
                <div class="kpi-progress mt-2">
                    <div class="kpi-progress-bar bg-danger" style="width: {{ $tolakPercent }}%;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Minit Mesyuarat Perlu Semakan --}}
    <div class="col-xxl-2 col-md-4 col-sm-6">
        <div class="card border-0 shadow-sm rounded-3 kpi-card hover-lift h-100" style="border-left:4px solid #6366f1 !important;">
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <div class="section-title mb-1">Minit AGM Pending</div>
                        <div class="fw-bold font-monospace" style="font-size:1.85rem;color:#0f172a;line-height:1.1;">
                            {{ $minitPending ?? 0 }}
                        </div>
                        <div style="font-size:0.73rem;color:#6366f1;font-weight:600;margin-top:4px;">
                            <i class="fa-solid fa-file-signature me-1"></i>Drp {{ $totalMinit ?? 0 }} fail AGM
                        </div>
                    </div>
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm"
                         style="width:42px;height:42px;background-color:#eef2ff;border:1px solid #e0e7ff;">
                        <i class="fa-solid fa-file-lines" style="color:#6366f1;font-size:1.05rem;"></i>
                    </div>
                </div>
                @php
                    $minitPercent = ($totalMinit > 0) ? round(($minitPending / $totalMinit) * 100) : 0;
                @endphp
                <div class="kpi-progress mt-2">
                    <div class="kpi-progress-bar" style="width: {{ $minitPercent }}%; background-color:#6366f1;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Lawatan Tapak / Temujanji --}}
    <div class="col-xxl-2 col-md-4 col-sm-6">
        <div class="card border-0 shadow-sm rounded-3 kpi-card hover-lift h-100" style="border-left:4px solid #0d9488 !important;">
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <div class="section-title mb-1">Lawatan Tapak</div>
                        <div class="fw-bold font-monospace" style="font-size:1.85rem;color:#0f172a;line-height:1.1;">
                            {{ $totalAppointments ?? 0 }}
                        </div>
                        <div style="font-size:0.73rem;color:#0d9488;font-weight:600;margin-top:4px;">
                            <i class="fa-solid fa-calendar-check me-1"></i>{{ ($upcomingAppointments ?? collect())->count() }} sesi akan datang
                        </div>
                    </div>
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm"
                         style="width:42px;height:42px;background-color:#f0fdfa;border:1px solid #ccfbf1;">
                        <i class="fa-solid fa-map-location-dot" style="color:#0d9488;font-size:1.05rem;"></i>
                    </div>
                </div>
                <div class="kpi-progress mt-2">
                    <div class="kpi-progress-bar" style="width: 100%; background-color:#0d9488;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── 3. Saluran Aliran Kerja Kelulusan Surau (Workflow Pipeline Funnel) ── --}}
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between" style="border-bottom:1px solid #eff6ff;">
        <div class="fw-bold d-flex align-items-center gap-2" style="font-size:0.875rem; color:#0f172a;">
            <i class="fa-solid fa-diagram-project" style="color:#1e40af;"></i>
            <span>Saluran Aliran Kerja Pengesahan Permohonan Surau</span>
        </div>
        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25" style="font-size:0.70rem;">
            Kedudukan Semasa Peringkat
        </span>
    </div>
    <div class="card-body p-3">
        <div class="row g-3">
            {{-- Peringkat 1: Kariah Masjid --}}
            <div class="col-lg-3 col-sm-6">
                <div class="p-3 rounded-3 h-100 hover-lift" style="background-color:#f8fafc; border:1px solid #e2e8f0; border-left:4px solid #64748b !important;">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25" style="font-size:0.68rem;">
                            Fasa 1
                        </span>
                        <i class="fa-solid fa-mosque text-secondary opacity-50"></i>
                    </div>
                    <div class="fw-bold text-dark font-monospace" style="font-size:1.45rem;">
                        {{ $workflowStages['masjid'] ?? 0 }}
                    </div>
                    <div class="fw-semibold text-dark mt-1" style="font-size:0.8rem;">Sokongan Kariah Masjid</div>
                    <div class="text-muted small" style="font-size:0.72rem;">Belum disahkan oleh Pentadbir Masjid</div>
                </div>
            </div>

            {{-- Peringkat 2: Semakan JAWI (Highlight) --}}
            <div class="col-lg-3 col-sm-6">
                <div class="p-3 rounded-3 h-100 hover-lift" style="background-color:#eff6ff; border:1px solid #bfdbfe; border-left:4px solid #1e40af !important;">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="badge rounded-pill bg-primary text-white fw-bold" style="font-size:0.68rem;">
                            Fasa 2 • Fokus JAWI
                        </span>
                        <i class="fa-solid fa-kaaba text-primary"></i>
                    </div>
                    <div class="fw-bold text-primary font-monospace" style="font-size:1.45rem;">
                        {{ $workflowStages['jawi'] ?? 0 }}
                    </div>
                    <div class="fw-semibold text-primary mt-1" style="font-size:0.8rem;">Pengesahan Peringkat JAWI</div>
                    <div class="text-muted small" style="font-size:0.72rem;">Sokongan masjid selesai, menanti tindakan JAWI</div>
                </div>
            </div>

            {{-- Peringkat 3: Pengesahan Zon --}}
            <div class="col-lg-3 col-sm-6">
                <div class="p-3 rounded-3 h-100 hover-lift" style="background-color:#f8fafc; border:1px solid #e2e8f0; border-left:4px solid #0284c7 !important;">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="badge rounded-pill bg-info bg-opacity-10 text-info border border-info border-opacity-25" style="font-size:0.68rem;">
                            Fasa 3
                        </span>
                        <i class="fa-solid fa-map-location-dot text-info opacity-75"></i>
                    </div>
                    <div class="fw-bold text-dark font-monospace" style="font-size:1.45rem;">
                        {{ $workflowStages['zon'] ?? 0 }}
                    </div>
                    <div class="fw-semibold text-dark mt-1" style="font-size:0.8rem;">Pengesahan Pentadbir Zon</div>
                    <div class="text-muted small" style="font-size:0.72rem;">JAWI telah sahkan, menanti perakuan zon</div>
                </div>
            </div>

            {{-- Peringkat 4: Selesai Diluluskan --}}
            <div class="col-lg-3 col-sm-6">
                <div class="p-3 rounded-3 h-100 hover-lift" style="background-color:#f0fdf4; border:1px solid #bbf7d0; border-left:4px solid #16a34a !important;">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="badge rounded-pill bg-success text-white fw-bold" style="font-size:0.68rem;">
                            Fasa 4 • Selesai
                        </span>
                        <i class="fa-solid fa-circle-check text-success"></i>
                    </div>
                    <div class="fw-bold text-success font-monospace" style="font-size:1.45rem;">
                        {{ $workflowStages['completed'] ?? 0 }}
                    </div>
                    <div class="fw-semibold text-success mt-1" style="font-size:0.8rem;">Pendaftaran Lengkap Diluluskan</div>
                    <div class="text-muted small" style="font-size:0.72rem;">Surau didaftarkan secara rasmi</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── 4. ANALISIS VISUAL (Row 1: Trend Bulanan + Status Permohonan) ── --}}
<div class="row g-3 mb-4">
    {{-- Carta 1: Trend Permohonan Bulanan --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between" style="border-bottom: 1px solid #eff6ff;">
                <div class="fw-bold d-flex align-items-center gap-2" style="font-size:0.875rem; color:#0f172a;">
                    <i class="fa-solid fa-chart-line text-primary"></i>
                    <span>Trend Permohonan Pendaftaran Surau ({{ now()->year }})</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light text-dark border font-monospace" style="font-size:0.70rem;">
                        12 Bulan (Jan - Dis)
                    </span>
                </div>
            </div>
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div style="width: 100%; height: 260px;">
                    <canvas id="chartTrendBulananJawi"></canvas>
                </div>
                <div class="d-flex align-items-center justify-content-between pt-2 border-top mt-2 text-muted small" style="font-size:0.75rem;">
                    <div>
                        <i class="fa-solid fa-circle-info me-1 text-primary"></i>
                        Menunjukkan jumlah permohonan baharu didaftarkan ke dalam sistem bagi tahun semasa.
                    </div>
                    <div class="fw-semibold text-dark">
                        Jumlah Tahunan: <span class="badge bg-primary text-white font-monospace">{{ array_sum($monthlyApplications ?? []) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Carta 2: Status Permohonan (Donut) --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between" style="border-bottom: 1px solid #eff6ff;">
                <div class="fw-bold d-flex align-items-center gap-2" style="font-size:0.875rem; color:#0f172a;">
                    <i class="fa-solid fa-chart-pie text-primary"></i>
                    <span>Pecahan Status Permohonan</span>
                </div>
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25" style="font-size:0.68rem;">Carta Donut</span>
            </div>
            <div class="card-body p-3 d-flex flex-column justify-content-center align-items-center" style="min-height: 260px;">
                <div style="width: 100%; max-width: 250px; height: 200px;">
                    <canvas id="chartStatusPermohonan"></canvas>
                </div>
                <div class="text-center text-muted small mt-3 pt-2 border-top w-100" style="font-size:0.75rem;">
                    Kadar Kelulusan/Sah: <strong class="text-success font-monospace">{{ $totalPermohonan > 0 ? round(($permohonanLulus / $totalPermohonan) * 100) : 0 }}%</strong>
                    &nbsp;|&nbsp;
                    Kadar Penolakan: <strong class="text-danger font-monospace">{{ $totalPermohonan > 0 ? round(($permohonanTolak / $totalPermohonan) * 100) : 0 }}%</strong>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── 5. ANALISIS VISUAL (Row 2: 3 Carta Tambahan Berkaitan) ── --}}
<div class="row g-3 mb-4">
    {{-- Carta 3: Status Semakan Minit Mesyuarat (AGM) --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between" style="border-bottom: 1px solid #eff6ff;">
                <div class="fw-bold d-flex align-items-center gap-2" style="font-size:0.85rem; color:#0f172a;">
                    <i class="fa-solid fa-file-lines" style="color:#6366f1;"></i>
                    <span>Status Minit Mesyuarat (AGM)</span>
                </div>
                <span class="badge bg-light text-secondary border font-monospace" style="font-size:0.68rem;">Carta Bar</span>
            </div>
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div style="width: 100%; height: 210px;">
                    <canvas id="chartStatusMinit"></canvas>
                </div>
                <div class="text-center text-muted small mt-2 pt-2 border-top" style="font-size:0.75rem;">
                    Jumlah Minit Disemak: <strong class="text-dark font-monospace">{{ $totalMinit ?? 0 }}</strong> fail
                    ({{ $totalMinit > 0 ? round(($minitDisahkan / $totalMinit) * 100) : 0 }}% disahkan)
                </div>
            </div>
        </div>
    </div>

    {{-- Carta 4: Pecahan Mengikut Kategori Surau --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between" style="border-bottom: 1px solid #eff6ff;">
                <div class="fw-bold d-flex align-items-center gap-2" style="font-size:0.85rem; color:#0f172a;">
                    <i class="fa-solid fa-kaaba text-primary"></i>
                    <span>Pecahan Kategori Surau</span>
                </div>
                <span class="badge bg-light text-secondary border font-monospace" style="font-size:0.68rem;">Polar / Donut</span>
            </div>
            <div class="card-body p-3 d-flex flex-column justify-content-between align-items-center">
                @if(($kategoriSurauCounts ?? collect())->isNotEmpty())
                    <div style="width: 100%; max-width: 250px; height: 210px;">
                        <canvas id="chartKategoriSurauJawi"></canvas>
                    </div>
                    <div class="text-center text-muted small mt-2 pt-2 border-top w-100" style="font-size:0.75rem;">
                        Jumlah Kategori Aktif: <strong class="text-primary font-monospace">{{ $kategoriSurauCounts->count() }}</strong> jenis
                    </div>
                @else
                    <div class="text-center py-5 text-muted small">
                        <i class="fa-solid fa-chart-pie fa-2x mb-2 d-block opacity-40"></i>
                        Tiada data kategori surau ditemui.
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Carta 5: Taraf Tapak Tanah Surau --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between" style="border-bottom: 1px solid #eff6ff;">
                <div class="fw-bold d-flex align-items-center gap-2" style="font-size:0.85rem; color:#0f172a;">
                    <i class="fa-solid fa-map-pin" style="color:#0ea5e9;"></i>
                    <span>Taraf Tapak Tanah Surau</span>
                </div>
                <span class="badge bg-light text-secondary border font-monospace" style="font-size:0.68rem;">Carta Bar</span>
            </div>
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                @if(($tarafTapakCounts ?? collect())->isNotEmpty())
                    <div style="width: 100%; height: 210px;">
                        <canvas id="chartTarafTapakJawi"></canvas>
                    </div>
                    <div class="text-center text-muted small mt-2 pt-2 border-top" style="font-size:0.75rem;">
                        Status Pegangan Tanah Surau Berdaftar
                    </div>
                @else
                    <div class="text-center py-5 text-muted small">
                        <i class="fa-solid fa-chart-simple fa-2x mb-2 d-block opacity-40"></i>
                        Tiada data taraf tapak tanah ditemui.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ── 6. Bahagian Utama: Permohonan Terkini & Temujanji Lawatan Tapak ── --}}
<div class="row g-3 mb-4">
    {{-- Kolum Kiri: Jadual Permohonan Terkini JAWI --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between" style="border-bottom:2px solid #eff6ff;">
                <div>
                    <div class="fw-bold" style="font-size:0.875rem; color:#0f172a;">
                        <i class="fa-solid fa-list-check me-2" style="color:#1e40af;"></i>Permohonan Terkini — Memerlukan Perhatian JAWI
                    </div>
                    <div class="text-muted small" style="font-size:0.72rem;">
                        Senarai permohonan terkini pendaftaran surau dan dokumen berkaitan.
                    </div>
                </div>
                <a href="{{ route('admin.jawi.pengesahan.index') }}"
                   class="btn btn-sm fw-semibold hover-lift"
                   style="background-color:#eff6ff;color:#1e40af;border:1px solid #bfdbfe;font-size:0.78rem;border-radius:6px;">
                    Lihat Semua <i class="fa-solid fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead>
                            <tr>
                                <th class="ps-3">Surau &amp; No. Rujukan</th>
                                <th>Kariah Masjid</th>
                                <th>Tarikh Masuk</th>
                                <th class="text-center">Status JAWI</th>
                                <th class="text-center">Status Minit</th>
                                <th class="text-center pe-3">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($senaraiPermohonanTerkini ?? [] as $app)
                                @php
                                    $minit = $app->meetingMinutes->sortByDesc('created_at')->first();
                                    $initial = strtoupper(substr($app->nama_surau ?? 'S', 0, 1));
                                    $jawiConf = $app->jawiConfirmation;
                                    $jawiStatus = $jawiConf?->status;
                                @endphp
                                <tr>
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center gap-2.5">
                                            <div class="avatar-initial">{{ $initial }}</div>
                                            <div>
                                                <div class="fw-bold text-dark text-truncate" style="max-width:180px; font-size:0.84rem;" title="{{ $app->nama_surau }}">
                                                    {{ $app->nama_surau }}
                                                </div>
                                                <span class="font-monospace text-primary fw-semibold" style="font-size:0.72rem;">{{ $app->reference_no }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="color:#475569; font-size:0.78rem;">
                                        <div class="text-truncate" style="max-width:140px;" title="{{ $app->masjid?->nama_masjid ?? $app->kariah_masjid ?? '-' }}">
                                            <i class="fa-solid fa-mosque me-1 text-secondary opacity-50"></i>
                                            {{ $app->masjid?->nama_masjid ?? $app->kariah_masjid ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="font-monospace" style="color:#64748b;font-size:0.75rem;">
                                        <i class="fa-regular fa-clock me-1 opacity-50"></i>
                                        {{ $app->created_at ? $app->created_at->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="text-center">
                                        @if($jawiStatus === 'APPROVED')
                                            <span class="badge-status" style="background-color:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;">
                                                <i class="fa-solid fa-check me-1"></i>Disahkan
                                            </span>
                                        @elseif($jawiStatus === 'REJECTED')
                                            <span class="badge-status" style="background-color:#fef2f2;color:#dc2626;border:1px solid #fecaca;">
                                                <i class="fa-solid fa-xmark me-1"></i>Ditolak
                                            </span>
                                        @else
                                            <span class="badge-status" style="background-color:#fffbeb;color:#92400e;border:1px solid #fde68a;">
                                                <i class="fa-solid fa-clock me-1"></i>Menunggu
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(!$minit)
                                            <span class="badge-status" style="background-color:#f1f5f9;color:#64748b;border:1px solid #e2e8f0;font-size:0.70rem;">
                                                Tiada Fail
                                            </span>
                                        @elseif($minit->status_pengesahan === 'DISAHKAN')
                                            <span class="badge-status" style="background-color:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;font-size:0.70rem;">
                                                Disahkan
                                            </span>
                                        @elseif($minit->status_pengesahan === 'DITOLAK')
                                            <span class="badge-status" style="background-color:#fef2f2;color:#dc2626;border:1px solid #fecaca;font-size:0.70rem;">
                                                Ditolak
                                            </span>
                                        @else
                                            <span class="badge-status" style="background-color:#fffbeb;color:#92400e;border:1px solid #fde68a;font-size:0.70rem;">
                                                Perlu Semakan
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center pe-3">
                                        <a href="{{ route('admin.jawi.pengesahan.show', $app->id) }}"
                                           class="btn btn-sm fw-semibold hover-lift"
                                           style="background-color:#1e40af;color:#fff;border:none;border-radius:6px;font-size:0.75rem;padding: 4px 10px;">
                                            <i class="fa-solid fa-eye me-1"></i> Semak
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5" style="color:#94a3b8;">
                                        <i class="fa-solid fa-circle-check fa-2x mb-2 d-block" style="color:#22c55e;"></i>
                                        Tiada permohonan terkini yang memerlukan tindakan semakan pada masa ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Kolum Kanan: Temujanji Lawatan Tapak & Tindakan Pantas --}}
    <div class="col-lg-4">
        <div class="d-flex flex-column gap-3">
            {{-- Widget: Temujanji Lawatan Tapak Terdekat --}}
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between" style="border-bottom:1px solid #eff6ff;">
                    <div class="fw-bold d-flex align-items-center gap-2" style="font-size:0.85rem; color:#0f172a;">
                        <i class="fa-solid fa-calendar-check text-teal" style="color:#0d9488;"></i>
                        <span>Jadual Lawatan Tapak Terdekat</span>
                    </div>
                    <span class="badge bg-light text-secondary border font-monospace" style="font-size:0.68rem;">
                        {{ ($upcomingAppointments ?? collect())->count() }} Sesi
                    </span>
                </div>
                <div class="card-body p-3">
                    @forelse($upcomingAppointments ?? [] as $apt)
                        <div class="p-2.5 rounded-3 mb-2" style="background-color:#f8fafc; border:1px solid #e2e8f0; border-left:3px solid #0d9488 !important;">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="badge bg-white text-dark border font-monospace fw-bold" style="font-size:0.70rem;">
                                    <i class="fa-regular fa-calendar me-1 text-primary"></i>
                                    {{ $apt->tarikh_temujanji ? $apt->tarikh_temujanji->format('d/m/Y') : '-' }}
                                    @if($apt->masa_temujanji)
                                        • {{ $apt->masa_temujanji->format('h:i A') }}
                                    @endif
                                </span>
                                @if($apt->application)
                                    <a href="{{ route('admin.jawi.pengesahan.show', $apt->application->id) }}" class="btn btn-xs btn-outline-primary py-0 px-1.5" style="font-size:0.68rem;">
                                        Lihat
                                    </a>
                                @endif
                            </div>
                            <div class="fw-bold text-dark text-truncate" style="font-size:0.82rem;">
                                {{ $apt->application?->nama_surau ?? 'Surau' }}
                            </div>
                            <div class="text-muted text-truncate small" style="font-size:0.72rem;">
                                <i class="fa-solid fa-location-dot me-1 text-secondary opacity-50"></i>
                                {{ $apt->tempat ?? 'Tapak Surau' }}
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-3 text-muted small">
                            <i class="fa-regular fa-calendar-xmark fa-2x mb-2 d-block opacity-40"></i>
                            Tiada temujanji lawatan tapak yang dijadualkan dalam masa terdekat.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Widget: Tindakan Pantas Pentadbir JAWI --}}
            <div class="card border-0 shadow-sm rounded-3" style="border-left:4px solid #1e40af !important;">
                <div class="card-body p-3">
                    <div class="section-title mb-2.5">
                        <i class="fa-solid fa-bolt me-1 text-warning"></i> Tindakan Pantas Pentadbir JAWI
                    </div>
                    <div class="d-flex flex-column gap-2">
                        <a href="{{ route('admin.jawi.pengesahan.index') }}"
                           class="d-flex align-items-center justify-content-between p-2.5 rounded-3 text-decoration-none hover-lift"
                           style="background-color:#f8fafc; border:1px solid #e2e8f0;">
                            <div class="d-flex align-items-center gap-2.5">
                                <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width:34px;height:34px;background-color:#eff6ff;color:#1e40af;border:1px solid #bfdbfe;">
                                    <i class="fa-solid fa-file-circle-check" style="font-size:0.95rem;"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size:0.82rem;">Pengesahan Permohonan</div>
                                    <div style="font-size:0.70rem;color:#64748b;">Semakan status permohonan surau</div>
                                </div>
                            </div>
                            <span class="badge bg-warning text-dark font-monospace fw-bold" style="font-size:0.70rem;">
                                {{ $permohonanPending ?? 0 }}
                            </span>
                        </a>

                        <a href="{{ route('admin.jawi.pengesahan.index') }}"
                           class="d-flex align-items-center justify-content-between p-2.5 rounded-3 text-decoration-none hover-lift"
                           style="background-color:#f8fafc; border:1px solid #e2e8f0;">
                            <div class="d-flex align-items-center gap-2.5">
                                <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width:34px;height:34px;background-color:#eef2ff;color:#6366f1;border:1px solid #e0e7ff;">
                                    <i class="fa-solid fa-file-lines" style="font-size:0.95rem;"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size:0.82rem;">Semakan Minit AGM</div>
                                    <div style="font-size:0.70rem;color:#64748b;">Pengesahan dokumen statutori</div>
                                </div>
                            </div>
                            <span class="badge bg-primary bg-opacity-10 text-primary font-monospace fw-bold" style="font-size:0.70rem;">
                                {{ $minitPending ?? 0 }}
                            </span>
                        </a>

                        <a href="{{ route('admin.permohonan.index') }}"
                           class="d-flex align-items-center justify-content-between p-2.5 rounded-3 text-decoration-none hover-lift"
                           style="background-color:#f8fafc; border:1px solid #e2e8f0;">
                            <div class="d-flex align-items-center gap-2.5">
                                <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width:34px;height:34px;background-color:#f0f9ff;color:#0284c7;border:1px solid #bae6fd;">
                                    <i class="fa-solid fa-folder-open" style="font-size:0.95rem;"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size:0.82rem;">Direktori Semua Borang</div>
                                    <div style="font-size:0.70rem;color:#64748b;">Senarai penuh rekod pendaftaran</div>
                                </div>
                            </div>
                            <i class="fa-solid fa-chevron-right text-secondary opacity-50" style="font-size:0.70rem;"></i>
                        </a>

                        <a href="{{ route('2fa.setup') }}"
                           class="d-flex align-items-center justify-content-between p-2.5 rounded-3 text-decoration-none hover-lift"
                           style="background-color:#f8fafc; border:1px solid #e2e8f0;">
                            <div class="d-flex align-items-center gap-2.5">
                                <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width:34px;height:34px;background-color:#fffbeb;color:#d97706;border:1px solid #fde68a;">
                                    <i class="fa-solid fa-shield-halved" style="font-size:0.95rem;"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size:0.82rem;">Keselamatan Akaun (2FA)</div>
                                    <div style="font-size:0.70rem;color:#64748b;">Perlindungan dua faktor akaun JAWI</div>
                                </div>
                            </div>
                            <i class="fa-solid fa-chevron-right text-secondary opacity-50" style="font-size:0.70rem;"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof window.Chart === 'undefined') {
        console.warn('Chart.js tidak dijumpai.');
        return;
    }

    /* ── 1. Carta Trend Bulanan (Line/Smooth Area) ── */
    const ctxTrend = document.getElementById('chartTrendBulananJawi');
    if (ctxTrend) {
        const monthlyValues = @json($monthlyApplications ?? array_fill(0, 12, 0));
        const monthNames = ['Jan', 'Feb', 'Mac', 'Apr', 'Mei', 'Jun', 'Jul', 'Ogo', 'Sep', 'Okt', 'Nov', 'Dis'];

        const gradient = ctxTrend.getContext('2d').createLinearGradient(0, 0, 0, 240);
        gradient.addColorStop(0, 'rgba(30, 64, 175, 0.28)');
        gradient.addColorStop(1, 'rgba(30, 64, 175, 0.01)');

        new window.Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: monthNames,
                datasets: [{
                    label: 'Bilangan Permohonan',
                    data: monthlyValues,
                    borderColor: '#1e40af',
                    backgroundColor: gradient,
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#1e40af',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { size: 12, family: "'Segoe UI', sans-serif" },
                        bodyFont: { size: 11, family: "'Segoe UI', sans-serif" },
                        padding: 10,
                        cornerRadius: 6,
                        callbacks: {
                            label: function (context) {
                                return ' Permohonan: ' + context.parsed.y + ' rekod';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            font: { size: 10, family: "'Segoe UI', sans-serif" },
                            color: '#64748b'
                        },
                        grid: { color: '#f1f5f9' }
                    },
                    x: {
                        ticks: {
                            font: { size: 10.5, family: "'Segoe UI', sans-serif" },
                            color: '#475569'
                        },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    /* ── 2. Carta Status Permohonan (Donut) ── */
    const ctxPermohonan = document.getElementById('chartStatusPermohonan');
    if (ctxPermohonan) {
        new window.Chart(ctxPermohonan, {
            type: 'doughnut',
            data: {
                labels: ['Dalam Semakan', 'Disahkan / Lulus', 'Ditolak'],
                datasets: [{
                    data: [
                        {{ (int)($permohonanPending ?? 0) }},
                        {{ (int)($permohonanLulus ?? 0) }},
                        {{ (int)($permohonanTolak ?? 0) }}
                    ],
                    backgroundColor: ['#f59e0b', '#10b981', '#ef4444'],
                    hoverBackgroundColor: ['#d97706', '#059669', '#dc2626'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 10,
                            padding: 10,
                            font: { size: 10.5, family: "'Segoe UI', sans-serif" }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                const val = ctx.raw || 0;
                                const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                const pct = total > 0 ? Math.round((val / total) * 100) : 0;
                                return ` ${ctx.label}: ${val} (${pct}%)`;
                            }
                        }
                    }
                },
                cutout: '68%'
            }
        });
    }

    /* ── 3. Carta Status Semakan Minit Mesyuarat (Bar) ── */
    const ctxMinit = document.getElementById('chartStatusMinit');
    if (ctxMinit) {
        new window.Chart(ctxMinit, {
            type: 'bar',
            data: {
                labels: ['Perlu Semakan', 'Disahkan', 'Ditolak'],
                datasets: [{
                    data: [
                        {{ (int)($minitPending ?? 0) }},
                        {{ (int)($minitDisahkan ?? 0) }},
                        {{ (int)($minitDitolak ?? 0) }}
                    ],
                    backgroundColor: ['#f59e0b', '#10b981', '#ef4444'],
                    borderRadius: 6,
                    maxBarThickness: 36
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                return ' Bilangan fail: ' + ctx.parsed.y;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            font: { size: 10, family: "'Segoe UI', sans-serif" },
                            color: '#64748b'
                        },
                        grid: { color: '#f1f5f9' }
                    },
                    x: {
                        ticks: {
                            font: { size: 10.5, family: "'Segoe UI', sans-serif" },
                            color: '#475569'
                        },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    /* ── 4. Carta Pecahan Kategori Surau (Polar/Donut) ── */
    const ctxKategori = document.getElementById('chartKategoriSurauJawi');
    if (ctxKategori) {
        const kategoriLabels = [];
        const kategoriValues = [];
        @foreach($kategoriSurauCounts ?? [] as $kat => $val)
            kategoriLabels.push('{{ addslashes($kat) }}');
            kategoriValues.push({{ (int)$val }});
        @endforeach

        new window.Chart(ctxKategori, {
            type: 'doughnut',
            data: {
                labels: kategoriLabels,
                datasets: [{
                    data: kategoriValues,
                    backgroundColor: [
                        '#1e40af', '#0284c7', '#0ea5e9', '#0d9488', '#6366f1', '#f59e0b', '#64748b'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 10,
                            padding: 8,
                            font: { size: 10, family: "'Segoe UI', sans-serif" }
                        }
                    }
                },
                cutout: '58%'
            }
        });
    }

    /* ── 5. Carta Taraf Tapak Tanah Surau (Horizontal Bar) ── */
    const ctxTaraf = document.getElementById('chartTarafTapakJawi');
    if (ctxTaraf) {
        const tarafLabels = [];
        const tarafValues = [];
        @foreach($tarafTapakCounts ?? [] as $tar => $val)
            tarafLabels.push('{{ addslashes($tar) }}');
            tarafValues.push({{ (int)$val }});
        @endforeach

        new window.Chart(ctxTaraf, {
            type: 'bar',
            data: {
                labels: tarafLabels,
                datasets: [{
                    axis: 'y',
                    data: tarafValues,
                    backgroundColor: ['#0284c7', '#0ea5e9', '#38bdf8', '#7dd3fc', '#bae6fd'],
                    borderRadius: 4,
                    maxBarThickness: 24
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                return ' Jumlah: ' + ctx.parsed.x + ' surau';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            font: { size: 10, family: "'Segoe UI', sans-serif" },
                            color: '#64748b'
                        },
                        grid: { color: '#f1f5f9' }
                    },
                    y: {
                        ticks: {
                            font: { size: 10.5, family: "'Segoe UI', sans-serif" },
                            color: '#334155'
                        },
                        grid: { display: false }
                    }
                }
            }
        });
    }
});
</script>
@endpush