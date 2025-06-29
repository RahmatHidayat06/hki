@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
<x-page-header 
    title="Wizard Validasi" 
    description="Proses validasi dan tanda tangan dokumen untuk {{ $pengajuan->judul_karya }}"
    icon="fas fa-magic"
    :breadcrumbs="[
        ['title' => 'Persetujuan HKI', 'url' => route('persetujuan.index')],
        ['title' => 'Detail Pengajuan', 'url' => route('persetujuan.show', $pengajuan->id)],
        ['title' => 'Wizard Validasi']
    ]"
/>

<div class="container-fluid px-4">
    <!-- Progress Indicator -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h5 class="card-title mb-3">
                <i class="fas fa-list-check me-2 text-primary"></i>Progress Validasi
            </h5>
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="progress-step completed">
                        <div class="step-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="step-content">
                            <h6 class="mb-1">Pengajuan Dibuat</h6>
                            <small class="text-muted">{{ $pengajuan->created_at->format('d M Y, H:i') }}</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="progress-step {{ $pengajuan->status == 'menunggu_validasi' ? 'active' : 'pending' }}">
                        <div class="step-icon">
                            <i class="fas fa-stamp"></i>
                        </div>
                        <div class="step-content">
                            <h6 class="mb-1">Validasi Direktur</h6>
                            <small class="text-muted">Sedang Berlangsung</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="progress-step pending">
                        <div class="step-icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="step-content">
                            <h6 class="mb-1">Review Admin</h6>
                            <small class="text-muted">Menunggu</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="progress-step pending">
                        <div class="step-icon">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <div class="step-content">
                            <h6 class="mb-1">Selesai</h6>
                            <small class="text-muted">Menunggu</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Informasi Pengajuan -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-gradient-info text-white border-0">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-info-circle me-2"></i>
                        Informasi Pengajuan HKI
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted fw-medium mb-1">ID Pengajuan</label>
                                <p class="text-dark fw-semibold">#{{ str_pad($pengajuan->id, 6, '0', STR_PAD_LEFT) }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted fw-medium mb-1">Nomor Pengajuan</label>
                                <p class="text-dark">{{ $pengajuan->nomor_pengajuan ?? 'Belum ditetapkan' }}</p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="info-item">
                                <label class="text-muted fw-medium mb-1">Judul Karya</label>
                                <p class="text-dark fw-semibold">{{ $pengajuan->judul_karya }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted fw-medium mb-1">Jenis Ciptaan</label>
                                <p class="text-dark">{{ $pengajuan->identitas_ciptaan ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted fw-medium mb-1">Sub Jenis Ciptaan</label>
                                <p class="text-dark">{{ $pengajuan->sub_jenis_ciptaan ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted fw-medium mb-1">Pemohon</label>
                                <p class="text-dark">{{ $pengajuan->user->name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted fw-medium mb-1">Email Pemohon</label>
                                <p class="text-dark">{{ $pengajuan->user->email }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted fw-medium mb-1">Tanggal Pengajuan</label>
                                <p class="text-dark">{{ $pengajuan->created_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted fw-medium mb-1">Tahun Usulan</label>
                                <p class="text-dark">{{ $pengajuan->tahun_usulan ?? date('Y') }}</p>
                            </div>
                        </div>
                        @if($pengajuan->jumlah_pencipta)
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted fw-medium mb-1">Jumlah Pencipta</label>
                                <p class="text-dark">{{ $pengajuan->jumlah_pencipta }} orang</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Informasi Pencipta -->
            @if($pengajuan->pencipta_data)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-gradient-success text-white border-0">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-users me-2"></i>
                        Informasi Pencipta
                    </h5>
                </div>
                <div class="card-body p-4">
                    @php
                        $penciptaData = json_decode($pengajuan->pencipta_data, true);
                    @endphp
                    @if($penciptaData && is_array($penciptaData))
                        <div class="row g-3">
                            @foreach($penciptaData as $index => $pencipta)
                            <div class="col-md-6">
                                <div class="pencipta-item border rounded-3 p-3 bg-light">
                                    <h6 class="mb-2 fw-semibold text-primary">
                                        <i class="fas fa-user me-1"></i>
                                        {{ $index == 0 ? 'Nama Pencipta' : 'Nama Pencipta ' . ($index + 1) }}
                                    </h6>
                                    <div class="row g-2">
                                        <div class="col-12">
                                            <small class="text-muted">Nama:</small>
                                            <p class="mb-1 fw-medium">{{ $pencipta['nama'] ?? '-' }}</p>
                                        </div>
                                        @if(isset($pencipta['alamat']))
                                        <div class="col-12">
                                            <small class="text-muted">Alamat:</small>
                                            <p class="mb-1">{{ $pencipta['alamat'] }}</p>
                                        </div>
                                        @endif
                                        @if(isset($pencipta['kewarganegaraan']))
                                        <div class="col-6">
                                            <small class="text-muted">Kewarganegaraan:</small>
                                            <p class="mb-1">{{ $pencipta['kewarganegaraan'] }}</p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-info-circle text-muted mb-2"></i>
                            <p class="text-muted mb-0">Data pencipta belum lengkap</p>
                        </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Data Pencipta (Relasi) -->
            @if(isset($pencipta) && $pencipta->count() > 0)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-gradient-success text-white border-0">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-users me-2"></i>
                        Data Pencipta
                    </h5>
                </div>
                <div class="card-body p-4">
                    @foreach($pencipta as $index => $creator)
                    <div class="pencipta-item {{ $index > 0 ? 'border-top pt-4 mt-4' : '' }}">
                        <h6 class="text-primary fw-bold mb-3">
                            <i class="fas fa-user-circle me-2"></i>
                            @if($index == 0)
                                Nama Pencipta
                            @else
                                Pencipta {{ $index + 1 }}
                            @endif
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-medium">Nama Lengkap</label>
                                <p class="mb-0 fw-semibold">{{ $creator->nama ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-medium">No. HP</label>
                                <p class="mb-0">{{ $creator->no_hp ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-medium">Email</label>
                                <p class="mb-0">{{ $creator->email ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-medium">Kecamatan</label>
                                <p class="mb-0">{{ $creator->kecamatan ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-medium">Kode Pos</label>
                                <p class="mb-0">{{ $creator->kodepos ?? '-' }}</p>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted small fw-medium">Alamat Lengkap</label>
                                <div class="p-3 bg-light rounded-3">
                                    <p class="mb-0">{{ $creator->alamat ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Dokumen yang Diupload -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-gradient-info text-white border-0">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-folder-open me-2"></i>
                        Dokumen yang Diupload
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        @foreach($documents as $docKey => $docData)
                        <div class="col-md-6">
                            <div class="document-item border rounded-3 p-3 h-100 {{ $docData['file_info'] ? 'bg-light' : 'bg-white border-dashed' }}">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="icon-box bg-{{ $docData['color'] }} bg-opacity-10 rounded-2 p-2">
                                            <i class="{{ $docData['icon'] }} text-{{ $docData['color'] }} fs-5"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-semibold">{{ $docData['label'] }}</h6>
                                        <p class="text-muted small mb-2">{{ $docData['description'] }}</p>
                                        
                                        @if($docData['file_info'])
                                            <div class="file-info">
                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <span class="badge bg-success text-white">
                                                        <i class="fas fa-check me-1"></i>Tersedia
                                                    </span>
                                                    <small class="text-muted">
                                                        {{ $docData['file_info']['extension'] ?? 'UNKNOWN' }} • 
                                                        {{ $docData['file_info']['size_formatted'] ?? 'Unknown size' }}
                                                    </small>
                                                </div>
                                                <div class="d-flex gap-2">
                                                    @if($docKey === 'contoh_ciptaan')
                                                        @if(isset($docData['file_info']['is_url']) && $docData['file_info']['is_url'])
                                                            <a href="{{ $pengajuan->file_karya }}" class="btn btn-outline-primary btn-sm" target="_blank">
                                                                <i class="fas fa-external-link-alt me-1"></i>Buka Link
                                                            </a>
                                                        @else
                                                            <a href="{{ Storage::url($pengajuan->file_karya) }}" class="btn btn-outline-primary btn-sm" target="_blank">
                                                                <i class="fas fa-eye me-1"></i>Lihat
                                                            </a>
                                                        @endif
                                                    @else
                                                        @if(isset($dokumen[$docKey]) && $dokumen[$docKey])
                                                            <a href="{{ Storage::url($dokumen[$docKey]) }}" class="btn btn-outline-primary btn-sm" target="_blank">
                                                                <i class="fas fa-eye me-1"></i>Lihat
                                                            </a>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <div class="text-center py-2">
                                                <i class="fas fa-exclamation-triangle text-warning mb-1"></i>
                                                <div class="text-muted small">Dokumen belum diupload</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="{{ route('persetujuan.show', $pengajuan->id) }}" class="btn btn-outline-primary">
                            <i class="fas fa-eye me-2"></i>Lihat Detail Lengkap
                        </a>
                        <a href="{{ route('persetujuan.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
                        </a>
                        @if($pengajuan->bukti_pembayaran)
                        <a href="{{ Storage::url($pengajuan->bukti_pembayaran) }}" class="btn btn-outline-success" target="_blank">
                            <i class="fas fa-receipt me-2"></i>Lihat Bukti Pembayaran
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Validation Panel -->
        <div class="col-lg-4">
            @if(auth()->user()->role === 'direktur' && $pengajuan->status == 'menunggu_validasi')
            <!-- Status Validasi -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-gradient-warning text-white border-0">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-clipboard-check me-2"></i>
                        Status Validasi
                    </h5>
                </div>
                <div class="card-body p-4">
                    @php
                        $pengalihanSigned = isset($dokumen['overlays']['surat_pengalihan']) && !empty($dokumen['overlays']['surat_pengalihan']);
                        $pernyataanSigned = isset($dokumen['overlays']['surat_pernyataan']) && !empty($dokumen['overlays']['surat_pernyataan']);
                        $allSigned = $pengalihanSigned && $pernyataanSigned;
                    @endphp
                    
                    <div class="validation-status mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fw-medium">Surat Pengalihan</span>
                            <span class="badge bg-{{ $pengalihanSigned ? 'success' : 'warning' }}">
                                <i class="fas fa-{{ $pengalihanSigned ? 'check' : 'clock' }} me-1"></i>
                                {{ $pengalihanSigned ? 'Ditandatangani' : 'Belum' }}
                            </span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fw-medium">Surat Pernyataan</span>
                            <span class="badge bg-{{ $pernyataanSigned ? 'success' : 'warning' }}">
                                <i class="fas fa-{{ $pernyataanSigned ? 'check' : 'clock' }} me-1"></i>
                                {{ $pernyataanSigned ? 'Ditandatangani' : 'Belum' }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="progress mb-3" style="height: 8px;">
                        <div class="progress-bar bg-{{ $allSigned ? 'success' : 'warning' }}" 
                             style="width: {{ ($pengalihanSigned + $pernyataanSigned) * 50 }}%"></div>
                    </div>
                    
                    <div class="text-center">
                        @if($allSigned)
                            <div class="alert alert-success py-2 mb-0">
                                <i class="fas fa-check-circle me-2"></i>
                                Semua dokumen telah ditandatangani, siap untuk divalidasi & sedang diproses
                            </div>
                        @else
                            <div class="alert alert-warning py-2 mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                {{ 2 - ($pengalihanSigned + $pernyataanSigned) }} dokumen belum ditandatangani
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Panel Tanda Tangan -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-gradient-primary text-white border-0">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-signature me-2"></i>
                        Panel Tanda Tangan
                    </h5>
                </div>
                <div class="card-body p-4">
                    <!-- Informasi File yang Akan Dihasilkan -->
                    <div class="alert alert-info border-0 mb-4">
                        <h6 class="alert-heading mb-2">
                            <i class="fas fa-info-circle me-2"></i>Informasi File
                        </h6>
                        <p class="mb-2 small">File yang ditandatangani akan disimpan dengan format:</p>
                        <code class="d-block bg-white p-2 rounded text-dark small">
                            {{ $pengajuan->id }}_[jenis_dokumen]_{{ str_replace(' ', '_', $pengajuan->user->name) }}_[timestamp].pdf
                        </code>
                        <small class="text-muted mt-2">
                            Contoh: {{ $pengajuan->id }}_surat_pengalihan_{{ str_replace(' ', '_', $pengajuan->user->name) }}_20241220_143052.pdf
                        </small>
                    </div>

                        <!-- Document Overlay Section -->
                        <div class="mb-4">
                            <label class="form-label fw-medium">
                                <i class="fas fa-stamp me-1"></i>Tempelkan Tanda Tangan
                            </label>
                            <div class="document-overlay-container border rounded-3 p-3 bg-light">
                                @php
                                    $availableDocs = [];
                                    $pengalihanPath = $dokumen['signed']['surat_pengalihan'] ?? ($dokumen['surat_pengalihan'] ?? null);
                                    $pernyataanPath = $dokumen['signed']['surat_pernyataan'] ?? ($dokumen['surat_pernyataan'] ?? null);

                                    if($pengalihanPath){
                                        $availableDocs['surat_pengalihan'] = [
                                            'label' => 'Surat Pengalihan',
                                            'file' => $pengalihanPath,
                                        'icon' => 'fas fa-file-signature',
                                        'signed' => $pengalihanSigned
                                        ];
                                    }
                                    if($pernyataanPath){
                                        $availableDocs['surat_pernyataan'] = [
                                            'label' => 'Surat Pernyataan', 
                                            'file' => $pernyataanPath,
                                        'icon' => 'fas fa-file-contract',
                                        'signed' => $pernyataanSigned
                                        ];
                                    }
                                @endphp
                                
                                @if(count($availableDocs) > 0)
                                    <div class="row g-3">
                                        @foreach($availableDocs as $docType => $docInfo)
                                    <div class="col-12">
                                            <div class="document-overlay-item border rounded-2 p-3 bg-white">
                                            <div class="d-flex align-items-center mb-3">
                                                    <i class="{{ $docInfo['icon'] }} text-primary me-2"></i>
                                                <h6 class="mb-0 fw-medium flex-grow-1">{{ $docInfo['label'] }}</h6>
                                                <span class="badge bg-{{ $docInfo['signed'] ? 'success' : 'warning' }}">
                                                    <i class="fas fa-{{ $docInfo['signed'] ? 'check' : 'clock' }} me-1"></i>
                                                    {{ $docInfo['signed'] ? 'Ditandatangani' : 'Belum' }}
                                                </span>
                                                </div>
                                                <div class="d-grid gap-2">
                                                    <a href="{{ route('persetujuan.signature.editor', [$pengajuan->id, $docType]) }}" 
                                                   class="btn btn-{{ $docInfo['signed'] ? 'success' : 'primary' }} btn-sm" target="_blank">
                                                    <i class="fas fa-{{ $docInfo['signed'] ? 'edit' : 'signature' }} me-1"></i>
                                                    {{ $docInfo['signed'] ? 'Edit Tanda Tangan' : 'Tempelkan Tanda Tangan' }}
                                                    </a>
                                                    <a href="{{ route('persetujuan.preview', [$pengajuan->id, $docType]) }}" 
                                                       class="btn btn-outline-secondary btn-sm" target="_blank">
                                                    <i class="fas fa-eye me-1"></i>Preview Dokumen
                                                    </a>
                                                @if($docInfo['signed'])
                                                <a href="{{ route('signed.document.serve', [$pengajuan->id, $docType]) }}" 
                                                   class="btn btn-outline-success btn-sm" target="_blank">
                                                    <i class="fas fa-download me-1"></i>Download Hasil
                                                </a>
                                                @endif
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    
                                @else
                                    <div class="text-center py-3">
                                        <i class="fas fa-exclamation-triangle text-warning fs-4 mb-2"></i>
                                    <p class="text-muted mb-0">Tidak ada dokumen yang tersedia untuk ditandatangani</p>
                                        <small class="text-muted">Surat pengalihan atau surat pernyataan belum diunggah</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                </div>
            </div>

            <!-- Panel Validasi -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-gradient-success text-white border-0">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-stamp me-2"></i>
                        Panel Validasi
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form id="validationForm" method="POST" action="{{ route('persetujuan.approve', $pengajuan->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Comment -->
                        <div class="mb-4">
                            <label class="form-label fw-medium">
                                <i class="fas fa-comment me-1"></i>Komentar untuk Pemohon
                            </label>
                            <textarea name="catatan_admin" class="form-control" rows="3" placeholder="Tambahkan komentar atau catatan untuk pemohon (opsional)..."></textarea>
                            <small class="text-muted">Komentar ini akan dikirim ke pemohon sebagai feedback</small>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-grid gap-2">
                            @php
                                $canApprove = isset($dokumen['overlays']['surat_pengalihan']) && !empty($dokumen['overlays']['surat_pengalihan']) && isset($dokumen['overlays']['surat_pernyataan']) && !empty($dokumen['overlays']['surat_pernyataan']);
                            @endphp
                            <button type="submit" class="btn btn-success btn-lg" id="approveBtn" data-can-approve="{{ $canApprove ? '1' : '0' }}">
                                <i class="fas fa-check me-2"></i>Validasi & Kirim ke Admin
                            </button>
                            <small class="text-muted text-center">
                                @if($canApprove)
                                    <i class="fas fa-check-circle text-success me-1"></i>
                                    Semua dokumen telah ditandatangani, siap untuk divalidasi
                                @else
                                    <i class="fas fa-exclamation-triangle text-warning me-1"></i>
                                    Pastikan semua dokumen telah ditandatangani sebelum validasi
                                @endif
                            </small>
                        </div>
                    </form>
                    
                    <!-- Reject Form -->
                    <form id="rejectForm" method="POST" action="{{ route('persetujuan.reject', $pengajuan->id) }}" class="mt-4">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label fw-medium text-danger">
                                <i class="fas fa-times me-1"></i>Alasan Penolakan
                            </label>
                            <textarea name="catatan_admin" class="form-control" rows="2" placeholder="Berikan alasan penolakan yang jelas..."></textarea>
                            <small class="text-muted">Alasan penolakan akan dikirim ke pemohon</small>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-danger btn-lg" onclick="return confirm('Yakin ingin menolak pengajuan ini? Tindakan ini tidak dapat dibatalkan.')">
                                <i class="fas fa-times me-2"></i>Tolak Pengajuan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
/* Progress Steps */
.progress-step {
    display: flex;
    align-items: center;
    position: relative;
}

.progress-step .step-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    font-size: 14px;
    font-weight: bold;
}

.progress-step.completed .step-icon {
    background: #28a745;
    color: white;
}

.progress-step.active .step-icon {
    background: #ffc107;
    color: #212529;
}

.progress-step.pending .step-icon {
    background: #e9ecef;
    color: #6c757d;
}

.progress-step .step-content h6 {
    margin: 0;
    font-size: 14px;
    font-weight: 600;
}

.progress-step .step-content small {
    font-size: 12px;
}

/* Pencipta Items */
.pencipta-item {
    transition: all 0.3s ease;
}

.pencipta-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

/* Validation Status */
.validation-status .badge {
    font-size: 11px;
}

/* Document Overlay Items */
.document-overlay-item {
    transition: all 0.3s ease;
    background: #ffffff;
}

.document-overlay-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.document-overlay-container {
    background: #f8f9fa;
}

/* Custom Gradient Backgrounds */
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #667eea 0%, #f093fb 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

/* Info Item Styles */
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

.info-item p {
    margin-bottom: 0;
    font-size: 0.95rem;
}

/* Card Hover Effects */
.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
}

/* Document Item Styles */
.border-dashed {
    border-style: dashed !important;
    border-width: 2px !important;
    border-color: #dee2e6 !important;
}

.document-item {
    transition: all 0.3s ease;
}

.document-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.icon-box {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.file-info .badge {
    font-size: 0.75rem;
}

/* Alert Styles */
.alert {
    border-radius: 0.5rem;
}

.alert-info {
    background: rgba(13, 202, 240, 0.1);
    border: 1px solid rgba(13, 202, 240, 0.2);
    color: #055160;
}

.alert-success {
    background: rgba(25, 135, 84, 0.1);
    border: 1px solid rgba(25, 135, 84, 0.2);
    color: #0a3622;
}

.alert-warning {
    background: rgba(255, 193, 7, 0.1);
    border: 1px solid rgba(255, 193, 7, 0.2);
    color: #664d03;
}

/* Code Styles */
code {
    font-size: 0.875rem;
    word-break: break-all;
}
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Custom approval logic
document.addEventListener('DOMContentLoaded', function(){
    const approveBtn = document.getElementById('approveBtn');
    if(!approveBtn) return;
    
    approveBtn.addEventListener('click', function(e){
            e.preventDefault();
        const canApprove = approveBtn.dataset.canApprove === '1';
        
        if(canApprove){
            Swal.fire({
                title: 'Validasi Pengajuan?',
                html: `
                    <div class="text-start">
                        <p>Dokumen yang akan dikirim ke admin:</p>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-2"></i>Surat Pengalihan (Bertanda tangan)</li>
                            <li><i class="fas fa-check text-success me-2"></i>Surat Pernyataan (Bertanda tangan)</li>
                        </ul>
                        <p class="text-muted small mt-3">Pengajuan akan diteruskan ke admin untuk proses selanjutnya.</p>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-check me-2"></i>Ya, Validasi',
                cancelButtonText: '<i class="fas fa-times me-2"></i>Batal',
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                reverseButtons: true
            }).then((result) => {
                if(result.isConfirmed){
                    // Show loading
                    Swal.fire({
                        title: 'Memproses Validasi...',
                        html: 'Sedang memproses dokumen dan mengirim ke admin',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    document.getElementById('validationForm').submit();
                }
            });
        } else {
            Swal.fire({
                title: 'Belum Dapat Divalidasi & Diproses',
                html: `
                    <div class="text-start">
                        <p>Pastikan semua dokumen telah ditandatangani:</p>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-${document.querySelector('[data-doc="surat_pengalihan"]') ? 'check text-success' : 'times text-danger'} me-2"></i>Surat Pengalihan</li>
                            <li><i class="fas fa-${document.querySelector('[data-doc="surat_pernyataan"]') ? 'check text-success' : 'times text-danger'} me-2"></i>Surat Pernyataan</li>
                        </ul>
                        <p class="text-muted small mt-3">Gunakan editor tanda tangan untuk menandatangani dokumen yang belum ditandatangani.</p>
                    </div>
                `,
                icon: 'warning',
                confirmButtonText: 'Mengerti',
                confirmButtonColor: '#ffc107'
            });
        }
    });

    // Handle reject form
    const rejectForm = document.getElementById('rejectForm');
    if(rejectForm) {
        rejectForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const reason = this.querySelector('textarea[name="catatan_admin"]').value.trim();
            
            if(!reason) {
                Swal.fire({
                    title: 'Alasan Penolakan Diperlukan',
                    text: 'Silakan berikan alasan penolakan yang jelas untuk pemohon.',
                    icon: 'warning',
                    confirmButtonText: 'Mengerti'
                });
                return;
            }
            
            Swal.fire({
                title: 'Tolak Pengajuan?',
                html: `
                    <div class="text-start">
                        <p>Alasan penolakan:</p>
                        <div class="alert alert-warning text-start">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            ${reason}
                        </div>
                        <p class="text-muted small">Tindakan ini tidak dapat dibatalkan dan pemohon akan menerima notifikasi penolakan.</p>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-times me-2"></i>Ya, Tolak',
                cancelButtonText: '<i class="fas fa-arrow-left me-2"></i>Batal',
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                reverseButtons: true
            }).then((result) => {
                if(result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Memproses Penolakan...',
                        html: 'Sedang memproses penolakan dan mengirim notifikasi',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
        }
    });
                    
                    rejectForm.submit();
                }
            });
        });
    }
});
</script>
@endpush 