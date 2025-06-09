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
            padding: 2rem 1rem 1rem 1rem;
            text-align: center;
            border-bottom: 1px solid #233366;
        }
        .sidebar .logo img {
            max-width: 160px;
            margin-bottom: 0.5rem;
        }
        .sidebar .nav-link {
            color: #fff;
            font-weight: 500;
            border-radius: 8px;
            margin: 0.25rem 0;
            padding: 0.75rem 1.5rem;
            transition: background 0.2s;
        }
        .sidebar .nav-link.active, .sidebar .nav-link:hover {
            background: #FFD600;
            color: #0a2a6c !important;
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
            .sidebar { width: 100%; min-height: auto; position: relative; }
            .main-content { margin-left: 0 !important; }
        }
        .main-content { margin-left: 250px; padding: 2rem 1rem 1rem 1rem; }
        .no-sidebar { margin-left: 0 !important; padding: 0 !important; }
    </style>
</head>
<body>
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
                <a href="{{ route('direktur.ttd.form') }}" class="nav-link{{ request()->routeIs('direktur.ttd.form') ? ' active' : '' }}">
                    <i class="fas fa-pen-nib me-2"></i> Upload Tanda Tangan
                </a>
                <a href="{{ route('persetujuan.index') }}" class="nav-link{{ request()->routeIs('persetujuan.index') ? ' active' : '' }}">
                    <i class="fas fa-check-circle me-2"></i> Persetujuan
                </a>
            @else
                <a href="{{ route('dashboard') }}" class="nav-link{{ request()->routeIs('dashboard') ? ' active' : '' }} dashboard-link">
                    <i class="fas fa-th-large me-2"></i> Dashboard
                </a>
                <div class="accordion bg-transparent border-0" id="sidebarAccordion">
                    <div class="accordion-item bg-transparent border-0">
                        <h2 class="accordion-header" id="headingHaki">
                            <button class="accordion-button bg-transparent px-0 py-2 text-white collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseHaki" aria-expanded="false" aria-controls="collapseHaki" style="box-shadow:none; font-weight:600;">
                                <i class="fas fa-copyright me-2"></i> Hak Cipta
                            </button>
                        </h2>
                        <div id="collapseHaki" class="accordion-collapse collapse{{ request()->is('pengajuan*') ? ' show' : '' }}" aria-labelledby="headingHaki" data-bs-parent="#sidebarAccordion">
                            <div class="accordion-body py-2 px-0">
                                <ul class="list-unstyled ms-4 mb-0">
                                    <li class="mb-2"><a href="{{ route('pengajuan.create') }}" class="text-white text-decoration-none" style="font-size:1rem;">&bull; Permohonan Baru</a></li>
                                    <li class="mb-2"><a href="{{ route('pengajuan.index') }}" class="text-white text-decoration-none" style="font-size:1rem;">&bull; Daftar Ciptaan</a></li>
                                    <li><a href="{{ route('draft.index') }}" class="text-white text-decoration-none{{ request()->routeIs('draft.index') ? ' active' : '' }}" style="font-size:1rem;">&bull; Daftar Ciptaan (Draft)</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </nav>
    </div>
    @endauth
    <main class="main-content position-relative @guest no-sidebar w-100 p-0 @endguest">
        @auth
        <!-- Profile Dropdown Atas Kanan -->
        <div class="position-absolute top-0 end-0 mt-3 me-4" style="z-index:1100;">
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
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @stack('scripts')
</body>
</html>