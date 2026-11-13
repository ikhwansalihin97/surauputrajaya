<nav class="navbar py-0 shadow-sm" style="background-color: #f0f7fc; border-bottom: 1px solid #dbeafe; min-height: 54px; position: relative; z-index: 1050;">
    <div class="container-fluid px-3 d-flex align-items-center" style="min-height: 54px;">

        {{-- Brand / Logo --}}
        <!-- <a class="navbar-brand d-flex align-items-center gap-2 text-dark text-decoration-none me-4" href="{{ route('dashboard') }}" style="font-size: 0.9rem; font-weight: 700; letter-spacing: 0.5px;">
            <div class="d-flex align-items-center justify-content-center rounded-2 text-white" style="width:32px; height:32px; background-color:#1e40af;">
                <i class="fa-solid fa-kaaba" style="font-size:0.85rem;"></i>
            </div>
            <span style="letter-spacing:0.04em; font-size:0.82rem; color:#1e3a8a; font-weight:800;">E-SURAU JAWI</span>
        </a> -->
        <a class="navbar-brand d-flex align-items-center me-4 text-decoration-none" href="{{ route('portal') }}">
            <img src="{{ asset('images/logo-jawi.webp') }}" alt="Logo JAWI" class="me-2" style="height: 32px; width: auto;">
            <span class="fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.95rem; color: #1e3a8a;">
                e-SURAU (JAWI)
            </span>
        </a>

        {{-- Spacer --}}
        <div class="flex-grow-1"></div>

        {{-- Right controls --}}
        <div class="d-flex align-items-center gap-2">

            {{-- Active role badge --}}
            @php
            $headerRole = strtolower(
            session('active_role')
            ?? auth()->user()->roles->first()?->slug
            ?? 'pengguna'
            );

            $headerRoleConfig = match($headerRole) {
                'admin' => [
                    'label' => 'Pentadbir Sistem',
                    'color' => '#dc2626',
                ],
                'jawi' => [
                    'label' => 'Pentadbir JAWI',
                    'color' => '#1e40af',
                ],
                'masjid' => [
                    'label' => 'Pentadbir Masjid',
                    'color' => '#0891b2',
                ],
                'surau' => [
                    'label' => 'Pentadbir Surau',
                    'color' => '#d97706',
                ],
                default => [
                    'label' => 'Pengguna',
                    'color' => '#475569',
                ],
            };

            $headerRoleLabel = $headerRoleConfig['label'];
            $headerRoleColor = $headerRoleConfig['color'];

            @endphp

            <span class="badge fw-bold px-2 py-1 d-none d-md-inline-block"
            style="background-color: {{ $headerRoleColor }}1a;
                    color: {{ $headerRoleColor }};
                    border: 1px solid {{ $headerRoleColor }}40;
                    font-size: 0.68rem;
                    letter-spacing: 0.08em;">

            <i class="fa-solid fa-circle-dot me-1"
            style="font-size: 0.55rem;"></i>

            {{ $headerRoleLabel }}

            </span>

            {{-- Date --}}
            <span class="text-slate-400 d-none d-lg-inline" style="font-size: 0.78rem; color: #64748b;">
                <i class="fa-regular fa-calendar me-1"></i>{{ now()->translatedFormat('d M Y') }}
            </span>

            {{-- User dropdown --}}
            <div class="dropdown">
                <a class="dropdown-toggle d-flex align-items-center gap-2 text-decoration-none px-2 py-1 rounded-2"
                   href="#" id="navUserDropdown" role="button"
                   data-bs-toggle="dropdown" aria-expanded="false"
                   style="background-color: #ffffff; border: 1px solid #bfdbfe;">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:28px; height:28px; background-color:#1e40af; flex-shrink:0;">
                        <i class="fa-solid fa-user" style="font-size:0.7rem; color:#fff;"></i>
                    </div>
                    <span style="font-size: 0.82rem; font-weight: 600; color: #1e3a8a; max-width: 140px;" class="text-truncate d-none d-md-inline">
                        {{ Auth::user()->name }}
                    </span>
                    <!-- <i class="fa-solid fa-chevron-down" style="font-size: 0.6rem; color: #94a3b8;"></i> -->
                </a>

                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-1 p-2" style="min-width: 240px; border-radius: 10px; z-index: 1060;">
                    {{-- User info --}}
                    <li class="px-3 py-2 mb-1" style="border-bottom: 1px solid #f1f5f9;">
                        <div class="fw-bold text-dark" style="font-size: 0.85rem;">{{ Auth::user()->name }}</div>
                        <div class="text-muted" style="font-size: 0.75rem;">{{ Auth::user()->email }}</div>
                    </li>

                    <li>
                        <a href="{{ route('profile.edit') }}" class="dropdown-item rounded-2 py-2 px-3 d-flex align-items-center gap-2" style="font-size: 0.83rem;">
                            <i class="fa-solid fa-user-pen text-primary" style="width:16px;"></i> Profil Saya
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('portal') }}" class="dropdown-item rounded-2 py-2 px-3 d-flex align-items-center gap-2" style="font-size: 0.83rem;">
                            <i class="fa-solid fa-globe text-primary" style="width:16px;"></i> Portal Awam e-Surau
                        </a>
                    </li>

                    @if(Auth::user()->roles->count() > 1)
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <div class="px-3 py-1 text-uppercase fw-bold" style="font-size: 0.62rem; letter-spacing: 0.08em; color: #94a3b8;">
                                <i class="fa-solid fa-repeat me-1"></i>Tukar Peranan
                            </div>
                        </li>
                        @foreach(Auth::user()->roles as $role)
                            @php
                                $isActive = session('active_role') == $role->slug;
                                $rIcon = match($role->slug) {
                                    'admin'  => 'fa-user-shield',
                                    'masjid' => 'fa-mosque',
                                    'jawi'   => 'fa-building-columns',
                                    'surau'  => 'fa-kaaba',
                                    default  => 'fa-user-gear'
                                };
                            @endphp
                            <li>
                                <form action="{{ route('switch.role') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="role_slug" value="{{ $role->slug }}">
                                    <button type="submit"
                                            class="dropdown-item rounded-2 py-1 px-3 d-flex align-items-center justify-content-between gap-2"
                                            style="font-size: 0.82rem; {{ $isActive ? 'background-color:#eff6ff; color:#1e40af; font-weight:600;' : '' }}">
                                        <span>
                                            <i class="fa-solid {{ $rIcon }} me-2" style="width:16px; color: {{ $isActive ? '#1e40af' : '#94a3b8' }};"></i>
                                            {{ $role->name }}
                                        </span>
                                        @if($isActive)
                                            <i class="fa-solid fa-check" style="font-size:0.7rem; color:#1e40af;"></i>
                                        @endif
                                    </button>
                                </form>
                            </li>
                        @endforeach
                    @endif

                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="dropdown-item rounded-2 py-2 px-3 d-flex align-items-center gap-2 text-danger fw-semibold" type="submit" style="font-size: 0.83rem;">
                                <i class="fa-solid fa-right-from-bracket" style="width:16px;"></i> Log Keluar
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>{{-- /right controls --}}
    </div>
</nav>