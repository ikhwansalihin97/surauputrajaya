@extends('layouts.app')

@section('content')

{{-- ── 1. Hero Welcome Banner Pentadbir Masjid ── --}}
<div class="hero-welcome-card p-4 mb-4 shadow-sm">
    <div class="hero-shape-1"></div>
    <div class="hero-shape-2"></div>
    <div class="row align-items-center position-relative">
        <div class="col-lg-8">
            <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                <span class="badge bg-white bg-opacity-25 px-2.5 py-1 text-white fw-semibold" style="font-size:0.72rem; letter-spacing:0.04em;">
                    <i class="fa-solid fa-mosque me-1"></i> PORTAL PENTADBIR MASJID
                </span>
                <span class="badge bg-white text-primary px-2.5 py-1 fw-bold" style="font-size:0.72rem;">
                    <i class="fa-solid fa-calendar-day me-1"></i> {{ now()->translatedFormat('l, d F Y') }}
                </span>
                <span class="badge bg-success bg-opacity-25 text-white border border-success border-opacity-50 px-2.5 py-1" style="font-size:0.72rem;">
                    <i class="fa-solid fa-circle-check me-1"></i> Sesi Kariah Aktif
                </span>
            </div>
            <h4 class="fw-bold text-white mb-1">Selamat Datang, {{ Auth::user()->name }}!</h4>
            <p class="mb-0 text-white-50" style="font-size: 0.85rem; max-width: 620px;">
                Memantau, menyemak &amp; mengesahkan permohonan pendaftaran surau di bawah seliaan 
                <strong class="text-white">{{ $jumlahMasjid }} kariah masjid</strong> yang diuruskan oleh anda.
            </p>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <a href="{{ route('admin.masjid.pengesahan.index') }}" class="btn btn-light fw-bold px-3 py-2 shadow-sm rounded-2 text-primary hover-lift" style="font-size:0.83rem;">
                <i class="fa-solid fa-file-signature me-1.5"></i> Semak Tindakan Kariah ({{ $menungguPengesahan ?? 0 }})
            </a>
        </div>
    </div>
</div>

