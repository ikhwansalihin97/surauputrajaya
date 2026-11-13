@extends('layouts.app')

@section('content')

<div class="container py-4">

    <div class="hero-welcome-card p-4 mb-4 shadow-sm">
        <div class="hero-shape-1"></div>
        <div class="hero-shape-2"></div>
        <div class="d-flex align-items-center justify-content-between position-relative flex-wrap gap-3">
            <div class="d-flex align-items-center">
                <div class="rounded-circle p-3 me-3 text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 52px; height: 52px; background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3);">
                    <i class="fa-solid fa-kaaba fa-lg"></i>
                </div>
                <div>
                    <div class="badge bg-white bg-opacity-25 text-white mb-1" style="font-size:0.7rem; letter-spacing:0.05em;">
                        <i class="fa-solid fa-building-columns me-1"></i> MODUL JAWI
                    </div>
                    <h4 class="fw-bold mb-0 text-white">Senarai Surau Berdaftar</h4>
                    <small class="text-white-50">Surau yang diluluskan melalui permohonan dan surau sedia ada yang didaftarkan oleh JAWI.</small>
                </div>
            </div>
            <a href="{{ route('admin.jawi.surau.create') }}" class="btn btn-light fw-bold shadow-sm">
                <i class="fa-solid fa-plus me-1"></i> Daftar Surau Sedia Ada
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4 alert-dismissible fade show">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.jawi.surau.index') }}" class="row g-2">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa-solid fa-magnifying-glass text-primary"></i></span>
                        <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Cari nama surau atau no. rujukan JAWI...">
                    </div>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button class="btn btn-primary fw-semibold flex-grow-1">Cari</button>
                    @if($search)
                        <a href="{{ route('admin.jawi.surau.index') }}" class="btn btn-light border">Set Semula</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-uppercase text-muted">
                    <tr>
                        <th class="ps-3">Surau</th>
                        <th>Kariah Masjid</th>
                        <th>Pentadbir Surau</th>
                        <th class="text-center">AJK</th>
                        <th class="text-center">Sumber</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suraus as $surau)
                        <tr>
                            <td class="ps-3">
                                <div class="fw-semibold">{{ $surau->nama_surau }}</div>
                                <div class="small text-muted">
                                    {{ $surau->kategori_surau }}
                                    @if($surau->no_rujukan_jawi)
                                        &bull; <span class="font-monospace">{{ $surau->no_rujukan_jawi }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="small">{{ $surau->masjid?->nama_masjid ?? '-' }}</td>
                            <td class="small">
                                {{ $surau->user?->name ?? '-' }}
                                @if($surau->user && $surau->user->activation_token)
                                    <span class="badge bg-warning text-dark ms-1">Belum Aktif</span>
                                @endif
                                @if($surau->pertukaran_menunggu_count)
                                    <div><span class="badge bg-info-subtle text-info-emphasis mt-1" title="Pentadbir baharu belum menerima serah tugas">
                                        <i class="fa-solid fa-hourglass-half me-1"></i>Pertukaran pentadbir menunggu penerimaan
                                    </span></div>
                                @endif
                            </td>
                            <td class="text-center small">{{ $surau->committees_count }}</td>
                            <td class="text-center">
                                @if($surau->isSediaAda())
                                    <span class="badge bg-info-subtle text-info-emphasis border">Sedia Ada</span>
                                @else
                                    <span class="badge bg-success-subtle text-success-emphasis border">Permohonan</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <i class="fa-solid fa-kaaba fa-2x mb-2 opacity-50 d-block"></i>
                                Tiada surau ditemui.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($suraus->hasPages())
            <div class="card-footer bg-white">{{ $suraus->links() }}</div>
        @endif
    </div>
</div>

@endsection
