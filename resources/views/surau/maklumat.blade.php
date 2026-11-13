@extends('layouts.app')

@section('content')

@php
    $val = fn ($field) => old($field, $surau->{$field});
    $sel = fn ($field, $option) => (string) $val($field) === (string) $option ? 'selected' : '';
    $sumberList = old('sumber_kewangan', $surau->sumber_kewangan ?: ['']);
    $sumberList = count($sumberList) ? $sumberList : [''];
@endphp

<div class="container py-4">

    @include('surau._pemilih')

    {{-- HEADER --}}
    <div class="hero-welcome-card p-4 mb-4 shadow-sm">
        <div class="hero-shape-1"></div>
        <div class="hero-shape-2"></div>
        <div class="d-flex align-items-center position-relative">
            <div class="rounded-circle p-3 me-3 text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 52px; height: 52px; background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3);">
                <i class="fa-solid fa-kaaba fa-lg"></i>
            </div>
            <div>
                <div class="badge bg-white bg-opacity-25 text-white mb-1" style="font-size:0.7rem; letter-spacing:0.05em;">
                    <i class="fa-solid fa-mosque me-1"></i> MAKLUMAT SURAU
                </div>
                <h4 class="fw-bold mb-0 text-white">{{ $surau->nama_surau }}</h4>
                <small class="text-white-50">
                    Perubahan disimpan terus sebagai rekod rasmi surau dan direkodkan dalam log aktiviti.
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

    <form action="{{ route('surau.maklumat.update') }}" method="POST" id="formMaklumatSurau" novalidate>
        @csrf
        @method('PUT')

        {{-- 1. MAKLUMAT AM --}}
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3 fw-bold" style="border-bottom:2px solid #eff6ff;">
                <i class="fa-solid fa-circle-info me-2 text-primary"></i>Maklumat Am Surau
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Nama Surau</label>
                        <input type="text" name="nama_surau" class="form-control" value="{{ $val('nama_surau') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Kategori Surau</label>
                        <select name="kategori_surau" class="form-select" required>
                            <option value="">-- Sila Pilih --</option>
                            @foreach(['Surau Jumaat', 'Surau Komuniti'] as $opt)
                                <option value="{{ $opt }}" {{ $sel('kategori_surau', $opt) }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Kariah Masjid</label>
                        <select name="masjid_id" class="form-select" required>
                            <option value="">-- Sila Pilih Masjid --</option>
                            @foreach($masjids as $masjid)
                                <option value="{{ $masjid->id }}" {{ $sel('masjid_id', $masjid->id) }}>{{ $masjid->nama_masjid }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">No. Telefon Surau (Jika ada)</label>
                        <input type="text" name="no_tel_surau" inputmode="numeric" pattern="[0-9]*" data-digits-only class="form-control" value="{{ $val('no_tel_surau') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Alamat Surau</label>
                        <textarea name="alamat_surau" class="form-control" rows="3" required>{{ $val('alamat_surau') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Alamat Surat Menyurat</label>
                        <textarea name="alamat_surat" class="form-control" rows="3" required>{{ $val('alamat_surat') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. TAPAK & BANGUNAN --}}
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3 fw-bold" style="border-bottom:2px solid #eff6ff;">
                <i class="fa-solid fa-building me-2 text-primary"></i>Maklumat Tapak &amp; Bangunan
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Kawasan Tapak</label>
                        <select name="kawasan_surau" id="kawasan_surau" class="form-select" required>
                            <option value="">-- Sila Pilih --</option>
                            @foreach(['Taman Perumahan' => 'Taman Perumahan', 'Perkampungan Tetap' => 'Perkampungan Tetap', 'Setinggan' => 'Setinggan', 'Rumah Pangsa/Flat' => 'Rumah Pangsa / Flat', 'Lain-lain' => 'Lain-lain'] as $opt => $label)
                                <option value="{{ $opt }}" {{ $sel('kawasan_surau', $opt) }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-8 {{ $val('kawasan_surau') === 'Lain-lain' ? '' : 'd-none' }}" id="divKawasanLain">
                        <label class="form-label fw-semibold small text-danger">Info Tapak (Nyatakan Butiran Tempat)</label>
                        <input type="text" name="kawasan_lain" class="form-control" value="{{ $val('kawasan_lain') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Jarak Surau Berdekatan (KM)</label>
                        <input type="number" step="0.1" min="0" name="jarak_surau_berdekatan" class="form-control" value="{{ $val('jarak_surau_berdekatan') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Nama Surau Berdekatan</label>
                        <input type="text" name="nama_surau_berdekatan" class="form-control" value="{{ $val('nama_surau_berdekatan') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Taraf Tapak Bangunan</label>
                        <select name="taraf_tapak" class="form-select" required>
                            <option value="">-- Sila Pilih --</option>
                            @foreach(['Diwakafkan', 'Dikhaskan', 'Kebenaran'] as $opt)
                                <option value="{{ $opt }}" {{ $sel('taraf_tapak', $opt) }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">No Lot / Geran Tanah</label>
                        <input type="text" name="no_lot_geran" class="form-control" value="{{ $val('no_lot_geran') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Jenis Bangunan</label>
                        <select name="jenis_bangunan" class="form-select" required>
                            <option value="">-- Sila Pilih --</option>
                            @foreach(['Batu', 'Kayu', 'Campuran'] as $opt)
                                <option value="{{ $opt }}" {{ $sel('jenis_bangunan', $opt) }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Tingkat Bangunan</label>
                        <select name="tingkat_bangunan" class="form-select" required>
                            <option value="">-- Sila Pilih --</option>
                            @foreach(['Setingkat', 'Dua Tingkat'] as $opt)
                                <option value="{{ $opt }}" {{ $sel('tingkat_bangunan', $opt) }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Tahun Didirikan</label>
                        <input type="number" name="tahun_didirikan" min="1800" max="{{ date('Y') }}" class="form-control" value="{{ $val('tahun_didirikan') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Perbelanjaan Pembinaan (RM)</label>
                        <input type="number" step="0.01" min="0" name="jumlah_perbelanjaan" class="form-control" value="{{ $val('jumlah_perbelanjaan') }}" required>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. BANK & KEWANGAN --}}
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3 fw-bold" style="border-bottom:2px solid #eff6ff;">
                <i class="fa-solid fa-building-columns me-2 text-primary"></i>Maklumat Bank &amp; Sumber Kewangan
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Nama Bank</label>
                        <input type="text" name="nama_bank" class="form-control" value="{{ $val('nama_bank') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">No Akaun Bank</label>
                        <input type="text" name="no_akaun_bank" inputmode="numeric" pattern="[0-9]*" data-digits-only maxlength="30" class="form-control" value="{{ $val('no_akaun_bank') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Alamat Cawangan Bank</label>
                        <textarea name="alamat_bank" class="form-control" rows="2" required>{{ $val('alamat_bank') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Penama Akaun Bank</label>
                        <textarea name="penama_akaun" class="form-control" rows="2" required>{{ $val('penama_akaun') }}</textarea>
                    </div>

                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-2 mt-2">
                            <label class="form-label fw-semibold small mb-0">Sumber Kewangan Surau</label>
                            <button type="button" class="btn btn-sm btn-outline-primary fw-bold" id="btnTambahSumber">
                                <i class="fa-solid fa-plus me-1"></i> Tambah
                            </button>
                        </div>
                        <div id="senaraiSumber">
                            @foreach($sumberList as $sumber)
                                <div class="input-group input-group-sm mb-2 sumber-row">
                                    <input type="text" name="sumber_kewangan[]" class="form-control" value="{{ $sumber }}" placeholder="Cth: Derma Jumaat / Yuran Ahli / Sumbangan Korporat">
                                    <button type="button" class="btn btn-outline-danger btn-buang-sumber" title="Buang"><i class="fa-solid fa-trash"></i></button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. PENDUDUK & AKTIVITI --}}
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3 fw-bold" style="border-bottom:2px solid #eff6ff;">
                <i class="fa-solid fa-people-group me-2 text-primary"></i>Penduduk &amp; Aktiviti Surau
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Jumlah Penduduk Islam Lelaki</label>
                        <input type="number" min="0" name="penduduk_lelaki" class="form-control" value="{{ $val('penduduk_lelaki') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Jumlah Penduduk Islam Perempuan</label>
                        <input type="number" min="0" name="penduduk_perempuan" class="form-control" value="{{ $val('penduduk_perempuan') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Kelas Pengajian (Kekerapan Sebulan)</label>
                        <input type="number" min="0" name="kelas_pengajian_sebulan" class="form-control" value="{{ $val('kelas_pengajian_sebulan') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Ceramah / Syarahan Umum (Kekerapan Sebulan)</label>
                        <input type="number" min="0" name="ceramah_sebulan" class="form-control" value="{{ $val('ceramah_sebulan') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Kelas Didikan Kanak-Kanak (Kekerapan Sebulan)</label>
                        <input type="number" min="0" name="kelas_kanak_sebulan" class="form-control" value="{{ $val('kelas_kanak_sebulan') }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex flex-column flex-sm-row justify-content-end gap-2 mb-4">
            <a href="{{ route('dashboard') }}" class="btn btn-light border fw-semibold px-4">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali
            </a>
            <button type="submit" class="btn btn-primary fw-bold px-4">
                <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Maklumat Surau
            </button>
        </div>
    </form>

    <div class="small text-muted mb-4">
        <i class="fa-solid fa-circle-info me-1"></i>
        Perubahan Ahli Jawatankuasa (AJK) atau Pentadbir Surau masih perlu dibuat melalui <strong>Kemaskini Maklumat</strong> di papan pemuka dan diluluskan oleh Pentadbir Masjid &amp; JAWI.
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formMaklumatSurau');

    // Tanda medan yang ditolak oleh pelayan
    @json($errors->keys()).forEach(key => {
        const name = key.includes('.') ? key.split('.')[0] + '[]' : key;
        form.querySelectorAll(`[name="${name}"]`).forEach(el => el.classList.add('is-invalid'));
    });

    // Bersihkan nilai lama yang bukan nombor (cth. data ujian)
    form.querySelectorAll('[data-digits-only]').forEach(el => { el.value = el.value.replace(/\D+/g, ''); });

    // Medan nombor sahaja
    form.addEventListener('input', (e) => {
        if (e.target.matches('[data-digits-only]')) {
            e.target.value = e.target.value.replace(/\D+/g, '');
        }
    });

    // Kawasan Lain-lain
    const kawasan = document.getElementById('kawasan_surau');
    const divLain = document.getElementById('divKawasanLain');
    kawasan.addEventListener('change', () => divLain.classList.toggle('d-none', kawasan.value !== 'Lain-lain'));

    // Sumber kewangan dinamik
    const senarai = document.getElementById('senaraiSumber');
    document.getElementById('btnTambahSumber').addEventListener('click', () => {
        const row = senarai.querySelector('.sumber-row').cloneNode(true);
        row.querySelector('input').value = '';
        senarai.appendChild(row);
        row.querySelector('input').focus();
    });
    senarai.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-buang-sumber');
        if (!btn) return;
        const rows = senarai.querySelectorAll('.sumber-row');
        if (rows.length > 1) {
            btn.closest('.sumber-row').remove();
        } else {
            rows[0].querySelector('input').value = '';
        }
    });
});
</script>

@include('surau._validasi-borang', ['formId' => 'formMaklumatSurau'])

@endsection
