@extends('layouts.app')

@section('content')

<div class="container py-4">

    <div class="hero-welcome-card p-4 mb-4 shadow-sm">
        <div class="hero-shape-1"></div>
        <div class="hero-shape-2"></div>
        <div class="d-flex align-items-center position-relative">
            <div class="rounded-circle p-3 me-3 text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 52px; height: 52px; background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3);">
                <i class="fa-solid fa-stamp fa-lg"></i>
            </div>
            <div>
                <div class="badge bg-white bg-opacity-25 text-white mb-1" style="font-size:0.7rem; letter-spacing:0.05em;">
                    <i class="fa-solid fa-building-columns me-1"></i> MODUL JAWI
                </div>
                <h4 class="fw-bold mb-0 text-white">Pengesahan Mesyuarat Agung Tahunan</h4>
                <small class="text-white-50">Sahkan minit mesyuarat, senarai AJK dan Pentadbir Surau (Bil. 1) atau minit dan laporan kewangan (Bil. 2).</small>
            </div>
        </div>
    </div>

    @php
        $tab = [
            'MENUNGGU'         => 'Menunggu',
            'PERLU_PEMBETULAN' => 'Perlu Pembetulan',
            'DISAHKAN'         => 'Disahkan',
            'SEMUA'            => 'Semua',
        ];
    @endphp
    <ul class="nav nav-pills mb-3 gap-1">
        @foreach($tab as $k => $t)
            <li class="nav-item">
                <a class="nav-link {{ $status === $k ? 'active' : 'bg-white border' }} fw-semibold small" href="{{ route('admin.jawi.agm.index', ['status' => $k, 'search' => $search ?: null]) }}">
                    {{ $t }}
                    <span class="badge {{ $status === $k ? 'bg-white text-primary' : 'bg-light text-dark border' }} ms-1">{{ $k === 'SEMUA' ? $bilangan->sum() : ($bilangan[$k] ?? 0) }}</span>
                </a>
            </li>
        @endforeach
    </ul>

    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.jawi.agm.index') }}" class="row g-2">
                <input type="hidden" name="status" value="{{ $status }}">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa-solid fa-magnifying-glass text-primary"></i></span>
                        <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Cari nama surau...">
                    </div>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button class="btn btn-primary fw-semibold flex-grow-1">Cari</button>
                    @if($search)
                        <a href="{{ route('admin.jawi.agm.index', ['status' => $status]) }}" class="btn btn-light border">Set Semula</a>
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
                        <th>Mesyuarat</th>
                        <th>Dihantar</th>
                        <th class="text-center">Status</th>
                        <th class="text-end pe-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mesyuarat as $m)
                        @php [$lbl, $cls, $ikon] = $m->labelStatusPengesahan(); @endphp
                        <tr>
                            <td class="ps-3">
                                <div class="fw-semibold">{{ $m->sesi->surau->nama_surau }}</div>
                                <div class="small text-muted">{{ $m->sesi->surau->masjid?->nama_masjid ?? '-' }}</div>
                            </td>
                            <td class="small">
                                Bil. {{ $m->bilangan }} ({{ $m->tahun }}) &bull; Sesi {{ $m->sesi->label }}
                                <div class="text-muted">{{ $m->tarikh->locale('ms')->translatedFormat('d F Y') }}</div>
                            </td>
                            <td class="small">
                                {{ $m->dihantar_pengesahan_at?->format('d/m/Y g:i A') }}
                                @if(($m->pengesahanTerkini?->pusingan ?? 1) > 1)
                                    <div><span class="badge bg-info-subtle text-info-emphasis">Hantar semula (pusingan {{ $m->pengesahanTerkini->pusingan }})</span></div>
                                @endif
                            </td>
                            <td class="text-center"><span class="badge {{ $cls }}"><i class="fa-solid {{ $ikon }} me-1"></i>{{ $lbl }}</span></td>
                            <td class="text-end pe-3">
                                <a href="{{ route('admin.jawi.agm.show', $m) }}" class="btn btn-sm {{ $m->status_pengesahan === 'MENUNGGU' ? 'btn-primary' : 'btn-outline-primary' }} fw-semibold">
                                    {{ $m->status_pengesahan === 'MENUNGGU' ? 'Semak' : 'Lihat' }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <i class="fa-solid fa-stamp fa-2x mb-2 opacity-50 d-block"></i>
                                Tiada rekod.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($mesyuarat->hasPages())
            <div class="card-footer bg-white">{{ $mesyuarat->links() }}</div>
        @endif
    </div>
</div>

@endsection
