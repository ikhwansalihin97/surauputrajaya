@extends('layouts.app')

@section('content')

<div class="container-fluid py-4">

    {{-- =========================================================
        HEADER
    ========================================================== --}}

    <div class="hero-welcome-card p-4 mb-4 shadow-sm">
        <div class="hero-shape-1"></div>
        <div class="hero-shape-2"></div>
        <div class="d-flex align-items-center justify-content-between position-relative flex-wrap gap-3">
            <div class="d-flex align-items-center">
                <div class="rounded-circle p-3 me-3 text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 52px; height: 52px; background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3);">
                    <i class="fa-solid fa-mosque fa-2x"></i>
                </div>
                <div>
                    <div class="badge bg-white bg-opacity-25 text-white mb-1" style="font-size:0.7rem; letter-spacing:0.05em;">
                        <i class="fa-solid fa-file-signature me-1"></i> PENGESAHAN KARIAH
                    </div>
                    <h4 class="fw-bold mb-0 text-white">
                        Pengesahan Permohonan Surau
                    </h4>
                    <small class="text-white-50">
                        Senarai permohonan pendaftaran surau daripada <strong>{{ $masjids->count() }} kariah masjid</strong> di bawah seliaan anda.
                    </small>
                </div>
            </div>

            <div class="text-end d-none d-md-block">
                <span class="badge bg-white text-primary px-3 py-2 font-monospace fw-bold shadow-sm" style="font-size: 0.8rem;">
                    <i class="fa-solid fa-mosque me-1"></i> {{ $masjids->count() }} Kariah Aktif
                </span>
            </div>
        </div>
    </div>


    {{-- =========================================================
        MASJID DI BAWAH JAGAAN
    ========================================================== --}}

    <div class="card border-0 shadow-sm rounded-3 mb-4">

        <div class="card-body p-3">

            <div class="d-flex align-items-center flex-wrap gap-2">

                <span class="small text-muted fw-bold me-2">

                    <i class="fa-solid fa-mosque me-1" style="color: #0891b2;"></i>

                    KARIAH:

                </span>

                @foreach($masjids as $masjid)

                    <span class="badge fw-semibold" style="background-color: #ecfeff; color: #0891b2; border: 1px solid #a5f3fc;">

                        <i class="fa-solid fa-mosque me-1" style="color: #0891b2;"></i>

                        {{ $masjid->nama_masjid }}

                    </span>

                @endforeach

            </div>

        </div>

    </div>


    {{-- =========================================================
        FILTER / CARIAN
    ========================================================== --}}

    <div class="card border-0 shadow-sm rounded-3 mb-4">

        <div class="card-body p-3 bg-light rounded-3">

            <form
                action="{{ route('admin.masjid.pengesahan.index') }}"
                method="GET"
                class="row g-2 align-items-end"
            >

                {{-- CARIAN --}}
                <div class="col-md-4">

                    <label class="form-label small fw-bold text-muted mb-1">
                        Carian
                    </label>

                    <div class="input-group input-group-sm">

                        <span class="input-group-text bg-white">
                            <i class="fa-solid fa-magnifying-glass text-muted"></i>
                        </span>

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="No. rujukan / nama surau / masjid..."
                            value="{{ request('search') }}"
                        >

                    </div>

                </div>


                {{-- STATUS --}}
                <div class="col-md-3">

                    <label class="form-label small fw-bold text-muted mb-1">
                        Status Pengesahan Masjid
                    </label>

                    <select
                        name="status"
                        class="form-select form-select-sm"
                    >

                        <option value="">
                            -- Semua Status --
                        </option>

                        <option
                            value="BELUM_DISAHKAN"
                            {{ request('status') === 'BELUM_DISAHKAN' ? 'selected' : '' }}
                        >
                            Belum Disahkan
                        </option>

                        <option
                            value="DISAHKAN"
                            {{ request('status') === 'DISAHKAN' ? 'selected' : '' }}
                        >
                            Telah Disahkan
                        </option>

                        <option
                            value="DITOLAK"
                            {{ request('status') === 'DITOLAK' ? 'selected' : '' }}
                        >
                            Ditolak
                        </option>

                    </select>

                </div>


                {{-- PER PAGE --}}
                <div class="col-md-2">

                    <label class="form-label small fw-bold text-muted mb-1">
                        Paparan
                    </label>

                    <select
                        name="per_page"
                        class="form-select form-select-sm font-monospace"
                    >

                        <option value="10"
                            {{ request('per_page', 10) == 10 ? 'selected' : '' }}>
                            10 Rekod
                        </option>

                        <option value="25"
                            {{ request('per_page') == 25 ? 'selected' : '' }}>
                            25 Rekod
                        </option>

                        <option value="50"
                            {{ request('per_page') == 50 ? 'selected' : '' }}>
                            50 Rekod
                        </option>

                        <option value="100"
                            {{ request('per_page') == 100 ? 'selected' : '' }}>
                            100 Rekod
                        </option>

                    </select>

                </div>


                {{-- BUTANG --}}
                <div class="col-md-3">

                    <div class="d-flex gap-2">

                        <button
                            type="submit"
                            class="btn btn-sm btn-primary flex-fill fw-semibold hover-lift"
                            style="background-color:#1e40af; border-color:#1e40af;"
                        >

                            <i class="fa-solid fa-filter me-1"></i>

                            Tapis

                        </button>


                        <a
                            href="{{ route('admin.masjid.pengesahan.index') }}"
                            class="btn btn-sm btn-outline-secondary flex-fill fw-medium"
                        >

                            <i class="fa-solid fa-rotate-left me-1"></i>

                            Reset

                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- =========================================================
        SENARAI PERMOHONAN
    ========================================================== --}}

    <div class="card border-0 shadow-sm rounded-3">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table
                    class="table table-hover align-middle mb-0"
                    style="font-size: 0.875rem;"
                >

                    <thead class="bg-light text-secondary small text-uppercase">

                        <tr>

                            <th
                                class="ps-3 text-center"
                                style="width: 55px;"
                            >
                                Bil.
                            </th>

                            <th>
                                No. Rujukan
                            </th>

                            <th>
                                Nama Surau
                            </th>

                            <th>
                                Kariah Masjid
                            </th>

                            <th>
                                Kategori
                            </th>

                            <th>
                                Tarikh Permohonan
                            </th>

                            <th>
                                Status
                            </th>

                            <th
                                class="text-center"
                                style="width: 110px;"
                            >
                                Tindakan
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($applications as $index => $application)

                            @php

                                $masjidConfirmation = $application->confirmations
                                    ->where('peringkat', 'MASJID')
                                    ->sortByDesc('created_at')
                                    ->first();

                                $zonConfirmation = $application->confirmations
                                    ->where('peringkat', 'ZON')
                                    ->sortByDesc('created_at')
                                    ->first();

                                $jawiConfirmation = $application->confirmations
                                    ->where('peringkat', 'JAWI')
                                    ->sortByDesc('created_at')
                                    ->first();


                                /*
                                |--------------------------------------------------------------------------
                                | STATUS SEMASA
                                |--------------------------------------------------------------------------
                                */

                                $statusSemasa = 'BELUM_DISAHKAN';

                                $peringkatSemasa = 'PENTADBIR MASJID';


                                /*
                                |--------------------------------------------------------------------------
                                | MASJID
                                |--------------------------------------------------------------------------
                                */

                                if ($masjidConfirmation) {

                                    if ($masjidConfirmation->status === 'REJECTED') {

                                        $statusSemasa = 'REJECTED';
                                        $peringkatSemasa = 'PENTADBIR MASJID';

                                    } elseif ($masjidConfirmation->status === 'APPROVED') {

                                        $statusSemasa = 'PENDING';
                                        $peringkatSemasa = 'PENTADBIR ZON';

                                    }

                                }


                                /*
                                |--------------------------------------------------------------------------
                                | ZON
                                |--------------------------------------------------------------------------
                                */

                                if (
                                    $masjidConfirmation?->status === 'APPROVED'
                                    && $zonConfirmation
                                ) {

                                    if ($zonConfirmation->status === 'REJECTED') {

                                        $statusSemasa = 'REJECTED';
                                        $peringkatSemasa = 'PENTADBIR ZON';

                                    } elseif ($zonConfirmation->status === 'APPROVED') {

                                        $statusSemasa = 'PENDING';
                                        $peringkatSemasa = 'PENTADBIR JAWI';

                                    }

                                }


                                /*
                                |--------------------------------------------------------------------------
                                | JAWI
                                |--------------------------------------------------------------------------
                                */

                                if (
                                    $zonConfirmation?->status === 'APPROVED'
                                    && $jawiConfirmation
                                ) {

                                    if ($jawiConfirmation->status === 'REJECTED') {

                                        $statusSemasa = 'REJECTED';
                                        $peringkatSemasa = 'PENTADBIR JAWI';

                                    } elseif ($jawiConfirmation->status === 'APPROVED') {

                                        $statusSemasa = 'APPROVED';
                                        $peringkatSemasa = 'PENTADBIR JAWI';

                                    }

                                }

                            @endphp


                            <tr>

                                {{-- BIL --}}
                                <td class="ps-3 text-center text-muted small fw-bold">

                                    {{ $applications->firstItem() + $index }}

                                </td>


                                {{-- RUJUKAN --}}
                                <td>

                                    <span class="font-monospace fw-bold text-primary">

                                        {{ $application->reference_no }}

                                    </span>
                                    @include('admin.permohonan._jenis_badge', ['application' => $application])

                                </td>


                                {{-- NAMA SURAU --}}
                                <td>

                                    <div class="fw-semibold text-dark">

                                        {{ $application->nama_surau }}

                                    </div>

                                </td>


                                {{-- KARIAH MASJID --}}
                                <td>

                                    <div class="fw-semibold">

                                        <i class="fa-solid fa-mosque me-1" style="color: #0891b2;"></i>

                                        {{ $application->masjid?->nama_masjid ?? '-' }}

                                    </div>

                                </td>


                                {{-- KATEGORI --}}
                                <td>

                                    {{ $application->kategori_surau }}

                                </td>


                                {{-- TARIKH --}}
                                <td class="font-monospace text-muted small">

                                    {{ $application->created_at->format('d/m/Y H:i') }}

                                </td>


                                {{-- STATUS --}}
                                <td>

                                    @if($statusSemasa === 'BELUM_DISAHKAN')

                                        <span class="badge bg-warning text-dark">

                                            <i class="fa-solid fa-clock me-1"></i>

                                            MENUNGGU

                                        </span>

                                        <div class="small text-warning-emphasis mt-1">

                                            Pentadbir Masjid

                                        </div>


                                    @elseif($statusSemasa === 'REJECTED')

                                        <span class="badge bg-danger">

                                            <i class="fa-solid fa-circle-xmark me-1"></i>

                                            DITOLAK

                                        </span>

                                        <div class="small text-danger mt-1">

                                            {{ $peringkatSemasa }}

                                        </div>


                                    @elseif($statusSemasa === 'APPROVED')

                                        <span class="badge bg-success">

                                            <i class="fa-solid fa-circle-check me-1"></i>

                                            DISAHKAN

                                        </span>

                                        <div class="small text-success mt-1">

                                            Pentadbir JAWI

                                        </div>


                                    @elseif($statusSemasa === 'PENDING')

                                        <span class="badge bg-info text-dark">

                                            <i class="fa-solid fa-clock me-1"></i>

                                            DALAM SEMAKAN

                                        </span>

                                        <div class="small text-info mt-1">

                                            {{ $peringkatSemasa }}

                                        </div>

                                    @endif

                                </td>


                                {{-- TINDAKAN --}}
                                <td class="text-center">

                                    <a
                                        href="{{ route('admin.masjid.pengesahan.show', $application->id) }}"
                                        class="btn btn-sm btn-outline-primary"
                                        title="Semak Permohonan"
                                    >

                                        <i class="fa-solid fa-eye me-1"></i>

                                        Semak

                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="8"
                                    class="text-center py-5 text-muted"
                                >

                                    <i class="fa-solid fa-inbox fa-2x mb-2 d-block text-secondary"></i>

                                    Tiada permohonan ditemui mengikut penapis semasa.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- =========================================================
            PAGINATION
        ========================================================== --}}

        @if($applications->total() > 0)

            <div class="card-footer bg-white py-3">

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">

                    <div class="small text-muted font-monospace">

                        Menunjukkan

                        <strong>
                            {{ $applications->firstItem() ?? 0 }}
                        </strong>

                        hingga

                        <strong>
                            {{ $applications->lastItem() ?? 0 }}
                        </strong>

                        daripada

                        <strong>
                            {{ $applications->total() }}
                        </strong>

                        rekod permohonan

                    </div>


                    <div>

                        {{ $applications->links('pagination::bootstrap-5') }}

                    </div>

                </div>

            </div>

        @endif

    </div>

</div>

@endsection