@extends('layouts.app')

@section('content')
<x-page-header 
    title="Pengajuan HKI" 
    description="Kelola dan pantau pengajuan Hak Kekayaan Intelektual Anda"
    icon="fas fa-file-alt"
    :breadcrumbs="[
        ['title' => 'Hak Cipta', 'url' => '#'],
        ['title' => 'Daftar Ciptaan']
    ]"
/>

<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <!-- Header section removed as per request -->
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    <!-- Button row removed as per request -->

                        <!-- Search and Filter Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 pt-4 pb-0">
            <h5 class="mb-0 fw-semibold text-dark">
                <i class="fas fa-filter me-2 text-primary"></i>
                Filter & Pencarian
            </h5>
        </div>
        <div class="card-body p-4">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label text-muted fw-medium">Cari Pengajuan</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" 
                               placeholder="Judul karya, nama pemohon, nomor..." 
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-muted fw-medium">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="menunggu_validasi" {{ request('status') == 'menunggu_validasi' ? 'selected' : '' }}>
                            Menunggu Validasi
                        </option>
                        <option value="divalidasi_sedang_diproses" {{ request('status') == 'divalidasi_sedang_diproses' ? 'selected' : '' }}>
                            Divalidasi & Sedang Diproses
                        </option>
                        <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>
                            Disetujui
                        </option>
                        <option value="menunggu_pembayaran" {{ request('status') == 'menunggu_pembayaran' ? 'selected' : '' }}>
                            Menunggu Bayar
                        </option>
                        <option value="menunggu_verifikasi_pembayaran" {{ request('status') == 'menunggu_verifikasi_pembayaran' ? 'selected' : '' }}>
                            Verifikasi Pembayaran
                        </option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>
                            Selesai
                        </option>
                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>
                            Ditolak
                        </option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-muted fw-medium">Dari Tanggal</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label text-muted fw-medium">Sampai Tanggal</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label text-muted fw-medium">Urutkan</label>
                    <select name="sort" class="form-select">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                        <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Judul A-Z</option>
                    </select>
                </div>
                <div class="col-12">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-search me-2"></i>Cari
                        </button>
                        <a href="{{ route('pengajuan.index') }}" class="btn btn-outline-secondary px-4">
                            <i class="fas fa-refresh me-2"></i>Reset
                        </a>
                    </div>
                </div>
            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0 py-3 fw-semibold text-muted">No</th>
                                    <th class="border-0 py-3 fw-semibold text-muted">Judul Karya</th>
                                    <th class="border-0 py-3 fw-semibold text-muted">Jenis Ciptaan</th>
                                    <th class="border-0 py-3 fw-semibold text-muted">Sub Jenis Ciptaan</th>
                                    <th class="border-0 py-3 fw-semibold text-muted">Tahun Usulan</th>
                                    <th class="border-0 py-3 fw-semibold text-muted">Jumlah Pencipta</th>
                                    <th class="border-0 py-3 fw-semibold text-muted">Status</th>
                                    <th class="border-0 py-3 fw-semibold text-muted">Tanggal Pengajuan</th>
                                    <th class="border-0 py-3 fw-semibold text-muted">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pengajuan as $item)
                                <tr class="border-bottom">
                                    <td class="ps-4 py-3">{{ $loop->iteration }}</td>
                                    <td class="py-3">{{ $item->judul_karya }}</td>
                                    <td class="py-3">{{ $item->identitas_ciptaan }}</td>
                                    <td class="py-3">{{ $item->sub_jenis_ciptaan }}</td>
                                    <td class="py-3">{{ $item->tahun_usulan ?? '-' }}</td>
                                    <td class="py-3">{{ $item->jumlah_pencipta }}</td>
                                    <td class="py-3">
                                        @if($item->status === 'menunggu_validasi')
                                            <span class="badge bg-warning text-dark px-3 py-2">
                                                <i class="fas fa-clock me-1"></i>Menunggu Validasi
                                            </span>
                                        @elseif($item->status === 'divalidasi_sedang_diproses')
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i>Divalidasi & Sedang Diproses
                                            </span>
                                        @elseif($item->status === 'disetujui')
                                            <span class="badge bg-primary px-3 py-2">
                                                <i class="fas fa-thumbs-up me-1"></i>Disetujui
                                            </span>
                                        @elseif($item->status === 'menunggu_pembayaran')
                                            <span class="badge bg-info text-dark px-3 py-2">
                                                <i class="fas fa-money-bill-wave me-1"></i>Menunggu Bayar
                                            </span>
                                        @elseif($item->status === 'menunggu_verifikasi_pembayaran')
                                            <span class="badge bg-info text-dark px-3 py-2">
                                                <i class="fas fa-hourglass-half me-1"></i>Verifikasi Pembayaran
                                            </span>
                                        @elseif($item->status === 'selesai')
                                            <span class="badge bg-success px-3 py-2">
                                                <i class="fas fa-flag-checkered me-1"></i>Selesai
                                            </span>
                                        @elseif($item->status === 'ditolak')
                                            <span class="badge bg-danger px-3 py-2">
                                                <i class="fas fa-times-circle me-1"></i>Ditolak
                                            </span>
                                        @else
                                            <span class="badge bg-secondary px-3 py-2">
                                                <i class="fas fa-question-circle me-1"></i>{{ ucfirst(str_replace('_', ' ', $item->status)) }}
                                        </span>
                                        @endif
                                    </td>
                                    <td class="py-3">{{ $item->created_at ? $item->created_at->format('d/m/Y H:i') . ' WITA' : '-' }}</td>
                                    <td class="py-3 text-center">
                                        <div class="btn-group-vertical btn-group-sm" role="group" aria-label="Aksi Pengajuan">
                                            <a href="{{ route('pengajuan.show', $item->id) }}" class="btn btn-info btn-sm" title="Lihat Detail">
                                                <i class="fas fa-eye me-1"></i>Detail
                                            </a>
                                            
                                            @if(in_array(auth()->user()->role, ['admin', 'direktur']))
                                                @if($item->status === 'menunggu_validasi')
                                                    <a href="{{ route('validasi.show', $item->id) }}" class="btn btn-success btn-sm" title="Validasi Pengajuan">
                                                        <i class="fas fa-check-circle me-1"></i>Validasi
                                                    </a>
                                                @endif
                                            @endif
                                            
                                            @if(auth()->user()->role === 'admin')
                                                @if($item->status === 'menunggu_validasi')
                                                    <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('pengajuan.edit', $item->id) }}" class="btn btn-warning btn-sm" title="Edit Pengajuan">
                                                        <i class="fas fa-edit me-1"></i>Edit
                                                    </a>
                                                    <form action="{{ route('pengajuan.destroy', $item->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus Pengajuan" onclick="return confirm('Apakah Anda yakin ingin menghapus pengajuan ini?')">
                                                            <i class="fas fa-trash me-1"></i>Hapus
                                                        </button>
                                                    </form>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">Tidak ada data pengajuan</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $pengajuan->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom styles for better visual appeal */
