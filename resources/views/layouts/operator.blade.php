<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Operator Dashboard') - Training Center Part Production</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
</head>
<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header d-flex justify-content-center align-items-center flex-column py-3">
            <a href="{{ route('operator.dashboard') }}" class="sidebar-brand d-flex flex-column align-items-center text-center text-decoration-none">
                <img src="{{ asset('img/logo.jpeg') }}" alt="DNP Logo" class="mb-2" style="height: 60px; width: 60px; border-radius: 50%; object-fit: cover;"> 
                <span class="fs-6 fw-bold text-white text-wrap" style="line-height: 1.2;">TRAINING CENTER<br>PART PRODUCTION</span>
            </a>
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="{{ route('operator.dashboard') }}" class="{{ request()->routeIs('operator.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('operator.attendance.create') }}" class="{{ request()->routeIs('operator.attendance.create') ? 'active' : '' }}">
                    <i class="fas fa-clock"></i> Absensi
                </a>
            </li>
            <li>
                <a href="{{ route('operator.attendance.index') }}" class="{{ request()->routeIs('operator.attendance.index') ? 'active' : '' }}">
                    <i class="fas fa-history"></i> Riwayat Absensi
                </a>
            </li>
            <li>
                <a href="{{ route('operator.evaluation.start') }}" class="{{ request()->routeIs('operator.evaluation.start') ? 'active' : '' }}">
                    <i class="fas fa-pen-alt"></i> Ujian Online
                </a>
            </li>
            <li>
                <a href="{{ route('operator.evaluation.results') }}" class="{{ request()->routeIs('operator.evaluation.results') ? 'active' : '' }}">
                    <i class="fas fa-history"></i> Riwayat Ujian
                </a>
            </li>
            <li>
                <a href="{{ route('operator.materials.index') }}" class="{{ request()->routeIs('operator.materials*') ? 'active' : '' }}">
                    <i class="fas fa-book"></i> Materi Pembelajaran
                </a>
            </li>

        </ul>
    </nav>

    <div class="main-content" id="mainContent">
        <header class="topbar">
            <div class="d-flex align-items-center">
                <button class="toggle-sidebar" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="topbar-title">
                    @yield('title')
                </div>
            </div>
            <div class="dropdown ms-auto">
                <div class="user-profile" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="user-info d-none d-md-block">
                        <span class="user-name">{{ auth()->user()->name ?? 'User' }}</span>
                        <span class="role-badge">Operator</span>
                    </div>
                    <div class="user-avatar">
                        @if(auth()->user()->photo)
                            <img src="{{ asset('storage/' . auth()->user()->photo) }}" alt="Operator" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'User') }}&background=C62828&color=fff" alt="Operator" class="rounded-circle" width="40">
                        @endif
                    </div>
                </div>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg animate slideIn">
                    <li class="px-3 py-2">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                @if(auth()->user()->photo)
                                    <img src="{{ asset('storage/' . auth()->user()->photo) }}" alt="User" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'User') }}&background=C62828&color=fff" alt="User" class="rounded-circle" width="40">
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 text-dark">{{ auth()->user()->name ?? 'User' }}</h6>
                                <small class="text-muted">Operator</small>
                            </div>
                        </div>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item py-2" href="{{ route('operator.profile.edit') }}">
                            <i class="fas fa-user me-2 text-muted"></i> Profile
                        </a>
                    </li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item py-2 text-danger">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </header>

        <div class="container-fluid p-0">
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const mainContent = document.getElementById('mainContent');

            function toggleSidebar() {
                if (window.innerWidth > 768) {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('collapsed');
                } else {
                    sidebar.classList.toggle('active');
                    sidebarOverlay.classList.toggle('active');
                }
            }

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    toggleSidebar();
                });
            }

            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    toggleSidebar();
                });
            }
        });
    </script>
</body>
</html>