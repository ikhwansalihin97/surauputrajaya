@extends('layouts.app')

@section('content')

{{-- ── 1. Hero Welcome Banner Pentadbir Zon ── --}}
<div class="hero-welcome-card p-4 mb-4 shadow-sm">
    <div class="hero-shape-1"></div>
    <div class="hero-shape-2"></div>
    <div class="row align-items-center position-relative">
        <div class="col-lg-8">
            <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                <span class="badge bg-white bg-opacity-25 px-2.5 py-1 text-white fw-semibold" style="font-size:0.72rem; letter-spacing:0.04em;">
                    <i class="fa-solid fa-map-location-dot me-1"></i> PENTADBIR PUSAT ZON
                </span>
                <span class="badge bg-white text-primary px-2.5 py-1 fw-bold" style="font-size:0.72rem;">
                    <i class="fa-solid fa-calendar-day me-1"></i> {{ now()->translatedFormat('l, d F Y') }}
                </span>
            </div>
            <h4 class="fw-bold text-white mb-1">Selamat Datang, {{ Auth::user()->name }}!</h4>
            <p class="mb-0 text-white-50" style="font-size: 0.85rem; max-width: 620px;">
                Memantau &amp; mengesahkan permohonan pendaftaran surau bagi zon di bawah seliaan anda sebelum kelulusan akhir dimuktamadkan.
            </p>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <a href="{{ route('admin.permohonan.index') }}" class="btn btn-light fw-bold px-3 py-2 shadow-sm rounded-2 text-primary hover-lift" style="font-size:0.83rem;">
                <i class="fa-solid fa-map-location-dot me-1"></i> Semak Tindakan Zon ({{ $menungguPengesahan ?? 0 }})
            </a>
        </div>
    </div>
</div>

{{-- ── 2. KPI Cards & Visual Chart Row ── --}}
<div class="row g-3 mb-4">
    {{-- Left 8 cols: KPI Cards --}}
    <div class="col-xl-8">
        <div class="section-title mb-2">
            <i class="fa-solid fa-chart-pie me-1"></i> Ringkasan Pengesahan Permohonan Zon
        </div>
        <div class="row g-3">
            {{-- Jumlah Permohonan --}}
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-3 kpi-card hover-lift h-100" style="border-left:4px solid #1e40af !important;">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <div class="section-title mb-1">Jumlah Permohonan</div>
                                <div class="fw-bold font-monospace" style="font-size:1.85rem;color:#0f172a;line-height:1.1;">
                                    {{ $totalPermohonan ?? 0 }}
                                </div>
                                <div style="font-size:0.75rem;color:#64748b;margin-top:4px;">Keseluruhan sistem</div>
                            </div>
                            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm"
                                 style="width:44px;height:44px;background-color:#eff6ff;border:1px solid #dbeafe;">
                                <i class="fa-solid fa-file-invoice" style="color:#1e40af;font-size:1.1rem;"></i>
                            </div>
                        </div>
                        <div class="kpi-progress">
                            <div class="kpi-progress-bar bg-primary" style="width: 100%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Menunggu Pengesahan Zon --}}
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-3 kpi-card hover-lift h-100" style="border-left:4px solid #d97706 !important;">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <div class="section-title mb-1">Menunggu Zon</div>
                                <div class="fw-bold font-monospace" style="font-size:1.85rem;color:#0f172a;line-height:1.1;">
                                    {{ $menungguPengesahan ?? 0 }}
                                </div>
                                <div style="font-size:0.75rem;color:#d97706;font-weight:600;margin-top:4px;">
                                    <i class="fa-solid fa-clock me-1"></i>Perlu tindakan segera
                                </div>
                            </div>
                            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm"
                                 style="width:44px;height:44px;background-color:#fffbeb;border:1px solid #fef3c7;">
                                <i class="fa-solid fa-clock-rotate-left" style="color:#d97706;font-size:1.1rem;"></i>
                            </div>
                        </div>
                        @php
                            $zmPct = ($totalPermohonan > 0) ? round(($menungguPengesahan / $totalPermohonan) * 100) : 0;
                        @endphp
                        <div class="kpi-progress">
                            <div class="kpi-progress-bar bg-warning" style="width: {{ $zmPct }}%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Disahkan Zon --}}
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-3 kpi-card hover-lift h-100" style="border-left:4px solid #16a34a !important;">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <div class="section-title mb-1">Disahkan Oleh Zon</div>
                                <div class="fw-bold font-monospace" style="font-size:1.85rem;color:#0f172a;line-height:1.1;">
                                    {{ $telahDisahkan ?? 0 }}
                                </div>
                                <div style="font-size:0.75rem;color:#16a34a;font-weight:600;margin-top:4px;">
                                    <i class="fa-solid fa-circle-check me-1"></i>Selesai peringkat zon
                                </div>
                            </div>
                            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm"
                                 style="width:44px;height:44px;background-color:#f0fdf4;border:1px solid #bbf7d0;">
                                <i class="fa-solid fa-circle-check" style="color:#16a34a;font-size:1.1rem;"></i>
                            </div>
                        </div>
                        @php
                            $zsPct = ($totalPermohonan > 0) ? round(($telahDisahkan / $totalPermohonan) * 100) : 0;
                        @endphp
                        <div class="kpi-progress">
                            <div class="kpi-progress-bar bg-success" style="width: {{ $zsPct }}%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Ditolak Zon --}}
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-3 kpi-card hover-lift h-100" style="border-left:4px solid #dc2626 !important;">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <div class="section-title mb-1">Ditolak Peringkat Zon</div>
                                <div class="fw-bold font-monospace" style="font-size:1.85rem;color:#0f172a;line-height:1.1;">
                                    {{ $ditolak ?? 0 }}
                                </div>
                                <div style="font-size:0.75rem;color:#dc2626;font-weight:600;margin-top:4px;">
                                    <i class="fa-solid fa-circle-xmark me-1"></i>Tidak diterima zon
                                </div>
                            </div>
                            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm"
                                 style="width:44px;height:44px;background-color:#fef2f2;border:1px solid #fecaca;">
                                <i class="fa-solid fa-circle-xmark" style="color:#dc2626;font-size:1.1rem;"></i>
                            </div>
                        </div>
                        @php
                            $ztPct = ($totalPermohonan > 0) ? round(($ditolak / $totalPermohonan) * 100) : 0;
                        @endphp
                        <div class="kpi-progress">
                            <div class="kpi-progress-bar bg-danger" style="width: {{ $ztPct }}%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Right 4 cols: Chart --}}
    <div class="col-xl-4">
        <div class="section-title mb-2">
            <i class="fa-solid fa-chart-pie me-1"></i> Nisbah Status Zon
        </div>
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between" style="border-bottom: 1px solid #eff6ff;">
                <div class="fw-bold" style="font-size:0.83rem;color:#0f172a;">Peratusan Kelulusan</div>
                <span class="badge bg-light text-secondary border font-monospace" style="font-size:0.68rem;">Carta Donut</span>
            </div>
            <div class="card-body p-3 d-flex align-items-center justify-content-center" style="min-height: 220px;">
                <div style="width: 100%; max-width: 250px; height: 180px;">
                    <canvas id="chartStatusZon"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── 3. Jadual Permohonan Terkini ── --}}
