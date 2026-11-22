@extends('layouts.app')

@section('content')

@php
    $sesi = $mesyuarat->sesi;
    $bil1 = $mesyuarat->bilangan === 1;
    $pusingan = $mesyuarat->pengesahan->first();
    [$lbl, $cls, $ikon] = $mesyuarat->labelStatusPengesahan();
    $labelItem = \App\Models\SurauAgmPengesahan::ITEM_LABEL;
    $badgeItem = fn (?string $st) => match ($st) {
        'DILULUSKAN' => ['Diluluskan', 'bg-success'],
        'DITOLAK'    => ['Ditolak', 'bg-danger'],
        'MENUNGGU'   => ['Menunggu', 'bg-warning text-dark'],
        default      => ['-', 'bg-secondary'],
    };
@endphp

<div class="container py-4">

    @include('surau._pemilih')

    <div class="hero-welcome-card p-4 mb-4 shadow-sm">
        <div class="hero-shape-1"></div>
        <div class="hero-shape-2"></div>
        <div class="d-flex align-items-center position-relative">
            <div class="rounded-circle p-3 me-3 text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 52px; height: 52px; background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3);">
                <i class="fa-solid fa-stamp fa-lg"></i>
            </div>
            <div>
                <div class="badge bg-white bg-opacity-25 text-white mb-1" style="font-size:0.7rem; letter-spacing:0.05em;">
                    <i class="fa-solid fa-people-roof me-1"></i> STATUS PENGESAHAN JAWI
                </div>
                <h4 class="fw-bold mb-0 text-white">Mesyuarat Bil. {{ $mesyuarat->bilangan }} ({{ $mesyuarat->tahun }}) &mdash; Sesi {{ $sesi->label }}</h4>
                <small class="text-white-50">{{ $surau->nama_surau }} &bull; {{ $mesyuarat->tarikhMelayu() }}</small>
            </div>
        </div>
    </div>

    @include('surau.agm._langkah', ['aktif' => 5])

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4 alert-dismissible fade show">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm mb-4 alert-dismissible fade show">
            <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ── STATUS KESELURUHAN ── --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="badge {{ $cls }} fs-6 px-3 py-2"><i class="fa-solid {{ $ikon }} me-1"></i>{{ $lbl }}</span>
                <div class="small text-muted mt-2">
                    Dihantar {{ $pusingan->dihantar_at?->format('d/m/Y g:i A') }} oleh {{ $pusingan->penghantar?->name ?? '-' }}
                    @if($pusingan->pusingan > 1) &bull; Pusingan {{ $pusingan->pusingan }} @endif
                    @if($pusingan->disemak_at)
                        <br>Disemak {{ $pusingan->disemak_at->format('d/m/Y g:i A') }} oleh {{ $pusingan->penyemak?->name ?? 'Pentadbir JAWI' }}
                    @endif
                </div>
            </div>
            @if($mesyuarat->status_pengesahan === 'PERLU_PEMBETULAN')
                @php $belum = $mesyuarat->itemBelumDibetulkan(); @endphp
                <div class="text-md-end">
                    @if($belum)
                        <a href="{{ route('surau.agm.keputusan.index', $mesyuarat) }}" class="btn btn-danger fw-bold px-4">
                            <i class="fa-solid fa-pen-to-square me-1"></i> Betulkan &amp; Hantar Semula
                        </a>
                        <div class="small text-muted mt-1">{{ count($belum) }} item belum dibetulkan</div>
                    @else
                        <a href="{{ route('surau.agm.keputusan.index', $mesyuarat) }}#hantar" class="btn btn-success fw-bold px-4">
                            <i class="fa-solid fa-paper-plane me-1"></i> Hantar Semula
                        </a>
                        <div class="small text-muted mt-1">Semua pembetulan telah dibuat</div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- ── STATUS SETIAP ITEM ── --}}
    <div class="row g-3 mb-4">
        @foreach($mesyuarat->itemPengesahan() as $item)
            @php
                $dibetulkan = $mesyuarat->status_pengesahan === 'PERLU_PEMBETULAN' && $pusingan->telahDibetulkan($item);
                [$bl, $bc] = $dibetulkan ? ['Dibetulkan — belum dihantar', 'bg-info text-dark'] : $badgeItem($pusingan->status($item));
                $catatan = $pusingan->catatan($item);
            @endphp
            <div class="col-md-{{ $bil1 ? 4 : 6 }}">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="fw-bold small">
                                <i class="fa-solid {{ ['minit' => 'fa-file-lines', 'laporan_kewangan' => 'fa-file-invoice-dollar', 'ajk' => 'fa-sitemap', 'pentadbir' => 'fa-user-shield'][$item] }} me-1 text-primary"></i>
                                {{ $labelItem[$item] }}
                            </div>
                            <span class="badge {{ $bc }}">{{ $bl }}</span>
                        </div>
                        <div class="small text-muted">
                            @switch($item)
                                @case('minit')
                                    @if($mesyuarat->minit_path)
                                        <a href="{{ Storage::url($mesyuarat->minit_path) }}" target="_blank" class="text-decoration-none fw-semibold"><i class="fa-solid fa-file-pdf me-1 text-danger"></i>Lihat minit</a>
                                    @endif
                                    @break
                                @case('laporan_kewangan')
                                    @if($mesyuarat->laporan_kewangan_path)
                                        <a href="{{ Storage::url($mesyuarat->laporan_kewangan_path) }}" target="_blank" class="text-decoration-none fw-semibold"><i class="fa-solid fa-file-pdf me-1 text-danger"></i>Lihat laporan kewangan</a>
                                    @endif
                                    @break
                                @case('ajk')
                                    {{ \App\Models\SurauCommittee::where('agm_sesi_id', $mesyuarat->agm_sesi_id)->count() }} orang AJK sesi {{ $sesi->label }}
                                    @break
                                @case('pentadbir')
                                    @if($pusingan->pentadbir_pilihan === 'KEKAL')
                                        Kekalkan {{ $surau->user?->name ?? 'pentadbir semasa' }}
                                    @else
                                        Baharu: <strong>{{ $pusingan->pentadbir_calon['nama'] ?? '-' }}</strong> ({{ $pusingan->pentadbir_calon['jawatan'] ?? '' }})
                                    @endif
                                    @break
                            @endswitch
                        </div>
                        @if($catatan)
                            <div class="small mt-2 p-2 rounded-2 {{ $pusingan->status($item) === 'DITOLAK' && !$dibetulkan ? 'bg-danger-subtle text-danger-emphasis' : 'bg-light text-muted' }}">
                                <i class="fa-solid fa-comment-dots me-1"></i><strong>Catatan JAWI:</strong> {{ $catatan }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ── SURAT PENTAULIAHAN AJK ── --}}
    @if($mesyuarat->bolehCetakPentauliahan())
        <div class="card border-0 shadow-sm rounded-3 mb-4" style="border-left:4px solid #16a34a !important;">
            <div class="card-body p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:46px;height:46px;background:#dcfce7;">
                        <i class="fa-solid fa-file-signature text-success fa-lg"></i>
                    </div>
                    <div class="small">
                        <div class="fw-bold fs-6">Surat Pentauliahan Anggota Jawatankuasa</div>
                        <div class="text-muted">
                            Surat pelantikan AJK sesi {{ $sesi->label }} daripada JAWI beserta Lampiran senarai AJK.
                            @if($mesyuarat->surat_no_rujukan)<br>Ruj.: <span class="font-monospace">{{ $mesyuarat->surat_no_rujukan }}</span> &bull; {{ $mesyuarat->surat_tarikh?->format('d/m/Y') }}@endif
                        </div>
                    </div>
                </div>
                <a href="{{ route('surau.agm.pengesahan.surat', $mesyuarat) }}" target="_blank" class="btn btn-success fw-bold px-4 flex-shrink-0">
                    <i class="fa-solid fa-print me-1"></i> Cetak Surat Pentauliahan
                </a>
            </div>
        </div>
    @endif

    {{-- ── SERAH TUGAS PENTADBIR SURAU ── --}}
    @if($bil1 && ($mesyuarat->status_pengesahan === 'DISAHKAN' || $pertukaran))
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3" style="border-bottom:2px solid #eff6ff;">
                <div class="fw-bold"><i class="fa-solid fa-user-shield me-2 text-primary"></i>Serah Tugas Pentadbir Surau</div>
                <div class="small text-muted mt-1">Permintaan serah tugas dihantar kepada calon selepas JAWI mengesahkan keputusan mesyuarat. Pentadbir semasa kekal mempunyai akses sehingga calon menerima.</div>
            </div>
            <div class="card-body p-4 small">
                @if($mesyuarat->status_pengesahan === 'DISAHKAN' && $pusingan->pentadbir_pilihan === 'KEKAL')
                    <i class="fa-solid fa-circle-check text-success me-1"></i>
                    Pentadbir Surau semasa (<strong>{{ $surau->user?->name }}</strong>) dikekalkan.
                @elseif($pertukaran)
                    @php
                        $tamat = $pertukaran->status === 'MENUNGGU' && $pertukaran->telahTamat();
                        [$pl, $pc] = match (true) {
                            $tamat                              => ['Tamat tempoh', 'bg-danger'],
                            $pertukaran->status === 'MENUNGGU'  => ['Menunggu penerimaan calon', 'bg-warning text-dark'],
                            $pertukaran->status === 'DITERIMA'  => ['Diterima', 'bg-success'],
                            $pertukaran->status === 'DITOLAK'   => ['Ditolak oleh calon', 'bg-danger'],
                            default                             => ['Dibatalkan', 'bg-secondary'],
                        };
                    @endphp
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                        <div>
                            <div class="mb-1"><span class="badge {{ $pc }}">{{ $pl }}</span></div>
                            <div><strong>{{ $pertukaran->calon_nama }}</strong> ({{ $pertukaran->calon_jawatan }}) &bull; {{ $pertukaran->calon_email }}</div>
                            @if($pertukaran->status === 'MENUNGGU')
                                <div class="text-muted">
                                    @if(!$pertukaran->kepada)
                                        <span class="text-danger">Akaun calon tidak ditemui</span>
                                    @elseif(!$pertukaran->akaunDiaktifkan())
                                        <i class="fa-solid fa-envelope me-1"></i>Akaun baharu &mdash; menunggu calon mengaktifkan akaun
                                    @else
                                        <i class="fa-solid fa-user-check me-1"></i>Akaun berdaftar &mdash; menunggu calon log masuk dan menerima
                                    @endif
                                </div>
                                <div class="text-muted">
                                    Dihantar {{ $pertukaran->updated_at->format('d/m/Y g:i A') }} &bull;
                                    @if($tamat)
                                        <span class="text-danger fw-semibold">permintaan telah tamat tempoh</span>
                                    @else
                                        sah sehingga {{ $pertukaran->tamat_at->format('d/m/Y g:i A') }}
                                    @endif
                                </div>
                            @elseif($pertukaran->diputuskan_at)
                                <div class="text-muted">{{ $pl }} pada {{ $pertukaran->diputuskan_at->format('d/m/Y g:i A') }}</div>
                            @endif
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            @if($pertukaran->status === 'MENUNGGU')
                                <form action="{{ route('surau.agm.pengesahan.hantarSemulaSerahTugas', $mesyuarat) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary fw-semibold"><i class="fa-solid fa-paper-plane me-1"></i> Hantar Semula E-mel</button>
                                </form>
                            @endif
                            @if($bolehPilihSemula)
                                <form action="{{ route('surau.agm.pengesahan.pilihSemula', $mesyuarat) }}" method="POST" class="form-sahkan"
                                      data-tajuk="Pilih Semula Pentadbir Surau?"
                                      data-teks="{{ $pertukaran->status === 'MENUNGGU' ? 'Permintaan serah tugas kepada ' . $pertukaran->calon_nama . ' akan dibatalkan. ' : '' }}Anda perlu memilih Pentadbir Surau semula dan menghantar untuk pengesahan JAWI.">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger fw-semibold"><i class="fa-solid fa-rotate-left me-1"></i> {{ $pertukaran->status === 'MENUNGGU' ? 'Batal & Pilih Semula' : 'Pilih Semula' }}</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @elseif($bolehPilihSemula)
                    <div class="d-flex justify-content-between align-items-center gap-2">
                        <span class="text-muted">Tiada permintaan serah tugas ditemui.</span>
                        <form action="{{ route('surau.agm.pengesahan.pilihSemula', $mesyuarat) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger fw-semibold"><i class="fa-solid fa-rotate-left me-1"></i> Pilih Semula</button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- ── SEJARAH PENGESAHAN ── --}}
    @if($mesyuarat->pengesahan->count() > 1 || $pusingan->disemak_at)
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
                                    <td><span class="badge {{ $bc }}" @if($p->catatan($item)) title="{{ $p->catatan($item) }}" @endif>{{ $bl }}</span></td>
                                @endforeach
                                <td>{{ $p->disemak_at ? $p->disemak_at->format('d/m/Y g:i A') . ' — ' . ($p->penyemak?->name ?? '') : '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="d-flex flex-column flex-sm-row justify-content-between gap-2 mb-4">
        <a href="{{ route('surau.agm.keputusan.index', $mesyuarat) }}" class="btn btn-light border fw-semibold px-4">
            <i class="fa-solid fa-arrow-left me-1"></i> Keputusan Mesyuarat
        </a>
        <a href="{{ route('surau.agm.index') }}" class="btn btn-light border fw-semibold px-4">
            <i class="fa-solid fa-list me-1"></i> Senarai Mesyuarat AGM
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.form-sahkan').forEach(form => {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            if (typeof Swal === 'undefined') { if (confirm(form.dataset.tajuk)) form.submit(); return; }
            Swal.fire({ icon: 'question', title: form.dataset.tajuk, text: form.dataset.teks, showCancelButton: true, confirmButtonText: 'Ya, Teruskan', cancelButtonText: 'Batal', confirmButtonColor: '#1e40af' })
                .then(r => { if (r.isConfirmed) form.submit(); });
        });
    });
});
</script>

@endsection
