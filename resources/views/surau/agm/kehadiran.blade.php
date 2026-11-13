@extends('layouts.app')

@section('content')

@php
    $sesi = $mesyuarat->sesi;
    $barisBaharu = old('kehadiran') ?: [['nama' => '', 'email' => '']];
@endphp

<div class="container py-4">

    @include('surau._pemilih')

    <div class="hero-welcome-card p-4 mb-4 shadow-sm">
        <div class="hero-shape-1"></div>
        <div class="hero-shape-2"></div>
        <div class="d-flex align-items-center position-relative">
            <div class="rounded-circle p-3 me-3 text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 52px; height: 52px; background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3);">
                <i class="fa-solid fa-clipboard-user fa-lg"></i>
            </div>
            <div>
                <div class="badge bg-white bg-opacity-25 text-white mb-1" style="font-size:0.7rem; letter-spacing:0.05em;">
                    <i class="fa-solid fa-people-roof me-1"></i> KEHADIRAN MESYUARAT AGUNG TAHUNAN
                </div>
                <h4 class="fw-bold mb-0 text-white">Mesyuarat Bil. {{ $mesyuarat->bilangan }} ({{ $mesyuarat->tahun }}) &mdash; Sesi {{ $sesi->label }}</h4>
                <small class="text-white-50">{{ $surau->nama_surau }} &bull; {{ $mesyuarat->tarikhMelayu() }}, {{ $mesyuarat->masaMelayu() }} &bull; {{ $mesyuarat->lokasi }}</small>
            </div>
        </div>
    </div>

    @include('surau.agm._langkah', ['aktif' => 3])

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4 alert-dismissible fade show">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-warning border-0 shadow-sm mb-4">
            <i class="fa-solid fa-circle-info me-2"></i> {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4">
            <div class="fw-bold mb-1"><i class="fa-solid fa-triangle-exclamation me-2"></i>Sila betulkan ralat berikut:</div>
            <ul class="mb-0 small ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($mesyuarat->tarikh->isFuture())
        <div class="alert alert-info border-0 shadow-sm small mb-4">
            <i class="fa-solid fa-calendar-day me-1"></i>
            Mesyuarat ini belum diadakan ({{ $mesyuarat->tarikhMelayu() }}). Kehadiran boleh direkodkan semasa atau selepas mesyuarat.
        </div>
    @endif

    <div class="row g-4">
        <div class="col-xl-5">
            {{-- Tambah daripada senarai jemputan --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center gap-2" style="border-bottom:2px solid #eff6ff;">
                    <div class="fw-bold"><i class="fa-solid fa-envelope-open-text me-2 text-success"></i>Daripada Senarai Jemputan</div>
                    @if($jemputanBelumHadir->isNotEmpty())
                        <button type="submit" form="formDariJemputan" class="btn btn-sm btn-success fw-bold" id="btnDariJemputan" disabled>
                            <i class="fa-solid fa-user-check me-1"></i> Tandakan Hadir (<span id="kiraanJemputan">0</span>)
                        </button>
                    @endif
                </div>
                @if($mesyuarat->jemputan->isEmpty())
                    <div class="card-body small text-muted">
                        Tiada senarai jemputan bagi mesyuarat ini. Tambah kehadiran secara manual di bawah.
                    </div>
                @elseif($jemputanBelumHadir->isEmpty())
                    <div class="card-body small text-success">
                        <i class="fa-solid fa-circle-check me-1"></i> Semua jemputan telah direkodkan hadir.
                    </div>
                @else
                    <form action="{{ route('surau.agm.kehadiran.dariJemputan', $mesyuarat) }}" method="POST" id="formDariJemputan">
                        @csrf
                        <div class="table-responsive" style="max-height:360px;">
                            <table class="table table-sm align-middle mb-0 small">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th class="ps-3" style="width:36px;"><input type="checkbox" class="form-check-input" id="pilihSemuaJemputan" title="Pilih semua"></th>
                                        <th>Nama</th>
                                        <th class="text-end pe-3"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($jemputanBelumHadir as $j)
                                        <tr>
                                            <td class="ps-3"><input type="checkbox" class="form-check-input pilih-jemputan" name="jemputan_ids[]" value="{{ $j->id }}"></td>
                                            <td>
                                                <div class="fw-semibold">{{ $j->nama }}</div>
                                                <div class="text-muted text-break" style="font-size:0.72rem;">{{ $j->email }}</div>
                                            </td>
                                            <td class="text-end pe-3">
                                                <button type="submit" form="hadir-{{ $j->id }}" class="btn btn-sm btn-outline-success text-nowrap" title="Tandakan hadir">
                                                    <i class="fa-solid fa-user-check me-1"></i>Hadir
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </form>
                    @foreach($jemputanBelumHadir as $j)
                        <form action="{{ route('surau.agm.kehadiran.dariJemputan', $mesyuarat) }}" method="POST" id="hadir-{{ $j->id }}" class="d-none">
                            @csrf
                            <input type="hidden" name="jemputan_ids[]" value="{{ $j->id }}">
                        </form>
                    @endforeach
                @endif
            </div>

            {{-- Tambah manual --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 fw-bold" style="border-bottom:2px solid #eff6ff;">
                    <i class="fa-solid fa-user-plus me-2 text-primary"></i>Tambah Kehadiran
                    <div class="small text-muted fw-normal mt-1">Bagi yang hadir tanpa jemputan melalui sistem.</div>
                </div>
                <div class="card-body">
                    <form action="{{ route('surau.agm.kehadiran.store', $mesyuarat) }}" method="POST" id="formTambahKehadiran" novalidate>
                        @csrf
                        <div id="barisKehadiran">
                            @foreach($barisBaharu as $i => $row)
                                <div class="border rounded-3 p-2 mb-2 ajk-row baris-kehadiran">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="badge bg-secondary-subtle text-secondary-emphasis ajk-bil">Hadirin {{ $i + 1 }}</span>
                                        <button type="button" class="btn btn-sm btn-link text-danger p-0 btn-buang-baris" title="Buang baris"><i class="fa-solid fa-xmark"></i></button>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-sm-6">
                                            <label class="form-label small fw-semibold mb-0">Nama <span class="text-danger">*</span></label>
                                            <input type="text" name="kehadiran[{{ $i }}][nama]" value="{{ $row['nama'] ?? '' }}" maxlength="255" required
                                                   class="form-control form-control-sm @error("kehadiran.$i.nama") is-invalid @enderror">
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label small fw-semibold mb-0">E-mel</label>
                                            <input type="email" name="kehadiran[{{ $i }}][email]" value="{{ $row['email'] ?? '' }}" maxlength="255"
                                                   class="form-control form-control-sm @error("kehadiran.$i.email") is-invalid @enderror">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary w-100 mb-2" id="btnTambahBaris">
                            <i class="fa-solid fa-plus me-1"></i> Tambah Baris
                        </button>
                        <button type="submit" class="btn btn-sm btn-primary fw-bold w-100">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Kehadiran
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Senarai kehadiran --}}
        <div class="col-xl-7">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2" style="border-bottom:2px solid #eff6ff;">
                    <div class="fw-bold">
                        <i class="fa-solid fa-clipboard-user me-2 text-primary"></i>Senarai Kehadiran
                        <span class="badge bg-primary ms-1">{{ $mesyuarat->kehadiran->count() }} hadir</span>
                    </div>
                    @if($mesyuarat->jemputan->isNotEmpty())
                        <span class="small text-muted">{{ $mesyuarat->kehadiran->whereNotNull('agm_jemputan_id')->count() }} daripada {{ $mesyuarat->jemputan->count() }} jemputan hadir</span>
                    @endif
                </div>
                @if($mesyuarat->kehadiran->isEmpty())
                    <div class="card-body text-center text-muted py-5 small">
                        <i class="fa-solid fa-clipboard-list fa-2x mb-2 d-block opacity-50"></i>
                        Belum ada kehadiran direkodkan.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0 small">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3" style="width:44px;">Bil.</th>
                                    <th>Nama</th>
                                    <th>E-mel</th>
                                    <th>Sumber</th>
                                    <th class="text-end pe-3"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mesyuarat->kehadiran as $k)
                                    <tr>
                                        <td class="ps-3 text-muted">{{ $loop->iteration }}</td>
                                        <td class="fw-semibold">{{ $k->nama }}</td>
                                        <td class="text-break">{{ $k->email ?: '-' }}</td>
                                        <td>
                                            @if($k->agm_jemputan_id)
                                                <span class="badge bg-success-subtle text-success-emphasis">Jemputan</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary-emphasis">Manual</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-3">
                                            <form action="{{ route('surau.agm.kehadiran.destroy', [$mesyuarat, $k]) }}" method="POST" class="form-buang-kehadiran" data-nama="{{ $k->nama }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Buang"><i class="fa-solid fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="d-flex flex-column flex-sm-row justify-content-between gap-2">
                <a href="{{ route('surau.agm.jemputan.index', $mesyuarat) }}" class="btn btn-light border fw-semibold px-4">
                    <i class="fa-solid fa-arrow-left me-1"></i> Jemputan Mesyuarat
                </a>
                @if($mesyuarat->kehadiran->isNotEmpty())
                    <a href="{{ route('surau.agm.keputusan.index', $mesyuarat) }}" class="btn btn-primary fw-bold px-4">
                        Seterusnya: Keputusan Mesyuarat <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                @else
                    <button type="button" class="btn btn-primary fw-bold px-4" disabled title="Rekod kehadiran terlebih dahulu">
                        <i class="fa-solid fa-lock me-1"></i> Keputusan Mesyuarat
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

<template id="templatBaris">
    <div class="border rounded-3 p-2 mb-2 ajk-row baris-kehadiran">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="badge bg-secondary-subtle text-secondary-emphasis ajk-bil">Hadirin</span>
            <button type="button" class="btn btn-sm btn-link text-danger p-0 btn-buang-baris" title="Buang baris"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="row g-2">
            <div class="col-sm-6">
                <label class="form-label small fw-semibold mb-0">Nama <span class="text-danger">*</span></label>
                <input type="text" name="kehadiran[__i__][nama]" maxlength="255" required class="form-control form-control-sm">
            </div>
            <div class="col-sm-6">
                <label class="form-label small fw-semibold mb-0">E-mel</label>
                <input type="email" name="kehadiran[__i__][email]" maxlength="255" class="form-control form-control-sm">
            </div>
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // ── Baris tambah kehadiran ──
    const senarai = document.getElementById('barisKehadiran');
    const templat = document.getElementById('templatBaris');
    const susun = () => {
        const rows = senarai.querySelectorAll('.baris-kehadiran');
        rows.forEach((row, i) => {
            row.querySelector('.ajk-bil').textContent = 'Hadirin ' + (i + 1);
            row.querySelectorAll('[name]').forEach(el => el.name = el.name.replace(/^kehadiran\[[^\]]*\]/, `kehadiran[${i}]`));
            row.querySelector('.btn-buang-baris').classList.toggle('invisible', rows.length === 1);
        });
    };
    document.getElementById('btnTambahBaris').addEventListener('click', () => {
        const row = templat.content.firstElementChild.cloneNode(true);
        senarai.appendChild(row);
        susun();
        row.querySelector('input').focus();
    });
    senarai.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-buang-baris');
        if (!btn || senarai.querySelectorAll('.baris-kehadiran').length === 1) return;
        btn.closest('.baris-kehadiran').remove();
        susun();
    });
    susun();

    // ── Pilih jemputan ──
    const kotak = [...document.querySelectorAll('.pilih-jemputan')];
    const pilihSemua = document.getElementById('pilihSemuaJemputan');
    const btn = document.getElementById('btnDariJemputan');
    if (pilihSemua && btn) {
        const kemaskini = () => {
            const n = kotak.filter(k => k.checked).length;
            document.getElementById('kiraanJemputan').textContent = n;
            btn.disabled = n === 0;
            pilihSemua.checked = n > 0 && n === kotak.length;
            pilihSemua.indeterminate = n > 0 && n < kotak.length;
        };
        pilihSemua.addEventListener('change', () => { kotak.forEach(k => k.checked = pilihSemua.checked); kemaskini(); });
        kotak.forEach(k => k.addEventListener('change', kemaskini));
        kemaskini();
    }

    // ── Sahkan buang ──
    const esc = (s) => s.replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    document.querySelectorAll('.form-buang-kehadiran').forEach(form => {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            if (typeof Swal === 'undefined') { if (confirm(`Buang ${form.dataset.nama}?`)) form.submit(); return; }
            Swal.fire({
                icon: 'warning',
                title: 'Buang Kehadiran?',
                html: `<strong>${esc(form.dataset.nama)}</strong> akan dibuang daripada rekod kehadiran.`,
                showCancelButton: true,
                confirmButtonText: 'Ya, Buang',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc2626',
            }).then(r => { if (r.isConfirmed) form.submit(); });
        });
    });
});
</script>

@include('surau._validasi-borang', ['formId' => 'formTambahKehadiran'])

@endsection