.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
}

.table-responsive {
    border-radius: 0.5rem;
}

.table th {
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
}

.table td {
    vertical-align: middle;
}

.badge {
    font-weight: 500;
    letter-spacing: 0.25px;
}

.btn {
    font-weight: 500;
    border-radius: 0.375rem;
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

/* Action buttons consistency */
.btn-group {
    display: inline-flex !important;
    vertical-align: middle;
}

.btn-group .btn {
    margin: 0 !important;
    border-radius: 0;
    border-right: 1px solid rgba(255,255,255,0.2);
}

.btn-group .btn:first-child {
    border-top-left-radius: 0.375rem;
    border-bottom-left-radius: 0.375rem;
}

.btn-group .btn:last-child {
    border-top-right-radius: 0.375rem;
    border-bottom-right-radius: 0.375rem;
    border-right: none;
}

.btn-group form {
    display: inline-flex !important;
    margin: 0 !important;
}

/* Ensure consistent button heights */
.btn-group .btn {
    height: 32px;
    line-height: 1.5;
    padding: 0.25rem 0.75rem;
    font-size: 0.875rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

/* Remove any unwanted spacing */
.btn-group .btn i {
    margin-right: 0.25rem;
}

/* Table cell alignment for action buttons */
td .btn-group {
    white-space: nowrap;
}

/* Vertical button groups */
.btn-group-vertical .btn {
    border-radius: 0;
    border-bottom: 1px solid rgba(255,255,255,0.2);
    border-right: none;
}

.btn-group-vertical .btn:first-child {
    border-top-left-radius: 0.375rem;
    border-top-right-radius: 0.375rem;
}

.btn-group-vertical .btn:last-child {
    border-bottom-left-radius: 0.375rem;
    border-bottom-right-radius: 0.375rem;
    border-bottom: none;
}

.modal-content {
    border-radius: 1rem;
}

.modal-header {
    border-radius: 1rem 1rem 0 0;
}

.form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.input-group-text {
    background-color: #f8f9fa;
    border-color: #dee2e6;
}

.pagination .page-link {
    border-radius: 0.375rem;
    margin: 0 2px;
    border: none;
    color: #6c757d;
}

.pagination .page-item.active .page-link {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.pagination .page-link:hover {
    background-color: #e9ecef;
    color: #0d6efd;
}
</style>

@endsection
