@extends('layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-center" style="min-height: 60vh;">
    <div class="card border-0 shadow-sm rounded-3 text-center p-5" style="max-width:540px;width:100%;border-top:4px solid #d97706 !important;">
        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mx-auto mb-4"
             style="width:72px;height:72px;background-color:#fffbeb;">
            <i class="fa-solid fa-user-lock fa-2x" style="color:#d97706;"></i>
        </div>
        <h5 class="fw-bold text-dark mb-2">Tiada Peranan Aktif Diperuntukkan</h5>
        <p class="text-muted mb-4" style="font-size:0.875rem;line-height:1.7;">
            Akaun anda belum ditetapkan dengan peranan pentadbir yang sah.
            Sila hubungi <strong>Pentadbir Sistem</strong> untuk pengesahan &amp; peruntukan akses.
        </p>
        <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
            <a href="{{ route('portal') }}"
               class="btn fw-semibold px-4 py-2 rounded-2"
               style="background-color:#f1f5f9;color:#334155;border:1px solid #e2e8f0;font-size:0.875rem;">
                <i class="fa-solid fa-arrow-left me-2"></i>Kembali ke Portal Awam
            </a>
        </div>
        <div class="mt-4 pt-3" style="border-top:1px solid #f1f5f9;">
            <div style="font-size:0.75rem;color:#94a3b8;">
                <i class="fa-solid fa-circle-info me-1"></i>
                Untuk bantuan, hubungi unit IT JAWI atau emel ke <strong>support@jawi.gov.my</strong>
            </div>
        </div>
    </div>
</div>
@endsection