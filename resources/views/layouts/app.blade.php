<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Yamaha DMPMS</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- DataTables Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root {
            --yamaha-blue: #003399;
            --yamaha-blue-dark: #002266;
            --yamaha-red: #E60012;
            --yamaha-dark: #0F172A;
            --yamaha-light: #F8FAFC;
            --sidebar-width: 270px;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F1F5F9;
            color: #334155;
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        #sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: linear-gradient(180deg, #002266 0%, #001540 100%);
            color: #FFFFFF;
            z-index: 1050;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 4px 0 25px rgba(0, 0, 0, 0.15);
            overflow-y: auto;
        }

        .sidebar-brand {
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-brand img {
            height: 36px;
        }

        .sidebar-menu {
            padding: 1.25rem 0.85rem;
            list-style: none;
            margin: 0;
        }

        .sidebar-menu .menu-header {
            font-size: 0.725rem;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #94A3B8;
            padding: 0.75rem 1rem 0.35rem 1rem;
            font-weight: 700;
        }

        .sidebar-menu li a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.75rem 1rem;
            color: #CBD5E1;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            margin-bottom: 3px;
        }

        .sidebar-menu li a:hover,
        .sidebar-menu li.active a {
            background: rgba(255, 255, 255, 0.12);
            color: #FFFFFF;
            font-weight: 600;
            transform: translateX(4px);
        }

        .sidebar-menu li a i {
            width: 20px;
            font-size: 1.1rem;
            text-align: center;
        }

        /* Sidebar Backdrop Overlay on Mobile */
        #sidebarBackdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(15, 23, 42, 0.5);
            backdrop-filter: blur(4px);
            z-index: 1040;
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        #sidebarBackdrop.show {
            display: block;
            opacity: 1;
        }

        /* Main Content */
        #main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        /* Top Navbar */
        .top-navbar {
            background: #FFFFFF;
            height: 70px;
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.04);
            position: sticky;
            top: 0;
            z-index: 999;
        }

        /* Cards & Glassmorphism */
        .card-custom {
            background: #FFFFFF;
            border-radius: 16px;
            border: 1px solid #E2E8F0;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card-custom:hover {
            box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.08);
        }

        .stat-card {
            padding: 1.5rem;
            border-radius: 16px;
            position: relative;
            overflow: hidden;
        }

        .stat-card .icon-box {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .badge-status-merah { background-color: #DC3545; color: #FFF; }
        .badge-status-kuning { background-color: #FFC107; color: #000; }
        .badge-status-hijau { background-color: #198754; color: #FFF; }
        .badge-status-biru { background-color: #0D6EFD; color: #FFF; }

        .btn-yamaha-primary {
            background-color: var(--yamaha-blue);
            color: #FFFFFF;
            font-weight: 600;
            border-radius: 10px;
            padding: 0.55rem 1.25rem;
            border: none;
            transition: all 0.2s;
        }
        .btn-yamaha-primary:hover {
            background-color: var(--yamaha-blue-dark);
            color: #FFFFFF;
        }

        /* Responsive Breakpoints (< 992px) */
        @media (max-width: 991.98px) {
            #sidebar {
                left: -270px;
                transform: translateX(-100%);
            }

            #sidebar.show {
                left: 0;
                transform: translateX(0);
            }

            #main-wrapper {
                margin-left: 0 !important;
            }

            .top-navbar {
                padding: 0.75rem 1rem;
                height: auto;
                min-height: 65px;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Mobile Sidebar Backdrop -->
    <div id="sidebarBackdrop"></div>

    <!-- Sidebar -->
    <aside id="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-motorcycle text-danger fa-2x"></i>
            <div>
                <div class="fw-bold text-white fs-6" style="letter-spacing: 0.5px;">YAMAHA DMPMS</div>
                <div class="text-white-50" style="font-size: 0.68rem;">PT. Aspacindo Kedaton Motor</div>
            </div>
            <button id="sidebarClose" class="btn text-white p-0 ms-auto d-lg-none fs-5" type="button" aria-label="Close Sidebar">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <ul class="sidebar-menu">
            <li class="menu-header">NAVIGASI UTAMA</li>
            <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}"><i class="fas fa-chart-pie text-info"></i> Executive Dashboard</a>
            </li>
            <li class="{{ request()->routeIs('leaderboard.index') ? 'active' : '' }}">
                <a href="{{ route('leaderboard.index') }}"><i class="fas fa-trophy text-warning"></i> Leaderboard Cabang</a>
            </li>
            <li class="{{ request()->routeIs('branch.performance') ? 'active' : '' }}">
                <a href="{{ route('branch.performance') }}"><i class="fas fa-chart-line text-success"></i> Performance Cabang</a>
            </li>

            <li class="menu-header">LAPORAN DIGITAL</li>
            <li class="{{ request()->routeIs('reports.daily.*') ? 'active' : '' }}">
                <a href="{{ route('reports.daily.index') }}"><i class="fas fa-calendar-check text-primary"></i> Daily Report (Harian)</a>
            </li>
            <li class="{{ request()->routeIs('reports.tiktok-live.*') ? 'active' : '' }}">
                <a href="{{ route('reports.tiktok-live.index') }}"><i class="fab fa-tiktok text-danger"></i> Live TikTok Report</a>
            </li>
            <li class="{{ request()->routeIs('reports.weekly.*') ? 'active' : '' }}">
                <a href="{{ route('reports.weekly.index') }}"><i class="fas fa-calendar-week text-warning"></i> Weekly Report (Post Insight)</a>
            </li>
            <li class="{{ request()->routeIs('reports.monthly.*') ? 'active' : '' }}">
                <a href="{{ route('reports.monthly.index') }}"><i class="fas fa-file-invoice text-danger"></i> Monthly Insight & SS</a>
            </li>

            @role('Super Admin')
            <li class="menu-header">KPI & MANAGEMENT</li>
            <li class="{{ request()->routeIs('kpis.*') ? 'active' : '' }}">
                <a href="{{ route('kpis.index') }}"><i class="fas fa-bullseye text-warning"></i> Target KPI Cabang</a>
            </li>
            <li class="{{ request()->routeIs('master.branches.*') ? 'active' : '' }}">
                <a href="{{ route('master.branches.index') }}"><i class="fas fa-store text-info"></i> Master Cabang</a>
            </li>
            <li class="{{ request()->routeIs('master.users.*') ? 'active' : '' }}">
                <a href="{{ route('master.users.index') }}"><i class="fas fa-users text-primary"></i> Master User Account</a>
            </li>

            <li class="menu-header">PRODUKTIVITAS DIGITAL</li>
            <li class="{{ request()->routeIs('todos.*') ? 'active' : '' }}">
                <a href="{{ route('todos.index') }}"><i class="fas fa-tasks text-success"></i> Kanban To-Do Board</a>
            </li>
            <li class="{{ request()->routeIs('personal-kpis.*') ? 'active' : '' }}">
                <a href="{{ route('personal-kpis.index') }}"><i class="fas fa-user-check text-warning"></i> KPI Pribadi AMD</a>
            </li>
            <li class="menu-header">EXPORT LAPORAN</li>
            <li class="{{ request()->routeIs('exports.*') ? 'active' : '' }}">
                <a href="{{ route('exports.index') }}"><i class="fas fa-file-export text-secondary"></i> Export PDF / Excel</a>
            </li>
            @endrole
        </ul>
    </aside>

    <!-- Main Content Wrapper -->
    <div id="main-wrapper">
        <!-- Top Navbar -->
        <header class="top-navbar">
            <div class="d-flex align-items-center gap-2 gap-md-3">
                <button id="sidebarToggle" class="btn btn-light d-lg-none me-1 shadow-sm border rounded-3 p-2" type="button" aria-label="Toggle Navigation">
                    <i class="fas fa-bars fs-5 text-primary"></i>
                </button>
                <div>
                    <h5 class="m-0 fw-bold text-dark fs-6 fs-md-5 text-truncate" style="max-width: 180px; @media (min-width: 576px) { max-width: none; }">@yield('title')</h5>
                    <span class="badge bg-danger rounded-pill px-2.5 py-1 mt-1 d-inline-block text-truncate" style="max-width: 200px; font-size: 0.72rem;">
                        <i class="fas fa-building me-1"></i> {{ auth()->user()->branch ? auth()->user()->branch->nama_cabang : 'Head Office (Semua Cabang)' }}
                    </span>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 gap-md-3 ms-auto">
                <!-- Role Badge -->
                <span class="badge bg-primary rounded-pill px-3 py-2 me-1 fs-6 d-none d-sm-inline-block">
                    <i class="fas fa-user-shield me-1"></i> {{ auth()->user()->roles->first()?->name }}
                </span>

                <!-- User Profile Dropdown -->
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" data-bs-toggle="dropdown">
                        <div class="bg-primary text-white rounded-circle me-1 me-md-2 d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px;">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                        <div class="text-start d-none d-md-block">
                            <div class="fw-bold fs-6 leading-tight">{{ auth()->user()->name }}</div>
                            <div class="text-muted small" style="font-size: 0.75rem;">{{ auth()->user()->email }}</div>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger"><i class="fas fa-sign-out-alt me-2"></i> Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Page Content Body -->
        <main class="p-3 p-md-4 flex-grow-1">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white py-3 px-3 px-md-4 text-center text-muted small border-top">
            © 2026 PT. Aspacindo Kedaton Motor (Dealer Main Yamaha) - Digital Marketing Performance Management System.
        </footer>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarClose = document.getElementById('sidebarClose');
            const sidebarBackdrop = document.getElementById('sidebarBackdrop');

            function openSidebar() {
                if (sidebar) sidebar.classList.add('show');
                if (sidebarBackdrop) sidebarBackdrop.classList.add('show');
                document.body.style.overflow = 'hidden';
            }

            function closeSidebar() {
                if (sidebar) sidebar.classList.remove('show');
                if (sidebarBackdrop) sidebarBackdrop.classList.remove('show');
                document.body.style.overflow = '';
            }

            if (sidebarToggle) sidebarToggle.addEventListener('click', openSidebar);
            if (sidebarClose) sidebarClose.addEventListener('click', closeSidebar);
            if (sidebarBackdrop) sidebarBackdrop.addEventListener('click', closeSidebar);

            const sidebarLinks = document.querySelectorAll('#sidebar .sidebar-menu a');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth < 992) closeSidebar();
                });
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
