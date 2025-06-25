@extends('layouts.app')

@section('content')
<x-page-header 
    title="Draft Ciptaan" 
    description="Kelola draft usulan HKI sebelum dikirim"
    icon="fas fa-pencil-alt"
    :breadcrumbs="[
        ['title' => 'Hak Cipta', 'url' => '#'],
        ['title' => 'Daftar Ciptaan (Draft)']
    ]"
/>

<div class="container-fluid px-4">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 pt-4 pb-0">
            <h5 class="mb-0 fw-semibold text-dark">
                <i class="fas fa-list me-2 text-primary"></i>
                Daftar Ciptaan (Draft)
                @if($drafts->count() > 0)
                    <span class="badge bg-light text-dark ms-2">{{ $drafts->count() }} total</span>
                @endif
            </h5>
        </div>
        <div class="card-body p-0">
            @if(session('success'))
                <div class="alert alert-success m-4">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger m-4">{{ session('error') }}</div>
            @endif
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
                            <th class="border-0 py-3 fw-semibold text-muted">Tanggal Draft</th>
                            <th class="border-0 py-3 fw-semibold text-muted text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($drafts as $draft)
                        <tr class="border-bottom">
                            <td class="ps-4 py-3">{{ $loop->iteration }}</td>
                            <td class="py-3">{{ $draft->judul_karya }}</td>
                            <td class="py-3">{{ $draft->identitas_ciptaan }}</td>
                            <td class="py-3">{{ $draft->sub_jenis_ciptaan }}</td>
                            <td class="py-3">{{ $draft->tahun_usulan ?? '-' }}</td>
                            <td class="py-3">{{ $draft->jumlah_pencipta }}</td>
                            <td class="py-3"><span class="badge bg-secondary px-3 py-2">Draft</span></td>
                            <td class="py-3">{{ $draft->created_at ? $draft->created_at->format('d/m/Y H:i') . ' WITA' : '-' }}</td>
                            <td class="py-3 text-center">
                                <div class="btn-group" role="group" aria-label="Aksi Draft">
                                    <a href="{{ route('draft.edit', $draft->id) }}" class="btn btn-warning btn-sm" title="Edit Draft">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </a>
                                <form action="{{ route('draft.destroy', $draft->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus draft ini?')" title="Hapus Draft">
                                        <i class="fas fa-trash me-1"></i>Hapus
                                    </button>
                                </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="fas fa-inbox text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mb-0 mt-2">Belum ada draft.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
/* Reuse persetujuan custom styles */
.card{transition:all .3s ease}.card:hover{transform:translateY(-2px)}.table-responsive{border-radius:.5rem}.table th{font-weight:600;text-transform:uppercase;font-size:.75rem;letter-spacing:.5px}.table td{vertical-align:middle}.badge{font-weight:500;letter-spacing:.25px}.btn{font-weight:500;border-radius:.375rem;transition:all .2s ease}.btn:hover{transform:translateY(-1px)}.modal-content{border-radius:1rem}.modal-header{border-radius:1rem 1rem 0 0}.form-control:focus{border-color:#0d6efd;box-shadow:0 0 0 .2rem rgba(13,110,253,.25)}.input-group-text{background-color:#f8f9fa;border-color:#dee2e6}.pagination .page-link{border-radius:.375rem;margin:0 2px;border:none;color:#6c757d}.pagination .page-item.active .page-link{background-color:#0d6efd;border-color:#0d6efd}.pagination .page-link:hover{background-color:#e9ecef;color:#0d6efd}

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
</style>

@endsection 