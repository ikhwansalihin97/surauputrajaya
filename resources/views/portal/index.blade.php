@extends('layouts.portal')

@section('content')

<!-- Style Khas Fixed Height Banner -->
<style>
    .custom-carousel-item {
        height: 420px; /* Laraskan ketinggian tetap mengikut kesesuaian (cth: 400px - 500px) */
    }

    /* Responsif untuk skrin mudah alih (telefon) */
    @media (max-width: 768px) {
        .custom-carousel-item {
            height: 320px;
        }
        .custom-carousel-item h1 {
            font-size: 1.8rem;
        }
        .custom-carousel-item p {
            font-size: 1rem;
        }
    }
</style>

<!-- Slide Banner Carousel -->
<div id="corporateCarousel" class="carousel slide shadow-sm" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#corporateCarousel" data-bs-slide-to="0" class="active"></button>
        <button type="button" data-bs-target="#corporateCarousel" data-bs-slide-to="1"></button>
    </div>
    
    <div class="carousel-inner">
        <!-- Item 1: Fixed Height + Vertical Center -->
        <div class="carousel-item active custom-carousel-item text-white text-center p-4" style="background: linear-gradient(135deg, #1e3a8a, #0284c7);">
            <div class="h-100 d-flex flex-column justify-content-center align-items-center">
                <div class="container">
                    <h1 class="display-5 fw-bold mb-3">Selamat Datang ke Portal Pengurusan Surau</h1>
                    <p class="lead mb-4" style="color: #e0f2fe;">Penyelesaian Bersepadu Pengurusan Surau, JAWI & Pentadbir Sistem secara Digital.</p>
                    <a href="{{ route('login') }}" class="btn btn-light btn-lg shadow fw-bold px-4 py-2" style="color: #1e40af; border: 1px solid #bfdbfe;">
                        <i class="fa-solid fa-right-to-bracket me-1" style="color: #1e40af;"></i> Log Masuk Sistem
                    </a>
                </div>
            </div>
        </div>

        <!-- Item 2: Fixed Height + Vertical Center -->
        <div class="carousel-item custom-carousel-item text-white text-center p-4" style="background: linear-gradient(135deg, #0f172a, #1e40af);">
            <div class="h-100 d-flex flex-column justify-content-center align-items-center">
                <div class="container">
                    <h1 class="display-5 fw-bold mb-3">Pengurusan Berteraskan Teknologi Terkini</h1>
                    <p class="lead mb-0" style="color: #e0f2fe;">Keselamatan Data Terjamin dengan Pengesahan Dua Faktor (2FA Google Authenticator).</p>
                </div>
            </div>
        </div>
    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#corporateCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#corporateCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>

<!-- Info Korporat -->
<div class="container py-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold text-dark">Fungsi Asas Sistem</h2>
        <p class="text-muted">Platform yang direka khas untuk keperluan fleksibel pelbagai peranan.</p>
    </div>
    <div class="row g-4 text-center">
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm p-3" style="border-top: 4px solid #0284c7 !important;">
                <div class="card-body">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px; background-color: #f0f9ff;">
                        <i class="fa-solid fa-user-gear fa-2x" style="color: #0284c7;"></i>
                    </div>
                    <h5 class="fw-bold" style="color: #0f172a;">Pentadbir Sistem</h5>
                    <p class="text-muted small">Mengawal akses pengguna, peranan, serta konfigurasi keselamatan asas sistem.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm p-3" style="border-top: 4px solid #1e40af !important;">
                <div class="card-body">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px; background-color: #eff6ff;">
                        <i class="fa-solid fa-building-columns fa-2x" style="color: #1e40af;"></i>
                    </div>
                    <h5 class="fw-bold" style="color: #0f172a;">Pentadbir JAWI</h5>
                    <p class="text-muted small">Memantau direktori surau, kelulusan permohonan, dan rekod secara berpusat.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm p-3" style="border-top: 4px solid #0ea5e9 !important;">
                <div class="card-body">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px; background-color: #f0f9ff;">
                        <i class="fa-solid fa-mosque fa-2x" style="color: #0ea5e9;"></i>
                    </div>
                    <h5 class="fw-bold" style="color: #0f172a;">Pentadbir Surau</h5>
                    <p class="text-muted small">Pengurusan aktiviti harian, maklumat jawatankuasa, dan laporan zon surau.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection