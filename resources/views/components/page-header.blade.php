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
                <div class="text-end">
                    <span class="badge bg-light text-dark fs-6 px-3 py-2">
                        <i class="fas fa-calendar-day me-1"></i>{{ now()->format('d M Y') }}
                    </span>
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
}
</style> 