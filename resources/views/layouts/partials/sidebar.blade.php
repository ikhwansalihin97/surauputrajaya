@php
    $activeRole = strtolower(
        session('active_role')
        ?? auth()->user()->roles->first()?->slug
        ?? ''
    );

    $isAdmin     = in_array($activeRole, ['admin', 'pentadbir sistem']);
    $isJawi      = in_array($activeRole, ['jawi', 'pentadbir jawi']);
    $isMasjid    = in_array($activeRole, ['masjid', 'pentadbir masjid']);
    $isZon       = in_array($activeRole, ['zon', 'pentadbir zon']);
    $isAdminOrJawi = $isAdmin || $isJawi;

    $pendingCount = 0;
    if ($isAdminOrJawi) {
        $pendingCount = \App\Models\Application::whereIn('status', ['PENDING', 'DALAM_SEMAKAN'])->count();
    }

    // Helper: is current route active?
    $isRoute = fn($pattern) => request()->routeIs($pattern);
@endphp

<div class="d-flex flex-column h-100 py-3 px-2" style="color:#475569;">

    {{-- ── Brand mark ── --}}
    <!-- <div class="px-2 mb-4 text-center">
        <div class="d-inline-flex align-items-center gap-2">
            <div class="rounded-2 d-flex align-items-center justify-content-center"
                 style="width:32px;height:32px;background-color:#1e40af;flex-shrink:0;">
                <i class="fa-solid fa-kaaba" style="font-size:0.78rem;color:#fff;"></i>
            </div>
            <div class="text-start">
                <div style="font-size:0.72rem;font-weight:700;letter-spacing:0.06em;color:#e2e8f0;">E-SURAU</div>
                <div style="font-size:0.60rem;color:#475569;letter-spacing:0.04em;">PANEL PENTADBIRAN</div>
            </div>
        </div>
    </div> -->

    {{-- ── Nav items ── --}}
    <nav class="flex-grow-1" style="overflow-y:auto;">
        <ul class="nav flex-column gap-1 list-unstyled m-0 p-0">

            {{-- Dashboard --}}
            @php
                $dashRoute = $isMasjid ? 'admin.masjid.dashboard' : 'dashboard';
                $dashActive = $isMasjid ? $isRoute('admin.masjid.dashboard') : $isRoute('dashboard');
            @endphp
            <li>
                <a href="{{ route($dashRoute) }}"
                   class="sidebar-nav-link {{ $dashActive ? 'active' : '' }}">
                    <i class="fa-solid fa-gauge-high nav-icon"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            {{-- ── PENGURUSAN (Admin & JAWI) ── --}}
            @if($isAdminOrJawi)
                <!-- <li class="sidebar-section-label mt-3">Pengurusan</li>

                <li>
                    <a href="{{ route('admin.permohonan.index') }}"
                       class="sidebar-nav-link {{ $isRoute('admin.permohonan.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-folder-open nav-icon"></i>
                        <span class="flex-grow-1">Permohonan Surau</span>
                        @if($pendingCount > 0)
                            <span class="badge rounded-pill ms-auto"
                                  style="background-color:#dc2626;font-size:0.67rem;padding:2px 6px;">{{ $pendingCount }}</span>
                        @endif
                    </a>
                </li> -->

                @if($isAdmin)
                <li>
                    <a href="{{ route('admin.users.index') }}"
                       class="sidebar-nav-link {{ $isRoute('admin.users.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-users nav-icon"></i>
                        <span>Pengurusan Pengguna</span>
                    </a>
                </li>
                @endif
            @endif

            {{-- ── PENTADBIR MASJID ── --}}
            @if($isMasjid)
                <li class="sidebar-section-label mt-3">Pengurusan Masjid</li>
                <li>
                    <a href="{{ route('admin.masjid.pengesahan.index') }}"
                       class="sidebar-nav-link {{ $isRoute('admin.masjid.pengesahan.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-mosque nav-icon"></i>
                        <span>Pengesahan Kariah</span>
                    </a>
                </li>
            @endif

            {{-- ── PENTADBIR JAWI ── --}}
            @if($isJawi)
                <li class="sidebar-section-label mt-3">Pengurusan JAWI</li>
                <li>
                    <a href="{{ route('admin.jawi.pengesahan.index') }}"
                       class="sidebar-nav-link {{ $isRoute('admin.jawi.pengesahan.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-file-circle-check nav-icon"></i>
                        <span>Pengesahan Permohonan</span>
                    </a>
                </li>
            @endif

            {{-- ── PENTADBIR ZON ── --}}
            @if($isZon)
                <li class="sidebar-section-label mt-3">Pengurusan Zon</li>
                <li>
                    <a href="{{ route('admin.permohonan.index') }}"
                       class="sidebar-nav-link {{ $isRoute('admin.permohonan.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-map-location-dot nav-icon"></i>
                        <span>Permohonan Zon</span>
                    </a>
                </li>
            @endif

            {{-- ── PENTADBIR SURAU ── --}}
            @if(!$isAdmin && !$isJawi && !$isMasjid && !$isZon)
                <li class="sidebar-section-label mt-3">Surau Saya</li>
                <li>
                    <a href="{{ route('dashboard') }}"
                       class="sidebar-nav-link {{ $isRoute('dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-kaaba nav-icon"></i>
                        <span>Maklumat Surau</span>
                    </a>
                </li>
            @endif


            <li class="sidebar-section-label mt-3">Keselamatan</li>

            @if($isAdmin)
            <li>
                <a href="{{ route('admin.audit-logs.index') }}"
                   class="sidebar-nav-link {{ ($isRoute('admin.audit-logs.*') || $isRoute('admin.audit.*')) ? 'active' : '' }}">
                    <i class="fa-solid fa-clock-rotate-left nav-icon"></i>
                    <span>Audit Trail</span>
                </a>
            </li>
            @endif

            <li>
                <a href="{{ route('2fa.setup') }}"
                   class="sidebar-nav-link {{ $isRoute('2fa.setup') ? 'active' : '' }}">
                    <i class="fa-solid fa-shield-halved nav-icon" style="color:{{ $isRoute('2fa.setup') ? '#fff' : '#f59e0b' }};"></i>
                    <span class="flex-grow-1">Tetapan 2FA</span>
                    <span class="ms-auto" style="font-size:0.55rem;">
                        <i class="fa-solid fa-circle {{ auth()->user()->google2fa_enabled ? 'text-success' : 'text-warning' }}"></i>
                    </span>
                </a>
            </li>

        </ul>
    </nav>

    {{-- ── User info (bottom) ── --}}
    <!-- <div class="mt-auto px-2 pt-3" style="border-top:1px solid #dbeafe;">
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                 style="width:32px;height:32px;background-color:#1e40af;">
                <i class="fa-solid fa-user" style="font-size:0.72rem;color:#fff;"></i>
            </div>
            <div class="overflow-hidden">
                <div class="text-truncate fw-semibold" style="font-size:0.80rem;color:#1e3a8a;max-width:160px;">
                    {{ auth()->user()->name }}
                </div>
                <div class="text-uppercase font-monospace" style="font-size:0.60rem;color:#64748b;letter-spacing:0.06em;">
                    {{ session('active_role') ?? auth()->user()->roles->first()?->slug ?? 'PENGGUNA' }}
                </div>
            </div>
        </div>
    </div> -->

</div>