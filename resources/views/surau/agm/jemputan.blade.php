@extends('layouts.app')

@section('content')

@php
    $sesi = $mesyuarat->sesi;
    $barisBaharu = old('jemputan') ?: [['nama' => '', 'email' => '']];
    $jumlahDihantar = $mesyuarat->jemputan->where('status_hantar', 'SENT')->count();
@endphp

<div class="container py-4">

    @include('surau._pemilih')

    <div class="hero-welcome-card p-4 mb-4 shadow-sm">
        <div class="hero-shape-1"></div>
        <div class="hero-shape-2"></div>
        <div class="d-flex align-items-center position-relative">
            <div class="rounded-circle p-3 me-3 text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 52px; height: 52px; background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3);">
                <i class="fa-solid fa-envelope-open-text fa-lg"></i>
            </div>
            <div>
                <div class="badge bg-white bg-opacity-25 text-white mb-1" style="font-size:0.7rem; letter-spacing:0.05em;">
                    <i class="fa-solid fa-people-roof me-1"></i> JEMPUTAN MESYUARAT AGUNG TAHUNAN
                </div>
                <h4 class="fw-bold mb-0 text-white">Mesyuarat Bil. {{ $mesyuarat->bilangan }} ({{ $mesyuarat->tahun }}) &mdash; Sesi {{ $sesi->label }}</h4>
                <small class="text-white-50">{{ $surau->nama_surau }}</small>
            </div>
        </div>
    </div>

    @include('surau.agm._langkah', ['aktif' => 2])

    <div class="alert alert-light border small mb-4">
        <i class="fa-solid fa-circle-info me-1 text-primary"></i>
        Langkah ini <strong>pilihan</strong>. Hantar jemputan melalui sistem jika perlu, atau terus ke <strong>Kehadiran Mesyuarat</strong> selepas mesyuarat diadakan.
        Nama jemputan boleh ditandakan hadir dengan mudah di halaman kehadiran.
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4 alert-dismissible fade show">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm mb-4">
            <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
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

    <div class="row g-4">
        {{-- Butiran mesyuarat --}}
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 fw-bold d-flex justify-content-between align-items-center" style="border-bottom:2px solid #eff6ff;">
                    <span><i class="fa-solid fa-calendar-check me-2 text-primary"></i>Butiran Mesyuarat</span>
                    <a href="{{ route('surau.agm.edit', $mesyuarat) }}" class="btn btn-sm btn-outline-primary" title="Kemaskini butiran"><i class="fa-solid fa-pen"></i></a>
                </div>
                <div class="card-body small">
                    <div class="mb-2"><i class="fa-regular fa-calendar me-2 text-muted"></i>{{ $mesyuarat->tarikh->locale('ms')->translatedFormat('l, d F Y') }}</div>
                    <div class="mb-2"><i class="fa-regular fa-clock me-2 text-muted"></i>{{ \Carbon\Carbon::parse($mesyuarat->masa)->format('g:i A') }}</div>
                    <div class="mb-3"><i class="fa-solid fa-location-dot me-2 text-muted"></i>{{ $mesyuarat->lokasi }}</div>
                    <div class="text-muted">Butiran ini akan dimasukkan ke dalam e-mel jemputan.</div>
                </div>
            </div>

            {{-- Tambah jemputan --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 fw-bold" style="border-bottom:2px solid #eff6ff;">
                    <i class="fa-solid fa-user-plus me-2 text-primary"></i>Tambah Jemputan
                </div>
                <div class="card-body">
                    <form action="{{ route('surau.agm.jemputan.store', $mesyuarat) }}" method="POST" id="formTambahJemputan" novalidate>
                        @csrf
                        <div id="barisJemputan">
                            @foreach($barisBaharu as $i => $row)
                                <div class="border rounded-3 p-2 mb-2 ajk-row baris-jemputan">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="badge bg-secondary-subtle text-secondary-emphasis ajk-bil">Jemputan {{ $i + 1 }}</span>
                                        <button type="button" class="btn btn-sm btn-link text-danger p-0 btn-buang-baris" title="Buang baris"><i class="fa-solid fa-xmark"></i></button>
                                    </div>
                                    <div class="mb-1">
                                        <label class="form-label small fw-semibold mb-0">Nama <span class="text-danger">*</span></label>
                                        <input type="text" name="jemputan[{{ $i }}][nama]" value="{{ $row['nama'] ?? '' }}" maxlength="255" required
                                               class="form-control form-control-sm @error("jemputan.$i.nama") is-invalid @enderror">
                                    </div>
                                    <div>
                                        <label class="form-label small fw-semibold mb-0">E-mel <span class="text-danger">*</span></label>
                                        <input type="email" name="jemputan[{{ $i }}][email]" value="{{ $row['email'] ?? '' }}" maxlength="255" required
                                               class="form-control form-control-sm @error("jemputan.$i.email") is-invalid @enderror">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary w-100 mb-2" id="btnTambahBaris">
                            <i class="fa-solid fa-plus me-1"></i> Tambah Baris
                        </button>
                        <button type="submit" class="btn btn-sm btn-primary fw-bold w-100">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Simpan ke Senarai Jemputan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Senarai jemputan --}}
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2" style="border-bottom:2px solid #eff6ff;">
                    <div class="fw-bold">
                        <i class="fa-solid fa-users me-2 text-primary"></i>Senarai Jemputan
                        <span class="badge bg-light text-dark border ms-1">{{ $mesyuarat->jemputan->count() }}</span>
                        @if($mesyuarat->jemputan->isNotEmpty())
                            <span class="small text-muted fw-normal ms-1">&bull; {{ $jumlahDihantar }} telah dihantar</span>
                        @endif
                    </div>
                    @if($mesyuarat->jemputan->isNotEmpty())
                        <button type="submit" form="formHantar" class="btn btn-sm btn-success fw-bold" id="btnHantarPukal" disabled>
                            <i class="fa-solid fa-paper-plane me-1"></i> Hantar kepada Dipilih (<span id="kiraanDipilih">0</span>)
                        </button>
                    @endif
                </div>

                @if($mesyuarat->jemputan->isEmpty())
                    <div class="card-body text-center text-muted py-5 small">
                        <i class="fa-solid fa-user-group fa-2x mb-2 d-block opacity-50"></i>
                        Belum ada jemputan. Tambah nama dan e-mel jemputan di sebelah.
                    </div>
                @else
                    <form action="{{ route('surau.agm.jemputan.hantarPukal', $mesyuarat) }}" method="POST" id="formHantar">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0 small">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3" style="width:36px;">
                                            <input type="checkbox" class="form-check-input" id="pilihSemua" title="Pilih semua">
                                        </th>
                                        <th>Nama</th>
                                        <th>E-mel</th>
                                        <th>Status</th>
                                        <th class="text-end pe-3">Tindakan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($mesyuarat->jemputan as $j)
                                        <tr>
                                            <td class="ps-3">
                                                <input type="checkbox" class="form-check-input pilih-jemputan" name="ids[]" value="{{ $j->id }}">
                                            </td>
                                            <td class="fw-semibold">{{ $j->nama }}</td>
                                            <td class="text-break">{{ $j->email }}</td>
                                            <td>
                                                @if($j->status_hantar === 'SENT')
                                                    <span class="badge bg-success-subtle text-success-emphasis"><i class="fa-solid fa-check me-1"></i>Dihantar</span>
                                                    <div class="text-muted" style="font-size:0.72rem;">
                                                        {{ $j->dihantar_at?->format('d/m/Y g:i A') }}@if($j->bilangan_hantar > 1) &bull; {{ $j->bilangan_hantar }} kali @endif
                                                    </div>
                                                @elseif($j->status_hantar === 'FAILED')
                                                    <span class="badge bg-danger-subtle text-danger-emphasis"><i class="fa-solid fa-xmark me-1"></i>Gagal</span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary-emphasis">Belum dihantar</span>
                                                @endif
                                            </td>
                                            <td class="text-end pe-3 text-nowrap">
                                                <button type="submit" class="btn btn-sm {{ $j->status_hantar === 'SENT' ? 'btn-outline-success' : 'btn-success' }} btn-hantar-satu"
                                                        formaction="{{ route('surau.agm.jemputan.hantar', [$mesyuarat, $j]) }}"
                                                        data-nama="{{ $j->nama }}" data-email="{{ $j->email }}" data-semula="{{ $j->status_hantar === 'SENT' ? 1 : 0 }}">
                                                    <i class="fa-solid fa-paper-plane me-1"></i>{{ $j->status_hantar === 'SENT' ? 'Hantar Semula' : 'Hantar' }}
                                                </button>
                                                <button type="submit" form="padam-{{ $j->id }}" class="btn btn-sm btn-outline-danger btn-buang-jemputan" data-nama="{{ $j->nama }}" title="Buang">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </form>

                    @foreach($mesyuarat->jemputan as $j)
                        <form action="{{ route('surau.agm.jemputan.destroy', [$mesyuarat, $j]) }}" method="POST" id="padam-{{ $j->id }}" class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endforeach
                @endif
            </div>

            <div class="d-flex flex-column flex-sm-row justify-content-between gap-2">
                <a href="{{ route('surau.agm.index') }}" class="btn btn-light border fw-semibold px-4">
                    <i class="fa-solid fa-list me-1"></i> Senarai Mesyuarat AGM
                </a>
                <a href="{{ route('surau.agm.kehadiran.index', $mesyuarat) }}" class="btn btn-primary fw-bold px-4">
                    Seterusnya: Kehadiran Mesyuarat <i class="fa-solid fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<template id="templatBaris">
    <div class="border rounded-3 p-2 mb-2 ajk-row baris-jemputan">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="badge bg-secondary-subtle text-secondary-emphasis ajk-bil">Jemputan</span>
            <button type="button" class="btn btn-sm btn-link text-danger p-0 btn-buang-baris" title="Buang baris"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="mb-1">
            <label class="form-label small fw-semibold mb-0">Nama <span class="text-danger">*</span></label>
            <input type="text" name="jemputan[__i__][nama]" maxlength="255" required class="form-control form-control-sm">
        </div>
        <div>
            <label class="form-label small fw-semibold mb-0">E-mel <span class="text-danger">*</span></label>
            <input type="email" name="jemputan[__i__][email]" maxlength="255" required class="form-control form-control-sm">
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // ── Baris tambah jemputan ──
    const senarai = document.getElementById('barisJemputan');
    const templat = document.getElementById('templatBaris');
    const susun = () => {
        const rows = senarai.querySelectorAll('.baris-jemputan');
        rows.forEach((row, i) => {
            row.querySelector('.ajk-bil').textContent = 'Jemputan ' + (i + 1);
            row.querySelectorAll('[name]').forEach(el => el.name = el.name.replace(/^jemputan\[[^\]]*\]/, `jemputan[${i}]`));
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
        if (!btn || senarai.querySelectorAll('.baris-jemputan').length === 1) return;
        btn.closest('.baris-jemputan').remove();
        susun();
    });
    susun();

    // ── Pilihan & penghantaran ──
    const formHantar = document.getElementById('formHantar');
    if (!formHantar) return;

    const kotak = [...document.querySelectorAll('.pilih-jemputan')];
    const pilihSemua = document.getElementById('pilihSemua');
    const btnPukal = document.getElementById('btnHantarPukal');
    const kiraan = document.getElementById('kiraanDipilih');
    const kemaskiniPilihan = () => {
        const n = kotak.filter(k => k.checked).length;
        kiraan.textContent = n;
        btnPukal.disabled = n === 0;
        pilihSemua.checked = n > 0 && n === kotak.length;
        pilihSemua.indeterminate = n > 0 && n < kotak.length;
    };
    pilihSemua.addEventListener('change', () => { kotak.forEach(k => k.checked = pilihSemua.checked); kemaskiniPilihan(); });
    kotak.forEach(k => k.addEventListener('change', kemaskiniPilihan));
    kemaskiniPilihan();

    const sahkan = (opts, teruskan) => {
        if (typeof Swal === 'undefined') { if (confirm(opts.text)) teruskan(); return; }
        Swal.fire({
            icon: opts.icon || 'question',
            title: opts.title,
            html: opts.html,
            showCancelButton: true,
            confirmButtonText: opts.confirm,
            cancelButtonText: 'Batal',
            confirmButtonColor: opts.color || '#16a34a',
        }).then(r => {
            if (!r.isConfirmed) return;
            Swal.fire({ title: 'Menghantar...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            teruskan();
        });
    };
    const esc = (s) => s.replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

    formHantar.addEventListener('submit', function (e) {
        e.preventDefault();
        const btn = e.submitter;
        if (btn && btn.classList.contains('btn-hantar-satu')) {
            const semula = btn.dataset.semula === '1';
            sahkan({
                title: semula ? 'Hantar Semula Jemputan?' : 'Hantar Jemputan?',
                html: `E-mel jemputan akan dihantar kepada <strong>${esc(btn.dataset.nama)}</strong><br><span class="small">${esc(btn.dataset.email)}</span>`,
                text: `Hantar jemputan kepada ${btn.dataset.email}?`,
                confirm: 'Ya, Hantar',
            }, () => { formHantar.action = btn.formAction; HTMLFormElement.prototype.submit.call(formHantar); });
            return;
        }
        const dipilih = kotak.filter(k => k.checked).length;
        if (!dipilih) return;
        sahkan({
            title: 'Hantar Jemputan Pukal?',
            html: `E-mel jemputan akan dihantar kepada <strong>${dipilih} orang</strong> yang dipilih.`,
            text: `Hantar jemputan kepada ${dipilih} orang?`,
            confirm: 'Ya, Hantar Semua',
        }, () => HTMLFormElement.prototype.submit.call(formHantar));
    });

    document.querySelectorAll('.btn-buang-jemputan').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const form = document.getElementById(btn.getAttribute('form'));
            if (typeof Swal === 'undefined') { if (confirm(`Buang ${btn.dataset.nama}?`)) form.submit(); return; }
            Swal.fire({
                icon: 'warning',
                title: 'Buang Jemputan?',
                html: `<strong>${esc(btn.dataset.nama)}</strong> akan dibuang daripada senarai jemputan.`,
                showCancelButton: true,
                confirmButtonText: 'Ya, Buang',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc2626',
            }).then(r => { if (r.isConfirmed) form.submit(); });
        });
    });
});
</script>

@include('surau._validasi-borang', ['formId' => 'formTambahJemputan'])

@endsection
