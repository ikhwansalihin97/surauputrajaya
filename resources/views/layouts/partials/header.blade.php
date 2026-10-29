<nav class="navbar navbar-expand-lg navbar-light shadow-sm py-3" style="background-color: #f0f7fc; border-bottom: 1px solid #dbeafe; position: relative; z-index: 1050;">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="{{ route('portal') }}">
            <i class="fa-solid fa-mosque me-2"></i> PORTAL SURAU
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link text-dark me-3" href="{{ route('portal') }}"> Utama</a>
                </li>

                @auth
                    <!-- Pengguna telah login: Boleh akses Dashboard & Portal tanpa logout -->
                    <li class="nav-item me-3">
                        <a class="btn btn-outline-primary btn-sm" href="{{ route('dashboard') }}">
                            <i class="fa-solid fa-gauge me-1"></i> Dashboard Saya
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-dark fw-semibold" href="#" id="userMenu" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-circle-user me-1"></i> {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li><h6 class="dropdown-header">Peranan Semasa: {{ strtoupper(session('active_role')) }}</h6></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button class="dropdown-item text-danger" type="submit">
                                        <i class="fa-solid fa-right-from-bracket me-1"></i> Log Keluar
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <!-- Pengguna belum login -->
                    <li class="nav-item">
                        <a class="btn btn-primary px-4 shadow-sm" href="{{ route('login') }}">
                            <i class="fa-solid fa-lock me-1"></i> Log Masuk
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>