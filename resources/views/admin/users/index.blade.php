@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    {{-- =========================================================
        HEADER KORPORAT
    ========================================================== --}}
    <div class="hero-welcome-card p-4 mb-4 shadow-sm">
        <div class="hero-shape-1"></div>
        <div class="hero-shape-2"></div>
        <div class="d-flex align-items-center justify-content-between position-relative flex-wrap gap-3">
            <div class="d-flex align-items-center">
                <div class="rounded-circle p-3 me-3 text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 52px; height: 52px; background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3);">
                    <i class="fa-solid fa-users-gear fa-2x"></i>
                </div>
                <div>
                    <div class="badge bg-white bg-opacity-25 text-white mb-1" style="font-size:0.7rem; letter-spacing:0.05em;">
                        <i class="fa-solid fa-shield-halved me-1"></i> PENTADBIR SISTEM
                    </div>
                    <h4 class="fw-bold mb-0 text-white">
                        Pengurusan Pengguna &amp; Peranan
                    </h4>
                    <small class="text-white-50">
                        Menguruskan pendaftaran pengguna baharu, penetapan peranan sistem (*roles*), dan status keaktifan akaun.
                    </small>
                </div>
            </div>
            <button type="button" class="btn btn-light fw-bold px-3 py-2 shadow-sm rounded-2 text-primary hover-lift btn-add-user" data-bs-toggle="modal" data-bs-target="#modalUser" style="font-size:0.83rem;">
                <i class="fa-solid fa-user-plus me-1"></i>
                Daftar Pengguna Baharu
            </button>
        </div>
    </div>

    <!-- Notifikasi Kejayaan / Ralat -->
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

    <!-- Kad Borang Carian & Penapis Kompleks (Standard Audit Trail) -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3 bg-light rounded-3">
            <form action="{{ route('admin.users.index') }}" method="GET" class="row g-2 align-items-center">
                <!-- Carian Teks -->
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted mb-1">Carian Teks</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Cari nama, no. kad pengenalan atau e-mel..."
                            value="{{ request('search') }}"
                        >
                    </div>
                </div>

                <!-- Penapis Peranan (Role) -->
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">Peranan (*Role*)</label>
                    <select name="role_id" class="form-select form-select-sm">
                        <option value="">-- Semua Peranan --</option>
                        @foreach($roles as $r)
                            <option value="{{ $r->id }}" {{ request('role_id') == $r->id ? 'selected' : '' }}>
                                {{ $r->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Penapis Status Akaun -->
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted mb-1">Status Akaun</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">-- Semua Status --</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>AKTIF</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>NYAHAKTIF</option>
                    </select>
                </div>

                <!-- Bilangan Rekod Per Page -->
                <div class="col-md-1">
                    <label class="form-label small fw-bold text-muted mb-1">Paparan</label>
                    <select name="per_page" class="form-select form-select-sm font-monospace" onchange="this.form.submit()">
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>

                <!-- Butang Aksi -->
                <div class="col-md-2 d-flex gap-1 align-self-end">
                    <button type="submit" class="btn btn-sm btn-primary w-100 fw-semibold hover-lift" style="background-color:#1e40af; border-color:#1e40af;">
                        <i class="fa-solid fa-filter me-1"></i> Tapis
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary w-100 fw-medium" title="Sifarkan Penapis">
                        <i class="fa-solid fa-rotate-left me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Jadual Senarai Pengguna -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="bg-light text-secondary text-uppercase">
                        <tr>
                            <th class="ps-3" style="width: 50px;">Bil.</th>
                            <th>Maklumat Pegawai</th>
                            <th>Agensi / Bahagian</th>
                            <th>Peranan (*Roles*)</th>
                            <th>Masjid / Kariah Dijaga</th>
                            <th class="text-center">Status Akaun</th>
                            <th class="text-center pe-4" style="width: 140px;">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $index => $u)
                            <tr class="{{ !$u->is_active ? 'bg-light text-muted' : '' }}">
                                <td class="ps-3 font-monospace fw-bold text-secondary">{{ $users->firstItem() + $index }}</td>
                                <td>
                                    <div class="fw-semibold text-dark">
                                        {{ $u->name }}

                                        @if(auth()->id() === $u->id)
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 ms-1">
                                                Akaun Anda
                                            </span>
                                        @endif
                                    </div>

                                    <div class="small text-muted mt-1">
                                        <i class="fa-solid fa-id-card me-1"></i>
                                        {{ $u->no_kad_pengenalan ?? '—' }}
                                    </div>

                                    <div class="small text-muted">
                                        <i class="fa-solid fa-envelope me-1"></i>
                                        {{ $u->email }}
                                    </div>
                                </td>

                                <td>
                                    @if($u->agency)
                                        <div class="fw-semibold text-dark">
                                            {{ $u->agency->name }}
                                        </div>

                                        @if($u->department)
                                            <div class="small text-muted">
                                                {{ $u->department->name }}
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @forelse($u->roles as $role)
                                        <span class="badge fw-semibold me-1 mb-1" style="background-color: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe;">
                                            <i class="fa-solid fa-user-tag me-1"></i>{{ $role->name }}
                                        </span>
                                    @empty
                                        <span class="badge bg-secondary">Tiada Peranan</span>
                                    @endforelse
                                </td>
                                <td>
                                    @forelse($u->masjids as $masjid)
                                        <div class="mb-1">
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">
                                                <i class="fa-solid fa-mosque me-1"></i>
                                                {{ $masjid->nama_masjid }}
                                            </span>
                                        </div>
                                    @empty
                                        <span class="text-muted small">
                                            —
                                        </span>
                                    @endforelse
                                </td>
                                <td class="text-center">
                                    @if($u->is_active)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1"><i class="fa-solid fa-circle-check me-1"></i> AKTIF</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1"><i class="fa-solid fa-circle-xmark me-1"></i> NYAHAKTIF</span>
                                    @endif
                                </td>
                                <td class="text-center pe-4">
                                    <div class="d-flex justify-content-center gap-1">
                                        <!-- Butang Kemaskini -->
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-primary btn-edit-user"

                                            data-id="{{ $u->id }}"
                                            data-name="{{ $u->name }}"
                                            data-no-kad="{{ $u->no_kad_pengenalan }}"
                                            data-email="{{ $u->email }}"
                                            data-telefon-bimbit="{{ $u->no_telefon_bimbit }}"
                                            data-telefon-pejabat="{{ $u->no_telefon_pejabat }}"

                                            data-jawatan="{{ $u->jawatan }}"
                                            data-gred="{{ $u->gred }}"
                                            data-sambungan="{{ $u->sambungan }}"
                                            data-skim="{{ $u->skim_perkhidmatan }}"
                                            data-status-perkhidmatan="{{ $u->status_perkhidmatan }}"

                                            data-agency-id="{{ $u->agency_id }}"
                                            data-department-id="{{ $u->department_id }}"

                                            data-roles='@json($u->roles->pluck("id"))'
                                            data-masjids='@json($u->masjids->pluck("id"))'

                                            data-bs-toggle="modal"
                                            data-bs-target="#modalUser"

                                            title="Kemaskini Maklumat / Peranan"
                                        >
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>

                                        <!-- Butang Status (Activate / Deactivate) -->
                                        @if(auth()->id() !== $u->id)
                                            <form action="{{ route('admin.users.toggleStatus', $u->id) }}" method="POST" onsubmit="return confirm('Adakah anda pasti mahu mengubah status akaun ini?')">
                                                @csrf
                                                @method('PATCH')
                                                @if($u->is_active)
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Nyahaktifkan Akaun">
                                                        <i class="fa-solid fa-user-slash"></i>
                                                    </button>
                                                @else
                                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Aktifkan Akaun">
                                                        <i class="fa-solid fa-user-check"></i>
                                                    </button>
                                                @endif
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fa-solid fa-users-slash fa-2x mb-2 d-block text-secondary"></i> Tiada rekod pengguna ditemui.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination Standard Audit Trail -->
        <div class="card-footer bg-white py-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
            <div class="small text-muted font-monospace">
                Menunjukkan <strong>{{ $users->firstItem() ?? 0 }}</strong> hingga <strong>{{ $users->lastItem() ?? 0 }}</strong> daripada <strong>{{ $users->total() }}</strong> rekod pengguna
            </div>
            <div>
                {{ $users->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
    
    {{-- MODAL TAMBAH / EDIT PENGGUNA --}}
    @include('admin.users._user_modal')

</div>
@endsection

@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'success',
                title: 'Berjaya!',
                text: @json(session('success')),
                confirmButtonText: 'OK'
            });
        });
    </script>
@endif

@if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'error',
                title: 'Ralat!',
                text: @json(session('error')),
                confirmButtonText: 'OK'
            });
        });
    </script>
@endif
