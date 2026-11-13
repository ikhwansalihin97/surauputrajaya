@extends('layouts.app')

@section('content')


<div class="container py-4">

    @include('surau._pemilih')

    <div class="hero-welcome-card p-4 mb-4 shadow-sm">
        <div class="hero-shape-1"></div>
        <div class="hero-shape-2"></div>
        <div class="d-flex align-items-center position-relative">
            <div class="rounded-circle p-3 me-3 text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 52px; height: 52px; background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3);">
                <i class="fa-solid fa-users fa-lg"></i>
            </div>
            <div>
                <div class="badge bg-white bg-opacity-25 text-white mb-1" style="font-size:0.7rem; letter-spacing:0.05em;">
                    <i class="fa-solid fa-sitemap me-1"></i> MAKLUMAT JAWATANKUASA SURAU
                </div>
                <h4 class="fw-bold mb-0 text-white">{{ $surau->nama_surau }}</h4>
                <small class="text-white-50">
                    Sesi AGM <strong class="text-white">{{ ($sesiDilihat ?? $sesi)->label }}</strong>
                    &bull; Perubahan disimpan terus dan direkodkan dalam log aktiviti.
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
                @foreach(collect($errors->all())->unique() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Pilih sesi: sesi semasa boleh dikemaskini, sesi terdahulu baca sahaja --}}
    @if($sesiList->count() > 1)
        <div class="d-flex flex-wrap align-items-center gap-2 mb-3 small">
            <span class="text-muted fw-semibold"><i class="fa-solid fa-clock-rotate-left me-1"></i>Sesi:</span>
            @foreach($sesiList as $s)
                @php $aktif = $sesiDilihat ? $sesiDilihat->id === $s->id : $s->id === $sesi->id; @endphp
                <a href="{{ $s->id === $sesi->id ? route('surau.jawatankuasa.edit') : route('surau.jawatankuasa.edit', ['sesi' => $s->id]) }}"
                   class="btn btn-sm {{ $aktif ? 'btn-primary' : 'btn-outline-secondary' }} fw-semibold">
                    {{ $s->label }}@if($s->id === $sesi->id) (semasa)@endif
                </a>
            @endforeach
        </div>
    @endif

    @if($sesiDilihat)
        <div class="alert alert-info border-0 shadow-sm small mb-3">
            <i class="fa-solid fa-lock me-1"></i> Senarai AJK sesi terdahulu ({{ $sesiDilihat->label }}) untuk rujukan sahaja.
        </div>
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0 small">
                    <thead class="table-light">
                        <tr><th class="ps-3">Jawatan</th><th>Nama</th><th>No. K/P</th><th>No. Telefon</th><th>E-mel</th></tr>
                    </thead>
                    <tbody>
                        @forelse($sesiDilihat->committees as $c)
                            <tr>
                                <td class="ps-3 fw-semibold">{{ $c->jawatan }}</td>
                                <td>{{ $c->nama }}</td>
                                <td class="font-monospace">{{ $c->ic_no }}</td>
                                <td>{{ $c->no_tel }}</td>
                                <td>{{ $c->email ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">Tiada AJK direkodkan bagi sesi ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <a href="{{ route('surau.jawatankuasa.edit') }}" class="btn btn-light border fw-semibold">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Sesi Semasa
        </a>
    @else
    @include('surau._borang-ajk', ['kembaliUrl' => route('dashboard')])
    @endif
</div>


@endsection
