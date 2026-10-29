@extends('layouts.app')

@section('content')

@if(!$application)
    {{-- ── Tiada Permohonan ── --}}
    <div class="d-flex align-items-center justify-content-center" style="min-height: 60vh;">
        <div class="card border-0 shadow-sm rounded-3 text-center p-5 hover-lift" style="max-width:540px;width:100%;border-top:4px solid #d97706 !important;">
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mx-auto mb-4"
                 style="width:72px;height:72px;background-color:#fffbeb;border:1px solid #fef3c7;">
                <i class="fa-solid fa-kaaba fa-2x" style="color:#d97706;"></i>
            </div>
            <h5 class="fw-bold text-dark mb-2">Pendaftaran Surau Belum Ditemui</h5>
            <p class="text-muted mb-4" style="font-size:0.875rem;line-height:1.6;">
                Akaun anda belum mempunyai sebarang rekod permohonan pendaftaran surau yang dipautkan.
                Sila semak status permohonan anda atau hantar permohonan baharu.
            </p>
            <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
                <a href="{{ route('permohonan.form') }}"
                   class="btn fw-bold px-4 py-2 hover-lift"
                   style="background-color:#1e40af;color:#fff;border:none;border-radius:8px;">
                    <i class="fa-solid fa-plus-circle me-2"></i>Borang Permohonan Surau Baharu
                </a>
                <a href="{{ route('portal') }}" class="btn btn-outline-secondary px-3 py-2 small fw-semibold">
                    Ke Portal Awam
                </a>
            </div>
        </div>
    </div>

