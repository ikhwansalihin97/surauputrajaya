{{-- Pemilih surau aktif: hanya dipaparkan jika pengguna mentadbir lebih daripada satu surau --}}
@php
    $pemilihSuraus = auth()->user()?->suraus ?? collect();
    $pemilihAktif  = auth()->user()?->activeSurau();
@endphp
@if($pemilihSuraus->count() > 1)
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body py-2 px-3 d-flex flex-column flex-md-row align-items-md-center gap-2">
            <div class="small fw-semibold text-muted flex-shrink-0">
                <i class="fa-solid fa-repeat me-1 text-primary"></i> Anda mentadbir {{ $pemilihSuraus->count() }} surau. Surau aktif:
            </div>
            <div class="dropdown flex-grow-1">
                <button class="btn btn-sm btn-outline-primary dropdown-toggle fw-semibold w-100 text-start" type="button" data-bs-toggle="dropdown">
                    {{ $pemilihAktif?->nama_surau }}
                </button>
                <ul class="dropdown-menu w-100 shadow-sm">
                    @foreach($pemilihSuraus as $item)
                        <li>
                            <form action="{{ route('surau.pilih', $item) }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item small {{ $pemilihAktif?->id === $item->id ? 'active' : '' }}">
                                    {{ $item->nama_surau }}
                                    @if($item->no_rujukan_jawi)
                                        <span class="text-muted font-monospace ms-1">({{ $item->no_rujukan_jawi }})</span>
                                    @endif
                                </button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif
