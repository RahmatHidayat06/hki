@props(['title', 'description', 'icon' => 'fas fa-file', 'breadcrumbs' => []])

<!-- Modern Header Section -->
<div class="modern-header bg-white border-bottom shadow-sm position-sticky top-0 w-100" style="z-index:1050;">
    <div class="container-fluid px-3">
        <div class="d-flex align-items-center justify-content-between py-2">
            <div class="d-flex align-items-center gap-2">
                <img src="/img/logo-hki.png" alt="Logo HKI" style="height:36px;width:auto" class="me-2">
                <span class="fw-bold text-dark" style="font-size:1.05rem">Sistem Pengajuan HKI</span>
                <button id="sidebarToggle" class="btn btn-sm btn-outline-primary ms-2">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="d-none d-lg-block text-end me-2">
                    <div id="header-time" class="text-primary fw-semibold small">--:--:--</div>
                    <div id="header-date" class="text-muted small">Loading...</div>
                </div>
                <!-- Notifications -->
                <div class="dropdown">
                    <button class="btn btn-light position-relative rounded-circle border-0 shadow-sm header-btn" 
                            id="notifDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell text-primary"></i>
                        @php
                            $unreadCount = auth()->check() ? \App\Models\Notifikasi::where('user_id', auth()->id())->where('dibaca', false)->count() : 0;
                        @endphp
                        @if($unreadCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                            </span>
                        @endif
                    </button>
                    
                    <div class="dropdown-menu dropdown-menu-end shadow border-0" style="min-width: 300px; border-radius: 12px;">
                        <div class="dropdown-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold">Notifikasi</h6>
                            <small class="text-muted">{{ $unreadCount }} baru</small>
                        </div>
                        <div class="dropdown-divider"></div>
                        
                        @if($unreadCount > 0)
                            @php
                                $latestNotifications = \App\Models\Notifikasi::where('user_id', auth()->id())
                                    ->latest()->take(3)->get();
                            @endphp
                            @foreach($latestNotifications as $notif)
                                <a href="{{ route('notifikasi.index') }}" class="dropdown-item py-2">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0 me-2">
                                            <div class="bg-primary bg-opacity-10 rounded-circle p-1" style="width: 28px; height: 28px;">
                                                <i class="fas fa-info-circle text-primary" style="font-size: 0.7rem;"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-medium" style="font-size: 0.85rem;">
                                                {{ \Illuminate\Support\Str::limit($notif->judul ?? $notif->pesan, 35) }}
                                            </div>
                                            <small class="text-muted">{{ $notif->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                            <div class="dropdown-divider"></div>
                        @else
                            <div class="dropdown-item text-center text-muted py-3">
                                <i class="fas fa-bell-slash mb-1" style="font-size: 1.2rem; opacity: 0.4;"></i>
                                <br><small>Tidak ada notifikasi</small>
                            </div>
                            <div class="dropdown-divider"></div>
                        @endif
                        
                        <a class="dropdown-item text-center text-primary fw-medium py-2" href="{{ route('notifikasi.index') }}">
                            <i class="fas fa-eye me-1"></i> Lihat Semua
                        </a>
                    </div>
                </div>
                
                <!-- User Profile -->
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle border-0 shadow-sm rounded-pill px-3 py-2 header-btn" 
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
                    
                    <div class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius: 12px; min-width: 200px;">
                        <div class="dropdown-header">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary rounded-circle me-2 d-flex align-items-center justify-content-center text-white fw-bold" 
                                     style="width: 32px; height: 32px; font-size: 0.8rem;">
                                    {{ strtoupper(substr(auth()->user()->nama_lengkap ?? auth()->user()->name ?? 'U', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-bold" style="font-size: 0.9rem;">{{ auth()->user()->nama_lengkap ?? auth()->user()->name ?? 'User' }}</div>
                                    <small class="text-muted text-capitalize">{{ auth()->user()->role ?? 'User' }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        
                        <a class="dropdown-item py-2" href="{{ route('profile.edit') }}">
                            <i class="fas fa-user-edit me-2 text-secondary"></i>
                            <span>Profile</span>
                        </a>
                        
                        <div class="dropdown-divider"></div>
                        
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item py-2 text-danger">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Breadcrumb & Page Title Section -->
<div class="container-fluid px-4 bg-light border-bottom">
    <div class="row">
        <div class="col-12">
            <!-- Breadcrumb Navigation -->
            <nav aria-label="breadcrumb" class="pt-3">
                <ol class="breadcrumb bg-transparent p-0 mb-2">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}" class="text-decoration-none text-primary">
                            <i class="fas fa-home me-1"></i>Dashboard
                        </a>
                    </li>
                    @if(count($breadcrumbs) > 0)
                        @foreach($breadcrumbs as $index => $breadcrumb)
                            @if($index === count($breadcrumbs) - 1)
                                <li class="breadcrumb-item active text-muted" aria-current="page">{{ $breadcrumb['title'] }}</li>
                            @else
                                <li class="breadcrumb-item">
                                    <a href="{{ $breadcrumb['url'] }}" class="text-decoration-none text-primary">
                                        {{ $breadcrumb['title'] }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    @else
                        <li class="breadcrumb-item active text-muted" aria-current="page">{{ $title }}</li>
                    @endif
                </ol>
            </nav>

            <!-- Page Title Section -->
            <div class="d-flex justify-content-between align-items-start pb-3">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 rounded-3 p-2 me-3">
                        <i class="{{ $icon }} text-primary" style="font-size: 1.2rem;"></i>
                    </div>
                    <div>
                        <h4 class="mb-1 text-dark fw-bold">{{ $title }}</h4>
                        @if($description)
                            <p class="text-muted mb-0" style="font-size: 0.9rem;">{{ $description }}</p>
                        @endif
                    </div>
                </div>
                
                <!-- Quick Actions (Optional slot for buttons) -->
                <div class="header-actions">
                    {{ $slot ?? '' }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Modern Header Styles */
.modern-header {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%) !important;
    backdrop-filter: blur(10px);
}

.header-btn {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    background: rgba(255, 255, 255, 0.8) !important;
    border: 1px solid rgba(0, 0, 0, 0.05) !important;
}

.header-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
    background: #ffffff !important;
}

.header-logo img {
    transition: transform 0.3s ease;
}

.header-logo:hover img {
    transform: scale(1.05);
}

.header-datetime {
    padding: 8px 12px;
    background: rgba(255, 255, 255, 0.7);
    border-radius: 8px;
    border: 1px solid rgba(0, 0, 0, 0.05);
    min-width: 90px;
}

/* Breadcrumb Styling */
.breadcrumb-item + .breadcrumb-item::before {
    content: "›";
    font-weight: bold;
    color: #6c757d;
    font-size: 1.1rem;
}

.breadcrumb-item a:hover {
    color: #0056b3 !important;
    text-decoration: underline !important;
}

/* Dropdown Improvements */
.dropdown-menu {
    border: none !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;
    animation: dropdownFadeIn 0.2s ease;
}

@keyframes dropdownFadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.dropdown-item:hover {
    background-color: rgba(13, 110, 253, 0.05) !important;
    color: #0d6efd !important;
}

/* Badge Styling */
.badge {
    font-size: 0.65rem !important;
    padding: 0.25em 0.5em !important;
    min-width: 18px;
    text-align: center;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .modern-header .container-fluid {
        /* removed extra padding */
    }
    
    .header-title h5 {
        font-size: 1rem;
    }
    
    .breadcrumb {
        font-size: 0.8rem;
    }
    
    .breadcrumb-item {
        max-width: 100px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .header-actions {
        display: none;
    }
}

@media (max-width: 576px) {
    .modern-header .row .col-8 {
        flex: 0 0 auto;
        width: 70%;
    }
    
    .modern-header .row .col-4 {
        flex: 0 0 auto;
        width: 30%;
    }
}

/* Loading Animation */
.loading-placeholder {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

/* Override sidebar-toggle inside header */
.modern-header .sidebar-toggle {
    position: relative !important;
    top: auto !important;
    left: auto !important;
    transform: none !important;
    background: transparent !important;
    border: none !important;
    box-shadow: none !important;
    color: #0d6efd !important;
}

.modern-header .sidebar-toggle:hover {
    background: rgba(13,110,253,0.1) !important;
}
</style>

<script>
// Enhanced Clock functionality
document.addEventListener('DOMContentLoaded', function() {
    function updateHeaderClock() {
        const now = new Date();
        
        // Format time as HH:MM:SS
        const timeString = now.toLocaleTimeString('id-ID', { 
            hour12: false,
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        
        // Format date as "Day, DD Mon YYYY"
        const dateString = now.toLocaleDateString('id-ID', {
            weekday: 'short',
            day: '2-digit', 
            month: 'short',
            year: 'numeric'
        });
        
        const timeElement = document.getElementById('header-time');
        const dateElement = document.getElementById('header-date');
        
        if (timeElement) timeElement.textContent = timeString;
        if (dateElement) dateElement.textContent = dateString;
    }
    
    // Update clock immediately and then every second
    updateHeaderClock();
    setInterval(updateHeaderClock, 1000);
    
    // Auto-close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        const dropdowns = document.querySelectorAll('.dropdown-menu.show');
        dropdowns.forEach(dropdown => {
            if (!dropdown.closest('.dropdown').contains(e.target)) {
                const toggle = dropdown.closest('.dropdown').querySelector('[data-bs-toggle="dropdown"]');
                if (toggle) {
                    bootstrap.Dropdown.getInstance(toggle)?.hide();
                }
            }
        });
    });
});
</script> 