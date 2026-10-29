@extends('layouts.app')

@section('content')

<div class="container py-4">

    {{-- =========================================================
        HEADER
    ========================================================== --}}

    {{-- =========================================================
        HEADER KORPORAT
    ========================================================== --}}
    <div class="hero-welcome-card p-4 mb-4 shadow-sm">
        <div class="hero-shape-1"></div>
        <div class="hero-shape-2"></div>
        <div class="d-flex align-items-center justify-content-between position-relative flex-wrap gap-3">
            <div class="d-flex align-items-center">
                <div class="rounded-circle p-3 me-3 text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 52px; height: 52px; background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3);">
                    <i class="fa-solid fa-file-circle-check fa-2x"></i>
                </div>
                <div>
                    <div class="badge bg-white bg-opacity-25 text-white mb-1" style="font-size:0.7rem; letter-spacing:0.05em;">
                        <i class="fa-solid fa-kaaba me-1"></i> MODUL JAWI
                    </div>
                    <h4 class="fw-bold mb-0 text-white">
                        Pengesahan &amp; Semakan JAWI
                    </h4>
                    <small class="text-white-50">
                        Senarai keseluruhan permohonan pendaftaran surau Wilayah Persekutuan.
                    </small>
                </div>
            </div>
        </div>
    </div>


    {{-- =========================================================
        CARIAN & FILTER
    ========================================================== --}}

    <div class="card border-0 shadow-sm rounded-3 mb-4">

        <div class="card-body">

            <form method="GET"
                  action="{{ route('admin.jawi.pengesahan.index') }}">

                <div class="row g-2">

                    <div class="col-md-6">

                        <div class="input-group">

                            <span class="input-group-text bg-light">

                                <i class="fa-solid fa-magnifying-glass text-primary"></i>

                            </span>

                            <input
                                type="text"
                                name="search"
                                class="form-control"
                                placeholder="Cari no. rujukan, nama surau atau nama pemohon..."
                                value="{{ request('search') }}"
                            >

                        </div>

                    </div>


                    <div class="col-md-3">

                        <select name="status"
                                class="form-select">

                            <option value="">
                                Semua Status
                            </option>

                            <option value="PENDING"
                                {{ request('status') === 'PENDING' ? 'selected' : '' }}>
                                PENDING
                            </option>

                            <option value="DALAM_SEMAKAN"
                                {{ request('status') === 'DALAM_SEMAKAN' ? 'selected' : '' }}>
                                DALAM SEMAKAN
                            </option>

                            <option value="APPROVED"
                                {{ request('status') === 'APPROVED' ? 'selected' : '' }}>
                                DILULUSKAN
                            </option>

                            <option value="REJECTED"
                                {{ request('status') === 'REJECTED' ? 'selected' : '' }}>
                                DITOLAK
                            </option>

                        </select>

                    </div>


                    <div class="col-md-3 d-flex gap-2">

                        <button type="submit"
                                class="btn btn-primary flex-fill fw-semibold hover-lift"
                                style="background-color:#1e40af; border-color:#1e40af;">

                            <i class="fa-solid fa-filter me-1"></i>

                            Tapis

                        </button>


                        <a href="{{ route('admin.jawi.pengesahan.index') }}"
                           class="btn btn-outline-secondary">

                            <i class="fa-solid fa-rotate-left"></i>

                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- =========================================================
        SENARAI
    ========================================================== --}}

    <div class="card border-0 shadow-sm rounded-3">

        <div class="card-header bg-success text-white fw-bold py-3">

            <i class="fa-solid fa-list me-2"></i>

            Semua Permohonan

            <span class="badge bg-white text-success ms-2">

                {{ $applications->total() }}

            </span>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>

                            <th class="text-center" style="width:50px;">
                                Bil.
                            </th>

                            <th>
                                Permohonan
                            </th>

                            <th>
                                Kariah
                            </th>

                            <th class="text-center">
                                Status Permohonan
                            </th>

                            <th class="text-center">
                                Masjid
                            </th>

                            <th class="text-center">
                                JAWI
                            </th>

                            <th class="text-center">
                                Zon
                            </th>

                            <th class="text-center">
                                Tindakan
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($applications as $index => $application)

                            <tr>

                                {{-- BIL --}}
                                <td class="text-center font-monospace text-muted">

                                    {{ $applications->firstItem() + $index }}

                                </td>


                                {{-- PERMOHONAN --}}
                                <td>

                                    <div class="fw-bold text-dark">

                                        {{ $application->nama_surau ?? '-' }}

                                    </div>

                                    <div class="small text-muted font-monospace">

                                        {{ $application->reference_no }}

                                    </div>

                                    <div class="small text-muted">

                                        {{ $application->pemohon_nama ?? '-' }}

                                    </div>

                                </td>


                                {{-- KARIAH --}}
                                <td>

                                    <div class="text-dark">

                                        {{ $application->kariah_masjid
                                            ?? $application->masjid?->nama_masjid
                                            ?? '-' }}

                                    </div>

                                </td>


                                {{-- STATUS PERMOHONAN --}}
                                <td class="text-center">

                                    @switch($application->status)

                                        @case('APPROVED')

                                            <span class="badge bg-success">

                                                <i class="fa-solid fa-circle-check me-1"></i>
                                                DILULUSKAN

                                            </span>

                                            @break

                                        @case('REJECTED')

                                            <span class="badge bg-danger">

                                                <i class="fa-solid fa-circle-xmark me-1"></i>
                                                DITOLAK

                                            </span>

                                            @break

                                        @case('DALAM_SEMAKAN')

                                            <span class="badge bg-warning text-dark">

                                                <i class="fa-solid fa-clock me-1"></i>
                                                DALAM SEMAKAN

                                            </span>

                                            @break

                                        @default

                                            <span class="badge bg-secondary">

                                                {{ $application->status ?? 'PENDING' }}

                                            </span>

                                    @endswitch

                                </td>


                                {{-- MASJID --}}
                                <td class="text-center">

                                    @php
                                        $masjidStatus =
                                            $application->masjidConfirmation?->status
                                            ?? null;
                                    @endphp

                                    @if($masjidStatus === 'APPROVED' || $masjidStatus === 'DISAHKAN')

                                        <span class="badge bg-success">

                                            <i class="fa-solid fa-check me-1"></i>
                                            DISAHKAN

                                        </span>

                                    @elseif($masjidStatus === 'REJECTED' || $masjidStatus === 'DITOLAK')

                                        <span class="badge bg-danger">

                                            <i class="fa-solid fa-xmark me-1"></i>
                                            DITOLAK

                                        </span>

                                    @else

                                        <span class="badge bg-warning text-dark">

                                            <i class="fa-solid fa-clock me-1"></i>
                                            BELUM

                                        </span>

                                    @endif

                                </td>


                                {{-- JAWI --}}
                                <td class="text-center">

                                    @php
                                        $jawiStatus =
                                            $application->jawiConfirmation?->status
                                            ?? null;
                                    @endphp

                                    @if($jawiStatus === 'APPROVED' || $jawiStatus === 'DISAHKAN')

                                        <span class="badge bg-success">

                                            <i class="fa-solid fa-check me-1"></i>
                                            DISAHKAN

                                        </span>

                                    @elseif($jawiStatus === 'REJECTED' || $jawiStatus === 'DITOLAK')

                                        <span class="badge bg-danger">

                                            <i class="fa-solid fa-xmark me-1"></i>
                                            DITOLAK

                                        </span>

                                    @else

                                        <span class="badge bg-secondary">

                                            BELUM

                                        </span>

                                    @endif

                                </td>


                                {{-- ZON --}}
                                <td class="text-center">

                                    @php
                                        $zonStatus =
                                            $application->zonConfirmation?->status
                                            ?? null;
                                    @endphp

                                    @if($zonStatus === 'APPROVED' || $zonStatus === 'DISAHKAN')

                                        <span class="badge bg-success">

                                            <i class="fa-solid fa-check me-1"></i>
                                            DISAHKAN

                                        </span>

                                    @elseif($zonStatus === 'REJECTED' || $zonStatus === 'DITOLAK')

                                        <span class="badge bg-danger">

                                            <i class="fa-solid fa-xmark me-1"></i>
                                            DITOLAK

                                        </span>

                                    @else

                                        <span class="badge bg-secondary">

                                            BELUM

                                        </span>

                                    @endif

                                </td>


                                {{-- TINDAKAN --}}
                                <td class="text-center">

                                    <a href="{{ route(
                                        'admin.jawi.pengesahan.show',
                                        $application->id
                                    ) }}"
                                    class="btn btn-sm btn-outline-success">

                                        <i class="fa-solid fa-eye me-1"></i>

                                        Semak

                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="8"
                                    class="text-center py-5 text-muted">

                                    <i class="fa-solid fa-folder-open fa-2x mb-2"></i>

                                    <div>
                                        Tiada permohonan ditemui.
                                    </div>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        @if($applications->hasPages())

            <div class="card-footer bg-white border-0">

                {{ $applications->links() }}

            </div>

        @endif

    </div>

</div>

@endsection