@extends('layouts.app')

@section('content')

{{-- ── 1. Hero Welcome Banner Pentadbir Sistem ── --}}
<div class="hero-welcome-card p-4 mb-4 shadow-sm">
    <div class="hero-shape-1"></div>
    <div class="hero-shape-2"></div>
    <div class="row align-items-center position-relative">
        <div class="col-lg-8">
            <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                <span class="badge bg-white bg-opacity-25 px-2.5 py-1 text-white fw-semibold" style="font-size:0.72rem; letter-spacing:0.04em;">
                    <i class="fa-solid fa-shield-halved me-1"></i> PANEL PENTADBIR SISTEM
                </span>
                <span class="badge bg-white text-primary px-2.5 py-1 fw-bold" style="font-size:0.72rem;">
                    <i class="fa-solid fa-calendar-day me-1"></i> {{ now()->translatedFormat('l, d F Y') }}
                </span>
                <span class="badge bg-success bg-opacity-25 text-white border border-success border-opacity-50 px-2.5 py-1" style="font-size:0.72rem;">
                    <i class="fa-solid fa-circle-check me-1"></i> Sistem Beroperasi
                </span>
            </div>
            <h4 class="fw-bold text-white mb-1">Selamat Datang, {{ Auth::user()->name }}!</h4>
            <p class="mb-0 text-white-50" style="font-size: 0.85rem; max-width: 620px;">
                Pengurusan pentadbiran pengguna, pemantauan integriti pangkalan data &amp; jejak audit keselamatan sistem e-Surau JAWI.
            </p>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <div class="d-inline-flex flex-column gap-2 align-items-lg-end">
                <a href="{{ route('admin.users.index') }}" class="btn btn-light fw-bold px-3 py-2 shadow-sm rounded-2 text-primary hover-lift" style="font-size:0.83rem;">
                    <i class="fa-solid fa-users-gear me-1.5"></i> Pengurusan Pengguna ({{ $totalUsers ?? 0 }})
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ── 2. KPI Statistik Operasi Sistem ── --}}
<div class="d-flex align-items-center justify-content-between mb-2">
    <div class="section-title mb-0">
        <i class="fa-solid fa-chart-line me-1"></i> Statistik Operasi Sistem
    </div>
    <span class="badge bg-white text-success border border-success-subtle shadow-sm small font-monospace px-2 py-1">
        <i class="fa-solid fa-circle text-success me-1" style="font-size:0.5rem;"></i>Status Sihat
    </span>
