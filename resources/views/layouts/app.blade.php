<!DOCTYPE html>
<html lang="ms" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sistem Pengurusan Surau — JAWI</title>

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        :root {
            --corp-dark:   #0f172a;
            --corp-blue:   #1e40af;
            --corp-accent: #3b82f6;
            --corp-light:  #f8fafc;
            --sidebar-w:   240px;
            --sidebar-bg:  #f0f7fc; /* Light blue sidebar background */
        }

        body {
            background-color: var(--corp-light);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        /* ── Sidebar ── */
        #app-sidebar {
            width: var(--sidebar-w);
            min-height: calc(100vh - 54px);
            background-color: var(--sidebar-bg);
            border-right: 1px solid #dbeafe;
            flex-shrink: 0;
        }

        /* ── Content area ── */
        #app-content {
            flex: 1;
            min-width: 0;
            padding: 1.5rem;
            overflow-y: auto;
        }

        /* ── KPI card uniform height & modern hover ── */
        .kpi-card {
            min-height: 105px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .hover-lift {
            transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .hover-lift:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 24px -4px rgba(30, 64, 175, 0.12), 0 6px 12px -4px rgba(30, 64, 175, 0.06) !important;
        }

        /* ── Hero Welcome Banner ── */
        .hero-welcome-card {
            background: linear-gradient(135deg, #1e3a8a 0%, #0369a1 100%);
            color: #ffffff;
            border-radius: 12px;
            position: relative;
            overflow: hidden;
            border: 1px solid #bfdbfe;
        }
        .hero-shape-1 {
            position: absolute;
            right: -30px;
            top: -40px;
            width: 180px;
            height: 180px;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        .hero-shape-2 {
            position: absolute;
            right: 80px;
            bottom: -50px;
            width: 140px;
            height: 140px;
            background: radial-gradient(circle, rgba(56, 189, 248, 0.2) 0%, rgba(56, 189, 248, 0) 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        /* ── Initial Avatar ── */
        .avatar-initial {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background-color: #eff6ff;
            color: #1e40af;
            font-weight: 700;
            font-size: 0.78rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #bfdbfe;
            flex-shrink: 0;
        }

        /* ── KPI Micro Progress Bar ── */
        .kpi-progress {
            height: 4px;
            border-radius: 2px;
            background-color: #f1f5f9;
            overflow: hidden;
            margin-top: 8px;
        }
        .kpi-progress-bar {
            height: 100%;
            border-radius: 2px;
        }

        /* ── Table tweaks ── */
        .table thead th {
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            background-color: #f8fafc;
            color: #64748b;
            border-bottom: 2px solid #e2e8f0;
        }
        .table tbody tr:hover { background-color: #f0f9ff; }

        /* ── Badge status ── */
        .badge-status { font-size: 0.74rem; font-weight: 600; padding: 0.3rem 0.65rem; border-radius: 6px; }

        /* ── Section title ── */
        .section-title {
            font-size: 0.70rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 0.75rem;
        }

        /* ── Card polish ── */
        .card { border: 1px solid #e2e8f0; }
        .card-header { border-bottom: 1px solid #e2e8f0; }

        /* ── Stepper (surau tracker) ── */
        .step-connector {
            flex: 1;
            height: 3px;
            background-color: #e2e8f0;
            margin-top: -1.1rem;
        }
        .step-connector.done  { background-color: #22c55e; }
        .step-connector.error { background-color: #ef4444; }

        /* ── Navbar & Dropdown z-index layer fixes ── */
        .navbar {
            position: relative;
            z-index: 1050 !important;
        }
        .dropdown-menu {
            z-index: 1060 !important;
        }

        /* ── Sidebar nav items ── */
        .sidebar-nav-link {
            display: flex;
            align-items: center;
            padding: 0.5rem 0.85rem;
            border-radius: 8px;
            color: #334155;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            transition: background 0.15s, color 0.15s;
            gap: 0.65rem;
        }
        .sidebar-nav-link:hover {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .sidebar-nav-link.active {
            background-color: var(--corp-blue);
            color: #ffffff;
            font-weight: 600;
        }
        .sidebar-nav-link .nav-icon { width: 18px; text-align: center; }

        .sidebar-section-label {
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.10em;
            text-transform: uppercase;
            color: #64748b;
            padding: 0 0.85rem;
            margin-top: 1.25rem;
            margin-bottom: 0.4rem;
        }
    </style>
    @stack('styles')
</head>

<body class="d-flex flex-column min-vh-100">

    {{-- Top Navbar --}}
    @include('layouts.partials.header-dashboard')

    {{-- Main wrapper --}}
    <div class="d-flex flex-grow-1" style="min-height: 0;">

        {{-- Sidebar --}}
        <aside id="app-sidebar">
            @include('layouts.partials.sidebar')
        </aside>

        {{-- Page content --}}
        <main id="app-content">
            @auth
                @if(!request()->routeIs('pertukaran-pentadbir.*')
                    && \App\Models\SurauPertukaranPentadbir::where('kepada_user_id', auth()->id())->aktif()->exists())
                    <div class="container pt-4 pb-0">
                        <div class="alert alert-primary border-0 shadow-sm d-flex flex-wrap align-items-center justify-content-between gap-2 mb-0">
                            <span><i class="fa-solid fa-user-shield me-2"></i>Anda mempunyai permintaan serah tugas <strong>Pentadbir Surau</strong> yang menunggu tindakan.</span>
                            <a href="{{ route('pertukaran-pentadbir.index') }}" class="btn btn-sm btn-primary fw-semibold">Lihat &amp; Terima</a>
                        </div>
                    </div>
                @endif
            @endauth
            @yield('content')
        </main>

    </div>

    {{-- Footer --}}
    @include('layouts.partials.footer')

    @stack('scripts')

</body>
</html>