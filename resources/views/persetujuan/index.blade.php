@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
<x-page-header 
    title="Persetujuan HKI" 
    description="Kelola dan setujui pengajuan Hak Kekayaan Intelektual"
    icon="fas fa-clipboard-check"
/>

<div class="container-fluid px-4">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                                <i class="fas fa-file-alt text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 fw-normal">Total Pengajuan</h6>
                            <h3 class="mb-0 fw-bold text-dark">{{ $stats['total'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                                <i class="fas fa-clock text-warning fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 fw-normal">Menunggu</h6>
                            <h3 class="mb-0 fw-bold text-dark">{{ $stats['pending'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-3 p-3">
                                <i class="fas fa-check-circle text-success fs-4"></i>
                            </div>
                        </div>
                                                 <div class="flex-grow-1 ms-3">
                             <h6 class="text-muted mb-1 fw-normal">Divalidasi & Sedang Diproses</h6>
                             <h3 class="mb-0 fw-bold text-dark">{{ $stats['approved'] ?? 0 }}</h3>
                         </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
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
                            <h3 class="mb-0 fw-bold text-dark">{{ $stats['rejected'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                        <option value="menunggu_validasi_direktur" {{ request('status') == 'menunggu_validasi_direktur' ? 'selected' : '' }}>
                            Menunggu Validasi Direktur
                        </option>
                                                                             <option value="divalidasi_sedang_diproses" {{ request('status') == 'divalidasi_sedang_diproses' ? 'selected' : '' }}>
                                Divalidasi & Sedang Diproses
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
                        <a href="{{ route('persetujuan.index') }}" class="btn btn-outline-secondary px-4">
                            <i class="fas fa-refresh me-2"></i>Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Main Data Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 pt-4 pb-0">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold text-dark">
                    <i class="fas fa-list me-2 text-primary"></i>
                    Daftar Pengajuan
                    @if($pengajuans->total() > 0)
                        <span class="badge bg-light text-dark ms-2">{{ $pengajuans->total() }} total</span>
                    @endif
                </h5>
                                 <div class="d-flex gap-2" id="bulkActions" style="display: none !important;">
                     <button class="btn btn-success btn-sm" onclick="showBulkApproveModal()">
                         <i class="fas fa-check me-1"></i>Validasi Terpilih
                     </button>
                     <button class="btn btn-danger btn-sm" onclick="showBulkRejectModal()">
                         <i class="fas fa-times me-1"></i>Tolak Terpilih
                     </button>
                 </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($pengajuans->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 ps-4 py-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                        <label class="form-check-label fw-medium text-muted" for="selectAll">
                                            Pilih Semua
                                        </label>
                                    </div>
                                </th>
                                <th class="border-0 py-3 fw-semibold text-muted">PENGAJUAN</th>
                                <th class="border-0 py-3 fw-semibold text-muted">NAMA PENCIPTA</th>
                                <th class="border-0 py-3 fw-semibold text-muted">STATUS</th>
                                <th class="border-0 py-3 fw-semibold text-muted">TANGGAL</th>
                                <th class="border-0 py-3 fw-semibold text-muted text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pengajuans as $pengajuan)
                            <tr class="border-bottom">
                                <td class="ps-4 py-3">
                                    <div class="form-check">
                                        <input class="form-check-input pengajuan-checkbox" type="checkbox" 
                                               value="{{ $pengajuan->id }}">
                                    </div>
                                </td>
                                <td class="py-3">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="bg-primary bg-opacity-10 rounded-2 p-2">
                                                <i class="fas fa-copyright text-primary"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fw-semibold text-dark">{{ $pengajuan->judul_karya }}</h6>
                                            <p class="mb-1 text-muted small">{{ Str::limit($pengajuan->deskripsi_karya, 80) }}</p>
                                            @if($pengajuan->nomor_pengajuan)
                                                <span class="badge bg-light text-dark small">
                                                    No: {{ $pengajuan->nomor_pengajuan }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3">
                                    @php $firstCreator = optional($pengajuan->pengaju->first()); @endphp
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-2">
                                            <div class="bg-secondary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                <i class="fas fa-user text-secondary small"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-medium text-dark">{{ $firstCreator->nama ?? '-' }}</div>
                                            @if(!empty($firstCreator->email))
                                            <div class="text-muted small text-truncate" title="{{ $firstCreator->email }}">{{ $firstCreator->email }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3">
                                    @if($pengajuan->status == 'menunggu_validasi_direktur')
                                        <span class="badge bg-warning text-dark px-3 py-2">
                                            <i class="fas fa-clock me-1"></i>Menunggu Validasi
                                        </span>
                                                                                                         @elseif(in_array($pengajuan->status, ['divalidasi_sedang_diproses']))
                                    <span class="badge bg-success px-3 py-2">
                                        <i class="fas fa-check-circle me-1"></i>Divalidasi & Sedang Diproses
                                    </span>
                                    @elseif($pengajuan->status == 'ditolak')
                                        <span class="badge bg-danger px-3 py-2">
                                            <i class="fas fa-times-circle me-1"></i>Ditolak
                                        </span>
                                    @elseif($pengajuan->status == 'selesai')
                                        <span class="badge bg-secondary px-3 py-2">
                                            <i class="fas fa-medal me-1"></i>Selesai
                                        </span>
                                    @else
                                        <span class="badge bg-secondary px-3 py-2">
                                            <i class="fas fa-question-circle me-1"></i>{{ ucfirst($pengajuan->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3">
                                    <div class="text-dark fw-medium">{{ $pengajuan->created_at->format('d M Y') }}</div>
                                    <div class="text-muted small">{{ $pengajuan->created_at->format('H:i') }}</div>
                                </td>
                                <td class="py-3 text-center">
                                    <div class="btn-group">
                                        <a href="{{ route('persetujuan.show', $pengajuan->id) }}" 
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye me-1"></i>Detail
                                        </a>
                                                                 @if($pengajuan->status == 'menunggu_validasi_direktur')
                        <div class="btn-group">
                                                                                      <a href="{{ route('persetujuan.validation.wizard', $pengajuan->id) }}" class="btn btn-success btn-sm">
                                 <i class="fas fa-clipboard-check me-1"></i>Validasi
                             </a>
                            <button type="button" class="btn btn-danger btn-sm" 
                                    data-id="{{ $pengajuan->id }}" 
                                    data-title="{{ $pengajuan->judul_karya }}"
                                    onclick="showRejectModal(this.dataset.id, this.dataset.title)">
                                <i class="fas fa-times me-1"></i>Tolak
                            </button>
                        </div>
                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($pengajuans->hasPages())
                <div class="card-footer bg-white border-0 px-4 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Menampilkan {{ $pengajuans->firstItem() }} sampai {{ $pengajuans->lastItem() }} 
                            dari {{ $pengajuans->total() }} pengajuan
                        </div>
                        <div>
                            {{ $pengajuans->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
                @endif
            @else
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-inbox text-muted" style="font-size: 4rem;"></i>
                    </div>
                    <h5 class="text-muted mb-2">Tidak ada pengajuan ditemukan</h5>
                    <p class="text-muted mb-0">
                        @if(request()->hasAny(['search', 'status', 'date_from', 'date_to']))
                            Coba ubah filter pencarian Anda
                        @else
                            Belum ada pengajuan HKI yang perlu disetujui
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white border-0">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle me-2"></i>Setujui Pengajuan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="approveForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                            <i class="fas fa-check text-success fs-3"></i>
                        </div>
                        <h6 class="mb-2">Apakah Anda yakin ingin menyetujui pengajuan ini?</h6>
                        <p class="text-muted mb-0" id="approveTitle"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Catatan Persetujuan (Opsional)</label>
                        <textarea name="catatan_admin" class="form-control" rows="3" 
                                  placeholder="Tambahkan catatan untuk pemohon..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-2"></i>Ya, Setujui
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title">
                    <i class="fas fa-times-circle me-2"></i>Tolak Pengajuan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                            <i class="fas fa-times text-danger fs-3"></i>
                        </div>
                        <h6 class="mb-2">Apakah Anda yakin ingin menolak pengajuan ini?</h6>
                        <p class="text-muted mb-0" id="rejectTitle"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium text-danger">Alasan Penolakan *</label>
                        <textarea name="catatan_admin" class="form-control" rows="3" required
                                  placeholder="Jelaskan alasan penolakan untuk pemohon..."></textarea>
                        <div class="form-text text-muted">Alasan penolakan wajib diisi untuk memberikan feedback kepada pemohon</div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-2"></i>Ya, Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Approve Modal -->
<div class="modal fade" id="bulkApproveModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white border-0">
                                 <h5 class="modal-title">
                     <i class="fas fa-check-circle me-2"></i>Validasi Multiple Pengajuan
                 </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
                         <form id="bulkApproveForm" method="POST" action="{{ route('persetujuan.bulkApprove') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                            <i class="fas fa-check text-success fs-3"></i>
                        </div>
                                                 <h6 class="mb-2">Validasi <span id="bulkApproveCount">0</span> pengajuan sekaligus?</h6>
                         <p class="text-muted mb-0">Semua pengajuan yang dipilih akan divalidasi</p>
                    </div>
                    <div class="mb-3">
                                                 <label class="form-label fw-medium">Catatan Validasi (Opsional)</label>
                         <textarea name="catatan_admin" class="form-control" rows="3" 
                                   placeholder="Tambahkan catatan untuk semua pemohon..."></textarea>
                    </div>
                    <input type="hidden" name="pengajuan_ids" id="bulkApproveIds">
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                                         <button type="submit" class="btn btn-success">
                         <i class="fas fa-check me-2"></i>Ya, Validasi Semua
                     </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Reject Modal -->
<div class="modal fade" id="bulkRejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title">
                    <i class="fas fa-times-circle me-2"></i>Tolak Multiple Pengajuan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
                         <form id="bulkRejectForm" method="POST" action="{{ route('persetujuan.bulkReject') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                            <i class="fas fa-times text-danger fs-3"></i>
                        </div>
                        <h6 class="mb-2">Tolak <span id="bulkRejectCount">0</span> pengajuan sekaligus?</h6>
                        <p class="text-muted mb-0">Semua pengajuan yang dipilih akan ditolak</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium text-danger">Alasan Penolakan *</label>
                        <textarea name="catatan_admin" class="form-control" rows="3" required
                                  placeholder="Jelaskan alasan penolakan untuk semua pemohon..."></textarea>
                        <div class="form-text text-muted">Alasan penolakan wajib diisi untuk memberikan feedback kepada semua pemohon</div>
                    </div>
                    <input type="hidden" name="pengajuan_ids" id="bulkRejectIds">
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-2"></i>Ya, Tolak Semua
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const pengajuanCheckboxes = document.querySelectorAll('.pengajuan-checkbox');
    const bulkActions = document.getElementById('bulkActions');
    
    // Handle select all functionality
    selectAllCheckbox.addEventListener('change', function() {
        pengajuanCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        toggleBulkActions();
    });
    
    // Handle individual checkbox changes
    pengajuanCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedCount = document.querySelectorAll('.pengajuan-checkbox:checked').length;
            selectAllCheckbox.checked = checkedCount === pengajuanCheckboxes.length;
            selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < pengajuanCheckboxes.length;
            toggleBulkActions();
        });
    });
    
    function toggleBulkActions() {
        const checkedBoxes = document.querySelectorAll('.pengajuan-checkbox:checked');
        if (checkedBoxes.length > 0) {
            bulkActions.style.display = 'flex';
        } else {
            bulkActions.style.display = 'none';
        }
    }
});

function showApproveModal(id, title) {
    document.getElementById('approveTitle').textContent = title;
    document.getElementById('approveForm').action = `/persetujuan/${id}/approve`;
    new bootstrap.Modal(document.getElementById('approveModal')).show();
}

function showRejectModal(id, title) {
    document.getElementById('rejectTitle').textContent = title;
    document.getElementById('rejectForm').action = `/persetujuan/${id}/reject`;
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}

function showBulkApproveModal() {
    const checkedBoxes = document.querySelectorAll('.pengajuan-checkbox:checked');
    const ids = Array.from(checkedBoxes).map(cb => cb.value);
    
    document.getElementById('bulkApproveCount').textContent = ids.length;
    document.getElementById('bulkApproveIds').value = ids.join(',');
    new bootstrap.Modal(document.getElementById('bulkApproveModal')).show();
}

function showBulkRejectModal() {
    const checkedBoxes = document.querySelectorAll('.pengajuan-checkbox:checked');
    const ids = Array.from(checkedBoxes).map(cb => cb.value);
    
    document.getElementById('bulkRejectCount').textContent = ids.length;
    document.getElementById('bulkRejectIds').value = ids.join(',');
    new bootstrap.Modal(document.getElementById('bulkRejectModal')).show();
}
</script>

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

/* Table Layout Fixes untuk kolom pemohon */
.table th:nth-child(3) { width: 20%; } /* Kolom PEMOHON */
.table td:nth-child(3) { 
    max-width: 200px; 
    min-width: 150px; 
}

.nama-user {
    max-width: 180px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .table th:nth-child(3), 
    .table td:nth-child(3) {
        max-width: 120px;
        min-width: 100px;
    }
    
    .nama-user {
        max-width: 100px;
    }
}
</style>
@endsection