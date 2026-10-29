@extends('layouts.app')

@section('content')
<div class="container py-4">
    {{-- =========================================================
        HEADER KORPORAT
    ========================================================== --}}
    <div class="hero-welcome-card p-4 mb-4 shadow-sm">
        <div class="hero-shape-1"></div>
        <div class="hero-shape-2"></div>
        <div class="d-flex align-items-center justify-content-between position-relative flex-wrap gap-3">
            <div class="d-flex align-items-center">
                <div class="rounded-circle p-3 me-3 text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 52px; height: 52px; background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3);">
                    <i class="fa-solid fa-folder-open fa-2x"></i>
                </div>
                <div>
                    <div class="badge bg-white bg-opacity-25 text-white mb-1" style="font-size:0.7rem; letter-spacing:0.05em;">
                        <i class="fa-solid fa-file-signature me-1"></i> MODUL PENGURUSAN PERMOHONAN
                    </div>
                    <h4 class="fw-bold mb-0 text-white">
                        Senarai Permohonan Surau
                    </h4>
                    <small class="text-white-50">
                        Pengurusan, semakan dan kelulusan permohonan pendaftaran surau Wilayah Persekutuan.
                    </small>
                </div>
            </div>
            <div class="text-end d-none d-md-block">
                <span class="badge bg-white text-primary px-3 py-2 font-monospace fw-bold shadow-sm" style="font-size: 0.8rem;">
                    <i class="fa-solid fa-layer-group me-1"></i> {{ $applications->total() }} Permohonan
                </span>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Kad Borang Carian & Penapis (Filter) -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3 bg-light rounded-3">
            <form action="{{ route('admin.permohonan.index') }}" method="GET" class="row g-2 align-items-center">
                
                <!-- Carian Kata Kunci -->
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">Carian</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="No Rujukan, Nama Surau, Pemohon..." value="{{ request('search') }}">
                    </div>
                </div>

                <!-- Penapis Status -->
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">Status Permohonan</label>
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Semua Status --</option>
                        <option value="DALAM_SEMAKAN" {{ request('status') == 'DALAM_SEMAKAN' ? 'selected' : '' }}>DALAM SEMAKAN</option>
                        <option value="APPROVED" {{ request('status') == 'APPROVED' ? 'selected' : '' }}>DISAHKAN / LULUS</option>
                        <option value="REJECTED" {{ request('status') == 'REJECTED' ? 'selected' : '' }}>DITOLAK / PEMBETULAN</option>
                    </select>
                </div>

                <!-- Penapis Kategori Surau (Jika Ada) -->
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted mb-1">Kategori</label>
                    <select name="kategori" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Semua --</option>
                        <option value="Surau Jumaat" {{ request('kategori') == 'Surau Jumaat' ? 'selected' : '' }}>Surau Jumaat</option>
                        <option value="Surau Biasa" {{ request('kategori') == 'Surau Biasa' ? 'selected' : '' }}>Surau Biasa</option>
                    </select>
                </div>

                <!-- Bilangan Rekod Per Halaman -->
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted mb-1">Per Halaman</label>
                    <select name="per_page" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10 Rekod</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 Rekod</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 Rekod</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 Rekod</option>
                    </select>
                </div>

                <!-- Butang Tindakan -->
                <div class="col-md-2 d-flex align-items-end gap-2 mt-3 mt-md-0">
                    <button type="submit" class="btn btn-sm btn-primary w-100 fw-semibold hover-lift" style="background-color:#1e40af; border-color:#1e40af;">
                        <i class="fa-solid fa-filter me-1"></i> Tapis
                    </button>
                    <a href="{{ route('admin.permohonan.index') }}" class="btn btn-sm btn-outline-secondary w-100 fw-medium">
                        <i class="fa-solid fa-rotate-left me-1"></i> Reset
                    </a>
                </div>

            </form>
        </div>
    </div>

    <!-- Jadual Permohonan Surau -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.875rem;">
                    <thead class="bg-light text-secondary small text-uppercase">
                        <tr>
                            <th class="ps-3 text-center" style="width: 50px;">Bil.</th>
                            <th>No. Rujukan</th>
                            <th>Nama Surau & Kariah</th>
                            <th>Kategori</th>
                            <th>Pemohon Utama</th>
                            <th>Tarikh Hantar</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($applications as $index => $app)
                            @php
                                // Semak minit mesyuarat paling terkini
                                $minitTerkini = $app->meetingMinutes ? $app->meetingMinutes->sortByDesc('created_at')->first() : null;
                                $isMinitDitolak = $minitTerkini && $minitTerkini->status_pengesahan === 'DITOLAK';
                            @endphp
                            <tr>
                                <!-- Pengiraan Bilangan (Index) Merentas Pagination -->
                                <td class="ps-3 text-center text-muted small fw-bold">
                                    {{ $applications->firstItem() + $index }}
                                </td>
                                <td class="font-monospace fw-bold text-primary">{{ $app->reference_no }}</td>
                                <td>
                                    <span class="fw-semibold text-dark">{{ $app->nama_surau }}</span>
                                    <div class="small text-muted">{{ $app->kariah_masjid ?? '-' }}</div>
                                </td>
                                <td><span class="badge bg-light text-dark border">{{ $app->kategori_surau ?? '-' }}</span></td>
                                <td>
                                    <div class="fw-medium text-dark">{{ $app->pemohon_nama }}</div>
                                    <small class="text-muted">{{ $app->pemohon_tel }}</small>
                                </td>
                                <td class="font-monospace text-muted small">{{ $app->created_at->format('d/m/Y') }}</td>
                                <td>
                                    @if($app->status === 'APPROVED')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">DISAHKAN</span>
                                    @elseif($app->status === 'REJECTED' || $isMinitDitolak)
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1">MINIT DITOLAK / PEMBETULAN</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-dark border border-warning border-opacity-25 px-2 py-1">DALAM SEMAKAN</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('admin.permohonan.show', $app->id) }}" class="btn btn-sm btn-outline-primary hover-lift fw-medium">
                                        <i class="fa-solid fa-eye me-1"></i> Semak
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <i class="fa-solid fa-inbox fa-2x mb-2 d-block text-secondary"></i>
                                    Tiada rekod permohonan pendaftaran surau ditemui mengikut penapis semasa.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination (Paging Standard) -->
        @if($applications->hasPages() || $applications->total() > 0)
            <div class="card-footer bg-white py-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                <div class="small text-muted">
                    Menunjukkan <strong>{{ $applications->firstItem() ?? 0 }}</strong> hingga <strong>{{ $applications->lastItem() ?? 0 }}</strong> daripada <strong>{{ $applications->total() }}</strong> rekod permohonan
                </div>
                <div>
                    {{ $applications->appends(request()->query())->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection