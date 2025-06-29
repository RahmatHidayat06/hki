<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pengajuan HKI</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Tailwind CSS - Minimal custom utility classes -->
    <style>
        /* Tailwind-like utility classes for specific needs */
        .hidden { display: none !important; }
        .block { display: block !important; }
        .flex { display: flex !important; }
        .items-center { align-items: center !important; }
        .justify-between { justify-content: space-between !important; }
        .space-x-2 > * + * { margin-left: 0.5rem !important; }
        .space-x-3 > * + * { margin-left: 0.75rem !important; }
        .space-y-2 > * + * { margin-top: 0.5rem !important; }
        .space-y-3 > * + * { margin-top: 0.75rem !important; }
        .w-full { width: 100% !important; }
        .flex-1 { flex: 1 1 0% !important; }
        .text-xs { font-size: 0.75rem !important; }
        .text-sm { font-size: 0.875rem !important; }
        .font-medium { font-weight: 500 !important; }
        .font-semibold { font-weight: 600 !important; }
        .font-bold { font-weight: 700 !important; }
        .rounded-lg { border-radius: 0.5rem !important; }
        .rounded-xl { border-radius: 0.75rem !important; }
        .shadow-md { box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1) !important; }
        .shadow-lg { box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1) !important; }
        .transition-all { transition: all 0.15s ease-in-out !important; }
        .animate-spin { animation: spin 1s linear infinite !important; }
        @keyframes spin { to { transform: rotate(360deg); } }
        
        /* Color utilities */
        .bg-blue-50 { background-color: #eff6ff !important; }
        .bg-green-100 { background-color: #dcfce7 !important; }
        .bg-green-500 { background-color: #22c55e !important; }
        .bg-green-600 { background-color: #16a34a !important; }
        .bg-green-700 { background-color: #15803d !important; }
        .bg-red-100 { background-color: #fee2e2 !important; }
        .bg-red-500 { background-color: #ef4444 !important; }
        .bg-orange-600 { background-color: #ea580c !important; }
        .bg-orange-700 { background-color: #c2410c !important; }
        .bg-blue-600 { background-color: #2563eb !important; }
        .bg-blue-700 { background-color: #1d4ed8 !important; }
        .bg-gray-400 { background-color: #9ca3af !important; }
        .bg-gray-500 { background-color: #6b7280 !important; }
        .text-white { color: #ffffff !important; }
        .text-blue-700 { color: #1d4ed8 !important; }
        .text-green-700 { color: #15803d !important; }
        .text-amber-600 { color: #d97706 !important; }
        .border-gray-300 { border-color: #d1d5db !important; }
        .border-amber-200 { border-color: #fde68a !important; }
        
        /* Hover states */
        .hover\:bg-green-700:hover { background-color: #15803d !important; }
        .hover\:bg-orange-700:hover { background-color: #c2410c !important; }
        .hover\:bg-blue-700:hover { background-color: #1d4ed8 !important; }
        .hover\:bg-gray-500:hover { background-color: #6b7280 !important; }
        .hover\:shadow-lg:hover { box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1) !important; }
        
        /* Spacing utilities */
        .p-3 { padding: 0.75rem !important; }
        .p-4 { padding: 1rem !important; }
        .p-6 { padding: 1.5rem !important; }
        .px-3 { padding-left: 0.75rem !important; padding-right: 0.75rem !important; }
        .px-4 { padding-left: 1rem !important; padding-right: 1rem !important; }
        .py-1\.5 { padding-top: 0.375rem !important; padding-bottom: 0.375rem !important; }
        .py-2 { padding-top: 0.5rem !important; padding-bottom: 0.5rem !important; }
        .mt-1 { margin-top: 0.25rem !important; }
        .mt-2 { margin-top: 0.5rem !important; }
        .mt-3 { margin-top: 0.75rem !important; }
        .mt-4 { margin-top: 1rem !important; }
        .mb-2 { margin-bottom: 0.5rem !important; }
        .mr-1 { margin-right: 0.25rem !important; }
        .mr-2 { margin-right: 0.5rem !important; }
        
        /* Layout utilities */
        .fixed { position: fixed !important; }
        .top-4 { top: 1rem !important; }
        .right-4 { right: 1rem !important; }
        .z-50 { z-index: 50 !important; }
        .grid { display: grid !important; }
        .grid-cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)) !important; }
        .grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
        .gap-4 { gap: 1rem !important; }
        .gap-6 { gap: 1.5rem !important; }
        
        /* Responsive utilities */
        @media (min-width: 768px) {
            .md\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
        }
        
        /* Animation utilities */
        .fade-in {
            animation: fadeIn 0.3s ease-in-out !important;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Global Table Layout Fixes untuk nama yang panjang */
        .table .col-nama-pencipta,
        .table .col-nama,
        .nama-pencipta,
        .nama-user {
            word-wrap: break-word !important;
            word-break: break-word !important;
            white-space: normal !important;
            line-height: 1.3 !important;
            max-height: 3.9em !important;
            overflow: hidden !important;
            display: -webkit-box !important;
            -webkit-line-clamp: 3 !important;
            -webkit-box-orient: vertical !important;
        }
        
        /* Pembatasan lebar kolom untuk nama */
        .table th:has(+ th:contains("Nama")),
        .table td.col-nama,
        .table td:nth-child(3):has(.nama-pencipta) {
            max-width: 150px !important;
            min-width: 120px !important;
        }

        /* Enhanced Sidebar Styles */
        body { 
            background: #f8f9fa; 
            transition: margin-left 0.3s ease;
            padding-top: 90px; /* Compact header height */
        }
        
        .sidebar {
            min-height: 100vh;
            background: #0a2a6c;
            color: #fff;
            width: 250px;
            position: fixed;
            top: 90px; /* Position below header */
            left: 0;
            z-index: 999; /* Below header but above content */
            transition: transform 0.3s ease;
            overflow-y: auto;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            height: calc(100vh - 90px); /* Adjust height for header */
        }
        
        .sidebar.collapsed {
            transform: translateX(-250px);
        }
        
        .sidebar-toggle {
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1100;
            background: #0a2a6c;
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            transition: background 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }
        
        .sidebar-toggle:hover {
            background: #1e3a8a;
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
        }
        
        .sidebar .logo {
            padding: 20px 15px;
            text-align: center;
            border-bottom: 1px solid #233366;
            background: #233366;
        }
        
        .sidebar .logo img {
            max-width: 60px;
            margin-bottom: 10px;
        }
        
        .sidebar .logo-text {
            font-size: 16px;
            font-weight: 600;
            color: #FFD600;
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            border-radius: 0;
            margin: 0;
            padding: 12px 20px;
            transition: all 0.3s ease;
            text-decoration: none !important;
            display: flex !important;
            align-items: center;
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
            border-left: 3px solid transparent;
        }
        
        .sidebar .nav-link:hover {
            background: #233366 !important;
            color: #fff !important;
            border-left-color: #FFD600;
        }
        
        .sidebar .nav-link.active {
            background: #FFD600 !important;
            color: #0a2a6c !important;
            border-left-color: #FFC107;
        }
        
        .sidebar .nav-link i {
            font-size: 16px;
            width: 20px;
            text-align: center;
            margin-right: 10px;
        }
        
        .sidebar .nav-text {
            white-space: nowrap;
        }
        
        .sidebar .nav-sub-link {
            color: rgba(255, 255, 255, 0.8) !important;
            font-weight: 400;
            margin: 0 !important;
            padding: 10px 20px 10px 50px !important;
            font-size: 14px !important;
            background: rgba(0, 0, 0, 0.1);
            border-left: 3px solid transparent;
        }
        
        .sidebar .nav-sub-link:hover {
            background: rgba(255, 214, 0, 0.1) !important;
            color: #FFD600 !important;
            border-left-color: #FFD600;
        }
        
        .sidebar .nav-sub-link.active {
            background: rgba(255, 214, 0, 0.2) !important;
            color: #FFD600 !important;
            border-left-color: #FFD600;
        }
        
        .sidebar .nav-accordion-toggle {
            cursor: pointer !important;
            user-select: none !important;
        }
        
        .sidebar .nav-accordion-toggle[data-expanded="true"] .nav-chevron {
            transform: rotate(180deg) !important;
        }
        
        .sidebar .nav-chevron {
            transition: transform 0.3s ease;
            margin-left: auto;
        }
        
        .sidebar .nav-submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
        
        .sidebar .nav-submenu.show {
            max-height: 200px;
        }
        
        .main-content { 
            margin-left: 250px; 
            padding: 20px; 
            padding-top: 0; /* Remove top padding since body has padding-top */
            transition: margin-left 0.3s ease;
            min-height: calc(100vh - 90px); /* Adjust for header */
        }
        
        .main-content.expanded {
            margin-left: 0;
        }
        
        .no-sidebar { 
            margin-left: 0 !important; 
            padding: 20px !important; 
        }
        
        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            body {
                padding-top: 80px; /* Smaller header on mobile */
            }
            
            .sidebar {
                top: 80px;
                height: calc(100vh - 80px);
                transform: translateX(-250px);
            }
            
            .sidebar.mobile-open {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0 !important;
                min-height: calc(100vh - 80px);
            }
            
            .sidebar-toggle {
                left: 15px;
            }
            
            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 998;
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
            }
            
            .sidebar-overlay.show {
                opacity: 1;
                visibility: visible;
            }
        }
        
        /* Scrollbar Styling */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: #233366;
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: #FFD600;
            border-radius: 3px;
        }
        
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: #FFC107;
        }

        /* Remove duplicate header elements from main content */
        .main-content .position-absolute {
            display: none !important;
        }

        /* Remove padding fix since header is sticky inside flow */
        .main-content {
            padding-top: 1rem;
        }

        /* === Global Header & Sidebar Alignment === */
        :root {
            --header-height: 60px; /* adjust if header taller */
        }
        .app-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: var(--header-height);
            z-index: 1050;
        }
        .sidebar {
            top: var(--header-height);
            height: calc(100% - var(--header-height));
        }
        .sidebar-overlay {
            top: var(--header-height);
        }
        .main-content.has-sidebar {
            padding-top: var(--header-height);
        }
        
        .modern-header .container-fluid {
            padding-left: 0;
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    @php
        $unreadNotifications = auth()->check() ? \App\Models\Notifikasi::where('user_id', auth()->id())->where('dibaca', false)->count() : 0;
        $latestNotifications = auth()->check() ? \App\Models\Notifikasi::where('user_id', auth()->id())->latest()->take(5)->get() : collect();
    @endphp
    
    @auth
    <!-- Sidebar Toggle Button -->
    <button class="sidebar-toggle d-none" id="sidebarToggleOld">
        <i class="fas fa-bars"></i>
    </button>
    
    <!-- Mobile Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <header class="app-header bg-white border-bottom shadow-sm">
        <div class="container-fluid px-3 d-flex align-items-center justify-content-between">
            <!-- Left Side -->
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('dashboard') }}" class="d-flex align-items-center text-decoration-none text-dark gap-2">
                    <img src="/img/logo-hki.png" alt="Logo HKI" style="height:32px;width:auto">
                    <span class="fw-bold d-none d-md-inline">Sistem Pengajuan HKI</span>
                </a>
                <button id="sidebarToggle" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <!-- Right Side -->
            <div class="d-flex align-items-center gap-3">
                <div class="d-none d-lg-block text-end">
                    <div id="header-time" class="text-primary fw-semibold small">--:--:--</div>
                    <div id="header-date" class="text-muted small">Loading...</div>
                </div>
                
                @php
                    $unreadCount = auth()->check() ? \App\Models\Notifikasi::where('user_id', auth()->id())->where('dibaca', false)->count() : 0;
                @endphp
                <!-- Notifications Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-sm btn-light position-relative rounded-circle border-0" 
                            id="notifDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell text-secondary"></i>
                        @if($unreadCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.6rem;">
                                {{ $unreadCount }}
                            </span>
                        @endif
                    </button>
                    <div class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" style="min-width: 300px;">
                        <!-- Notification content will be loaded here via JS or livewire for better performance -->
                        <div class="p-3 text-center text-muted">Notifikasi akan muncul di sini.</div>
                    </div>
                </div>

                <!-- User Profile Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-sm btn-light dropdown-toggle border-0 rounded-pill px-2 py-1" 
                            id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary rounded-circle me-2 d-flex align-items-center justify-content-center text-white fw-bold" 
                                 style="width: 28px; height: 28px; font-size: 0.75rem;">
                                {{ strtoupper(substr(auth()->user()->nama_lengkap ?? auth()->user()->name ?? 'U', 0, 1)) }}
                            </div>
                            <span class="d-none d-md-inline text-dark fw-medium" style="font-size: 0.85rem;">
                                {{ \Illuminate\Support\Str::limit(auth()->user()->nama_lengkap ?? auth()->user()->name ?? 'User', 12) }}
                            </span>
                        </div>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" style="min-width: 200px;">
                        <div class="dropdown-header">
                            <div class="fw-bold">{{ auth()->user()->nama_lengkap ?? auth()->user()->name ?? 'User' }}</div>
                            <small class="text-muted text-capitalize">{{ auth()->user()->role ?? 'User' }}</small>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item py-2" href="{{ route('profile.edit') }}"><i class="fas fa-user-edit me-2 text-secondary"></i> Profile</a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item py-2 text-danger"><i class="fas fa-sign-out-alt me-2"></i> Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>
    
    <div class="sidebar" id="sidebar">
        <nav class="nav flex-column px-2 pt-3">
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="nav-link{{ request()->routeIs('admin.dashboard') ? ' active' : '' }} dashboard-link">
                    <i class="fas fa-th-large"></i>
                    <span class="nav-text">Dashboard Admin</span>
                </a>
                <a href="{{ route('admin.pengajuan') }}" class="nav-link{{ request()->routeIs('admin.pengajuan') ? ' active' : '' }}">
                    <i class="fas fa-list"></i>
                    <span class="nav-text">Daftar Pengajuan</span>
                </a>
                <a href="{{ route('admin.rekap') }}" class="nav-link{{ request()->routeIs('admin.rekap') ? ' active' : '' }}">
                    <i class="fas fa-file-excel"></i>
                    <span class="nav-text">Rekap Data</span>
                </a>
            @elseif(auth()->user()->role === 'direktur')
                <a href="{{ route('dashboard') }}" class="nav-link{{ request()->routeIs('dashboard') ? ' active' : '' }} dashboard-link">
                    <i class="fas fa-th-large"></i>
                    <span class="nav-text">Dashboard Direktur</span>
                </a>
                <a href="{{ route('persetujuan.index') }}" class="nav-link{{ request()->routeIs('persetujuan.index') ? ' active' : '' }}">
                    <i class="fas fa-check-circle"></i>
                    <span class="nav-text">Persetujuan</span>
                </a>
            @else
                <a href="{{ route('dashboard') }}" class="nav-link{{ request()->routeIs('dashboard') ? ' active' : '' }} dashboard-link">
                    <i class="fas fa-th-large"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
                
                <!-- Enhanced Menu for Dosen/Mahasiswa -->
                <div class="nav-section mt-2">
                    <div class="nav-accordion-toggle nav-link d-flex align-items-center justify-content-between{{ request()->is('pengajuan*') || request()->is('draft*') || request()->routeIs('pengajuan.*') || request()->routeIs('draft.*') ? ' active' : '' }}" 
                         data-target="#hakiSubmenu" 
                         data-expanded="{{ request()->is('pengajuan*') || request()->is('draft*') || request()->routeIs('pengajuan.*') || request()->routeIs('draft.*') ? 'true' : 'false' }}">
                        <span class="d-flex align-items-center">
                            <i class="fas fa-copyright"></i>
                            <span class="nav-text">Hak Cipta</span>
                        </span>
                        <i class="fas fa-chevron-down nav-chevron" style="font-size: 0.75rem;"></i>
                    </div>
                    
                    <div class="nav-submenu{{ request()->is('pengajuan*') || request()->is('draft*') || request()->routeIs('pengajuan.*') || request()->routeIs('draft.*') ? ' show' : '' }}" id="hakiSubmenu">
                        <div class="ms-3 mt-2">
                            <a href="{{ route('pengajuan.create') }}" class="nav-link nav-sub-link{{ request()->routeIs('pengajuan.create') ? ' active' : '' }}">
                                <i class="fas fa-plus-circle me-2" style="font-size: 0.8rem;"></i> Permohonan Baru
                            </a>
                            <a href="{{ route('pengajuan.index') }}" class="nav-link nav-sub-link{{ request()->routeIs('pengajuan.index') ? ' active' : '' }}">
                                <i class="fas fa-list me-2" style="font-size: 0.8rem;"></i> Daftar Ciptaan
                            </a>
                            <a href="{{ route('draft.index') }}" class="nav-link nav-sub-link{{ request()->routeIs('draft.index') ? ' active' : '' }}">
                                <i class="fas fa-file-alt me-2" style="font-size: 0.8rem;"></i> Daftar Ciptaan (Draft)
                            </a>
                        </div>
                    </div>
                </div>
                <!-- Pembayaran Link -->
                <a href="{{ route('pembayaran.index') }}" class="nav-link{{ request()->routeIs('pembayaran.*') ? ' active' : '' }}">
                    <i class="fas fa-wallet"></i>
                    <span class="nav-text">Pembayaran</span>
                </a>
            @endif
        </nav>
    </div>
    @endauth
    
    <main class="main-content @auth has-sidebar @endauth position-relative @guest no-sidebar w-100 p-0 @endguest" id="mainContent">
        @if(session('success'))
            <div class="container">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="container">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif
        @yield('content')
    </main>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS (Bundle includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Enhanced Sidebar Functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            
            // Sidebar functionality
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const mainContent = document.getElementById('mainContent');
            let isMobile = window.innerWidth <= 768;
            let sidebarOpen = !isMobile; // Default open on desktop, closed on mobile
            
            // Initialize sidebar state
            function initializeSidebar() {
                if (isMobile) {
                    sidebar.classList.add('collapsed');
                    mainContent.classList.add('expanded');
                    sidebarOpen = false;
                } else {
                    sidebar.classList.remove('collapsed');
                    mainContent.classList.remove('expanded');
                    sidebarOpen = true;
                }
            }
            
            // Toggle sidebar
            function toggleSidebar() {
                if (isMobile) {
                    // Mobile behavior - overlay
                    if (sidebarOpen) {
                        sidebar.classList.remove('mobile-open');
                        sidebarOverlay.classList.remove('show');
                        document.body.style.overflow = '';
                        sidebarOpen = false;
                    } else {
                        sidebar.classList.add('mobile-open');
                        sidebarOverlay.classList.add('show');
                        document.body.style.overflow = 'hidden';
                        sidebarOpen = true;
                    }
                } else {
                    // Desktop behavior - slide
                    if (sidebarOpen) {
                        sidebar.classList.add('collapsed');
                        mainContent.classList.add('expanded');
                        sidebarOpen = false;
                    } else {
                        sidebar.classList.remove('collapsed');
                        mainContent.classList.remove('expanded');
                        sidebarOpen = true;
                    }
                }
            }
            
            // Event listeners
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', toggleSidebar);
            }
            
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    if (isMobile && sidebarOpen) {
                        toggleSidebar();
                    }
                });
            }
            
            // Handle window resize
            window.addEventListener('resize', function() {
                const wasMobile = isMobile;
                isMobile = window.innerWidth <= 768;
                
                if (wasMobile !== isMobile) {
                    // Reset sidebar state when switching between mobile/desktop
                    sidebar.classList.remove('mobile-open');
                    sidebarOverlay.classList.remove('show');
                    document.body.style.overflow = '';
                    initializeSidebar();
                }
            });
            
            // Accordion functionality
            const accordionToggles = document.querySelectorAll('.nav-accordion-toggle');
            
            accordionToggles.forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const targetId = this.getAttribute('data-target');
                    const target = document.querySelector(targetId);
                    const chevron = this.querySelector('.nav-chevron');
                    const isExpanded = this.getAttribute('data-expanded') === 'true';
                    
                    if (target) {
                        if (isExpanded) {
                            target.classList.remove('show');
                            this.setAttribute('data-expanded', 'false');
                            if (chevron) {
                                chevron.style.transform = 'rotate(0deg)';
                            }
                        } else {
                            target.classList.add('show');
                            this.setAttribute('data-expanded', 'true');
                            if (chevron) {
                                chevron.style.transform = 'rotate(180deg)';
                            }
                        }
                    }
                });
                
                // Initialize chevron state
                const chevron = toggle.querySelector('.nav-chevron');
                const isExpanded = toggle.getAttribute('data-expanded') === 'true';
                if (chevron && isExpanded) {
                    chevron.style.transform = 'rotate(180deg)';
                }
            });
            
            // Close mobile sidebar with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && isMobile && sidebarOpen) {
                    toggleSidebar();
                }
            });
            
            // Initialize on page load
            initializeSidebar();
            
            // Auto-close alerts after 5 seconds
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert.show');
                alerts.forEach(alert => {
                    const closeBtn = alert.querySelector('.btn-close');
                    if (closeBtn) {
                        closeBtn.click();
                    }
                });
            }, 5000);
        });
    </script>
    
    @stack('scripts')
</body>
</html>