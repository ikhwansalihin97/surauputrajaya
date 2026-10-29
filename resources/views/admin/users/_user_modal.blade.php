<div
    class="modal fade"
    id="modalUser"
    tabindex="-1"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered modal-lg">

        <div class="modal-content border-0 shadow">

            {{-- =====================================================
                HEADER
            ====================================================== --}}

            <div class="modal-header text-white" style="background: linear-gradient(135deg, #1e3a8a 0%, #0369a1 100%);">

                <h5
                    class="modal-title fw-bold fs-6"
                    id="modalUserTitle"
                >
                    <i class="fa-solid fa-user-plus me-2"></i>
                    Daftar Pengguna Baharu
                </h5>

                <button
                    type="button"
                    class="btn-close btn-close-white"
                    data-bs-dismiss="modal"
                ></button>

            </div>


            {{-- =====================================================
                FORM
            ====================================================== --}}

            <form
                id="formUser"
                action="{{ route('admin.users.store') }}"
                method="POST"
            >

                @csrf

                {{-- Digunakan untuk EDIT sahaja --}}
                <input
                    type="hidden"
                    name="_method"
                    id="userFormMethod"
                    value="POST"
                >

                <div class="modal-body">

                    {{-- =================================================
                        MAKLUMAT PERIBADI
                    ================================================== --}}

                    <div class="mb-3">

                        <div class="fw-bold text-primary border-bottom pb-2">

                            <i class="fa-solid fa-user me-1"></i>

                            Maklumat Peribadi

                        </div>

                    </div>


                    <div class="row g-3">

                        {{-- Nama --}}
                        <div class="col-md-8">

                            <label class="form-label fw-semibold small">

                                Nama Penuh

                                <span class="text-danger">*</span>

                            </label>

                            <input
                                type="text"
                                name="name"
                                id="userName"
                                class="form-control form-control-sm"
                                required
                            >

                        </div>


                        {{-- IC --}}
                        <div class="col-md-4">

                            <label class="form-label fw-semibold small">

                                No. Kad Pengenalan

                                <span class="text-danger">*</span>

                            </label>

                            <input
                                type="text"
                                name="no_kad_pengenalan"
                                id="userNoKad"
                                class="form-control form-control-sm"
                                maxlength="14"
                                required
                            >

                        </div>


                        {{-- Email --}}
                        <div class="col-md-6">

                            <label class="form-label fw-semibold small">

                                E-Mel Rasmi

                                <span class="text-danger">*</span>

                            </label>

                            <input
                                type="email"
                                name="email"
                                id="userEmail"
                                class="form-control form-control-sm"
                                required
                            >

                            <small class="text-muted">

                                Digunakan untuk log masuk dan OTP.

                            </small>

                        </div>


                        {{-- Telefon Bimbit --}}
                        <div class="col-md-3">

                            <label class="form-label fw-semibold small">

                                Telefon Bimbit

                                <span class="text-danger">*</span>

                            </label>

                            <input
                                type="text"
                                name="no_telefon_bimbit"
                                id="userTelefonBimbit"
                                class="form-control form-control-sm"
                                required
                            >

                        </div>


                        {{-- Telefon Pejabat --}}
                        <div class="col-md-3">

                            <label class="form-label fw-semibold small">

                                Telefon Pejabat

                            </label>

                            <input
                                type="text"
                                name="no_telefon_pejabat"
                                id="userTelefonPejabat"
                                class="form-control form-control-sm"
                            >

                        </div>

                    </div>


                    {{-- =================================================
                        MAKLUMAT PERKHIDMATAN
                    ================================================== --}}

                    <div class="mt-4 mb-3">

                        <div class="fw-bold text-primary border-bottom pb-2">

                            <i class="fa-solid fa-briefcase me-1"></i>

                            Maklumat Perkhidmatan

                        </div>

                    </div>


                    <div class="row g-3">

                        {{-- Jawatan --}}
                        <div class="col-md-6">

                            <label class="form-label fw-semibold small">

                                Jawatan

                            </label>

                            <input
                                type="text"
                                name="jawatan"
                                id="userJawatan"
                                class="form-control form-control-sm"
                            >

                        </div>


                        {{-- Gred --}}
                        <div class="col-md-3">

                            <label class="form-label fw-semibold small">

                                Gred

                            </label>

                            <input
                                type="text"
                                name="gred"
                                id="userGred"
                                class="form-control form-control-sm"
                            >

                        </div>


                        {{-- Sambungan --}}
                        <div class="col-md-3">

                            <label class="form-label fw-semibold small">

                                Sambungan

                            </label>

                            <input
                                type="text"
                                name="sambungan"
                                id="userSambungan"
                                class="form-control form-control-sm"
                            >

                        </div>


                        {{-- Skim --}}
                        <div class="col-md-6">

                            <label class="form-label fw-semibold small">

                                Skim Perkhidmatan

                            </label>

                            <input
                                type="text"
                                name="skim_perkhidmatan"
                                id="userSkim"
                                class="form-control form-control-sm"
                            >

                        </div>


                        {{-- Status --}}
                        <div class="col-md-6">

                            <label class="form-label fw-semibold small">

                                Status Perkhidmatan

                            </label>

                            <select
                                name="status_perkhidmatan"
                                id="userStatusPerkhidmatan"
                                class="form-select form-select-sm"
                            >

                                <option value="">
                                    -- Pilih Status --
                                </option>

                                <option value="TETAP">
                                    TETAP
                                </option>

                                <option value="KONTRAK">
                                    KONTRAK
                                </option>

                                <option value="SAMBILAN">
                                    SAMBILAN
                                </option>

                                <option value="LAIN_LAIN">
                                    LAIN-LAIN
                                </option>

                            </select>

                        </div>

                    </div>


                    {{-- =================================================
                        ORGANISASI
                    ================================================== --}}

                    <div class="mt-4 mb-3">

                        <div class="fw-bold text-primary border-bottom pb-2">

                            <i class="fa-solid fa-building me-1"></i>

                            Organisasi

                        </div>

                    </div>


                    <div class="row g-3">

                        {{-- Agency --}}
                        <div class="col-md-6">

                            <label class="form-label fw-semibold small">

                                Jabatan / Agensi

                            </label>

                            <select
                                name="agency_id"
                                id="userAgency"
                                class="form-select form-select-sm"
                            >

                                <option value="">
                                    -- Tiada / Bukan Pegawai Kerajaan --
                                </option>

                                @foreach($agencies as $agency)

                                    <option value="{{ $agency->id }}">

                                        {{ $agency->name }}

                                        @if($agency->code)
                                            ({{ $agency->code }})
                                        @endif

                                    </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- Department --}}
                        <div class="col-md-6">

                            <label class="form-label fw-semibold small">

                                Bahagian / Jabatan

                            </label>

                            <select
                                name="department_id"
                                id="userDepartment"
                                class="form-select form-select-sm"
                            >

                                <option value="">
                                    -- Pilih Bahagian --
                                </option>

                                @foreach($departments as $department)

                                    <option value="{{ $department->id }}">

                                        {{ $department->name }}

                                        @if($department->code)
                                            ({{ $department->code }})
                                        @endif

                                    </option>

                                @endforeach

                            </select>

                        </div>

                    </div>


                    {{-- =================================================
                        AKAUN & KESELAMATAN
                    ================================================== --}}

                    <div class="mt-4 mb-3">

                        <div class="fw-bold text-primary border-bottom pb-2">

                            <i class="fa-solid fa-shield-halved me-1"></i>

                            Akaun & Keselamatan

                        </div>

                    </div>


                    <div class="row g-3">

                        {{-- Password --}}
                        <div class="col-md-6">

                            <label class="form-label fw-semibold small">

                                Kata Laluan

                                <span
                                    class="text-danger"
                                    id="passwordRequiredStar"
                                >
                                    *
                                </span>

                            </label>

                            <input
                                type="password"
                                name="password"
                                id="userPassword"
                                class="form-control form-control-sm"
                                minlength="8"
                            >

                            <small
                                class="text-muted"
                                id="passwordHelp"
                            >
                                Minimum 8 aksara.
                            </small>

                        </div>


                        {{-- Confirm Password --}}
                        <div class="col-md-6">

                            <label class="form-label fw-semibold small">

                                Sahkan Kata Laluan

                                <span
                                    class="text-danger"
                                    id="passwordConfirmationRequiredStar"
                                >
                                    *
                                </span>

                            </label>

                            <input
                                type="password"
                                name="password_confirmation"
                                id="userPasswordConfirmation"
                                class="form-control form-control-sm"
                                minlength="8"
                            >

                        </div>

                    </div>


                    {{-- =================================================
                        ROLE
                    ================================================== --}}

                    <div class="mt-4 mb-3">

                        <div class="fw-bold text-primary border-bottom pb-2">

                            <i class="fa-solid fa-user-shield me-1"></i>

                            Peranan Sistem

                        </div>

                    </div>


                    <div class="p-3 border rounded bg-light">

                        <div class="small text-muted mb-2">

                            Pilih sekurang-kurangnya satu peranan.

                        </div>


                        @foreach($roles as $r)

                            <div class="form-check mb-1">

                                <input
                                    class="form-check-input user-role-checkbox"
                                    type="checkbox"
                                    name="roles[]"
                                    value="{{ $r->id }}"
                                    id="userRole{{ $r->id }}"
                                    data-role-slug="{{ $r->slug }}"
                                >

                                <label
                                    class="form-check-label small fw-semibold"
                                    for="userRole{{ $r->id }}"
                                >

                                    <i class="fa-solid fa-user-tag me-1 text-primary"></i>

                                    {{ $r->name }}

                                </label>

                            </div>

                        @endforeach

                    </div>


                    {{-- =================================================
                        MASJID / KARIAH
                    ================================================== --}}

                    <div
                        id="masjidSectionUser"
                        class="mt-4 d-none"
                    >

                        <div class="mb-3">

                            <div class="fw-bold text-primary border-bottom pb-2">

                                <i class="fa-solid fa-mosque me-1"></i>

                                Masjid / Kariah Di Bawah Seliaan

                            </div>

                        </div>


                        <div
                            class="p-3 border rounded bg-light"
                            style="max-height: 200px; overflow-y: auto;"
                        >

                            @foreach($masjids as $masjid)

                                <div class="form-check mb-1">

                                    <input
                                        class="form-check-input user-masjid-checkbox"
                                        type="checkbox"
                                        name="masjids[]"
                                        value="{{ $masjid->id }}"
                                        id="userMasjid{{ $masjid->id }}"
                                    >

                                    <label
                                        class="form-check-label small"
                                        for="userMasjid{{ $masjid->id }}"
                                    >

                                        <i class="fa-solid fa-mosque me-1 text-primary"></i>

                                        {{ $masjid->nama_masjid }}

                                    </label>

                                </div>

                            @endforeach

                        </div>


                        <small class="text-muted">

                            Digunakan terutamanya untuk akaun
                            <strong>Pentadbir Masjid</strong>.

                        </small>

                    </div>

                </div>


                {{-- =================================================
                    FOOTER
                ================================================== --}}

                <div class="modal-footer bg-light py-2">

                    <button
                        type="button"
                        class="btn btn-sm btn-secondary"
                        data-bs-dismiss="modal"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="btn btn-sm btn-primary fw-bold hover-lift"
                        id="btnSubmitUser"
                        style="background-color:#1e40af; border-color:#1e40af;"
                    >
                        <i class="fa-solid fa-check me-1"></i>

                        <span id="btnSubmitUserText">
                            Daftar Pengguna
                        </span>

                    </button>

                </div>

            </form>

        </div>

    </div>