@if($masjids->isEmpty())
    {{-- Amaran Tiada Masjid --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4" style="border-left:4px solid #d97706 !important;">
        <div class="card-body p-4 d-flex align-items-center gap-3">
            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm"
                 style="width:48px;height:48px;background-color:#fffbeb;border:1px solid #fde68a;">
                <i class="fa-solid fa-triangle-exclamation" style="color:#d97706;font-size:1.2rem;"></i>
            </div>
            <div>
                <div class="fw-bold text-dark mb-1">Akaun Belum Dipautkan Kepada Kariah Masjid</div>
                <div style="font-size:0.84rem;color:#64748b;">
                    Akaun anda belum mempunyai sebarang Kariah Masjid yang aktif. Sila hubungi Pentadbir Sistem untuk menetapkan akses masjid anda.
                </div>
            </div>
        </div>
    </div>
@else

    {{-- ── 2. Kariah Masjid Di Bawah Jagaan ── --}}
    <div class="mb-4">
        <div class="section-title mb-2">
            <i class="fa-solid fa-mosque me-1"></i> Kariah Masjid Seliaan Anda ({{ $jumlahMasjid }})
        </div>
        <div class="row g-3">
            @foreach($masjids as $masjid)
                <div class="col-xl-4 col-md-6">
                    <div class="card border-0 shadow-sm rounded-3 h-100 hover-lift" style="border-left:4px solid #1e40af !important;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between gap-2">
                                <div class="d-flex align-items-center gap-3 overflow-hidden">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm"
                                         style="width:42px;height:42px;background-color:#eff6ff;border:1px solid #dbeafe;">
                                        <i class="fa-solid fa-mosque" style="color:#1e40af;font-size:1.1rem;"></i>
                                    </div>
                                    <div class="overflow-hidden">
                                        <div style="font-size:0.68rem;font-weight:700;letter-spacing:0.07em;text-transform:uppercase;color:#94a3b8;">
                                            Kariah Masjid
                                        </div>
                                        <div class="fw-semibold text-dark text-truncate" style="font-size:0.875rem;" title="{{ $masjid->nama_masjid }}">
                                            {{ $masjid->nama_masjid }}
                                        </div>
                                        @if($masjid->alamat)
                                            <div class="text-muted text-truncate small" style="font-size:0.72rem;">
                                                <i class="fa-solid fa-location-dot me-1 text-secondary opacity-50"></i>{{ $masjid->alamat }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25" style="font-size:0.7rem;">
                                    <i class="fa-solid fa-check me-1"></i>Aktif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ── 3. KPI Statistik Pengesahan Kariah ── --}}
    <div class="d-flex align-items-center justify-content-between mb-2">
        <div class="section-title mb-0">
            <i class="fa-solid fa-chart-line me-1"></i> Ringkasan Pengesahan Permohonan Kariah
        </div>
        <span class="badge bg-white text-primary border shadow-sm small font-monospace px-2 py-1">
            Data Terkini
        </span>
    </div>
    <div class="row g-3 mb-4">
        {{-- Jumlah Keseluruhan --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 kpi-card hover-lift h-100" style="border-left:4px solid #1e40af !important;">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="section-title mb-1">Jumlah Permohonan</div>
                            <div class="fw-bold font-monospace" style="font-size:1.85rem;color:#0f172a;line-height:1.1;">
                                {{ $jumlahPermohonan }}
                            </div>
                            <div style="font-size:0.75rem;color:#64748b;margin-top:4px;">
                                <i class="fa-solid fa-layer-group me-1 text-primary"></i>Semua kariah seliaan
                            </div>
                        </div>
                        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm"
                             style="width:44px;height:44px;background-color:#eff6ff;border:1px solid #dbeafe;">
                            <i class="fa-solid fa-file-invoice" style="color:#1e40af;font-size:1.1rem;"></i>
                        </div>
                    </div>
                    <div class="kpi-progress mt-2">
                        <div class="kpi-progress-bar bg-primary" style="width:100%;"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Menunggu Tindakan --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 kpi-card hover-lift h-100" style="border-left:4px solid #d97706 !important;">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="section-title mb-1">Menunggu Pengesahan</div>
                            <div class="fw-bold font-monospace" style="font-size:1.85rem;color:#0f172a;line-height:1.1;">
                                {{ $menungguPengesahan }}
                            </div>
                            <div style="font-size:0.75rem;color:#d97706;font-weight:600;margin-top:4px;">
                                <i class="fa-solid fa-clock me-1"></i>Perlu tindakan segera
                            </div>
                        </div>
                        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm"
                             style="width:44px;height:44px;background-color:#fffbeb;border:1px solid #fef3c7;">
                            <i class="fa-solid fa-clock" style="color:#d97706;font-size:1.1rem;"></i>
                        </div>
                    </div>
                    @php
                        $mPct = ($jumlahPermohonan > 0) ? round(($menungguPengesahan / $jumlahPermohonan) * 100) : 0;
                    @endphp
                    <div class="kpi-progress mt-2">
                        <div class="kpi-progress-bar bg-warning" style="width:{{ $mPct }}%;"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Telah Disahkan --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 kpi-card hover-lift h-100" style="border-left:4px solid #16a34a !important;">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="section-title mb-1">Permohonan Disahkan</div>
                            <div class="fw-bold font-monospace" style="font-size:1.85rem;color:#0f172a;line-height:1.1;">
                                {{ $telahDisahkan }}
                            </div>
                            <div style="font-size:0.75rem;color:#16a34a;font-weight:600;margin-top:4px;">
                                <i class="fa-solid fa-circle-check me-1"></i>Telah disahkan masjid
                            </div>
                        </div>
                        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm"
                             style="width:44px;height:44px;background-color:#f0fdf4;border:1px solid #bbf7d0;">
                            <i class="fa-solid fa-circle-check" style="color:#16a34a;font-size:1.1rem;"></i>
                        </div>
                    </div>
                    @php
                        $sPct = ($jumlahPermohonan > 0) ? round(($telahDisahkan / $jumlahPermohonan) * 100) : 0;
                    @endphp
                    <div class="kpi-progress mt-2">
                        <div class="kpi-progress-bar bg-success" style="width:{{ $sPct }}%;"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ditolak --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 kpi-card hover-lift h-100" style="border-left:4px solid #dc2626 !important;">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="section-title mb-1">Permohonan Ditolak</div>
                            <div class="fw-bold font-monospace" style="font-size:1.85rem;color:#0f172a;line-height:1.1;">
                                {{ $ditolak }}
                            </div>
                            <div style="font-size:0.75rem;color:#dc2626;font-weight:600;margin-top:4px;">
                                <i class="fa-solid fa-circle-xmark me-1"></i>Ditolak peringkat kariah
                            </div>
                        </div>
                        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm"
                             style="width:44px;height:44px;background-color:#fef2f2;border:1px solid #fecaca;">
                            <i class="fa-solid fa-circle-xmark" style="color:#dc2626;font-size:1.1rem;"></i>
                        </div>
                    </div>
                    @php
                        $tPct = ($jumlahPermohonan > 0) ? round(($ditolak / $jumlahPermohonan) * 100) : 0;
                    @endphp
                    <div class="kpi-progress mt-2">
                        <div class="kpi-progress-bar bg-danger" style="width:{{ $tPct }}%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── 4. ANALISIS VISUAL (3 CARTA PROFESIONAL) ── --}}
    <div class="row g-3 mb-4">
        {{-- Carta 1: Donut Status Pengesahan --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between" style="border-bottom: 1px solid #e2e8f0;">
                    <div class="fw-bold d-flex align-items-center gap-2" style="font-size:0.83rem;color:#0f172a;">
                        <i class="fa-solid fa-chart-pie" style="color:#0284c7;"></i>
                        <span>Status Pengesahan</span>
                    </div>
                    <span class="badge bg-light text-secondary border font-monospace" style="font-size:0.68rem;">Carta Donut</span>
                </div>
                <div class="card-body p-3 d-flex flex-column justify-content-center align-items-center" style="min-height: 230px;">
                    <div style="width: 100%; max-width: 240px; height: 180px;">
                        <canvas id="chartStatusMasjid"></canvas>
                    </div>
                    <div class="text-center text-muted small mt-2" style="font-size:0.75rem;">
                        Kadar Pengesahan: <strong class="text-success font-monospace">{{ $jumlahPermohonan > 0 ? round(($telahDisahkan / $jumlahPermohonan) * 100) : 0 }}%</strong>
                    </div>
                </div>
            </div>
        </div>

        {{-- Carta 2: Pecahan Kategori Surau (Donut/Pie) --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between" style="border-bottom: 1px solid #e2e8f0;">
                    <div class="fw-bold d-flex align-items-center gap-2" style="font-size:0.83rem;color:#0f172a;">
                        <i class="fa-solid fa-kaaba" style="color:#1e40af;"></i>
                        <span>Kategori Surau Kariah</span>
                    </div>
                    <span class="badge bg-light text-secondary border font-monospace" style="font-size:0.68rem;">Pecahan Kategori</span>
                </div>
                <div class="card-body p-3 d-flex flex-column justify-content-center align-items-center" style="min-height: 230px;">
                    @if(($kategoriSurauCounts ?? collect())->isNotEmpty())
                        <div style="width: 100%; max-width: 240px; height: 180px;">
                            <canvas id="chartKategoriSurau"></canvas>
                        </div>
                        <div class="text-center text-muted small mt-2" style="font-size:0.75rem;">
                            Jumlah Kategori: <strong class="text-primary font-monospace">{{ $kategoriSurauCounts->count() }}</strong>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted small">
                            <i class="fa-solid fa-chart-pie fa-2x mb-2 d-block opacity-40"></i>
                            Tiada rekod kategori surau.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Carta 3: Trend Permohonan Bulanan (Bar Chart) --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between" style="border-bottom: 1px solid #e2e8f0;">
                    <div class="fw-bold d-flex align-items-center gap-2" style="font-size:0.83rem;color:#0f172a;">
                        <i class="fa-solid fa-chart-column" style="color:#0ea5e9;"></i>
                        <span>Trend Permohonan {{ now()->year }}</span>
                    </div>
                    <span class="badge bg-light text-secondary border font-monospace" style="font-size:0.68rem;">Carta Bar</span>
                </div>
                <div class="card-body p-3 d-flex flex-column justify-content-center" style="min-height: 230px;">
                    <div style="width: 100%; height: 180px;">
                        <canvas id="chartTrendMasjid"></canvas>
                    </div>
                    <div class="text-center text-muted small mt-2" style="font-size:0.75rem;">
                        Permohonan Tahunan: <strong class="text-dark font-monospace">{{ $jumlahPermohonan }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── 5. Jadual Permohonan Terkini (Dikekalkan Komen) ── --}}
    <!-- <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between" style="border-bottom:2px solid #eff6ff;">
            <div>
                <div class="fw-bold" style="font-size:0.875rem;color:#0f172a;">
                    <i class="fa-solid fa-list-check me-2 text-primary"></i>Permohonan Terkini — Perlu Pengesahan Kariah
                </div>
                <div style="font-size:0.75rem;color:#94a3b8;">
                    Permohonan pendaftaran surau daripada kariah di bawah jagaan anda.
                </div>
            </div>
            <a href="{{ route('admin.masjid.pengesahan.index') }}"
               class="btn btn-sm fw-semibold hover-lift"
               style="background-color:#eff6ff;color:#1e40af;border:1px solid #bfdbfe;font-size:0.78rem;border-radius:6px;">
                Lihat Semua <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="card-body p-0">
            @if($permohonanTerkini->count())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead>
                            <tr>
                                <th class="ps-3">Surau &amp; No. Rujukan</th>
                                <th>Kariah Masjid</th>
                                <th>Tarikh Hantar</th>
                                <th class="text-center">Status Pengesahan</th>
                                <th class="text-center pe-3">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($permohonanTerkini as $application)
                                @php
                                    $confirmation = $application->masjidConfirmation
                                        ?? $application->confirmations->where('peringkat', 'MASJID')->first();
                                     $statusStr = $confirmation?->status;
                                    $initial = strtoupper(substr($application->nama_surau ?? 'S', 0, 1));
                                @endphp
                                <tr>
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center gap-2.5">
                                            <div class="avatar-initial">{{ $initial }}</div>
                                            <div>
                                                <div class="fw-bold text-dark" style="font-size:0.85rem;">{{ $application->nama_surau }}</div>
                                                <span class="font-monospace text-primary fw-semibold" style="font-size:0.72rem;">{{ $application->reference_no }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="color:#475569;">
                                        <i class="fa-solid fa-mosque me-1 text-secondary opacity-50"></i>
                                        {{ $application->masjid?->nama_masjid ?? $application->kariah_masjid ?? '-' }}
                                    </td>
                                    <td class="font-monospace" style="color:#64748b;font-size:0.78rem;">
                                        <i class="fa-regular fa-clock me-1 opacity-50"></i>
                                        {{ $application->created_at ? $application->created_at->format('d/m/Y h:i A') : '-' }}
                                    </td>
                                    <td class="text-center">
                                        @if(in_array($statusStr, ['APPROVED', 'DISAHKAN']))
                                            <span class="badge-status" style="background-color:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;">
                                                <i class="fa-solid fa-check me-1"></i>Disahkan
                                            </span>
                                        @elseif(in_array($statusStr, ['REJECTED', 'DITOLAK']))
                                            <span class="badge-status" style="background-color:#fef2f2;color:#dc2626;border:1px solid #fecaca;">
                                                <i class="fa-solid fa-xmark me-1"></i>Ditolak
                                            </span>
                                        @else
                                            <span class="badge-status" style="background-color:#fffbeb;color:#92400e;border:1px solid #fde68a;">
                                                <i class="fa-solid fa-clock me-1"></i>Perlu Semakan
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center pe-3">
                                        <a href="{{ route('admin.masjid.pengesahan.show', $application->id) }}"
                                           class="btn btn-sm fw-semibold hover-lift"
                                           style="background-color:#1e40af;color:#fff;border:none;border-radius:6px;font-size:0.78rem;padding: 4px 12px;">
                                            <i class="fa-solid fa-eye me-1"></i> Semak
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5" style="color:#94a3b8;">
                    <i class="fa-solid fa-circle-check fa-2x mb-2 d-block" style="color:#22c55e;"></i>
                    <div class="fw-semibold mb-1">Tiada Permohonan Baharu</div>
                    <small>Tiada permohonan pendaftaran surau yang memerlukan pengesahan kariah pada masa ini.</small>
                </div>
            @endif
        </div>
    </div> -->

    {{-- ── 6. Tindakan Pantas (Dikekalkan Komen) ── --}}
    <!-- <div class="card border-0 shadow-sm rounded-3" style="border-left:4px solid #1e40af !important;">
        <div class="card-body p-4">
            <div class="section-title mb-3">
                <i class="fa-solid fa-bolt me-1"></i> Tindakan Pantas Pentadbir Masjid
            </div>
            <div class="row g-2">
                <div class="col-md-4">
                    <a href="{{ route('admin.masjid.pengesahan.index') }}"
                       class="d-flex align-items-center justify-content-between gap-2 px-3 py-3 rounded-3 text-decoration-none hover-lift h-100"
                       style="background-color:#f8fafc;border:1px solid #e2e8f0;">
                        <div>
                            <i class="fa-solid fa-file-circle-check mb-1" style="color:#1e40af;font-size:1.1rem;"></i>
                            <div class="fw-semibold text-dark" style="font-size:0.83rem;">Pengesahan Permohonan</div>
                            <div style="font-size:0.72rem;color:#94a3b8;">Semak &amp; sahkan kariah</div>
                        </div>
                        <i class="fa-solid fa-chevron-right" style="color:#cbd5e1;font-size:0.75rem;"></i>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('admin.permohonan.index') }}"
                       class="d-flex align-items-center justify-content-between gap-2 px-3 py-3 rounded-3 text-decoration-none hover-lift h-100"
                       style="background-color:#f8fafc;border:1px solid #e2e8f0;">
                        <div>
                            <i class="fa-solid fa-folder-open mb-1" style="color:#0284c7;font-size:1.1rem;"></i>
                            <div class="fw-semibold text-dark" style="font-size:0.83rem;">Senarai Borang Permohonan</div>
                            <div style="font-size:0.72rem;color:#94a3b8;">Lihat direktori borang</div>
                        </div>
                        <i class="fa-solid fa-chevron-right" style="color:#cbd5e1;font-size:0.75rem;"></i>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('2fa.setup') }}"
                       class="d-flex align-items-center justify-content-between gap-2 px-3 py-3 rounded-3 text-decoration-none hover-lift h-100"
                       style="background-color:#f8fafc;border:1px solid #e2e8f0;">
                        <div>
                            <i class="fa-solid fa-shield-halved mb-1" style="color:#64748b;font-size:1.1rem;"></i>
                            <div class="fw-semibold text-dark" style="font-size:0.83rem;">Tetapan Keselamatan 2FA</div>
                            <div style="font-size:0.72rem;color:#94a3b8;">Pengesahan dua faktor akaun</div>
                        </div>
                        <i class="fa-solid fa-chevron-right" style="color:#cbd5e1;font-size:0.75rem;"></i>
                    </a>
                </div>
            </div>
        </div>
    </div> -->

@endif

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof window.Chart === 'undefined') return;

    /* ── Carta 1: Status Pengesahan (Donut) ── */
    const ctxMasjid = document.getElementById('chartStatusMasjid');
    if (ctxMasjid) {
        new window.Chart(ctxMasjid, {
            type: 'doughnut',
            data: {
                labels: ['Menunggu', 'Disahkan', 'Ditolak'],
                datasets: [{
                    data: [{{ $menungguPengesahan ?? 0 }}, {{ $telahDisahkan ?? 0 }}, {{ $ditolak ?? 0 }}],
                    backgroundColor: ['#f59e0b', '#10b981', '#ef4444'],
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
                        labels: { boxWidth: 10, padding: 8, font: { size: 10, family: "'Segoe UI', sans-serif" } }
                    }
                },
                cutout: '65%'
            }
        });
    }

    /* ── Carta 2: Kategori Surau Kariah (Doughnut / Pie) ── */
    const ctxKategori = document.getElementById('chartKategoriSurau');
    if (ctxKategori) {
        const katLabels = [];
        const katCounts = [];
        @foreach($kategoriSurauCounts ?? [] as $kat => $total)
            katLabels.push('{{ addslashes($kat) }}');
            katCounts.push({{ (int)$total }});
        @endforeach

        new window.Chart(ctxKategori, {
            type: 'doughnut',
            data: {
                labels: katLabels,
                datasets: [{
                    data: katCounts,
                    backgroundColor: ['#1e40af', '#0284c7', '#0e7490', '#7c3aed', '#b45309', '#64748b'],
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
                        labels: { boxWidth: 10, padding: 8, font: { size: 10, family: "'Segoe UI', sans-serif" } }
                    }
                },
                cutout: '55%'
            }
        });
    }

    /* ── Carta 3: Trend Permohonan Bulanan (Bar) ── */
    const ctxTrend = document.getElementById('chartTrendMasjid');
    if (ctxTrend) {
        const monthNames = ['Jan','Feb','Mac','Apr','Mei','Jun','Jul','Ogos','Sep','Okt','Nov','Dis'];
        const monthlyData = new Array(12).fill(0);
        @foreach(range(1, 12) as $m)
            monthlyData[{{ $m - 1 }}] = {{ ($monthlyApplications ?? collect())->get($m, 0) }};
        @endforeach

        new window.Chart(ctxTrend, {
            type: 'bar',
            data: {
                labels: monthNames,
                datasets: [{
                    label: 'Permohonan',
                    data: monthlyData,
                    backgroundColor: monthlyData.map((v, i) => {
                        const curMonth = new Date().getMonth();
                        return i === curMonth ? 'rgba(14, 165, 233, 0.9)' : 'rgba(14, 165, 233, 0.25)';
                    }),
                    borderColor: 'rgba(14, 165, 233, 0.8)',
                    borderWidth: 1,
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) { return ' ' + ctx.parsed.y + ' permohonan'; }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 9 }, color: '#94a3b8' }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            stepSize: 1,
                            font: { size: 9 },
                            color: '#94a3b8',
                            callback: function(v) { return Number.isInteger(v) ? v : ''; }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
