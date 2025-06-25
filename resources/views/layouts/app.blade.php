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
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { background: #f8f9fa; }
        .sidebar {
            min-height: 100vh;
            background: #0a2a6c;
            color: #fff;
            width: 250px;
            position: fixed;
            top: 0; left: 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }
        .sidebar .logo {
            padding: 1rem 0.75rem 0.75rem 0.75rem;
            text-align: center;
            border-bottom: 1px solid #233366;
        }
        .sidebar .logo img {
            max-width: 100px;
            margin-bottom: 0.25rem;
        }
        .sidebar .nav-link {
            color: #fff !important;
            font-weight: 500;
            border-radius: 8px;
            margin: 0.25rem 0;
            padding: 0.75rem 1.5rem;
            transition: all 0.2s ease;
            text-decoration: none !important;
            display: block !important;
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
        }
        .sidebar .nav-link.active, .sidebar .nav-link:hover {
            background: #FFD600 !important;
            color: #0a2a6c !important;
        }
        .sidebar .nav-link:focus {
            background: #FFD600 !important;
            color: #0a2a6c !important;
            box-shadow: none !important;
            outline: none !important;
        }
        .sidebar .nav-sub-link {
            color: rgba(255, 255, 255, 0.8) !important;
            font-weight: 400;
            border-radius: 8px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 3px solid transparent;
            margin: 0.125rem 0.75rem !important;
            padding: 0.625rem 1rem !important;
            font-size: 0.875rem !important;
            position: relative;
        }
        .sidebar .nav-sub-link:hover {
            background: rgba(255, 214, 0, 0.1) !important;
            color: #FFD600 !important;
            border-left-color: #FFD600;
            transform: translateX(4px);
        }
        .sidebar .nav-sub-link.active {
            background: rgba(255, 214, 0, 0.2) !important;
            color: #FFD600 !important;
            border-left-color: #FFD600;
            font-weight: 500;
        }
        .sidebar .nav-accordion-toggle {
            cursor: pointer !important;
            user-select: none !important;
        }
        .sidebar .nav-accordion-toggle[data-expanded="true"] .nav-chevron {
            transform: rotate(180deg) !important;
        }
        .sidebar .nav-submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
        .sidebar .nav-submenu.show {
            max-height: 200px;
        }
        .sidebar .sidebar-footer {
            margin-top: auto;
            padding: 1rem;
            border-top: 1px solid #233366;
            text-align: center;
        }
        .sidebar .sidebar-footer .dropdown-menu {
            left: auto;
            right: 0;
        }
        @media (max-width: 991.98px) {
            .sidebar { 
                width: 100%; 
                min-height: auto; 
                position: relative; 
            }
            .main-content { 
                margin-left: 0 !important; 
            }
        }
        .main-content { margin-left: 250px; padding: 2rem 1rem 1rem 1rem; }
        .no-sidebar { margin-left: 0 !important; padding: 0 !important; }
    </style>
</head>
<body>
    @php
        $unreadNotifications = auth()->check() ? \App\Models\Notifikasi::where('user_id', auth()->id())->where('dibaca', false)->count() : 0;
        $latestNotifications = auth()->check() ? \App\Models\Notifikasi::where('user_id', auth()->id())->latest()->take(5)->get() : collect();
    @endphp
    @auth
    <div class="sidebar">
        <div class="logo">
            <img src="/img/logo-hki.png" alt="Logo HKI" class="img-fluid mb-2">
        </div>
        <nav class="nav flex-column px-2 mt-3">
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="nav-link{{ request()->routeIs('admin.dashboard') ? ' active' : '' }} dashboard-link">
                    <i class="fas fa-th-large me-2"></i> Dashboard Admin
                </a>
                <a href="{{ route('admin.pengajuan') }}" class="nav-link{{ request()->routeIs('admin.pengajuan') ? ' active' : '' }}">
                    <i class="fas fa-list me-2"></i> Daftar Pengajuan
                </a>
                <a href="{{ route('admin.rekap') }}" class="nav-link{{ request()->routeIs('admin.rekap') ? ' active' : '' }}">
                    <i class="fas fa-file-excel me-2"></i> Rekap Data
                </a>
            @elseif(auth()->user()->role === 'direktur')
                <a href="{{ route('dashboard') }}" class="nav-link{{ request()->routeIs('dashboard') ? ' active' : '' }} dashboard-link">
                    <i class="fas fa-th-large me-2"></i> Dashboard Direktur
                </a>
                <a href="{{ route('persetujuan.index') }}" class="nav-link{{ request()->routeIs('persetujuan.index') ? ' active' : '' }}">
                    <i class="fas fa-check-circle me-2"></i> Persetujuan
                </a>
            @else
                <a href="{{ route('dashboard') }}" class="nav-link{{ request()->routeIs('dashboard') ? ' active' : '' }} dashboard-link">
                    <i class="fas fa-th-large me-2"></i> Dashboard
                </a>
                
                <!-- Enhanced Menu for Dosen/Mahasiswa -->
                <div class="nav-section mt-2">
                    <div class="nav-accordion-toggle nav-link d-flex align-items-center justify-content-between{{ request()->is('pengajuan*') || request()->is('draft*') || request()->routeIs('pengajuan.*') || request()->routeIs('draft.*') ? ' active' : '' }}" 
                         data-target="#hakiSubmenu" 
                         data-expanded="{{ request()->is('pengajuan*') || request()->is('draft*') || request()->routeIs('pengajuan.*') || request()->routeIs('draft.*') ? 'true' : 'false' }}"
                         style="cursor: pointer; user-select: none;">
                        <span>
                            <i class="fas fa-copyright me-2"></i> Hak Cipta
                        </span>
                        <i class="fas fa-chevron-down nav-chevron" style="font-size: 0.75rem; transition: transform 0.3s ease; opacity: 0.8;"></i>
                    </div>
                    
                    <div class="nav-submenu{{ request()->is('pengajuan*') || request()->is('draft*') || request()->routeIs('pengajuan.*') || request()->routeIs('draft.*') ? ' show' : '' }}" id="hakiSubmenu" style="overflow: hidden; transition: max-height 0.3s ease;">
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
                    <i class="fas fa-wallet me-2"></i> Pembayaran
                </a>
            @endif
        </nav>
    </div>
    @endauth
    <main class="main-content position-relative @guest no-sidebar w-100 p-0 @endguest">
        @auth
        <div class="position-absolute top-0 end-0 mt-3 me-5 d-flex align-items-center gap-2" style="z-index:1100;">
            <!-- Notification Dropdown -->
            <div class="dropdown">
                <a href="#" class="btn btn-light position-relative" id="notifDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-bell"></i>
                    @if($unreadNotifications > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ $unreadNotifications }}
                        </span>
                    @endif
                </a>
                <ul class="dropdown-menu dropdown-menu-end bg-light border-0 shadow-sm" style="min-width: 300px;" aria-labelledby="notifDropdown">
                    @forelse($latestNotifications as $notif)
                        <li>
                            <a href="{{ route('notifikasi.index') }}" class="dropdown-item small {{ $notif->dibaca ? 'text-muted' : '' }}">
                                {{ \Illuminate\Support\Str::limit($notif->judul ?? $notif->pesan, 50) }}
                                <br>
                                <small class="text-muted">{{ $notif->created_at->diffForHumans() }}</small>
                            </a>
                        </li>
                    @empty
                        <li><span class="dropdown-item text-muted">Tidak ada notifikasi</span></li>
                    @endforelse
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-center" href="{{ route('notifikasi.index') }}">Lihat Semua</a></li>
                </ul>
            </div>

            <!-- Profile Dropdown -->
            <div class="dropdown">
                <a href="#" class="btn btn-light dropdown-toggle d-flex align-items-center" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="font-weight:600;">
                    <i class="fas fa-user-circle me-2"></i> {{ auth()->user()->nama_lengkap ?? auth()->user()->name }}
                </a>
                <ul class="dropdown-menu dropdown-menu-end bg-light border-0 shadow-sm" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">Logout</button>
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
            // Custom accordion functionality for sidebar
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
                            // Collapse
                            target.classList.remove('show');
                            this.setAttribute('data-expanded', 'false');
                            if (chevron) {
                                chevron.style.transform = 'rotate(0deg)';
                            }
                        } else {
                            // Expand
                            target.classList.add('show');
                            this.setAttribute('data-expanded', 'true');
                            if (chevron) {
                                chevron.style.transform = 'rotate(180deg)';
                            }
                        }
                    }
                });
                
                // Initialize chevron state on page load
                const chevron = toggle.querySelector('.nav-chevron');
                const isExpanded = toggle.getAttribute('data-expanded') === 'true';
                if (chevron && isExpanded) {
                    chevron.style.transform = 'rotate(180deg)';
                }
            });
            
            // Add hover effects for better UX
            const subLinks = document.querySelectorAll('.nav-sub-link');
            subLinks.forEach(link => {
                link.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateX(4px)';
                });
                
                link.addEventListener('mouseleave', function() {
                    if (!this.classList.contains('active')) {
                        this.style.transform = 'translateX(0)';
                    }
                });
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>