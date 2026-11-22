{{-- Langkah Mesyuarat AGM. $aktif: 1 = Butiran, 2 = Jemputan, 3 = Kehadiran, 4 = Keputusan, 5 = Status Pengesahan --}}
@php
    $adaKehadiran = $mesyuarat->relationLoaded('kehadiran') ? $mesyuarat->kehadiran->isNotEmpty() : $mesyuarat->kehadiran()->exists();
    $langkah = [
        1 => ['Butiran Mesyuarat', route('surau.agm.edit', $mesyuarat), null, $mesyuarat->dikunciPengesahan() ? 'Dikunci semasa pengesahan JAWI' : null],
        2 => ['Jemputan Mesyuarat', route('surau.agm.jemputan.index', $mesyuarat), 'pilihan', null],
        3 => ['Kehadiran Mesyuarat', route('surau.agm.kehadiran.index', $mesyuarat), null, null],
        4 => ['Keputusan Mesyuarat', route('surau.agm.keputusan.index', $mesyuarat), null, $adaKehadiran ? null : 'Rekod kehadiran mesyuarat terlebih dahulu'],
        5 => ['Status Pengesahan', route('surau.agm.pengesahan.index', $mesyuarat), null, $mesyuarat->status_pengesahan ? null : 'Hantar keputusan mesyuarat untuk pengesahan JAWI terlebih dahulu'],
    ];
    $ikonStatus = match ($mesyuarat->status_pengesahan) {
        'MENUNGGU'         => 'fa-hourglass-half',
        'PERLU_PEMBETULAN' => 'fa-circle-exclamation',
        'DISAHKAN'         => 'fa-circle-check',
        default            => null,
    };
@endphp
<div class="d-flex flex-wrap align-items-center gap-2 mb-4 small">
    @foreach($langkah as $no => [$label, $url, $nota, $kunci])
        @if($no > 1)
            <i class="fa-solid fa-chevron-right text-muted" style="font-size:0.7rem;"></i>
        @endif
        @if($kunci && $no !== $aktif)
            <span class="badge rounded-pill px-3 py-2 bg-light text-muted border" title="{{ $kunci }}">
                <i class="fa-solid fa-lock me-1"></i>{{ $no }}. {{ $label }}
            </span>
        @else
            <a href="{{ $url }}" class="badge rounded-pill px-3 py-2 text-decoration-none {{ $no === $aktif ? 'bg-primary' : 'bg-light text-secondary border' }}">
                @if($no === 5 && $ikonStatus)<i class="fa-solid {{ $ikonStatus }} me-1"></i>@endif{{ $no }}. {{ $label }}@if($nota) <span class="fw-normal opacity-75">({{ $nota }})</span>@endif
            </a>
        @endif
    @endforeach
</div>
