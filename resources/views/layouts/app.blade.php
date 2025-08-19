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
        }
        
        .sidebar {
            min-height: 100vh;
            background: #0a2a6c;
            color: #fff;
            width: 250px;
            position: fixed;
            top: 0; 
            left: 0;
            z-index: 1000;
            transition: transform 0.3s ease;
            overflow-y: auto;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
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
            transition: margin-left 0.3s ease;
            min-height: 100vh;
        }
        
        .main-content.expanded {
            margin-left: 0;
        }
        
        .no-sidebar { 
            margin-left: 0 !important; 
            padding: 0 !important; 
        }
        
        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-250px);
            }
            
            .sidebar.mobile-open {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0 !important;
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
                z-index: 999;
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
    <button class="sidebar-toggle" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>
    
    <!-- Mobile Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <div class="sidebar" id="sidebar">
        <div class="logo">
            <img src="/img/logo-hki.png" alt="Logo HKI" class="img-fluid mb-2">
            <div class="logo-text">Sistem HKI</div>
        </div>
        <nav class="nav flex-column px-2 mt-3">
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
    <main class="main-content position-relative @guest no-sidebar w-100 p-0 @endguest" id="mainContent">
        @auth
        <div class="position-absolute top-0 end-0 mt-3 me-4 d-flex align-items-center gap-3" style="z-index:1100;">
            <!-- Simple Notification Bell -->
            <div class="dropdown">
                <a href="#" class="btn btn-light position-relative rounded-circle border-0 shadow-sm" id="notifDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-bell text-primary" style="font-size: 16px;"></i>
                    @if($unreadNotifications > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger text-white" style="font-size: 10px; padding: 3px 6px; min-width: 18px; height: 18px; display: flex; align-items: center; justify-content: center;">
                            {{ $unreadNotifications > 9 ? '9+' : $unreadNotifications }}
                        </span>
                    @endif
                </a>
                <ul class="dropdown-menu dropdown-menu-end bg-white border-0 shadow-lg" style="min-width: 320px; border-radius: 12px; margin-top: 8px;" aria-labelledby="notifDropdown">
                    <li class="px-3 py-3 border-bottom">
                        <h6 class="mb-1 fw-bold text-dark">Notifikasi</h6>
                        <small class="text-muted">{{ $unreadNotifications }} belum dibaca</small>
                    </li>
                    @forelse($latestNotifications as $notif)
                        <li>
                            <a href="{{ route('notifikasi.index') }}" class="dropdown-item py-3 {{ $notif->dibaca ? 'text-muted' : '' }}">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                            <i class="fas fa-info-circle text-primary" style="font-size: 14px;"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-medium text-dark" style="font-size: 0.9rem; line-height: 1.3;">
                                            {{ \Illuminate\Support\Str::limit($notif->judul ?? $notif->pesan, 45) }}
                                        </div>
                                        <small class="text-muted">{{ $notif->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @empty
                        <li>
                            <div class="dropdown-item text-center text-muted py-4">
                                <i class="fas fa-bell-slash mb-2" style="font-size: 2rem; opacity: 0.3;"></i>
                                <br>
                                <small>Tidak ada notifikasi</small>
                            </div>
                        </li>
                    @endforelse
                    <li><hr class="dropdown-divider my-0"></li>
                    <li>
                        <a class="dropdown-item text-center text-primary fw-medium py-3" href="{{ route('notifikasi.index') }}">
                            <i class="fas fa-eye me-1"></i> Lihat Semua
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Elegant User Profile -->
            <div class="dropdown">
                <a href="#" class="btn btn-light dropdown-toggle d-flex align-items-center border-0 shadow-sm rounded-pill px-3 py-2" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="font-weight: 500; font-size: 0.9rem; background: #f8f9fa;">
                    <div class="bg-primary rounded-circle me-2 d-flex align-items-center justify-content-center text-white fw-bold" style="width: 32px; height: 32px; font-size: 0.8rem;">
                        {{ strtoupper(substr(auth()->user()->nama_lengkap ?? auth()->user()->name, 0, 1)) }}
                    </div>
                    <span class="d-none d-lg-inline text-dark">{{ \Illuminate\Support\Str::limit(auth()->user()->nama_lengkap ?? auth()->user()->name, 15) }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end bg-white border-0 shadow-lg" style="border-radius: 12px; min-width: 220px; margin-top: 8px;" aria-labelledby="userDropdown">
                    <li class="px-3 py-3 border-bottom">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary rounded-circle me-3 d-flex align-items-center justify-content-center text-white fw-bold" style="width: 40px; height: 40px; font-size: 0.9rem;">
                                {{ strtoupper(substr(auth()->user()->nama_lengkap ?? auth()->user()->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-bold text-dark" style="font-size: 0.95rem; line-height: 1.2;">{{ auth()->user()->nama_lengkap ?? auth()->user()->name }}</div>
                            </div>
                        </div>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center py-3" href="{{ route('profile.edit') }}">
                            <div class="bg-secondary bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="fas fa-user-edit text-secondary" style="font-size: 14px;"></i>
                            </div>
                            <span class="text-dark">Profile</span>
                        </a>
                    </li>
                    <li><hr class="dropdown-divider my-0"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item d-flex align-items-center py-3 text-danger">
                                <div class="bg-danger bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="fas fa-sign-out-alt text-danger" style="font-size: 14px;"></i>
                                </div>
                                <span>Logout</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
        @endauth
        @if(session('success'))
            <div class="container">
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="container">
                <div class="alert alert-danger">
                    {{ session('error') }}
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
        });
    </script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: @json(session('success')),
                    confirmButtonText: 'OK'
                });
            @endif
            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: @json(session('error')),
                    confirmButtonText: 'OK'
                });
            @endif
        });
    </script>
    
    @stack('scripts')
</body>
</html>