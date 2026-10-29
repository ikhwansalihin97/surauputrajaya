@extends('layouts.auth')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
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
                    <h5 class="mb-0 fw-bold"><i class="fa-solid fa-shield-halved me-2"></i> Pengesahan 2FA</h5>
                </div>
                <div class="card-body p-4 text-center">
                    <p class="text-muted small">Sila buka aplikasi <strong>Google Authenticator</strong> di telefon bimbit anda dan masukkan 6-digit kod keselamatan.</p>

                    @if ($errors->any())
                        <div class="alert alert-danger py-2 small">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form action="{{ route('2fa.verify') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <input type="text" name="one_time_password" class="form-control form-control-lg text-center fw-bold letter-spacing-2" placeholder="000000" maxlength="6" required autofocus style="border-color: #bfdbfe;">
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold" style="background-color: #1e40af; border-color: #1e40af;">
                            Sahkan Kod 2FA
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection