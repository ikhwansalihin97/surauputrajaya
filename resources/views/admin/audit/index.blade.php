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
        <div class="d-flex align-items-center position-relative">
            <div class="rounded-circle p-3 me-3 text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 52px; height: 52px; background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3);">
                <i class="fa-solid fa-clock-rotate-left fa-2x"></i>
            </div>
            <div>
                <div class="badge bg-white bg-opacity-25 text-white mb-1" style="font-size:0.7rem; letter-spacing:0.05em;">
                    <i class="fa-solid fa-shield-halved me-1"></i> LOG KESELAMATAN &amp; AUDIT TRAIL
                </div>
                <h4 class="fw-bold mb-0 text-white">
                    Pengurusan Jejak Audit Sistem
                </h4>
                <small class="text-white-50">
                    Rekod audit keselamatan transaksi dan aktiviti perubahan data pengguna sistem E-Surau JAWI.
                </small>
            </div>
        </div>
    </div>


    {{-- =========================================================
        FILTER
    ========================================================== --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">

        <div class="card-body p-3 bg-light rounded-3">

            <form
                action="{{ route('admin.audit-logs.index') }}"
                method="GET"
                class="row g-2 align-items-end"
            >

                {{-- CARIAAN --}}
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
                            placeholder="Cari pengguna / keterangan..."
                            value="{{ request('search') }}"
                        >

                    </div>

                </div>


                {{-- MODUL --}}
                <div class="col-md-3">

                    <label class="form-label small fw-bold text-muted mb-1">
                        Modul / Kategori
                    </label>

                    <select
                        name="log_name"
                        class="form-select form-select-sm"
                    >

                        <option value="">
                            -- Semua Modul --
                        </option>

                        <option
                            value="kawalan_keselamatan"
                            @selected(request('log_name') === 'kawalan_keselamatan')
                        >
                            Kawalan Keselamatan
                        </option>

                        <option
                            value="tukar_peranan"
                            @selected(request('log_name') === 'tukar_peranan')
                        >
                            Tukar Peranan
                        </option>

                        <option
                            value="permohonan"
                            @selected(request('log_name') === 'permohonan')
                        >
                            Permohonan Surau
                        </option>

                        <option
                            value="pengurusan_pengguna"
                            @selected(request('log_name') === 'pengurusan_pengguna')
                        >
                            Pengurusan Pengguna
                        </option>

                        <option
                            value="ajk_surau"
                            @selected(request('log_name') === 'ajk_surau')
                        >
                            AJK Surau
                        </option>

                        <option
                            value="dokumen_surau"
                            @selected(request('log_name') === 'dokumen_surau')
                        >
                            Dokumen Sokongan
                        </option>

                        <option
                            value="minit_mesyuarat"
                            @selected(request('log_name') === 'minit_mesyuarat')
                        >
                            Minit Mesyuarat
                        </option>

                        <option
                            value="peranan_sistem"
                            @selected(request('log_name') === 'peranan_sistem')
                        >
                            Peranan Sistem
                        </option>

                    </select>

                </div>


                {{-- EVENT --}}
                <div class="col-md-2">

                    <label class="form-label small fw-bold text-muted mb-1">
                        Acara
                    </label>

                    <select
                        name="event"
                        class="form-select form-select-sm"
                    >

                        <option value="">
                            -- Semua --
                        </option>

                        <option
                            value="created"
                            @selected(request('event') === 'created')
                        >
                            Created
                        </option>

                        <option
                            value="updated"
                            @selected(request('event') === 'updated')
                        >
                            Updated
                        </option>

                        <option
                            value="deleted"
                            @selected(request('event') === 'deleted')
                        >
                            Deleted
                        </option>

                    </select>

                </div>


                {{-- PER PAGE --}}
                <div class="col-md-1">

                    <label class="form-label small fw-bold text-muted mb-1">
                        Paparan
                    </label>

                    <select
                        name="per_page"
                        class="form-select form-select-sm"
                    >

                        @foreach([10,25,50,100] as $size)

                            <option
                                value="{{ $size }}"
                                @selected((int) request('per_page', 10) === $size)
                            >
                                {{ $size }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- BUTTON --}}
                <div class="col-md-2">

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
                            href="{{ route('admin.audit-logs.index') }}"
                            class="btn btn-sm btn-outline-secondary"
                        >
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- =========================================================
        TABLE
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
                                style="width: 60px;"
                            >
                                Bil.
                            </th>

                            <th style="width: 150px;">
                                Tarikh & Masa
                            </th>

                            <th style="width: 230px;">
                                Pelaku
                            </th>

                            <th style="width: 170px;">
                                Modul
                            </th>

                            <th style="width: 110px;">
                                Acara
                            </th>

                            <th>
                                Butiran Aktiviti
                            </th>

                            <th
                                class="text-center"
                                style="width: 120px;"
                            >
                                Perubahan
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    @forelse($logs as $index => $log)

                        @php

                            /*
                             * =================================================
                             * CAUSER
                             * =================================================
                             *
                             * causer = pengguna yang melakukan tindakan
                             */

                            $causer = $log->causer;


                            /*
                             * =================================================
                             * SUBJECT
                             * =================================================
                             *
                             * subject = model / rekod yang terlibat
                             */

                            $subject = $log->subject;


                            /*
                             * =================================================
                             * EVENT
                             * =================================================
                             */

                            $event = strtolower(
                                $log->event ?? 'action'
                            );


                            $badgeClass = match($event) {

                                'created'
                                    => 'bg-success-subtle text-success border-success',

                                'updated'
                                    => 'bg-warning-subtle text-warning-emphasis border-warning',

                                'deleted'
                                    => 'bg-danger-subtle text-danger border-danger',

                                default
                                    => 'bg-info-subtle text-info border-info',

                            };


                            /*
                             * =================================================
                             * PROPERTIES
                             * =================================================
                             */

                            $properties = $log->properties;

                            $attributes = [];

                            $old = [];

                            if ($properties) {

                                $attributes =
                                    $properties->get(
                                        'attributes',
                                        []
                                    );

                                $old =
                                    $properties->get(
                                        'old',
                                        []
                                    );

                            }

                        @endphp


                        <tr>

                            {{-- BIL --}}
                            <td class="ps-3 text-center text-muted small fw-bold">

                                {{ $logs->firstItem() + $index }}

                            </td>


                            {{-- TARIKH --}}
                            <td class="font-monospace text-muted small">

                                {{ $log->created_at?->format('d/m/Y H:i:s') }}

                            </td>


                            {{-- PELAKU --}}
                            <td>

                                @if($causer)

                                    <div class="d-flex align-items-center">

                                        <div
                                            class="rounded-circle d-flex align-items-center justify-content-center me-2 flex-shrink-0"
                                            style="width: 36px; height: 36px; background-color: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe;"
                                        >

                                            <i class="fa-solid fa-user"></i>

                                        </div>

                                        <div class="overflow-hidden">

                                            <div
                                                class="fw-semibold text-dark text-truncate"
                                                title="{{ $causer->name }}"
                                            >

                                                {{ $causer->name }}

                                            </div>

                                            <small
                                                class="text-muted font-monospace text-truncate d-block"
                                                style="font-size: 0.72rem;"
                                                title="{{ $causer->email }}"
                                            >

                                                {{ $causer->email }}

                                            </small>

                                        </div>

                                    </div>

                                @else

                                    <div class="d-flex align-items-center">

                                        <div
                                            class="rounded-circle bg-secondary bg-opacity-10 text-secondary d-flex align-items-center justify-content-center me-2 flex-shrink-0"
                                            style="width: 36px; height: 36px;"
                                        >

                                            <i class="fa-solid fa-server"></i>

                                        </div>

                                        <div>

                                            <div class="fw-semibold text-muted">
                                                Sistem / Awam
                                            </div>

                                            <small class="text-muted">
                                                Tiada pengguna
                                            </small>

                                        </div>

                                    </div>

                                @endif

                            </td>


                            {{-- MODUL --}}
                            <td>

                                <span
                                    class="badge text-uppercase fw-semibold"
                                    style="background-color: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; font-size: 0.72rem; letter-spacing: 0.04em;"
                                >

                                    {{ str_replace('_', ' ', $log->log_name ?? 'default') }}

                                </span>

                            </td>


                            {{-- EVENT --}}
                            <td>

                                <span
                                    class="badge border border-opacity-25 text-uppercase {{ $badgeClass }}"
                                >

                                    @if($event === 'created')

                                        <i class="fa-solid fa-plus me-1"></i>

                                    @elseif($event === 'updated')

                                        <i class="fa-solid fa-pen me-1"></i>

                                    @elseif($event === 'deleted')

                                        <i class="fa-solid fa-trash me-1"></i>

                                    @else

                                        <i class="fa-solid fa-bolt me-1"></i>

                                    @endif

                                    {{ $event }}

                                </span>

                            </td>


                            {{-- DESCRIPTION --}}
                            <td>

                                <div class="text-secondary">

                                    {{ $log->description }}

                                </div>

                                @if($subject)

                                    <small class="text-muted">

                                        <i class="fa-solid fa-database me-1"></i>

                                        Rekod:
                                        {{ class_basename($subject) }}
                                        #{{ $log->subject_id }}

                                    </small>

                                @endif

                            </td>


                            {{-- PERUBAHAN --}}
                            <td class="text-center">

                                @if(
                                    count($attributes) > 0 ||
                                    count($old) > 0 ||
                                    $properties
                                )

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalChanges{{ $log->id }}"
                                    >

                                        <i class="fa-solid fa-eye me-1"></i>
                                        Perincian

                                    </button>

                                @else

                                    <span class="text-muted">
                                        -
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="text-center py-5 text-muted"
                            >

                                <i
                                    class="fa-solid fa-inbox fa-2x mb-3 d-block"
                                ></i>

                                Tiada rekod audit ditemui.

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- =====================================================
            PAGINATION
        ====================================================== --}}
        @if($logs->total() > 0)

            <div
                class="card-footer bg-white py-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-2"
            >

                <div class="small text-muted">

                    Menunjukkan

                    <strong>
                        {{ $logs->firstItem() }}
                    </strong>

                    hingga

                    <strong>
                        {{ $logs->lastItem() }}
                    </strong>

                    daripada

                    <strong>
                        {{ $logs->total() }}
                    </strong>

                    rekod audit

                </div>


                <div>

                    {{ $logs->appends(request()->query())->links('pagination::bootstrap-5') }}

                </div>

            </div>

        @endif

    </div>


    {{-- =========================================================
        MODALS
        PENTING:
        MODAL DILETAKKAN DI LUAR TABLE
        ========================================================== --}}

    @foreach($logs as $log)

        @php

            $properties = $log->properties;

            $attributes = $properties
                ? $properties->get('attributes', [])
                : [];

            $old = $properties
                ? $properties->get('old', [])
                : [];

            $causer = $log->causer;

            $event = strtolower(
                $log->event ?? 'action'
            );

        @endphp


        @if(
            count($attributes) > 0 ||
            count($old) > 0 ||
            $properties
        )

            <div
                class="modal fade"
                id="modalChanges{{ $log->id }}"
                tabindex="-1"
                aria-hidden="true"
            >

                <div class="modal-dialog modal-lg modal-dialog-centered">

                    <div class="modal-content border-0 shadow">


                        {{-- HEADER --}}
                        <div class="modal-header text-white" style="background: linear-gradient(135deg, #1e3a8a 0%, #0369a1 100%);">

                            <div>

                                <h6 class="modal-title mb-1">

                                    <i class="fa-solid fa-clock-rotate-left me-2"></i>

                                    Perincian Audit #{{ $log->id }}

                                </h6>

                                <small class="text-white-50">

                                    {{ $log->created_at?->format('d/m/Y H:i:s') }}

                                </small>

                            </div>

                            <button
                                type="button"
                                class="btn-close btn-close-white"
                                data-bs-dismiss="modal"
                            ></button>

                        </div>


                        {{-- BODY --}}
                        <div class="modal-body bg-light">


                            {{-- PELAKU --}}
                            <div class="card border-0 shadow-sm mb-3">

                                <div class="card-body">

                                    <div class="row">

                                        <div class="col-md-6">

                                            <small class="text-muted d-block">
                                                PELAKU
                                            </small>

                                            <strong>
                                                {{ $causer?->name ?? 'Sistem / Awam' }}
                                            </strong>

                                        </div>


                                        <div class="col-md-6">

                                            <small class="text-muted d-block">
                                                EMAIL
                                            </small>

                                            <strong>
                                                {{ $causer?->email ?? '-' }}
                                            </strong>

                                        </div>

                                    </div>

                                </div>

                            </div>


                            {{-- DESCRIPTION --}}
                            <div class="card border-0 shadow-sm mb-3">

                                <div class="card-body">

                                    <small class="text-muted d-block mb-1">
                                        BUTIRAN AKTIVITI
                                    </small>

                                    <div class="text-secondary">
                                        {{ $log->description }}
                                    </div>

                                </div>

                            </div>


                            {{-- UPDATED --}}
                            @if(
                                $event === 'updated' &&
                                count($attributes) > 0
                            )

                                <div class="table-responsive">

                                    <table
                                        class="table table-sm table-bordered bg-white align-middle"
                                    >

                                        <thead class="table-light">

                                            <tr>

                                                <th>
                                                    Medan
                                                </th>

                                                <th class="text-danger">
                                                    Data Asal
                                                </th>

                                                <th class="text-success">
                                                    Data Baharu
                                                </th>

                                            </tr>

                                        </thead>

                                        <tbody>

                                        @foreach($attributes as $key => $newValue)

                                            @php

                                                $oldValue =
                                                    $old[$key] ?? '-';

                                                if (is_bool($oldValue)) {

                                                    $oldValue =
                                                        $oldValue
                                                            ? 'Ya'
                                                            : 'Tidak';

                                                }

                                                if (is_bool($newValue)) {

                                                    $newValue =
                                                        $newValue
                                                            ? 'Ya'
                                                            : 'Tidak';

                                                }

                                                if (is_array($oldValue)) {

                                                    $oldValue =
                                                        json_encode(
                                                            $oldValue,
                                                            JSON_UNESCAPED_UNICODE
                                                        );

                                                }

                                                if (is_array($newValue)) {

                                                    $newValue =
                                                        json_encode(
                                                            $newValue,
                                                            JSON_UNESCAPED_UNICODE
                                                        );

                                                }

                                            @endphp


                                            <tr>

                                                <td class="fw-bold bg-light">
                                                    {{ $key }}
                                                </td>

                                                <td class="text-danger">
                                                    {{ $oldValue }}
                                                </td>

                                                <td class="text-success fw-bold">
                                                    {{ $newValue }}
                                                </td>

                                            </tr>

                                        @endforeach

                                        </tbody>

                                    </table>

                                </div>


                            {{-- CREATED --}}
                            @elseif(
                                $event === 'created' &&
                                count($attributes) > 0
                            )

                                <div class="table-responsive">

                                    <table
                                        class="table table-sm table-bordered bg-white"
                                    >

                                        <thead class="table-light">

                                            <tr>

                                                <th>
                                                    Medan
                                                </th>

                                                <th>
                                                    Nilai
                                                </th>

                                            </tr>

                                        </thead>

                                        <tbody>

                                        @foreach($attributes as $key => $value)

                                            @php

                                                if (is_bool($value)) {

                                                    $value =
                                                        $value
                                                            ? 'Ya'
                                                            : 'Tidak';

                                                }

                                                if (is_array($value)) {

                                                    $value =
                                                        json_encode(
                                                            $value,
                                                            JSON_UNESCAPED_UNICODE
                                                        );

                                                }

                                            @endphp

                                            <tr>

                                                <td class="fw-bold bg-light">
                                                    {{ $key }}
                                                </td>

                                                <td>
                                                    {{ $value }}
                                                </td>

                                            </tr>

                                        @endforeach

                                        </tbody>

                                    </table>

                                </div>


                            {{-- ACTION / LOGIN / FALLBACK --}}
                            @else

                                <div class="card border-0">

                                    <div class="card-body">

                                        <div class="fw-bold mb-2">

                                            <i class="fa-solid fa-code me-1"></i>

                                            Maklumat Aktiviti

                                        </div>

                                        <pre
                                            class="bg-dark text-light p-3 rounded small mb-0"
                                            style="white-space: pre-wrap;"
                                        >{{ json_encode(
                                            $properties?->toArray() ?? [],
                                            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
                                        ) }}</pre>

                                    </div>

                                </div>

                            @endif

                        </div>

                    </div>

                </div>

            </div>

        @endif

    @endforeach

</div>

@endsection