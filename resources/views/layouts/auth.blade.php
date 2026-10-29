<!DOCTYPE html>
<html lang="ms" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Masuk - Sistem Pengurusan Surau</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body class="d-flex flex-column h-100 bg-light">

    {{-- Kandungan Utama (Borang Login) tanpa Header --}}
    <main class="flex-shrink-0 my-auto py-5">
        @yield('content')
    </main>

    {{-- Footer Sahaja --}}
    @include('layouts.partials.footer')

</body>
</html>