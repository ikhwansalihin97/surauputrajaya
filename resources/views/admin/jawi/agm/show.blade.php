@extends('layouts.app')

@section('content')

@php
    $sesi = $mesyuarat->sesi;
    $bil1 = $mesyuarat->bilangan === 1;
    [$lbl, $cls, $ikon] = $mesyuarat->labelStatusPengesahan();
    $labelItem = \App\Models\SurauAgmPengesahan::ITEM_LABEL;
    $menunggu = $mesyuarat->status_pengesahan === 'MENUNGGU';
    $badgeItem = fn (?string $st) => match ($st) {
        'DILULUSKAN' => ['Diluluskan', 'bg-success'],
        'DITOLAK'    => ['Ditolak', 'bg-danger'],
        'MENUNGGU'   => ['Menunggu', 'bg-warning text-dark'],
        default      => ['-', 'bg-secondary'],
    };
    $calon = $pusingan->pentadbir_calon ?? [];
@endphp

<div class="container py-4">

    <div class="hero-welcome-card p-4 mb-4 shadow-sm">
        <div class="hero-shape-1"></div>
        <div class="hero-shape-2"></div>
        <div class="d-flex align-items-center justify-content-between position-relative flex-wrap gap-3">
            <div class="d-flex align-items-center">
                <div class="rounded-circle p-3 me-3 text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 52px; height: 52px; background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3);">
                    <i class="fa-solid fa-stamp fa-lg"></i>
                </div>
                <div>
                    <div class="badge bg-white bg-opacity-25 text-white mb-1" style="font-size:0.7rem; letter-spacing:0.05em;">
                        <i class="fa-solid fa-building-columns me-1"></i> PENGESAHAN AGM
                    </div>
                    <h4 class="fw-bold mb-0 text-white">{{ $surau->nama_surau }}</h4>
                    <small class="text-white-50">Mesyuarat Bil. {{ $mesyuarat->bilangan }} ({{ $mesyuarat->tahun }}) &bull; Sesi {{ $sesi->label }} &bull; {{ $mesyuarat->tarikhMelayu() }}</small>
                </div>
            </div>
            <div class="d-flex gap-2">
                @if($mesyuarat->bolehCetakPentauliahan())
                    <a href="{{ route('admin.jawi.agm.surat', $mesyuarat) }}" target="_blank" class="btn btn-success fw-bold shadow-sm"><i class="fa-solid fa-file-signature me-1"></i> Surat Pentauliahan</a>
                @endif
                <a href="{{ route('admin.jawi.agm.index') }}" class="btn btn-light fw-bold shadow-sm"><i class="fa-solid fa-arrow-left me-1"></i> Senarai</a>
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

    {{-- ── MAKLUMAT MESYUARAT ── --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-4">
            <div class="row g-3 small">
                <div class="col-md-3"><div class="text-muted">Status</div><span class="badge {{ $cls }}"><i class="fa-solid {{ $ikon }} me-1"></i>{{ $lbl }}</span></div>
                <div class="col-md-3"><div class="text-muted">Kariah Masjid</div><div class="fw-semibold">{{ $surau->masjid?->nama_masjid ?? '-' }}</div></div>
                <div class="col-md-3"><div class="text-muted">Masa &amp; Lokasi</div><div class="fw-semibold">{{ $mesyuarat->masaMelayu() }}, {{ $mesyuarat->lokasi }}</div></div>
                <div class="col-md-3"><div class="text-muted">Kehadiran</div><div class="fw-semibold">{{ $mesyuarat->kehadiran->count() }} orang</div></div>
                <div class="col-md-3"><div class="text-muted">Pentadbir Surau Semasa</div><div class="fw-semibold">{{ $surau->user?->name ?? '-' }}</div><div class="text-muted">{{ $surau->user?->email }}</div></div>
                <div class="col-md-3"><div class="text-muted">Dihantar</div><div class="fw-semibold">{{ $pusingan->dihantar_at?->format('d/m/Y g:i A') }}</div><div class="text-muted">oleh {{ $pusingan->penghantar?->name ?? '-' }} &bull; pusingan {{ $pusingan->pusingan }}</div></div>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.jawi.agm.putuskan', $mesyuarat) }}" method="POST" id="formKeputusan">
        @csrf

        @foreach($mesyuarat->itemPengesahan() as $no => $item)
            @php
                $st = $pusingan->status($item);
                [$bl, $bc] = $badgeItem($st);
                $boleh = $menunggu && $st === 'MENUNGGU';
                $pilih = old("keputusan.$item.status");
            @endphp
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center" style="border-bottom:2px solid #eff6ff;">
                    <div class="fw-bold">{{ chr(65 + $no) }}. {{ $labelItem[$item] }}</div>
                    <span class="badge {{ $bc }}">{{ $bl }}</span>
                </div>
                <div class="card-body p-4">
                    @switch($item)
                        @case('minit')
                        @case('laporan_kewangan')
                            @php $path = $item === 'minit' ? $mesyuarat->minit_path : $mesyuarat->laporan_kewangan_path; @endphp
                            @if($path)
                                <a href="{{ Storage::url($path) }}" target="_blank" class="btn btn-sm btn-outline-danger fw-semibold">
                                    <i class="fa-solid fa-file-pdf me-1"></i> Buka {{ strtolower($labelItem[$item]) }} (PDF)
                                </a>
                            @else
                                <span class="text-muted small">Tiada fail.</span>
                            @endif
                            @break
                        @case('ajk')
                            <div class="border rounded-3">@include('surau.agm._jadual-ajk', ['ajk' => $ajk])</div>
                            @break
                        @case('pentadbir')
                            @if($pusingan->pentadbir_pilihan === 'KEKAL')
                                <div class="small"><i class="fa-solid fa-user-check me-1 text-success"></i> Kekalkan Pentadbir Surau semasa: <strong>{{ $surau->user?->name }}</strong> ({{ $surau->user?->email }})</div>
                            @else
                                <div class="row g-2 small">
                                    <div class="col-md-6">
                                        <div class="text-muted">Pentadbir semasa</div>
                                        <div class="fw-semibold">{{ $surau->user?->name ?? '-' }}</div>
                                        <div class="text-muted">{{ $surau->user?->email }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="text-muted">Pentadbir baharu (calon)</div>
                                        <div class="fw-semibold">{{ $calon['nama'] ?? '-' }} <span class="badge bg-light text-dark border">{{ $calon['jawatan'] ?? '' }}</span></div>
                                        <div class="text-muted">{{ $calon['email'] ?? '' }} &bull; {{ $calon['no_tel'] ?? '' }} &bull; K/P {{ $calon['ic_no'] ?? '' }}</div>
                                    </div>
                                </div>
                                <div class="small text-muted mt-2"><i class="fa-solid fa-circle-info me-1"></i> Jika semua item diluluskan, permintaan serah tugas akan dihantar kepada calon. Calon perlu log masuk dan menerima sebelum menjadi Pentadbir Surau.</div>
                                @if($pertukaran && $mesyuarat->status_pengesahan === 'DISAHKAN')
                                    <div class="small mt-2"><span class="text-muted">Status serah tugas:</span> <strong>{{ $pertukaran->status === 'MENUNGGU' && $pertukaran->telahTamat() ? 'TAMAT TEMPOH' : $pertukaran->status }}</strong></div>
                                @endif
                            @endif
                            @break
                    @endswitch

                    @if($boleh)
                        <hr>
                        <div class="d-flex flex-wrap gap-3 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="keputusan[{{ $item }}][status]" id="lulus-{{ $item }}" value="DILULUSKAN" {{ $pilih === 'DILULUSKAN' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold text-success" for="lulus-{{ $item }}"><i class="fa-solid fa-check me-1"></i>Lulus</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="keputusan[{{ $item }}][status]" id="tolak-{{ $item }}" value="DITOLAK" {{ $pilih === 'DITOLAK' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold text-danger" for="tolak-{{ $item }}"><i class="fa-solid fa-xmark me-1"></i>Tolak</label>
                            </div>
                        </div>
                        <textarea name="keputusan[{{ $item }}][catatan]" rows="2" class="form-control form-control-sm @error("keputusan.$item.catatan") is-invalid @enderror" placeholder="Catatan (wajib jika ditolak)">{{ old("keputusan.$item.catatan") }}</textarea>
                    @elseif($pusingan->catatan($item))
                        <div class="small mt-3 p-2 rounded-2 bg-light"><i class="fa-solid fa-comment-dots me-1"></i><strong>Catatan:</strong> {{ $pusingan->catatan($item) }}</div>
                    @endif
                    @if($menunggu && $st === 'DILULUSKAN')
                        <div class="small text-muted mt-2"><i class="fa-solid fa-lock me-1"></i> Telah diluluskan dalam pusingan terdahulu.</div>
                    @endif
                </div>
            </div>
        @endforeach

        @if($menunggu)
            <div class="d-flex justify-content-end mb-4">
                <button type="submit" class="btn btn-primary fw-bold px-4"><i class="fa-solid fa-stamp me-1"></i> Simpan Keputusan</button>
            </div>
        @endif
    </form>

    {{-- ── SEJARAH PENGESAHAN ── --}}
    @if($mesyuarat->pengesahan->count() > 1)
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3 fw-bold" style="border-bottom:2px solid #eff6ff;"><i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i>Sejarah Pengesahan</div>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0 small">
                    <thead class="table-light text-muted text-uppercase">
                        <tr>
                            <th class="ps-3">Pusingan</th>
                            <th>Dihantar</th>
                            @foreach($mesyuarat->itemPengesahan() as $item)<th>{{ $labelItem[$item] }}</th>@endforeach
                            <th>Disemak</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($mesyuarat->pengesahan as $p)
                            <tr>
                                <td class="ps-3 fw-semibold">{{ $p->pusingan }}</td>
                                <td>{{ $p->dihantar_at?->format('d/m/Y g:i A') }}</td>
                                @foreach($mesyuarat->itemPengesahan() as $item)
                                    @php [$bl, $bc] = $badgeItem($p->status($item)); @endphp
                                    <td>
                                        <span class="badge {{ $bc }}">{{ $bl }}</span>
                                        @if($p->catatan($item))<div class="text-muted">{{ $p->catatan($item) }}</div>@endif
                                    </td>
                                @endforeach
                                <td>{{ $p->disemak_at ? $p->disemak_at->format('d/m/Y g:i A') . ' — ' . ($p->penyemak?->name ?? '') : '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formKeputusan');
    form?.addEventListener('submit', (e) => {
        if (typeof Swal === 'undefined' || form.dataset.sah) return;
        e.preventDefault();
        Swal.fire({ icon: 'question', title: 'Simpan Keputusan Pengesahan?', text: 'E-mel status pengesahan akan dihantar kepada Pentadbir Surau semasa. Item yang ditolak perlu dibetulkan oleh surau.', showCancelButton: true, confirmButtonText: 'Ya, Simpan', cancelButtonText: 'Batal', confirmButtonColor: '#1e40af' })
            .then(r => { if (r.isConfirmed) { form.dataset.sah = 1; form.submit(); } });
    });
});
</script>

@endsection
