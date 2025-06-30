@props(['title', 'description', 'icon' => 'fas fa-file', 'breadcrumbs' => []])

<div class="container-fluid px-4">
    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-transparent p-0 mb-2">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}" class="text-primary text-decoration-none">
                    <i class="fas fa-home me-1"></i>Dashboard
                </a>
            </li>
            @if(count($breadcrumbs) > 0)
                @foreach($breadcrumbs as $index => $breadcrumb)
                    @if($index === count($breadcrumbs) - 1)
                        <li class="breadcrumb-item active text-muted" aria-current="page">{{ $breadcrumb['title'] }}</li>
                    @else
                        <li class="breadcrumb-item">
                            <a href="{{ $breadcrumb['url'] }}" class="text-primary text-decoration-none">
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

    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-start">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                        <i class="{{ $icon }} text-primary fs-4"></i>
                    </div>
                    <div>
                        <h2 class="mb-1 text-dark fw-bold">{{ $title }}</h2>
                        @if($description)
                            <p class="text-muted mb-0">{{ $description }}</p>
                        @endif
                    </div>
                </div>
                <div class="d-flex align-items-center text-muted" style="font-size: 0.9rem;">
                    <i class="fas fa-clock me-2"></i>
                    <div id="page-header-time" class="fw-medium">08.00.30</div>
                    <div class="mx-2 text-black-50">|</div>
                    <div id="page-header-date" class="text-dark">Jum, 27 Jun 2025</div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.breadcrumb-item + .breadcrumb-item::before {
    content: "›";
    font-weight: bold;
    color: #6c757d;
}

.breadcrumb-item a:hover {
    color: #0056b3 !important;
    text-decoration: underline !important;
}

@media (max-width: 768px) {
    .breadcrumb {
        font-size: 0.875rem;
    }
    .breadcrumb-item {
        max-width: 120px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    /* Hide clock on mobile for cleaner look */
    #page-header-time,
    #page-header-date,
    .fa-clock {
        display: none !important;
    }
}
</style>

<script>
// Clock functionality for page header
document.addEventListener('DOMContentLoaded', function() {
    function updatePageHeaderClock() {
        const now = new Date();
        
        // Format time as HH.MM.SS
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const timeString = `${hours}.${minutes}.${seconds}`;
        
        // Format date as "Day, DD Mon YYYY"
        const days = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        
        const dayName = days[now.getDay()];
        const day = String(now.getDate()).padStart(2, '0');
        const month = months[now.getMonth()];
        const year = now.getFullYear();
        const dateString = `${dayName}, ${day} ${month} ${year}`;
        
        const timeElement = document.getElementById('page-header-time');
        const dateElement = document.getElementById('page-header-date');
        
        if (timeElement) timeElement.textContent = timeString;
        if (dateElement) dateElement.textContent = dateString;
    }
    
    // Update clock immediately and then every second
    updatePageHeaderClock();
    setInterval(updatePageHeaderClock, 1000);
});
</script> 