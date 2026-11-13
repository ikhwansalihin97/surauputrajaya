@extends('layouts.app')

@section('content')

@php
    $jenis = old('pentadbir_jenis', 'SEDIA_ADA');
@endphp

<div class="container py-4">

    <div class="hero-welcome-card p-4 mb-4 shadow-sm">
        <div class="hero-shape-1"></div>
        <div class="hero-shape-2"></div>
        <div class="d-flex align-items-center position-relative">
            <div class="rounded-circle p-3 me-3 text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 52px; height: 52px; background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3);">
                <i class="fa-solid fa-plus fa-lg"></i>
            </div>
            <div>
                <div class="badge bg-white bg-opacity-25 text-white mb-1" style="font-size:0.7rem; letter-spacing:0.05em;">
                    <i class="fa-solid fa-building-columns me-1"></i> MODUL JAWI
                </div>
                <h4 class="fw-bold mb-0 text-white">Daftar Surau Sedia Ada</h4>
                <small class="text-white-50">Isi maklumat asas sahaja. Pentadbir Surau akan melengkapkan maklumat surau dan AJK.</small>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4">
            <div class="fw-bold mb-1"><i class="fa-solid fa-triangle-exclamation me-2"></i>Sila betulkan ralat berikut:</div>
            <ul class="mb-0 small ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.jawi.surau.store') }}" method="POST" id="formDaftarSurau" novalidate>
        @csrf

        {{-- 1. MAKLUMAT SURAU --}}
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3 fw-bold" style="border-bottom:2px solid #eff6ff;">
                <i class="fa-solid fa-kaaba me-2 text-primary"></i>1. Maklumat Surau
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Nama Surau <span class="text-danger">*</span></label>
                        <input type="text" name="nama_surau" class="form-control @error('nama_surau') is-invalid @enderror" value="{{ old('nama_surau') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">No. Rujukan JAWI <span class="text-danger">*</span></label>
                        <input type="text" name="no_rujukan_jawi" class="form-control font-monospace @error('no_rujukan_jawi') is-invalid @enderror" value="{{ old('no_rujukan_jawi') }}" placeholder="Cth: JAWI/SRU/2015/0123" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Kategori Surau <span class="text-danger">*</span></label>
                        <select name="kategori_surau" class="form-select @error('kategori_surau') is-invalid @enderror" required>
                            <option value="">-- Sila Pilih --</option>
                            @foreach(['Surau Jumaat', 'Surau Komuniti'] as $opt)
                                <option value="{{ $opt }}" {{ old('kategori_surau') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Dalam Kariah Masjid <span class="text-danger">*</span></label>
                        <select name="masjid_id" class="form-select @error('masjid_id') is-invalid @enderror" required>
                            <option value="">-- Sila Pilih Masjid --</option>
                            @foreach($masjids as $masjid)
                                <option value="{{ $masjid->id }}" {{ (string) old('masjid_id') === (string) $masjid->id ? 'selected' : '' }}>{{ $masjid->nama_masjid }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. PENTADBIR SURAU --}}
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3 fw-bold" style="border-bottom:2px solid #eff6ff;">
                <i class="fa-solid fa-user-shield me-2 text-primary"></i>2. Pentadbir Surau
            </div>
            <div class="card-body p-4">
                <p class="small text-muted mb-3">
                    Pentadbir Surau akan log masuk untuk melengkapkan maklumat surau dan senarai Ahli Jawatankuasa (AJK).
                    Seorang pengguna boleh mentadbir lebih daripada satu surau.
                </p>

                <div class="btn-group w-100 mb-4" role="group">
                    <input type="radio" class="btn-check" name="pentadbir_jenis" id="jenisSediaAda" value="SEDIA_ADA" {{ $jenis === 'SEDIA_ADA' ? 'checked' : '' }}>
                    <label class="btn btn-outline-primary fw-semibold" for="jenisSediaAda">
                        <i class="fa-solid fa-user-check me-1"></i> Pengguna Sedia Ada
                    </label>
                    <input type="radio" class="btn-check" name="pentadbir_jenis" id="jenisBaharu" value="BAHARU" {{ $jenis === 'BAHARU' ? 'checked' : '' }}>
                    <label class="btn btn-outline-primary fw-semibold" for="jenisBaharu">
                        <i class="fa-solid fa-user-plus me-1"></i> Pengguna Baharu
                    </label>
                </div>

                {{-- Pengguna sedia ada --}}
                <div id="panelSediaAda" class="{{ $jenis === 'SEDIA_ADA' ? '' : 'd-none' }}">
                    <label class="form-label fw-semibold small">Cari Pengguna</label>
                    <input type="text" id="cariPengguna" class="form-control mb-2" placeholder="Taip nama, No. K/P atau e-mel untuk menapis senarai...">
                    <select name="pentadbir_user_id" id="selectPengguna" class="form-select @error('pentadbir_user_id') is-invalid @enderror" size="8">
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ (string) old('pentadbir_user_id') === (string) $u->id ? 'selected' : '' }}>
                                {{ $u->name }} — {{ $u->no_kad_pengenalan ?: 'Tiada K/P' }} — {{ $u->email }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text">Peranan Pentadbir Surau akan ditambah kepada pengguna ini (peranan lain dikekalkan).</div>
                </div>

                {{-- Pengguna baharu --}}
                <div id="panelBaharu" class="{{ $jenis === 'BAHARU' ? '' : 'd-none' }}">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Nama Penuh <span class="text-danger">*</span></label>
                            <input type="text" name="pentadbir_nama" class="form-control @error('pentadbir_nama') is-invalid @enderror" value="{{ old('pentadbir_nama') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">No. Kad Pengenalan <span class="text-danger">*</span></label>
                            <input type="text" name="pentadbir_ic" inputmode="numeric" pattern="[0-9]*" maxlength="12" data-digits-only class="form-control @error('pentadbir_ic') is-invalid @enderror" value="{{ old('pentadbir_ic') }}" placeholder="12 digit tanpa '-'">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">E-mel (ID Log Masuk) <span class="text-danger">*</span></label>
                            <input type="email" name="pentadbir_email" class="form-control @error('pentadbir_email') is-invalid @enderror" value="{{ old('pentadbir_email') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">No. Telefon Bimbit <span class="text-danger">*</span></label>
                            <input type="text" name="pentadbir_tel" inputmode="numeric" pattern="[0-9]*" data-digits-only class="form-control @error('pentadbir_tel') is-invalid @enderror" value="{{ old('pentadbir_tel') }}">
                        </div>
                    </div>
                    <div class="form-text mt-2">
                        <i class="fa-solid fa-envelope me-1"></i> Pautan pengaktifan akaun akan dihantar ke e-mel ini.
                    </div>
                </div>

                {{-- Jawatan pentadbir dalam AJK (bagi kedua-dua jenis pengguna) --}}
                <div class="row g-3 mt-1">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Jawatan dalam AJK <span class="text-danger">*</span></label>
                        <select name="pentadbir_jawatan" class="form-select @error('pentadbir_jawatan') is-invalid @enderror" required>
                            <option value="">-- Sila Pilih --</option>
                            @foreach(\App\Models\SurauCommittee::JAWATAN as $opt)
                                <option value="{{ $opt }}" {{ old('pentadbir_jawatan') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Maklumat pentadbir akan dimasukkan terus ke dalam senarai AJK surau di bawah jawatan ini.</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex flex-column flex-sm-row justify-content-end gap-2 mb-4">
            <a href="{{ route('admin.jawi.surau.index') }}" class="btn btn-light border fw-semibold px-4">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali
            </a>
            <button type="submit" class="btn btn-primary fw-bold px-4">
                <i class="fa-solid fa-floppy-disk me-1"></i> Daftar Surau
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formDaftarSurau');
    const panelSediaAda = document.getElementById('panelSediaAda');
    const panelBaharu = document.getElementById('panelBaharu');

    form.querySelectorAll('input[name="pentadbir_jenis"]').forEach(radio => {
        radio.addEventListener('change', () => {
            const baharu = radio.value === 'BAHARU' && radio.checked;
            panelBaharu.classList.toggle('d-none', !baharu);
            panelSediaAda.classList.toggle('d-none', baharu);
        });
    });

    // Tapis senarai pengguna
    const cari = document.getElementById('cariPengguna');
    const select = document.getElementById('selectPengguna');
    cari.addEventListener('input', () => {
        const q = cari.value.trim().toLowerCase();
        [...select.options].forEach(opt => {
            opt.hidden = q !== '' && !opt.textContent.toLowerCase().includes(q);
        });
    });

    // Medan nombor sahaja
    form.addEventListener('input', (e) => {
        if (e.target.matches('[data-digits-only]')) {
            e.target.value = e.target.value.replace(/\D+/g, '');
        }
    });
});
</script>

@endsection
