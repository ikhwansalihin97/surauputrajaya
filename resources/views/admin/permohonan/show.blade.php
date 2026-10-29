@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Header Halaman -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-file-invoice me-2 text-primary"></i> Semakan Permohonan: {{ $application->reference_no }}
            </h4>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge bg-secondary font-monospace"><i class="fa-solid fa-calendar-days me-1"></i> Tarikh Hantar: {{ $application->created_at->format('d/m/Y h:i A') }}</span>

                @php
                    $masjidConf = $application->masjidConfirmation;
                    $jawiConf   = $application->jawiConfirmation;
                    $zonConf    = $application->zonConfirmation;
                    $apt        = $application->appointment;
                @endphp

                @if($masjidConf?->status === 'APPROVED')
                    <span class="badge bg-success"><i class="fa-solid fa-mosque me-1"></i> MASJID: DISAHKAN</span>
                @else
                    <span class="badge bg-warning text-dark"><i class="fa-solid fa-mosque me-1"></i> MASJID: MENUNGGU</span>
                @endif

                @if($jawiConf?->status === 'APPROVED')
                    <span class="badge bg-success"><i class="fa-solid fa-building-columns me-1"></i> JAWI: DISAHKAN</span>
                @elseif($jawiConf?->status === 'REJECTED')
                    <span class="badge bg-danger"><i class="fa-solid fa-building-columns me-1"></i> JAWI: DITOLAK</span>
                @else
                    <span class="badge bg-warning text-dark"><i class="fa-solid fa-building-columns me-1"></i> JAWI: MENUNGGU</span>
                @endif

                @if($apt)
                    <span class="badge bg-primary"><i class="fa-solid fa-calendar-check me-1"></i> LAWATAN: {{ $apt->tarikh_temujanji?->format('d/m/Y') }}</span>
                @else
                    <span class="badge bg-secondary"><i class="fa-solid fa-calendar-xmark me-1"></i> LAWATAN: BELUM DIATUR</span>
                @endif

                @if($application->status === 'APPROVED' || $zonConf?->status === 'APPROVED')
                    <span class="badge bg-success"><i class="fa-solid fa-circle-check me-1"></i> ZON: LULUS</span>
                @elseif($application->status === 'REJECTED' || $zonConf?->status === 'REJECTED')
                    <span class="badge bg-danger"><i class="fa-solid fa-circle-xmark me-1"></i> ZON: DITOLAK</span>
                @else
                    <span class="badge bg-info text-dark"><i class="fa-solid fa-clock me-1"></i> MENUNGGU KEPUTUSAN ZON</span>
                @endif
            </div>
        </div>
        <div>
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

                                            <!-- Borang Pengesahan Minit Mesyuarat -->
                                            <form action="{{ route('admin.permohonan.sahkanMinit', $minut->id) }}" method="POST" id="formMinit-{{ $minut->id }}" class="d-flex gap-1">
                                                @csrf
                                                @method('PUT')
                                                
                                                <!-- Hidden input untuk simpan status yang dipilih -->
                                                <input type="hidden" name="status_pengesahan" id="statusMinitInput-{{ $minut->id }}" value="">

                                                @if($minut->status_pengesahan !== 'DISAHKAN')
                                                    <button type="button" class="btn btn-sm btn-success fw-bold" onclick="sahkanMinitWithSwal({{ $minut->id }}, 'DISAHKAN')">
                                                        <i class="fa-solid fa-check me-1"></i> Sahkan Minit
                                                    </button>
                                                @endif

                                                @if($minut->status_pengesahan !== 'DITOLAK')
                                                    <button type="button" class="btn btn-sm btn-outline-danger fw-bold" onclick="sahkanMinitWithSwal({{ $minut->id }}, 'DITOLAK')">
                                                        <i class="fa-solid fa-xmark me-1"></i> Tolak Minit
                                                    </button>
                                                @endif
                                            </form>
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

            <!-- Seksyen 7: Maklumat Lawatan Tapak -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header text-white fw-bold py-2.5" style="background: linear-gradient(135deg, #1e3a8a 0%, #0369a1 100%);">
                    7. Maklumat Lawatan Tapak
                </div>
                <div class="card-body">
                    @php
                        $appointment = $application->appointment;
                        $allAppointments = $application->appointments ?? collect();
                    @endphp

                    @if($appointment)
                        <div class="p-3 border rounded bg-primary bg-opacity-10 border-primary border-opacity-25 mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-primary border-opacity-25">
                                <div class="fw-bold text-primary">
                                    <i class="fa-solid fa-calendar-check me-2"></i> Temujanji Lawatan Tapak Rasmi
                                </div>
                                <span class="badge bg-primary px-2 py-1">TELAH DIATUR</span>
                            </div>
                            <div class="row g-3 small">
                                <div class="col-md-4">
                                    <span class="text-muted d-block">Tarikh Temujanji:</span>
                                    <strong class="text-primary fs-6"><i class="fa-regular fa-calendar me-1"></i> {{ $appointment->tarikh_temujanji ? $appointment->tarikh_temujanji->format('d/m/Y') : '-' }}</strong>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-muted d-block">Masa Temujanji:</span>
                                    <strong class="text-primary fs-6"><i class="fa-regular fa-clock me-1"></i> {{ $appointment->masa_temujanji ? \Carbon\Carbon::parse($appointment->masa_temujanji)->format('h:i A') : '-' }}</strong>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-muted d-block">Lokasi / Tempat:</span>
                                    <strong class="text-dark"><i class="fa-solid fa-location-dot me-1 text-danger"></i> {{ $appointment->tempat ?? '-' }}</strong>
                                </div>
                                @if($appointment->catatan)
                                <div class="col-12 mt-2">
                                    <span class="text-muted d-block">Catatan / Arahan Temujanji JAWI:</span>
                                    <div class="bg-white p-2 rounded border border-secondary border-opacity-25 text-secondary">{{ $appointment->catatan }}</div>
                                </div>
                                @endif
                                @if($appointment->ditetapkanOleh)
                                <div class="col-12 text-muted" style="font-size: 0.75rem;">
                                    <i class="fa-solid fa-user-pen me-1"></i> Ditetapkan oleh Pentadbir JAWI: <strong>{{ $appointment->ditetapkanOleh->name }}</strong> ({{ $appointment->created_at ? $appointment->created_at->format('d/m/Y h:i A') : '' }})
                                </div>
                                @endif
                            </div>
                        </div>

                        @if($allAppointments->count() > 1)
                            <div class="accordion" id="accordionSejarahTemujanji">
                                <div class="accordion-item border rounded-3 overflow-hidden shadow-sm">
                                    <h2 class="accordion-header" id="headingSejarah">
                                        <button class="accordion-button collapsed py-2 px-3 bg-light text-dark fw-bold small" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSejarah" aria-expanded="false" aria-controls="collapseSejarah">
                                            <i class="fa-solid fa-clock-rotate-left me-2 text-secondary"></i> Sejarah Temujanji Terdahulu ({{ $allAppointments->count() - 1 }} Rekod Lama)
                                        </button>
                                    </h2>
                                    <div id="collapseSejarah" class="accordion-collapse collapse" aria-labelledby="headingSejarah">
                                        <div class="accordion-body p-2 bg-light">
                                            @foreach($allAppointments->skip(1) as $oldApt)
                                                <div class="card mb-2 p-2 border-0 shadow-sm bg-white rounded small">
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
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="alert alert-warning border-0 p-3 mb-0">
                            <div class="d-flex align-items-start">
                                <i class="fa-solid fa-triangle-exclamation text-warning fa-lg me-3 mt-1"></i>
                                <div>
                                    <h6 class="fw-bold text-warning-emphasis mb-1">Temujanji Lawatan Tapak Belum Ditetapkan</h6>
                                    <p class="small text-secondary mb-0">
                                        Pentadbir JAWI belum menetapkan temujanji lawatan tapak bagi permohonan ini. Pentadbir Zon hanya boleh meluluskan permohonan selepas lawatan tapak dibuat.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Seksyen 8: Akuan Pengesahan Pemohon -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header text-white fw-bold py-2.5" style="background: linear-gradient(135deg, #1e3a8a 0%, #0369a1 100%);">
                    8. Akuan Pengesahan Pemohon
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

        <!-- Panel Tindakan Pentadbir Zon -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 sticky-top" style="top: 20px;">
                <div class="card-header text-white fw-bold py-3" style="background: linear-gradient(135deg, #1e3a8a 0%, #0369a1 100%);">
                    <i class="fa-solid fa-gavel me-2"></i> Tindakan Pentadbir Zon
                    <div class="small opacity-75 fw-normal mt-0.5">Kelulusan Permohonan Selepas Lawatan Tapak</div>
                </div>
                <div class="card-body p-3">

                    @php
                        $zonConf = $application->zonConfirmation;
                        $isZonApproved = ($zonConf?->status === 'APPROVED') || ($application->status === 'APPROVED');
                        $isZonRejected = ($zonConf?->status === 'REJECTED') || ($application->status === 'REJECTED');
                        $hasAppointment = $application->appointment !== null || ($application->appointments && $application->appointments->isNotEmpty());
                    @endphp

                    <!-- 1. Ringkasan Status Peringkat Pengesahan -->
                    <div class="border rounded-3 p-3 bg-light mb-3">
                        <div class="fw-bold small text-dark mb-2 border-bottom pb-2">
                            <i class="fa-solid fa-list-check text-primary me-1"></i> Aliran Pengesahan Permohonan
                        </div>
                        <ul class="list-unstyled mb-0 small">
                            <li class="d-flex justify-content-between align-items-center mb-2">
                                <span><i class="fa-solid fa-mosque me-1 text-muted"></i> 1. Pentadbir Masjid:</span>
                                @if($application->masjidConfirmation?->status === 'APPROVED')
                                    <span class="badge bg-success"><i class="fa-solid fa-check me-1"></i> Disahkan</span>
                                @else
                                    <span class="badge bg-warning text-dark"><i class="fa-solid fa-clock me-1"></i> Menunggu</span>
                                @endif
                            </li>
                            <li class="d-flex justify-content-between align-items-center mb-2">
                                <span><i class="fa-solid fa-building-columns me-1 text-muted"></i> 2. Pentadbir JAWI:</span>
                                @if($application->jawiConfirmation?->status === 'APPROVED')
                                    <span class="badge bg-success"><i class="fa-solid fa-check me-1"></i> Disahkan</span>
                                @elseif($application->jawiConfirmation?->status === 'REJECTED')
                                    <span class="badge bg-danger"><i class="fa-solid fa-xmark me-1"></i> Ditolak</span>
                                @else
                                    <span class="badge bg-warning text-dark"><i class="fa-solid fa-clock me-1"></i> Menunggu</span>
                                @endif
                            </li>
                            <li class="d-flex justify-content-between align-items-center mb-2">
                                <span><i class="fa-solid fa-calendar-check me-1 text-muted"></i> 3. Lawatan Tapak:</span>
                                @if($hasAppointment)
                                    <span class="badge bg-primary"><i class="fa-solid fa-check me-1"></i> Ditetapkan</span>
                                @else
                                    <span class="badge bg-secondary"><i class="fa-solid fa-xmark me-1"></i> Tiada Temujanji</span>
                                @endif
                            </li>
                            <li class="d-flex justify-content-between align-items-center">
                                <span><i class="fa-solid fa-map-location-dot me-1 text-muted"></i> 4. Pentadbir Zon:</span>
                                @if($isZonApproved)
                                    <span class="badge bg-success"><i class="fa-solid fa-circle-check me-1"></i> Diluluskan</span>
                                @elseif($isZonRejected)
                                    <span class="badge bg-danger"><i class="fa-solid fa-circle-xmark me-1"></i> Ditolak</span>
                                @else
                                    <span class="badge bg-info text-dark"><i class="fa-solid fa-clock me-1"></i> Dalam Semakan</span>
                                @endif
                            </li>
                        </ul>
                    </div>

                    @if($isZonApproved)
                        <!-- Paparan Jika Telah Diluluskan -->
                        <div class="alert alert-success border-0 shadow-sm mb-3 p-3">
                            <div class="fw-bold text-success mb-1">
                                <i class="fa-solid fa-circle-check me-1"></i> Permohonan Telah Diluluskan
                            </div>
                            <div class="small text-secondary">
                                Permohonan ini telah disahkan &amp; diluluskan oleh Pentadbir Zon
                                @if($zonConf?->confirmed_at)
                                    pada <strong>{{ $zonConf->confirmed_at->format('d/m/Y h:i A') }}</strong>.
                                @endif
                            </div>
                        </div>

                        @if(!empty($zonConf?->catatan))
                            <div class="mb-3 p-3 bg-light rounded border small">
                                <strong>Catatan / Laporan Lawatan Tapak:</strong>
                                <div class="text-secondary mt-1 fst-italic">"{{ $zonConf->catatan }}"</div>
                            </div>
                        @endif

                        <!-- Maklumat Akaun Pengguna yang didaftarkan -->
                        <div class="card border-primary border-opacity-25 bg-primary bg-opacity-10 rounded-3 p-3 mb-3">
                            <div class="fw-bold text-primary small mb-2 border-bottom border-primary border-opacity-25 pb-1">
                                <i class="fa-solid fa-user-check me-1"></i> Maklumat Pengguna Didaftarkan
                            </div>
                            <div class="small text-dark">
                                <div class="mb-1"><strong>Nama:</strong> {{ $application->pemohon_nama }}</div>
                                <div class="mb-1"><strong>E-mel (ID Log Masuk):</strong> <span class="font-monospace text-primary fw-bold">{{ $application->pemohon_email }}</span></div>
                                <div class="mb-1"><strong>Peranan Sistem:</strong> <span class="badge bg-primary">Pentadbir Surau</span></div>
                                <div class="mt-2 text-success fw-semibold" style="font-size:0.75rem;">
                                    <i class="fa-solid fa-paper-plane me-1"></i> 2 E-mel telah dikeluarkan oleh sistem:
                                    <ul class="mb-0 ps-3 mt-1">
                                        <li>E-mel kelulusan permohonan (CC kesemua pentadbir)</li>
                                        <li>E-mel link pengaktifan (BCC Pentadbir Sistem)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-secondary w-100 fw-bold py-2 shadow-sm opacity-75" disabled>
                            <i class="fa-solid fa-lock me-1"></i> Keputusan Telah Disimpan &amp; Dikunci
                        </button>
                    @else
                        <!-- Form Kelulusan Pentadbir Zon -->
                        @if(!$hasAppointment)
                            <div class="alert alert-warning border-0 small mb-3">
                                <i class="fa-solid fa-triangle-exclamation me-1"></i> <strong>Perhatian:</strong> Temujanji lawatan tapak belum diatur oleh Pentadbir JAWI. Pentadbir Zon hanya boleh meluluskan permohonan selepas lawatan tapak dibuat.
                            </div>
                        @endif

                        <form action="{{ route('admin.permohonan.updateStatus', $application->id) }}" method="POST" id="formKelulusanZon">
                            @csrf
                            @method('PUT')

                            @if($hasAppointment)
                                <div class="form-check p-3 bg-light rounded border mb-3">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" name="pengesahan_lawatan" id="pengesahanLawatan" value="1" required checked>
                                    <label class="form-check-label small fw-semibold text-dark" for="pengesahanLawatan">
                                        <i class="fa-solid fa-clipboard-check text-success me-1"></i> Saya mengesahkan bahawa lawatan tapak ke <u>{{ $application->nama_surau }}</u> telah selesai dijalankan.
                                    </label>
                                </div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Status Kelulusan Pentadbir Zon</label>
                                <select name="status" id="selectStatusZon" class="form-select" {{ !$hasAppointment ? 'disabled' : 'required' }}>
                                    <option value="">-- Pilih Keputusan --</option>
                                    <option value="APPROVED" selected>LULUS / SAHKAN PERMOHONAN</option>
                                    <option value="REJECTED">TOLAK PERMOHONAN</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Catatan / Laporan Hasil Lawatan Tapak</label>
                                <textarea name="catatan_zon" id="catatanZon" class="form-control" rows="3" placeholder="Masukkan ulasan lawatan tapak, syor kelulusan, atau catatan berkaitan...">Lawatan tapak telah dilaksanakan dan premis surau didapati mematuhi syarat yang ditetapkan.</textarea>
                            </div>

                            <div class="p-3 bg-light rounded border small mb-3 text-secondary">
                                <div class="fw-bold text-dark mb-1"><i class="fa-solid fa-wand-magic-sparkles text-primary me-1"></i> Tindakan Automatik Selepas Lulus:</div>
                                <ul class="ps-3 mb-0" style="font-size:0.78rem;">
                                    <li>Pemohon didaftarkan sebagai pengguna sistem (Pentadbir Surau).</li>
                                    <li><strong>E-mel 1:</strong> E-mel kelulusan permohonan dihantar kepada pemohon (CC kesemua pentadbir).</li>
                                    <li><strong>E-mel 2:</strong> E-mel link pengaktifan akaun dihantar kepada pemohon (BCC Pentadbir Sistem).</li>
                                </ul>
                            </div>

                            @if(!$hasAppointment)
                                <button type="button" class="btn btn-secondary w-100 fw-bold py-2 shadow-sm" disabled>
                                    <i class="fa-solid fa-lock me-1"></i> Menunggu Lawatan Tapak Ditetapkan
                                </button>
                            @else
                                <button type="submit" class="btn btn-success w-100 fw-bold py-2 shadow-sm hover-lift" id="btnSimpanKeputusanZon" style="background-color:#15803d; border-color:#15803d;">
                                    <i class="fa-solid fa-check-double me-1"></i> Luluskan Permohonan &amp; Hantar E-Mel
                                </button>
                            @endif
                        </form>
                    @endif

                </div>
            </div>
        </div>

    </div>
</div>

<!-- Script Pengesahan SweetAlert2 Sebelum Simpan Keputusan -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const formKelulusanZon = document.getElementById('formKelulusanZon');
    
    if (formKelulusanZon) {
        formKelulusanZon.addEventListener('submit', function (e) {
            e.preventDefault();
            const statusSelect = document.getElementById('selectStatusZon');
            const statusVal = statusSelect ? statusSelect.value : 'APPROVED';
            const isApprove = statusVal === 'APPROVED';

            const titleText = isApprove ? 'Luluskan Permohonan Surau?' : 'Tolak Permohonan Surau?';
            const htmlText = isApprove 
                ? 'Adakah anda pasti mahu <strong>MELULUSKAN</strong> permohonan ini?<br><br>' +
                  '<div class="text-start p-2 bg-light rounded small text-secondary">' +
                  '• Pemohon akan didaftarkan sebagai pengguna (Pentadbir Surau).<br>' +
                  '• <strong>E-mel 1:</strong> Kelulusan dihantar kepada pemohon & CC kesemua pentadbir.<br>' +
                  '• <strong>E-mel 2:</strong> Pautan pengaktifan dihantar kepada pemohon & BCC Pentadbir Sistem.' +
                  '</div>'
                : 'Adakah anda pasti mahu <strong>MENOLAK</strong> permohonan ini?';

            const confirmColor = isApprove ? '#15803d' : '#dc3545';
            const confirmBtnText = isApprove ? 'Ya, Luluskan & Hantar E-Mel' : 'Ya, Tolak Permohonan';

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: titleText,
                    html: htmlText,
                    icon: isApprove ? 'question' : 'warning',
                    showCancelButton: true,
                    confirmButtonColor: confirmColor,
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: confirmBtnText,
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        formKelulusanZon.submit();
                    }
                });
            } else {
                if (confirm(`${titleText}\nTeruskan simpan keputusan?`)) {
                    formKelulusanZon.submit();
                }
            }
        });
    }
});

