@extends('layouts.auth')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            
            <!-- Logo JAWI & Tajuk Atas Borang -->
            <div class="text-center mb-4">
                <a href="{{ route('portal') }}" class="text-decoration-none">
                    <!-- Memanggil Logo WebP dari public/images/jawi-logo.webp -->
                    <img src="{{ asset('images/logo-jawi.webp') }}" alt="Logo JAWI" class="img-fluid mb-2" style="max-height: 100px; width: auto;">
                    <h4 class="fw-bold text-dark mt-2 mb-0">SISTEM PENGURUSAN SURAU</h4>
                </a>
                <p class="text-muted small">Jabatan Agama Islam Wilayah Persekutuan</p>
            </div>

            <div class="card shadow-sm border-0 rounded-3" style="overflow: hidden;">
                <div class="card-header text-white text-center py-3" style="background: linear-gradient(135deg, #1e40af 0%, #0284c7 100%);">
                    <h6 class="mb-0 fw-bold"><i class="fa-solid fa-lock me-2"></i> Log Masuk</h6>
                </div>
                <div class="card-body p-4">

                    @if ($errors->any())
                        <div class="alert alert-danger py-2">
                            <ul class="mb-0 small ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('login.post') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Alamat E-Mel</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-envelope"></i></span>
                                <input type="email" name="email" class="form-control" placeholder="admin@surau.com" required autofocus>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold small">Kata Laluan</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-key"></i></span>
                                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2 shadow-sm fw-bold mb-2" style="background-color: #1e40af; border-color: #1e40af;">
                            <i class="fa-solid fa-right-to-bracket me-1"></i> Log Masuk
                        </button>
                    </form>
                </div>
            </div>

            <!-- Pautan Kembali Ke Portal Korporat -->
            <div class="text-center mt-3">
                <a href="{{ route('portal') }}" class="text-decoration-none text-muted small fw-semibold">
                    <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Portal
                </a>
            </div>

        </div>
    </div>
</div>
@endsection