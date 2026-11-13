@extends('layouts.app')

@section('content')

@php
    $edit      = $mesyuarat->exists;
    $tahunMula = (int) old('tahun_mula', $tahunMula);
    $bilangan  = (int) old('bilangan', $mesyuarat->bilangan ?: 1);
    $tahunIni  = now()->year;
    $pilihan   = range($tahunIni + 1, $tahunIni - 10);
    // Sertakan sesi sedia ada di luar julat lalai
    $pilihan = collect($pilihan)->merge(array_keys($sesiSedia))->push($tahunMula)->unique()->sortDesc()->values()->all();

    // Status setiap pilihan sesi: sedia ada / bertindih / penuh (Bil. 1 & 2 telah direkodkan)
    $statusSesi = [];
    foreach ($pilihan as $t) {
        $bertindih = collect(array_keys($sesiSedia))->first(fn ($x) => $x !== $t && abs($x - $t) <= 1);
        $statusSesi[$t] = [
            'sedia'     => array_key_exists($t, $sesiSedia),
            'direkod'   => $sesiSedia[$t] ?? [],
            'bertindih' => $bertindih,
        ];
    }
@endphp

<div class="container py-4" style="max-width: 860px;">

    @include('surau._pemilih')

    <div class="hero-welcome-card p-4 mb-4 shadow-sm">
        <div class="hero-shape-1"></div>
        <div class="hero-shape-2"></div>
        <div class="d-flex align-items-center position-relative">
            <div class="rounded-circle p-3 me-3 text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 52px; height: 52px; background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3);">
                <i class="fa-solid fa-calendar-plus fa-lg"></i>
            </div>
            <div>
                <div class="badge bg-white bg-opacity-25 text-white mb-1" style="font-size:0.7rem; letter-spacing:0.05em;">
                    <i class="fa-solid fa-people-roof me-1"></i> MESYUARAT AGUNG TAHUNAN (AGM)
                </div>
                <h4 class="fw-bold mb-0 text-white">
                    {{ $edit ? "Kemaskini Mesyuarat Bil. {$mesyuarat->bilangan} ({$mesyuarat->tahun})" : 'Rekod Mesyuarat AGM' }}
                </h4>
                <small class="text-white-50">{{ $surau->nama_surau }}</small>
            </div>
        </div>
    </div>

    @if($edit)
        @include('surau.agm._langkah', ['aktif' => 1])
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

    <form action="{{ $edit ? route('surau.agm.update', $mesyuarat) : route('surau.agm.store') }}" method="POST" id="formAgm" novalidate>
        @csrf
        @if($edit) @method('PUT') @endif

        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3 fw-bold" style="border-bottom:2px solid #eff6ff;">
                <i class="fa-solid fa-calendar-days me-2 text-primary"></i>Sesi &amp; Bilangan Mesyuarat
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Sesi <span class="text-danger">*</span></label>
                        @if($edit)
                            <input type="text" class="form-control" value="{{ $mesyuarat->sesi->label }}" disabled>
                            <input type="hidden" id="tahunMula" value="{{ $tahunMula }}">
                        @else
                            <select name="tahun_mula" id="tahunMula" class="form-select @error('tahun_mula') is-invalid @enderror" required>
                                @foreach($pilihan as $t)
                                    @php
                                        $st = $statusSesi[$t];
                                        $penuh = count($st['direkod']) >= 2;
                                        $tutup = $st['bertindih'] || $penuh;
                                        $nota = $st['bertindih'] ? ' — bertindih dengan sesi ' . $st['bertindih'] . '/' . ($st['bertindih'] + 1)
                                              : ($penuh ? ' — Bil. 1 & Bil. 2 telah direkodkan'
                                              : ($st['sedia'] ? ' — sesi sedia ada' : ''));
                                    @endphp
                                    <option value="{{ $t }}" data-direkod="{{ implode(',', $st['direkod']) }}"
                                        {{ $tahunMula === $t ? 'selected' : '' }} {{ $tutup ? 'disabled' : '' }}>{{ $t }}/{{ $t + 1 }}{{ $nota }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">Sesi dua tahun. Cth: 2026/2027. Sesi yang bertindih dengan sesi sedia ada tidak boleh dipilih.</div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Bilangan Mesyuarat <span class="text-danger">*</span></label>
                        @if($edit)
                            <input type="text" class="form-control" value="Bil. {{ $mesyuarat->bilangan }} ({{ $mesyuarat->tahun }})" disabled>
                            <input type="hidden" id="bilanganTetap" value="{{ $bilangan }}">
                        @else
                            <div class="btn-group w-100" role="group">
                                @foreach([1, 2] as $bil)
                                    <input type="radio" class="btn-check" name="bilangan" id="bil{{ $bil }}" value="{{ $bil }}" {{ $bilangan === $bil ? 'checked' : '' }} required>
                                    <label class="btn btn-outline-primary fw-semibold" for="bil{{ $bil }}">
                                        Bil. {{ $bil }} (<span class="tahun-bil" data-bil="{{ $bil }}">{{ $tahunMula + $bil - 1 }}</span>)<span class="bil-direkod d-none" data-bil="{{ $bil }}"> &check; direkod</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('bilangan')<div class="small text-danger mt-1">{{ $message }}</div>@enderror
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3 fw-bold" style="border-bottom:2px solid #eff6ff;">
                <i class="fa-solid fa-location-dot me-2 text-primary"></i>Butiran Mesyuarat
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Tarikh <span class="text-danger">*</span></label>
                        <input type="date" name="tarikh" id="tarikh" class="form-control @error('tarikh') is-invalid @enderror"
                               value="{{ old('tarikh', $mesyuarat->tarikh?->format('Y-m-d')) }}" required>
                        <div class="form-text">Mesti dalam tahun <span id="tahunTarikh">{{ $tahunMula + $bilangan - 1 }}</span>.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Masa <span class="text-danger">*</span></label>
                        <input type="time" name="masa" class="form-control @error('masa') is-invalid @enderror"
                               value="{{ old('masa', $mesyuarat->masa ? substr($mesyuarat->masa, 0, 5) : '') }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Lokasi <span class="text-danger">*</span></label>
                        <input type="text" name="lokasi" maxlength="255" class="form-control @error('lokasi') is-invalid @enderror"
                               value="{{ old('lokasi', $mesyuarat->lokasi) }}" placeholder="Cth: Dewan Surau" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex flex-column flex-sm-row justify-content-end gap-2 mb-4">
            <a href="{{ route('surau.agm.index') }}" class="btn btn-light border fw-semibold px-4">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali
            </a>
            <button type="submit" class="btn btn-primary fw-bold px-4">
                <i class="fa-solid fa-floppy-disk me-1"></i> {{ $edit ? 'Simpan Perubahan' : 'Rekod Mesyuarat' }}
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tahunMula = document.getElementById('tahunMula');
    const tarikh = document.getElementById('tarikh');
    const tahunTarikh = document.getElementById('tahunTarikh');

    // Tahun mesyuarat: Bil. 1 = tahun mula, Bil. 2 = tahun tamat. Hadkan pilihan tarikh kepada tahun itu.
    // Bilangan yang telah direkodkan bagi sesi terpilih tidak boleh dipilih semula
    const kemaskiniBilangan = (pilihSemula = false) => {
        const opt = tahunMula.options ? tahunMula.options[tahunMula.selectedIndex] : null;
        if (!opt) return;
        const direkod = (opt.dataset.direkod || '').split(',').filter(Boolean);
        const radios = [...document.querySelectorAll('input[name="bilangan"]')];
        radios.forEach(r => {
            const ada = direkod.includes(r.value);
            r.disabled = ada;
            if (ada) r.checked = false;
            document.querySelector(`.bil-direkod[data-bil="${r.value}"]`)?.classList.toggle('d-none', !ada);
        });
        // Tukar sesi: pilih bilangan pertama yang belum direkodkan (sesi baharu bermula dengan Bil. 1)
        if (pilihSemula || !radios.some(r => r.checked)) {
            const pertama = radios.find(r => !r.disabled);
            if (pertama) pertama.checked = true;
        }
    };

    const kemaskiniTahun = (pilihSemula = false) => {
        kemaskiniBilangan(pilihSemula);
        const mula = parseInt(tahunMula.value, 10);
        const bilEl = document.querySelector('input[name="bilangan"]:checked') || document.getElementById('bilanganTetap');
        const bil = parseInt(bilEl?.value || '1', 10);
        const tahun = mula + bil - 1;
        document.querySelectorAll('.tahun-bil').forEach(el => el.textContent = mula + parseInt(el.dataset.bil, 10) - 1);
        tahunTarikh.textContent = tahun;
        tarikh.min = `${tahun}-01-01`;
        tarikh.max = `${tahun}-12-31`;
    };

    tahunMula.addEventListener('change', () => kemaskiniTahun(true));
    document.querySelectorAll('input[name="bilangan"]').forEach(r => r.addEventListener('change', () => kemaskiniTahun()));
    kemaskiniTahun();
});
</script>

@include('surau._validasi-borang', ['formId' => 'formAgm'])

@endsection