</div>
<div class="row g-3 mb-4">
    {{-- Jumlah Pengguna --}}
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-3 kpi-card hover-lift h-100" style="border-left:4px solid #1e40af !important;">
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <div class="section-title mb-1">Jumlah Pengguna</div>
                        <div class="fw-bold font-monospace" style="font-size:1.9rem; color:#0f172a; line-height:1.1;">
                            {{ $totalUsers ?? 0 }}
                        </div>
                        <div style="font-size:0.75rem; color:#16a34a; font-weight:600; margin-top:4px;">
                            <i class="fa-solid fa-user-check me-1"></i>{{ $activeUsers ?? 0 }} pengguna aktif
                        </div>
                    </div>
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm"
                         style="width:44px;height:44px;background-color:#eff6ff;border:1px solid #dbeafe;">
                        <i class="fa-solid fa-users" style="color:#1e40af;font-size:1.1rem;"></i>
                    </div>
                </div>
                @php
                    $uPct = ($totalUsers > 0) ? round(($activeUsers / $totalUsers) * 100) : 0;
                @endphp
                <div class="kpi-progress mt-2">
                    <div class="kpi-progress-bar bg-primary" style="width: {{ $uPct }}%;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Permohonan Surau --}}
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-3 kpi-card hover-lift h-100" style="border-left:4px solid #0284c7 !important;">
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <div class="section-title mb-1">Permohonan Surau</div>
                        <div class="fw-bold font-monospace" style="font-size:1.9rem; color:#0f172a; line-height:1.1;">
                            {{ $totalApplications ?? 0 }}
                        </div>
                        <div style="font-size:0.75rem; color:#0284c7; font-weight:600; margin-top:4px;">
                            <i class="fa-solid fa-folder-open me-1"></i>Keseluruhan rekod
                        </div>
                    </div>
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm"
                         style="width:44px;height:44px;background-color:#f0f9ff;border:1px solid #bae6fd;">
                        <i class="fa-solid fa-file-invoice" style="color:#0284c7;font-size:1.1rem;"></i>
                    </div>
                </div>
                <div class="kpi-progress mt-2">
                    <div class="kpi-progress-bar" style="width: 100%; background-color:#0284c7;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Masjid / Kariah --}}
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-3 kpi-card hover-lift h-100" style="border-left:4px solid #0ea5e9 !important;">
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <div class="section-title mb-1">Masjid / Kariah</div>
                        <div class="fw-bold font-monospace" style="font-size:1.9rem; color:#0f172a; line-height:1.1;">
                            {{ $totalMasjids ?? 0 }}
                        </div>
                        <div style="font-size:0.75rem; color:#0ea5e9; font-weight:600; margin-top:4px;">
                            <i class="fa-solid fa-mosque me-1"></i>Aktif dalam sistem
                        </div>
                    </div>
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm"
                         style="width:44px;height:44px;background-color:#f0f9ff;border:1px solid #bae6fd;">
                        <i class="fa-solid fa-mosque" style="color:#0ea5e9;font-size:1.1rem;"></i>
                    </div>
                </div>
                <div class="kpi-progress mt-2">
                    <div class="kpi-progress-bar" style="width: 100%; background-color:#0ea5e9;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Log Audit Keselamatan --}}
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-3 kpi-card hover-lift h-100" style="border-left:4px solid #d97706 !important;">
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <div class="section-title mb-1">Log Audit Keselamatan</div>
                        <div class="fw-bold font-monospace" style="font-size:1.9rem; color:#0f172a; line-height:1.1;">
                            {{ $totalAuditLogs ?? 0 }}
                        </div>
                        <div style="font-size:0.75rem; color:#d97706; font-weight:600; margin-top:4px;">
                            <i class="fa-solid fa-shield-halved me-1"></i>Rekod jejak aktiviti
                        </div>
                    </div>
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm"
                         style="width:44px;height:44px;background-color:#fffbeb;border:1px solid #fef3c7;">
                        <i class="fa-solid fa-clock-rotate-left" style="color:#d97706;font-size:1.1rem;"></i>
                    </div>
                </div>
                <div class="kpi-progress mt-2">
                    <div class="kpi-progress-bar" style="width: 100%; background-color:#d97706;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── 3. Roles Breakdown & Komposisi Pengguna ── --}}