function sahkanMinitWithSwal(minitId, status) {
    const isSah = status === 'DISAHKAN';
    const titleText = isSah ? 'Sahkan Minit Mesyuarat?' : 'Tolak Minit Mesyuarat?';
    const htmlText = isSah 
        ? 'Adakah anda pasti mahu <strong>MENGESAHKAN</strong> fail minit mesyuarat ini?' 
        : 'Adakah anda pasti mahu <strong>MENOLAK</strong> fail minit mesyuarat ini?';
    const confirmColor = isSah ? '#198754' : '#dc3545';
    const confirmBtnText = isSah ? 'Ya, Sahkan' : 'Ya, Tolak';

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: titleText,
            html: htmlText,
            icon: isSah ? 'question' : 'warning',
            showCancelButton: true,
            confirmButtonColor: confirmColor,
            cancelButtonColor: '#6c757d',
            confirmButtonText: confirmBtnText,
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Setkan nilai status ke hidden input dan submit form
                document.getElementById('statusMinitInput-' + minitId).value = status;
                document.getElementById('formMinit-' + minitId).submit();
            }
        });
    } else {
        // Fallback jika CDN SweetAlert2 gagal dimuatkan
        if (confirm(`${titleText}\n${isSah ? 'Sahkan fail ini?' : 'Tolak fail ini?'}`)) {
            document.getElementById('statusMinitInput-' + minitId).value = status;
            document.getElementById('formMinit-' + minitId).submit();
        }
    }
}
</script>
@endsection