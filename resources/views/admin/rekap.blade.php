@extends('layouts.app')

@section('title', 'Rekapitulasi Dokumen DJKI')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-primary">
                        <i class="fas fa-archive me-2"></i>Rekapitulasi Dokumen DJKI
                    </h3>
                    <p class="text-muted mb-0">Kelola dokumen yang siap diserahkan ke Direktorat Jenderal Kekayaan Intelektual</p>
                </div>
                <div>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bulkSubmissionModal">
                        <i class="fas fa-paper-plane me-2"></i>Kirim Batch ke DJKI
                    </button>
                </div>
            </div>

            <!-- Statistik -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="bg-success text-white rounded-circle p-3 me-3">
                                    <i class="fas fa-check-circle fa-lg"></i>
                                </div>
                                <div>
                                    <h4 class="fw-bold mb-0">{{ $pengajuanSiap->count() }}</h4>
                                    <small class="text-muted">Siap Serah DJKI</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="bg-warning text-white rounded-circle p-3 me-3">
                                    <i class="fas fa-clock fa-lg"></i>
                                </div>
                                <div>
                                    <h4 class="fw-bold mb-0">{{ $pengajuanProgress->count() }}</h4>
                                    <small class="text-muted">Dalam Proses</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="bg-info text-white rounded-circle p-3 me-3">
                                    <i class="fas fa-signature fa-lg"></i>
                                </div>
                                <div>
                                    <h4 class="fw-bold mb-0">{{ $pengajuanSiap->sum(fn($p) => $p->signatures->count()) }}</h4>
                                    <small class="text-muted">Total Tanda Tangan</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary text-white rounded-circle p-3 me-3">
                                    <i class="fas fa-file-pdf fa-lg"></i>
                                </div>
                                <div>
                                    <h4 class="fw-bold mb-0">{{ $pengajuanSiap->count() * 3 }}</h4>
                                    <small class="text-muted">Dokumen Siap</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Navigation -->
            <ul class="nav nav-tabs mb-4" id="rekapTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="siap-tab" data-bs-toggle="tab" data-bs-target="#siap" type="button" role="tab">
                        <i class="fas fa-check-circle me-2"></i>Siap Serah DJKI ({{ $pengajuanSiap->count() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="progress-tab" data-bs-toggle="tab" data-bs-target="#progress" type="button" role="tab">
                        <i class="fas fa-clock me-2"></i>Dalam Proses ({{ $pengajuanProgress->count() }})
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="rekapTabsContent">
                <!-- Siap Serah DJKI Tab -->
                <div class="tab-pane fade show active" id="siap" role="tabpanel">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="fas fa-check-circle text-success me-2"></i>Pengajuan Siap Diserahkan ke DJKI
                                </h6>
                                <div>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="selectAllReady()">
                                        <i class="fas fa-check-square me-1"></i>Pilih Semua
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm" onclick="generateBulkDocuments()">
                                        <i class="fas fa-download me-1"></i>Download Paket
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            @if($pengajuanSiap->isNotEmpty())
                                <form id="bulkActionForm" action="{{ route('admin.mark-submitted-djki') }}" method="POST">
                                    @csrf
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="40">
                                                        <input type="checkbox" class="form-check-input" id="selectAllCheckbox">
                                                    </th>
                                                    <th>Pengajuan</th>
                                                    <th>Pengusul</th>
                                                    <th>Pencipta</th>
                                                    <th>Status Dokumen</th>
                                                    <th>Tanggal Update</th>
                                                    <th width="200">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($pengajuanSiap as $pengajuan)
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" class="form-check-input pengajuan-checkbox" 
                                                               name="pengajuan_ids[]" value="{{ $pengajuan->id }}">
                                                    </td>
                                                    <td>
                                                        <div>
                                                            <h6 class="mb-1">{{ $pengajuan->judul_karya }}</h6>
                                                            <small class="text-muted">ID: {{ $pengajuan->id }}</small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div>
                                                            <strong>{{ $pengajuan->user->nama_lengkap }}</strong><br>
                                                            <small class="text-muted">{{ $pengajuan->user->email }}</small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <span class="badge bg-success me-2">{{ $pengajuan->signatures->count() }}</span>
                                                            <small>Pencipta</small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="small">
                                                            <div class="text-success">
                                                                <i class="fas fa-check me-1"></i>Surat Pengalihan
                                                            </div>
                                                            <div class="text-success">
                                                                <i class="fas fa-check me-1"></i>Surat Pernyataan
                                                            </div>
                                                            <div class="text-success">
                                                                <i class="fas fa-check me-1"></i>Form Permohonan
                                                            </div>
                                                            <div class="text-success">
                                                                <i class="fas fa-check me-1"></i>KTP Gabungan
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <small>{{ $pengajuan->updated_at->format('d/m/Y H:i') }}</small>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm" role="group">
                                                            <a href="{{ route('admin.generate-combined-document', $pengajuan->id) }}" 
                                                               class="btn btn-outline-primary" title="Download Dokumen">
                                                                <i class="fas fa-download"></i>
                                                            </a>
                                                            <a href="{{ route('pengajuan.show', $pengajuan->id) }}" 
                                                               class="btn btn-outline-info" title="Detail">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="{{ route('tracking.show', $pengajuan->id) }}" 
                                                               class="btn btn-outline-secondary" title="Tracking">
                                                                <i class="fas fa-route"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </form>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Belum ada pengajuan yang siap diserahkan</h5>
                                    <p class="text-muted">Pengajuan akan muncul di sini setelah direktur menandatangani semua dokumen</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Dalam Proses Tab -->
                <div class="tab-pane fade" id="progress" role="tabpanel">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="fas fa-clock text-warning me-2"></i>Pengajuan Dalam Proses
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            @if($pengajuanProgress->isNotEmpty())
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Pengajuan</th>
                                                <th>Pengusul</th>
                                                <th>Status</th>
                                                <th>Progress Tanda Tangan</th>
                                                <th>Tanggal Update</th>
                                                <th width="120">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($pengajuanProgress as $pengajuan)
                                            <tr>
                                                <td>
                                                    <div>
                                                        <h6 class="mb-1">{{ $pengajuan->judul_karya }}</h6>
                                                        <small class="text-muted">ID: {{ $pengajuan->id }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong>{{ $pengajuan->user->nama_lengkap }}</strong><br>
                                                        <small class="text-muted">{{ $pengajuan->user->email }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    @php
                                                        $statusConfig = [
                                                            'menunggu_tanda_tangan' => ['class' => 'warning', 'text' => 'Menunggu TTD Pencipta'],
                                                            'menunggu_persetujuan_direktur' => ['class' => 'info', 'text' => 'Menunggu Direktur'],
                                                            'disetujui_direktur' => ['class' => 'primary', 'text' => 'Disetujui Direktur']
                                                        ];
                                                        $status = $statusConfig[$pengajuan->status] ?? ['class' => 'secondary', 'text' => ucfirst($pengajuan->status)];
                                                    @endphp
                                                    <span class="badge bg-{{ $status['class'] }}">{{ $status['text'] }}</span>
                                                </td>
                                                <td>
                                                    @php
                                                        $totalSignatures = $pengajuan->signatures->count();
                                                        $signedSignatures = $pengajuan->signatures->where('status', 'signed')->count();
                                                        $progress = $totalSignatures > 0 ? round(($signedSignatures / $totalSignatures) * 100) : 0;
                                                    @endphp
                                                    <div class="progress" style="height: 6px;">
                                                        <div class="progress-bar bg-success" style="width: {{ $progress }}%"></div>
                                                    </div>
                                                    <small class="text-muted">{{ $signedSignatures }}/{{ $totalSignatures }} ({{ $progress }}%)</small>
                                                </td>
                                                <td>
                                                    <small>{{ $pengajuan->updated_at->format('d/m/Y H:i') }}</small>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <a href="{{ route('pengajuan.show', $pengajuan->id) }}" 
                                                           class="btn btn-outline-info" title="Detail">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('tracking.show', $pengajuan->id) }}" 
                                                           class="btn btn-outline-secondary" title="Tracking">
                                                            <i class="fas fa-route"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-hourglass-half fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Tidak ada pengajuan dalam proses</h5>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Bulk Submission -->
<div class="modal fade" id="bulkSubmissionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tandai Sebagai Diserahkan ke DJKI</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.mark-submitted-djki') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nomor_submisi" class="form-label">Nomor Submisi DJKI</label>
                        <input type="text" class="form-control" id="nomor_submisi" name="nomor_submisi" 
                               placeholder="Contoh: DJKI/2024/001" required>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_submisi" class="form-label">Tanggal Submisi</label>
                        <input type="date" class="form-control" id="tanggal_submisi" name="tanggal_submisi" 
                               value="{{ date('Y-m-d') }}" required>
                    </div>
                    <input type="hidden" id="modalPengajuanIds" name="pengajuan_ids">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Tandai Sebagai Diserahkan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Checkbox selection handlers
document.getElementById('selectAllCheckbox').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.pengajuan-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

function selectAllReady() {
    const checkboxes = document.querySelectorAll('.pengajuan-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
    document.getElementById('selectAllCheckbox').checked = true;
}

function generateBulkDocuments() {
    const selectedIds = [];
    document.querySelectorAll('.pengajuan-checkbox:checked').forEach(checkbox => {
        selectedIds.push(checkbox.value);
    });
    
    if (selectedIds.length === 0) {
        alert('Pilih minimal satu pengajuan');
        return;
    }
    
    // Create form and submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin.generate-bulk-combined-documents") }}';
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = csrfToken;
    form.appendChild(csrfInput);
    
    selectedIds.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'pengajuan_ids[]';
        input.value = id;
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

// Handle bulk submission modal
document.getElementById('bulkSubmissionModal').addEventListener('show.bs.modal', function() {
    const selectedIds = [];
    document.querySelectorAll('.pengajuan-checkbox:checked').forEach(checkbox => {
        selectedIds.push(checkbox.value);
    });
    
    if (selectedIds.length === 0) {
        alert('Pilih minimal satu pengajuan');
        return;
    }
    
    document.getElementById('modalPengajuanIds').value = JSON.stringify(selectedIds);
});
</script>
@endsection 