<!-- <div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between" style="border-bottom:2px solid #eff6ff;">
        <div class="fw-bold" style="font-size:0.875rem;color:#0f172a;">
            <i class="fa-solid fa-list-check me-2" style="color:#1e40af;"></i>Permohonan Pendaftaran Surau Terkini
        </div>
        <span class="badge px-3 py-1.5 fw-semibold"
              style="background-color:#eff6ff;color:#1e40af;border:1px solid #bfdbfe;font-size:0.72rem; border-radius:6px;">
            <i class="fa-solid fa-map-location-dot me-1"></i>Zon Seliaan
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead>
                    <tr>
                        <th class="ps-3">Surau &amp; No. Rujukan</th>
                        <th>Kariah Masjid</th>
                        <th class="text-center">Status Masjid</th>
                        <th class="text-center">Status JAWI</th>
                        <th class="text-center">Status Zon</th>
                        <th class="text-center pe-3">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($senaraiPermohonanTerkini ?? [] as $app)
                        @php
                            $masjidC = $app->masjidConfirmation ?? $app->confirmations->where('peringkat','MASJID')->first();
                            $jawiC   = $app->jawiConfirmation   ?? $app->confirmations->where('peringkat','JAWI')->first();
                            $zonC    = $app->confirmations->where('peringkat','ZON')->first();
                            $initial = strtoupper(substr($app->nama_surau ?? 'S', 0, 1));
                        @endphp
                        <tr>
                            <td class="ps-3">
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="avatar-initial">{{ $initial }}</div>
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size:0.85rem;">{{ $app->nama_surau }}</div>
                                        <span class="font-monospace text-primary fw-semibold" style="font-size:0.72rem;">{{ $app->reference_no }}</span>
                                    </div>
                                </div>
                            </td>
                            <td style="color:#475569;">
                                <i class="fa-solid fa-mosque me-1 text-secondary opacity-50"></i>
                                {{ $app->masjid?->nama_masjid ?? $app->kariah_masjid ?? '-' }}
                            </td>

                            {{-- STATUS MASJID --}}
                            <td class="text-center">
                                @if($masjidC && in_array($masjidC->status, ['APPROVED','DISAHKAN']))
                                    <span class="badge-status" style="background-color:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;">
                                        <i class="fa-solid fa-check me-1"></i>Disahkan
                                    </span>
                                @elseif($masjidC && in_array($masjidC->status, ['REJECTED','DITOLAK']))
                                    <span class="badge-status" style="background-color:#fef2f2;color:#dc2626;border:1px solid #fecaca;">
                                        <i class="fa-solid fa-xmark me-1"></i>Ditolak
                                    </span>
                                @else
                                    <span class="badge-status" style="background-color:#fffbeb;color:#92400e;border:1px solid #fde68a;">
                                        <i class="fa-solid fa-clock me-1"></i>Menunggu
                                    </span>
                                @endif
                            </td>

                            {{-- STATUS JAWI --}}
                            <td class="text-center">
                                @if($jawiC && in_array($jawiC->status, ['APPROVED','DISAHKAN']))
                                    <span class="badge-status" style="background-color:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;">
                                        <i class="fa-solid fa-check me-1"></i>Disahkan
                                    </span>
                                @elseif($jawiC && in_array($jawiC->status, ['REJECTED','DITOLAK']))
                                    <span class="badge-status" style="background-color:#fef2f2;color:#dc2626;border:1px solid #fecaca;">
                                        <i class="fa-solid fa-xmark me-1"></i>Ditolak
                                    </span>
                                @else
                                    <span class="badge-status" style="background-color:#fffbeb;color:#92400e;border:1px solid #fde68a;">
                                        <i class="fa-solid fa-clock me-1"></i>Menunggu
                                    </span>
                                @endif
                            </td>

                            {{-- STATUS ZON --}}
                            <td class="text-center">
                                @if($zonC && in_array($zonC->status, ['APPROVED','DISAHKAN']))
                                    <span class="badge-status" style="background-color:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;">
                                        <i class="fa-solid fa-check me-1"></i>Disahkan Zon
                                    </span>
                                @elseif($zonC && in_array($zonC->status, ['REJECTED','DITOLAK']))
                                    <span class="badge-status" style="background-color:#fef2f2;color:#dc2626;border:1px solid #fecaca;">
                                        <i class="fa-solid fa-xmark me-1"></i>Ditolak Zon
                                    </span>
                                @else
                                    <span class="badge-status" style="background-color:#fffbeb;color:#92400e;border:1px solid #fde68a;">
                                        <i class="fa-solid fa-clock me-1"></i>Menunggu Zon
                                    </span>
                                @endif
                            </td>

                            <td class="text-center pe-3">
                                <a href="{{ route('admin.permohonan.show', $app->id) }}"
                                   class="btn btn-sm fw-semibold hover-lift"
                                   style="background-color:#1e40af;color:#fff;border:none;border-radius:6px;font-size:0.78rem;padding:4px 12px;">
                                    <i class="fa-solid fa-eye me-1"></i> Lihat
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5" style="color:#94a3b8;">
                                <i class="fa-solid fa-circle-check fa-2x mb-2 d-block" style="color:#22c55e;"></i>
                                Tiada rekod permohonan dalam zon seliaan pada masa ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div> -->

