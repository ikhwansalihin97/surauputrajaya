@extends('layouts.app')

@section('content')

<div class="container py-4">

    @include('surau._pemilih')

    <div class="hero-welcome-card p-4 mb-4 shadow-sm">
        <div class="hero-shape-1"></div>
        <div class="hero-shape-2"></div>
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 position-relative">
            <div class="d-flex align-items-center">
                <div class="rounded-circle p-3 me-3 text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 52px; height: 52px; background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3);">
                    <i class="fa-solid fa-people-roof fa-lg"></i>
                </div>
                <div>
                    <div class="badge bg-white bg-opacity-25 text-white mb-1" style="font-size:0.7rem; letter-spacing:0.05em;">
                        <i class="fa-solid fa-calendar-days me-1"></i> MESYUARAT AGUNG TAHUNAN (AGM)
                    </div>
                    <h4 class="fw-bold mb-0 text-white">{{ $surau->nama_surau }}</h4>
                    <small class="text-white-50">Setiap sesi meliputi dua tahun: Bil. 1 (tahun pertama) dan Bil. 2 (tahun kedua).</small>
                </div>
            </div>
            <a href="{{ route('surau.agm.create') }}" class="btn btn-light fw-bold flex-shrink-0">
                <i class="fa-solid fa-plus me-1"></i> Rekod Mesyuarat AGM
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4 alert-dismissible fade show">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('info'))
        <div class="alert alert-warning border-0 shadow-sm mb-4">
            <i class="fa-solid fa-circle-info me-2"></i> {{ session('info') }}
        </div>
    @endif

    @forelse($sesiList as $sesi)
        @php $semasa = $loop->first; @endphp
        <div class="card border-0 shadow-sm rounded-3 mb-4" style="border-left:4px solid {{ $semasa ? '#1e40af' : '#94a3b8' }} !important;">
            <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2" style="border-bottom:2px solid #eff6ff;">
                <div class="fw-bold">
                    <i class="fa-solid fa-calendar-check me-2 text-primary"></i>Sesi {{ $sesi->label }}
                    @if($semasa)
                        <span class="badge bg-primary ms-1">SESI SEMASA</span>
                    @endif
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge {{ $sesi->committees_count ? 'bg-success' : 'bg-warning text-dark' }}">
                        {{ $sesi->committees_count ? $sesi->committees_count . ' AJK' : 'AJK BELUM DIISI' }}
                    </span>
                    <a href="{{ $semasa ? route('surau.jawatankuasa.edit') : route('surau.jawatankuasa.edit', ['sesi' => $sesi->id]) }}" class="btn btn-sm btn-outline-primary fw-semibold">
                        <i class="fa-solid fa-users me-1"></i> {{ $semasa ? 'Senarai AJK' : 'Lihat AJK' }}
                    </a>
                </div>
            </div>
            <div class="card-body p-3 p-md-4">
                <div class="row g-3">
                    @foreach([1, 2] as $bil)
                        @php $m = $sesi->mesyuarat->firstWhere('bilangan', $bil); @endphp
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100 {{ $m ? '' : 'bg-light-subtle' }}">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge {{ $m ? 'bg-success' : 'bg-secondary-subtle text-secondary-emphasis' }}">
                                        Bil. {{ $bil }} ({{ $sesi->tahunBagi($bil) }})
                                    </span>
                                    @if($m)
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('surau.agm.edit', $m) }}" class="btn btn-sm btn-outline-primary" title="Kemaskini">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>
                                            <form action="{{ route('surau.agm.destroy', $m) }}" method="POST" class="form-padam-agm" data-label="Bil. {{ $bil }} ({{ $m->tahun }}) sesi {{ $sesi->label }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Padam"><i class="fa-solid fa-trash"></i></button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                                @if($m)
                                    <div class="small">
                                        <div class="mb-1"><i class="fa-regular fa-calendar me-2 text-muted"></i>{{ $m->tarikh->locale('ms')->translatedFormat('d F Y') }}</div>
                                        <div class="mb-1"><i class="fa-regular fa-clock me-2 text-muted"></i>{{ \Carbon\Carbon::parse($m->masa)->format('g:i A') }}</div>
                                        <div class="mb-2"><i class="fa-solid fa-location-dot me-2 text-muted"></i>{{ $m->lokasi }}</div>
                                        <a href="{{ route('surau.agm.jemputan.index', $m) }}" class="btn btn-sm btn-outline-success fw-semibold me-1 mb-1">
                                            <i class="fa-solid fa-envelope-open-text me-1"></i> Jemputan
                                            <span class="badge bg-success ms-1">{{ $m->jemputan_dihantar_count }}/{{ $m->jemputan_count }}</span>
                                        </a>
                                        <a href="{{ route('surau.agm.kehadiran.index', $m) }}" class="btn btn-sm btn-outline-primary fw-semibold me-1 mb-1">
                                            <i class="fa-solid fa-clipboard-user me-1"></i> Kehadiran
                                            <span class="badge bg-primary ms-1">{{ $m->kehadiran_count }}</span>
                                        </a>
                                        @if($m->kehadiran_count)
                                            <a href="{{ route('surau.agm.keputusan.index', $m) }}" class="btn btn-sm btn-outline-dark fw-semibold mb-1">
                                                <i class="fa-solid fa-gavel me-1"></i> Keputusan
                                                @if($m->minit_path)<i class="fa-solid fa-circle-check text-success ms-1" title="Minit dimuat naik"></i>@endif
                                            </a>
                                        @endif
                                    </div>
                                @else
                                    <div class="small text-muted mb-2">Belum direkodkan.</div>
                                    <a href="{{ route('surau.agm.create', ['sesi' => $sesi->tahun_mula, 'bilangan' => $bil]) }}" class="btn btn-sm btn-outline-secondary fw-semibold">
                                        <i class="fa-solid fa-plus me-1"></i> Rekod Bil. {{ $bil }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @empty
        <div class="card border-0 shadow-sm rounded-3 text-center p-5" style="border-top:4px solid #d97706 !important;">
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mx-auto mb-3" style="width:64px;height:64px;background-color:#fffbeb;border:1px solid #fef3c7;">
                <i class="fa-solid fa-calendar-plus fa-xl" style="color:#d97706;"></i>
            </div>
            <h5 class="fw-bold mb-2">Tiada Rekod Mesyuarat Agung Tahunan</h5>
            <p class="text-muted small mb-4 mx-auto" style="max-width:480px;">
                Sila rekodkan Mesyuarat Agung Tahunan surau terlebih dahulu. Senarai Ahli Jawatankuasa (AJK)
                hanya boleh diisi selepas sesi AGM direkodkan.
            </p>
            <div>
                <a href="{{ route('surau.agm.create') }}" class="btn btn-primary fw-bold px-4">
                    <i class="fa-solid fa-plus me-1"></i> Rekod Mesyuarat AGM
                </a>
            </div>
        </div>
    @endforelse
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.form-padam-agm').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const label = form.dataset.label;
            const teruskan = () => form.submit();
            if (typeof Swal === 'undefined') {
                if (confirm(`Padam rekod Mesyuarat ${label}?`)) teruskan();
                return;
            }
            Swal.fire({
                icon: 'warning',
                title: 'Padam Rekod Mesyuarat?',
                html: `Rekod Mesyuarat <strong>${label}</strong> beserta senarai jemputannya akan dipadam.`,
                showCancelButton: true,
                confirmButtonText: 'Ya, Padam',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc2626',
            }).then(r => { if (r.isConfirmed) teruskan(); });
        });
    });
});
</script>

@endsection
