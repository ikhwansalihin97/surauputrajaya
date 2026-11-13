{{--
    Borang senarai AJK (jawatan utama + AJK pilihan) bagi sesi AGM semasa.
    Dikongsi oleh halaman Maklumat Jawatankuasa dan langkah Keputusan Mesyuarat (Bil. 1).
    Parameter: $surau, $kembali (pilihan: URL dalaman selepas simpan), $kembaliUrl (pilihan: butang Kembali)
--}}
@php
    $wajib   = \App\Models\SurauCommittee::JAWATAN_WAJIB;
    $maksAjk = \App\Models\SurauCommittee::MAKS_AJK;
    $kosong  = ['nama' => '', 'ic_no' => '', 'alamat' => '', 'no_tel' => '', 'email' => ''];
    $medan   = array_keys($kosong);

    if (old('utama')) {
        // Selepas ralat pengesahan: isi semula daripada input lama
        $utama = collect($wajib)->mapWithKeys(fn ($j, $i) => [$j => array_merge($kosong, old("utama.$i", []))])->all();
        $ajk   = array_values(old('ajk', []));
    } else {
        // Rekod semasa: seorang bagi setiap jawatan utama, selebihnya sebagai AJK
        // Termasuk Pentadbir Surau jika belum tersenarai sebagai AJK
        $baki  = $surau->committeesForForm();
        $utama = [];
        foreach ($wajib as $jawatan) {
            $idx = $baki->search(fn ($c) => $c['jawatan'] === $jawatan);
            $utama[$jawatan] = $idx !== false ? $baki->pull($idx) : $kosong;
        }
        $ajk = $baki->values()->take($maksAjk)->all();
    }

    $ikon = [
        'Pengerusi'          => 'fa-user-tie',
        'Timbalan Pengerusi' => 'fa-user-group',
        'Setiausaha'         => 'fa-file-signature',
        'Bendahari'          => 'fa-coins',
    ];
@endphp

    <form action="{{ route('surau.jawatankuasa.update') }}" method="POST" id="formAjk" novalidate>
        @csrf
        @method('PUT')
        @if(!empty($kembali))
            <input type="hidden" name="kembali" value="{{ $kembali }}">
        @endif

        {{-- ── A. JAWATAN UTAMA (WAJIB) ── --}}
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3" style="border-bottom:2px solid #eff6ff;">
                <div class="fw-bold"><i class="fa-solid fa-user-shield me-2 text-primary"></i>A. Jawatan Utama</div>
                <div class="small text-muted mt-1">Wajib diisi: Pengerusi, Timbalan Pengerusi, Setiausaha dan Bendahari.</div>
            </div>
            <div class="card-body p-3 p-md-4">
                @foreach($wajib as $i => $jawatan)
                    @php $row = $utama[$jawatan]; @endphp
                    <div class="border rounded-3 p-3 mb-3 ajk-row" style="border-left:4px solid #1e40af !important;">
                        <div class="mb-2">
                            <span class="badge bg-primary ajk-bil"><i class="fa-solid {{ $ikon[$jawatan] }} me-1"></i>{{ $jawatan }}</span>
                        </div>
                        @include('surau._ajk-medan', ['prefix' => "utama[$i]", 'row' => $row])
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ── B. AHLI JAWATANKUASA (PILIHAN, MAKS 8) ── --}}
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-start gap-2" style="border-bottom:2px solid #eff6ff;">
                <div>
                    <div class="fw-bold"><i class="fa-solid fa-users me-2 text-primary"></i>B. Ahli Jawatankuasa (AJK)</div>
                    <div class="small text-muted mt-1">Pilihan. Maksimum {{ $maksAjk }} orang &mdash; <span id="kiraanAjk">{{ count($ajk) }}</span>/{{ $maksAjk }} diisi.</div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary fw-bold flex-shrink-0" id="btnTambahAjk">
                    <i class="fa-solid fa-user-plus me-1"></i> Tambah AJK
                </button>
            </div>
            <div class="card-body p-3 p-md-4">
                <div id="senaraiAjk">
                    @foreach($ajk as $i => $row)
                        <div class="border rounded-3 p-3 mb-3 ajk-row ajk-biasa bg-light-subtle">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-secondary-subtle text-secondary-emphasis ajk-bil">AJK {{ $i + 1 }}</span>
                                <button type="button" class="btn btn-sm btn-outline-danger btn-buang-ajk" title="Buang AJK">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                            @include('surau._ajk-medan', ['prefix' => "ajk[$i]", 'row' => array_merge($kosong, $row)])
                        </div>
                    @endforeach
                </div>
                <div id="tiadaAjk" class="text-center text-muted small py-3 {{ count($ajk) ? 'd-none' : '' }}">
                    <i class="fa-solid fa-users-slash me-1"></i> Tiada AJK tambahan. Klik <strong>Tambah AJK</strong> untuk menambah.
                </div>
            </div>
        </div>

        <div class="d-flex flex-column flex-sm-row justify-content-end gap-2 mb-4">
            @if(!empty($kembaliUrl))
                <a href="{{ $kembaliUrl }}" class="btn btn-light border fw-semibold px-4">
                    <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                </a>
            @endif
            <button type="submit" class="btn btn-primary fw-bold px-4">
                <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Senarai AJK
            </button>
        </div>
    </form>

