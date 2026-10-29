@extends('layouts.auth')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">

            {{-- Logo & Tajuk --}}
            <div class="text-center mb-4">
                <a href="{{ route('portal') }}" class="text-decoration-none">
                    <img src="{{ asset('images/logo-jawi.webp') }}" alt="Logo JAWI" class="img-fluid mb-2" style="max-height: 100px; width: auto;">
                    <h4 class="fw-bold text-dark mt-2 mb-0">SISTEM PENGURUSAN SURAU</h4>
                </a>
                <p class="text-muted small">Jabatan Agama Islam Wilayah Persekutuan</p>
            </div>

            {{-- Kad Borang --}}
            <div class="card shadow-sm border-0 rounded-3" style="overflow: hidden;">
                <div class="card-header text-white text-center py-3" style="background: linear-gradient(135deg, #1e40af 0%, #0284c7 100%);">
                    <h6 class="mb-0 fw-bold">
                        <i class="fa-solid fa-key me-2"></i>Tetapkan Kata Laluan Akaun
                    </h6>
                </div>
                <div class="card-body p-4">

                    {{-- Salam aluan --}}
                    <div class="alert py-2 mb-3" style="background-color:#eff6ff; border:1px solid #bfdbfe; border-radius:8px;">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-circle-info" style="color:#1e40af;"></i>
                            <div class="small" style="color:#1e3a8a;">
                                Assalamu'alaikum, <strong>{{ $user->name }}</strong>.<br>
                                Sila tetapkan kata laluan untuk mengaktifkan akaun Pentadbir Surau anda.
                            </div>
                        </div>
                    </div>

                    {{-- Flash errors --}}
                    @if ($errors->any())
                        <div class="alert alert-danger py-2">
                            <ul class="mb-0 small ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Borang --}}
                    <form action="{{ route('account.activate.set', $token) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Kata Laluan Baharu</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted">
                                    <i class="fa-solid fa-lock"></i>
                                </span>
                                <input
                                    type="password"
                                    name="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Sekurang-kurangnya 8 aksara"
                                    required
                                    autofocus
                                >
                            </div>
                            <div class="form-text text-muted" style="font-size:0.75rem;">
                                Kata laluan mestilah sekurang-kurangnya 8 aksara.
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold small">Sahkan Kata Laluan</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted">
                                    <i class="fa-solid fa-lock-open"></i>
                                </span>
                                <input
                                    type="password"
                                    name="password_confirmation"
                                    class="form-control"
                                    placeholder="Taip semula kata laluan"
                                    required
                                >
                            </div>
                        </div>

                        <button
                            type="submit"
                            class="btn w-100 py-2 shadow-sm fw-bold mb-2 text-white"
                            style="background: linear-gradient(135deg, #1e40af 0%, #0284c7 100%); border: none;"
                        >
                            <i class="fa-solid fa-right-to-bracket me-2"></i>Aktifkan Akaun & Log Masuk
                        </button>
                    </form>

                </div>
            </div>

            {{-- Pautan Kembali --}}
            <div class="text-center mt-3">
                <a href="{{ route('portal') }}" class="text-decoration-none text-muted small fw-semibold">
                    <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Portal
                </a>
            </div>

        </div>
    </div>
</div>
@endsection
