@extends('layouts.app')

@section('content')
<x-page-header 
    title="Pembayaran Pengajuan HKI" 
    description="Kelola proses pembayaran dan unduh sertifikat"
    icon="fas fa-wallet"
/>

<div class="container-fluid px-4">

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 pt-4 pb-0">
            <h5 class="mb-0 fw-semibold text-dark">
                <i class="fas fa-list me-2 text-primary"></i>
                Daftar Pembayaran
                @if(!$pengajuans->isEmpty())
                    <span class="badge bg-light text-dark ms-2">{{ $pengajuans->count() }} total</span>
                @endif
            </h5>
        </div>
        <div class="card-body p-0">
            @if($pengajuans->isEmpty())
                <div class="alert alert-info">Belum ada riwayat pembayaran.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 py-3 fw-semibold text-muted">No</th>
                                <th class="border-0 py-3 fw-semibold text-muted">Judul Karya</th>
                                <th class="border-0 py-3 fw-semibold text-muted">Status</th>
                                <th class="border-0 py-3 fw-semibold text-muted text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pengajuans as $i => $p)
                                <tr class="border-bottom">
                                    <td class="ps-4 py-3">{{ $i+1 }}</td>
                                    <td class="py-3">{{ $p->judul_karya }}</td>
                                    <td class="py-3">
                                        @switch($p->status)
                                            @case('menunggu_pembayaran')
                                                <span class="badge bg-info text-dark px-3 py-2"><i class="fas fa-money-bill-wave me-1"></i>Menunggu Bayar</span>
                                                @break
                                            @case('menunggu_verifikasi_pembayaran')
                                                <span class="badge bg-info text-dark px-3 py-2"><i class="fas fa-hourglass-half me-1"></i>Verifikasi</span>
                                                @break
                                            @case('selesai')
                                                <span class="badge bg-success px-3 py-2"><i class="fas fa-flag-checkered me-1"></i>Selesai</span>
                                                @break
                                            @case('disetujui')
                                                <span class="badge bg-primary px-3 py-2"><i class="fas fa-thumbs-up me-1"></i>Disetujui</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary px-3 py-2">{{ ucfirst(str_replace('_',' ',$p->status)) }}</span>
                                        @endswitch
                                    </td>
                                    <td class="py-3 text-center">
                                        @php
                                            $canDownload = $p->status === 'selesai';
                                        @endphp
                                        @switch($p->status)
                                            @case('menunggu_pembayaran')
                                                <a href="{{ route('pembayaran.pay', $p->id) }}" class="btn btn-sm btn-success">
                                                    <i class="fas fa-wallet me-1"></i>Bayar Sekarang
                                                </a>
                                                @break
                                            @case('menunggu_verifikasi_pembayaran')
                                                @php
                                                    $extension = pathinfo($p->bukti_pembayaran, PATHINFO_EXTENSION);
                                                    $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']);
                                                    $isPdf = strtolower($extension) === 'pdf';
                                                    $fileType = $isImage ? 'image' : ($isPdf ? 'pdf' : 'other');
                                                    $fileName = basename($p->bukti_pembayaran);
                                                    $fileUrl = route('bukti.serve', $p->id);
                                                @endphp
                                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                                        onclick="previewPaymentProof('{{ $fileUrl }}', '{{ $fileType }}', '{{ $fileName }}')">
                                                    <i class="fas fa-eye me-1"></i>Preview Bukti
                                                </button>
                                                @break
                                            @case('selesai')
                                                @if($p->sertifikat)
                                                    <a href="{{ route('sertifikat.serve', $p->id) }}" class="btn btn-sm btn-success" title="Unduh Sertifikat" target="_blank">
                                                    <i class="fas fa-download me-1"></i>Unduh Sertifikat
                                                </a>
                                                @else
                                                    <span class="text-muted small">Sertifikat sedang diproses</span>
                                                @endif
                                                @break
                                            @case('disetujui')
                                                <span class="text-muted">Menunggu selesai</span>
                                                @break
                                            @default
                                                <span class="text-muted">-</span>
                                        @endswitch
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.card{transition:all .3s ease}.card:hover{transform:translateY(-2px)}.table-responsive{border-radius:.5rem}.table th{font-weight:600;text-transform:uppercase;font-size:.75rem;letter-spacing:.5px}.table td{vertical-align:middle}.badge{font-weight:500;letter-spacing:.25px}.btn{font-weight:500;border-radius:.375rem;transition:all .2s ease}.btn:hover{transform:translateY(-1px)}.form-control:focus{border-color:#0d6efd;box-shadow:0 0 0 .2rem rgba(13,110,253,.25)}.input-group-text{background-color:#f8f9fa;border-color:#dee2e6}
</style>

<!-- Modal Preview Bukti Pembayaran -->
<div class="modal fade" id="paymentProofModal" tabindex="-1" aria-labelledby="paymentProofModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentProofModalLabel">
                    <i class="fas fa-receipt me-2"></i>Preview Bukti Pembayaran
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div id="paymentProofContainer" style="min-height: 400px; background: #f8f9fa;" class="d-flex align-items-center justify-content-center">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Memuat preview...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <span class="text-muted small me-auto" id="paymentProofFileName"></span>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection 

@push('scripts')
<script>
function previewPaymentProof(url, type, filename) {
    const modal = new bootstrap.Modal(document.getElementById('paymentProofModal'));
    const container = document.getElementById('paymentProofContainer');
    const filenameElement = document.getElementById('paymentProofFileName');
    
    // Reset container
    container.innerHTML = '<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2 text-muted">Memuat preview...</p></div>';
    
    // Set filename
    filenameElement.textContent = filename;
    
    // Show modal
    modal.show();
    
    // Load content based on type
    setTimeout(() => {
        if (type === 'image') {
            container.innerHTML = `
                <div class="text-center p-3">
                    <img src="${url}" alt="Bukti Pembayaran" class="img-fluid rounded shadow" style="max-height: 70vh; max-width: 100%;">
                </div>
            `;
        } else if (type === 'pdf') {
            container.innerHTML = `
                <embed src="${url}" type="application/pdf" width="100%" height="600px" class="border-0">
            `;
        } else {
            container.innerHTML = `
                <div class="text-center p-5">
                    <i class="fas fa-file fa-3x text-muted mb-3"></i>
                    <h6 class="text-muted">Preview tidak tersedia untuk tipe file ini</h6>
                    <p class="text-muted">Silakan download file untuk melihat isinya</p>
                    <a href="${url}" target="_blank" class="btn btn-primary">
                        <i class="fas fa-external-link-alt me-1"></i>Buka File
                    </a>
                </div>
            `;
        }
    }, 300);
}
</script>
@endpush 