@extends('layouts.app')

@section('content')

<div class="container py-4">

    <div class="hero-welcome-card p-4 mb-4 shadow-sm">
        <div class="hero-shape-1"></div>
        <div class="hero-shape-2"></div>
        <div class="d-flex align-items-center position-relative">
            <div class="rounded-circle p-3 me-3 text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 52px; height: 52px; background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3);">
                <i class="fa-solid fa-envelope-open-text fa-lg"></i>
            </div>
            <div>
                <div class="badge bg-white bg-opacity-25 text-white mb-1" style="font-size:0.7rem; letter-spacing:0.05em;">
                    <i class="fa-solid fa-building-columns me-1"></i> MODUL JAWI
                </div>
                <h4 class="fw-bold mb-0 text-white">Tetapan Surat</h4>
                <small class="text-white-50">Kepala surat, no. rujukan dan salinan kepada bagi Surat Pentauliahan AJK.</small>
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
            <ul class="mb-0 small ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="alert alert-light border small mb-4">
        <i class="fa-solid fa-signature me-1 text-primary"></i>
        Tandatangan pegawai ditetapkan oleh setiap pegawai di halaman <a href="{{ route('profile.edit') }}#tandatangan" class="fw-semibold">Profil Saya</a>.
        Surat pentauliahan ditandatangani oleh Pentadbir JAWI yang mengesahkan keputusan mesyuarat.
    </div>

    <form action="{{ route('admin.jawi.tetapan-surat.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3 fw-bold" style="border-bottom:2px solid #eff6ff;"><i class="fa-solid fa-image me-2 text-primary"></i>Kepala Surat</div>
            <div class="card-body p-4">
                <p class="small text-muted mb-3">
                    Secara lalai, kepala surat rasmi JAWI dijana oleh sistem. Muat naik imej kepala surat (PNG/JPG, lebar penuh, maks. 4MB)
                    untuk menggantikannya.
                </p>
                @if($kepalaSurat)
                    <div class="border rounded-3 p-2 mb-3 bg-light">
                        <img src="{{ Storage::url($kepalaSurat) }}" class="img-fluid" alt="Kepala surat">
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="buang_kepala_surat" value="1" id="buangKepala">
                        <label class="form-check-label small" for="buangKepala">Buang imej dan guna kepala surat lalai sistem</label>
                    </div>
                @else
                    <div class="small mb-3"><span class="badge bg-success-subtle text-success-emphasis">Menggunakan kepala surat lalai sistem</span></div>
                @endif
                <input type="file" name="kepala_surat" accept="image/png,image/jpeg" class="form-control">
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3 fw-bold" style="border-bottom:2px solid #eff6ff;"><i class="fa-solid fa-hashtag me-2 text-primary"></i>No. Rujukan</div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label small fw-semibold">No. Rujukan Fail</label>
                        <input type="text" name="no_rujukan_fail" class="form-control" value="{{ old('no_rujukan_fail', $tetapan['surat.no_rujukan_fail']) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Bilangan Seterusnya</label>
                        <input type="number" min="1" name="bil_seterusnya" class="form-control" value="{{ old('bil_seterusnya', $tetapan['surat.bil_seterusnya']) }}" required>
                    </div>
                </div>
                <div class="small text-muted mt-2">
                    Surat seterusnya akan bernombor:
                    <strong class="font-monospace">{{ $tetapan['surat.no_rujukan_fail'] }} ({{ $tetapan['surat.bil_seterusnya'] }})</strong>.
                    Nombor diberikan secara automatik apabila keputusan mesyuarat disahkan.
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3 fw-bold" style="border-bottom:2px solid #eff6ff;"><i class="fa-solid fa-file-lines me-2 text-primary"></i>Kandungan Surat</div>
            <div class="card-body p-4">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Pautan Garis Panduan</label>
                    <input type="url" name="pautan_garis_panduan" class="form-control" value="{{ old('pautan_garis_panduan', $tetapan['surat.pautan_garis_panduan']) }}">
                </div>
                <div>
                    <label class="form-label small fw-semibold">Salinan Kepada (s.k.)</label>
                    <textarea name="salinan_kepada" rows="8" class="form-control font-monospace small">{{ old('salinan_kepada', $tetapan['surat.salinan_kepada']) }}</textarea>
                    <div class="small text-muted mt-1">Satu penerima bagi setiap perenggan; pisahkan penerima dengan satu baris kosong. Pengerusi masjid kariah surau ditambah secara automatik.</div>
                </div>
            </div>
        </div>

        <div class="text-end mb-4">
            <button type="submit" class="btn btn-primary fw-bold px-4"><i class="fa-solid fa-floppy-disk me-1"></i> Simpan Tetapan</button>
        </div>
    </form>
</div>

@endsection
