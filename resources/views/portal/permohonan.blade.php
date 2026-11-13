@extends('layouts.portal')

@section('content')

@php
    // Borang yang sama digunakan untuk kemaskini maklumat surau (draf KEMASKINI)
    $isRevision = isset($revision) && $revision;
    $revisionDocs = $isRevision ? $revision->documents->keyBy('jenis_dokumen') : collect();
    $revisionMinit = $isRevision ? $revision->meetingMinutes->sortByDesc('id')->first() : null;
@endphp

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            <div class="card shadow-sm border-0 rounded-3">
                
                <!-- Card Header -->
                <div class="card-header text-white p-4 text-center position-relative" style="background: linear-gradient(135deg, #1e40af 0%, #0284c7 100%); border-radius: 8px 8px 0 0;">
                    @if($isRevision)
                    <h4 class="fw-bold mb-1"><i class="fa-solid fa-pen-to-square text-white me-2"></i> KEMASKINI MAKLUMAT SURAU</h4>
                    <p class="text-white-50 small mb-2">Jabatan Agama Islam Wilayah Persekutuan (JAWI)</p>

                    <div class="d-inline-flex align-items-center flex-wrap justify-content-center gap-2 mt-2 p-2 rounded" style="background-color: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.25);">
                        <span class="badge bg-warning text-dark py-2 px-3"><i class="fa-solid fa-file-pen me-1"></i> DRAF</span>
                        <span class="badge bg-white text-primary font-monospace py-2 px-3">{{ $revision->reference_no }}</span>
                        <span class="small text-white">Dikemaskini: {{ $revision->updated_at?->format('d/m/Y h:i A') }}</span>
                    </div>
                    @else
                    <h4 class="fw-bold mb-1"><i class="fa-solid fa-file-signature text-white me-2"></i> BORANG PENDAFTARAN SURAU</h4>
                    <p class="text-white-50 small mb-2">Jabatan Agama Islam Wilayah Persekutuan (JAWI)</p>
                    @endif

                    @unless($isRevision)
                    <!-- Panel Kawalan Draf Mudah -->
                    <div class="d-flex align-items-center justify-content-center gap-2 mt-3 p-2 rounded" style="background-color: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.25);">
                        <span id="draftStatusBadge" class="badge bg-white text-dark font-monospace py-2 px-3">
                            <i class="fa-solid fa-floppy-disk me-1 text-primary"></i> Menyemak draf tersimpan...
                        </span>
                        
                        <button type="button" id="btnRestoreDraft" class="btn btn-sm btn-light text-primary fw-bold py-1 px-3 d-none">
                            <i class="fa-solid fa-rotate-left me-1"></i> Isikan Semula Data Terakhir Anda
                        </button>
                        
                        <button type="button" id="btnClearDraft" class="btn btn-sm btn-outline-light py-1 px-2 small d-none" title="Padam draf jika mahu mula dari kosong">
                            Padam Draf
                        </button>
                    </div>
                    @endunless
                </div>

                <div class="card-body p-4">
                    @if($isRevision)
                        <div class="alert alert-info d-flex align-items-start gap-2 mb-4 small">
                            <i class="fa-solid fa-circle-info mt-1"></i>
                            <div>
                                Maklumat di bawah diambil daripada rekod rasmi surau yang telah diluluskan
                                @if($revision->parent)(No. Rujukan asal: <strong class="font-monospace">{{ $revision->parent->reference_no }}</strong>)@endif.
                                Anda boleh <strong>simpan draf</strong> pada bila-bila masa. Rekod rasmi <strong>tidak akan berubah</strong> sehingga kemaskini ini
                                <strong>dihantar dan diluluskan</strong> melalui proses pengesahan Pentadbir Masjid, JAWI dan Pentadbir Zon.
                            </div>
                        </div>
                    @endif


                    @if ($errors->any())
                        <div class="alert alert-danger mb-4" id="serverValidationAlert">
                            <h6 class="fw-bold mb-1"><i class="fa-solid fa-triangle-exclamation me-1"></i> Sila Betulkan Ralat Berikut:</h6>
                            <ul class="mb-0 small ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ $isRevision ? route('surau.kemaskini.update', $revision->id) : route('permohonan.store') }}" method="POST" enctype="multipart/form-data" id="formPermohonanSurau" novalidate>
                        @csrf
                        
                        <div class="accordion" id="accordionBorangSurau">

                            <!-- SEKSYEN 1: PROFIL SURAU -->
                            <div class="accordion-item border mb-3 rounded-2">
                                <h2 class="accordion-header">
                                    <button class="accordion-button fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#sec1">
                                        1. Maklumat Surau
                                    </button>
                                </h2>
                                <div id="sec1" class="accordion-collapse collapse show" data-bs-parent="#accordionBorangSurau">
                                    <div class="accordion-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">Nama Surau</label>
                                                <input type="text" name="nama_surau" class="form-control" value="{{ old('nama_surau') }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">Kategori Surau</label>
                                                <select name="kategori_surau" class="form-select" required>
                                                    <option value="Surau Jumaat" {{ old('kategori_surau') == 'Surau Jumaat' ? 'selected' : '' }}>Surau Jumaat</option>
                                                    <option value="Surau Komuniti" {{ old('kategori_surau') == 'Surau Komuniti' ? 'selected' : '' }}>Surau Komuniti</option>
                                                </select>
                                            </div>
                                           <div class="col-md-6">
                                                <label for="masjid_id" class="form-label fw-semibold small">
                                                    Dalam Kariah Masjid <span class="text-danger">*</span>
                                                </label>

                                                @php
                                                    $masjids = \App\Models\Masjid::query()
                                                        ->where('is_active', true)
                                                        ->whereHas('users', function ($query) {

                                                            // Mesti mempunyai peranan Pentadbir Masjid
                                                            $query->whereHas('roles', function ($roleQuery) {
                                                                $roleQuery->where('slug', 'masjid');
                                                            })

                                                            // Pentadbir Sistem dikecualikan
                                                            ->whereDoesntHave('roles', function ($roleQuery) {
                                                                $roleQuery->where('slug', 'admin');
                                                            })

                                                            // Pentadbir Masjid mesti aktif
                                                            ->where('is_active', true);

                                                        })
                                                        ->orderBy('nama_masjid')
                                                        ->get();
                                                @endphp

                                                <select
                                                    name="masjid_id"
                                                    id="masjid_id"
                                                    class="form-select"
                                                    required
                                                >
                                                    <option value="">-- Sila Pilih Masjid --</option>

                                                    @foreach($masjids as $masjid)
                                                        <option
                                                            value="{{ $masjid->id }}"
                                                            {{ old('masjid_id') == $masjid->id ? 'selected' : '' }}
                                                        >
                                                            {{ $masjid->nama_masjid }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">No. Telefon Surau (Jika ada)</label>
                                                <input type="text" name="no_tel_surau" inputmode="numeric" pattern="[0-9]*" data-digits-only class="form-control" value="{{ old('no_tel_surau') }}">
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">Alamat Surau</label>
                                                <textarea name="alamat_surau" id="alamat_surau" class="form-control" rows="3" required>{{ old('alamat_surau') }}</textarea>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <label class="form-label fw-semibold small mb-0">Alamat Surat Menyurat</label>
                                                    <div class="form-check small">
                                                        <input class="form-check-input" type="checkbox" id="copyAlamatCheck">
                                                        <label class="form-check-input-label text-primary fw-bold cursor-pointer" for="copyAlamatCheck" style="font-size: 0.78rem;">
                                                            Sama seperti alamat surau
                                                        </label>
                                                    </div>
                                                </div>
                                                <textarea name="alamat_surat" id="alamat_surat" class="form-control" rows="3" required>{{ old('alamat_surat') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- SEKSYEN 2: TAPAK & BANGUNAN -->
                            <div class="accordion-item border mb-3 rounded-2">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#sec2">
                                        2. Maklumat Tapak & Struktur Bangunan
                                    </button>
                                </h2>
                                <div id="sec2" class="accordion-collapse collapse" data-bs-parent="#accordionBorangSurau">
                                    <div class="accordion-body">
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold small">Kawasan Tapak</label>
                                                <select name="kawasan_surau" id="kawasan_surau" class="form-select" required>
                                                    <option value="Taman Perumahan" {{ old('kawasan_surau') == 'Taman Perumahan' ? 'selected' : '' }}>Taman Perumahan</option>
                                                    <option value="Perkampungan Tetap" {{ old('kawasan_surau') == 'Perkampungan Tetap' ? 'selected' : '' }}>Perkampungan Tetap</option>
                                                    <option value="Setinggan" {{ old('kawasan_surau') == 'Setinggan' ? 'selected' : '' }}>Setinggan</option>
                                                    <option value="Rumah Pangsa/Flat" {{ old('kawasan_surau') == 'Rumah Pangsa/Flat' ? 'selected' : '' }}>Rumah Pangsa / Flat</option>
                                                    <option value="Lain-lain" {{ old('kawasan_surau') == 'Lain-lain' ? 'selected' : '' }}>Lain-lain</option>
                                                </select>
                                            </div>
                                            <div class="col-md-8 {{ old('kawasan_surau') == 'Lain-lain' ? '' : 'd-none' }}" id="divKawasanLain">
                                                <label class="form-label fw-semibold small text-danger">Info Tapak (Nyatakan Butiran Tempat)</label>
                                                <input type="text" name="kawasan_lain" id="kawasan_lain" class="form-control" value="{{ old('kawasan_lain') }}" placeholder="Sila nyatakan lokasi terperinci">
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">Jarak Surau Berdekatan (KM)</label>
                                                <input type="number" step="0.1" name="jarak_surau_berdekatan" class="form-control" value="{{ old('jarak_surau_berdekatan') }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">Nama Surau Berdekatan</label>
                                                <input type="text" name="nama_surau_berdekatan" class="form-control" value="{{ old('nama_surau_berdekatan') }}" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold small">Taraf Tapak Bangunan</label>
                                                <select name="taraf_tapak" class="form-select" required>
                                                    <option value="Diwakafkan" {{ old('taraf_tapak') == 'Diwakafkan' ? 'selected' : '' }}>Diwakafkan</option>
                                                    <option value="Dikhaskan" {{ old('taraf_tapak') == 'Dikhaskan' ? 'selected' : '' }}>Dikhaskan</option>
                                                    <option value="Kebenaran" {{ old('taraf_tapak') == 'Kebenaran' ? 'selected' : '' }}>Kebenaran</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold small">No Lot / Geran Tanah</label>
                                                <input type="text" name="no_lot_geran" class="form-control" value="{{ old('no_lot_geran') }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold small">Jenis Bangunan</label>
                                                <select name="jenis_bangunan" class="form-select" required>
                                                    <option value="Batu" {{ old('jenis_bangunan') == 'Batu' ? 'selected' : '' }}>Batu</option>
                                                    <option value="Kayu" {{ old('jenis_bangunan') == 'Kayu' ? 'selected' : '' }}>Kayu</option>
                                                    <option value="Campuran" {{ old('jenis_bangunan') == 'Campuran' ? 'selected' : '' }}>Campuran</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold small">Tingkat Bangunan</label>
                                                <select name="tingkat_bangunan" class="form-select" required>
                                                    <option value="Setingkat" {{ old('tingkat_bangunan') == 'Setingkat' ? 'selected' : '' }}>Setingkat</option>
                                                    <option value="Dua Tingkat" {{ old('tingkat_bangunan') == 'Dua Tingkat' ? 'selected' : '' }}>Dua Tingkat</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold small">Tahun Didirikan</label>
                                                <input type="number" name="tahun_didirikan" class="form-control" value="{{ old('tahun_didirikan') }}" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold small">Perbelanjaan Pembinaan (RM)</label>
                                                <input type="number" step="0.01" name="jumlah_perbelanjaan" class="form-control" value="{{ old('jumlah_perbelanjaan') }}" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- SEKSYEN 3: MAKLUMAT BANK & SUMBER KEWANGAN -->
                            <div class="accordion-item border mb-3 rounded-2">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#sec3">
                                        3. Maklumat Bank & Sumber Kewangan
                                    </button>
                                </h2>
                                <div id="sec3" class="accordion-collapse collapse" data-bs-parent="#accordionBorangSurau">
                                    <div class="accordion-body">
                                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                                            <i class="fa-solid fa-building-columns me-1 text-primary"></i> Maklumat Bank
                                        </h6>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">Nama Bank</label>
                                                <input type="text" name="nama_bank" class="form-control" value="{{ old('nama_bank') }}" placeholder="Cth: Bank Islam Malaysia Berhad" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">No Akaun Bank</label>
                                                <input type="text" name="no_akaun_bank" inputmode="numeric" pattern="[0-9]*" data-digits-only class="form-control" value="{{ old('no_akaun_bank') }}" placeholder="Cth: 14011010000000" maxlength="14" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">Alamat Cawangan Bank</label>
                                                <textarea name="alamat_bank" class="form-control" rows="2" required>{{ old('alamat_bank') }}</textarea>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">Penama Akaun Bank</label>
                                                <textarea name="penama_akaun" class="form-control" rows="2" placeholder="Nama jawatankuasa/penama rasmi akaun bank" required>{{ old('penama_akaun') }}</textarea>
                                            </div>

                                            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3 mt-4">
                                                <i class="fa-solid fa-hand-holding-dollar me-1 text-primary"></i> Maklumat Sumber Kewangan
                                            </h6>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="fw-bold text-dark mb-0"> Sumber Kewangan Surau</h6>
                                                <button type="button" class="btn btn-sm btn-outline-primary fw-bold" id="btnTambahSumber">
                                                    <i class="fa-solid fa-plus me-1"></i> Tambah Sumber Kewangan
                                                </button>
                                            </div>

                                            <div id="containerSumberKewangan">
                                                @php $oldSumber = old('sumber_kewangan', ['']); @endphp
                                                @foreach($oldSumber as $index => $sumberVal)
                                                    <div class="row g-2 mb-2 sumber-row">
                                                        <div class="col-10">
                                                            <input type="text" name="sumber_kewangan[]" class="form-control form-control-sm" value="{{ $sumberVal }}" placeholder="Cth: Derma Jumaat / Yuran Ahli / Sumbangan Korporat" required>
                                                        </div>
                                                        <div class="col-2">
                                                            <button type="button" class="btn btn-sm btn-outline-danger w-100 btn-padam-sumber" {{ count($oldSumber) == 1 ? 'disabled' : '' }}>
                                                                <i class="fa-solid fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- SEKSYEN 4: MAKLUMAT PENDUDUK & AKTIVITI -->
                            <div class="accordion-item border mb-3 rounded-2">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#sec4">
                                        4. Maklumat Penduduk & Aktiviti Surau
                                    </button>
                                </h2>
                                <div id="sec4" class="accordion-collapse collapse" data-bs-parent="#accordionBorangSurau">
                                    <div class="accordion-body">
                                        <div class="row g-3 mb-4">
                                            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                                                <i class="fa-solid fa-people-group me-1 text-primary"></i> Statistik Penduduk Islam
                                            </h6>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">Jumlah Penduduk Islam Lelaki</label>
                                                <input type="number" name="penduduk_lelaki" class="form-control" value="{{ old('penduduk_lelaki') }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">Jumlah Penduduk Islam Perempuan</label>
                                                <input type="number" name="penduduk_perempuan" class="form-control" value="{{ old('penduduk_perempuan') }}" required>
                                            </div>
                                            
                                            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3 mt-4">
                                                <i class="fa-solid fa-book-quran me-1 text-primary"></i> Kekerapan Aktiviti & Pengajian (Sebulan)
                                            </h6>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold small">Kelas Pengajian (Kekerapan Sebulan)</label>
                                                <input type="number" name="kelas_pengajian_sebulan" class="form-control" value="{{ old('kelas_pengajian_sebulan') }}" placeholder="Bilangan kali sebulan" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold small">Ceramah / Syarahan Umum (Kekerapan Sebulan)</label>
                                                <input type="number" name="ceramah_sebulan" class="form-control" value="{{ old('ceramah_sebulan') }}" placeholder="Bilangan kali sebulan" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold small">Kelas Didikan Kanak-Kanak (Kekerapan Sebulan)</label>
                                                <input type="number" name="kelas_kanak_sebulan" class="form-control" value="{{ old('kelas_kanak_sebulan') }}" placeholder="Bilangan kali sebulan" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- SEKSYEN 5: MAKLUMAT JAWATANKUASA SURAU -->
                            <div class="accordion-item border mb-3 rounded-2">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#sec5">
                                        5. Maklumat Jawatankuasa Surau
                                    </button>
                                </h2>
                                <div id="sec5" class="accordion-collapse collapse" data-bs-parent="#accordionBorangSurau">
                                    <div class="accordion-body">
                                        <div class="alert alert-warning py-2 small d-flex align-items-center mb-3">
                                            <i class="fa-solid fa-triangle-exclamation fa-lg me-2"></i>
                                            @if($isRevision)
                                            <div><strong>PENTING:</strong> Pemohon Utama ialah <strong>Pentadbir Surau</strong>. Jika anda menandakan Ahli Jawatankuasa lain, pentadbiran surau akan <strong>diserahkan kepada individu tersebut</strong> selepas kemaskini ini diluluskan. Individu tersebut akan menerima pautan pengaktifan akaun, dan akaun anda tidak lagi mentadbir surau ini.</div>
                                            @else
                                            <div><strong>PENTING:</strong> Sila tandakan (Tick) pada **satu sahaja** Ahli Jawatankuasa yang membuat permohonan ini. Pengguna ini akan mendaftar sebagai **Pentadbir Surau** dan menerima link kelulusan e-mel.</div>
                                            @endif
                                        </div>

                                        <div id="containerAJK">
                                            @php 
                                                $oldAjkNama = old('ajk_nama', ['']);
                                                $oldPemohonIndex = old('pemohon_index', 0);
                                            @endphp
                                            
                                            @foreach($oldAjkNama as $i => $val)
                                                <div class="card border mb-3 ajk-card">
                                                    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                                                        <span class="fw-bold small text-dark ajk-title"><i class="fa-solid fa-user me-1"></i> Ahli Jawatankuasa #{{ $i + 1 }}</span>
                                                        <button type="button" class="btn btn-sm btn-danger btn-padam-ajk text-white py-0 px-2" style="{{ count($oldAjkNama) == 1 ? 'display:none;' : '' }}">Hapus</button>
                                                    </div>
                                                    <div class="card-body p-3">
                                                        <div class="row g-3">
                                                            <div class="col-md-4">
                                                                <label class="form-label fw-semibold small">Nama Lengkap</label>
                                                                <input type="text" name="ajk_nama[]" value="{{ old('ajk_nama.'.$i) }}" class="form-control form-control-sm" required>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="form-label fw-semibold small">No Kad Pengenalan</label>
                                                                <input type="text" name="ajk_ic[]" inputmode="numeric" pattern="[0-9]*" data-digits-only value="{{ old('ajk_ic.'.$i) }}" class="form-control form-control-sm" maxlength="12" required>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="form-label fw-semibold small">Jawatan</label>
                                                                <select name="ajk_jawatan[]" class="form-select form-select-sm" required>
                                                                    <option value="Pengerusi" {{ old('ajk_jawatan.'.$i) == 'Pengerusi' ? 'selected' : '' }}>Pengerusi</option>
                                                                    <option value="Setiausaha" {{ old('ajk_jawatan.'.$i) == 'Setiausaha' ? 'selected' : '' }}>Setiausaha</option>
                                                                    <option value="Bendahari" {{ old('ajk_jawatan.'.$i) == 'Bendahari' ? 'selected' : '' }}>Bendahari</option>
                                                                    <option value="AJK" {{ old('ajk_jawatan.'.$i) == 'AJK' ? 'selected' : '' }}>AJK</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="form-label fw-semibold small">Alamat Rumah</label>
                                                                <input type="text" name="ajk_alamat[]" value="{{ old('ajk_alamat.'.$i) }}" class="form-control form-control-sm" required>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="form-label fw-semibold small">No Telefon</label>
                                                                <input type="text" name="ajk_tel[]" inputmode="numeric" pattern="[0-9]*" data-digits-only value="{{ old('ajk_tel.'.$i) }}" class="form-control form-control-sm" required>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="form-label fw-semibold small">Alamat E-Mel</label>
                                                                <input type="email" name="ajk_email[]" value="{{ old('ajk_email.'.$i) }}" class="form-control form-control-sm" required>
                                                            </div>
                                                            <div class="col-12">
                                                                <div class="form-check bg-primary bg-opacity-10 p-2 rounded border border-primary border-opacity-25">
                                                                    <input class="form-check-input ms-1 is-pemohon-radio" type="radio" name="pemohon_index" value="{{ $i }}" id="pemohon_{{ $i }}" {{ $oldPemohonIndex == $i ? 'checked' : '' }} required>
                                                                    <label class="form-check-label fw-bold text-primary small ms-2" for="pemohon_{{ $i }}">
                                                                        <i class="fa-solid fa-user-check me-1"></i> Tandakan jika Individu ini ialah Pemohon Utama (Pentadbir Surau)
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <button type="button" class="btn btn-sm btn-outline-primary fw-bold w-100 py-2" id="btnTambahAJK">
                                            <i class="fa-solid fa-user-plus me-1"></i> Tambah Ahli Jawatankuasa Lain
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- SEKSYEN 6: DOKUMEN SOKONGAN -->
                            <div class="accordion-item border mb-3 rounded-2">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#sec6">
                                        6. Dokumen Sokongan (Muat Naik)
                                    </button>
                                </h2>
                                <div id="sec6" class="accordion-collapse collapse" data-bs-parent="#accordionBorangSurau">
                                    <div class="accordion-body">
                                        <div class="alert alert-info py-2 small mb-3">
                                            @if($isRevision)
                                                <i class="fa-solid fa-info-circle me-1"></i> Dokumen semasa dikekalkan. Muat naik fail baharu hanya jika ingin menggantikannya. <strong>Minit mesyuarat terkini wajib dimuat naik</strong> sebelum kemaskini dihantar. Maksimum saiz fail adalah 5MB.
                                            @else
                                                <i class="fa-solid fa-info-circle me-1"></i> Sila muat naik semula dokumen sokongan jika halaman direfresh disebabkan ralat validasi. Maximum saiz fail adalah 5MB.
                                            @endif
                                        </div>

                                        @php
                                            // Pautan fail semasa bagi draf kemaskini
                                            $currentFileNote = function ($doc) {
                                                if (!$doc) {
                                                    return '<div class="form-text text-danger">Tiada fail semasa.</div>';
                                                }
                                                return '<div class="form-text">Fail semasa: <a href="' . e(asset('storage/' . $doc->fail_path)) . '" target="_blank">'
                                                    . e($doc->fail_nama_asal ?: basename($doc->fail_path)) . '</a>. Biarkan kosong untuk kekalkan.</div>';
                                            };
                                        @endphp

                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">Minit Mesyuarat Terkini (PDF, Maks 5MB)</label>
                                                <input type="file" name="minit_mesyuarat" class="form-control" accept=".pdf" @unless($isRevision) required @endunless>
                                                @if($isRevision)
                                                    @if($revisionMinit)
                                                        <div class="form-text">Minit untuk kemaskini ini: <a href="{{ asset('storage/' . $revisionMinit->fail_path) }}" target="_blank">{{ basename($revisionMinit->fail_path) }}</a>. Muat naik semula untuk menggantikannya.</div>
                                                    @else
                                                        <div class="form-text text-danger">Belum dimuat naik. Wajib sebelum kemaskini dihantar.</div>
                                                    @endif
                                                @endif
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">Surat Pengesahan Arah Kiblat daripada Mufti Wilayah (PDF, Maks 5MB)</label>
                                                <input type="file" name="pengesahan_kiblat" class="form-control" accept=".pdf" @unless($isRevision) required @endunless>
                                                @if($isRevision) {!! $currentFileNote($revisionDocs->get('PENGESAHAN_KIBLAT')) !!} @endif
                                            </div>

                                            <div class="col-12 mt-4">
                                                <h6 class="fw-bold text-dark border-bottom pb-2 mb-2">
                                                    <i class="fa-solid fa-id-card me-1 text-primary"></i> Salinan Kad Pengenalan AJK Utama (Maks 5MB Seunit)
                                                </h6>
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold small">Salinan IC Pengerusi (PDF/JPG/PNG)</label>
                                                <input type="file" name="ic_pengerusi" class="form-control" accept=".pdf,.png,.jpg,.jpeg" @unless($isRevision) required @endunless>
                                                @if($isRevision) {!! $currentFileNote($revisionDocs->get('IC_PENGERUSI')) !!} @endif
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold small">Salinan IC Setiausaha (PDF/JPG/PNG)</label>
                                                <input type="file" name="ic_setiausaha" class="form-control" accept=".pdf,.png,.jpg,.jpeg" @unless($isRevision) required @endunless>
                                                @if($isRevision) {!! $currentFileNote($revisionDocs->get('IC_SETIAUSAHA')) !!} @endif
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold small">Salinan IC Bendahari (PDF/JPG/PNG)</label>
                                                <input type="file" name="ic_bendahari" class="form-control" accept=".pdf,.png,.jpg,.jpeg" @unless($isRevision) required @endunless>
                                                @if($isRevision) {!! $currentFileNote($revisionDocs->get('IC_BENDAHARI')) !!} @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- SEKSYEN 7: PENGESAHAN -->
                            <div class="accordion-item border mb-3 rounded-2">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#sec7">
                                        7. Pengesahan Pemohon
                                    </button>
                                </h2>
                                <div id="sec7" class="accordion-collapse collapse" data-bs-parent="#accordionBorangSurau">
                                    <div class="accordion-body p-4">
                                        <div class="card border-0 bg-light rounded-3 mb-4 shadow-sm" id="cardAkuan">
                                            <div class="card-body p-3.5">
                                                <div class="form-check d-flex align-items-start ps-0">
                                                    <div class="me-3 pt-1">
                                                        <input class="form-check-input style-check" type="checkbox" name="akuan_pengesahan" id="akuan_pengesahan" style="width: 1.35rem; height: 1.35rem; cursor: pointer;" {{ old('akuan_pengesahan') ? 'checked' : '' }} @unless($isRevision) required @endunless>
                                                    </div>
                                                    <label class="form-check-label text-dark small cursor-pointer user-select-none lh-lg" for="akuan_pengesahan">
                                                        <strong>PENGESAHAN AKUAN:</strong> “Dengan ini saya memperakukan bahawa segala keterangan butir-butir yang dinyatakan adalah benar dan sah, dan saya bersetuju sekiranya didapati salah dan tidak benar oleh pihak Jabatan Agama Islam Wilayah Persekutuan, maka permohonan ini patut ditolak atau dibatalkan pendaftarannya.”
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        @if($isRevision)
                                        <div class="d-flex flex-column flex-md-row gap-2">
                                            <button type="submit" name="action" value="draft" formnovalidate class="btn btn-outline-primary btn-lg fw-bold rounded-2 flex-fill py-2.5">
                                                <i class="fa-solid fa-floppy-disk me-2"></i> Simpan Draf
                                            </button>
                                            <button type="submit" name="action" value="submit" class="btn btn-primary btn-lg fw-bold rounded-2 shadow-sm flex-fill py-2.5" style="background-color: #0284c7; border: 1px solid #0369a1;">
                                                <i class="fa-solid fa-paper-plane me-2"></i> Hantar Kemaskini untuk Pengesahan
                                            </button>
                                        </div>
                                        <div class="text-center mt-3">
                                            <button type="submit" form="formBuangDraf" class="btn btn-link btn-sm text-danger text-decoration-none">
                                                <i class="fa-solid fa-trash me-1"></i> Buang draf ini
                                            </button>
                                        </div>
                                        @else
                                        <div class="card border mb-4 rounded-3">
                                            <div class="card-body p-3">
                                                <label for="inputCaptcha" class="form-label small fw-semibold text-secondary mb-2">
                                                    Pengesahan Keselamatan
                                                </label>
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="position-relative">
                                                        <canvas id="captchaCanvas" width="130" height="38" class="border rounded bg-white"></canvas>
                                                        <button type="button" class="btn btn-sm btn-link position-absolute top-0 end-0 m-0 text-decoration-none" id="btnRefreshCaptcha" title="Tukar Kod Captcha">
                                                            ↻
                                                        </button>
                                                    </div>
                                                    <input type="text" name="captcha" inputmode="numeric" pattern="[0-9]*" data-digits-only id="inputCaptcha" class="form-control font-monospace fw-bold text-center" style="max-width: 150px; letter-spacing: 2px;" placeholder="Kod 5-Digit" maxlength="5" required>
                                                </div>
                                                <div id="captchaError" class="text-danger small mt-1 d-none">Kod Captcha yang dimasukkan tidak tepat!</div>
                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold rounded-2 shadow-sm py-2.5" style="background-color: #0284c7; border: 1px solid #0369a1;">
                                            <i class="fa-solid fa-paper-plane me-2"></i> Hantar Permohonan Surau
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>

                    @if($isRevision)
                        <form id="formBuangDraf" action="{{ route('surau.kemaskini.destroy', $revision->id) }}" method="POST" class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    
    const STORAGE_KEY = 'surau_registration_draft_v1';
    const form = document.getElementById('formPermohonanSurau');
    // Mod kemaskini: draf disimpan di pelayan, bukan localStorage, dan tiada captcha
    const IS_REVISION = @json($isRevision);

    // =========================================================================
    // MEDAN NOMBOR SAHAJA (No. Telefon, No. K/P, No. Akaun Bank, Captcha)
    // Dikawal melalui delegasi supaya baris AJK yang ditambah secara dinamik turut terlibat.
    // =========================================================================
    const stripNonDigits = (el) => {
        const cleaned = el.value.replace(/\D+/g, '');
        if (cleaned !== el.value) el.value = cleaned;
    };
    const normaliseDigitFields = () => form.querySelectorAll('[data-digits-only]').forEach(stripNonDigits);

    form.addEventListener('input', (e) => {
        if (e.target.matches('[data-digits-only]')) stripNonDigits(e.target);
    });
    form.addEventListener('keydown', (e) => {
        if (!e.target.matches('[data-digits-only]')) return;
        if (e.ctrlKey || e.metaKey || e.altKey || e.key.length !== 1) return;
        if (!/\d/.test(e.key)) e.preventDefault();
    });
    // Bersihkan nilai sedia ada (cth. data lama dengan tanda '-') sebelum dihantar
    form.addEventListener('submit', normaliseDigitFields, true);
    normaliseDigitFields();

    // =========================================================================
    // 0. SEMAKAN RALAT VALIDASI & POP-UP SWEETALERT2
    // =========================================================================
    @if ($errors->any())

        Swal.fire({
            title: 'Sila Betulkan Ralat!',
            text: 'Terdapat maklumat borang yang belum diisi dengan betul. Sila semak semula.',
            icon: 'error',
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Semak Borang'
        });

        // Buka Seksyen Accordion Pertama yang Mengandungi Field Ralat
        const inputsWithErrors =
            form.querySelectorAll('input:invalid, select:invalid, textarea:invalid');

        if (inputsWithErrors.length > 0) {

            const firstInvalidInput = inputsWithErrors[0];

            const parentCollapse =
                firstInvalidInput.closest('.accordion-collapse');

            if (parentCollapse) {

                const accordionButton = document.querySelector(
                    `[data-bs-target="#${parentCollapse.id}"]`
                );

                if (accordionButton) {

                    if (!parentCollapse.classList.contains('show')) {
                        accordionButton.click();
                    }

                    setTimeout(() => {
                        firstInvalidInput.focus();
                    }, 300);

                } else {

                    parentCollapse.classList.add('show');

                    setTimeout(() => {
                        firstInvalidInput.focus();
                    }, 300);
                }
            }
        }

    @endif

    @if (session('success') && $isRevision)
        Swal.fire({
            title: 'Draf Disimpan',
            text: @json(session('success')),
            icon: 'success',
            confirmButtonColor: '#198754',
            confirmButtonText: 'OK'
        });
    @elseif (session('success'))
        Swal.fire({
            title: 'Berjaya Dihantar!',
            text: "{{ session('success') }} (No. Rujukan: {{ session('reference_no') }})",
            icon: 'success',
            confirmButtonColor: '#198754',
            confirmButtonText: 'Selesai'
        });
    @endif

    // =========================================================================
    // 1. KAWASAN TAPAK LAIN-LAIN
    // =========================================================================
    const selectKawasan = document.getElementById('kawasan_surau');
    const divKawasanLain = document.getElementById('divKawasanLain');
    if (selectKawasan && divKawasanLain) {
        selectKawasan.addEventListener('change', function () {
            if (this.value === 'Lain-lain') {
                divKawasanLain.classList.remove('d-none');
                document.getElementById('kawasan_lain').setAttribute('required', 'required');
            } else {
                divKawasanLain.classList.add('d-none');
                document.getElementById('kawasan_lain').removeAttribute('required');
            }
        });
    }

    // =========================================================================
    // 2. AUTO COPY ALAMAT SURAU
    // =========================================================================
    const copyCheck = document.getElementById('copyAlamatCheck');
    const alamatSurau = document.getElementById('alamat_surau');
    const alamatSurat = document.getElementById('alamat_surat');
    if (copyCheck && alamatSurau && alamatSurat) {
        copyCheck.addEventListener('change', function () {
            if (this.checked) {
                alamatSurat.value = alamatSurau.value;
                saveDraftData();
            }
        });
    }

    // =========================================================================
    // 3. TAMBAH / HAPUS SUMBER KEWANGAN
    // =========================================================================
    const containerSumber = document.getElementById('containerSumberKewangan');
    const btnTambahSumber = document.getElementById('btnTambahSumber');
    
    if (btnTambahSumber) {
        btnTambahSumber.addEventListener('click', function () {
            tambahSumberRow('');
            saveDraftData();
        });
    }

    function tambahSumberRow(value = '') {
        if (!containerSumber) return;
        const div = document.createElement('div');
        div.className = 'row g-2 mb-2 sumber-row';
        div.innerHTML = `
            <div class="col-10">
                <input type="text" name="sumber_kewangan[]" class="form-control form-control-sm" value="${value}" placeholder="Cth: Derma Jumaat / Yuran Ahli / Sumbangan Korporat" required>
            </div>
            <div class="col-2">
                <button type="button" class="btn btn-sm btn-outline-danger w-100 btn-padam-sumber"><i class="fa-solid fa-trash"></i></button>
            </div>
        `;
        containerSumber.appendChild(div);
        updateBtnPadamSumber();
    }

    if (containerSumber) {
        containerSumber.addEventListener('click', function (e) {
            if (e.target.closest('.btn-padam-sumber')) {
                e.target.closest('.sumber-row').remove();
                updateBtnPadamSumber();
                saveDraftData();
            }
        });
    }

    function updateBtnPadamSumber() {
        if (!containerSumber) return;
        const rows = containerSumber.querySelectorAll('.sumber-row');
        rows.forEach(r => {
            const btn = r.querySelector('.btn-padam-sumber');
            if (btn) btn.disabled = rows.length === 1;
        });
    }

    // =========================================================================
    // 4. TAMBAH / HAPUS AHLI JAWATANKUASA (AJK)
    // =========================================================================
    const containerAJK = document.getElementById('containerAJK');
    const btnTambahAJK = document.getElementById('btnTambahAJK');
    let ajkCounter = containerAJK ? containerAJK.querySelectorAll('.ajk-card').length : 1;

    if (btnTambahAJK) {
        btnTambahAJK.addEventListener('click', function () {
            tambahAJKCard();
            saveDraftData();
        });
    }

    function tambahAJKCard(data = {}) {
        if (!containerAJK) return;
        ajkCounter++;
        const card = document.createElement('div');
        card.className = 'card border mb-3 ajk-card';
        card.innerHTML = `
            <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                <span class="fw-bold small text-dark ajk-title"><i class="fa-solid fa-user me-1"></i> Ahli Jawatankuasa #${ajkCounter}</span>
                <button type="button" class="btn btn-sm btn-danger btn-padam-ajk text-white py-0 px-2">Hapus</button>
            </div>
            <div class="card-body p-3">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Nama Lengkap</label>
                        <input type="text" name="ajk_nama[]" value="${data.nama || ''}" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">No Kad Pengenalan</label>
                        <input type="text" name="ajk_ic[]" inputmode="numeric" pattern="[0-9]*" data-digits-only value="${data.ic || ''}" class="form-control form-control-sm" maxlength="12" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Jawatan</label>
                        <select name="ajk_jawatan[]" class="form-select form-select-sm" required>
                            <option value="Pengerusi" ${data.jawatan === 'Pengerusi' ? 'selected' : ''}>Pengerusi</option>
                            <option value="Setiausaha" ${data.jawatan === 'Setiausaha' ? 'selected' : ''}>Setiausaha</option>
                            <option value="Bendahari" ${data.jawatan === 'Bendahari' ? 'selected' : ''}>Bendahari</option>
                            <option value="AJK" ${data.jawatan === 'AJK' ? 'selected' : ''}>AJK</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Alamat Rumah</label>
                        <input type="text" name="ajk_alamat[]" value="${data.alamat || ''}" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">No Telefon</label>
                        <input type="text" name="ajk_tel[]" inputmode="numeric" pattern="[0-9]*" data-digits-only value="${data.tel || ''}" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Alamat E-Mel</label>
                        <input type="email" name="ajk_email[]" value="${data.email || ''}" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-12">
                        <div class="form-check bg-success bg-opacity-10 p-2 rounded border border-success border-opacity-25">
                            <input class="form-check-input ms-1 is-pemohon-radio" type="radio" name="pemohon_index" value="${ajkCounter - 1}" id="pemohon_${ajkCounter - 1}" ${data.is_applicant ? 'checked' : ''} required>
                            <label class="form-check-label fw-bold text-success small ms-2" for="pemohon_${ajkCounter - 1}">
                                <i class="fa-solid fa-user-check me-1"></i> Tandakan jika Individu ini ialah Pemohon Utama (Pentadbir Surau)
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        `;
        containerAJK.appendChild(card);
        updateAJKTitles();
    }

    if (containerAJK) {
        containerAJK.addEventListener('click', function (e) {
            if (e.target.classList.contains('btn-padam-ajk')) {
                e.target.closest('.ajk-card').remove();
                updateAJKTitles();
                saveDraftData();
            }
        });
    }

    function updateAJKTitles() {
        if (!containerAJK) return;
        const cards = containerAJK.querySelectorAll('.ajk-card');
        cards.forEach((card, index) => {
            const titleEl = card.querySelector('.ajk-title');
            if (titleEl) titleEl.innerHTML = `<i class="fa-solid fa-user me-1"></i> Ahli Jawatankuasa #${index + 1}`;
            
            const radio = card.querySelector('.is-pemohon-radio');
            if (radio) {
                radio.value = index;
                radio.id = `pemohon_${index}`;
                const label = card.querySelector('.form-check-label');
                if (label) label.setAttribute('for', `pemohon_${index}`);
            }
            
            const btnDelete = card.querySelector('.btn-padam-ajk');
            if (btnDelete) {
                btnDelete.style.display = (cards.length === 1) ? 'none' : 'block';
            }
        });
    }

    // =========================================================================
    // 5. CAPTCHA TEMPATAN
    // =========================================================================
    let currentCaptchaCode = '';
    const canvas = document.getElementById('captchaCanvas');

    function generateCaptcha() {
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        currentCaptchaCode = Math.floor(10000 + Math.random() * 90000).toString();
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        ctx.fillStyle = '#f8f9fa';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        
        ctx.strokeStyle = '#ced4da';
        for (let i = 0; i < 4; i++) {
            ctx.beginPath();
            ctx.moveTo(Math.random() * canvas.width, Math.random() * canvas.height);
            ctx.lineTo(Math.random() * canvas.width, Math.random() * canvas.height);
            ctx.stroke();
        }

        ctx.font = 'bold 22px monospace';
        ctx.fillStyle = '#198754';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(currentCaptchaCode, canvas.width / 2, canvas.height / 2);
    }

    generateCaptcha();
    const btnRefresh = document.getElementById('btnRefreshCaptcha');
    if (btnRefresh) btnRefresh.addEventListener('click', generateCaptcha);

    // =========================================================================
    // 6. AUTO-SAVE & RESTORE DRAF
    // =========================================================================
    function saveDraftData() {
        if (!form || IS_REVISION) return;
        const draftObj = {
            timestamp: new Date().toLocaleString('ms-MY'),
            fields: {},
            sumber_kewangan: [],
            ajk_list: []
        };

        const inputs = form.querySelectorAll('input:not([type="file"]):not([type="hidden"]):not([type="radio"]):not([type="checkbox"]), select, textarea');
        inputs.forEach(input => {
            if (input.name && !input.name.includes('[]')) {
                draftObj.fields[input.name] = input.value;
            }
        });

        if (containerSumber) {
            const sumberInputs = containerSumber.querySelectorAll('input[name="sumber_kewangan[]"]');
            sumberInputs.forEach(inp => {
                if (inp.value.trim() !== '') draftObj.sumber_kewangan.push(inp.value);
            });
        }

        if (containerAJK) {
            const ajkCards = containerAJK.querySelectorAll('.ajk-card');
            ajkCards.forEach(card => {
                const radio = card.querySelector('.is-pemohon-radio');
                draftObj.ajk_list.push({
                    nama: card.querySelector('input[name="ajk_nama[]"]')?.value || '',
                    ic: card.querySelector('input[name="ajk_ic[]"]')?.value || '',
                    jawatan: card.querySelector('select[name="ajk_jawatan[]"]')?.value || 'Pengerusi',
                    alamat: card.querySelector('input[name="ajk_alamat[]"]')?.value || '',
                    tel: card.querySelector('input[name="ajk_tel[]"]')?.value || '',
                    email: card.querySelector('input[name="ajk_email[]"]')?.value || '',
                    is_applicant: radio ? radio.checked : false
                });
            });
        }

        localStorage.setItem(STORAGE_KEY, JSON.stringify(draftObj));
        updateDraftBadgeStatus(draftObj.timestamp);
    }

    function restoreDraftData() {
        const savedData = localStorage.getItem(STORAGE_KEY);
        if (!savedData || !form) return;

        try {
            const draftObj = JSON.parse(savedData);
            
            // Object.keys(draftObj.fields).forEach(key => {
            //    const el = form.querySelector(`[name="${key}"]`);
            //    if (el) {
            //        el.value = draftObj.fields[key];
            //        el.dispatchEvent(new Event('change'));
            //    }
            //});
            Object.keys(draftObj.fields).forEach(key => {

                const el = form.querySelector(`[name="${key}"]`);

                if (!el) return;

                // Untuk input biasa / select / textarea
                el.value = draftObj.fields[key];

                // Trigger change supaya fungsi berkaitan turut berjalan
                el.dispatchEvent(new Event('change', {
                    bubbles: true
                }));
            });

            if (draftObj.sumber_kewangan && draftObj.sumber_kewangan.length > 0 && containerSumber) {
                containerSumber.innerHTML = '';
                draftObj.sumber_kewangan.forEach(val => tambahSumberRow(val));
            }

            if (draftObj.ajk_list && draftObj.ajk_list.length > 0 && containerAJK) {
                containerAJK.innerHTML = '';
                ajkCounter = 0;
                draftObj.ajk_list.forEach(ajkData => tambahAJKCard(ajkData));
            }
            normaliseDigitFields();

            Swal.fire({
                title: 'Draf Berjaya Diisikan!',
                text: 'Semua maklumat permohonan yang tersimpan telah dimasukkan semula ke dalam borang.',
                icon: 'success',
                confirmButtonText: 'Teruskan Mengisi',
                confirmButtonColor: '#198754'
            });

        } catch (e) {
            console.error('Ralat restore draf:', e);
        }
    }

    function updateDraftBadgeStatus(timestamp) {
        const badge = document.getElementById('draftStatusBadge');
        const btnRestore = document.getElementById('btnRestoreDraft');
        const btnClear = document.getElementById('btnClearDraft');

        if (badge && btnRestore && btnClear) {
            if (timestamp) {
                badge.className = 'badge bg-success font-monospace py-2 px-3';
                badge.innerHTML = `Draf Ada (${timestamp})`;
                btnRestore.classList.remove('d-none');
                btnClear.classList.remove('d-none');
            } else {
                badge.className = 'badge bg-secondary font-monospace py-2 px-3';
                badge.innerHTML = `Tiada Draf Tersimpan`;
                btnRestore.classList.add('d-none');
                btnClear.classList.add('d-none');
            }
        }
    }

    if (form) {
        form.addEventListener('input', saveDraftData);
        form.addEventListener('change', saveDraftData);
    }

    const savedData = localStorage.getItem(STORAGE_KEY);
    if (savedData) {
        try {
            const draftObj = JSON.parse(savedData);
            updateDraftBadgeStatus(draftObj.timestamp);
        } catch(e){}
    } else {
        updateDraftBadgeStatus(null);
    }

    const btnRestore = document.getElementById('btnRestoreDraft');
    if (btnRestore) btnRestore.addEventListener('click', restoreDraftData);

    const btnClear = document.getElementById('btnClearDraft');
    if (btnClear) {
        btnClear.addEventListener('click', function () {
            Swal.fire({
                title: 'Padam Draf Permohonan?',
                text: 'Adakah anda pasti mahu memadamkan draf yang tersimpan?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Padam Draf',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    localStorage.removeItem(STORAGE_KEY);
                    location.reload();
                }
            });
        });
    }

    // =========================================================================
    // 7. SEMAKAN SUBMIT & ACCORDION EXPAND ON ERROR
    // =========================================================================
    if (form) {

        form.addEventListener('submit', function (e) {

            // Simpan draf kemaskini: terus hantar, validasi dibuat di pelayan
            if (IS_REVISION && e.submitter && e.submitter.value === 'draft') {
                return;
            }

            // =====================================================
            // 1. SEMAK HTML5 VALIDATION
            // =====================================================
            if (!form.checkValidity()) {

                e.preventDefault();
                e.stopPropagation();

                const invalidInputs = form.querySelectorAll(':invalid');

                const fieldNames = [];

                invalidInputs.forEach((input) => {

                    let fieldName = '';

                    // =====================================================
                    // CARI LABEL BERDASARKAN ID
                    // =====================================================
                    if (input.id) {

                        const label = form.querySelector(
                            `label[for="${input.id}"]`
                        );

                        if (label) {
                            fieldName = label.innerText
                                .replace(/\s+/g, ' ')
                                .trim();
                        }
                    }

                    // =====================================================
                    // JIKA TIADA LABEL[for]
                    // =====================================================
                    if (!fieldName) {

                        const parent =
                            input.closest(
                                '.form-check, .col-md-4, .col-md-6, .col-12, .card-body'
                            );

                        if (parent) {

                            const labelElement =
                                parent.querySelector('label');

                            if (labelElement) {

                                fieldName = labelElement.innerText
                                    .replace(/\s+/g, ' ')
                                    .trim();
                            }
                        }
                    }

                    // =====================================================
                    // FALLBACK GUNA NAME
                    // =====================================================
                    if (!fieldName) {
                        fieldName =
                            input.name || 'Medan tidak dikenali';
                    }

                    // Buang *
                    fieldName = fieldName
                        .replace(/\*/g, '')
                        .trim();

                    // Elak duplicate
                    if (
                        fieldName &&
                        !fieldNames.includes(fieldName)
                    ) {
                        fieldNames.push(fieldName);
                    }
                });


                


                // =====================================================
                // 3. BINA SENARAI ERROR
                // =====================================================
                let fieldListHtml = '';

                if (fieldNames.length > 0) {

                    fieldListHtml = `
                        <div class="text-start mt-3">

                            <div class="fw-bold text-danger mb-2">
                                Medan berikut perlu dilengkapkan:
                            </div>

                            <ul class="mb-0 ps-4">

                                ${fieldNames.map(name => `
                                    <li class="mb-1">
                                        ${name}
                                    </li>
                                `).join('')}

                            </ul>

                        </div>
                    `;
                }


                // =====================================================
                // 4. BUKA ACCORDION FIELD PERTAMA YANG ERROR
                // =====================================================
                if (invalidInputs.length > 0) {

                    const firstInvalid =
                        invalidInputs[0];

                    const collapseEl =
                        firstInvalid.closest(
                            '.accordion-collapse'
                        );

                    if (collapseEl) {

                        const accordionButton =
                            document.querySelector(
                                `[data-bs-target="#${collapseEl.id}"]`
                            );

                        if (accordionButton) {

                            if (
                                !collapseEl.classList.contains('show')
                            ) {
                                accordionButton.click();
                            }

                            setTimeout(() => {
                                firstInvalid.focus();
                            }, 300);

                        } else {

                            collapseEl.classList.add('show');

                            setTimeout(() => {
                                firstInvalid.focus();
                            }, 300);
                        }
                    }
                }


                // =====================================================
                // 5. SWEETALERT
                // =====================================================
                Swal.fire({

                    title: 'Maklumat Tidak Lengkap!',

                    html: `
                        <div class="text-start">

                            <p class="mb-2">
                                Sila lengkapkan semua medan yang diperlukan.
                            </p>

                            ${fieldListHtml}

                        </div>
                    `,

                    icon: 'warning',

                    confirmButtonColor: '#ffc107',

                    confirmButtonText: 'OK',

                    width: '600px'

                });

                return;
            }


            // Kemaskini: wajib tandakan akuan, tiada captcha (pengguna telah log masuk)
            if (IS_REVISION) {
                const akuan = document.getElementById('akuan_pengesahan');
                if (akuan && !akuan.checked) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Pengesahan Akuan',
                        text: 'Sila tandakan pengesahan akuan sebelum menghantar kemaskini.',
                        icon: 'warning',
                        confirmButtonColor: '#ffc107',
                        confirmButtonText: 'OK'
                    });
                }
                return;
            }

            // =====================================================
            // 6. SEMAK CAPTCHA
            // =====================================================
            const inputCaptchaEl =
                document.getElementById('inputCaptcha');

            const inputCaptcha =
                inputCaptchaEl
                    ? inputCaptchaEl.value.trim()
                    : '';


            if (inputCaptcha !== currentCaptchaCode) {

                e.preventDefault();
                e.stopPropagation();

                Swal.fire({

                    title: 'Captcha Salah!',

                    html: `
                        <div>
                            Kod <strong>Pengesahan Keselamatan</strong>
                            yang dimasukkan tidak tepat.
                            <br>
                            Sila cuba lagi.
                        </div>
                    `,

                    icon: 'error',

                    confirmButtonColor: '#dc3545',

                    confirmButtonText: 'OK'

                }).then(() => {

                    generateCaptcha();

                    if (inputCaptchaEl) {
                        inputCaptchaEl.value = '';
                        inputCaptchaEl.focus();
                    }

                });

                return;
            }


            // =====================================================
            // 7. SEMUA VALID
            // =====================================================

            localStorage.removeItem(STORAGE_KEY);

            // Jangan preventDefault()
            // Laravel akan menerima POST seperti biasa

        });
    }

    // =========================================================================
    // 7. SEMAKAN SUBMIT & ACCORDION EXPAND ON ERROR
    // =========================================================================
    // if (form) {

    //     form.addEventListener('submit', function (e) {

    //         // console.log('SUBMIT BUTTON DITEKAN');

    //         // ==========================================
    //         // 1. SEMAK BORANG
    //         // ==========================================
    //         if (!form.checkValidity()) {

    //             e.preventDefault();
    //             e.stopPropagation();

    //             const invalidInputs =
    //                 form.querySelectorAll(':invalid');

    //             // console.log(
    //             //     'Jumlah field tidak lengkap:',
    //             //     invalidInputs.length
    //             // );

    //             // =====================================================
    //             // DAPATKAN NAMA SEMUA FIELD YANG TIDAK LENGKAP
    //             // =====================================================
    //             const fieldNames = [];

    //             invalidInputs.forEach((input) => {

    //                 let fieldName = '';

    //                 // Ambil label yang berkaitan
    //                 const label = form.querySelector(
    //                     `label[for="${input.id}"]`
    //                 );

    //                 if (label) {

    //                     fieldName = label.innerText
    //                         .replace(/\s+/g, ' ')
    //                         .trim();

    //                 } else {

    //                     // Jika tiada label[for]
    //                     // cuba cari label dalam parent
    //                     const parentLabel =
    //                         input.closest('.form-check, .col-md-4, .col-md-6, .col-12');

    //                     if (parentLabel) {

    //                         const labelElement =
    //                             parentLabel.querySelector('label');

    //                         if (labelElement) {
    //                             fieldName = labelElement.innerText
    //                                 .replace(/\s+/g, ' ')
    //                                 .trim();
    //                         }
    //                     }
    //                 }

    //                 // Jika masih tak jumpa label
    //                 if (!fieldName) {
    //                     fieldName = input.name || 'Medan tidak dikenali';
    //                 }

    //                 // Buang tanda (*) jika ada
    //                 fieldName = fieldName
    //                     .replace(/\*/g, '')
    //                     .trim();

    //                 // Elakkan duplicate
    //                 if (!fieldNames.includes(fieldName)) {
    //                     fieldNames.push(fieldName);
    //                 }
    //             });

    //             // console.log(
    //             //     'Medan tidak lengkap:',
    //             //     fieldNames
    //             // );


    //             // =====================================================
    //             // BINA SENARAI HTML
    //             // =====================================================
    //             let fieldListHtml = '';

    //             if (fieldNames.length > 0) {

    //                 fieldListHtml = `
    //                     <div class="text-start mt-3">
    //                         <div class="fw-bold text-danger mb-2">
    //                             Medan berikut perlu dilengkapkan:
    //                         </div>

    //                         <ul class="mb-0 ps-4">
    //                             ${fieldNames.map(name => `
    //                                 <li class="mb-1">
    //                                     ${name}
    //                                 </li>
    //                             `).join('')}
    //                         </ul>
    //                     </div>
    //                 `;
    //             }


    //             // =====================================================
    //             // BUKA ACCORDION FIELD PERTAMA YANG ERROR
    //             // =====================================================
    //             if (invalidInputs.length > 0) {

    //                 const firstInvalid =
    //                     invalidInputs[0];

    //                 // console.log(
    //                 //     'Field pertama tidak lengkap:',
    //                 //     firstInvalid.name
    //                 // );

    //                 const collapseEl =
    //                     firstInvalid.closest('.accordion-collapse');

    //                 if (collapseEl) {

    //                     // console.log(
    //                     //     'Accordion error:',
    //                     //     collapseEl.id
    //                     // );

    //                     const accordionButton =
    //                         document.querySelector(
    //                             `[data-bs-target="#${collapseEl.id}"]`
    //                         );

    //                     if (accordionButton) {

    //                         if (!collapseEl.classList.contains('show')) {
    //                             accordionButton.click();
    //                         }

    //                         setTimeout(() => {
    //                             firstInvalid.focus();
    //                         }, 300);

    //                     } else {

    //                         console.warn(
    //                             'Button accordion tidak dijumpai:',
    //                             collapseEl.id
    //                         );

    //                         collapseEl.classList.add('show');

    //                         setTimeout(() => {
    //                             firstInvalid.focus();
    //                         }, 300);
    //                     }
    //                 }
    //             }


    //             // =====================================================
    //             // SWEETALERT
    //             // =====================================================
    //             Swal.fire({
    //                 title: 'Maklumat Tidak Lengkap!',
    //                 html: `
    //                     <div class="text-start">
    //                         <p class="mb-2">
    //                             Sila lengkapkan semua medan yang diperlukan.
    //                         </p>

    //                         ${fieldListHtml}
    //                     </div>
    //                 `,
    //                 icon: 'warning',
    //                 confirmButtonColor: '#ffc107',
    //                 confirmButtonText: 'OK',
    //                 width: '600px'
    //             });

    //             return;
    //         }


    //         // ==========================================
    //         // 2. SEMAK CAPTCHA
    //         // ==========================================
    //         const inputCaptchaEl =
    //             document.getElementById('inputCaptcha');

    //         const inputCaptcha =
    //             inputCaptchaEl
    //                 ? inputCaptchaEl.value.trim()
    //                 : '';

    //         // console.log(
    //         //     'Captcha pengguna:',
    //         //     inputCaptcha
    //         // );

    //         // console.log(
    //         //     'Captcha sebenar:',
    //         //     currentCaptchaCode
    //         // );


    //         if (inputCaptcha !== currentCaptchaCode) {

    //             e.preventDefault();

    //             Swal.fire({
    //                 title: 'Captcha Salah!',
    //                 text: 'Kod Captcha yang dimasukkan tidak tepat. Sila cuba lagi.',
    //                 icon: 'error',
    //                 confirmButtonColor: '#dc3545',
    //                 confirmButtonText: 'OK'
    //             });

    //             generateCaptcha();

    //             if (inputCaptchaEl) {
    //                 inputCaptchaEl.value = '';
    //                 inputCaptchaEl.focus();
    //             }

    //             return;
    //         }


    //         // ==========================================
    //         // 3. SEMUA VALID
    //         // ==========================================
    //         // console.log(
    //         //     'BORANG VALID - HANTAR KE SERVER'
    //         // );

    //         localStorage.removeItem(STORAGE_KEY);

    //         // Jangan preventDefault()
    //         // Form akan dihantar ke Laravel
    //     });
    // }

    const formBuangDraf = document.getElementById('formBuangDraf');
    if (formBuangDraf) {
        formBuangDraf.addEventListener('submit', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Buang Draf Kemaskini?',
                text: 'Semua perubahan dalam draf ini akan dibuang. Rekod rasmi surau tidak terjejas.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Buang Draf',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    formBuangDraf.submit();
                }
            });
        });
    }

    const checkAkuan = document.getElementById('akuan_pengesahan');
    const cardAkuan = document.getElementById('cardAkuan');

    if (checkAkuan && cardAkuan) {
        checkAkuan.addEventListener('change', function () {
            if (this.checked) {
                cardAkuan.classList.remove('bg-light');
                cardAkuan.classList.add('bg-success', 'bg-opacity-10', 'border', 'border-success');
            } else {
                cardAkuan.classList.remove('bg-success', 'bg-opacity-10', 'border', 'border-success');
                cardAkuan.classList.add('bg-light');
            }
        });
    }

});
</script>
@endsection