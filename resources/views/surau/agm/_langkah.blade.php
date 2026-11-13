{{-- Langkah Mesyuarat AGM. $aktif: 1 = Butiran, 2 = Jemputan, 3 = Kehadiran, 4 = Keputusan --}}
@php
    $adaKehadiran = $mesyuarat->relationLoaded('kehadiran') ? $mesyuarat->kehadiran->isNotEmpty() : $mesyuarat->kehadiran()->exists();
    $langkah = [
        1 => ['Butiran Mesyuarat', route('surau.agm.edit', $mesyuarat), null, false],
        2 => ['Jemputan Mesyuarat', route('surau.agm.jemputan.index', $mesyuarat), 'pilihan', false],
        3 => ['Kehadiran Mesyuarat', route('surau.agm.kehadiran.index', $mesyuarat), null, false],
        4 => ['Keputusan Mesyuarat', route('surau.agm.keputusan.index', $mesyuarat), null, !$adaKehadiran],
    ];
@endphp
<div class="d-flex flex-wrap align-items-center gap-2 mb-4 small">
    @foreach($langkah as $no => [$label, $url, $nota, $kunci])
        @if($no > 1)
            <i class="fa-solid fa-chevron-right text-muted" style="font-size:0.7rem;"></i>
        @endif
        @if($kunci)
            <span class="badge rounded-pill px-3 py-2 bg-light text-muted border" title="Rekod kehadiran mesyuarat terlebih dahulu">
                <i class="fa-solid fa-lock me-1"></i>{{ $no }}. {{ $label }}
            </span>
        @else
            <a href="{{ $url }}" class="badge rounded-pill px-3 py-2 text-decoration-none {{ $no === $aktif ? 'bg-primary' : 'bg-light text-secondary border' }}">
                {{ $no }}. {{ $label }}@if($nota) <span class="fw-normal opacity-75">({{ $nota }})</span>@endif
            </a>
        @endif
    @endforeach
</div>
