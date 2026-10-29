<nav class="navbar navbar-expand-lg navbar-light shadow-sm py-2.5" style="background-color: #f0f7fc; border-bottom: 1px solid #dbeafe; position: relative; z-index: 1050;">
    <div class="container">
        <!-- Brand / Logo Korporat -->
        <!-- <a class="navbar-brand d-flex align-items-center me-4 text-decoration-none" href="{{ route('portal') }}">
            <div class="rounded-circle p-2 me-2 text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background-color: #1e40af;">
                <i class="fa-solid fa-kaaba fs-6 text-white"></i>
            </div>
            <span class="fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.95rem; color: #1e3a8a;">
                Portal Surau JAWI
            </span>
        </a> -->
        <a class="navbar-brand d-flex align-items-center me-4 text-decoration-none" href="{{ route('portal') }}">
            <img src="{{ asset('images/logo-jawi.webp') }}" alt="Logo JAWI" class="me-2" style="height: 32px; width: auto;">
            <span class="fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.95rem; color: #1e3a8a;">
                Portal Pengurusan Surau (JAWI)
            </span>
        </a>

        <!-- Mobile Toggle Button -->
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarPortal">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarPortal">
            <!-- Navigasi Utama -->
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                <li class="nav-item">
                    <a class="nav-link text-dark small fw-medium px-3" href="{{ route('portal') }}">
                        Utama
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-dark small fw-medium px-3" href="{{ route('permohonan.form') }}">
                        Permohonan Surau
                    </a>
                </li>

                <li class="nav-item">
                    <button type="button" class="nav-link btn btn-link text-dark text-decoration-none small fw-medium px-3 border-0" data-bs-toggle="modal" data-bs-target="#modalSemakanHeader">
                        Semak Status Permohonan
                    </button>
                </li>

                <!-- Logik Sesi Pengguna -->
                @auth
                    <li class="nav-item ms-lg-2 my-1 my-lg-0">
                        <a class="btn btn-sm px-3 py-1.5 small fw-medium rounded-2 border-0 shadow-sm text-white" href="{{ route('dashboard') }}" style="background-color: #1e40af;">
                            Dashboard Saya
                        </a>
                    </li>
                @else
                    <li class="nav-item ms-lg-2 my-1 my-lg-0">
                        <a class="btn btn-outline-primary btn-sm px-3 py-1.5 small fw-medium rounded-2" href="{{ route('login') }}">
                            Log Masuk Staf
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<!-- MODAL SEMAKAN STATUS PERMOHONAN -->
<div class="modal fade" id="modalSemakanHeader" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white py-3" style="background-color: #0284c7;">
                <h6 class="modal-title mb-0 fw-bold">
                    <i class="fa-solid fa-magnifying-glass me-2 text-white"></i> Semakan Status Permohonan Surau
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            
            <form action="{{ route('permohonan.semak') }}" method="GET">
                <div class="modal-body p-4">
                    @if(session('error'))
                        <div class="alert alert-danger py-2 small mb-3">
                            <i class="fa-solid fa-circle-exclamation me-1"></i> {{ session('error') }}
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Nombor Rujukan Permohonan</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted font-monospace"><i class="fa-solid fa-ticket"></i></span>
                            <input type="text" name="reference_no" class="form-control form-control-lg font-monospace text-uppercase" placeholder="SURAU-XXXXXX" required autofocus>
                        </div>
                        <div class="form-text small mt-2">Masukkan nombor rujukan rasmi permohonan anda (Contoh: <code>SURAU-AB12CD</code>).</div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-sm btn-secondary fw-semibold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm text-white fw-bold px-4" style="background-color: #0284c7; border: none;">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> Semak Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>