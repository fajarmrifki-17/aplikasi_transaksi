<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Sistem Pengajuan Transaksi') | {{ config('app.name', 'Laravel') }}</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- Custom Premium Stylesheet -->
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">

    @yield('styles')
</head>
<body>
    <!-- Theme loader to prevent white flash in dark mode -->
    <script>
        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark-mode');
        }
    </script>

    <div class="app-wrapper" id="appWrapper">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="brand">
                <i class="bi bi-wallet2 fs-4"></i>
                <span>EXPENSE SYSTEM</span>
            </div>
            
            <ul class="sidebar-menu">
                <!-- All Roles have access to Dashboard -->
                <li class="{{ Request::routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- STAFF MENUS -->
                @if(Auth::user()->isStaff())
                    <li class="{{ Request::routeIs('submissions.*') ? 'active' : '' }}">
                        <a href="{{ route('submissions.index') }}">
                            <i class="bi bi-file-earmark-text"></i>
                            <span>Pengajuan Saya</span>
                        </a>
                    </li>
                @endif

                <!-- APPROVER MENUS (Supervisor, Manager, Director) -->
                @if(Auth::user()->isSupervisor() || Auth::user()->isManager() || Auth::user()->isDirector())
                    <li class="{{ Request::routeIs('approvals.*') ? 'active' : '' }}">
                        <a href="{{ route('approvals.index') }}">
                            <i class="bi bi-check-circle"></i>
                            <span>Approval Transaksi</span>
                        </a>
                    </li>
                @endif

                <!-- FINANCE MENUS -->
                @if(Auth::user()->isFinance())
                    <li class="{{ Request::routeIs('payments.*') ? 'active' : '' }}">
                        <a href="{{ route('payments.index') }}">
                            <i class="bi bi-cash-stack"></i>
                            <span>Validasi & Bayar</span>
                        </a>
                    </li>
                    <li class="{{ Request::routeIs('categories.*') ? 'active' : '' }}">
                        <a href="{{ route('categories.index') }}">
                            <i class="bi bi-tags"></i>
                            <span>Kategori Pengeluaran</span>
                        </a>
                    </li>
                    <li class="{{ Request::routeIs('budgets.*') ? 'active' : '' }}">
                        <a href="{{ route('budgets.index') }}">
                            <i class="bi bi-cash-coin"></i>
                            <span>Alokasi Anggaran</span>
                        </a>
                    </li>
                    <li class="{{ Request::routeIs('users.*') ? 'active' : '' }}">
                        <a href="{{ route('users.index') }}">
                            <i class="bi bi-people"></i>
                            <span>Pengguna Sistem</span>
                        </a>
                    </li>
                @endif

                <!-- REPORT MENUS (Finance, Director, Manager) -->
                @if(Auth::user()->isFinance() || Auth::user()->isDirector() || Auth::user()->isManager())
                    <li class="{{ Request::routeIs('reports.*') ? 'active' : '' }}">
                        <a href="{{ route('reports.index') }}">
                            <i class="bi bi-graph-up-arrow"></i>
                            <span>Laporan Pengeluaran</span>
                        </a>
                    </li>
                @endif
            </ul>

            <div class="sidebar-footer">
                <div class="d-flex align-items-center gap-2 text-muted small">
                    <i class="bi bi-shield-lock-fill text-success"></i>
                    <span>Role: <strong class="text-white">{{ Auth::user()->roles->first()->name ?? 'N/A' }}</strong></span>
                </div>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="main-content">
            <!-- Navbar -->
            <nav class="main-navbar">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-light d-lg-none" id="sidebarToggle">
                        <i class="bi bi-list fs-4"></i>
                    </button>
                    <h4 class="mb-0 fw-semibold text-dark">@yield('page-title', 'Dashboard')</h4>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <!-- Premium Dark Mode Toggle -->
                    <button class="btn btn-light rounded-circle p-2 d-flex align-items-center justify-content-center border-0 shadow-sm" id="themeToggle" style="width: 40px; height: 40px;" title="Ganti Tema">
                        <i class="bi bi-moon fs-5" id="themeIcon"></i>
                    </button>

                    <!-- User Menu Dropdown -->
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle navbar-user-dropdown" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="text-end d-none d-sm-block">
                                <div class="fw-semibold text-dark leading-none">{{ Auth::user()->name }}</div>
                                <small class="text-muted text-uppercase" style="font-size: 0.65rem; font-weight: 700;">{{ Auth::user()->roles->first()->name ?? 'N/A' }}</small>
                            </div>
                            <div class="stat-icon bg-secondary text-white rounded-circle fs-5" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="userMenu">
                            <li>
                                <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('profile.edit') }}">
                                    <i class="bi bi-person text-muted"></i>
                                    <span>Profil Saya</span>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger py-2 d-flex align-items-center gap-2 w-100 border-0 bg-transparent">
                                        <i class="bi bi-box-arrow-right"></i>
                                        <span>Keluar Aplikasi</span>
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Content Body -->
            <main class="content-body">
                <!-- Breadcrumbs -->
                @yield('breadcrumbs')

                <!-- Session Flash Messages (Notifications) -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2 mb-4" role="alert" style="border-radius: 10px;">
                        <i class="bi bi-check-circle-fill fs-5"></i>
                        <div>{{ session('success') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2 mb-4" role="alert" style="border-radius: 10px;">
                        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                        <div>{{ session('warning') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2 mb-4" role="alert" style="border-radius: 10px;">
                        <i class="bi bi-x-circle-fill fs-5"></i>
                        <div>{{ session('error') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Yield Page Content -->
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert2 (CDN for assessment convenience) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Sidebar Toggler & Dark Mode Toggle Script -->
    <script>
        // Set script-based theme before body loads to prevent visual flash
        (function() {
            if (localStorage.getItem('theme') === 'dark') {
                document.body.classList.add('dark-mode');
            }
        })();

        document.addEventListener('DOMContentLoaded', function () {
            // Sidebar toggle logic
            const toggleBtn = document.getElementById('sidebarToggle');
            const appWrapper = document.getElementById('appWrapper');
            
            if(toggleBtn) {
                toggleBtn.addEventListener('click', function () {
                    appWrapper.classList.toggle('sidebar-open');
                });
            }

            // Dark Mode toggle logic
            const themeToggleBtn = document.getElementById('themeToggle');
            const themeIcon = document.getElementById('themeIcon');
            const body = document.body;

            // Load theme on DOM ready
            if (localStorage.getItem('theme') === 'dark') {
                body.classList.add('dark-mode');
                if (themeIcon) {
                    themeIcon.classList.remove('bi-moon');
                    themeIcon.classList.add('bi-sun');
                }
            }

            if (themeToggleBtn) {
                themeToggleBtn.addEventListener('click', function () {
                    body.classList.toggle('dark-mode');
                    const isDark = body.classList.contains('dark-mode');
                    localStorage.setItem('theme', isDark ? 'dark' : 'light');

                    if (themeIcon) {
                        if (isDark) {
                            themeIcon.classList.remove('bi-moon');
                            themeIcon.classList.add('bi-sun');
                        } else {
                            themeIcon.classList.remove('bi-sun');
                            themeIcon.classList.add('bi-moon');
                        }
                    }
                });
            }
        });
    </script>
    
    @yield('scripts')
</body>
</html>
