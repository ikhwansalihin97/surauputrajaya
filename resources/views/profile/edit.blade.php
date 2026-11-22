@extends('layouts.app')

@section('content')

<div class="container py-4">

    {{-- =========================================================
        HEADER KORPORAT
    ========================================================== --}}
    <div class="hero-welcome-card p-4 mb-4 shadow-sm">
        <div class="hero-shape-1"></div>
        <div class="hero-shape-2"></div>
        <div class="d-flex align-items-center position-relative">
            <div class="rounded-circle p-3 me-3 text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 52px; height: 52px; background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3);">
                <i class="fa-solid fa-user-pen fa-lg"></i>
            </div>
            <div>
                <div class="badge bg-white bg-opacity-25 text-white mb-1" style="font-size:0.7rem; letter-spacing:0.05em;">
                    <i class="fa-solid fa-id-card me-1"></i> PROFIL PENGGUNA
                </div>
                <h4 class="fw-bold mb-0 text-white">
                    Kemaskini Profil Saya
                </h4>
                <small class="text-white-50">
                    Kemaskini maklumat peribadi, perhubungan dan kata laluan akaun anda.
                </small>
            </div>
        </div>
    </div>


    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4 alert-dismissible fade show">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif


    <div class="row g-4">

        {{-- =========================================================
            MAKLUMAT PROFIL
        ========================================================== --}}
        <div class="col-lg-8">

            <div class="card border-0 shadow-sm rounded-3">

                <div class="card-body p-4">

                    <form action="{{ route('profile.update') }}" method="POST">

                        @csrf
                        @method('PUT')

                        {{-- MAKLUMAT PERIBADI --}}
                        <div class="mb-3">
                            <div class="fw-bold text-primary border-bottom pb-2">
                                <i class="fa-solid fa-user me-1"></i>
                                Maklumat Peribadi
                            </div>
                        </div>

                        <div class="row g-3 mb-4 align-items-end">

                            <div class="col-md-8">
                                <label class="form-label fw-semibold small">
                                    Nama Penuh <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name"
                                       class="form-control form-control-sm @error('name') is-invalid @enderror"
                                       value="{{ old('name', $user->name) }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">No. Kad Pengenalan</label>
                                <input type="text" class="form-control form-control-sm bg-light"
                                       value="{{ $user->no_kad_pengenalan }}" disabled>
                            </div>

                            <div class="col-md-8">
                                <label class="form-label fw-semibold small">E-mel / ID Log Masuk</label>
                                <input type="email" class="form-control form-control-sm bg-light"
                                       value="{{ $user->email }}" disabled>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">
                                    No. Telefon Bimbit <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="no_telefon_bimbit"
                                       class="form-control form-control-sm @error('no_telefon_bimbit') is-invalid @enderror"
                                       value="{{ old('no_telefon_bimbit', $user->no_telefon_bimbit) }}" required>
                                @error('no_telefon_bimbit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">No. Telefon Pejabat</label>
                                <input type="text" name="no_telefon_pejabat"
                                       class="form-control form-control-sm @error('no_telefon_pejabat') is-invalid @enderror"
                                       value="{{ old('no_telefon_pejabat', $user->no_telefon_pejabat) }}">
                                @error('no_telefon_pejabat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-2">
                                <label class="form-label fw-semibold small">Sambungan</label>
                                <input type="text" name="sambungan"
                                       class="form-control form-control-sm @error('sambungan') is-invalid @enderror"
                                       value="{{ old('sambungan', $user->sambungan) }}">
                                @error('sambungan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                        </div>


                        {{-- MAKLUMAT PERKHIDMATAN --}}
                        <div class="mb-3">
                            <div class="fw-bold text-primary border-bottom pb-2">
                                <i class="fa-solid fa-briefcase me-1"></i>
                                Maklumat Perkhidmatan
                            </div>
                        </div>

                        <div class="row g-3 mb-4 align-items-end">

                            <div class="col-md-8">
                                <label class="form-label fw-semibold small">
                                    Jawatan <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="jawatan"
                                       class="form-control form-control-sm @error('jawatan') is-invalid @enderror"
                                       value="{{ old('jawatan', $user->jawatan) }}" required>
                                @error('jawatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Gred</label>
                                <input type="text" name="gred"
                                       class="form-control form-control-sm @error('gred') is-invalid @enderror"
                                       value="{{ old('gred', $user->gred) }}">
                                @error('gred') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-8">
                                <label class="form-label fw-semibold small">Skim Perkhidmatan</label>
                                <input type="text" name="skim_perkhidmatan"
                                       class="form-control form-control-sm @error('skim_perkhidmatan') is-invalid @enderror"
                                       value="{{ old('skim_perkhidmatan', $user->skim_perkhidmatan) }}">
                                @error('skim_perkhidmatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">
                                    Status Perkhidmatan <span class="text-danger">*</span>
                                </label>
                                @php
                                    $statusPerkhidmatan = old('status_perkhidmatan', $user->status_perkhidmatan);
                                @endphp
                                <select name="status_perkhidmatan"
                                        class="form-select form-select-sm @error('status_perkhidmatan') is-invalid @enderror" required>
                                    <option value="">-- Pilih Status --</option>
                                    @foreach(['TETAP' => 'TETAP', 'KONTRAK' => 'KONTRAK', 'SAMBILAN' => 'SAMBILAN', 'LAIN_LAIN' => 'LAIN-LAIN'] as $value => $label)
                                        <option value="{{ $value }}" @selected($statusPerkhidmatan === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('status_perkhidmatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Agensi</label>
                                <input type="text" class="form-control form-control-sm bg-light"
                                       value="{{ $user->agency?->name ?? '-' }}" disabled>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Jabatan / Bahagian</label>
                                <input type="text" class="form-control form-control-sm bg-light"
                                       value="{{ $user->department?->name ?? '-' }}" disabled>
                            </div>

                        </div>

                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <small class="text-muted">
                                <i class="fa-solid fa-circle-info me-1"></i>
                                E-mel, No. KP, agensi dan jabatan hanya boleh dikemaskini oleh pentadbir sistem.
                            </small>
                            <button type="submit" class="btn btn-primary btn-sm px-4">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Perubahan
                            </button>
                        </div>

                    </form>

                </div>

            </div>

        </div>


        {{-- =========================================================
            PERANAN & KATA LALUAN
        ========================================================== --}}
        <div class="col-lg-4">

            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-body p-4">
                    <div class="fw-bold text-primary border-bottom pb-2 mb-3">
                        <i class="fa-solid fa-user-shield me-1"></i>
                        Peranan Sistem
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        @forelse($user->roles as $role)
                            <span class="badge px-2 py-1" style="background-color:#dbeafe; color:#1e40af; border:1px solid #bfdbfe; font-size:0.75rem;">
                                {{ $role->name }}
                            </span>
                        @empty
                            <span class="text-muted small">Tiada peranan ditetapkan.</span>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">

                    <div class="fw-bold text-primary border-bottom pb-2 mb-3">
                        <i class="fa-solid fa-key me-1"></i>
                        Tukar Kata Laluan
                    </div>

                    @if(session('password_success'))
                        <div class="alert alert-success border-0 small py-2 mb-3">
                            <i class="fa-solid fa-circle-check me-1"></i> {{ session('password_success') }}
                        </div>
                    @endif

                    <form action="{{ route('profile.password') }}" method="POST">

                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">
                                Kata Laluan Semasa <span class="text-danger">*</span>
                            </label>
                            <input type="password" name="current_password" autocomplete="current-password"
                                   class="form-control form-control-sm @error('current_password') is-invalid @enderror" required>
                            @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">
                                Kata Laluan Baharu <span class="text-danger">*</span>
                            </label>
                            <input type="password" name="password" autocomplete="new-password" minlength="8"
                                   class="form-control form-control-sm @error('password') is-invalid @enderror" required>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">
                                Sahkan Kata Laluan Baharu <span class="text-danger">*</span>
                            </label>
                            <input type="password" name="password_confirmation" autocomplete="new-password" minlength="8"
                                   class="form-control form-control-sm" required>
                        </div>

                        <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                            <i class="fa-solid fa-lock me-1"></i> Tukar Kata Laluan
                        </button>

                    </form>

                </div>
            </div>

            {{-- ── TANDATANGAN ── --}}
            <div class="card border-0 shadow-sm rounded-3 mt-4" id="tandatangan">
                <div class="card-body p-4">
                    <div class="fw-bold text-primary border-bottom pb-2 mb-3">
                        <i class="fa-solid fa-signature me-1"></i> Tandatangan Surat
                    </div>
                    <p class="small text-muted">Digunakan pada surat yang anda luluskan atau hantar melalui sistem (cth. Surat Pentauliahan AJK).</p>

                    @if(session('tandatangan_success'))
                        <div class="alert alert-success border-0 small py-2 mb-3"><i class="fa-solid fa-circle-check me-1"></i> {{ session('tandatangan_success') }}</div>
                    @endif
                    @if($errors->has('tandatangan_lukisan') || $errors->has('tandatangan_fail'))
                        <div class="alert alert-danger border-0 small py-2 mb-3">{{ $errors->first('tandatangan_lukisan') ?: $errors->first('tandatangan_fail') }}</div>
                    @endif

                    <form action="{{ route('profile.tandatangan') }}" method="POST" enctype="multipart/form-data" id="formTandatangan">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="tandatangan_lukisan" id="ttdLukisan">

                        @if($user->tandatangan_path)
                            <div class="small fw-semibold mb-1">Tandatangan semasa</div>
                            <div class="border rounded-2 p-2 mb-2 text-center bg-white">
                                <img src="{{ route('profile.tandatangan.imej') }}?v={{ md5($user->tandatangan_path) }}" style="max-height:70px; max-width:100%;" alt="Tandatangan">
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="buang_tandatangan" value="1" id="buangTtd">
                                <label class="form-check-label small" for="buangTtd">Buang tandatangan</label>
                            </div>
                        @endif

                        <ul class="nav nav-tabs nav-fill small mb-2" role="tablist">
                            <li class="nav-item"><button type="button" class="nav-link active py-1" data-bs-toggle="tab" data-bs-target="#ttdTabLukis">Lukis</button></li>
                            <li class="nav-item"><button type="button" class="nav-link py-1" data-bs-toggle="tab" data-bs-target="#ttdTabFail">Muat Naik</button></li>
                        </ul>
                        <div class="tab-content mb-3">
                            <div class="tab-pane fade show active" id="ttdTabLukis">
                                <canvas id="ttdKanvas" height="140" class="border rounded-2 w-100 bg-white" style="touch-action:none; cursor:crosshair;"></canvas>
                                <div class="d-flex justify-content-between small mt-1">
                                    <span class="text-muted">Lukis tandatangan anda di atas</span>
                                    <a href="#" id="ttdPadam" class="text-decoration-none">Padam</a>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="ttdTabFail">
                                <input type="file" name="tandatangan_fail" accept="image/png,image/jpeg" class="form-control form-control-sm">
                                <div class="small text-muted mt-1">PNG berlatar belakang lut sinar adalah terbaik. Maks. 2MB.</div>
                            </div>
                        </div>

                        <label class="form-label fw-semibold small">Baris di bawah nama</label>
                        <textarea name="blok_tandatangan" rows="3" class="form-control form-control-sm" placeholder="{{ ($user->jawatan ? $user->jawatan . "\n" : '') . \App\Services\SuratPentauliahanService::BLOK_TANDATANGAN_LALAI }}">{{ old('blok_tandatangan', $user->blok_tandatangan) }}</textarea>
                        <div class="small text-muted mt-1 mb-3">Satu baris bagi setiap baris, cth. jawatan, &ldquo;b.p. Pengarah&rdquo;, jabatan. Kosongkan untuk guna jawatan anda.</div>

                        <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Tandatangan
                        </button>
                    </form>
                </div>
            </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const kanvas = document.getElementById('ttdKanvas');
    if (!kanvas) return;
    const ctx = kanvas.getContext('2d');
    let ada = false, melukis = false;
    const saiz = () => { kanvas.width = kanvas.clientWidth * 2; kanvas.height = 280; ctx.scale(2, 2); ctx.lineWidth = 2.2; ctx.lineCap = 'round'; ctx.lineJoin = 'round'; ctx.strokeStyle = '#0b1f5c'; ada = false; };
    saiz();
    const titik = (e) => { const r = kanvas.getBoundingClientRect(); return [e.clientX - r.left, e.clientY - r.top]; };
    kanvas.addEventListener('pointerdown', (e) => { melukis = true; kanvas.setPointerCapture(e.pointerId); ctx.beginPath(); ctx.moveTo(...titik(e)); });
    kanvas.addEventListener('pointermove', (e) => { if (!melukis) return; ctx.lineTo(...titik(e)); ctx.stroke(); ada = true; });
    ['pointerup', 'pointerleave'].forEach(ev => kanvas.addEventListener(ev, () => { melukis = false; }));
    document.getElementById('ttdPadam').addEventListener('click', (e) => { e.preventDefault(); ctx.clearRect(0, 0, kanvas.width, kanvas.height); ada = false; });
    document.getElementById('formTandatangan').addEventListener('submit', () => {
        const lukisAktif = document.getElementById('ttdTabLukis').classList.contains('active');
        document.getElementById('ttdLukisan').value = (lukisAktif && ada) ? kanvas.toDataURL('image/png') : '';
    });
});
</script>

        </div>

    </div>

</div>

@endsection
