@extends('layouts.app')

@section('content')
<x-page-header 
    title="Dashboard {{ ucfirst(auth()->user()->role) }}" 
    description="Selamat datang di Sistem Pengajuan HKI"
    icon="fas fa-tachometer-alt"
/>

<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    @if(auth()->user()->role === 'dosen')
                        <div class="row g-4">
                            <div class="col-lg-4 col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body p-4 text-center">
                                        <div class="bg-primary bg-opacity-10 rounded-3 p-3 mb-3 d-inline-block">
                                            <i class="fas fa-plus-circle text-primary fs-2"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-2">Pengajuan HKI</h5>
                                        <p class="text-muted mb-3">Buat pengajuan HKI baru atau kelola pengajuan yang sudah ada.</p>
                                        <a href="{{ route('pengajuan.index') }}" class="btn btn-primary">
                                            <i class="fas fa-file-plus me-2"></i>Kelola Pengajuan
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif(auth()->user()->role === 'admin')
                        <div class="row g-4">
                            <div class="col-lg-4 col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body p-4 text-center">
                                        <div class="bg-success bg-opacity-10 rounded-3 p-3 mb-3 d-inline-block">
                                            <i class="fas fa-check-circle text-success fs-2"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-2">Validasi Pengajuan</h5>
                                        <p class="text-muted mb-3">Validasi pengajuan HKI dari dosen.</p>
                                        <a href="{{ route('validasi.index') }}" class="btn btn-success">
                                            <i class="fas fa-clipboard-check me-2"></i>Lihat Pengajuan
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif(auth()->user()->role === 'direktur')
                        <div class="row g-4 mb-4">
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                                                    <i class="fas fa-clock text-warning fs-4"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="text-muted mb-1 fw-normal">Menunggu Persetujuan</h6>
                                                <h3 class="mb-0 fw-bold text-dark">{{ $menunggu }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="bg-success bg-opacity-10 rounded-3 p-3">
                                                    <i class="fas fa-check-circle text-success fs-4"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="text-muted mb-1 fw-normal">Disetujui</h6>
                                                <h3 class="mb-0 fw-bold text-dark">{{ $disetujui }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="bg-danger bg-opacity-10 rounded-3 p-3">
                                                    <i class="fas fa-times-circle text-danger fs-4"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="text-muted mb-1 fw-normal">Ditolak</h6>
                                                <h3 class="mb-0 fw-bold text-dark">{{ $ditolak }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-gradient-primary text-white border-0">
                                <h5 class="mb-0 fw-semibold">
                                    <i class="fas fa-clock me-2"></i>Pengajuan Menunggu Persetujuan Terbaru
                                    @if($pengajuanBaru->count() > 0)
                                        <span class="badge bg-light text-primary ms-2">
                                            @if($menunggu <= 5)
                                                {{ $menunggu }} total
                                            @else
                                                {{ $pengajuanBaru->count() }} dari {{ $menunggu }} total
                                            @endif
                                        </span>
                                    @elseif($menunggu > 0)
                                        <span class="badge bg-warning text-dark ms-2">{{ $menunggu }} menunggu</span>
                                    @endif
                                </h5>
                            </div>
                            <div class="card-body p-0 table-container">
                                @if($pengajuanBaru->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="border-0 py-3 fw-semibold text-muted">JUDUL KARYA</th>
                                                <th class="border-0 py-3 fw-semibold text-muted">PENCIPTA</th>
                                                <th class="border-0 py-3 fw-semibold text-muted">TANGGAL</th>
                                                <th class="border-0 py-3 fw-semibold text-muted text-center">AKSI</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($pengajuanBaru as $item)
                                            <tr>
                                                <td class="py-3 col-judul">
                                                    <h6 class="mb-1 fw-semibold text-dark">{{ $item->judul_karya }}</h6>
                                                    @if($item->nomor_pengajuan)
                                                        <small class="text-muted">No: {{ $item->nomor_pengajuan }}</small>
                                                    @endif
                                                </td>
                                                <td class="py-3 col-nama">
                                                    <div class="nama-pencipta fw-medium text-dark">
                                                        {{ optional($item->pengaju->first())->nama ?? $item->nama_pengusul ?? '-' }}
                                                    </div>
                                                </td>
                                                <td class="py-3 text-dark">
                                                    {{ $item->tanggal_pengajuan ? $item->tanggal_pengajuan->format('d/m/Y H:i') : '-' }}
                                                </td>
                                                <td class="py-3 text-center">
                                                    <a href="{{ route('persetujuan.show', $item->id) }}" class="btn btn-info btn-sm">
                                                        <i class="fas fa-eye me-1"></i>Detail
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                                    <h6 class="mb-2">Tidak ada pengajuan menunggu persetujuan</h6>
                                    <p class="mb-0 small">Semua pengajuan telah diproses</p>
                                </div>
                                @endif
                                
                                @if($menunggu > 5)
                                <div class="card-footer bg-light border-0 text-center py-3">
                                    <a href="{{ route('persetujuan.index') }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-list me-2"></i>Lihat Semua {{ $menunggu }} Pengajuan
                                    </a>
                                </div>
                                @elseif($pengajuanBaru->count() > 0)
                                <div class="card-footer bg-light border-0 text-center py-2">
                                    <a href="{{ route('persetujuan.index') }}" class="btn btn-link btn-sm text-decoration-none">
                                        <i class="fas fa-external-link-alt me-1"></i>Buka Halaman Persetujuan
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Dashboard Table Layout Fixes */
.table th:nth-child(1) { width: 40%; } /* Judul */
.table th:nth-child(2) { width: 25%; } /* Pengusul */
.table th:nth-child(3) { width: 25%; } /* Tanggal */
.table th:nth-child(4) { width: 10%; } /* Aksi */

.nama-pencipta {
    word-wrap: break-word;
    word-break: break-word;
    white-space: normal;
    line-height: 1.3;
    max-height: 3.9em;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
}

.col-nama {
    max-width: 150px;
    min-width: 120px;
}

.col-judul {
    max-width: 250px;
}

/* Perbaikan layout untuk direktur */
.bg-purple-100 { background-color: #e9d5ff !important; }
.bg-green-100 { background-color: #dcfce7 !important; }
.bg-red-100 { background-color: #fee2e2 !important; }
.text-purple-800 { color: #6b21a8 !important; }
.text-purple-700 { color: #7c3aed !important; }
.text-green-800 { color: #166534 !important; }
.text-green-700 { color: #15803d !important; }
.text-red-800 { color: #991b1b !important; }
.text-red-700 { color: #dc2626 !important; }

/* Gradient backgrounds */
.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

/* Card hover effects */
.card {
    transition: all 0.3s ease;
}
.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

/* Responsive improvements for direktur dashboard */
@media (max-width: 768px) {
    .btn.w-100.py-3 {
        padding: 1rem !important;
        font-size: 1.1rem;
    }
}

/* Primary button enhancements */
.btn-primary.py-3 {
    border-radius: 8px;
    font-weight: 500;
    box-shadow: 0 2px 4px rgba(0,123,255,0.3);
    transition: all 0.2s ease;
}

.btn-primary.py-3:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,123,255,0.4);
}

/* Dashboard table container - Remove any padding/margin */
.table-container {
    margin: 0 !important;
    padding: 0 !important;
}

.table-container .table-responsive {
    margin: 0 !important;
    padding: 0 !important;
}

/* Dashboard table styling - Full width hover */
.table-hover tbody tr td {
    border: none !important;
    vertical-align: middle !important;
    background-color: #ffffff !important;
    padding-left: 1.5rem !important;
    padding-right: 1.5rem !important;
    margin: 0 !important;
}

/* Hover effect yang penuh di seluruh baris - Full edge to edge */
.table-hover tbody tr:hover td {
    background-color: rgba(0, 0, 0, 0.075) !important;
    transition: background-color 0.15s ease-in-out !important;
}

/* Ensure table width is 100% */
.table-hover {
    width: 100% !important;
    margin: 0 !important;
}

.nama-pencipta {
    color: inherit !important;
    background: transparent !important;
}
</style>
@endsection