@extends('layouts.app')

@section('content')

@php
    $sesi  = $mesyuarat->sesi;
    $bil1  = $mesyuarat->bilangan === 1;
    $pentadbirSemasa = $surau->user;
    $emelSemasa = strtolower((string) $pentadbirSemasa?->email);
    // E-mel AJK yang sudah mempunyai akaun dalam sistem
    $emelBerdaftar = \App\Models\User::whereIn(
        \Illuminate\Support\Facades\DB::raw('LOWER(email)'),
        $ajkSesi->pluck('email')->filter()->map(fn ($e) => strtolower(trim($e)))->values()->all()
    )->pluck('email')->map(fn ($e) => strtolower($e))->all();
    $pusingan = $mesyuarat->pengesahanTerkini;
    $calonEmel = strtolower((string) ($mesyuarat->pentadbir_calon['email'] ?? ''));
    $ubahPentadbir = $mesyuarat->bolehUbah('pentadbir');
@endphp

<div class="container py-4">

    @include('surau._pemilih')

    <div class="hero-welcome-card p-4 mb-4 shadow-sm">
        <div class="hero-shape-1"></div>
        <div class="hero-shape-2"></div>
        <div class="d-flex align-items-center position-relative">
            <div class="rounded-circle p-3 me-3 text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 52px; height: 52px; background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3);">
                <i class="fa-solid fa-gavel fa-lg"></i>
            </div>
            <div>
                <div class="badge bg-white bg-opacity-25 text-white mb-1" style="font-size:0.7rem; letter-spacing:0.05em;">
                    <i class="fa-solid fa-people-roof me-1"></i> KEPUTUSAN MESYUARAT AGUNG TAHUNAN
                </div>
                <h4 class="fw-bold mb-0 text-white">Mesyuarat Bil. {{ $mesyuarat->bilangan }} ({{ $mesyuarat->tahun }}) &mdash; Sesi {{ $sesi->label }}</h4>
                <small class="text-white-50">{{ $surau->nama_surau }} &bull; {{ $mesyuarat->tarikhMelayu() }}</small>
            </div>
        </div>
    </div>

    @include('surau.agm._langkah', ['aktif' => 4])

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

    {{-- ── STATUS PENGESAHAN JAWI ── --}}
    @if($mesyuarat->status_pengesahan === 'PERLU_PEMBETULAN')
        @php
            $ditolak = $mesyuarat->itemDitolak();
            $belum   = $mesyuarat->itemBelumDibetulkan();
            $selesai = empty($belum);
            $sauh    = ['minit' => '#dokumen', 'laporan_kewangan' => '#dokumen', 'ajk' => '#ajk', 'pentadbir' => '#pentadbir'];
        @endphp
        <div class="card border-0 shadow-sm rounded-3 mb-4" style="border-left:4px solid {{ $selesai ? '#0284c7' : '#dc2626' }} !important;">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                    <div>
                        @if($selesai)
                            <div class="fw-bold text-info-emphasis"><i class="fa-solid fa-circle-check me-1"></i> Semua pembetulan telah dibuat</div>
                            <div class="small text-muted">Langkah terakhir: hantar semula keputusan mesyuarat supaya JAWI boleh menyemak pembetulan anda.</div>
                        @else
                            <div class="fw-bold text-danger"><i class="fa-solid fa-rotate-left me-1"></i> JAWI meminta pembetulan</div>
                            <div class="small text-muted">{{ count($belum) }} daripada {{ count($ditolak) }} item belum dibetulkan. Betulkan item di bawah, kemudian hantar semula.</div>
                        @endif
                    </div>
                    @if($selesai)
                        <a href="#hantar" class="btn btn-success fw-bold btn-sm px-3"><i class="fa-solid fa-paper-plane me-1"></i> Pergi ke Hantar Semula</a>
                    @endif
                </div>
                <ul class="list-group small">
                    @foreach($ditolak as $item)
                        @php $ok = !in_array($item, $belum, true); @endphp
                        <li class="list-group-item d-flex justify-content-between align-items-center gap-2 {{ $ok ? 'bg-light' : '' }}">
                            <div>
                                <i class="fa-solid {{ $ok ? 'fa-square-check text-success' : 'fa-square text-danger' }} me-2"></i>
                                <strong class="{{ $ok ? 'text-muted text-decoration-line-through' : '' }}">{{ \App\Models\SurauAgmPengesahan::ITEM_LABEL[$item] }}</strong>
                                @if($pusingan->catatan($item))
                                    <div class="text-muted ms-4">Catatan JAWI: {{ $pusingan->catatan($item) }}</div>
                                @endif
                            </div>
                            @if($ok)
                                <span class="badge bg-success-subtle text-success-emphasis text-nowrap">Telah dibetulkan</span>
                            @else
                                <a href="{{ $sauh[$item] }}" class="btn btn-sm btn-outline-danger fw-semibold text-nowrap">Betulkan</a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @elseif($mesyuarat->status_pengesahan)
        @php [$lbl, $cls, $ikon] = $mesyuarat->labelStatusPengesahan(); @endphp
        <div class="alert {{ $mesyuarat->status_pengesahan === 'DISAHKAN' ? 'alert-success' : 'alert-warning' }} border-0 shadow-sm mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <span class="badge {{ $cls }} me-1"><i class="fa-solid {{ $ikon }} me-1"></i>{{ $lbl }}</span>
                    <span class="small">
                        {{ $mesyuarat->status_pengesahan === 'MENUNGGU' ? 'Keputusan mesyuarat sedang disemak oleh JAWI dan tidak boleh diubah.' : 'Keputusan mesyuarat telah disahkan oleh JAWI.' }}
                    </span>
                </div>
                <a href="{{ route('surau.agm.pengesahan.index', $mesyuarat) }}" class="btn btn-sm btn-light border fw-semibold">
                    <i class="fa-solid fa-stamp me-1"></i> Status Pengesahan
                </a>
            </div>
        </div>
    @endif

    {{-- ── A. DOKUMEN MESYUARAT ── --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4" id="dokumen">
        <div class="card-header bg-white py-3" style="border-bottom:2px solid #eff6ff;">
            <div class="fw-bold"><i class="fa-solid fa-file-pdf me-2 text-danger"></i>A. Dokumen Mesyuarat</div>
            <div class="small text-muted mt-1">
                {{ $bil1 ? 'Minit Mesyuarat Agung Tahunan.' : 'Minit Mesyuarat Agung Tahunan dan Laporan Kewangan.' }} Format PDF, maksimum 10MB setiap fail.
            </div>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('surau.agm.keputusan.dokumen', $mesyuarat) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @php
                    $dokumen = ['fail_minit' => ['Minit Mesyuarat', $mesyuarat->minit_path, 'minit']];
                    if (!$bil1) {
                        $dokumen['fail_laporan_kewangan'] = ['Laporan Kewangan', $mesyuarat->laporan_kewangan_path, 'laporan_kewangan'];
                    }
                    $adaBolehUbah = collect($dokumen)->contains(fn ($d) => $mesyuarat->bolehUbah($d[2]));
                @endphp
                <div class="row g-3">
                    @foreach($dokumen as $input => [$label, $path, $item])
                        @php $bolehUbahDok = $mesyuarat->bolehUbah($item); @endphp
                        <div class="col-md-6">
                            @include('surau.agm._status-item', ['item' => $item])
                            <label class="form-label fw-semibold small d-flex justify-content-between align-items-center">
                                <span>{{ $label }}</span>
                                @if($path)
                                    <span class="badge bg-success-subtle text-success-emphasis"><i class="fa-solid fa-check me-1"></i>Telah dimuat naik</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis">Belum dimuat naik</span>
                                @endif
                            </label>
                            @if($bolehUbahDok)
                                <input type="file" name="{{ $input }}" accept="application/pdf" class="form-control @error($input) is-invalid @enderror">
                            @endif
                            @if($path)
                                <a href="{{ Storage::url($path) }}" target="_blank" class="small fw-semibold text-decoration-none d-inline-block mt-1">
                                    <i class="fa-solid fa-file-pdf me-1 text-danger"></i> Lihat {{ strtolower($label) }} semasa
                                </a>
                                @if($bolehUbahDok)
                                    <span class="small text-muted"> &bull; muat naik fail baharu untuk menggantikan</span>
                                @endif
                            @endif
                        </div>
                    @endforeach
                </div>
                @if($adaBolehUbah)
                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-primary fw-bold px-4">
                            <i class="fa-solid fa-upload me-1"></i> Muat Naik
                        </button>
                    </div>
                @endif
            </form>
        </div>
    </div>

    @if($bil1)
        {{-- ── B. SENARAI AJK ── --}}
        <div class="d-flex align-items-center gap-2 mb-2 mt-2" id="ajk">
            <h6 class="fw-bold mb-0"><i class="fa-solid fa-sitemap me-2 text-primary"></i>B. Senarai AJK Sesi {{ $sesi->label }}</h6>
        </div>
        <p class="small text-muted mb-3">AJK yang dilantik dalam mesyuarat ini. Senarai yang sama dipaparkan di halaman Maklumat Jawatankuasa.</p>
        <div class="mb-3">@include('surau.agm._status-item', ['item' => 'ajk'])</div>
        @if($bolehUbahAjk)
            @include('surau._borang-ajk', ['kembali' => route('surau.agm.keputusan.index', $mesyuarat)])
        @elseif($sesiAktif)
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white small text-muted"><i class="fa-solid fa-lock me-1"></i> Senarai AJK telah dihantar untuk pengesahan JAWI dan tidak boleh dikemaskini.</div>
                @include('surau.agm._jadual-ajk', ['ajk' => $ajkSesi])
            </div>
        @else
            <div class="alert alert-secondary border-0 shadow-sm small mb-4">
                <i class="fa-solid fa-lock me-1"></i> Sesi {{ $sesi->label }} bukan sesi semasa. Senarai AJK sesi ini tidak lagi boleh dikemaskini.
            </div>
        @endif

        {{-- ── C. PENTADBIR SURAU ── --}}
        <div class="card border-0 shadow-sm rounded-3 mb-4" id="pentadbir">
            <div class="card-header bg-white py-3" style="border-bottom:2px solid #eff6ff;">
                <div class="fw-bold"><i class="fa-solid fa-user-shield me-2 text-primary"></i>C. Pentadbir Surau</div>
                <div class="small text-muted mt-1">
                    Kekalkan pentadbir semasa atau pilih pentadbir baharu daripada senarai AJK di atas. Permintaan serah tugas hanya
                    dihantar kepada calon <strong>selepas pilihan ini disahkan oleh JAWI</strong>. Pentadbir semasa kekal mempunyai akses
                    sehingga pentadbir baharu <strong>log masuk dan menerima</strong> serah tugas. Calon dikenal pasti melalui <strong>e-mel</strong>;
                    jika belum berdaftar, akaun baharu akan dicipta dan perlu diaktifkan melalui e-mel.
                </div>
            </div>
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-2 mb-3 small">
                    <span class="text-muted">Pentadbir semasa:</span>
                    <strong>{{ $pentadbirSemasa?->name ?? '-' }}</strong>
                    <span class="text-muted">({{ $pentadbirSemasa?->email }})</span>
                </div>

                @include('surau.agm._status-item', ['item' => 'pentadbir'])

                @if($mesyuarat->pentadbir_pilihan)
                    <div class="small mb-3 mt-2">
                        <span class="text-muted">Pilihan disimpan:</span>
                        <strong>{{ $mesyuarat->pentadbir_pilihan === 'KEKAL' ? 'Kekalkan ' . ($pentadbirSemasa?->name ?? 'pentadbir semasa') : ($mesyuarat->pentadbir_calon['nama'] ?? '-') . ' (' . ($mesyuarat->pentadbir_calon['jawatan'] ?? '') . ')' }}</strong>
                    </div>
                @endif
                <form action="{{ route('surau.agm.keputusan.pentadbir', $mesyuarat) }}" method="POST" id="formPentadbir">
                    @csrf
                    <div class="list-group mb-3">
                        <label class="list-group-item d-flex gap-3 align-items-center">
                            <input class="form-check-input flex-shrink-0" type="radio" name="calon" value="KEKAL" {{ $mesyuarat->pentadbir_pilihan === 'KEKAL' ? 'checked' : '' }} {{ $ubahPentadbir ? '' : 'disabled' }} data-label="kekalkan {{ $pentadbirSemasa?->name }}">
                            <div class="small">
                                <div class="fw-semibold">Kekalkan pentadbir semasa</div>
                                <div class="text-muted">{{ $pentadbirSemasa?->name }}</div>
                            </div>
                        </label>
                        @foreach($ajkSesi as $c)
                            @php
                                $emel = strtolower(trim((string) $c->email));
                                $sahEmel = (bool) filter_var($emel, FILTER_VALIDATE_EMAIL);
                                $sebab = $emel !== '' && $emel === $emelSemasa ? 'Pentadbir semasa'
                                       : (!$sahEmel ? 'E-mel diperlukan' : null);
                                $berdaftar = in_array($emel, $emelBerdaftar, true);
                                $dipilih = $mesyuarat->pentadbir_pilihan === 'BAHARU' && $emel !== '' && $calonEmel === $emel;
                            @endphp
                            <label class="list-group-item d-flex gap-3 align-items-center {{ $sebab ? 'bg-light text-muted' : '' }}">
                                <input class="form-check-input flex-shrink-0" type="radio" name="calon" value="{{ $c->id }}" {{ ($sebab || !$ubahPentadbir) ? 'disabled' : '' }} {{ $dipilih ? 'checked' : '' }} data-label="{{ $c->nama }}" data-emel="{{ $emel }}" data-berdaftar="{{ $berdaftar ? 1 : 0 }}">
                                <div class="small flex-grow-1">
                                    <div class="fw-semibold">{{ $c->nama }} <span class="badge bg-light text-dark border ms-1">{{ $c->jawatan }}</span></div>
                                    <div class="text-muted">{{ $c->email ?: 'Tiada e-mel' }}</div>
                                </div>
                                @if($sebab)
                                    <span class="small text-muted text-nowrap">{{ $sebab }}</span>
                                @elseif($dipilih)
                                    <span class="badge bg-primary-subtle text-primary-emphasis">Dipilih</span>
                                @elseif($berdaftar)
                                    <span class="badge bg-success-subtle text-success-emphasis" title="E-mel ini sudah mempunyai akaun. Calon hanya perlu log masuk dan menerima.">Akaun berdaftar</span>
                                @else
                                    <span class="badge bg-info-subtle text-info-emphasis" title="Akaun baharu akan dicipta dan e-mel pengaktifan dihantar.">Akaun baharu</span>
                                @endif
                            </label>
                        @endforeach
                    </div>
                    @if($ajkSesi->isEmpty())
                        <div class="small text-muted mb-3">Simpan senarai AJK di Bahagian B terlebih dahulu untuk memilih pentadbir baharu.</div>
                    @endif
                    @if($ubahPentadbir)
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary fw-bold px-4">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Pilihan Pentadbir
                            </button>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    @else
        <div class="alert alert-light border small mb-4">
            <i class="fa-solid fa-circle-info me-1 text-primary"></i>
            Senarai AJK dan Pentadbir Surau tidak dikemaskini dalam Mesyuarat Bil. 2. AJK sesi {{ $sesi->label }} kekal seperti yang dilantik dalam Mesyuarat Bil. 1.
        </div>
    @endif

    {{-- ── HANTAR UNTUK PENGESAHAN JAWI ── --}}
    @if($mesyuarat->bolehHantarPengesahan())
        <div class="card border-0 shadow-sm rounded-3 mb-4" id="hantar" style="border-left:4px solid #16a34a !important;">
            <div class="card-body p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="small">
                    <div class="fw-bold mb-1"><i class="fa-solid fa-paper-plane me-1 text-success"></i>
                        {{ $mesyuarat->status_pengesahan === 'PERLU_PEMBETULAN' ? 'Hantar Semula untuk Pengesahan JAWI' : 'Hantar untuk Pengesahan JAWI' }}
                    </div>
                    <div class="text-muted">
                        @if($mesyuarat->status_pengesahan === 'PERLU_PEMBETULAN')
                            Hanya item yang dibetulkan akan disemak semula oleh Pentadbir JAWI. Item yang telah diluluskan kekal diluluskan.
                        @else
                            {{ $bil1 ? 'Minit mesyuarat, senarai AJK dan pilihan Pentadbir Surau' : 'Minit mesyuarat dan laporan kewangan' }}
                            akan disemak oleh Pentadbir JAWI.
                        @endif
                        Maklumat tidak boleh diubah semasa disemak.
                    </div>
                    @if($kekurangan)
                        <ul class="text-danger mb-0 mt-2 ps-3">
                            @foreach($kekurangan as $k)<li>{{ $k }}</li>@endforeach
                        </ul>
                    @endif
                </div>
                <form action="{{ route('surau.agm.pengesahan.hantar', $mesyuarat) }}" method="POST" class="form-sahkan flex-shrink-0"
                      data-tajuk="Hantar untuk Pengesahan JAWI?" data-teks="Maklumat yang dihantar tidak boleh diubah sehingga JAWI membuat keputusan.">
                    @csrf
                    <button type="submit" class="btn btn-success fw-bold px-4" {{ $kekurangan ? 'disabled' : '' }}>
                        <i class="fa-solid fa-paper-plane me-1"></i> {{ $mesyuarat->status_pengesahan === 'PERLU_PEMBETULAN' ? 'Hantar Semula' : 'Hantar' }}
                    </button>
                </form>
            </div>
        </div>
    @endif

    <div class="d-flex flex-column flex-sm-row justify-content-between gap-2 mb-4">
        <a href="{{ route('surau.agm.kehadiran.index', $mesyuarat) }}" class="btn btn-light border fw-semibold px-4">
            <i class="fa-solid fa-arrow-left me-1"></i> Kehadiran Mesyuarat
        </a>
        <a href="{{ route('surau.agm.index') }}" class="btn btn-light border fw-semibold px-4">
            <i class="fa-solid fa-list me-1"></i> Senarai Mesyuarat AGM
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const esc = (s) => String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    const sahkan = (title, html, teruskan) => {
        if (typeof Swal === 'undefined') { if (confirm(title)) teruskan(); return; }
        Swal.fire({ icon: 'question', title, html, showCancelButton: true, confirmButtonText: 'Ya, Teruskan', cancelButtonText: 'Batal', confirmButtonColor: '#1e40af' })
            .then(r => { if (r.isConfirmed) teruskan(); });
    };

    document.querySelectorAll('.form-sahkan').forEach(form => {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            sahkan(form.dataset.tajuk, esc(form.dataset.teks), () => form.submit());
        });
    });

    const formPentadbir = document.getElementById('formPentadbir');
    formPentadbir?.addEventListener('submit', (e) => {
        e.preventDefault();
        const pilihan = formPentadbir.querySelector('input[name="calon"]:checked');
        if (!pilihan) return;
        if (pilihan.value === 'KEKAL') {
            sahkan('Kekalkan Pentadbir Surau?', 'Pilihan untuk mengekalkan Pentadbir Surau semasa akan disimpan.', () => formPentadbir.submit());
        } else {
            sahkan('Pilih Pentadbir Surau Baharu?',
                `<strong>${esc(pilihan.dataset.label)}</strong> (${esc(pilihan.dataset.emel)}) akan disimpan sebagai calon Pentadbir Surau.<br>`
                + '<span class="small">Permintaan serah tugas hanya dihantar kepada calon selepas JAWI mengesahkan keputusan mesyuarat.</span><br>'
                + (pilihan.dataset.berdaftar === '1'
                    ? '<span class="small">E-mel ini sudah berdaftar. Calon hanya perlu log masuk dan menerima serah tugas.</span>'
                    : '<span class="small">Akaun baharu akan dicipta untuk calon selepas disahkan JAWI.</span>'),
                () => formPentadbir.submit());
        }
    });
});
</script>

@endsection