</div>


{{-- =============================================================
    JAVASCRIPT
============================================================= --}}

@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    const modal = document.getElementById('modalUser');

    if (!modal) {
        console.error('modalUser tidak dijumpai');
        return;
    }

    const form = document.getElementById('formUser');
    const formMethod = document.getElementById('userFormMethod');

    const modalTitle = document.getElementById('modalUserTitle');
    const submitText = document.getElementById('btnSubmitUserText');

    const password = document.getElementById('userPassword');
    const passwordConfirmation =
        document.getElementById('userPasswordConfirmation');

    const passwordHelp =
        document.getElementById('passwordHelp');

    const passwordRequiredStar =
        document.getElementById('passwordRequiredStar');

    const passwordConfirmationRequiredStar =
        document.getElementById(
            'passwordConfirmationRequiredStar'
        );

    const masjidSection =
        document.getElementById('masjidSectionUser');

    const roleCheckboxes =
        document.querySelectorAll('.user-role-checkbox');

    const masjidCheckboxes =
        document.querySelectorAll('.user-masjid-checkbox');


    // ==========================================================
    // RESET FORM
    // ==========================================================

    function resetUserForm() {

        form.reset();

        form.action = "{{ route('admin.users.store') }}";

        formMethod.value = 'POST';

        modalTitle.innerHTML =
            '<i class="fa-solid fa-user-plus me-2"></i>' +
            'Daftar Pengguna Baharu';

        submitText.textContent = 'Daftar Pengguna';

        password.required = true;
        passwordConfirmation.required = true;

        passwordHelp.textContent =
            'Minimum 8 aksara.';

        passwordRequiredStar.classList.remove('d-none');

        passwordConfirmationRequiredStar.classList.remove('d-none');

        roleCheckboxes.forEach(function (checkbox) {
            checkbox.checked = false;
        });

        masjidCheckboxes.forEach(function (checkbox) {
            checkbox.checked = false;
        });

        masjidSection.classList.add('d-none');
    }


    // ==========================================================
    // CHECK ROLE PENTADBIR MASJID
    // ==========================================================

    function updateMasjidSection() {

        const isPentadbirMasjid =
            Array.from(roleCheckboxes).some(function (checkbox) {

                return checkbox.checked &&
                       checkbox.dataset.roleSlug === 'masjid';

            });

        if (isPentadbirMasjid) {

            masjidSection.classList.remove('d-none');

        } else {

            masjidSection.classList.add('d-none');

        }
    }


    // ==========================================================
    // ROLE CHANGE
    // ==========================================================

    roleCheckboxes.forEach(function (checkbox) {

        checkbox.addEventListener('change', function () {

            updateMasjidSection();

        });

    });


    // ==========================================================
    // ADD USER
    // ==========================================================

    document
        .querySelectorAll('.btn-add-user')
        .forEach(function (button) {

            button.addEventListener('click', function () {

                resetUserForm();

            });

        });


    // ==========================================================
    // EDIT USER
    // ==========================================================

    document
        .querySelectorAll('.btn-edit-user')
        .forEach(function (button) {

            button.addEventListener('click', function () {

                const data = this.dataset;

                form.action =
                    "{{ url('/admin/users') }}/" + data.id;

                formMethod.value = 'PUT';


                // TITLE

                modalTitle.innerHTML =
                    '<i class="fa-solid fa-user-pen me-2"></i>' +
                    'Kemaskini: ' +
                    data.name;

                submitText.textContent =
                    'Simpan Perubahan';


                // PERSONAL

                document.getElementById('userName').value =
                    data.name || '';

                document.getElementById('userNoKad').value =
                    data.noKad || '';

                document.getElementById('userEmail').value =
                    data.email || '';

                document.getElementById('userTelefonBimbit').value =
                    data.telefonBimbit || '';

                document.getElementById('userTelefonPejabat').value =
                    data.telefonPejabat || '';


                // SERVICE

                document.getElementById('userJawatan').value =
                    data.jawatan || '';

                document.getElementById('userGred').value =
                    data.gred || '';

                document.getElementById('userSambungan').value =
                    data.sambungan || '';

                document.getElementById('userSkim').value =
                    data.skim || '';

                document.getElementById('userStatusPerkhidmatan').value =
                    data.statusPerkhidmatan || '';


                // ORGANIZATION

                document.getElementById('userAgency').value =
                    data.agencyId || '';

                document.getElementById('userDepartment').value =
                    data.departmentId || '';


                // PASSWORD

                password.value = '';
                passwordConfirmation.value = '';

                password.required = false;
                passwordConfirmation.required = false;

                passwordHelp.textContent =
                    'Biarkan kosong jika tidak mahu menukar kata laluan.';

                passwordRequiredStar.classList.add('d-none');

                passwordConfirmationRequiredStar.classList.add('d-none');


                // ROLES

                let selectedRoles = [];

                try {

                    selectedRoles =
                        JSON.parse(data.roles || '[]');

                } catch (error) {

                    selectedRoles = [];

                }

                roleCheckboxes.forEach(function (checkbox) {

                    checkbox.checked =
                        selectedRoles.includes(
                            Number(checkbox.value)
                        );

                });


                // MASJIDS

                let selectedMasjids = [];

                try {

                    selectedMasjids =
                        JSON.parse(data.masjids || '[]');

                } catch (error) {

                    selectedMasjids = [];

                }

                masjidCheckboxes.forEach(function (checkbox) {

                    checkbox.checked =
                        selectedMasjids.includes(
                            Number(checkbox.value)
                        );

                });


                updateMasjidSection();

            });

        });


    // ==========================================================
    // MODAL DITUTUP
    // ==========================================================

    modal.addEventListener('hidden.bs.modal', function () {

        resetUserForm();

    });

});
</script>

@endpush