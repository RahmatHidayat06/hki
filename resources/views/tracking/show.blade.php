@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="mb-2 text-primary">
                                <i class="fas fa-route me-2"></i>Tracking Status Pengajuan
                            </h4>
                            <p class="mb-0 text-muted">
                                <strong>{{ $pengajuan->judul_karya }}</strong><br>
                                Nomor: {{ $pengajuan->nomor_pengajuan ?? 'Belum ada nomor' }}
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="badge bg-{{ $pengajuan->status === 'disetujui' ? 'success' : ($pengajuan->status === 'ditolak' ? 'danger' : 'warning') }} fs-6 px-3 py-2">
                                {{ ucfirst(str_replace('_', ' ', $pengajuan->status)) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Section -->
            @if($pengajuan->signatures->count() > 0)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="text-muted mb-3">
                        <i class="fas fa-signature me-2"></i>Progress Tanda Tangan
                    </h6>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" 
                                     role="progressbar" 
                                     style="width: {{ $signatureProgress }}%"
                                     aria-valuenow="{{ $signatureProgress }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="badge bg-primary">{{ $signatureProgress }}% Complete</span>
                        </div>
                    </div>
                    <div class="row mt-3">
                        @foreach($pengajuan->signatures as $signature)
                        <div class="col-md-6 mb-2">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-{{ $signature->status === 'signed' ? 'check-circle text-success' : 'clock text-warning' }} me-2"></i>
                                <span class="small">
                                    {{ $signature->nama_pencipta }}
                                    @if($signature->status === 'signed')
                                        <small class="text-muted">({{ $signature->signed_at->format('d/m/Y H:i') }})</small>
                                    @endif
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Timeline -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="text-muted mb-4">
                                <i class="fas fa-history me-2"></i>Timeline Proses
                            </h6>
                            
                            <div class="timeline">
                                @foreach($trackingStatuses as $index => $tracking)
                                <div class="timeline-item {{ $index === 0 ? 'active' : '' }}">
                                    <div class="timeline-marker">
                                        <i class="{{ $tracking->icon }} text-{{ $tracking->color }}"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <div class="card border-0 shadow-sm">
                                            <div class="card-body p-3">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h6 class="mb-1 text-{{ $tracking->color }}">{{ $tracking->title }}</h6>
                                                    <small class="text-muted">{{ $tracking->created_at->format('d/m/Y H:i') }}</small>
                                                </div>
                                                @if($tracking->description)
                                                <p class="mb-2 text-muted small">{{ $tracking->description }}</p>
                                                @endif
                                                @if($tracking->notes)
                                                <div class="alert alert-light border-start border-4 border-{{ $tracking->color }} mb-2 py-2">
                                                    <small><strong>Catatan:</strong> {{ $tracking->notes }}</small>
                                                </div>
                                                @endif
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">
                                                        <i class="fas fa-user me-1"></i>{{ $tracking->user->nama_lengkap ?? 'System' }}
                                                    </small>
                                                    <small class="text-muted">{{ $tracking->created_at->diffForHumans() }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="d-flex gap-2 justify-content-between">
                        <div>
                            <a href="{{ route('pengajuan.show', $pengajuan->id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali ke Detail
                            </a>
                        </div>
                        <div>
                            @if($pengajuan->signatures->count() > 0)
                            <a href="{{ route('signatures.index', $pengajuan->id) }}" class="btn btn-primary">
                                <i class="fas fa-signature me-2"></i>Kelola Tanda Tangan
                            </a>
                            @endif
                            <button type="button" class="btn btn-outline-primary" onclick="refreshTracking()">
                                <i class="fas fa-sync-alt me-2"></i>Refresh
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding: 0;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 20px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    padding-left: 60px;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: 8px;
    top: 8px;
    width: 24px;
    height: 24px;
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2;
}

.timeline-item.active .timeline-marker {
    border-color: #0d6efd;
    background: #0d6efd;
    color: white;
}

.timeline-content {
    flex: 1;
}

.timeline-item:last-child::before {
    display: none;
}
</style>

<script>
function refreshTracking() {
    // Refresh tracking data via AJAX
    fetch(`{{ route('tracking.data', $pengajuan->id) }}`)
        .then(response => response.json())
        .then(data => {
            // Update progress if exists
            if (data.signature_progress !== undefined) {
                const progressBar = document.querySelector('.progress-bar');
                if (progressBar) {
                    progressBar.style.width = data.signature_progress + '%';
                    progressBar.setAttribute('aria-valuenow', data.signature_progress);
                }
            }
            
            // Refresh page untuk update timeline
            window.location.reload();
        })
        .catch(error => {
            console.error('Error refreshing tracking:', error);
        });
}

// Auto refresh setiap 30 detik
setInterval(refreshTracking, 30000);
</script>
@endsection 