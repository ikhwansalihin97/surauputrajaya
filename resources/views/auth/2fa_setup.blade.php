@extends('layouts.app')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body p-4 text-center">
        <h4 class="fw-bold text-dark mb-3"><i class="fa-solid fa-qrcode text-primary me-2"></i> Tetapan Google Authenticator (2FA)</h4>
        <p class="text-muted">Imbas Kod QR di bawah menggunakan aplikasi <strong>Google Authenticator</strong> pada telefon pintar anda.</p>

        <div class="my-4">
            <img src="data:image/svg+xml;base64,{{ $qrCodeImage }}" alt="QR Code 2FA" class="img-thumbnail p-3 shadow-sm" style="border-color: #bfdbfe;">
        </div>

        <p class="small text-muted mb-4">Secret Key Manual: <code class="px-2 py-1 bg-light rounded text-primary">{{ $user->google2fa_secret }}</code></p>

        <form action="{{ route('2fa.enable') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary btn-lg shadow-sm fw-bold" style="background-color: #1e40af; border-color: #1e40af;">
                <i class="fa-solid fa-check-circle me-1"></i> Saya Sudah Imbas & Aktifkan 2FA
            </button>
        </form>
    </div>
</div>
@endsection