@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Header Halaman -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-file-invoice me-2 text-success"></i> Semakan Permohonan: {{ $application->reference_no }} @include('admin.permohonan._jenis_badge', ['application' => $application])
            </h4>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge bg-secondary font-monospace"><i class="fa-solid fa-calendar-days me-1"></i> Tarikh Hantar: {{ $application->created_at->format('d/m/Y h:i A') }}</span>

                @php
                    $jawiHeaderConf = $application->jawiConfirmation;
                @endphp

                @if($jawiHeaderConf?->status === 'APPROVED')
                    <span class="badge bg-success"><i class="fa-solid fa-building-columns me-1"></i> JAWI: DISAHKAN</span>
                @elseif($jawiHeaderConf?->status === 'REJECTED')
                    <span class="badge bg-danger"><i class="fa-solid fa-building-columns me-1"></i> JAWI: DITOLAK</span>
                @else
                    <span class="badge bg-warning text-dark"><i class="fa-solid fa-building-columns me-1"></i> JAWI: DALAM SEMAKAN</span>
                @endif

                @if($application->appointment)
                    <span class="badge bg-primary text-white"><i class="fa-solid fa-calendar-check me-1"></i> TEMUJANJI: {{ $application->appointment->tarikh_temujanji?->format('d/m/Y') }}</span>
                @endif

                @if($application->status === 'APPROVED')
                    <span class="badge bg-success"><i class="fa-solid fa-circle-check me-1"></i> KESELURUHAN: LULUS</span>
                @elseif($application->status === 'REJECTED')
                    <span class="badge bg-danger"><i class="fa-solid fa-circle-xmark me-1"></i> KESELURUHAN: DITOLAK</span>
                @else
                    <span class="badge bg-info text-dark"><i class="fa-solid fa-clock me-1"></i> MENUNGGU SEMAKAN ZON</span>
                @endif
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.jawi.pengesahan.cetak', $application->id) }}" target="_blank" class="btn btn-primary btn-sm fw-medium shadow-sm">
                <i class="fa-solid fa-print me-1"></i> Cetak Permohonan (PDF)
            </a>
            <a href="{{ route('admin.permohonan.index') }}" class="btn btn-outline-secondary btn-sm fw-medium">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Senarai
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm mb-4">
            <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
        </div>
    @endif

    <div class="row g-4">
        <!-- Ruang Paparan Kesemua Maklumat Permohonan -->
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

            <!-- =========================================================
                SEKsyen 6: DOKUMEN SOKONGAN & MINIT MESYUARAT
            ========================================================= -->

            <div class="card border-0 shadow-sm rounded-3 mb-4">

                <div class="card-header text-white fw-bold py-2.5" style="background: linear-gradient(135deg, #1e3a8a 0%, #0369a1 100%);">
                    6. Dokumen Sokongan & Minit Mesyuarat
                </div>

                <div class="card-body">

                    <!-- DOKUMEN SOKONGAN -->
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                        <i class="fa-solid fa-file-lines me-1 text-success"></i>
                        Fail Dokumen Sokongan
                    </h6>

                    <div class="row g-3 mb-4">

                        @if(isset($application->documents)
                            && $application->documents->count() > 0)

                            @foreach($application->documents as $doc)

                                @php
                                    $jenisRaw = strtoupper(
                                        $doc->jenis_dokumen
                                        ?? $doc->nama_dokumen
                                        ?? ''
                                    );

                                    $labelMapping = [
                                        'IC_PENGERUSI'      => 'Salinan IC Pengerusi',
                                        'IC_SETIAUSAHA'     => 'Salinan IC Setiausaha',
                                        'IC_BENDAHARI'      => 'Salinan IC Bendahari',
                                        'PENGESAHAN_KIBLAT' => 'Surat Pengesahan Arah Kiblat',
                                        'SALINAN_IC_AJK'    => 'Salinan IC AJK (Gabungan)',
                                    ];

                                    $tajukDokumen =
                                        $labelMapping[$jenisRaw]
                                        ?? ucwords(
                                            str_replace(
                                                '_',
                                                ' ',
                                                strtolower($jenisRaw)
                                            )
                                        );

                                    $filePath =
                                        $doc->fail_path
                                        ?? $doc->file_path;

                                    $ext = $filePath
                                        ? strtoupper(
                                            pathinfo(
                                                $filePath,
                                                PATHINFO_EXTENSION
                                            )
                                        )
                                        : 'PDF/Imej';
                                @endphp

                                <div class="col-md-6">

                                    <div class="p-3 border rounded bg-light
                                                d-flex justify-content-between
                                                align-items-center">

                                        <div class="overflow-hidden me-2">

                                            <div class="fw-bold small text-dark text-truncate">
                                                {{ $tajukDokumen }}
                                            </div>

                                            <div class="text-muted small">

                                                Format:

                                                <span class="badge bg-light text-dark border font-monospace">
                                                    {{ $ext }}
                                                </span>

                                            </div>

                                        </div>


                                        @if($filePath)

                                            <a
                                                href="{{ asset('storage/' . $filePath) }}"
                                                target="_blank"
                                                class="btn btn-sm btn-outline-success flex-shrink-0">

                                                <i class="fa-solid fa-eye me-1"></i>
                                                Lihat Fail

                                            </a>

                                        @else

                                            <span class="badge bg-secondary">
                                                Tiada Fail
                                            </span>

                                        @endif

                                    </div>

                                </div>

                            @endforeach

                        @else

                            <div class="col-12">

                                <div class="p-3 border rounded bg-light
                                            text-center text-muted small">

                                    <i class="fa-solid fa-folder-xmark me-1"></i>

                                    Tiada fail dokumen sokongan dimuat naik.

                                </div>

                            </div>

                        @endif

                    </div>


                    <!-- =====================================================
                        MINIT MESYUARAT — PAPARAN SAHAJA
                    ====================================================== -->

                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">

                        <i class="fa-solid fa-clipboard-list me-1 text-success"></i>

                        Minit Mesyuarat Penubuhan

                    </h6>


                    <div class="row g-3">

                        @if(isset($application->meetingMinutes)
                            && $application->meetingMinutes->count() > 0)

                            @foreach($application->meetingMinutes as $minut)

                                @php
                                    $minutPath =
                                        $minut->fail_path
                                        ?? $minut->file_path;
                                @endphp

                                <div class="col-12">

                                    <div class="p-3 border rounded bg-light
                                                d-flex flex-column
                                                flex-md-row
                                                justify-content-between
                                                align-items-md-center
                                                gap-3">

                                        <div>

                                            <div class="fw-bold small text-dark">

                                                Minit Mesyuarat Penubuhan

                                                @if($minut->tahun_minit)
                                                    ({{ $minut->tahun_minit }})
                                                @endif

                                            </div>


                                            <div class="text-muted small mt-1">

                                                Status Pengesahan:

                                                @if($minut->status_pengesahan === 'DISAHKAN')

                                                    <span class="badge bg-success">
                                                        <i class="fa-solid fa-check me-1"></i>
                                                        DISAHKAN
                                                    </span>

                                                @elseif($minut->status_pengesahan === 'DITOLAK')

                                                    <span class="badge bg-danger">
                                                        <i class="fa-solid fa-xmark me-1"></i>
                                                        DITOLAK
                                                    </span>

                                                @else

                                                    <span class="badge bg-warning text-dark">
                                                        <i class="fa-solid fa-clock me-1"></i>
                                                        DALAM SEMAKAN
                                                    </span>

                                                @endif

                                            </div>

                                        </div>


                                        @if($minutPath)

                                            <a
                                                href="{{ asset('storage/' . $minutPath) }}"
                                                target="_blank"
                                                class="btn btn-sm btn-outline-dark">

                                                <i class="fa-solid fa-eye me-1"></i>
                                                Lihat Fail

                                            </a>

                                        @endif

                                    </div>

                                </div>

                            @endforeach

                        @else

                            <div class="col-12">

                                <div class="p-3 border rounded bg-light
                                            text-center text-muted small">

                                    <i class="fa-solid fa-folder-xmark me-1"></i>

                                    Tiada fail minit mesyuarat dimuat naik.

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

        <!-- =========================================================
            PANEL KAWALAN / TINDAKAN
        ========================================================= -->
        <div class="col-lg-4">

            <div class="sticky-top" style="top: 20px;">

                <!-- =====================================================
                    1. PENTADBIR MASJID
                ====================================================== -->
                <div class="card border-0 shadow-sm rounded-3 mb-3 overflow-hidden">

                    <!-- HEADER -->
                    <div class="card-header text-white py-3" style="background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%);">
                        <div class="fw-bold">
                            <i class="fa-solid fa-mosque me-2"></i>
                            1. Pentadbir Masjid
                        </div>

                        <small class="opacity-75">
                            Semakan peringkat Masjid
                        </small>
                    </div>

                    <div class="card-body p-3">

                        @php
                            $masjidConfirmation = $application->masjidConfirmation;
                        @endphp

                        <!-- STATUS -->
                        <div class="mb-2">
                            <small class="text-muted">
                                Status Semakan
                            </small>

                            <div class="mt-1">

                                @if(($masjidConfirmation?->status ?? null) === 'APPROVED')

                                    <span class="badge bg-success">
                                        <i class="fa-solid fa-circle-check me-1"></i>
                                        DISAHKAN
                                    </span>

                                @elseif(($masjidConfirmation?->status ?? null) === 'REJECTED')

                                    <span class="badge bg-danger">
                                        <i class="fa-solid fa-circle-xmark me-1"></i>
                                        DITOLAK
                                    </span>

                                @else

                                    <span class="badge bg-warning text-dark">
                                        <i class="fa-solid fa-clock me-1"></i>
                                        DALAM SEMAKAN
                                    </span>

                                @endif

                            </div>
                        </div>

                        <!-- TARIKH -->
                        <div class="small text-muted mb-3">
                            <i class="fa-regular fa-calendar me-1"></i>
                            Tarikh:

                            {{ $masjidConfirmation?->confirmed_at
                                ? \Carbon\Carbon::parse($masjidConfirmation->confirmed_at)
                                    ->format('d/m/Y h:i A')
                                : '-' }}
                        </div>

                        <!-- OVERRIDE -->
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-success w-100"
                            onclick="overridePeringkat(
                                'masjid',
                                {{ $application->id }}
                            )">

                            <i class="fa-solid fa-pen-to-square me-1"></i>
                            Override Keputusan Masjid

                        </button>

                    </div>

                </div>


                <!-- =====================================================
                    2. PENTADBIR JAWI
                ====================================================== -->
                <div class="card border-0 shadow-sm rounded-3 mb-3 overflow-hidden">

                    <!-- HEADER -->
                    <div class="card-header text-white py-3" style="background: linear-gradient(135deg, #1e3a8a 0%, #0369a1 100%);">

                        <div class="fw-bold">
                            <i class="fa-solid fa-building-columns me-2"></i>
                            2. Pentadbir JAWI
                        </div>

                        <small class="opacity-75">
                            Semakan dan tindakan peringkat JAWI
                        </small>

                    </div>


                    <div class="card-body p-3">


                        <!-- =================================================
                            C. BUAT TEMUJANJI LAWATAN TAPAK
                        ================================================== -->

                        @php
                            $allAppointments = $application->appointments ?? collect();
                            $appointment = $allAppointments->first(); // Rekod terkini
                            $zonConf = $application->zonConfirmation;
                            $isZonConfirmed = $zonConf && in_array($zonConf->status, ['APPROVED', 'REJECTED']);
                            $hasAppointment = $appointment !== null;
                        @endphp

                        <div class="border rounded-3 mb-3 overflow-hidden">

                            <div class="bg-light px-3 py-2 border-bottom d-flex justify-content-between align-items-center">

                                <div class="fw-bold small">

                                    <i class="fa-solid fa-calendar-check text-primary me-2"></i>

                                    Temujanji Lawatan Tapak

                                </div>

                                @if($hasAppointment)
                                    <span class="badge bg-primary">
                                        <i class="fa-solid fa-check me-1"></i> {{ $allAppointments->count() }} Rekod
                                    </span>
                                @endif

                            </div>

                            <div class="p-3">

                                @if($hasAppointment)
                                    <!-- RINGKASAN TEMUJANJI TERKINI -->
                                    <div class="card border-primary border-opacity-25 bg-primary bg-opacity-10 rounded-3 mb-3 p-3 shadow-sm">
                                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-primary border-opacity-25">
                                            <div class="fw-bold text-primary small">
                                                <i class="fa-solid fa-calendar-check me-1"></i> Rekod Temujanji Terkini (Aktif)
                                            </div>
                                            <span class="badge bg-primary">AKTIF</span>
                                        </div>
                                        <div class="row g-2 small text-dark">
                                            <div class="col-6">
                                                <span class="text-muted d-block" style="font-size: 0.75rem;">Tarikh Temujanji:</span>
                                                <strong class="text-primary"><i class="fa-regular fa-calendar me-1"></i> {{ $appointment->tarikh_temujanji ? $appointment->tarikh_temujanji->format('d/m/Y') : '-' }}</strong>
                                            </div>
                                            <div class="col-6">
                                                <span class="text-muted d-block" style="font-size: 0.75rem;">Masa Temujanji:</span>
                                                <strong class="text-primary"><i class="fa-regular fa-clock me-1"></i> {{ $appointment->masa_temujanji ? \Carbon\Carbon::parse($appointment->masa_temujanji)->format('h:i A') : '-' }}</strong>
                                            </div>
                                            <div class="col-12 mt-2">
                                                <span class="text-muted d-block" style="font-size: 0.75rem;">Tempat / Lokasi:</span>
                                                <strong><i class="fa-solid fa-location-dot me-1 text-danger"></i> {{ $appointment->tempat }}</strong>
                                            </div>
                                            @if($appointment->catatan)
                                            <div class="col-12 mt-2">
                                                <span class="text-muted d-block" style="font-size: 0.75rem;">Catatan / Arahan:</span>
                                                <div class="bg-white p-2 rounded border border-secondary border-opacity-25 text-secondary small">{{ $appointment->catatan }}</div>
                                            </div>
                                            @endif
                                            @if($appointment->ditetapkanOleh)
                                            <div class="col-12 mt-2 text-muted" style="font-size: 0.72rem;">
                                                <i class="fa-solid fa-user-pen me-1"></i> Ditetapkan oleh: <strong>{{ $appointment->ditetapkanOleh->name }}</strong> ({{ $appointment->created_at ? $appointment->created_at->format('d/m/Y h:i A') : '' }})
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                @if($allAppointments->count() > 1)
                                    <!-- SEJARAH TEMUJANJI TERDAHULU -->
                                    <div class="accordion mb-3" id="accordionSejarahTemujanji">
                                        <div class="accordion-item border rounded-3 overflow-hidden shadow-sm">
                                            <h2 class="accordion-header" id="headingSejarah">
                                                <button class="accordion-button collapsed py-2 px-3 bg-light text-dark fw-bold small" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSejarah" aria-expanded="false" aria-controls="collapseSejarah">
                                                    <i class="fa-solid fa-clock-rotate-left me-2 text-secondary"></i> Sejarah Temujanji Terdahulu ({{ $allAppointments->count() - 1 }} Rekod Lama)
                                                </button>
                                            </h2>
                                            <div id="collapseSejarah" class="accordion-collapse collapse" aria-labelledby="headingSejarah" data-bs-parent="#accordionSejarahTemujanji">
                                                <div class="accordion-body p-2 bg-light">
                                                    @foreach($allAppointments->skip(1) as $index => $oldApt)
                                                        <div class="card mb-2 p-2.5 border-0 shadow-sm bg-white rounded-3 small">
                                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                                <strong class="text-secondary"><i class="fa-regular fa-calendar-days me-1"></i> {{ $oldApt->tarikh_temujanji?->format('d/m/Y') }}</strong>
                                                                <span class="badge bg-secondary opacity-75">REKOD LAMA</span>
                                                            </div>
                                                            <div class="text-dark">
                                                                <i class="fa-regular fa-clock me-1 text-muted"></i> {{ $oldApt->masa_temujanji ? \Carbon\Carbon::parse($oldApt->masa_temujanji)->format('h:i A') : '-' }} | 
                                                                <i class="fa-solid fa-location-dot me-1 text-muted"></i> {{ $oldApt->tempat }}
                                                            </div>
                                                            @if($oldApt->catatan)
                                                                <div class="text-muted mt-1 fst-italic">"{{ $oldApt->catatan }}"</div>
                                                            @endif
                                                            <div class="text-muted mt-1" style="font-size: 0.7rem;">
                                                                Ditetapkan oleh: {{ $oldApt->ditetapkanOleh?->name ?? 'Pentadbir' }} ({{ $oldApt->created_at?->format('d/m/Y h:i A') }})
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if($isZonConfirmed)
                                    <div class="alert alert-secondary py-2 px-3 small rounded-3 mb-3 border-0 bg-secondary bg-opacity-10 text-secondary">
                                        <i class="fa-solid fa-lock me-1"></i> Keputusan akhir telah dibuat bagi permohonan ini. Temujanji tidak boleh ditambah atau diubah semula.
                                    </div>
                                @elseif($hasAppointment)
                                    <div class="alert alert-info py-2 px-3 small rounded-3 mb-3 border-0 bg-info bg-opacity-10 text-info">
                                        <i class="fa-solid fa-circle-info me-1"></i> Borang di bawah adalah untuk menetapkan **temujanji baharu**. Rekod temujanji lama akan kekal disimpan dalam sejarah.
                                    </div>
                                @endif

                                <form
                                    action="{{ route(
                                        'admin.jawi.pengesahan.temujanji',
                                        $application->id
                                    ) }}"
                                    method="POST">

                                    @csrf


                                    <!-- TARIKH -->
                                    <div class="mb-3">

                                        <label
                                            for="tarikhTemujanji"
                                            class="form-label small fw-semibold">

                                            Tarikh Temujanji Baharu

                                        </label>

                                        <input
                                            type="date"
                                            name="tarikh_temujanji"
                                            id="tarikhTemujanji"
                                            class="form-control"
                                            value="{{ old('tarikh_temujanji') }}"
                                            {{ $isZonConfirmed ? 'disabled' : 'required' }}>

                                    </div>


                                    <!-- MASA -->
                                    <div class="mb-3">

                                        <label
                                            for="masaTemujanji"
                                            class="form-label small fw-semibold">

                                            Masa Temujanji Baharu

                                        </label>

                                        <input
                                            type="time"
                                            name="masa_temujanji"
                                            id="masaTemujanji"
                                            class="form-control"
                                            value="{{ old('masa_temujanji') }}"
                                            {{ $isZonConfirmed ? 'disabled' : 'required' }}>

                                    </div>


                                    <!-- TEMPAT -->
                                    <div class="mb-3">

                                        <label
                                            for="tempatTemujanji"
                                            class="form-label small fw-semibold">

                                            Tempat / Lokasi

                                        </label>

                                        <input
                                            type="text"
                                            name="tempat_temujanji"
                                            id="tempatTemujanji"
                                            class="form-control"
                                            value="{{ old('tempat_temujanji', $appointment?->tempat ?? 'Pejabat JAWI / Tapak Surau') }}"
                                            placeholder="Contoh: Pejabat JAWI / Tapak Surau"
                                            {{ $isZonConfirmed ? 'disabled' : 'required' }}>

                                    </div>


                                    <!-- CATATAN / ARAHAN TEMUJANJI -->
                                    <div class="mb-3">

                                        <label
                                            for="catatanTemujanji"
                                            class="form-label small fw-semibold">

                                            Catatan / Arahan Baharu (Opsional)

                                        </label>

                                        <textarea
                                            name="catatan"
                                            id="catatanTemujanji"
                                            class="form-control"
                                            rows="2"
                                            placeholder="Sila bawa dokumen sokongan berkaitan..."
                                            {{ $isZonConfirmed ? 'disabled' : '' }}>{{ old('catatan') }}</textarea>

                                    </div>


                                    <!-- BUTANG -->
                                    @if($isZonConfirmed)
                                        <button
                                            type="button"
                                            class="btn btn-secondary w-100 fw-bold"
                                            disabled>

                                            <i class="fa-solid fa-lock me-1"></i>
                                            Temujanji Dikunci (Keputusan Akhir Telah Dibuat)

                                        </button>
                                    @else
                                        <button
                                            type="submit"
                                            class="btn btn-primary w-100 fw-bold shadow-sm">

                                            <i class="fa-solid fa-calendar-plus me-1"></i>
                                            Buat Temujanji Baharu
                                        </button>
                                    @endif

                                </form>

                            </div>

                        </div>

                        <!-- =================================================
                            D. KEPUTUSAN AKHIR SELEPAS LAWATAN TAPAK
                        ================================================== -->

                        @php
                            $keputusanAkhir = $zonConf && in_array($zonConf->status, ['APPROVED', 'REJECTED']) ? $zonConf : null;
                        @endphp

                        <div class="border rounded-3 mb-3 overflow-hidden">

                            <div class="bg-light px-3 py-2 border-bottom">
                                <div class="fw-bold small">
                                    <i class="fa-solid fa-gavel text-success me-2"></i>
                                    Keputusan Akhir Permohonan
                                </div>
                            </div>

                            <div class="p-3">

                                @if($keputusanAkhir)

                                    <div class="alert {{ $keputusanAkhir->status === 'APPROVED' ? 'alert-success' : 'alert-danger' }} border-0 small mb-3">
                                        <div class="fw-bold mb-1">
                                            @if($keputusanAkhir->status === 'APPROVED')
                                                <i class="fa-solid fa-circle-check me-1"></i> Permohonan Telah Diluluskan
                                            @else
                                                <i class="fa-solid fa-circle-xmark me-1"></i> Permohonan Telah Ditolak
                                            @endif
                                        </div>
                                        @if($keputusanAkhir->confirmed_at)
                                            Pada {{ \Carbon\Carbon::parse($keputusanAkhir->confirmed_at)->format('d/m/Y h:i A') }}
                                        @endif
                                    </div>

                                    @if(!empty($keputusanAkhir->catatan))
                                        <div class="p-2 bg-light rounded border small mb-3">
                                            <strong>Catatan / Laporan Lawatan Tapak:</strong>
                                            <div class="text-secondary mt-1 fst-italic">"{{ $keputusanAkhir->catatan }}"</div>
                                        </div>
                                    @endif

                                    <button type="button" class="btn btn-secondary btn-sm w-100 fw-bold" disabled>
                                        <i class="fa-solid fa-lock me-1"></i> Keputusan Telah Disimpan &amp; Dikunci
                                    </button>

                                @else

                                    @if(!$hasAppointment)
                                        <div class="alert alert-warning border-0 small mb-3">
                                            <i class="fa-solid fa-triangle-exclamation me-1"></i>
                                            Sila tetapkan temujanji lawatan tapak terlebih dahulu. Keputusan akhir hanya boleh dibuat selepas lawatan tapak.
                                        </div>
                                    @endif

                                    <form action="{{ route('admin.jawi.pengesahan.keputusanAkhir', $application->id) }}" method="POST" id="formKeputusanAkhir">
                                        @csrf
                                        @method('PUT')

                                        @if($hasAppointment)
                                            <div class="form-check p-3 bg-light rounded border mb-3">
                                                <input class="form-check-input ms-0 me-2" type="checkbox" name="pengesahan_lawatan" id="pengesahanLawatan" value="1" checked>
                                                <label class="form-check-label small fw-semibold text-dark" for="pengesahanLawatan">
                                                    <i class="fa-solid fa-clipboard-check text-success me-1"></i>
                                                    Saya mengesahkan bahawa lawatan tapak ke <u>{{ $application->nama_surau }}</u> telah selesai dijalankan.
                                                </label>
                                            </div>
                                        @endif

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small">Status Kelulusan</label>
                                            <select name="status" id="selectStatusAkhir" class="form-select form-select-sm" {{ !$hasAppointment ? 'disabled' : 'required' }}>
                                                <option value="">-- Pilih Keputusan --</option>
                                                <option value="APPROVED" selected>LULUS / SAHKAN PERMOHONAN</option>
                                                <option value="REJECTED">TOLAK PERMOHONAN</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small">Catatan / Laporan Hasil Lawatan Tapak</label>
                                            <textarea name="catatan_zon" class="form-control form-control-sm" rows="3" placeholder="Masukkan ulasan lawatan tapak, syor kelulusan, atau catatan berkaitan..." {{ !$hasAppointment ? 'disabled' : '' }}>Lawatan tapak telah dilaksanakan dan premis surau didapati mematuhi syarat yang ditetapkan.</textarea>
                                        </div>

                                        <div class="p-2 bg-light rounded border small mb-3 text-secondary">
                                            <div class="fw-bold text-dark mb-1"><i class="fa-solid fa-wand-magic-sparkles text-primary me-1"></i> Tindakan Automatik Selepas Lulus:</div>
                                            <ul class="ps-3 mb-0" style="font-size:0.78rem;">
                                                <li>Minit mesyuarat yang masih dalam semakan akan disahkan.</li>
                                                <li>Pemohon didaftarkan sebagai pengguna sistem (Pentadbir Surau).</li>
                                                <li><strong>E-mel 1:</strong> E-mel kelulusan permohonan dihantar kepada pemohon (CC kesemua pentadbir).</li>
                                                <li><strong>E-mel 2:</strong> E-mel link pengaktifan akaun dihantar kepada pemohon (BCC Pentadbir Sistem).</li>
                                            </ul>
                                        </div>

                                        @if($hasAppointment)
                                            <button type="submit" class="btn btn-success w-100 fw-bold py-2 shadow-sm" style="background-color:#15803d; border-color:#15803d;">
                                                <i class="fa-solid fa-check-double me-1"></i> Simpan Keputusan Akhir
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-secondary w-100 fw-bold py-2" disabled>
                                                <i class="fa-solid fa-lock me-1"></i> Menunggu Lawatan Tapak Ditetapkan
                                            </button>
                                        @endif
                                    </form>

                                @endif

                            </div>
                        </div>

                        {{-- Seksyen Sahkan Minit Mesyuarat & Semak Permohonan disembunyikan buat sementara.
                             Tukar kepada true untuk memaparkannya semula. --}}
                        @php $paparMinitDanSemakan = false; @endphp

                        @if($paparMinitDanSemakan)
                        <!-- =================================================
                            A. SAHKAN MINIT MESYUARAT
                        ================================================== -->

                        <div class="border rounded-3 mb-3 overflow-hidden">

                            <div class="bg-light px-3 py-2 border-bottom">

                                <div class="fw-bold small">

                                    <i class="fa-solid fa-file-signature text-primary me-2"></i>

                                    Sahkan Minit Mesyuarat

                                </div>

                            </div>


                            <div class="p-3">

                                @if(
                                    isset($application->meetingMinutes)
                                    && $application->meetingMinutes->count() > 0
                                )

                                    @foreach($application->meetingMinutes as $minut)

                                        @php
                                            $minutPath =
                                                $minut->fail_path
                                                ?? $minut->file_path;
                                        @endphp


                                        <div class="border rounded-3 p-3 mb-2">

                                            <!-- TAJUK -->
                                            <div class="fw-bold small text-dark mb-2">

                                                <i class="fa-solid fa-file-lines me-1 text-muted"></i>

                                                Minit Mesyuarat Penubuhan

                                                @if($minut->tahun_minit)
                                                    ({{ $minut->tahun_minit }})
                                                @endif

                                            </div>


                                            <!-- STATUS -->
                                            <div class="mb-2">

                                                <small class="text-muted d-block mb-1">
                                                    Status Pengesahan
                                                </small>

                                                @if(!empty($minut->catatan_semakan))

                                                    <div class="alert alert-light border mt-2 mb-2">

                                                        <div class="fw-bold small text-dark mb-1">
                                                            <i class="fa-solid fa-comment-dots text-primary me-1"></i>
                                                            Catatan Semakan JAWI
                                                        </div>

                                                        <div class="small text-muted">
                                                            {{ $minut->catatan_semakan }}
                                                        </div>

                                                    </div>

                                                @endif

                                                @if($minut->status_pengesahan === 'DISAHKAN')

                                                    <span class="badge bg-success">
                                                        <i class="fa-solid fa-check me-1"></i>
                                                        DISAHKAN
                                                    </span>

                                                @elseif($minut->status_pengesahan === 'DITOLAK')

                                                    <span class="badge bg-danger">
                                                        <i class="fa-solid fa-xmark me-1"></i>
                                                        DITOLAK
                                                    </span>

                                                @else

                                                    <span class="badge bg-warning text-dark">
                                                        <i class="fa-solid fa-clock me-1"></i>
                                                        DALAM SEMAKAN
                                                    </span>

                                                @endif

                                            </div>


                                            <!-- LIHAT FAIL -->
                                            @if($minutPath)

                                                <a
                                                    href="{{ asset('storage/' . $minutPath) }}"
                                                    target="_blank"
                                                    class="btn btn-sm btn-outline-dark w-100 mb-2">

                                                    <i class="fa-solid fa-eye me-1"></i>
                                                    Lihat Fail Minit

                                                </a>

                                            @endif


                                            <!-- FORM PENGESAHAN -->
                                            <form
                                                action="{{ route(
                                                    'admin.jawi.pengesahan.sahkanMinit',
                                                    $minut->id
                                                ) }}"
                                                method="POST"
                                                id="formMinit-{{ $minut->id }}">

                                                @csrf
                                                @method('PUT')


                                                <input
                                                    type="hidden"
                                                    name="status_pengesahan"
                                                    id="statusMinitInput-{{ $minut->id }}"
                                                    value="">


                                                <input
                                                    type="hidden"
                                                    name="catatan"
                                                    id="catatanMinitInput-{{ $minut->id }}"
                                                    value="">


                                                <div class="d-grid gap-2">

                                                    @if(
                                                        strtoupper(trim($minut->status_pengesahan ?? ''))
                                                        !== 'DISAHKAN'
                                                        &&
                                                        strtoupper(trim($minut->status_pengesahan ?? ''))
                                                        !== 'DITOLAK'
                                                    )

                                                        <!-- ================================================
                                                            SAHKAN MINIT
                                                        ================================================= -->

                                                        <button
                                                            type="button"
                                                            class="btn btn-sm btn-success fw-bold"
                                                            onclick="sahkanMinitWithSwal(
                                                                {{ $minut->id }},
                                                                'DISAHKAN'
                                                            )">

                                                            <i class="fa-solid fa-check me-1"></i>

                                                            Sahkan Minit

                                                        </button>


                                                        <!-- ================================================
                                                            TOLAK MINIT
                                                        ================================================= -->

                                                        <button
                                                            type="button"
                                                            class="btn btn-sm btn-outline-danger fw-bold"
                                                            onclick="sahkanMinitWithSwal(
                                                                {{ $minut->id }},
                                                                'DITOLAK'
                                                            )">

                                                            <i class="fa-solid fa-xmark me-1"></i>

                                                            Tolak Minit

                                                        </button>

                                                    @endif

                                                </div>

                                            </form>

                                        </div>

                                    @endforeach

                                @else

                                    <div class="text-center text-muted small py-3">

                                        <i class="fa-solid fa-folder-xmark me-1"></i>

                                        Tiada fail minit mesyuarat.

                                    </div>

                                @endif

                            </div>

                        </div>


                        <!-- =================================================
                            B. SEMAK PERMOHONAN
                        ================================================== -->

                        <div class="border rounded-3 mb-3 overflow-hidden">

                            <div class="bg-light px-3 py-2 border-bottom">

                                <div class="fw-bold small">

                                    <i class="fa-solid fa-clipboard-check text-success me-2"></i>

                                    Semak Permohonan

                                </div>

                            </div>


                            <div class="p-3">

                                @php
                                    $jawiConfirmation = $application->jawiConfirmation;
                                    $isJawiConfirmed = in_array($jawiConfirmation?->status ?? '', ['APPROVED', 'REJECTED']);
                                @endphp

                                <form
                                    action="{{ route(
                                        'admin.jawi.pengesahan.updateStatus',
                                        $application->id
                                    ) }}"
                                    method="POST"
                                    id="formKelulusanJawi">

                                    @csrf
                                    @method('PUT')


                                    <!-- =================================================
                                        STATUS SEDIA ADA
                                    ================================================== -->

                                    <div class="mb-3">

                                        <label class="form-label small fw-semibold">
                                            Status Semasa JAWI
                                        </label>

                                        <div>

                                            @if(($jawiConfirmation?->status ?? null) === 'APPROVED')

                                                <span class="badge bg-success">

                                                    <i class="fa-solid fa-circle-check me-1"></i>

                                                    DISAHKAN (LULUS)

                                                </span>

                                            @elseif(($jawiConfirmation?->status ?? null) === 'REJECTED')

                                                <span class="badge bg-danger">

                                                    <i class="fa-solid fa-circle-xmark me-1"></i>

                                                    DITOLAK

                                                </span>

                                            @else

                                                <span class="badge bg-warning text-dark">

                                                    <i class="fa-solid fa-clock me-1"></i>

                                                    DALAM SEMAKAN

                                                </span>

                                            @endif

                                        </div>

                                        @if($jawiConfirmation?->confirmed_at)
                                            <div class="small text-muted mt-2">
                                                <i class="fa-regular fa-calendar me-1"></i> Tarikh Semakan: {{ $jawiConfirmation->confirmed_at->format('d/m/Y h:i A') }}
                                            </div>
                                        @endif

                                    </div>

                                    @if($isJawiConfirmed)
                                        <div class="alert alert-info py-2 px-3 small rounded-3 mb-3 border-0 bg-info bg-opacity-10 text-info">
                                            <i class="fa-solid fa-lock me-1"></i> Pengesahan permohonan ini telah dilengkapkan dan dikunci.
                                        </div>
                                    @endif


                                    <!-- =================================================
                                        KEPUTUSAN
                                    ================================================== -->

                                    <div class="mb-3">

                                        <label
                                            for="selectStatusJawi"
                                            class="form-label small fw-semibold">

                                            Keputusan JAWI

                                        </label>


                                        <select
                                            name="status_jawi"
                                            id="selectStatusJawi"
                                            class="form-select"
                                            {{ $isJawiConfirmed ? 'disabled' : 'required' }}>

                                            <option value="">
                                                -- Pilih Keputusan --
                                            </option>

                                            <option value="APPROVED" {{ ($jawiConfirmation?->status ?? '') === 'APPROVED' ? 'selected' : '' }}>

                                                LULUS / SAHKAN

                                            </option>

                                            <option value="REJECTED" {{ ($jawiConfirmation?->status ?? '') === 'REJECTED' ? 'selected' : '' }}>

                                                TOLAK PERMOHONAN

                                            </option>

                                        </select>

                                    </div>


                                    <!-- =================================================
                                        CATATAN
                                    ================================================== -->

                                    <div class="mb-3">

                                        <label
                                            for="catatanJawi"
                                            class="form-label small fw-semibold">

                                            Catatan / Ulasan JAWI

                                        </label>


                                        <textarea
                                            name="catatan_jawi"
                                            id="catatanJawi"
                                            class="form-control"
                                            rows="3"
                                            {{ $isJawiConfirmed ? 'disabled' : '' }}
                                            placeholder="Catatan akan diisi melalui pilihan ulasan...">{{ $jawiConfirmation?->catatan ?? '' }}</textarea>

                                    </div>


                                    <!-- =================================================
                                        SIMPAN
                                    ================================================== -->

                                    @if($isJawiConfirmed)
                                        <button
                                            type="button"
                                            class="btn btn-secondary w-100 fw-bold shadow-sm opacity-75"
                                            disabled>

                                            <i class="fa-solid fa-lock me-1"></i>

                                            Keputusan Telah Disahkan

                                        </button>
                                    @else
                                        <button
                                            type="submit"
                                            class="btn btn-success w-100 fw-bold"
                                            id="btnSimpanJawi">

                                            <i class="fa-solid fa-floppy-disk me-1"></i>

                                            Simpan Keputusan JAWI

                                        </button>
                                    @endif

                                </form>

                            </div>

                        </div>

                        @endif

                    </div>

                </div>


            </div>

        </div>

    </div>
