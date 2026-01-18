<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Visitor Management')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Figtree', sans-serif;
            background-color: #f8f9fa;
        }
        .navbar-brand {
            font-weight: 700;
            color: #4f46e5;
        }
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 70px 0 0;
            width: 250px;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .main-content {
            margin-left: 250px;
            padding-top: 70px;
        }
        .nav-item.active .nav-link {
            background-color: #eef2ff;
            color: #4f46e5;
            border-left: 4px solid #4f46e5;
        }
        .user-avatar {
            width: 35px;
            height: 35px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-weight: 600;
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Top Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top border-bottom">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <a class="navbar-brand d-none d-lg-block" href="{{ route('dashboard') }}">
                <i class="bi bi-building me-2"></i>
                Visitor System
            </a>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                            <div class="user-avatar me-2">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-bold">{{ Auth::user()->name }}</div>
                                <small class="text-muted">
                                    @switch(Auth::user()->role)
                                        @case('internal')
                                            <i class="bi bi-briefcase"></i> Internal
                                            @break
                                        @case('security')
                                            <i class="bi bi-shield-check"></i> Security
                                            @break
                                        @case('admin')
                                            <i class="bi bi-gear"></i> Admin
                                            @break
                                    @endswitch
                                </small>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            {{-- HAPUS/COMMENT BAGIAN INI --}}
                            {{-- 
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-person me-2"></i> Profil
                            </a>
                            <div class="dropdown-divider"></div>
                            --}}
                            
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                </button>
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar d-none d-lg-block">
        <div class="px-3 py-4">
            <!-- Role-based Sidebar -->
            @switch(Auth::user()->role)
                @case('internal')
                    @include('partials.sidebar-internal')
                @break
                @case('security')
                    @include('partials.sidebar-security')
                @break
                @case('admin')
                    @include('partials.sidebar-admin')
                @break
            @endswitch
        </div>
    </div>

    <!-- Mobile Sidebar (offcanvas) -->
    <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="mobileSidebar">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">
                <i class="bi bi-building me-2"></i> Menu
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            @switch(Auth::user()->role)
                @case('internal')
                    @include('partials.sidebar-internal')
                @break
                @case('security')
                    @include('partials.sidebar-security')
                @break
                @case('admin')
                    @include('partials.sidebar-admin')
                @break
            @endswitch
        </div>
    </div>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container-fluid p-4">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-2">@yield('title')</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            @yield('breadcrumb')
                        </ol>
                    </nav>
                </div>
                
                <!-- Mobile Menu Toggle -->
                <button class="btn btn-outline-primary d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
                    <i class="bi bi-list"></i> Menu
                </button>
                
                <!-- Action Buttons -->
                <div class="d-none d-lg-block">
                    @yield('actions')
                </div>
            </div>

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Main Content -->
            @yield('content')
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Scripts -->
    <script>
        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });

        // Mark active menu item
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname;
            document.querySelectorAll('.nav-link').forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    link.parentElement.classList.add('active');
                }
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>