{{-- ── 4. Capaian Pantas ── --}}
<!-- <div class="card border-0 shadow-sm rounded-3" style="border-left:4px solid #1e40af !important;">
    <div class="card-body p-4">
        <div class="section-title mb-3">
            <i class="fa-solid fa-bolt me-1"></i> Tindakan Pantas Pentadbir Zon
        </div>
        <div class="row g-2">
            <div class="col-md-6">
                <a href="{{ route('admin.permohonan.index') }}"
                   class="d-flex align-items-center justify-content-between gap-2 px-3 py-3 rounded-3 text-decoration-none hover-lift h-100"
                   style="background-color:#f8fafc;border:1px solid #e2e8f0;">
                    <div>
                        <i class="fa-solid fa-map-location-dot mb-1" style="color:#1e40af;font-size:1.1rem;"></i>
                        <div class="fw-semibold text-dark" style="font-size:0.83rem;">Senarai Permohonan Surau Zon</div>
                        <div style="font-size:0.72rem;color:#94a3b8;">Semak &amp; sahkan permohonan zon</div>
                    </div>
                    <i class="fa-solid fa-chevron-right" style="color:#cbd5e1;font-size:0.75rem;"></i>
                </a>
            </div>
            <div class="col-md-6">
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

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof window.Chart !== 'undefined') {
        const ctxZon = document.getElementById('chartStatusZon');
        if (ctxZon) {
            new window.Chart(ctxZon, {
                type: 'doughnut',
                data: {
                    labels: ['Menunggu Zon', 'Disahkan Zon', 'Ditolak Zon'],
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
                            labels: { boxWidth: 10, font: { size: 10, family: "'Segoe UI', sans-serif" } }
                        }
                    },
                    cutout: '68%'
                }
            });
        }
    }
});
</script>
@endpush