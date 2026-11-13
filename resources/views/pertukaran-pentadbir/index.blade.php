@extends('layouts.app')

@section('content')
@php
    $statusLabel = [
        'DITERIMA'   => ['Diterima', 'bg-success'],
        'DITOLAK'    => ['Ditolak', 'bg-danger'],
        'DIBATALKAN' => ['Dibatalkan', 'bg-secondary'],
        'MENUNGGU'   => ['Tamat Tempoh', 'bg-warning text-dark'],
    ];
@endphp
<div class="container py-4" style="max-width: 900px;">

    <div class="hero-welcome-card p-4 mb-4 shadow-sm">
        <div class="hero-shape-1"></div>
        <div class="hero-shape-2"></div>
        <div class="d-flex align-items-center position-relative">
            <div class="rounded-circle p-3 me-3 text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 52px; height: 52px; background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3);">
                <i class="fa-solid fa-user-shield fa-lg"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-0 text-white">Serah Tugas Pentadbir Surau</h4>
                <small class="text-white-50">Terima atau tolak pelantikan sebagai Pentadbir Surau dalam Sistem e-Surau JAWI.</small>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4 alert-dismissible fade show">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @forelse($menunggu as $p)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <p class="mb-3">
                    Anda telah dipilih sebagai <strong>Pentadbir Surau</strong> bagi surau berikut, menggantikan
                    <strong>{{ $p->dari?->name ?? 'pentadbir semasa' }}</strong>:
                </p>
                <table class="table table-sm small mb-3">
                    <tr><td class="text-muted" style="width:35%;">Nama Surau</td><td class="fw-semibold">{{ $p->surau->nama_surau }}</td></tr>
                    <tr><td class="text-muted">Kariah Masjid</td><td>{{ $p->surau->masjid?->nama_masjid ?? '-' }}</td></tr>
                    <tr><td class="text-muted">Jawatan AJK</td><td>{{ $p->calon_jawatan ?? '-' }}</td></tr>
                    <tr><td class="text-muted">Sah Sehingga</td><td>{{ $p->tamat_at?->locale('ms')->translatedFormat('j F Y, g:i A') ?? '-' }}</td></tr>
                </table>
                <p class="small text-muted mb-4">
                    Selepas menerima, anda akan mentadbir surau ini dan akses pentadbir semasa kepada surau ini akan ditamatkan.
                </p>
                <div class="d-flex flex-wrap gap-2 justify-content-end">
                    <form action="{{ route('pertukaran-pentadbir.tolak', $p) }}" method="POST" class="form-sahkan"
                          data-tajuk="Tolak Serah Tugas?" data-teks="Anda akan menolak pelantikan sebagai Pentadbir Surau bagi {{ $p->surau->nama_surau }}." data-butang="Ya, Tolak" data-warna="#dc2626">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger fw-semibold px-4">Tolak</button>
                    </form>
                    <form action="{{ route('pertukaran-pentadbir.terima', $p) }}" method="POST" class="form-sahkan"
                          data-tajuk="Terima Serah Tugas?" data-teks="Anda akan menjadi Pentadbir Surau bagi {{ $p->surau->nama_surau }}." data-butang="Ya, Terima" data-warna="#16a34a">
                        @csrf
                        <button type="submit" class="btn btn-success fw-bold px-4"><i class="fa-solid fa-check me-1"></i> Terima Serah Tugas</button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4 text-center text-muted">
                <i class="fa-solid fa-inbox fa-2x mb-2 opacity-50"></i>
                <div>Tiada permintaan serah tugas yang menunggu tindakan anda.</div>
                @unless($adaPeranan)
                    <form action="{{ route('logout') }}" method="POST" class="mt-3">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary btn-sm">Log Keluar</button>
                    </form>
                @endunless
            </div>
        </div>
    @endforelse

    @if($terdahulu->isNotEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold small">Rekod Terdahulu</div>
            <div class="table-responsive">
                <table class="table table-sm small align-middle mb-0">
                    <thead class="table-light"><tr><th class="ps-3">Surau</th><th>Status</th><th>Tarikh</th></tr></thead>
                    <tbody>
                        @foreach($terdahulu as $p)
                            @php [$label, $kelas] = $statusLabel[$p->status] ?? [$p->status, 'bg-secondary']; @endphp
                            <tr>
                                <td class="ps-3">{{ $p->surau?->nama_surau ?? '-' }}</td>
                                <td><span class="badge {{ $kelas }}">{{ $label }}</span></td>
                                <td>{{ ($p->diputuskan_at ?? $p->tamat_at)?->format('d/m/Y g:i A') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.form-sahkan').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (form.dataset.disahkan) return;
            e.preventDefault();
            Swal.fire({
                title: form.dataset.tajuk,
                text: form.dataset.teks,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: form.dataset.butang,
                cancelButtonText: 'Batal',
                confirmButtonColor: form.dataset.warna,
            }).then(function (r) {
                if (r.isConfirmed) { form.dataset.disahkan = '1'; form.submit(); }
            });
        });
    });
</script>
@endpush
