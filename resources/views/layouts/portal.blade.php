<!DOCTYPE html>
<html lang="ms" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Rasmi Sistem Pengurusan Surau</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <!-- <script>
        document.addEventListener('DOMContentLoaded', function () {
            console.log('=== TEST SELEPAS DOMContentLoaded ===');
            console.log('BOOTSTRAP GLOBAL:', window.bootstrap);
            console.log('SWAL GLOBAL:', window.Swal);
        });
    </script> -->
</head>
<body class="d-flex flex-column h-100 bg-light">

    {{-- Guna Header Portal --}}
    @include('layouts.partials.header-portal')

    <main class="flex-shrink-0">
        @yield('content')
    </main>

    @include('layouts.partials.footer-portal')

    @if(session('success') && session('reference_no'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    title: 'Permohonan Berjaya Dihantar!',
                    html: `
                        <p class="mb-2">{{ session('success') }}</p>
                        <div class="alert alert-success border-success bg-success bg-opacity-10 py-2 my-3">
                            <span class="d-block small text-muted">Nombor Rujukan Permohonan Anda:</span>
                            <strong class="fs-4 text-success font-monospace" id="refNoText">{{ session('reference_no') }}</strong>
                        </div>
                        <small class="text-danger">*Sila simpan nombor rujukan ini untuk semakan status permohonan.</small>
                    `,
                    icon: 'success',
                    confirmButtonText: '<i class="fa-solid fa-copy me-1"></i> Salin Nombor Rujukan & Tutup',
                    confirmButtonColor: '#198754',
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        navigator.clipboard.writeText("{{ session('reference_no') }}");
                    }
                });
            });
        </script>
    @endif

</body>
</html>