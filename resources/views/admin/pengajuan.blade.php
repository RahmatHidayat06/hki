@extends('layouts.app')

@section('content')
<x-page-header 
    title="Pengajuan HKI (Admin)" 
    description="Kelola seluruh pengajuan HKI"
    icon="fas fa-clipboard-list"
    :breadcrumbs="[
        ['title' => 'Admin', 'url' => route('admin.dashboard')],
        ['title' => 'Pengajuan HKI']
    ]"
/>

<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow rounded-lg border-0 bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title mb-1">Total Pengajuan</h5>
                    <h2 class="mb-0">{{ $total }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow rounded-lg border-0 bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title mb-1">Pengajuan Lengkap</h5>
                    <h2 class="mb-0">{{ $totalLengkap }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4 d-flex align-items-center">
            <form action="{{ route('admin.rekap') }}" method="GET" class="w-100">
                <button type="submit" class="btn btn-warning w-100" {{ $total === 0 || $total !== $totalLengkap ? 'disabled' : '' }}>
                    <i class="bi bi-download"></i> Rekap Data (Excel)
                </button>
            </form>
        </div>
    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

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
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Judul karya, nama pemohon, nomor..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-muted fw-medium">Status</label>
                    <select name="status" class="form-select status-filter-dropdown" data-current-status="{{ request('status') }}">
                        <option value="" data-status="">Semua Status</option>
                        <option value="menunggu_validasi" {{ request('status')=='menunggu_validasi'?'selected':'' }} data-status="menunggu_validasi">Menunggu Validasi</option>
                        <option value="divalidasi_sedang_diproses" {{ request('status')=='divalidasi_sedang_diproses'?'selected':'' }} data-status="divalidasi_sedang_diproses">Divalidasi & Sedang Diproses</option>
                        <option value="menunggu_pembayaran" {{ request('status')=='menunggu_pembayaran'?'selected':'' }} data-status="menunggu_pembayaran">Menunggu Pembayaran</option>
                        <option value="menunggu_verifikasi_pembayaran" {{ request('status')=='menunggu_verifikasi_pembayaran'?'selected':'' }} data-status="menunggu_verifikasi_pembayaran">Verifikasi Pembayaran</option>
                        <option value="selesai" {{ request('status')=='selesai'?'selected':'' }} data-status="selesai">Selesai</option>
                        <option value="ditolak" {{ request('status')=='ditolak'?'selected':'' }} data-status="ditolak">Ditolak</option>
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
                        <option value="newest" {{ request('sort')=='newest'?'selected':'' }}>Terbaru</option>
                        <option value="oldest" {{ request('sort')=='oldest'?'selected':'' }}>Terlama</option>
                        <option value="title" {{ request('sort')=='title'?'selected':'' }}>Judul A-Z</option>
                    </select>
                </div>
                <div class="col-12">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4"><i class="fas fa-search me-2"></i>Cari</button>
                        <a href="{{ route('admin.pengajuan') }}" class="btn btn-outline-secondary px-4"><i class="fas fa-refresh me-2"></i>Reset</a>
                </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 pt-4 pb-0">
            <h5 class="mb-0 fw-semibold text-dark">
                <i class="fas fa-list me-2 text-primary"></i>
                Daftar Semua Pengajuan HKI
                @if($pengajuan->total() > 0)
                    <span class="badge bg-light text-dark ms-2">{{ $pengajuan->total() }} total</span>
                @endif
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 py-3 fw-semibold text-muted">No</th>
                            <th class="border-0 py-3 fw-semibold text-muted">Judul Karya</th>
                            <th class="border-0 py-3 fw-semibold text-muted">Nama Pencipta</th>
                            <th class="border-0 py-3 fw-semibold text-muted">Status</th>
                            <th class="border-0 py-3 fw-semibold text-muted text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengajuan as $item)
                        <tr class="border-bottom">
                            <td class="ps-4 py-3">{{ ($pengajuan->currentPage() - 1) * $pengajuan->perPage() + $loop->iteration }}</td>
                            <td class="py-3 col-judul">
                                <div class="text-truncate" title="{{ $item->judul_karya }}">
                                    {{ $item->judul_karya }}
                                </div>
                            </td>
                            <td class="py-3 col-nama">
                                <div class="nama-pencipta">
                                    {{ optional($item->pengaju->first())->nama ?? '-' }}
                                </div>
                            </td>
                            <td class="py-3">
                                @php
                                    // Definisi urutan status
                                    $statusOrder = [
                                        'menunggu_validasi' => 1,
                                        'divalidasi_sedang_diproses' => 2,
                                        'menunggu_pembayaran' => 3,
                                        'menunggu_verifikasi_pembayaran' => 4,
                                        'selesai' => 5,
                                        'ditolak' => 99 // Status khusus
                                    ];
                                    
                                    $currentStatusLevel = $statusOrder[$item->status] ?? 1;
                                    $allStatuses = ['menunggu_validasi','divalidasi_sedang_diproses','menunggu_pembayaran','menunggu_verifikasi_pembayaran','selesai'];
                                    
                                    // Filter status yang bisa dipilih (hanya yang sama level atau lebih tinggi)
                                    $availableStatuses = array_filter($allStatuses, function($st) use ($statusOrder, $currentStatusLevel) {
                                        return $statusOrder[$st] >= $currentStatusLevel;
                                    });
                                @endphp
                                
                                @if($item->status !== 'ditolak' && $item->status !== 'selesai')
                                <div class="d-flex align-items-center gap-1">
                                    <form action="{{ route('pengajuan.updateStatus', $item->id) }}" method="POST" class="d-flex align-items-center">
                                        @csrf
                                        <select name="status" class="form-select form-select-sm me-1 status-dropdown" data-current-status="{{ $item->status }}">
                                            @foreach($availableStatuses as $st)
                                                <option value="{{ $st }}" {{ $item->status==$st ? 'selected' : '' }} data-status="{{ $st }}">
                                                    @if($st === 'divalidasi_sedang_diproses')
                                                        Divalidasi & Sedang Diproses
                                                    @else
                                                        {{ ucfirst(str_replace('_',' ',$st)) }}
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-sm btn-success" title="Update Status">
                                            <i class="fas fa-arrow-up"></i>
                                        </button>
                                    </form>
                                    
                                    @if($item->status !== 'selesai')
                                    <form action="{{ route('pengajuan.updateStatus', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="status" value="ditolak">
                                        <button type="submit" class="btn btn-sm btn-danger" title="Tolak Pengajuan" onclick="return confirm('Yakin ingin menolak pengajuan ini?')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                                @else
                                    <div class="d-flex align-items-center">
                                        @if($item->status === 'selesai')
                                            <span class="badge bg-success px-3 py-2">
                                                <i class="fas fa-check-circle me-1"></i>Selesai
                                            </span>
                                        @elseif($item->status === 'ditolak')
                                            <span class="badge bg-danger px-3 py-2">
                                                <i class="fas fa-times-circle me-1"></i>Ditolak
                                            </span>
                                            @if(auth()->user()->role === 'admin')
                                            <form action="{{ route('pengajuan.updateStatus', $item->id) }}" method="POST" class="d-inline ms-1">
                                                @csrf
                                                <input type="hidden" name="status" value="menunggu_validasi">
                                                <button type="submit" class="btn btn-sm btn-warning" title="Reset ke Menunggu Validasi" onclick="return confirm('Reset pengajuan ke status Menunggu Validasi?')">
                                                    <i class="fas fa-undo"></i>
                                                </button>
                                            </form>
                                            @endif
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 text-center">
                                <div class="d-flex flex-wrap justify-content-center gap-1">
                                    <a href="{{ route('admin.pengajuan.show', $item->id) }}" class="btn btn-info btn-sm">Detail</a>
                                @if($item->status === 'menunggu_validasi')
                                        <a href="{{ route('pengajuan.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                @else
                                        <button type="button" class="btn btn-secondary btn-sm" onclick="alert('Pengajuan hanya dapat diedit jika statusnya Menunggu Validasi');">Edit</button>
                                @endif
                                @php
                                    $rekapReady = in_array($item->status, ['divalidasi_sedang_diproses','menunggu_pembayaran','menunggu_verifikasi_pembayaran','selesai','disetujui']);
                                @endphp
                                @if($rekapReady)
                                        <a href="{{ route('admin.pengajuan.rekapPdf', $item->id) }}" class="btn btn-outline-primary btn-sm" title="Rekap PDF"><i class="fas fa-file-pdf"></i></a>
                                        <a href="{{ route('admin.pengajuan.rekapExcel', $item->id) }}" class="btn btn-outline-success btn-sm" title="Rekap Excel"><i class="fas fa-file-excel"></i></a>
                                @else
                                        <button class="btn btn-outline-secondary btn-sm" title="Rekap tersedia setelah validasi" disabled><i class="fas fa-file-pdf"></i></button>
                                        <button class="btn btn-outline-secondary btn-sm" title="Rekap tersedia setelah validasi" disabled><i class="fas fa-file-excel"></i></button>
                                @endif
                                <form action="{{ route('pengajuan.destroy', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus data?')">Hapus</button>
                                </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">Belum ada data pengajuan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                {{ $pengajuan->links() }}
            </div>
        </div>
    </div>
</div>

<style>
.card{transition:all .3s ease}.card:hover{transform:translateY(-2px)}.table-responsive{border-radius:.5rem}.table th{font-weight:600;text-transform:uppercase;font-size:.75rem;letter-spacing:.5px}.table td{vertical-align:middle}.badge{font-weight:500;letter-spacing:.25px}.btn{font-weight:500;border-radius:.375rem;transition:all .2s ease}.btn:hover{transform:translateY(-1px)}.form-control:focus{border-color:#0d6efd;box-shadow:0 0 0 .2rem rgba(13,110,253,.25)}

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

/* Table Layout Fixes */
.table th:nth-child(1) { width: 5%; }  /* No */
.table th:nth-child(2) { width: 30%; } /* Judul Karya */
.table th:nth-child(3) { width: 20%; } /* Nama Pencipta */
.table th:nth-child(4) { width: 25%; } /* Status */
.table th:nth-child(5) { width: 20%; } /* Aksi */

.col-judul {
    max-width: 250px;
}

.col-nama {
    max-width: 150px;
    min-width: 120px;
}

.nama-pencipta {
    word-wrap: break-word;
    word-break: break-word;
    white-space: normal;
    line-height: 1.3;
    max-height: 3.9em; /* Allow max 3 lines */
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
}

/* Status Dropdown Colors */
.status-dropdown {
    font-weight: 600;
    border: 2px solid #ddd;
    transition: all 0.3s ease;
}

.status-dropdown[data-current-status="menunggu_validasi"] {
    background-color: #fff3cd !important;
    color: #856404 !important;
    border-color: #ffeaa7 !important;
}

.status-dropdown[data-current-status="divalidasi_sedang_diproses"] {
    background-color: #d1ecf1 !important;
    color: #0c5460 !important;
    border-color: #bee5eb !important;
}

.status-dropdown[data-current-status="menunggu_pembayaran"] {
    background-color: #e2e3e5 !important;
    color: #41464b !important;
    border-color: #d6d8db !important;
}

.status-dropdown[data-current-status="menunggu_verifikasi_pembayaran"] {
    background-color: #fce4ec !important;
    color: #880e4f !important;
    border-color: #f8bbd9 !important;
}

.status-dropdown[data-current-status="selesai"] {
    background-color: #d4edda !important;
    color: #155724 !important;
    border-color: #c3e6cb !important;
}

.status-dropdown[data-current-status="ditolak"] {
    background-color: #f8d7da !important;
    color: #721c24 !important;
    border-color: #f5c6cb !important;
}

/* Dropdown option colors */
.status-dropdown option[data-status="menunggu_validasi"] {
    background-color: #fff3cd;
    color: #856404;
}

.status-dropdown option[data-status="divalidasi_sedang_diproses"] {
    background-color: #d1ecf1;
    color: #0c5460;
}

.status-dropdown option[data-status="menunggu_pembayaran"] {
    background-color: #e2e3e5;
    color: #41464b;
}

.status-dropdown option[data-status="menunggu_verifikasi_pembayaran"] {
    background-color: #fce4ec;
    color: #880e4f;
}

.status-dropdown option[data-status="selesai"] {
    background-color: #d4edda;
    color: #155724;
}

.status-dropdown option[data-status="ditolak"] {
    background-color: #f8d7da;
    color: #721c24;
}

.status-dropdown:focus {
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Status Filter Dropdown Colors */
.status-filter-dropdown {
    font-weight: 500;
    transition: all 0.3s ease;
}

.status-filter-dropdown[data-current-status="menunggu_validasi"] {
    background-color: #fff3cd !important;
    color: #856404 !important;
    border-color: #ffeaa7 !important;
}

.status-filter-dropdown[data-current-status="divalidasi_sedang_diproses"] {
    background-color: #d1ecf1 !important;
    color: #0c5460 !important;
    border-color: #bee5eb !important;
}

.status-filter-dropdown[data-current-status="menunggu_pembayaran"] {
    background-color: #e2e3e5 !important;
    color: #41464b !important;
    border-color: #d6d8db !important;
}

.status-filter-dropdown[data-current-status="menunggu_verifikasi_pembayaran"] {
    background-color: #fce4ec !important;
    color: #880e4f !important;
    border-color: #f8bbd9 !important;
}

.status-filter-dropdown[data-current-status="selesai"] {
    background-color: #d4edda !important;
    color: #155724 !important;
    border-color: #c3e6cb !important;
}

.status-filter-dropdown[data-current-status="ditolak"] {
    background-color: #f8d7da !important;
    color: #721c24 !important;
    border-color: #f5c6cb !important;
}

.status-filter-dropdown[data-current-status=""] {
    background-color: #f8f9fa !important;
    color: #495057 !important;
    border-color: #ced4da !important;
}

/* Status Action Buttons */
.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    line-height: 1.2;
}

.btn-sm i {
    font-size: 0.7rem;
}

.d-flex.gap-1 {
    gap: 0.25rem !important;
}

/* Status badges for final states */
.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
}

/* Status control improvements */
.status-dropdown {
    min-width: 180px;
}

.btn[title] {
    position: relative;
}

/* Better spacing for status controls */
.status-control-group {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    flex-wrap: nowrap;
}

/* Disabled state for completed items */
.status-final {
    opacity: 0.8;
}
</style>

<script>
// Status Management Script
document.addEventListener('DOMContentLoaded', function() {
    // Status order definition
    const statusOrder = {
        'menunggu_validasi': 1,
        'divalidasi_sedang_diproses': 2,
        'menunggu_pembayaran': 3,
        'menunggu_verifikasi_pembayaran': 4,
        'selesai': 5,
        'ditolak': 99
    };

    // Status labels for user feedback
    const statusLabels = {
        'menunggu_validasi': 'Menunggu Validasi',
        'divalidasi_sedang_diproses': 'Divalidasi & Sedang Diproses',
        'menunggu_pembayaran': 'Menunggu Pembayaran',
        'menunggu_verifikasi_pembayaran': 'Menunggu Verifikasi Pembayaran',
        'selesai': 'Selesai',
        'ditolak': 'Ditolak'
    };

    // Add confirmation for status updates
    const updateForms = document.querySelectorAll('form[action*="updateStatus"]');
    updateForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const select = form.querySelector('select[name="status"]');
            const hiddenStatus = form.querySelector('input[name="status"]');
            
            if (select) {
                const newStatus = select.value;
                const currentStatus = select.dataset.currentStatus;
                
                if (newStatus !== currentStatus) {
                    const currentLabel = statusLabels[currentStatus] || currentStatus;
                    const newLabel = statusLabels[newStatus] || newStatus;
                    
                    if (!confirm(`Ubah status dari "${currentLabel}" ke "${newLabel}"?\n\nPerubahan ini akan memajukan proses pengajuan.`)) {
                        e.preventDefault();
                        return false;
                    }
                }
            } else if (hiddenStatus && hiddenStatus.value === 'ditolak') {
                if (!confirm('Yakin ingin menolak pengajuan ini?\n\nPengajuan yang ditolak dapat di-reset kembali jika diperlukan.')) {
                    e.preventDefault();
                    return false;
                }
            }
        });
    });

    // Update dropdown colors when selection changes
    const statusDropdowns = document.querySelectorAll('.status-dropdown');
    statusDropdowns.forEach(dropdown => {
        dropdown.addEventListener('change', function() {
            this.setAttribute('data-current-status', this.value);
        });
    });

    // Update filter dropdown colors when changed
    const filterDropdowns = document.querySelectorAll('.status-filter-dropdown');
    filterDropdowns.forEach(dropdown => {
        dropdown.addEventListener('change', function() {
            this.setAttribute('data-current-status', this.value);
        });
    });

    // Add tooltips to status badges
    const statusBadges = document.querySelectorAll('.badge');
    statusBadges.forEach(badge => {
        if (badge.textContent.includes('Selesai')) {
            badge.title = 'Pengajuan telah selesai diproses';
        } else if (badge.textContent.includes('Ditolak')) {
            badge.title = 'Pengajuan ditolak - dapat di-reset untuk diproses ulang';
        }
    });
});
</script>


@endsection 