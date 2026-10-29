@extends('layouts.app')

@section('content')
<div class="container py-4">

    {{-- =========================================================
        HEADER
    ========================================================== --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-mosque me-2" style="color: #0891b2;"></i>
                Semakan Pendaftaran Surau
            </h4>

            <div class="d-flex align-items-center gap-2 flex-wrap">

                {{-- Reference No --}}
                @if(!empty($application->reference_no))
                    <span class="badge bg-secondary font-monospace">
                        <i class="fa-solid fa-hashtag me-1"></i>
                        {{ $application->reference_no }}
                    </span>
                @elseif(!empty($masjid->reference_no))
                    <span class="badge bg-secondary font-monospace">
                        <i class="fa-solid fa-hashtag me-1"></i>
                        {{ $masjid->reference_no }}
                    </span>
                @endif

                {{-- Tarikh --}}
                @if(!empty($application->created_at))
                    <span class="badge bg-secondary font-monospace">
                        <i class="fa-solid fa-calendar-days me-1"></i>
                        Tarikh Hantar:
                        {{ $application->created_at->format('d/m/Y h:i A') }}
                    </span>
                @elseif(!empty($masjid->created_at))
                    <span class="badge bg-secondary font-monospace">
                        <i class="fa-solid fa-calendar-days me-1"></i>
                        Tarikh Daftar:
                        {{ $masjid->created_at->format('d/m/Y h:i A') }}
                    </span>
                @endif

                {{-- =========================================================
                    STATUS PERINGKAT PENTADBIR MASJID
                ========================================================= --}}

                @php
                    $masjidConfirmation = $application->masjidConfirmation;
                    $masjidStatus = $masjidConfirmation?->status;
                @endphp

                @if($masjidStatus === 'APPROVED')

                    <span class="badge bg-success">
                        <i class="fa-solid fa-circle-check me-1"></i>
                        TELAH DISOKONG - MENUNGGU JAWI
                    </span>

                @elseif($masjidStatus === 'REJECTED')

                    <span class="badge bg-danger">
                        <i class="fa-solid fa-circle-xmark me-1"></i>
                        DITOLAK
                    </span>

                @else

                    <span class="badge bg-warning text-dark">
                        <i class="fa-solid fa-clock me-1"></i>
                        MENUNGGU SOKONGAN MASJID
                    </span>

                @endif
            </div>
        </div>

        <div>
            <a href="{{ route('admin.masjid.pengesahan.index') }}"
               class="btn btn-outline-secondary btn-sm fw-medium">
                <i class="fa-solid fa-arrow-left me-1"></i>
                Kembali ke Senarai
            </a>
        </div>
    </div>


    <div class="row g-4">

        {{-- =====================================================
            KANDUNGAN UTAMA
        ====================================================== --}}
        <div class="col-lg-8">

            <!-- Seksyen 1: Maklumat Am Surau -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header text-white fw-bold py-2.5" style="background: linear-gradient(135deg, #1e3a8a 0%, #0369a1 100%);">
                    1. Maklumat Surau
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6"><strong>Nama Surau:</strong> <div class="text-secondary">{{ $application->nama_surau ?? '-' }}</div></div>
                        <div class="col-md-6"><strong>Kategori Surau:</strong> <div class="text-secondary">{{ $application->kategori_surau ?? '-' }}</div></div>
                        <div class="col-md-6"><strong>Kariah Masjid:</strong> <div class="text-secondary">{{ $application->kariah_masjid ?? '-' }}</div></div>
                        <div class="col-md-6"><strong>No. Telefon Surau:</strong> <div class="text-secondary">{{ $application->no_tel_surau ?? '-' }}</div></div>
                        <div class="col-md-6"><strong>Alamat Surau:</strong> <div class="text-secondary">{{ $application->alamat_surau ?? '-' }}</div></div>
                        <div class="col-md-6"><strong>Alamat Surat-Menyurat:</strong> <div class="text-secondary">{{ $application->alamat_surat ?? '-' }}</div></div>
                    </div>
                </div>
            </div>

            <!-- Seksyen 2: Maklumat Tapak & Struktur Bangunan -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header text-white fw-bold py-2.5" style="background: linear-gradient(135deg, #1e3a8a 0%, #0369a1 100%);">
                    2. Maklumat Tapak & Struktur Bangunan
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Kawasan Tapak & Butiran -->
                        <div class="col-md-6">
                            <strong>Kawasan Tapak:</strong> 
                            <div class="text-secondary">
                                {{ $application->kawasan_surau ?? '-' }}
                                @if(($application->kawasan_surau === 'Lain-lain' || !empty($application->kawasan_lain)) && $application->kawasan_lain)
                                    <span class="text-muted">({{ $application->kawasan_lain }})</span>
                                @endif
                            </div>
                        </div>

                        <!-- Taraf Tapak -->
                        <div class="col-md-6">
                            <strong>Taraf Tapak Bangunan:</strong> 
                            <div class="text-secondary">{{ $application->taraf_tapak ?? '-' }}</div>
                        </div>

                        <!-- No Lot / Geran Tanah -->
                        <div class="col-md-6">
                            <strong>No. Lot / Geran Tanah:</strong> 
                            <div class="text-secondary font-monospace fw-bold">{{ $application->no_lot_geran ?? '-' }}</div>
                        </div>

                        <!-- Surau Berdekatan & Jarak -->
                        <div class="col-md-6">
                            <strong>Surau Berdekatan (Jarak):</strong> 
                            <div class="text-secondary">
                                {{ $application->nama_surau_berdekatan ?? '-' }} 
                                @if($application->jarak_surau_berdekatan)
                                    <span class="badge bg-light text-dark border ms-1">{{ $application->jarak_surau_berdekatan }} KM</span>
                                @endif
                            </div>
                        </div>

                        <hr class="my-2 text-muted opacity-25">

                        <!-- Jenis Bangunan -->
                        <div class="col-md-3">
                            <strong>Jenis Bangunan:</strong> 
                            <div class="text-secondary">{{ $application->jenis_bangunan ?? '-' }}</div>
                        </div>

                        <!-- Tingkat Bangunan -->
                        <div class="col-md-3">
                            <strong>Tingkat Bangunan:</strong> 
                            <div class="text-secondary">{{ $application->tingkat_bangunan ?? '-' }}</div>
                        </div>

                        <!-- Tahun Didirikan -->
                        <div class="col-md-3">
                            <strong>Tahun Didirikan:</strong> 
                            <div class="text-secondary font-monospace">{{ $application->tahun_didirikan ?? '-' }}</div>
                        </div>

                        <!-- Perbelanjaan Pembinaan -->
                        <div class="col-md-3">
                            <strong>Kos Pembinaan:</strong> 
                            <div class="text-secondary font-monospace fw-bold text-success">
                                RM {{ number_format($application->jumlah_perbelanjaan ?? 0, 2) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seksyen 3: Kemudahan & Kelengkapan -->
            <!-- <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header text-white fw-bold py-2.5" style="background: linear-gradient(135deg, #1e3a8a 0%, #0369a1 100%);">
                    <i class="fa-solid fa-faucet-drip me-2 text-success"></i> 3. Kemudahan & Kelengkapan
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4"><strong>Tempat Wuduk:</strong> <div class="text-secondary">{{ $application->kemudahan_wuduk ? 'Ada' : 'Tiada' }}</div></div>
                        <div class="col-md-4"><strong>Bilik Tandas:</strong> <div class="text-secondary">{{ $application->kemudahan_tandas ? 'Ada' : 'Tiada' }}</div></div>
                        <div class="col-md-4"><strong>Pembesar Suara (PA System):</strong> <div class="text-secondary">{{ $application->kemudahan_pa_system ? 'Ada' : 'Tiada' }}</div></div>
                        <div class="col-md-4"><strong>Pendingin Hawa / Kipas:</strong> <div class="text-secondary">{{ $application->kemudahan_pendingin_hawa ? 'Ada' : 'Tiada' }}</div></div>
                        <div class="col-md-4"><strong>Kawasan Tempat Letak Kereta:</strong> <div class="text-secondary">{{ $application->kemudahan_parkir ? 'Ada' : 'Tiada' }}</div></div>
                        <div class="col-md-4"><strong>Bilik Pengurusan / Stor:</strong> <div class="text-secondary">{{ $application->kemudahan_stor ? 'Ada' : 'Tiada' }}</div></div>
                    </div>
                </div>
            </div> -->

            <!-- Seksyen 3: Maklumat Bank & Sumber Kewangan -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header text-white fw-bold py-2.5" style="background: linear-gradient(135deg, #1e3a8a 0%, #0369a1 100%);">
                    3. Maklumat Bank & Sumber Kewangan
                </div>
                <div class="card-body">
                    
                    <!-- Sub-Seksyen A: Maklumat Bank -->
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                        <i class="fa-solid fa-building-columns me-1 text-success"></i> Maklumat Bank
                    </h6>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <strong>Nama Bank:</strong>
                            <div class="text-secondary fw-semibold">{{ $application->nama_bank ?? '-' }}</div>
                        </div>
                        
                        <div class="col-md-6">
                            <strong>No. Akaun Bank:</strong>
                            <div class="text-secondary font-monospace fw-bold">{{ $application->no_akaun_bank ?? '-' }}</div>
                        </div>

                        <div class="col-md-6">
                            <strong>Alamat Cawangan Bank:</strong>
                            <div class="text-secondary">{{ $application->alamat_bank ?? '-' }}</div>
                        </div>

                        <div class="col-md-6">
                            <strong>Penama Akaun Bank:</strong>
                            <div class="text-secondary">{{ $application->penama_akaun ?? '-' }}</div>
                        </div>
                    </div>

                    <!-- Sub-Seksyen B: Maklumat Sumber Kewangan Surau -->
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                        <i class="fa-solid fa-hand-holding-dollar me-1 text-success"></i> Maklumat Sumber Kewangan
                    </h6>
                    
                    <div class="mt-3">
                        @php
                            // 1. Dapatkan data sumber kewangan
                            $dataSumber = $application->sumber_kewangan;

                            // 2. Jika data adalah string JSON, tukarkan kepada Array PHP
                            if (is_string($dataSumber)) {
                                $decoded = json_decode($dataSumber, true);
                                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                    $dataSumber = $decoded;
                                } else {
                                    $dataSumber = array_map('trim', explode(',', $dataSumber));
                                }
                            }
                        @endphp

                        <div class="table-responsive">
                            <table class="table table-bordered table-sm align-middle mb-0" style="font-size: 0.85rem;">
                                <thead class="bg-light text-secondary small text-uppercase">
                                    <tr>
                                        <th class="text-center" style="width: 50px;">Bil.</th>
                                        <th>Butiran Sumber Kewangan Surau</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(is_array($dataSumber) && count($dataSumber) > 0)
                                        @foreach($dataSumber as $index => $sumber)
                                            @if(!empty($sumber))
                                                <tr>
                                                    <td class="text-center font-monospace text-muted fw-bold">{{ $loop->iteration }}</td>
                                                    <td class="text-secondary fw-medium">{{ $sumber }}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="2" class="text-center text-muted py-3">
                                                <i class="fa-solid fa-circle-info me-1"></i> Tiada sumber kewangan dinyatakan.
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Seksyen 4: Maklumat Penduduk & Aktiviti Surau -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header text-white fw-bold py-2.5" style="background: linear-gradient(135deg, #1e3a8a 0%, #0369a1 100%);">
                    4. Maklumat Penduduk & Aktiviti Surau
                </div>
                <div class="card-body">
                    
                    <!-- Sub-Seksyen A: Statistik Penduduk Islam -->
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                        <i class="fa-solid fa-people-group me-1 text-success"></i> Statisik Penduduk Islam
                    </h6>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light">
                                <div class="text-muted small fw-semibold mb-1">Lelaki</div>
                                <div class="h5 mb-0 fw-bold text-dark font-monospace">
                                    {{ number_format($application->penduduk_lelaki ?? 0) }} <span class="fs-6 fw-normal text-muted">Orang</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light">
                                <div class="text-muted small fw-semibold mb-1">Perempuan</div>
                                <div class="h5 mb-0 fw-bold text-dark font-monospace">
                                    {{ number_format($application->penduduk_perempuan ?? 0) }} <span class="fs-6 fw-normal text-muted">Orang</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-success bg-opacity-10 border-success border-opacity-25">
                                <div class="text-success small fw-bold mb-1">Jumlah Keseluruhan</div>
                                <div class="h5 mb-0 fw-bold text-success font-monospace">
                                    @php
                                        $totalPenduduk = ($application->penduduk_lelaki ?? 0) + ($application->penduduk_perempuan ?? 0);
                                    @endphp
                                    {{ number_format($totalPenduduk) }} <span class="fs-6 fw-normal text-success">Orang</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sub-Seksyen B: Kekerapan Aktiviti Surau (Sebulan) -->
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                        <i class="fa-solid fa-book-quran me-1 text-success"></i> Kekerapan Aktiviti & Pengajian (Sebulan)
                    </h6>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <strong>Kelas Pengajian:</strong>
                            <div class="text-secondary mt-1">
                                <span class="badge bg-light text-dark border font-monospace fs-6">
                                    {{ $application->kelas_pengajian_sebulan ?? 0 }}
                                </span> kali sebulan
                            </div>
                        </div>

                        <div class="col-md-4">
                            <strong>Ceramah / Syarahan Umum:</strong>
                            <div class="text-secondary mt-1">
                                <span class="badge bg-light text-dark border font-monospace fs-6">
                                    {{ $application->ceramah_sebulan ?? 0 }}
                                </span> kali sebulan
                            </div>
                        </div>

                        <div class="col-md-4">
                            <strong>Kelas Didikan Kanak-Kanak:</strong>
                            <div class="text-secondary mt-1">
                                <span class="badge bg-light text-dark border font-monospace fs-6">
                                    {{ $application->kelas_kanak_sebulan ?? 0 }}
                                </span> kali sebulan
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Seksyen 5: Ahli Jawatankuasa (Jadual Bertingkat UI Baharu) -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header text-white fw-bold py-2.5" style="background: linear-gradient(135deg, #1e3a8a 0%, #0369a1 100%);">
                    5. Senarai Ahli Jawatankuasa (AJK) Surau
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0 small">
                            <thead class="bg-light text-secondary small text-uppercase">
                                <tr>
                                    <th class="ps-3 text-center" style="width: 40px;">Bil.</th>
                                    <th>Maklumat Peribadi</th>
                                    <th class="text-center">Jawatan</th>
                                    <th>Maklumat Untuk Dihubungi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($application->committees as $index => $ajk)
                                    @php
                                        $isApplicant = $ajk->is_applicant || ($application->pemohon_ic == ($ajk->ic_no ?? $ajk->ic ?? $ajk->ajk_ic_no));
                                        
                                        // Dapatkan teks jawatan & tetapkan warna badge secara dinamik
                                        $jawatanNama = $ajk->jawatan ?? $ajk->ajk_jawatan ?? 'AJK';
                                        $badgeJawatanColor = match(strtolower(trim($jawatanNama))) {
                                            'pengerusi', 'pengerusi surau' => 'bg-danger text-danger border-danger',
                                            'timbalan pengerusi', 'naib pengerusi' => 'bg-warning text-dark border-warning',
                                            'setiausaha', 'setiausaha surau' => 'bg-primary text-primary border-primary',
                                            'penolong setiausaha' => 'bg-info text-info border-info',
                                            'bendahari', 'bendahari surau' => 'bg-success text-success border-success',
                                            default => 'bg-secondary text-secondary border-secondary'
                                        };
                                    @endphp
                                    
                                    <!-- Baris Maklumat Utama -->
                                    <tr class="{{ $isApplicant ? 'bg-success bg-opacity-10' : '' }} border-bottom-0">
                                        <td class="ps-3 text-center font-monospace text-muted fw-bold" rowspan="2">{{ $index + 1 }}</td>
                                        
                                        <!-- Kolum 1: Maklumat Peribadi (Nama, IC, Status Pemohon Utama) -->
                                        <td>
                                            <div class="text-muted font-monospace">
                                                <span>Nama: {{ $ajk->nama ?? $ajk->ajk_nama }}</span>
                                            </div>
                                            <div class="text-muted font-monospace">
                                                No. KP: {{ $ajk->ic_no ?? $ajk->ic ?? $ajk->ajk_ic_no ?? '-' }}
                                            </div>
                                            @if($isApplicant)
                                                <span class="badge bg-success px-2 py-0.5" style="font-size: 0.7rem;">
                                                    <i class="fa-solid fa-user-check me-1"></i> Pemohon Utama
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Kolum 2: Jawatan -->
                                        <td class="font-monospace text-dark text-center">
                                            <span class="badge bg-opacity-10 border border-opacity-25 font-monospace {{ $badgeJawatanColor }} ">
                                                {{ $jawatanNama }}
                                            </span>
                                        </td>

                                        <!-- Kolum 3: No. Tel -->
                                        <td class="font-monospace text-dark">
                                            No. Tel: {{ $ajk->no_tel ?? $ajk->ajk_no_tel ?? '-' }}<br>
                                            E-Mel: {{ $ajk->email ?? $ajk->ajk_email ?? '-' }}
                                        </td>
                                    </tr>

                                    <!-- Baris Sub-Row Khusus Alamat Rumah -->
                                    <tr class="{{ $isApplicant ? 'bg-success bg-opacity-10' : 'bg-light bg-opacity-50' }}">
                                        <td colspan="4" class="py-2 text-secondary border-top-0">
                                            <small class="fw-semibold text-dark"> Alamat Rumah:</small>
                                            <span class="text-dark">{{ $ajk->alamat ?? $ajk->ajk_alamat ?? '-' }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <i class="fa-solid fa-folder-xmark me-1"></i> Tiada rekod AJK ditemui.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Seksyen 6: Dokumen Sokongan & Minit Mesyuarat -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header text-white fw-bold py-2.5" style="background: linear-gradient(135deg, #1e3a8a 0%, #0369a1 100%);">
                    6. Dokumen Sokongan & Minit Mesyuarat
                </div>
                <div class="card-body">
                    
                    <!-- Sub-Seksyen A: Dokumen Sokongan (application_documents) -->
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                        <i class="fa-solid fa-file-lines me-1 text-success"></i> Fail Dokumen Sokongan
                    </h6>
                    
                    <div class="row g-3 mb-4">
                        @if(isset($application->documents) && $application->documents->count() > 0)
                            @php
                                // 1. Semak jika terdapat rekod salinan IC versi gabung (Lama)
                                $oldIcDoc = $application->documents->first(function($doc) {
                                    $jenis = strtoupper($doc->jenis_dokumen ?? $doc->nama_dokumen ?? '');
                                    return $jenis === 'SALINAN_IC_AJK' || $jenis === 'SALINAN IC AJK';
                                });

                                // 2. Formatkan pemetaan tajuk untuk paparan mesra pengguna
                                $labelMapping = [
                                    'IC_PENGERUSI'      => 'Salinan IC Pengerusi',
                                    'IC_SETIAUSAHA'     => 'Salinan IC Setiausaha',
                                    'IC_BENDAHARI'      => 'Salinan IC Bendahari',
                                    'PENGESAHAN_KIBLAT' => 'Surat Pengesahan Arah Kiblat',
                                    'SALINAN_IC_AJK'    => 'Salinan IC AJK (Gabungan)',
                                ];
                            @endphp

                            @foreach($application->documents as $doc)
                                @php
                                    $jenisRaw = strtoupper($doc->jenis_dokumen ?? $doc->nama_dokumen ?? '');
                                    
                                    // KELUARKAN jika sistem mengesan data baharu (IC Pengerusi/Setiausaha/Bendahari) TETAPI fail lama SALINAN_IC_AJK juga ada
                                    // (Mencegah paparan bertindih)
                                    if ($oldIcDoc && in_array($jenisRaw, ['IC_PENGERUSI', 'IC_SETIAUSAHA', 'IC_BENDAHARI'])) {
                                        continue;
                                    }

                                    // Dapatkan tajuk paparan
                                    $tajukDokumen = $labelMapping[$jenisRaw] ?? ucwords(str_replace('_', ' ', strtolower($jenisRaw)));
                                    $filePath = $doc->fail_path ?? $doc->file_path;
                                    $ext = $filePath ? strtoupper(pathinfo($filePath, PATHINFO_EXTENSION)) : 'PDF/Imej';
                                @endphp

                                <div class="col-md-6">
                                    <div class="p-3 border rounded bg-light d-flex justify-content-between align-items-center">
                                        <div class="overflow-hidden me-2">
                                            <div class="fw-bold small text-dark text-truncate">
                                                {{ $tajukDokumen }}
                                            </div>
                                            <div class="text-muted small">
                                                Format: <span class="badge bg-light text-dark border font-monospace">{{ $ext }}</span>
                                            </div>
                                        </div>
                                        @if($filePath)
                                            <a href="{{ asset('storage/' . $filePath) }}" target="_blank" class="btn btn-sm btn-outline-success flex-shrink-0">
                                                <i class="fa-solid fa-download me-1"></i> Lihat Fail
                                            </a>
                                        @else
                                            <span class="badge bg-secondary flex-shrink-0">Tiada Fail</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="col-12">
                                <div class="p-3 border rounded bg-light text-center text-muted small">
                                    <i class="fa-solid fa-folder-xmark me-1"></i> Tiada fail dokumen sokongan dimuat naik.
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Sub-Seksyen B: Minit Mesyuarat dengan Butang Sahkan -->
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                        <i class="fa-solid fa-clipboard-list me-1 text-success"></i> Minit Mesyuarat Penubuhan
                    </h6>

                    <div class="row g-3">
                        @if(isset($application->meetingMinutes) && $application->meetingMinutes->count() > 0)
                            @foreach($application->meetingMinutes as $minut)
                                @php
                                    $minutPath = $minut->fail_path ?? $minut->file_path;
                                @endphp
                                <div class="col-12">
                                    <div class="p-3 border rounded bg-light d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                                        <div class="overflow-hidden">
                                            <div class="fw-bold small text-dark">
                                                Minit Mesyuarat Penubuhan {{ $minut->tahun_minit ? "({$minut->tahun_minit})" : '' }}
                                            </div>
                                           <div class="text-muted small">
                                                Status Pengesahan: 
                                                @if($minut->status_pengesahan === 'DISAHKAN')
                                                    <span class="badge bg-success"><i class="fa-solid fa-check me-1"></i> DISAHKAN</span>
                                                @elseif($minut->status_pengesahan === 'DITOLAK')
                                                    <span class="badge bg-danger"><i class="fa-solid fa-xmark me-1"></i> DITOLAK</span>
                                                @else
                                                    <span class="badge bg-warning text-dark"><i class="fa-solid fa-clock me-1"></i> DALAM SEMAKAN</span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Tindakan Muat Turun & Borang Pengesahan Mini -->
                                        <div class="d-flex align-items-center gap-2">
                                            @if($minutPath)
                                                <a href="{{ asset('storage/' . $minutPath) }}" target="_blank" class="btn btn-sm btn-outline-dark">
                                                    <i class="fa-solid fa-download me-1"></i> Lihat Fail
                                                </a>
                                            @endif

                                            
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="col-12">
                                <div class="p-3 border rounded bg-light text-center text-muted small">
                                    <i class="fa-solid fa-folder-xmark me-1"></i> Tiada fail minit mesyuarat dimuat naik.
                                </div>
                            </div>
                        @endif
                    </div>

                </div>
            </div>

            <!-- Seksyen 7: Akuan Pengesahan Pemohon -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header text-white fw-bold py-2.5" style="background: linear-gradient(135deg, #1e3a8a 0%, #0369a1 100%);">
                    7. Akuan Pengesahan Pemohon
                </div>
                <div class="card-body">
                    <div class="alert alert-success bg-success bg-opacity-10 border border-success border-opacity-25 mb-0 p-3">
                        <div class="d-flex align-items-start">
                            <i class="fa-solid fa-circle-check text-success fa-lg me-3 mt-1"></i>
                            <div>
                                <h6 class="fw-bold text-success mb-1">Pengesahan Diperakukan oleh Pemohon</h6>
                                <p class="small text-secondary mb-0">
                                    Pemohon (<strong>{{ $application->pemohon_nama }}</strong>) telah mengesahkan bahawa segala maklumat yang diberikan di dalam permohonan pendaftaran ini adalah sah, benar, dan betul semasa penghantaran borang.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>


        {{-- =====================================================
            PANEL TINDAKAN PENTADBIR MASJID
        ===================================================== --}}

        @php
            $masjidConfirmation = $confirmation ?? null;
            $masjidStatus = $masjidConfirmation?->status;
        @endphp

        <div class="col-lg-4">

            <div class="card border-0 shadow-sm rounded-3 sticky-top"
                style="top: 20px;">

                {{-- HEADER --}}
                <div class="card-header text-white fw-bold py-3" style="background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%);">
                    <i class="fa-solid fa-gavel me-2"></i>
                    Tindakan Pentadbir Masjid
                </div>

                <div class="card-body p-4">

                    {{-- =====================================================
                        STATUS SEMASA
                    ====================================================== --}}
                    <div class="mb-4">

                        <label class="form-label fw-semibold small mb-2">
                            Status Semasa
                        </label>

                        @if($masjidStatus === 'APPROVED')

                            <div class="alert alert-success mb-0 py-2">
                                <i class="fa-solid fa-circle-check me-2"></i>
                                <strong>DISOKONG / DISAHKAN</strong>
                            </div>

                        @elseif($masjidStatus === 'REJECTED')

                            <div class="alert alert-danger mb-0 py-2">
                                <i class="fa-solid fa-circle-xmark me-2"></i>
                                <strong>DITOLAK</strong>
                            </div>

                        @else

                            <div class="alert alert-warning mb-0 py-2">
                                <i class="fa-solid fa-clock me-2"></i>
                                <strong>DALAM SEMAKAN</strong>
                            </div>

                        @endif

                    </div>


                    {{-- =====================================================
                        BORANG TINDAKAN
                    ====================================================== --}}
                    @if(!$masjidStatus || !in_array($masjidStatus, ['APPROVED', 'REJECTED']))

                        {{-- =================================================
                            CATATAN / ULASAN
                        ================================================== --}}
                        <div class="mb-4">

                            <label for="catatan_masjid"
                                class="form-label fw-semibold small">

                                Catatan / Ulasan Pentadbir

                            </label>

                            <textarea
                                name="catatan_masjid"
                                id="catatan_masjid"
                                class="form-control"
                                rows="5"
                                maxlength="1000"
                                placeholder="Masukkan catatan atau ulasan pentadbir..."
                            >{{ old('catatan_masjid', $masjidConfirmation?->catatan ?? '') }}</textarea>

                            <div class="form-text">
                                <i class="fa-solid fa-circle-info me-1"></i>
                                Catatan adalah pilihan semasa sokongan.
                                Catatan wajib diisi sekiranya permohonan ditolak.
                                Maksimum 1,000 aksara.
                            </div>

                        </div>


                        {{-- =================================================
                            FORM SOKONG
                        ================================================== --}}
                        <form
                            action="{{ route('admin.masjid.pengesahan.sahkan', $application->id) }}"
                            method="POST"
                            id="formSahkan"
                            class="mb-2"
                        >

                            @csrf

                            <input
                                type="hidden"
                                name="catatan_masjid"
                                id="catatan_sahkan_hidden"
                                value=""
                            >

                            <button
                                type="button"
                                id="btnSahkan"
                                class="btn btn-success w-100 fw-bold py-2"
                            >
                                <i class="fa-solid fa-circle-check me-1"></i>
                                Sokong Permohonan
                            </button>

                        </form>


                        {{-- =================================================
                            FORM TOLAK
                        ================================================== --}}
                        <form
                            action="{{ route('admin.masjid.pengesahan.tolak', $application->id) }}"
                            method="POST"
                            id="formTolak"
                        >

                            @csrf

                            <input
                                type="hidden"
                                name="catatan_masjid"
                                id="catatan_tolak_hidden"
                                value=""
                            >

                            <button
                                type="button"
                                id="btnTolak"
                                class="btn btn-outline-danger w-100 fw-bold py-2"
                            >
                                <i class="fa-solid fa-circle-xmark me-1"></i>
                                Tolak Permohonan
                            </button>

                        </form>


                    @else

                        {{-- =================================================
                            KEPUTUSAN TELAH DIBUAT
                        ================================================== --}}

                        @if($masjidConfirmation?->catatan)

                            <div class="mb-4">

                                <label class="form-label fw-semibold small">
                                    Catatan / Ulasan Pentadbir
                                </label>

                                <div class="p-3 bg-light border rounded small text-secondary">

                                    <i class="fa-solid fa-comment-dots text-success me-2"></i>

                                    {{ $masjidConfirmation->catatan }}

                                </div>

                            </div>

                        @else

                            <div class="mb-4">

                                <div class="p-3 bg-light border rounded text-muted small">

                                    <i class="fa-solid fa-comment-slash me-2"></i>

                                    Tiada catatan direkodkan.

                                </div>

                            </div>

                        @endif

                    @endif


                    {{-- =====================================================
                        TARIKH SEMAKAN
                    ====================================================== --}}
                    @if($masjidConfirmation?->confirmed_at)

                        <div class="mt-4 p-3 bg-light rounded border text-muted small">

                            <strong>
                                <i class="fa-solid fa-calendar-days me-1"></i>
                                Tarikh Semakan:
                            </strong>

                            <br>

                            {{ $masjidConfirmation->confirmed_at->format('d/m/Y h:i A') }}

                        </div>

                    @endif

                </div>

            </div>

        </div>

    </div>

</div>


<script>

    document.addEventListener('DOMContentLoaded', function () {

        /*
        |--------------------------------------------------------------------------
        | ELEMENT
        |--------------------------------------------------------------------------
        */

        const catatanInput = document.getElementById('catatan_masjid');

        const btnSahkan = document.getElementById('btnSahkan');
        const formSahkan = document.getElementById('formSahkan');
        const hiddenSahkan = document.getElementById('catatan_sahkan_hidden');

        const btnTolak = document.getElementById('btnTolak');
        const formTolak = document.getElementById('formTolak');
        const hiddenTolak = document.getElementById('catatan_tolak_hidden');


        /*
        |--------------------------------------------------------------------------
        | HELPER SWEETALERT
        |--------------------------------------------------------------------------
        |
        | Jangan simpan hasSwal semasa page load.
        | Kita periksa window.Swal ketika diperlukan.
        |
        */

        function sweetAlertAvailable() {

            return typeof window.Swal !== 'undefined';

        }


        /*
        |--------------------------------------------------------------------------
        | ESCAPE HTML
        |--------------------------------------------------------------------------
        |
        | Elakkan catatan pengguna dimasukkan terus ke dalam HTML SweetAlert.
        |
        */

        function escapeHtml(text) {

            const div = document.createElement('div');

            div.textContent = text;

            return div.innerHTML;

        }


        /*
        |--------------------------------------------------------------------------
        | SUCCESS MESSAGE
        |--------------------------------------------------------------------------
        */

        @if(session('success'))

            if (sweetAlertAvailable()) {

                window.Swal.fire({

                    icon: 'success',

                    title: 'Berjaya!',

                    text: @json(session('success')),

                    confirmButtonColor: '#198754',

                    confirmButtonText:
                        '<i class="fa-solid fa-check me-1"></i> OK',

                    allowOutsideClick: false,

                    allowEscapeKey: true

                });

            } else {

                alert(@json(session('success')));

            }

        @endif


        /*
        |--------------------------------------------------------------------------
        | ERROR MESSAGE
        |--------------------------------------------------------------------------
        */

        @if(session('error'))

            if (sweetAlertAvailable()) {

                window.Swal.fire({

                    icon: 'error',

                    title: 'Tidak Berjaya',

                    text: @json(session('error')),

                    confirmButtonColor: '#dc3545',

                    confirmButtonText:
                        '<i class="fa-solid fa-check me-1"></i> OK',

                    allowOutsideClick: false,

                    allowEscapeKey: true

                });

            } else {

                alert(@json(session('error')));

            }

        @endif


        /*
        |--------------------------------------------------------------------------
        | SAHKAN / SOKONG PERMOHONAN
        |--------------------------------------------------------------------------
        */

        if (btnSahkan && formSahkan) {

            btnSahkan.addEventListener('click', function () {

                /*
                |------------------------------------------------------------------
                | Ambil catatan terkini
                |------------------------------------------------------------------
                */

                const catatan = catatanInput
                    ? catatanInput.value.trim()
                    : '';


                /*
                |------------------------------------------------------------------
                | Simpan catatan ke hidden input
                |------------------------------------------------------------------
                */

                if (hiddenSahkan) {

                    hiddenSahkan.value = catatan;

                }


                /*
                |------------------------------------------------------------------
                | SWEETALERT
                |------------------------------------------------------------------
                */

                if (sweetAlertAvailable()) {

                    window.Swal.fire({

                        icon: 'question',

                        title: 'Sahkan Permohonan?',

                        html: `
                            <div class="text-start">

                                <p class="mb-3">
                                    Adakah anda pasti mahu
                                    <strong class="text-success">
                                        MENGESAHKAN
                                    </strong>
                                    permohonan ini?
                                </p>

                                ${
                                    catatan
                                    ?
                                    `
                                        <div class="alert alert-light border small mb-0">

                                            <div class="fw-bold text-dark mb-1">
                                                <i class="fa-solid fa-comment-dots me-1 text-success"></i>
                                                Catatan Pentadbir
                                            </div>

                                            <div class="text-secondary"
                                                style="white-space: pre-wrap;">
                                                ${escapeHtml(catatan)}
                                            </div>

                                        </div>
                                    `
                                    :
                                    `
                                        <div class="alert alert-light border small mb-0 text-muted">

                                            <i class="fa-solid fa-circle-info me-1"></i>

                                            Tiada catatan dimasukkan.

                                        </div>
                                    `
                                }

                            </div>
                        `,

                        showCancelButton: true,

                        confirmButtonColor: '#198754',

                        cancelButtonColor: '#6c757d',

                        confirmButtonText:
                            '<i class="fa-solid fa-check me-1"></i> Ya, Sokong',

                        cancelButtonText:
                            '<i class="fa-solid fa-xmark me-1"></i> Batal',

                        reverseButtons: true,

                        focusCancel: true,

                        allowOutsideClick: false

                    }).then(function (result) {

                        if (!result.isConfirmed) {

                            return;

                        }


                        /*
                        |----------------------------------------------------------
                        | Pastikan catatan masih terkini
                        |----------------------------------------------------------
                        */

                        if (hiddenSahkan) {

                            hiddenSahkan.value = catatan;

                        }


                        /*
                        |----------------------------------------------------------
                        | Elakkan double submit
                        |----------------------------------------------------------
                        */

                        btnSahkan.disabled = true;

                        if (btnTolak) {

                            btnTolak.disabled = true;

                        }


                        btnSahkan.innerHTML = `
                            <span
                                class="spinner-border spinner-border-sm me-2"
                                role="status"
                                aria-hidden="true">
                            </span>
                            Memproses...
                        `;


                        /*
                        |----------------------------------------------------------
                        | Submit
                        |----------------------------------------------------------
                        */

                        formSahkan.submit();

                    });

                }

                /*
                |--------------------------------------------------------------------------
                | FALLBACK JIKA SWEETALERT TIADA
                |--------------------------------------------------------------------------
                */

                else {

                    const confirmed = confirm(
                        'Adakah anda pasti mahu mengesahkan permohonan ini?'
                    );


                    if (confirmed) {

                        if (hiddenSahkan) {

                            hiddenSahkan.value = catatan;

                        }

                        formSahkan.submit();

                    }

                }

            });

        }


        /*
        |--------------------------------------------------------------------------
        | TOLAK PERMOHONAN
        |--------------------------------------------------------------------------
        */

        if (btnTolak && formTolak) {

            btnTolak.addEventListener('click', function () {

                /*
                |------------------------------------------------------------------
                | Ambil catatan
                |------------------------------------------------------------------
                */

                const catatan = catatanInput
                    ? catatanInput.value.trim()
                    : '';


                /*
                |------------------------------------------------------------------
                | CATATAN WAJIB
                |------------------------------------------------------------------
                */

                if (!catatan) {

                    if (sweetAlertAvailable()) {

                        window.Swal.fire({

                            icon: 'warning',

                            title: 'Catatan diperlukan',

                            html: `
                                <div class="text-center">

                                    <p class="mb-2">
                                        Sila nyatakan
                                        <strong>sebab atau ulasan penolakan</strong>
                                        sebelum meneruskan.
                                    </p>

                                    <div class="text-muted small">
                                        Catatan ini akan direkodkan sebagai
                                        alasan penolakan permohonan.
                                    </div>

                                </div>
                            `,

                            confirmButtonColor: '#dc3545',

                            confirmButtonText:
                                '<i class="fa-solid fa-check me-1"></i> Baik',

                            allowOutsideClick: false

                        }).then(function () {

                            if (catatanInput) {

                                catatanInput.focus();

                            }

                        });

                    } else {

                        alert(
                            'Sila nyatakan sebab atau ulasan penolakan.'
                        );

                        if (catatanInput) {

                            catatanInput.focus();

                        }

                    }

                    return;

                }


                /*
                |------------------------------------------------------------------
                | Simpan catatan ke hidden input
                |------------------------------------------------------------------
                */

                if (hiddenTolak) {

                    hiddenTolak.value = catatan;

                }


                /*
                |------------------------------------------------------------------
                | SWEETALERT CONFIRMATION
                |------------------------------------------------------------------
                */

                if (sweetAlertAvailable()) {

                    window.Swal.fire({

                        icon: 'warning',

                        title: 'Tolak Permohonan?',

                        html: `
                            <div class="text-start">

                                <p class="mb-3">
                                    Adakah anda pasti mahu
                                    <strong class="text-danger">
                                        MENOLAK
                                    </strong>
                                    permohonan ini?
                                </p>

                                <div class="alert alert-danger bg-danger bg-opacity-10 border border-danger border-opacity-25 small mb-0">

                                    <div class="fw-bold text-danger mb-1">
                                        <i class="fa-solid fa-comment-dots me-1"></i>
                                        Catatan Penolakan
                                    </div>

                                    <div class="text-secondary"
                                        style="white-space: pre-wrap;">
                                        ${escapeHtml(catatan)}
                                    </div>

                                </div>

                            </div>
                        `,

                        showCancelButton: true,

                        confirmButtonColor: '#dc3545',

                        cancelButtonColor: '#6c757d',

                        confirmButtonText:
                            '<i class="fa-solid fa-xmark me-1"></i> Ya, Tolak',

                        cancelButtonText:
                            '<i class="fa-solid fa-arrow-left me-1"></i> Batal',

                        reverseButtons: true,

                        focusCancel: true,

                        allowOutsideClick: false

                    }).then(function (result) {

                        if (!result.isConfirmed) {

                            return;

                        }


                        /*
                        |----------------------------------------------------------
                        | Pastikan nilai terkini dihantar
                        |----------------------------------------------------------
                        */

                        if (hiddenTolak) {

                            hiddenTolak.value = catatan;

                        }


                        /*
                        |----------------------------------------------------------
                        | Elakkan double submit
                        |----------------------------------------------------------
                        */

                        btnTolak.disabled = true;

                        if (btnSahkan) {

                            btnSahkan.disabled = true;

                        }


                        btnTolak.innerHTML = `
                            <span
                                class="spinner-border spinner-border-sm me-2"
                                role="status"
                                aria-hidden="true">
                            </span>
                            Memproses...
                        `;


                        /*
                        |----------------------------------------------------------
                        | Submit
                        |----------------------------------------------------------
                        */

                        formTolak.submit();

                    });

                }

                /*
                |--------------------------------------------------------------------------
                | FALLBACK
                |--------------------------------------------------------------------------
                */

                else {

                    const confirmed = confirm(
                        'Adakah anda pasti mahu menolak permohonan ini?'
                    );


                    if (confirmed) {

                        if (hiddenTolak) {

                            hiddenTolak.value = catatan;

                        }

                        formTolak.submit();

                    }

                }

            });

        }

    });

</script>
@endsection