@else
    {{-- ── 1. Hero Welcome Banner Surau ── --}}
    @php
        $finalStatus = $application->status ?? 'PENDING';
        [$hdrBg, $hdrColor, $hdrIcon, $hdrLabel] = match($finalStatus) {
            'APPROVED' => ['#f0fdf4', '#16a34a', 'fa-circle-check', 'LULUS BERDAFTAR'],
            'REJECTED' => ['#fef2f2', '#dc2626', 'fa-circle-xmark', 'DITOLAK'],
            default    => ['#fffbeb', '#d97706', 'fa-clock', 'DALAM PROSES'],
        };
    @endphp

    <div class="hero-welcome-card p-4 mb-4 shadow-sm">
        <div class="hero-shape-1"></div>
        <div class="hero-shape-2"></div>
        <div class="row align-items-center position-relative">
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                    <span class="badge bg-white bg-opacity-25 px-2.5 py-1 text-white fw-semibold" style="font-size:0.72rem; letter-spacing:0.04em;">
                        <i class="fa-solid fa-kaaba me-1"></i> DASHBOARD PENTADBIR SURAU
                    </span>
                    <span class="badge bg-white text-primary px-2.5 py-1 fw-bold" style="font-size:0.72rem;">
                        <i class="fa-solid fa-calendar-day me-1"></i> {{ now()->translatedFormat('l, d F Y') }}
                    </span>
                </div>
                <h4 class="fw-bold text-white mb-1">{{ $application->nama_surau }}</h4>
                <div class="text-white-50 small mt-1">
                    No. Rujukan: <strong class="text-white font-monospace">{{ $application->reference_no }}</strong>
                    &nbsp;&bull;&nbsp;
                    Kariah: <strong class="text-white">{{ $application->masjid?->nama_masjid ?? $application->kariah_masjid ?? '-' }}</strong>
                </div>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <span class="badge fw-bold px-3 py-2 shadow-sm rounded-2"
                      style="background-color:{{ $hdrBg }};color:{{ $hdrColor }};border:1px solid {{ $hdrColor }}40;font-size:0.83rem;letter-spacing:0.05em;">
                    <i class="fa-solid {{ $hdrIcon }} me-1"></i> {{ $hdrLabel }}
                </span>
            </div>
        </div>
    </div>

    {{-- ── 2. Status Tracker (Horizontal Stepper) ── --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3" style="border-bottom:2px solid #eff6ff;">
            <div class="fw-bold" style="font-size:0.875rem;color:#0f172a;">
                <i class="fa-solid fa-bars-progress me-2" style="color:#1e40af;"></i>Status Peringkat Permohonan Pendaftaran Surau
            </div>
        </div>
        <div class="card-body px-4 py-4">
            @php
                $masjidConf = $application->masjidConfirmation ?? $application->confirmations->where('peringkat','MASJID')->first();
                $jawiConf   = $application->jawiConfirmation   ?? $application->confirmations->where('peringkat','JAWI')->first();
                $zonConf    = $application->zonConfirmation    ?? $application->confirmations->where('peringkat','ZON')->first();

                $isMasjidOk  = in_array($masjidConf?->status, ['APPROVED','DISAHKAN']);
                $isMasjidErr = in_array($masjidConf?->status, ['REJECTED','DITOLAK']);
                $isJawiOk    = in_array($jawiConf?->status, ['APPROVED','DISAHKAN']);
                $isJawiErr   = in_array($jawiConf?->status, ['REJECTED','DITOLAK']);
                $isZonOk     = in_array($zonConf?->status, ['APPROVED','DISAHKAN']);
                $isZonErr    = in_array($zonConf?->status, ['REJECTED','DITOLAK']);

                $steps = [
                    [
                        'num'     => 1,
                        'label'   => 'Permohonan Dihantar',
                        'sub'     => $application->created_at ? $application->created_at->format('d/m/Y') : '-',
                        'done'    => true,
                        'error'   => false,
                        'badge'   => 'SELESAI',
                        'badgeBg' => '#1e40af',
                    ],
                    [
                        'num'     => 2,
                        'label'   => 'Pengesahan Masjid',
                        'sub'     => $application->masjid?->nama_masjid ?? 'Kariah Masjid',
                        'done'    => $isMasjidOk,
                        'error'   => $isMasjidErr,
                        'badge'   => $isMasjidOk ? 'DISAHKAN' : ($isMasjidErr ? 'DITOLAK' : 'DALAM SEMAKAN'),
                        'badgeBg' => $isMasjidOk ? '#16a34a' : ($isMasjidErr ? '#dc2626' : '#d97706'),
                    ],
                    [
                        'num'     => 3,
                        'label'   => 'Semakan JAWI',
                        'sub'     => 'Dokumen & Minit AGM',
                        'done'    => $isJawiOk,
                        'error'   => $isJawiErr,
                        'badge'   => $isJawiOk ? 'DISAHKAN' : ($isJawiErr ? 'DITOLAK' : 'MENUNGGU'),
                        'badgeBg' => $isJawiOk ? '#16a34a' : ($isJawiErr ? '#dc2626' : '#94a3b8'),
                    ],
                    [
                        'num'     => 4,
                        'label'   => 'Pengesahan Zon',
                        'sub'     => 'Pusat Zon',
                        'done'    => $isZonOk,
                        'error'   => $isZonErr,
                        'badge'   => $isZonOk ? 'DISAHKAN' : ($isZonErr ? 'DITOLAK' : 'MENUNGGU'),
                        'badgeBg' => $isZonOk ? '#16a34a' : ($isZonErr ? '#dc2626' : '#94a3b8'),
                    ],
                ];
            @endphp

            {{-- Stepper layout --}}
            <div class="d-flex align-items-start">
                @foreach($steps as $i => $step)
                    <div class="text-center flex-fill" style="min-width:0;">
                        {{-- Circle + connector row --}}
                        <div class="d-flex align-items-center mb-2">
                            @if($i > 0)
                                <div class="flex-grow-1" style="height:3px; border-radius:2px;
                                    background-color: {{ $steps[$i-1]['error'] ? '#fecaca' : ($steps[$i-1]['done'] ? '#86efac' : '#e2e8f0') }};
                                    margin-top: -10px;">
                                </div>
                            @else
                                <div style="width:0;"></div>
                            @endif

                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 fw-bold shadow-sm"
                                 style="width:38px;height:38px;font-size:0.85rem;
                                    background-color: {{ $step['error'] ? '#fef2f2' : ($step['done'] ? '#f0fdf4' : '#f8fafc') }};
                                    border: 2px solid {{ $step['error'] ? '#dc2626' : ($step['done'] ? '#16a34a' : '#cbd5e1') }};
                                    color: {{ $step['error'] ? '#dc2626' : ($step['done'] ? '#16a34a' : '#64748b') }};">
                                @if($step['done'])
                                    <i class="fa-solid fa-check" style="font-size:0.85rem;"></i>
                                @elseif($step['error'])
                                    <i class="fa-solid fa-xmark" style="font-size:0.85rem;"></i>
                                @else
                                    {{ $step['num'] }}
                                @endif
                            </div>

                            @if($i < count($steps) - 1)
                                <div class="flex-grow-1" style="height:3px; border-radius:2px;
                                    background-color: {{ $steps[$i]['error'] ? '#fecaca' : ($steps[$i]['done'] ? '#86efac' : '#e2e8f0') }};
                                    margin-top: -10px;">
                                </div>
                            @else
                                <div style="width:0;"></div>
                            @endif
                        </div>

                        {{-- Label --}}
                        <div class="fw-semibold text-dark mb-1" style="font-size:0.78rem; padding:0 4px;">{{ $step['label'] }}</div>
                        <div class="text-truncate" style="font-size:0.70rem;color:#94a3b8;padding:0 4px;" title="{{ $step['sub'] }}">{{ $step['sub'] }}</div>
                        <div class="mt-2">
                            <span class="badge fw-bold" style="background-color:{{ $step['badgeBg'] }};font-size:0.65rem;border-radius:5px;padding:3px 8px;letter-spacing:0.04em;">
                                {{ $step['badge'] }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── Temujanji Alert ── --}}
    @if($application->appointment)
        <div class="d-flex align-items-center gap-3 rounded-3 p-3 mb-4 shadow-sm hover-lift"
             style="background-color:#eff6ff; border:1px solid #bfdbfe; border-left:4px solid #1e40af;">
            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm"
                 style="width:46px;height:46px;background-color:#1e40af;">
                <i class="fa-solid fa-calendar-check" style="color:#fff;font-size:1.1rem;"></i>
            </div>
            <div class="flex-grow-1">
                <div class="fw-bold" style="font-size:0.875rem;color:#1e40af;">
                    Temujanji Pemeriksaan / Semakan Ditentukan
                </div>
                <div style="font-size:0.80rem;color:#1e3a8a;">
                    <strong>Tarikh &amp; Masa:</strong>
                    <span class="font-monospace">
                        {{ \Carbon\Carbon::parse($application->appointment->tarikh_temujanji)->format('d/m/Y') }}@if($application->appointment->masa_temujanji) ({{ \Carbon\Carbon::parse($application->appointment->masa_temujanji)->format('h:i A') }})@endif
                    </span>
                    &nbsp;&bull;&nbsp;
                    <strong>Lokasi:</strong> {{ $application->appointment->tempat ?? $application->appointment->lokasi ?? 'Surau' }}
                    @if($application->appointment->catatan)
                        &nbsp;&bull;&nbsp;
                        <strong>Catatan:</strong> {{ $application->appointment->catatan }}
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- ── 3. Minit Mesyuarat Section ── --}}
    <div class="row g-3">

        {{-- Upload Form --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-3 h-100 hover-lift">
                <div class="card-header py-3" style="background: linear-gradient(135deg, #1e40af 0%, #0284c7 100%); border-bottom:none; border-radius: 12px 12px 0 0;">
                    <div class="fw-bold text-white" style="font-size:0.875rem;">
                        <i class="fa-solid fa-file-arrow-up me-2"></i>Muat Naik Minit Mesyuarat Agung (AGM)
                    </div>
                </div>
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success border-0 rounded-2 py-2 px-3 mb-3 d-flex align-items-center gap-2" style="font-size:0.82rem;background-color:#f0fdf4;color:#16a34a;">
                            <i class="fa-solid fa-circle-check flex-shrink-0"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger border-0 rounded-2 py-2 px-3 mb-3" style="font-size:0.80rem;background-color:#fef2f2;color:#dc2626;">
                            <ul class="mb-0 ps-3">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.minit.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="application_id" value="{{ $application->id }}">

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">Tahun Mesyuarat Agung (AGM)</label>
                            <input type="number"
                                   name="tahun_minit"
                                   class="form-control form-control-sm font-monospace"
                                   value="{{ old('tahun_minit', date('Y')) }}"
                                   min="2000"
                                   max="{{ date('Y') + 1 }}"
                                   required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold small text-dark">Fail Minit Mesyuarat (PDF)</label>
                            <input type="file"
                                   name="fail_minit"
                                   class="form-control form-control-sm"
                                   accept=".pdf"
                                   required>
                            <div class="form-text" style="font-size:0.72rem;color:#94a3b8;">
                                Format: PDF sahaja. Saiz maksimum fail: 5MB.
                            </div>
                        </div>

                        <button type="submit"
                                class="btn w-100 fw-bold py-2 shadow-sm rounded-2 text-white hover-lift"
                                style="background-color:#1e40af;border:none;font-size:0.83rem;">
                            <i class="fa-solid fa-cloud-arrow-up me-1"></i>
                            Hantar Minit Untuk Semakan JAWI
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Senarai Minit --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between" style="border-bottom:2px solid #eff6ff;">
                    <div class="fw-bold" style="font-size:0.875rem;color:#0f172a;">
                        <i class="fa-solid fa-clock-rotate-left me-2" style="color:#1e40af;"></i>Sejarah Minit Mesyuarat Yang Dihantar
                    </div>
                    <span class="badge fw-semibold" style="background-color:#eff6ff;color:#1e40af;border:1px solid #bfdbfe;font-size:0.72rem;">
                        {{ $minitList->count() }} rekod
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead>
                                <tr>
                                    <th class="ps-3">Tahun AGM</th>
                                    <th>Fail Minit</th>
                                    <th class="text-center">Status Semakan</th>
                                    <th class="text-end pe-3">Tarikh Hantar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($minitList as $minit)
                                    <tr>
                                        <td class="ps-3 font-monospace fw-bold" style="color:#0f172a;">{{ $minit->tahun_minit }}</td>
                                        <td>
                                            <a href="{{ asset('storage/' . $minit->fail_path) }}" target="_blank"
                                               class="btn btn-sm fw-semibold hover-lift"
                                               style="background-color:#eff6ff;color:#1e40af;border:1px solid #bfdbfe;border-radius:6px;font-size:0.78rem;">
                                                <i class="fa-solid fa-file-pdf me-1" style="color:#dc2626;"></i>Lihat PDF
                                            </a>
                                        </td>
                                        <td class="text-center">
                                            @if($minit->status_pengesahan == 'DISAHKAN')
                                                <span class="badge-status" style="background-color:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;">
                                                    <i class="fa-solid fa-check me-1"></i>Disahkan
                                                </span>
                                            @elseif($minit->status_pengesahan == 'DITOLAK')
                                                <span class="badge-status" style="background-color:#fef2f2;color:#dc2626;border:1px solid #fecaca;" title="{{ $minit->catatan_semakan ?? '' }}">
                                                    <i class="fa-solid fa-xmark me-1"></i>Ditolak
                                                </span>
                                            @else
                                                <span class="badge-status" style="background-color:#fffbeb;color:#92400e;border:1px solid #fde68a;">
                                                    <i class="fa-solid fa-clock me-1"></i>Dalam Semakan
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-3 font-monospace" style="font-size:0.78rem;color:#94a3b8;">
                                            {{ $minit->created_at ? $minit->created_at->format('d/m/Y') : '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5" style="color:#94a3b8;">
                                            <i class="fa-solid fa-folder-open fa-2x mb-2 d-block"></i>
                                            Tiada rekod minit mesyuarat ditemui.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endif

@endsection