<div class="row g-3 mb-4">
    {{-- Senarai Peranan Pengguna --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between" style="border-bottom: 1px solid #e2e8f0;">
                <div class="fw-bold d-flex align-items-center gap-2" style="font-size:0.875rem; color:#0f172a;">
                    <i class="fa-solid fa-id-card-clip" style="color:#1e40af;"></i>
                    <span>Ringkasan Peranan Pengguna</span>
                </div>
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-light border fw-semibold text-primary px-2.5 py-1" style="font-size:0.75rem;">
                    <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Urus Pengguna
                </a>
            </div>
            <div class="card-body p-3">
                <div class="row g-2.5">
                    @forelse($rolesWithCount ?? [] as $roleItem)
                        @php
                            [$roleColor, $roleBg, $roleBorder, $roleIcon] = match($roleItem->slug) {
                                'admin'  => ['#1e40af', '#eff6ff', '#bfdbfe', 'fa-user-shield'],
                                'jawi'   => ['#0284c7', '#f0f9ff', '#bae6fd', 'fa-building-columns'],
                                'masjid' => ['#0ea5e9', '#f0f9ff', '#bae6fd', 'fa-mosque'],
                                'surau'  => ['#d97706', '#fffbeb', '#fde68a', 'fa-kaaba'],
                                default  => ['#64748b', '#f8fafc', '#e2e8f0', 'fa-user'],
                            };
                            $rolePct = ($totalUsers > 0) ? round(($roleItem->users_count / $totalUsers) * 100) : 0;
                        @endphp
                        <div class="col-md-6 col-xl-4">
                            <div class="card border-0 shadow-none rounded-3 p-3 h-100 hover-lift" style="background-color: #f8fafc; border: 1px solid #e2e8f0 !important; border-left: 3px solid {{ $roleColor }} !important;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                                         style="width:36px;height:36px;background-color:{{ $roleBg }};border:1px solid {{ $roleBorder }};">
                                        <i class="fa-solid {{ $roleIcon }}" style="color:{{ $roleColor }};font-size:0.95rem;"></i>
                                    </div>
                                    <span class="badge rounded-pill fw-bold" style="background-color: {{ $roleBg }}; color: {{ $roleColor }}; font-size: 0.68rem; border: 1px solid {{ $roleBorder }};">
                                        {{ $rolePct }}%
                                    </span>
                                </div>
                                <div class="fw-bold font-monospace" style="font-size:1.4rem;color:#0f172a;line-height:1.1;">
                                    {{ $roleItem->users_count }}
                                </div>
                                <div class="text-truncate fw-semibold mt-1" style="font-size:0.78rem;color:#475569;" title="{{ $roleItem->name }}">
                                    {{ $roleItem->name }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-4 text-muted small">
                            Tiada maklumat peranan ditemui.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Carta Agihan Peranan --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between" style="border-bottom: 1px solid #e2e8f0;">
                <div class="fw-bold d-flex align-items-center gap-2" style="font-size:0.875rem; color:#0f172a;">
                    <i class="fa-solid fa-chart-pie" style="color:#0284c7;"></i>
                    <span>Komposisi Pengguna</span>
                </div>
                <span class="badge bg-light text-secondary border font-monospace" style="font-size:0.68rem;">Carta Donut</span>
            </div>
            <div class="card-body p-3 d-flex flex-column justify-content-center align-items-center" style="min-height: 240px;">
                <div style="width: 100%; max-width: 250px; height: 190px;">
                    <canvas id="chartRolesAdmin"></canvas>
                </div>
                <div class="text-center text-muted small mt-2">
                    <span class="fw-bold text-dark font-monospace">{{ $totalUsers ?? 0 }}</span> Jumlah Keseluruhan Akaun
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── 4. Ringkasan Pantas Keselamatan & Kawalan Sistem ── --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 p-3 h-100" style="background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:42px;height:42px;background-color:#eff6ff;color:#1e40af;border:1px solid #bfdbfe;">
                    <i class="fa-solid fa-server" style="font-size:1.1rem;"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="text-muted small fw-semibold">Pangkalan Data & Sistem</div>
                    <div class="fw-bold text-dark" style="font-size:0.88rem;">Dalam Talian & Stabil</div>
                </div>
                <i class="fa-solid fa-circle-check text-success fs-5"></i>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 p-3 h-100" style="background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:42px;height:42px;background-color:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;">
                    <i class="fa-solid fa-shield-halved" style="font-size:1.1rem;"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="text-muted small fw-semibold">Audit Trail & Keselamatan</div>
                    <div class="fw-bold text-dark" style="font-size:0.88rem;">{{ $totalAuditLogs ?? 0 }} Rekod Aktif</div>
                </div>
                <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size:0.75rem;" title="Lihat Audit Log">
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 p-3 h-100" style="background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:42px;height:42px;background-color:#fffbeb;color:#d97706;border:1px solid #fde68a;">
                    <i class="fa-solid fa-key" style="font-size:1.1rem;"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="text-muted small fw-semibold">Pengesahan 2-Faktor (2FA)</div>
                    <div class="fw-bold text-dark" style="font-size:0.88rem;">
                        {{ auth()->user()->google2fa_enabled ? 'Akaun Anda Dilindungi' : 'Disarankan Diaktifkan' }}
                    </div>
                </div>
                <a href="{{ route('2fa.setup') }}" class="btn btn-sm btn-outline-warning text-dark py-1 px-2" style="font-size:0.75rem;" title="Tetapan 2FA">
                    <i class="fa-solid fa-gear"></i>
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ── Bahagian yang dikomen asal dikekalkan seperti permintaan ── --}}

{{-- ── 4. Main 2-Col: Permohonan + Log ── --}}
<!-- <div class="row g-3 mb-4"> -->
    {{-- Permohonan Terkini --}}
    <!-- <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between" style="border-bottom:2px solid #eff6ff;">
                <div class="fw-bold" style="font-size:0.875rem; color:#0f172a;">
                    <i class="fa-solid fa-list-check me-2" style="color:#1e40af;"></i>Permohonan Surau Terkini
                </div>
                <a href="{{ route('admin.permohonan.index') }}"
                   class="btn btn-sm fw-semibold hover-lift"
                   style="background-color:#eff6ff; color:#1e40af; border:1px solid #bfdbfe; font-size:0.78rem; border-radius:6px;">
                    Lihat Semua <i class="fa-solid fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead>
                            <tr>
                                <th class="ps-3">No. Rujukan</th>
                                <th>Nama Surau</th>
                                <th>Kariah</th>
                                <th class="text-center">Status</th>
                                <th class="text-center pe-3">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentApplications ?? [] as $app)
                                @php
                                    $initial = strtoupper(substr($app->nama_surau ?? 'S', 0, 1));
                                @endphp
                                <tr>
                                    <td class="ps-3 font-monospace fw-bold" style="color:#1e40af;">
                                        {{ $app->reference_no }}
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-initial" style="width:28px;height:28px;font-size:0.72rem;">{{ $initial }}</div>
                                            <span class="fw-semibold text-dark">{{ $app->nama_surau }}</span>
                                        </div>
                                    </td>
                                    <td style="color:#64748b;">
                                        {{ $app->masjid?->nama_masjid ?? $app->kariah_masjid ?? '-' }}
                                    </td>
                                    <td class="text-center">
                                        @if($app->status === 'APPROVED')
                                            <span class="badge-status" style="background-color:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0;">
                                                <i class="fa-solid fa-check me-1"></i>Lulus
                                            </span>
                                        @elseif($app->status === 'REJECTED')
                                            <span class="badge-status" style="background-color:#fef2f2; color:#dc2626; border:1px solid #fecaca;">
                                                <i class="fa-solid fa-xmark me-1"></i>Ditolak
                                            </span>
                                        @else
                                            <span class="badge-status" style="background-color:#fffbeb; color:#92400e; border:1px solid #fde68a;">
                                                <i class="fa-solid fa-clock me-1"></i>Dalam Proses
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center pe-3">
                                        <a href="{{ route('admin.permohonan.show', $app->id) }}"
                                           class="btn btn-sm fw-semibold hover-lift"
                                           style="background-color:#1e40af; color:#fff; border:none; border-radius:6px; font-size:0.78rem; padding:3px 10px;">
                                            <i class="fa-solid fa-eye me-1"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5" style="color:#94a3b8;">
                                        <i class="fa-solid fa-folder-open fa-2x mb-2 d-block"></i>
                                        Tiada rekod permohonan terkini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div> -->
    {{-- Audit Log Terkini --}}
    <!-- <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between" style="border-bottom:2px solid #eff6ff;">
                <div class="fw-bold" style="font-size:0.875rem; color:#0f172a;">
                    <i class="fa-solid fa-shield-halved me-2" style="color:#d97706;"></i>Jejak Audit Terkini
                </div>
                <a href="{{ route('admin.audit-logs.index') }}"
                   class="btn btn-sm fw-semibold hover-lift"
                   style="background-color:#fffbeb; color:#d97706; border:1px solid #fde68a; font-size:0.78rem; border-radius:6px;">
                    Log Penuh <i class="fa-solid fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-3" style="overflow-y:auto;max-height:340px;">
                @forelse($recentAuditLogs ?? [] as $log)
                    <div class="d-flex align-items-start gap-3 mb-3 pb-3" style="border-bottom:1px solid #f1f5f9;">
                        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm"
                             style="width:34px;height:34px;background-color:#fffbeb;border:1px solid #fef3c7;">
                            <i class="fa-solid fa-shield-halved" style="color:#d97706;font-size:0.8rem;"></i>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-semibold text-truncate" style="font-size:0.82rem;color:#0f172a;max-width:140px;">
                                    {{ $log->causer?->name ?? 'Sistem' }}
                                </span>
                                <span class="font-monospace flex-shrink-0" style="font-size:0.70rem;color:#94a3b8;">
                                    {{ $log->created_at ? $log->created_at->diffForHumans() : '' }}
                                </span>
                            </div>
                            <p class="mb-0 mt-1 text-truncate" style="font-size:0.78rem;color:#64748b;">
                                {{ $log->description }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4" style="color:#94a3b8;">
                        <i class="fa-solid fa-circle-check fa-2x mb-2 d-block" style="color:#22c55e;"></i>
                        <div style="font-size:0.82rem;">Tiada log aktiviti terkini.</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div> -->
<!-- </div> -->

{{-- ── 5. Capaian Pantas ── --}}
<!-- <div class="card border-0 shadow-sm rounded-3" style="border-left:4px solid #1e40af !important;">
    <div class="card-body p-4">
        <div class="section-title mb-3">
            <i class="fa-solid fa-bolt me-1"></i> Capaian Pantas Pentadbir Sistem
        </div>
        <div class="row g-2">
            <div class="col-md-3">
                <a href="{{ route('admin.users.index') }}"
                   class="d-flex align-items-center justify-content-between gap-2 px-3 py-3 rounded-3 text-decoration-none h-100 hover-lift"
                   style="background-color:#f8fafc; border:1px solid #e2e8f0;">
                    <div>
                        <i class="fa-solid fa-users mb-1" style="color:#1e40af; font-size:1.1rem;"></i>
                        <div class="fw-semibold text-dark" style="font-size:0.83rem;">Pengurusan Pengguna</div>
                        <div style="font-size:0.72rem;color:#94a3b8;">Tambah &amp; urus akaun</div>
                    </div>
                    <i class="fa-solid fa-chevron-right" style="color:#cbd5e1;font-size:0.75rem;"></i>
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('admin.permohonan.index') }}"
                   class="d-flex align-items-center justify-content-between gap-2 px-3 py-3 rounded-3 text-decoration-none h-100 hover-lift"
                   style="background-color:#f8fafc; border:1px solid #e2e8f0;">
                    <div>
                        <i class="fa-solid fa-folder-open mb-1" style="color:#0284c7; font-size:1.1rem;"></i>
                        <div class="fw-semibold text-dark" style="font-size:0.83rem;">Senarai Permohonan</div>
                        <div style="font-size:0.72rem;color:#94a3b8;">Semak &amp; luluskan</div>
                    </div>
                    <i class="fa-solid fa-chevron-right" style="color:#cbd5e1;font-size:0.75rem;"></i>
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('admin.audit-logs.index') }}"
                   class="d-flex align-items-center justify-content-between gap-2 px-3 py-3 rounded-3 text-decoration-none h-100 hover-lift"
                   style="background-color:#f8fafc; border:1px solid #e2e8f0;">
                    <div>
                        <i class="fa-solid fa-shield-cat mb-1" style="color:#d97706; font-size:1.1rem;"></i>
                        <div class="fw-semibold text-dark" style="font-size:0.83rem;">Audit Log</div>
                        <div style="font-size:0.72rem;color:#94a3b8;">Rekod keselamatan</div>
                    </div>
                    <i class="fa-solid fa-chevron-right" style="color:#cbd5e1;font-size:0.75rem;"></i>
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('2fa.setup') }}"
                   class="d-flex align-items-center justify-content-between gap-2 px-3 py-3 rounded-3 text-decoration-none h-100 hover-lift"
                   style="background-color:#f8fafc; border:1px solid #e2e8f0;">
                    <div>
                        <i class="fa-solid fa-key mb-1" style="color:#64748b; font-size:1.1rem;"></i>
                        <div class="fw-semibold text-dark" style="font-size:0.83rem;">Tetapan 2FA</div>
                        <div style="font-size:0.72rem;color:#94a3b8;">Keselamatan akaun</div>
                    </div>
                    <i class="fa-solid fa-chevron-right" style="color:#cbd5e1;font-size:0.75rem;"></i>
                </a>
            </div>
        </div>
    </div>
</div> -->
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof window.Chart !== 'undefined') {
        const ctxRoles = document.getElementById('chartRolesAdmin');
        if (ctxRoles) {
            const roleLabels = [];
            const roleCounts = [];
            @foreach($rolesWithCount ?? [] as $role)
                roleLabels.push('{{ addslashes($role->name) }}');
                roleCounts.push({{ (int)$role->users_count }});
            @endforeach

            new window.Chart(ctxRoles, {
                type: 'doughnut',
                data: {
                    labels: roleLabels,
                    datasets: [{
                        data: roleCounts,
                        backgroundColor: ['#1e40af', '#0284c7', '#0ea5e9', '#3b82f6', '#d97706', '#64748b'],
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
                                font: { size: 11, family: "'Segoe UI', sans-serif" }
                            }
                        }
                    },
                    cutout: '65%'
                }
            });
        }
    }
});
</script>
@endpush
