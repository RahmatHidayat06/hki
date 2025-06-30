@extends('layouts.app')

@section('content')
<x-page-header 
    title="{{ $pengajuan->judul_karya }}" 
    description="Detail pengajuan oleh {{ $pengajuan->user->name }} • {{ $pengajuan->created_at->format('d M Y, H:i') }}{{ $pengajuan->nomor_pengajuan ? ' • #' . $pengajuan->nomor_pengajuan : '' }}"
    icon="fas fa-eye"
    :breadcrumbs="[
        ['title' => 'Admin', 'url' => route('admin.dashboard')],
        ['title' => 'Pengajuan HKI', 'url' => route('admin.pengajuan')],
        ['title' => 'Detail #' . $pengajuan->id]
    ]"
/>

<div class="container-fluid px-4 premium-admin">

        <!-- Main Content -->
    <div class="row g-4">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Informasi Karya -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light border-0 py-3">
                    <h5 class="mb-0 fw-semibold text-dark">
                        <i class="fas fa-info-circle text-primary me-2"></i>Informasi Karya
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small fw-medium">Jenis Ciptaan</label>
                                <p class="mb-0 fw-medium">{{ $pengajuan->identitas_ciptaan ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small fw-medium">Sub Jenis Ciptaan</label>
                                <p class="mb-0 fw-medium">{{ $pengajuan->sub_jenis_ciptaan ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small fw-medium">Jumlah Pencipta</label>
                                <p class="mb-0 fw-medium">{{ $pengajuan->jumlah_pencipta ?? '-' }} orang</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small fw-medium">Tanggal Diumumkan</label>
                                <p class="mb-0 fw-medium">
                                    {{ $pengajuan->tanggal_pertama_kali_diumumkan ? \Carbon\Carbon::parse($pengajuan->tanggal_pertama_kali_diumumkan)->format('d M Y') : '-' }}
                                </p>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-0">
                                <label class="form-label text-muted small fw-medium">Deskripsi Karya</label>
                                <div class="p-3 bg-light rounded-3">
                                    <p class="mb-0">{{ $pengajuan->deskripsi ?? 'Tidak ada deskripsi' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Pengusul -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light border-0 py-3">
                    <h5 class="mb-0 fw-semibold text-dark">
                        <i class="fas fa-user text-info me-2"></i>Data Pengusul
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small fw-medium">Nama Pengusul</label>
                                <p class="mb-0 fw-semibold">{{ $pengajuan->nama_pengusul ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small fw-medium">Peran</label>
                                @if($pengajuan->role == 'dosen')
                                    <span class="badge bg-success">
                                        <i class="fas fa-chalkboard-teacher me-1"></i>Dosen
                                    </span>
                                @elseif($pengajuan->role == 'mahasiswa')
                                    <span class="badge bg-info">
                                        <i class="fas fa-graduation-cap me-1"></i>Mahasiswa
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small fw-medium">NIP / NIDN</label>
                                <p class="mb-0">{{ $pengajuan->nip_nidn ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small fw-medium">No. HP</label>
                                <p class="mb-0">{{ $pengajuan->no_hp ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-0">
                                <label class="form-label text-muted small fw-medium">ID SINTA</label>
                                <p class="mb-0">{{ $pengajuan->id_sinta ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Pencipta -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light border-0 py-3">
                    <h5 class="mb-0 fw-semibold text-dark">
                        <i class="fas fa-users text-warning me-2"></i>Data Pencipta
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if($pencipta && $pencipta->count() > 0)
                        @foreach($pencipta as $index => $creator)
                            <div class="pencipta-item {{ $index > 0 ? 'border-top pt-4 mt-4' : '' }}">
                                @if($pencipta->count() > 1)
                                    <h6 class="text-primary fw-bold mb-3">
                                        <i class="fas fa-user-circle me-2"></i>
                                        @if($index == 0)
                                            Nama Pencipta
                                        @else
                                            Pencipta {{ $index + 1 }}
                                        @endif
                                    </h6>
                                @else
                                    <h6 class="text-primary fw-bold mb-3">
                                        <i class="fas fa-user-circle me-2"></i>Nama Pencipta
                                    </h6>
                                @endif
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
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-user-slash text-muted fs-1 mb-3"></i>
                            <p class="text-muted mb-0">Data pencipta belum tersedia</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Informasi Pembayaran -->
            @if(in_array($pengajuan->status, ['menunggu_pembayaran', 'menunggu_verifikasi_pembayaran', 'selesai']))
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light border-0 py-3">
                    <h5 class="mb-0 fw-semibold text-dark">
                        <i class="fas fa-credit-card text-success me-2"></i>Informasi Pembayaran
                    </h5>
                </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <!-- Kode Billing -->
                                <div class="col-md-6">
                                    <div class="payment-info-item p-3 border rounded-3 bg-light">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-barcode fa-2x text-info me-3"></i>
                                            <div class="flex-grow-1">
                                                <h6 class="fw-semibold mb-1">Kode Billing</h6>
                                                <small class="text-muted">Kode untuk pembayaran PNBP</small>
                                            </div>
                                        </div>
                                        @if($pengajuan->billing_code)
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="billing-code p-2 bg-white rounded border">
                                                    <span class="fw-bold text-primary fs-5">{{ $pengajuan->billing_code }}</span>
                                                </div>
                                                <button class="btn btn-outline-secondary btn-sm ms-2" onclick="copyBilling('{{ $pengajuan->billing_code }}')">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                            <small class="text-success d-block mt-1" id="copyMsg" style="display:none;">Berhasil disalin!</small>
                                @else
                                            <div class="text-center py-2">
                                                <span class="text-muted">Belum tersedia</span>
                                                @if(auth()->user()->role === 'admin')
                                                    <button type="button" class="btn btn-sm btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#billingModal">
                                                        <i class="fas fa-plus me-1"></i>Set Billing Code
                                                    </button>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Status Pembayaran -->
                                <div class="col-md-6">
                                    <div class="payment-info-item p-3 border rounded-3 bg-light">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-wallet fa-2x text-warning me-3"></i>
                                            <div>
                                                <h6 class="fw-semibold mb-1">Status Pembayaran</h6>
                                                <small class="text-muted">Status proses pembayaran</small>
                                            </div>
                                        </div>
                                        <div class="text-center py-2">
                                            @switch($pengajuan->status)
                                                @case('menunggu_pembayaran')
                                                    <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                                                        <i class="fas fa-clock me-1"></i>Menunggu Pembayaran
                                                    </span>
                                                    @break
                                                @case('menunggu_verifikasi_pembayaran')
                                                    <span class="badge bg-info text-white fs-6 px-3 py-2">
                                                        <i class="fas fa-hourglass-half me-1"></i>Menunggu Verifikasi
                                                    </span>
                                                    @break
                                                @case('selesai')
                                                    <span class="badge bg-success fs-6 px-3 py-2">
                                                        <i class="fas fa-check-circle me-1"></i>Pembayaran Lunas
                                                    </span>
                                                    @break
                                            @endswitch
                                        </div>
                                    </div>
                                </div>

                                <!-- Bukti Pembayaran -->
                                @if($pengajuan->bukti_pembayaran)
                                <div class="col-12">
                                    <div class="payment-info-item p-3 border rounded-3 bg-light">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="fas fa-receipt fa-2x text-success me-3"></i>
                                            <div>
                                                <h6 class="fw-semibold mb-1">Bukti Pembayaran</h6>
                                                <small class="text-muted">File bukti pembayaran yang diunggah</small>
                                            </div>
                                        </div>
                                        @php
                                            $extension = pathinfo($pengajuan->bukti_pembayaran, PATHINFO_EXTENSION);
                                            $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']);
                                            $isPdf = strtolower($extension) === 'pdf';
                                            $fileType = $isImage ? 'image' : ($isPdf ? 'pdf' : 'other');
                                            $fileName = basename($pengajuan->bukti_pembayaran);
                                            $fileUrl = route('bukti.serve', $pengajuan->id);
                                        @endphp
                                        <div class="d-flex flex-wrap gap-2 align-items-center">
                                            <button type="button" class="btn btn-outline-primary btn-sm" 
                                                    onclick="previewPaymentProof('{{ $fileUrl }}', '{{ $fileType }}', '{{ $fileName }}')">
                                                <i class="fas fa-eye me-1"></i>Preview Bukti
                                            </button>
                                            <a href="{{ $fileUrl }}" class="btn btn-outline-success btn-sm" target="_blank">
                                                <i class="fas fa-external-link-alt me-1"></i>Buka File
                                            </a>
                                            <a href="{{ $fileUrl }}" class="btn btn-outline-info btn-sm" download>
                                                <i class="fas fa-download me-1"></i>Download
                                            </a>
                                            <span class="text-muted small ms-2">
                                                <i class="fas fa-file me-1"></i>{{ $fileName }}
                                                <span class="text-muted">•</span>
                                                <i class="fas fa-clock me-1"></i>Diunggah {{ $pengajuan->updated_at->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!-- Detail Biaya -->
                                <div class="col-12">
                                    <div class="payment-info-item p-3 border rounded-3 bg-white">
                                        <h6 class="fw-semibold mb-3">
                                            <i class="fas fa-calculator me-2 text-primary"></i>Detail Biaya
                                        </h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm mb-0">
                                                <tbody>
                                                    <tr>
                                                        <td class="border-0 py-2">Biaya Pendaftaran HKI</td>
                                                        <td class="border-0 py-2 text-end fw-semibold">Rp 200.000</td>
                                                    </tr>
                                                    <tr class="border-top">
                                                        <td class="py-2 fw-bold">Total Biaya</td>
                                                        <td class="py-2 text-end fw-bold text-primary fs-5">Rp 200.000</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Dokumen Pendukung -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light border-0 py-3">
                    <h5 class="mb-0 fw-semibold text-dark">
                        <i class="fas fa-folder-open text-secondary me-2"></i>Dokumen Pendukung
                    </h5>
                </div>
                        <div class="card-body p-4">
                    <div class="row g-3">
                        @foreach($documents as $field => $docInfo)
                            <div class="col-md-6">
                                @php
                                    // Extract file info helper
                                    $fileInfo = $docInfo['file_info'];
                                    $hasFile = $fileInfo['exists'] ?? false;
                                    $cardClass = $hasFile ? 'border-success bg-success bg-opacity-10' : 'border-danger bg-danger bg-opacity-10';
                                @endphp
                                <div class="border rounded-3 p-3 h-100 {{ $cardClass }}">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="{{ $docInfo['icon'] }} text-{{ $docInfo['color'] }} fs-5 me-2"></i>
                                        <h6 class="mb-0 fw-medium">{{ $docInfo['label'] }}</h6>
                                    </div>
                                    <p class="text-muted small mb-2">{{ $docInfo['description'] }}</p>

                                    @if($hasFile)
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i>Tersedia
                                            </span>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ $fileInfo['url'] }}?v={{ $pengajuan->updated_at->timestamp }}" target="_blank" class="btn btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ $fileInfo['url'] }}" download class="btn btn-outline-success">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            </div>
                                        </div>

                                        <div class="mt-2">
                                            <small class="text-muted">
                                                <i class="fas fa-file me-1"></i>{{ $fileInfo['filename'] ?? 'File' }}
                                                <span class="ms-2">
                                                    <i class="fas fa-weight me-1"></i>{{ $fileInfo['size_formatted'] ?? '-' }}
                                                </span>
                                            </small>
                                        </div>

                                        @if(($fileInfo['is_signed'] ?? false))
                                            <div class="mt-2">
                                                <span class="badge bg-info">
                                                    <i class="fas fa-key me-1"></i>Sudah Ditandatangani
                                                </span>
                                            </div>
                                        @endif
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times me-1"></i>Tidak Ada
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Baris 5: Status Overlay & Tanda Tangan Direktur -->
                                    @if(in_array($pengajuan->status, ['divalidasi_sedang_diproses', 'menunggu_pembayaran', 'menunggu_verifikasi_pembayaran', 'selesai']))
            <div class="row g-3 mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-gradient-warning text-white border-0">
                            <h5 class="mb-0 fw-semibold">
                                <i class="fas fa-stamp me-2"></i>
                                Status Overlay & Tanda Tangan Direktur
                            </h5>
                            <small class="opacity-75">Pembaruan surat yang telah diberi overlay dan ditandatangani</small>
                        </div>
                        <div class="card-body p-4">
                            @php
                                $hasOverlays = false;
                                $overlayPengalihan = isset($dokumen['overlays']['surat_pengalihan']) ? $dokumen['overlays']['surat_pengalihan'] : [];
                                $overlayPernyataan = isset($dokumen['overlays']['surat_pernyataan']) ? $dokumen['overlays']['surat_pernyataan'] : [];
                                
                                if(!empty($overlayPengalihan) || !empty($overlayPernyataan)) {
                                    $hasOverlays = true;
                                }
                            @endphp

                            @if($hasOverlays)
                                <div class="row g-4">
                                    <!-- Surat Pengalihan Overlay Status -->
                                    @if(!empty($overlayPengalihan))
                                    <div class="col-md-6">
                                        <div class="overlay-status-card border rounded-3 p-3 bg-light">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="overlay-icon bg-info bg-opacity-10 rounded-3 p-2 me-3">
                                                    <i class="fas fa-file-signature fa-2x text-info"></i>
                                                </div>
                                                <div>
                                                    <h6 class="fw-bold mb-1">Surat Pengalihan Hak</h6>
                                                    <small class="text-muted">Status overlay dan tanda tangan</small>
                                                </div>
                                            </div>

                                            <!-- Overlay Information -->
                                            <div class="overlay-info mb-3">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="fw-medium">Status Overlay:</span>
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check me-1"></i>{{ count($overlayPengalihan) }} Overlay Applied
                                                    </span>
                                                </div>
                                                
                                                @foreach($overlayPengalihan as $index => $overlay)
                                                <div class="overlay-item border rounded-2 p-2 mb-2 bg-white">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <small class="fw-medium text-dark">
                                                                @if($overlay['type'] === 'signature')
                                                                    <i class="fas fa-signature text-primary me-1"></i>Tanda Tangan
                                                                @else
                                                                    <i class="fas fa-stamp text-warning me-1"></i>Materai
                                                                @endif
                                                            </small>
                                                            <br>
                                                            <small class="text-muted">
                                                                Posisi: {{ number_format($overlay['x_percent'] ?? 0, 1) }}%, {{ number_format($overlay['y_percent'] ?? 0, 1) }}%
                                                            </small>
                                                        </div>
                                                        <small class="text-success fw-medium">Applied</small>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>

                                            <!-- Signed File Status -->
                                            <div class="signed-status">
                                                @php
                                                    // Check signed pengalihan file existence
                                                    $signedPengalihanExistsOverlay = false;
                                                    if(is_array($dokumen) && isset($dokumen['signed']['surat_pengalihan'])) {
                                                        $signedPath = $dokumen['signed']['surat_pengalihan'];
                                                        $normalizedPath = ltrim($signedPath, '/');
                                                        if (str_starts_with($normalizedPath, 'storage/')) {
                                                            $normalizedPath = substr($normalizedPath, strlen('storage/'));
                                                        }
                                                        $signedPengalihanExistsOverlay = Storage::disk('public')->exists($normalizedPath);
                                                    }
                                                @endphp
                                                @if($signedPengalihanExistsOverlay)
                                                    <div class="d-flex justify-content-between align-items-center p-2 bg-success bg-opacity-10 rounded-2">
                                                        <div>
                                                            <small class="fw-medium text-success">
                                                                <i class="fas fa-certificate me-1"></i>File Bertanda Tangan
                                                            </small>
                                                            <br>
                                                            <small class="text-muted">Siap untuk diunduh</small>
                                                        </div>
                                                        <a href="{{ route('admin.pengajuan.suratPengalihanSigned', $pengajuan->id) }}" 
                                                           class="btn btn-success btn-sm" target="_blank">
                                                            <i class="fas fa-download me-1"></i>Download
                                                        </a>
                                                    </div>
                                                @else
                                                    <div class="d-flex justify-content-between align-items-center p-2 bg-warning bg-opacity-10 rounded-2">
                                                        <div>
                                                            <small class="fw-medium text-warning">
                                                                <i class="fas fa-exclamation-triangle me-1"></i>File Signed Belum Tersedia
                                                            </small>
                                                            <br>
                                                            <small class="text-muted">Overlay sudah diterapkan, menunggu proses finalisasi</small>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Surat Pernyataan Overlay Status -->
                                    @if(!empty($overlayPernyataan))
                                    <div class="col-md-6">
                                        <div class="overlay-status-card border rounded-3 p-3 bg-light">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="overlay-icon bg-warning bg-opacity-10 rounded-3 p-2 me-3">
                                                    <i class="fas fa-file-contract fa-2x text-warning"></i>
                                                </div>
                                                <div>
                                                    <h6 class="fw-bold mb-1">Surat Pernyataan</h6>
                                                    <small class="text-muted">Status overlay dan tanda tangan</small>
                                                </div>
                                            </div>

                                            <!-- Overlay Information -->
                                            <div class="overlay-info mb-3">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="fw-medium">Status Overlay:</span>
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check me-1"></i>{{ count($overlayPernyataan) }} Overlay Applied
                                                    </span>
                                                </div>
                                                
                                                @foreach($overlayPernyataan as $index => $overlay)
                                                <div class="overlay-item border rounded-2 p-2 mb-2 bg-white">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <small class="fw-medium text-dark">
                                                                @if($overlay['type'] === 'signature')
                                                                    <i class="fas fa-signature text-primary me-1"></i>Tanda Tangan
                                                                @else
                                                                    <i class="fas fa-stamp text-warning me-1"></i>Materai
                                                                @endif
                                                            </small>
                                                            <br>
                                                            <small class="text-muted">
                                                                Posisi: {{ number_format($overlay['x_percent'] ?? 0, 1) }}%, {{ number_format($overlay['y_percent'] ?? 0, 1) }}%
                                                            </small>
                                                        </div>
                                                        <small class="text-success fw-medium">Applied</small>
                                                    </div>
                                                </div>
                                        @endforeach
                                            </div>

                                            <!-- Signed File Status -->
                                            <div class="signed-status">
                                                @php
                                                    // Check signed pernyataan file existence
                                                    $signedPernyataanExistsOverlay = false;
                                                    if(is_array($dokumen) && isset($dokumen['signed']['surat_pernyataan'])) {
                                                        $signedPath = $dokumen['signed']['surat_pernyataan'];
                                                        $normalizedPath = ltrim($signedPath, '/');
                                                        if (str_starts_with($normalizedPath, 'storage/')) {
                                                            $normalizedPath = substr($normalizedPath, strlen('storage/'));
                                                        }
                                                        $signedPernyataanExistsOverlay = Storage::disk('public')->exists($normalizedPath);
                                                    }
                                                @endphp
                                                @if($signedPernyataanExistsOverlay)
                                                    <div class="d-flex justify-content-between align-items-center p-2 bg-success bg-opacity-10 rounded-2">
                                                        <div>
                                                            <small class="fw-medium text-success">
                                                                <i class="fas fa-certificate me-1"></i>File Bertanda Tangan
                                                            </small>
                                                            <br>
                                                            <small class="text-muted">Siap untuk diunduh</small>
                                                        </div>
                                                        <a href="{{ route('admin.pengajuan.suratPernyataanSigned', $pengajuan->id) }}" 
                                                           class="btn btn-warning btn-sm" target="_blank">
                                                            <i class="fas fa-download me-1"></i>Download
                                                        </a>
                                                    </div>
                                                @else
                                                    <div class="d-flex justify-content-between align-items-center p-2 bg-warning bg-opacity-10 rounded-2">
                                                        <div>
                                                            <small class="fw-medium text-warning">
                                                                <i class="fas fa-exclamation-triangle me-1"></i>File Signed Belum Tersedia
                                                            </small>
                                                            <br>
                                                            <small class="text-muted">Overlay sudah diterapkan, menunggu proses finalisasi</small>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>

                                <!-- Summary Information -->
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="alert alert-info border-0 d-flex align-items-center">
                                            <i class="fas fa-info-circle fa-2x me-3"></i>
                                            <div>
                                                <strong>Informasi Overlay:</strong>
                                                <p class="mb-0">
                                                    Direktur telah menerapkan overlay (tanda tangan dan/atau materai) pada dokumen. 
                                                    File yang telah ditandatangani akan tersedia untuk diunduh setelah proses finalisasi selesai.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @else
                                <!-- No Overlays State -->
                                <div class="text-center py-5">
                                    <i class="fas fa-signature fa-4x text-muted mb-3"></i>
                                    <h6 class="fw-medium text-muted mb-2">Belum Ada Overlay</h6>
                                    <p class="text-muted mb-0">
                                        Direktur belum menerapkan tanda tangan atau materai pada dokumen ini.
                                        <br>Status pengajuan: <strong>{{ ucfirst(str_replace('_', ' ', $pengajuan->status)) }}</strong>
                                    </p>
                                </div>
                                @endif
                        </div>
                    </div>
                </div>
            </div>
                                @endif
        </div>

        <!-- Right Column -->
        <div class="col-lg-4 d-flex flex-column gap-4">
            <!-- Timeline Status -->
            <div class="card border-0 shadow-sm mb-4 order-lg-2">
                <div class="card-header bg-light border-0 py-3">
                    <h5 class="mb-0 fw-semibold text-dark">
                        <i class="fas fa-history text-primary me-2"></i>Timeline Status
                    </h5>
                </div>
                    <div class="card-body p-4">
                        <div class="timeline">
                            <div class="timeline-item active">
                                <div class="timeline-marker bg-primary"></div>
                                <div class="timeline-content">
                                    <h6 class="fw-semibold mb-1">Pengajuan Dibuat</h6>
                                    <p class="text-muted small mb-0">{{ $pengajuan->created_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>

                            <div class="timeline-item active">
                                <div class="timeline-marker bg-info"></div>
                                <div class="timeline-content">
                                    <h6 class="fw-semibold mb-1">Pengajuan Diperbarui</h6>
                                    <p class="text-muted small mb-0">{{ $pengajuan->updated_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>

                            @if(in_array($pengajuan->status, ['divalidasi_sedang_diproses']))
                            <div class="timeline-item active">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="fw-semibold mb-1">Pengajuan Divalidasi & Sedang Diproses</h6>
                                    <p class="text-muted small mb-0">{{ $pengajuan->tanggal_validasi ? $pengajuan->tanggal_validasi->format('d M Y, H:i') : $pengajuan->updated_at->format('d M Y, H:i') }}</p>
                                    @if($pengajuan->catatan_admin)
                                        <div class="mt-2 p-2 bg-success bg-opacity-10 rounded-2">
                                            <small class="text-success">{{ $pengajuan->catatan_admin }}</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @elseif(in_array($pengajuan->status, ['menunggu_pembayaran', 'menunggu_verifikasi_pembayaran']))
                            <div class="timeline-item active">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="fw-semibold mb-1">Pengajuan Divalidasi & Sedang Diproses</h6>
                                    <p class="text-muted small mb-0">{{ $pengajuan->tanggal_validasi ? $pengajuan->tanggal_validasi->format('d M Y, H:i') : $pengajuan->updated_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                            <div class="timeline-item active">
                                <div class="timeline-marker bg-warning"></div>
                                <div class="timeline-content">
                                    <h6 class="fw-semibold mb-1">{{ $pengajuan->status == 'menunggu_pembayaran' ? 'Menunggu Pembayaran' : 'Menunggu Verifikasi Pembayaran' }}</h6>
                                    <p class="text-muted small mb-0">{{ $pengajuan->updated_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                            @elseif($pengajuan->status == 'selesai')
                            <div class="timeline-item active">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="fw-semibold mb-1">Pengajuan Divalidasi & Sedang Diproses</h6>
                                    <p class="text-muted small mb-0">{{ $pengajuan->tanggal_validasi ? $pengajuan->tanggal_validasi->format('d M Y, H:i') : $pengajuan->updated_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                            <div class="timeline-item active">
                                <div class="timeline-marker bg-primary"></div>
                                <div class="timeline-content">
                                    <h6 class="fw-semibold mb-1">Pembayaran Terverifikasi</h6>
                                    <p class="text-muted small mb-0">{{ $pengajuan->updated_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                            <div class="timeline-item active">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="fw-semibold mb-1">Pengajuan Selesai</h6>
                                    <p class="text-muted small mb-0">{{ $pengajuan->updated_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                            @elseif($pengajuan->status == 'ditolak')
                            <div class="timeline-item active">
                                <div class="timeline-marker bg-danger"></div>
                                <div class="timeline-content">
                                    <h6 class="fw-semibold mb-1">Pengajuan Ditolak</h6>
                                    <p class="text-muted small mb-0">{{ $pengajuan->updated_at->format('d M Y, H:i') }}</p>
                                    @if($pengajuan->catatan_admin)
                                        <div class="mt-2 p-2 bg-danger bg-opacity-10 rounded-2">
                                            <small class="text-danger">{{ $pengajuan->catatan_admin }}</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @else
                            <div class="timeline-item">
                                <div class="timeline-marker bg-secondary"></div>
                                <div class="timeline-content">
                                    <h6 class="fw-semibold mb-1 text-muted">Proses {{ ucfirst(str_replace('_',' ',$pengajuan->status)) }}</h6>
                                    <p class="text-muted small mb-0">Sedang diproses</p>
                                </div>
                            </div>
                            @endif
                        </div>
                        </div>
                    </div>

            <!-- Quick Info -->
            <div class="card border-0 shadow-sm mb-4 order-lg-3">
                <div class="card-header bg-light border-0 py-3">
                    <h5 class="mb-0 fw-semibold text-dark">
                        <i class="fas fa-info text-info me-2"></i>Informasi Cepat
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-user-circle text-primary fs-5 me-3"></i>
                        <div>
                            <small class="text-muted d-block">Pemohon</small>
                            <span class="fw-medium">{{ $pengajuan->user->name }}</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-calendar text-primary fs-5 me-3"></i>
                        <div>
                            <small class="text-muted d-block">Tanggal Pengajuan</small>
                            <span class="fw-medium">{{ $pengajuan->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-tag text-primary fs-5 me-3"></i>
                        <div>
                            <small class="text-muted d-block">Kategori</small>
                            <span class="fw-medium">{{ $pengajuan->identitas_ciptaan ?? '-' }}</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-clock text-primary fs-5 me-3"></i>
                        <div>
                            <small class="text-muted d-block">Terakhir Diperbarui</small>
                            <span class="fw-medium">{{ $pengajuan->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                            @if(in_array($pengajuan->status, ['menunggu_pembayaran', 'menunggu_verifikasi_pembayaran', 'selesai']))
                            <div class="info-item">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-credit-card text-primary me-2"></i>
                                    <div>
                                        <small class="text-muted">Status Pembayaran</small>
                                        <div class="fw-medium">
                                            @switch($pengajuan->status)
                                                @case('menunggu_pembayaran')
                                                    <span class="badge bg-warning text-dark">Menunggu Pembayaran</span>
                                                    @break
                                                @case('menunggu_verifikasi_pembayaran')
                                                    <span class="badge bg-info text-white">Menunggu Verifikasi</span>
                                                    @break
                                                @case('selesai')
                                                    <span class="badge bg-success">Lunas</span>
                                                    @break
                                            @endswitch
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

            <!-- Action Panel -->
            <div class="card border-0 shadow-sm mb-4 order-lg-1">
                <div class="card-header bg-light border-0 py-3">
                    <h5 class="mb-0 fw-semibold text-dark">
                        <i class="fas fa-cogs text-warning me-2"></i>Panel Aksi
                    </h5>
                </div>
                    <div class="card-body p-4">
                        <div class="d-grid gap-2">
                            @if($pengajuan->status === 'menunggu_validasi')
                                <a href="{{ route('persetujuan.validation.wizard', $pengajuan->id) }}" 
                                   class="btn btn-success">
                                    <i class="fas fa-signature me-2"></i>Validasi Pengajuan
                                </a>
                                <div class="alert alert-info py-2 mb-2">
                                    <small><i class="fas fa-info-circle me-1"></i>Pengajuan siap untuk divalidasi dan ditandatangani direktur</small>
                                </div>
                            @endif

                            @if(in_array($pengajuan->status, ['divalidasi_sedang_diproses']))
                                @if(!$pengajuan->billing_code)
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#billingModal">
                                        <i class="fas fa-barcode me-2"></i>Set Kode Billing
                                    </button>
                                @else
                                    <div class="alert alert-success py-2 mb-2">
                                        <small><i class="fas fa-check-circle me-1"></i>Kode billing sudah tersedia: <strong>{{ $pengajuan->billing_code }}</strong></small>
                                    </div>
                                    <a href="{{ route('admin.pengajuan.finalisasi', $pengajuan->id) }}" 
                                       class="btn btn-warning">
                                        <i class="fas fa-check-double me-2"></i>Finalisasi ke Menunggu Pembayaran
                                    </a>
                                @endif
                            @endif

                            @if($pengajuan->status === 'menunggu_verifikasi_pembayaran')
                                <a href="{{ route('admin.pengajuan.konfirmasiPembayaran', $pengajuan->id) }}" 
                                   class="btn btn-success">
                                    <i class="fas fa-check-circle me-2"></i>Konfirmasi Pembayaran
                                </a>
                                <div class="alert alert-warning py-2 mb-2">
                                    <small><i class="fas fa-clock me-1"></i>Menunggu verifikasi bukti pembayaran</small>
                                </div>
                            @endif

                            @if($pengajuan->status === 'disetujui')
                                @if(!$pengajuan->sertifikat)
                                    <!-- Upload Sertifikat Button -->
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadSertifikatModal">
                                        <i class="fas fa-file-upload me-2"></i>Upload Sertifikat
                                    </button>
                                @else
                                    <!-- Sertifikat already uploaded -->
                                    <a href="{{ route('sertifikat.serve', $pengajuan->id) }}" target="_blank" class="btn btn-success">
                                        <i class="fas fa-file-download me-2"></i>Lihat / Download Sertifikat
                                    </a>
                                @endif
                            @endif

                            <hr class="my-3">

                        
                        <a href="{{ route('admin.pengajuan.rekapExcel', $pengajuan->id) }}" 
                           class="btn btn-outline-primary">
                            <i class="fas fa-file-excel me-2"></i>Ekspor Excel
                        </a>
                        
                        <a href="{{ route('admin.pengajuan.rekapPdf', $pengajuan->id) }}" 
                           target="_blank" 
                           class="btn btn-outline-danger">
                            <i class="fas fa-file-pdf me-2"></i>Rekap PDF
                        </a>
                        
                        <a href="{{ route('admin.pengajuan') }}" 
                           class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
                        </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Clean Modern Styles */
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
}

.bg-gradient-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
}

.bg-gradient-dark {
    background: linear-gradient(135deg, #343a40 0%, #212529 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #198754 0%, #146c43 100%);
}

.bg-gradient-light {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.card {
    transition: all 0.3s ease;
    border: none !important;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
}

.card-header {
    background: #f8f9fa !important;
    border-bottom: 1px solid #e9ecef !important;
}

/* Timeline Styles */
.timeline {
    position: relative;
    padding-left: 20px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 24px;
}

.timeline-marker {
    position: absolute;
    left: -14px;
    top: 4px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 1px #dee2e6;
}

.timeline-item.active .timeline-marker {
    box-shadow: 0 0 0 1px currentColor;
}

.timeline-content {
    padding-left: 16px;
}

/* Form styling */
.form-label {
    font-weight: 500;
    margin-bottom: 0.25rem;
}

/* Button styling */
.btn {
    border-radius: 0.5rem;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

/* Badge styling */
.badge {
    font-size: 0.75rem;
    padding: 0.35rem 0.6rem;
    font-weight: 600;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .container-fluid {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .card-body {
        padding: 1.5rem !important;
    }
    
    .sticky-top {
        position: relative !important;
        top: auto !important;
    }
}

/* Document validation card styling */
.document-validation-card {
    transition: all 0.3s ease;
    border: 1px solid #dee2e6 !important;
}

.document-validation-card:hover {
    border-color: #adb5bd !important;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.document-icon {
    transition: all 0.3s ease;
}

.document-validation-card:hover .document-icon {
    transform: scale(1.05);
}

/* Payment info styling */
.payment-info-item {
    transition: all 0.3s ease;
}

.payment-info-item:hover {
    background-color: #f8f9fa !important;
    transform: translateY(-1px);
}

.billing-code {
    transition: all 0.3s ease;
}

.billing-code:hover {
    transform: scale(1.02);
}

/* Overlay status styling */
.overlay-status-card {
    transition: all 0.3s ease;
}

.overlay-status-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.overlay-icon {
    transition: transform 0.3s ease;
}

.overlay-icon:hover {
    transform: scale(1.1);
}

.overlay-item {
    transition: all 0.2s ease;
}

.overlay-item:hover {
    background-color: #f8f9fa !important;
    border-color: #dee2e6 !important;
}

/* ================= Premium Admin Wrapper ================= */
.premium-admin {
    background: linear-gradient(145deg, #f8f9fa 0%, #e9ecef 100%);
}

.premium-admin .card {
    border-radius: 1rem !important;
    box-shadow: 0 8px 24px rgba(0,0,0,0.05);
}

.premium-admin .card:hover {
    box-shadow: 0 12px 32px rgba(0,0,0,0.08);
    transform: translateY(-4px);
}

.premium-admin .card-header, .premium-admin .card-body {
    background-color: #ffffff !important;
}

.premium-admin .badge {
    border-radius: 0.5rem;
    font-size: 0.75rem;
    padding: 0.35rem 0.6rem;
}
</style>

<!-- Modal untuk Set Billing Code -->
<div class="modal fade" id="billingModal" tabindex="-1" aria-labelledby="billingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="billingModalLabel">
                    <i class="fas fa-barcode me-2"></i>Set Kode Billing
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.pengajuan.setBilling', $pengajuan->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="billing_code" class="form-label">Kode Billing PNBP</label>
                        <input type="text" class="form-control" id="billing_code" name="billing_code" 
                               placeholder="Masukkan kode billing" required>
                        <div class="form-text">Kode billing untuk pembayaran PNBP HKI</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

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

<!-- Modal Upload Sertifikat -->
@if($pengajuan->status === 'disetujui' && !$pengajuan->sertifikat)
<div class="modal fade" id="uploadSertifikatModal" tabindex="-1" aria-labelledby="uploadSertifikatLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="uploadSertifikatLabel">Upload Sertifikat HKI</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('sertifikat.upload', $pengajuan->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label for="sertifikatFile" class="form-label">File Sertifikat (PDF)</label>
            <input type="file" class="form-control" id="sertifikatFile" name="sertifikat" accept="application/pdf" required>
            <div class="form-text">Unggah file sertifikat berformat PDF, maksimal 5&nbsp;MB.</div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary"><i class="fas fa-upload me-1"></i>Upload</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endif

<script>
// Function untuk copy billing code
function copyBilling(code) {
    navigator.clipboard.writeText(code).then(function() {
        const msg = document.getElementById('copyMsg');
        msg.style.display = 'inline';
        setTimeout(function() {
            msg.style.display = 'none';
        }, 2000);
    }).catch(function() {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = code;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        
        const msg = document.getElementById('copyMsg');
        msg.style.display = 'inline';
        setTimeout(function() {
            msg.style.display = 'none';
        }, 2000);
    });
}

// Function untuk preview bukti pembayaran
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
    setTimeout(function() {
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
@endsection 