{{-- Templat baris AJK baharu --}}
<template id="templatAjk">
    <div class="border rounded-3 p-3 mb-3 ajk-row ajk-biasa bg-light-subtle">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="badge bg-secondary-subtle text-secondary-emphasis ajk-bil">AJK</span>
            <button type="button" class="btn btn-sm btn-outline-danger btn-buang-ajk" title="Buang AJK">
                <i class="fa-solid fa-trash"></i>
            </button>
        </div>
        @include('surau._ajk-medan', ['prefix' => 'ajk[__i__]', 'row' => $kosong])
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const MAKS = {{ $maksAjk }};
    const form = document.getElementById('formAjk');
    const senarai = document.getElementById('senaraiAjk');
    const btnTambah = document.getElementById('btnTambahAjk');
    const kiraan = document.getElementById('kiraanAjk');
    const tiada = document.getElementById('tiadaAjk');
    const templat = document.getElementById('templatAjk');

    // Nombor semula baris AJK dan nama medan (ajk[0][nama], ajk[1][nama], ...)
    const susunSemula = () => {
        const rows = senarai.querySelectorAll('.ajk-biasa');
        rows.forEach((row, i) => {
            row.querySelector('.ajk-bil').textContent = 'AJK ' + (i + 1);
            row.querySelectorAll('[name]').forEach(el => {
                el.name = el.name.replace(/^ajk\[[^\]]*\]/, `ajk[${i}]`);
            });
        });
        kiraan.textContent = rows.length;
        tiada.classList.toggle('d-none', rows.length > 0);
        btnTambah.disabled = rows.length >= MAKS;
        btnTambah.title = rows.length >= MAKS ? `Maksimum ${MAKS} AJK` : '';
    };

    // Bersihkan nilai sedia ada (cth. No. K/P lama dengan tanda '-')
    const digitsOnly = (el) => { el.value = el.value.replace(/\D+/g, ''); };
    form.querySelectorAll('[data-digits-only]').forEach(digitsOnly);
    form.addEventListener('input', (e) => {
        if (e.target.matches('[data-digits-only]')) digitsOnly(e.target);
    });

    btnTambah.addEventListener('click', () => {
        if (senarai.querySelectorAll('.ajk-biasa').length >= MAKS) return;
        const row = templat.content.firstElementChild.cloneNode(true);
        senarai.appendChild(row);
        susunSemula();
        row.querySelector('input').focus();
    });

    senarai.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-buang-ajk');
        if (!btn) return;
        btn.closest('.ajk-biasa').remove();
        susunSemula();
    });

    // Tanda medan yang ditolak oleh pelayan (cth. utama.0.nama -> utama[0][nama])
    @json($errors->keys()).forEach(key => {
        const parts = key.split('.');
        const name = parts[0] + parts.slice(1).map(p => `[${p}]`).join('');
        form.querySelectorAll(`[name="${name}"]`).forEach(el => el.classList.add('is-invalid'));
    });

    susunSemula();
});
</script>

@include('surau._validasi-borang', ['formId' => 'formAjk'])