</div>

<!-- Script Pengesahan SweetAlert2 Sebelum Simpan Keputusan -->
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const formKelulusanJawi =
            document.getElementById('formKelulusanJawi');


        if (!formKelulusanJawi) {
            return;
        }


        formKelulusanJawi.addEventListener('submit', function (e) {

            e.preventDefault();


            const statusSelect =
                document.getElementById('selectStatusJawi');

            const catatanInput =
                document.getElementById('catatanJawi');


            const status =
                statusSelect.value;


            /*
            |--------------------------------------------------------------------------
            | PASTIKAN KEPUTUSAN DIPILIH
            |--------------------------------------------------------------------------
            */

            if (!status) {

                Swal.fire({
                    icon: 'warning',
                    title: 'Keputusan Belum Dipilih',
                    text: 'Sila pilih keputusan JAWI terlebih dahulu.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#198754'
                });

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | CATATAN RASMI - DISAHKAN
            |--------------------------------------------------------------------------
            */

            const catatanDisahkan = [

                {
                    text: 'Permohonan lengkap dan memenuhi keperluan',
                    value: 'Permohonan telah disemak dan didapati lengkap serta memenuhi keperluan yang ditetapkan.'
                },

                {
                    text: 'Permohonan telah disemak dan disahkan',
                    value: 'Permohonan telah disemak dan disahkan di peringkat Pentadbir JAWI untuk tindakan selanjutnya.'
                },

                {
                    text: 'Disahkan untuk lawatan tapak',
                    value: 'Permohonan telah disemak dan disahkan oleh pihak JAWI untuk tindakan lawatan tapak.'
                },

                {
                    text: 'Disahkan untuk proses selanjutnya',
                    value: 'Permohonan telah disemak dan didapati teratur serta disahkan untuk melalui proses selanjutnya.'
                },

                {
                    text: 'Memenuhi keperluan yang ditetapkan',
                    value: 'Permohonan telah disemak dan didapati memenuhi keperluan serta syarat yang ditetapkan oleh pihak JAWI.'
                }

            ];


            /*
            |--------------------------------------------------------------------------
            | CATATAN RASMI - DITOLAK
            |--------------------------------------------------------------------------
            */

            const catatanDitolak = [

                {
                    text: 'Maklumat tidak lengkap',
                    value: 'Permohonan telah disemak dan didapati terdapat maklumat yang tidak lengkap. Sila semak, lengkapkan dan kemukakan permohonan semula.'
                },

                {
                    text: 'Tidak memenuhi keperluan yang ditetapkan',
                    value: 'Permohonan yang dikemukakan didapati tidak memenuhi keperluan yang ditetapkan oleh pihak JAWI. Sila buat penambahbaikan yang diperlukan sebelum mengemukakan permohonan semula.'
                },

                {
                    text: 'Dokumen / maklumat perlu diperbetulkan',
                    value: 'Hasil semakan mendapati terdapat dokumen atau maklumat yang perlu diperbetulkan. Sila buat pembetulan yang berkaitan dan kemukakan permohonan semula.'
                },

                {
                    text: 'Permohonan tidak dapat dipertimbangkan',
                    value: 'Permohonan tidak dapat dipertimbangkan pada masa ini kerana tidak memenuhi keperluan yang ditetapkan. Pemohon boleh mengemukakan permohonan baharu setelah membuat penambahbaikan yang diperlukan.'
                },

                {
                    text: 'Sila buat permohonan semula',
                    value: 'Permohonan telah ditolak di peringkat Pentadbir JAWI. Sekiranya masih berhasrat untuk mendaftarkan surau, sila buat permohonan semula dengan memastikan semua maklumat dan dokumen yang diperlukan adalah lengkap.'
                },

                {
                    text: 'Tidak memenuhi syarat pendaftaran',
                    value: 'Permohonan telah disemak dan didapati tidak memenuhi syarat pendaftaran yang ditetapkan. Pemohon boleh membuat permohonan semula setelah memenuhi keperluan yang ditetapkan.'
                }

            ];


            /*
            |--------------------------------------------------------------------------
            | TENTUKAN SENARAI CATATAN
            |--------------------------------------------------------------------------
            */

            const isDitolak =
                status === 'REJECTED';


            const catatanOptions =
                isDitolak
                    ? catatanDitolak
                    : catatanDisahkan;


            /*
            |--------------------------------------------------------------------------
            | BINA DROPDOWN
            |--------------------------------------------------------------------------
            */

            let optionsHtml = '';


            catatanOptions.forEach(function (option, index) {

                optionsHtml += `

                    <option
                        value="${option.value}"
                        ${index === 0 ? 'selected' : ''}>

                        ${option.text}

                    </option>

                `;

            });


            /*
            |--------------------------------------------------------------------------
            | PILIHAN CATATAN LAIN
            |--------------------------------------------------------------------------
            */

            optionsHtml += `

                <option value="LAIN">
                    Catatan lain
                </option>

            `;


            /*
            |--------------------------------------------------------------------------
            | SWEETALERT
            |--------------------------------------------------------------------------
            */

            Swal.fire({

                title: isDitolak
                    ? 'Tolak Permohonan'
                    : 'Sahkan Permohonan',


                html: `

                    <div class="text-start">

                        <div class="mb-3">

                            <label
                                for="swalCatatanJawi"
                                class="form-label fw-bold">

                                Catatan / Ulasan JAWI

                            </label>


                            <select
                                id="swalCatatanJawi"
                                class="form-select">

                                ${optionsHtml}

                            </select>

                        </div>


                        <div
                            id="swalCatatanJawiLainWrapper"
                            style="display:none;">

                            <label
                                for="swalCatatanJawiLain"
                                class="form-label fw-bold">

                                Catatan Lain

                            </label>


                            <textarea
                                id="swalCatatanJawiLain"
                                class="form-control"
                                rows="4"
                                maxlength="1000"
                                placeholder="Masukkan catatan / ulasan rasmi JAWI..."></textarea>


                            <div class="form-text">

                                Maksimum 1000 aksara.

                            </div>

                        </div>

                    </div>

                `,


                icon: isDitolak
                    ? 'warning'
                    : 'question',


                showCancelButton: true,


                confirmButtonText: isDitolak

                    ? '<i class="fa-solid fa-xmark me-1"></i> Ya, Tolak Permohonan'

                    : '<i class="fa-solid fa-check me-1"></i> Ya, Sahkan Permohonan',


                cancelButtonText:
                    '<i class="fa-solid fa-arrow-left me-1"></i> Batal',


                confirmButtonColor:
                    isDitolak
                        ? '#dc3545'
                        : '#198754',


                cancelButtonColor: '#6c757d',


                reverseButtons: true,


                focusConfirm: false,


                /*
                |--------------------------------------------------------------------------
                | SWEETALERT DIBUKA
                |--------------------------------------------------------------------------
                */

                didOpen: function () {

                    const select =
                        document.getElementById(
                            'swalCatatanJawi'
                        );


                    const wrapper =
                        document.getElementById(
                            'swalCatatanJawiLainWrapper'
                        );


                    const textarea =
                        document.getElementById(
                            'swalCatatanJawiLain'
                        );


                    select.addEventListener(
                        'change',
                        function () {

                            if (this.value === 'LAIN') {

                                wrapper.style.display =
                                    'block';


                                setTimeout(function () {

                                    textarea.focus();

                                }, 100);

                            } else {

                                wrapper.style.display =
                                    'none';


                                textarea.value = '';

                            }

                        }
                    );

                },


                /*
                |--------------------------------------------------------------------------
                | VALIDASI CATATAN
                |--------------------------------------------------------------------------
                */

                preConfirm: function () {

                    const select =
                        document.getElementById(
                            'swalCatatanJawi'
                        );


                    const textarea =
                        document.getElementById(
                            'swalCatatanJawiLain'
                        );


                    let catatan =
                        select.value;


                    /*
                    |--------------------------------------------------------------------------
                    | CATATAN LAIN
                    |--------------------------------------------------------------------------
                    */

                    if (catatan === 'LAIN') {

                        catatan =
                            textarea.value.trim();


                        if (!catatan) {

                            Swal.showValidationMessage(
                                'Sila masukkan catatan / ulasan JAWI.'
                            );

                            textarea.focus();

                            return false;
                        }


                        if (catatan.length > 1000) {

                            Swal.showValidationMessage(
                                'Catatan tidak boleh melebihi 1000 aksara.'
                            );

                            return false;
                        }

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | PASTIKAN CATATAN ADA
                    |--------------------------------------------------------------------------
                    */

                    if (!catatan) {

                        Swal.showValidationMessage(
                            'Sila pilih catatan / ulasan JAWI.'
                        );

                        return false;
                    }


                    return catatan;

                }

            }).then(function (result) {

                /*
                |--------------------------------------------------------------------------
                | BATAL
                |--------------------------------------------------------------------------
                */

                if (!result.isConfirmed) {
                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | SIMPAN CATATAN KE TEXTAREA ASAL
                |--------------------------------------------------------------------------
                */

                catatanInput.value =
                    result.value;


                /*
                |--------------------------------------------------------------------------
                | SUBMIT FORM SEBENAR
                |--------------------------------------------------------------------------
                */

                formKelulusanJawi.submit();

            });

        });

    });

    /**
     * ============================================================
     * SAHKAN / TOLAK MINIT MESYUARAT
     * ============================================================
     */
    function sahkanMinitWithSwal(minitId, status) {

        const isDitolak = status === 'DITOLAK';


        /*
        |--------------------------------------------------------------------------
        | DAPATKAN FORM
        |--------------------------------------------------------------------------
        */

        const form = document.getElementById(
            `formMinit-${minitId}`
        );

        const statusInput = document.getElementById(
            `statusMinitInput-${minitId}`
        );

        const catatanInput = document.getElementById(
            `catatanMinitInput-${minitId}`
        );


        /*
        |--------------------------------------------------------------------------
        | SEMAK ELEMEN FORM
        |--------------------------------------------------------------------------
        */

        if (!form || !statusInput || !catatanInput) {

            Swal.fire({
                icon: 'error',
                title: 'Ralat',
                text: 'Maklumat borang minit mesyuarat tidak lengkap.',
                confirmButtonText: 'Tutup'
            });

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | CATATAN RASMI - DISAHKAN
        |--------------------------------------------------------------------------
        */

        const catatanDisahkan = [

            {
                text: 'Minit lengkap dan mematuhi keperluan',
                value: 'Minit mesyuarat telah disemak dan didapati lengkap serta mematuhi keperluan yang ditetapkan.'
            },

            {
                text: 'Minit telah disemak dan disahkan',
                value: 'Minit mesyuarat telah disemak dan disahkan.'
            },

            {
                text: 'Disahkan untuk tindakan selanjutnya',
                value: 'Minit mesyuarat telah disemak dan disahkan untuk tindakan selanjutnya.'
            },

            {
                text: 'Disahkan untuk rekod',
                value: 'Minit mesyuarat telah disemak dan disahkan untuk tujuan rekod serta tindakan selanjutnya.'
            },

            {
                text: 'Tiada pembetulan diperlukan',
                value: 'Minit mesyuarat telah disemak dan didapati teratur. Tiada pembetulan lanjut diperlukan dan minit adalah disahkan.'
            }

        ];


        /*
        |--------------------------------------------------------------------------
        | CATATAN RASMI - DITOLAK
        |--------------------------------------------------------------------------
        */

        const catatanDitolak = [

            {
                text: 'Minit perlu dikemaskini',
                value: 'Minit mesyuarat telah disemak dan didapati terdapat maklumat yang perlu dikemaskini sebelum dapat disahkan.'
            },

            {
                text: 'Maklumat tidak lengkap',
                value: 'Minit mesyuarat yang dikemukakan didapati tidak lengkap. Sila lengkapkan maklumat yang diperlukan dan kemukakan semula untuk semakan.'
            },

            {
                text: 'Terdapat maklumat yang perlu diperbetulkan',
                value: 'Minit mesyuarat telah disemak dan didapati terdapat maklumat yang perlu diperbetulkan. Sila buat pembetulan yang berkaitan dan kemukakan semula untuk semakan.'
            },

            {
                text: 'Tidak memenuhi keperluan yang ditetapkan',
                value: 'Minit mesyuarat yang dikemukakan didapati belum memenuhi keperluan yang ditetapkan. Sila buat pengemaskinian yang diperlukan dan kemukakan semula untuk semakan.'
            },

            {
                text: 'Sila kemaskini dan muat naik semula',
                value: 'Sila kemaskini minit mesyuarat berdasarkan perkara yang perlu diperbetulkan dan muat naik semula dokumen untuk semakan selanjutnya.'
            },

            {
                text: 'Perlu semakan dan pembetulan lanjut',
                value: 'Minit mesyuarat memerlukan semakan dan pembetulan lanjut sebelum dapat dipertimbangkan untuk pengesahan. Sila kemaskini dokumen dan kemukakan semula untuk semakan.'
            }

        ];


        /*
        |--------------------------------------------------------------------------
        | PILIH SENARAI BERDASARKAN STATUS
        |--------------------------------------------------------------------------
        */

        const catatanOptions = isDitolak
            ? catatanDitolak
            : catatanDisahkan;


        /*
        |--------------------------------------------------------------------------
        | BINA DROPDOWN
        |--------------------------------------------------------------------------
        */

        let optionsHtml = '';

        catatanOptions.forEach((option, index) => {

            optionsHtml += `
                <option
                    value="${option.value}"
                    ${index === 0 ? 'selected' : ''}>

                    ${option.text}

                </option>
            `;

        });


        /*
        |--------------------------------------------------------------------------
        | TAMBAH PILIHAN CATATAN LAIN
        |--------------------------------------------------------------------------
        */

        optionsHtml += `
            <option value="LAIN">
                Catatan lain
            </option>
        `;


        /*
        |--------------------------------------------------------------------------
        | SWEETALERT
        |--------------------------------------------------------------------------
        */

        Swal.fire({

            title: isDitolak
                ? 'Tolak Minit Mesyuarat'
                : 'Sahkan Minit Mesyuarat',

            html: `

                <div class="text-start">

                    <div class="mb-3">

                        <label
                            for="swalCatatanSelect"
                            class="form-label fw-bold">

                            Catatan Semakan

                        </label>

                        <select
                            id="swalCatatanSelect"
                            class="form-select">

                            ${optionsHtml}

                        </select>

                    </div>


                    <div
                        id="swalCatatanLainWrapper"
                        style="display: none;">

                        <label
                            for="swalCatatanLain"
                            class="form-label fw-bold">

                            Catatan Lain

                        </label>

                        <textarea
                            id="swalCatatanLain"
                            class="form-control"
                            rows="4"
                            maxlength="1000"
                            placeholder="Masukkan catatan semakan..."></textarea>

                        <div class="form-text">
                            Maksimum 1000 aksara.
                        </div>

                    </div>

                </div>

            `,

            icon: isDitolak
                ? 'warning'
                : 'question',

            showCancelButton: true,

            confirmButtonText: isDitolak
                ? '<i class="fa-solid fa-xmark me-1"></i> Ya, Tolak Minit'
                : '<i class="fa-solid fa-check me-1"></i> Ya, Sahkan Minit',

            cancelButtonText:
                '<i class="fa-solid fa-arrow-left me-1"></i> Batal',

            confirmButtonColor: isDitolak
                ? '#dc3545'
                : '#198754',

            cancelButtonColor: '#6c757d',

            reverseButtons: true,

            focusConfirm: false,


            /*
            |--------------------------------------------------------------------------
            | APABILA SWEETALERT DIBUKA
            |--------------------------------------------------------------------------
            */

            didOpen: () => {

                const select =
                    document.getElementById(
                        'swalCatatanSelect'
                    );

                const wrapper =
                    document.getElementById(
                        'swalCatatanLainWrapper'
                    );

                const textarea =
                    document.getElementById(
                        'swalCatatanLain'
                    );


                /*
                |--------------------------------------------------------------------------
                | APABILA PILIH CATATAN LAIN
                |--------------------------------------------------------------------------
                */

                select.addEventListener(
                    'change',
                    function () {

                        if (this.value === 'LAIN') {

                            wrapper.style.display = 'block';

                            setTimeout(() => {
                                textarea.focus();
                            }, 100);

                        } else {

                            wrapper.style.display = 'none';

                            textarea.value = '';

                        }

                    }
                );

            },


            /*
            |--------------------------------------------------------------------------
            | VALIDASI SEBELUM SUBMIT
            |--------------------------------------------------------------------------
            */

            preConfirm: () => {

                const select =
                    document.getElementById(
                        'swalCatatanSelect'
                    );

                const textarea =
                    document.getElementById(
                        'swalCatatanLain'
                    );


                let catatan = select.value;


                /*
                |--------------------------------------------------------------------------
                | CATATAN LAIN
                |--------------------------------------------------------------------------
                */

                if (catatan === 'LAIN') {

                    catatan = textarea.value.trim();


                    if (!catatan) {

                        Swal.showValidationMessage(
                            'Sila masukkan catatan semakan.'
                        );

                        textarea.focus();

                        return false;
                    }


                    if (catatan.length > 1000) {

                        Swal.showValidationMessage(
                            'Catatan tidak boleh melebihi 1000 aksara.'
                        );

                        return false;
                    }

                }


                /*
                |--------------------------------------------------------------------------
                | PASTIKAN CATATAN TIDAK KOSONG
                |--------------------------------------------------------------------------
                */

                if (!catatan) {

                    Swal.showValidationMessage(
                        'Sila pilih catatan semakan.'
                    );

                    return false;
                }


                return catatan;

            }

        }).then((result) => {


            /*
            |--------------------------------------------------------------------------
            | JIKA BATAL
            |--------------------------------------------------------------------------
            */

            if (!result.isConfirmed) {
                return;
            }


            /*
            |--------------------------------------------------------------------------
            | MASUKKAN DATA KE FORM
            |--------------------------------------------------------------------------
            */

            statusInput.value = status;

            catatanInput.value = result.value;


            /*
            |--------------------------------------------------------------------------
            | HANTAR FORM
            |--------------------------------------------------------------------------
            */

            form.submit();

        });

    }

    document.addEventListener('DOMContentLoaded', function () {

        @if(session('success'))

            Swal.fire({
                icon: 'success',
                title: 'Berjaya',
                text: @json(session('success')),
                confirmButtonText: 'OK',
                confirmButtonColor: '#198754'
            });

        @endif

        @if(session('error'))

            Swal.fire({
                icon: 'error',
                title: 'Perhatian',
                text: @json(session('error')),
                confirmButtonText: 'Faham',
                confirmButtonColor: '#dc3545'
            });

        @endif

    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // =========================================================================
    // PENGESAHAN KEPUTUSAN AKHIR (SweetAlert)
    // =========================================================================
    const formKeputusanAkhir = document.getElementById('formKeputusanAkhir');
    if (formKeputusanAkhir) {
        formKeputusanAkhir.addEventListener('submit', function (e) {
            e.preventDefault();
            const status = document.getElementById('selectStatusAkhir')?.value;
            const lawatan = document.getElementById('pengesahanLawatan');

            if (!status) {
                Swal.fire({ icon: 'warning', title: 'Sila pilih keputusan', confirmButtonColor: '#15803d' });
                return;
            }
            if (status === 'APPROVED' && lawatan && !lawatan.checked) {
                Swal.fire({ icon: 'warning', title: 'Sila sahkan lawatan tapak telah selesai dijalankan', confirmButtonColor: '#15803d' });
                return;
            }

            const isApprove = status === 'APPROVED';
            Swal.fire({
                title: isApprove ? 'Luluskan Permohonan Surau?' : 'Tolak Permohonan Surau?',
                html: isApprove
                    ? 'Adakah anda pasti mahu <strong>MELULUSKAN</strong> permohonan ini?<br><br>' +
                      '<div class="text-start p-2 bg-light rounded small text-secondary">' +
                      '• Minit mesyuarat dalam semakan akan disahkan.<br>' +
                      '• Pemohon akan didaftarkan sebagai pengguna (Pentadbir Surau).<br>' +
                      '• <strong>E-mel 1:</strong> Kelulusan dihantar kepada pemohon & CC kesemua pentadbir.<br>' +
                      '• <strong>E-mel 2:</strong> Pautan pengaktifan dihantar kepada pemohon & BCC Pentadbir Sistem.' +
                      '</div>'
                    : 'Adakah anda pasti mahu <strong>MENOLAK</strong> permohonan ini?',
                icon: isApprove ? 'question' : 'warning',
                showCancelButton: true,
                confirmButtonColor: isApprove ? '#15803d' : '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: isApprove ? 'Ya, Luluskan & Hantar E-Mel' : 'Ya, Tolak Permohonan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) formKeputusanAkhir.submit();
            });
        });
    }
});
</script>
@endsection