{{-- Status pengesahan JAWI bagi satu item. Perlukan $mesyuarat dan $item --}}
@php
    $p = $mesyuarat->status_pengesahan ? $mesyuarat->pengesahanTerkini : null;
    $st = $p?->status($item);
    $catatan = $p?->catatan($item);
    $pembetulan = $mesyuarat->status_pengesahan === 'PERLU_PEMBETULAN';
    $dibetulkan = $pembetulan && $p?->telahDibetulkan($item);
    [$lbl, $cls, $ikon] = match (true) {
        $dibetulkan                => ['Telah dibetulkan — sedia untuk dihantar semula', 'bg-info-subtle text-info-emphasis', 'fa-circle-check'],
        $st === 'DITOLAK'          => [$pembetulan ? 'Ditolak JAWI — perlu dibetulkan' : 'Ditolak', 'bg-danger-subtle text-danger-emphasis', 'fa-circle-xmark'],
        $st === 'DILULUSKAN'       => ['Diluluskan JAWI', 'bg-success-subtle text-success-emphasis', 'fa-circle-check'],
        $st === 'MENUNGGU'         => ['Menunggu pengesahan JAWI', 'bg-warning-subtle text-warning-emphasis', 'fa-hourglass-half'],
        default                    => [null, null, null],
    };
@endphp
@if($lbl)
    <div class="small mb-1">
        <span class="badge {{ $cls }}"><i class="fa-solid {{ $ikon }} me-1"></i>{{ $lbl }}</span>
        @if($catatan)
            <div class="mt-1 p-2 rounded-2 {{ $st === 'DITOLAK' && !$dibetulkan ? 'bg-danger-subtle text-danger-emphasis' : 'bg-light text-muted' }}">
                <i class="fa-solid fa-comment-dots me-1"></i><strong>{{ $dibetulkan ? 'Catatan JAWI (telah diambil tindakan):' : 'Catatan JAWI:' }}</strong> {{ $catatan }}
            </div>
        @endif
    </div>
@endif
