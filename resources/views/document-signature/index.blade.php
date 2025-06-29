@extends('layouts.app')

@section('content')
<x-page-header 
    title="Tanda Tangan & Materai Dokumen" 
    description="Kelola tanda tangan dan materai untuk dokumen pengajuan HKI"
    icon="fas fa-signature"
    :breadcrumbs="[
        ['title' => 'Pengajuan', 'url' => route('pengajuan.index')],
        ['title' => 'Tanda Tangan Dokumen']
    ]"
/>

<div class="container-fluid py-4">

    <!-- Pengajuan Info -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-primary text-white border-0">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-file-alt me-2"></i>
                        Informasi Pengajuan
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small">Nomor Pengajuan</label>
                                <div class="fw-semibold">{{ $pengajuan->nomor_pengajuan ?? 'Belum ada nomor' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small">Judul Karya</label>
                                <div class="fw-semibold">{{ $pengajuan->judul_karya }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small">Nama Pengusul</label>
                                <div class="fw-semibold">{{ $pengajuan->nama_pengusul }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small">Status</label>
                                <div>
                                    <span class="badge bg-{{ $pengajuan->status == 'divalidasi_sedang_diproses' ? 'success' : 'warning' }}">
                                        {{ ucfirst(str_replace('_', ' ', $pengajuan->status)) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Available Documents -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-info text-white border-0">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-file-pdf me-2"></i>
                        Dokumen yang Tersedia
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if(count($documents) > 0)
                        <div class="row g-3">
                            @foreach($documents as $type => $document)
                            <div class="col-md-6">
                                <div class="document-card border rounded-3 p-3 h-100">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="document-icon me-3">
                                            <i class="fas fa-file-pdf text-danger fs-2"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fw-semibold">{{ $document['name'] }}</h6>
                                            <small class="text-muted">{{ basename($document['path']) }}</small>
                                        </div>
                                    </div>
                                    
                                    <div class="d-grid gap-2">
                                        <a href="{{ $document['url'] }}" 
                                           target="_blank" 
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye me-1"></i>Lihat Dokumen
                                        </a>
                                        <a href="{{ route('document-signature.show', [$pengajuan->id, $type]) }}" 
                                           class="btn btn-success btn-sm">
                                            <i class="fas fa-signature me-1"></i>Tambah Tanda Tangan & Materai
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-file-times text-muted fs-1 mb-3"></i>
                            <h5 class="text-muted">Tidak Ada Dokumen</h5>
                            <p class="text-muted mb-0">Belum ada dokumen yang diupload untuk pengajuan ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Available Signatures & Stamps -->
    <div class="row">
        <!-- Signatures -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-success text-white border-0">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-signature me-2"></i>
                        Tanda Tangan Tersedia
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if(count($signatures) > 0)
                        <div class="row g-3">
                            @foreach($signatures as $signature)
                            <div class="col-12">
                                <div class="signature-item border rounded-3 p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="signature-preview me-3">
                                            <img src="{{ $signature['url'] }}" 
                                                 alt="{{ $signature['name'] }}" 
                                                 class="img-fluid rounded border"
                                                 style="max-width: 80px; max-height: 40px; object-fit: contain;">
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fw-semibold">{{ $signature['name'] }}</h6>
                                            <small class="text-muted">ID: {{ $signature['id'] }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-signature text-muted fs-3 mb-2"></i>
                            <p class="text-muted mb-0">Belum ada tanda tangan tersedia</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Stamps -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-warning text-dark border-0">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-certificate me-2"></i>
                        Materai Tersedia
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if(count($stamps) > 0)
                        <div class="row g-3">
                            @foreach($stamps as $stamp)
                            <div class="col-12">
                                <div class="stamp-item border rounded-3 p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="stamp-preview me-3">
                                            <img src="{{ $stamp['url'] }}" 
                                                 alt="{{ $stamp['name'] }}" 
                                                 class="img-fluid rounded border"
                                                 style="max-width: 60px; max-height: 60px; object-fit: contain;">
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fw-semibold">{{ $stamp['name'] }}</h6>
                                            <small class="text-muted">ID: {{ $stamp['id'] }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-certificate text-muted fs-3 mb-2"></i>
                            <p class="text-muted mb-0">Belum ada materai tersedia</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #667eea 0%, #f093fb 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
    color: #333 !important;
}

.document-card {
    transition: all 0.3s ease;
    background: #f8f9fa;
}

.document-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.signature-item, .stamp-item {
    background: #f8f9fa;
    transition: all 0.3s ease;
}

.signature-item:hover, .stamp-item:hover {
    background: #e9ecef;
}

.info-item {
    margin-bottom: 1rem;
}

.info-item label {
    display: block;
    font-size: 0.875rem;
    font-weight: 500;
    color: #6c757d;
    margin-bottom: 0.25rem;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: "›";
    font-weight: bold;
    color: #6c757d;
}

@media (max-width: 768px) {
    .container-fluid {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .card-body {
        padding: 1.5rem;
    }
}
</style>
@endsection 