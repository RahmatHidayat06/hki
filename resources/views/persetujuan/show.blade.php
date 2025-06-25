@extends('layouts.app')

@section('content')
<x-page-header 
    title="{{ $pengajuan->judul_karya }}" 
    description="Detail pengajuan oleh {{ $pengajuan->user->name }} • {{ $pengajuan->created_at->format('d M Y, H:i') }}{{ $pengajuan->nomor_pengajuan ? ' • #' . $pengajuan->nomor_pengajuan : '' }}"
    icon="fas fa-eye"
    :breadcrumbs="[
        ['title' => 'Persetujuan HKI', 'url' => route('persetujuan.index')],
        ['title' => 'Detail #' . $pengajuan->id]
    ]"
/>

<div class="container-fluid px-4">

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
                                <label class="form-label text-muted small fw-medium">Role</label>
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
                                <div class="border rounded-3 p-3 h-100 {{ $docInfo['file_info'] ? 'border-success bg-success bg-opacity-10' : 'border-danger bg-danger bg-opacity-10' }}">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="{{ $docInfo['icon'] }} text-{{ $docInfo['color'] }} fs-5 me-2"></i>
                                        <h6 class="mb-0 fw-medium">{{ $docInfo['label'] }}</h6>
                                                    </div>
                                    <p class="text-muted small mb-2">{{ $docInfo['description'] }}</p>
                                    
                                            @if($docInfo['file_info'])
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span class="badge bg-success">
                                                    <i class="fas fa-check me-1"></i>Tersedia
                                                </span>
                                            <div class="btn-group btn-group-sm">
                            @php
                                $path = '';
                                if ($field === 'contoh_ciptaan') {
                                    if (filter_var($pengajuan->file_karya, FILTER_VALIDATE_URL)) {
                                                            $path = $pengajuan->file_karya;
                                    } else {
                                                            $path = Storage::url($pengajuan->file_karya);
                                    }
                                } else {
                                    if($pengajuan->status == 'divalidasi' && isset($dokumen['signed'][$field])){
                                        $path = $dokumen['signed'][$field];
                                    } else {
                                        $path = $dokumen[$field] ?? '';
                                    }
                                }
                                $path = ltrim($path, '/');
                                $fileUrl = $path ? Storage::url($path) : '';
                            @endphp
                                                <a href="{{ $fileUrl }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-eye"></i>
                            </a>
                                                <a href="{{ $fileUrl }}" download class="btn btn-outline-success btn-sm">
                                                    <i class="fas fa-download"></i>
                            </a>
                                            </div>
                                                </div>

                                        <div class="mt-2">
                                            <small class="text-muted">
                                                <i class="fas fa-file me-1"></i>{{ $docInfo['file_info']['filename'] ?? 'File' }}
                                                <span class="ms-2">
                                                    <i class="fas fa-weight me-1"></i>{{ $docInfo['file_info']['size_formatted'] ?? '-' }}
                                                </span>
                                            </small>
                                        </div>
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

            <!-- Dokumen Telah Ditandatangani (Hanya tampil untuk pengajuan yang sudah divalidasi) -->
            @if($pengajuan->status === 'divalidasi' && isset($dokumen['signed']) && (isset($dokumen['signed']['surat_pengalihan']) || isset($dokumen['signed']['surat_pernyataan'])))
            <div class="card border-0 shadow-sm mb-4">

                <div class="card-header bg-light border-0 py-3">
                    <h5 class="mb-0 fw-semibold text-dark">
                        <i class="fas fa-stamp text-success me-2"></i>Dokumen Telah Ditandatangani
                    </h5>
                                            </div>
                <div class="card-body p-4">
                    <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                        <i class="fas fa-check-circle fs-4 me-3"></i>
                        <div>
                            <strong>Dokumen telah ditandatangani oleh Direktur</strong><br>
                            <small class="text-muted">Dokumen berikut telah melalui proses validasi dan penandatanganan</small>
                        </div>
                    </div>
                    
                    <div class="row g-3">
                        @if(isset($dokumen['signed']['surat_pengalihan']))
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100 border-success bg-success bg-opacity-10">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-exchange-alt text-success fs-5 me-2"></i>
                                    <h6 class="mb-0 fw-medium">Surat Pengalihan Hak Cipta</h6>
                                            </div>
                                <p class="text-muted small mb-3">Surat pengalihan hak cipta yang telah ditandatangani</p>
                                
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>Ditandatangani
                                    </span>
                                    <div class="btn-group btn-group-sm">
                                                                            @php
                                        $signedPath = ltrim($dokumen['signed']['surat_pengalihan'], '/');
                                        $fileExists = Storage::disk('public')->exists($signedPath);
                                        
                                        // URL untuk preview dan download
                                        $previewUrl = route('persetujuan.preview', [$pengajuan->id, 'surat_pengalihan']);
                                        $downloadUrl = route('signed.document.serve', [$pengajuan->id, 'surat_pengalihan']);
                                    @endphp
                                        <a href="{{ $previewUrl }}" target="_blank" class="btn btn-outline-success btn-sm" title="Preview Dokumen">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ $downloadUrl }}" download class="btn btn-outline-primary btn-sm" title="Download Dokumen">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        </div>
                                            </div>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-file-pdf me-1"></i>Surat Pengalihan (Ditandatangani)
                                    </small>
                                </div>
                        </div>
                    </div>
                        @endif
                        
                        @if(isset($dokumen['signed']['surat_pernyataan']))
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100 border-success bg-success bg-opacity-10">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-file-signature text-success fs-5 me-2"></i>
                                    <h6 class="mb-0 fw-medium">Surat Pernyataan Hak Cipta</h6>
                                            </div>
                                <p class="text-muted small mb-3">Surat pernyataan hak cipta yang telah ditandatangani</p>
                                
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>Ditandatangani
                                    </span>
                                    <div class="btn-group btn-group-sm">
                                                                            @php
                                        $signedPath = ltrim($dokumen['signed']['surat_pernyataan'], '/');
                                        $fileExists = Storage::disk('public')->exists($signedPath);
                                        
                                        // URL untuk preview dan download
                                        $previewUrl = route('persetujuan.preview', [$pengajuan->id, 'surat_pernyataan']);
                                        $downloadUrl = route('signed.document.serve', [$pengajuan->id, 'surat_pernyataan']);
                                    @endphp
                                        <a href="{{ $previewUrl }}" target="_blank" class="btn btn-outline-success btn-sm" title="Preview Dokumen">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ $downloadUrl }}" download class="btn btn-outline-primary btn-sm" title="Download Dokumen">
                                            <i class="fas fa-download"></i>
                                            </a>
                                        </div>
                                    </div>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-file-pdf me-1"></i>Surat Pernyataan (Ditandatangani)
                                    </small>
                                </div>
                            </div>
                        </div>
            @endif
                    </div>
                        </div>
                    </div>
                    @endif

            <!-- Status Overlay & Tanda Tangan Direktur -->
            @if(in_array($pengajuan->status, ['divalidasi', 'sedang_di_proses', 'menunggu_pembayaran', 'menunggu_verifikasi_pembayaran', 'selesai']))
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light border-0 py-3">
                    <h5 class="mb-0 fw-semibold text-dark">
                        <i class="fas fa-stamp text-success me-2"></i>
                        Status Overlay & Tanda Tangan Direktur
                    </h5>
                </div>
                <div class="card-body p-4">
                    @php
                        // Get overlay data for both documents
                        $overlayPengalihan = $dokumen['overlays']['surat_pengalihan'] ?? [];
                        $overlayPernyataan = $dokumen['overlays']['surat_pernyataan'] ?? [];
                        $hasAnyOverlay = !empty($overlayPengalihan) || !empty($overlayPernyataan);
                    @endphp

                    @if($hasAnyOverlay)
                    <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                        <i class="fas fa-check-circle fs-4 me-3"></i>
                        <div>
                            <strong>Direktur telah menerapkan overlay tanda tangan</strong><br>
                            <small class="text-muted">Dokumen telah diberi overlay dan siap untuk proses selanjutnya</small>
                        </div>
                    </div>
                    
                    <div class="row g-3">
                        @if(!empty($overlayPengalihan))
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100 border-primary bg-primary bg-opacity-10">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-file-signature text-primary fs-5 me-2"></i>
                                    <h6 class="mb-0 fw-medium">Surat Pengalihan</h6>
                                </div>
                                <p class="text-muted small mb-3">Status overlay pada surat pengalihan hak cipta</p>
                                
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge bg-primary">
                                        <i class="fas fa-signature me-1"></i>{{ count($overlayPengalihan) }} Overlay
                                    </span>
                                    <small class="text-success fw-medium">✓ Applied</small>
                                </div>
                                
                                @foreach($overlayPengalihan as $index => $overlay)
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-{{ $overlay['type'] === 'signature' ? 'signature' : 'stamp' }} me-1"></i>
                                        {{ $overlay['type'] === 'signature' ? 'Tanda Tangan' : 'Materai' }} 
                                        - Posisi: {{ number_format($overlay['x_percent'] ?? 0, 1) }}%, {{ number_format($overlay['y_percent'] ?? 0, 1) }}%
                                    </small>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        
                        @if(!empty($overlayPernyataan))
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100 border-info bg-info bg-opacity-10">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-file-contract text-info fs-5 me-2"></i>
                                    <h6 class="mb-0 fw-medium">Surat Pernyataan</h6>
                                </div>
                                <p class="text-muted small mb-3">Status overlay pada surat pernyataan hak cipta</p>
                                
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge bg-info">
                                        <i class="fas fa-signature me-1"></i>{{ count($overlayPernyataan) }} Overlay
                                    </span>
                                    <small class="text-success fw-medium">✓ Applied</small>
                                </div>
                                
                                @foreach($overlayPernyataan as $index => $overlay)
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-{{ $overlay['type'] === 'signature' ? 'signature' : 'stamp' }} me-1"></i>
                                        {{ $overlay['type'] === 'signature' ? 'Tanda Tangan' : 'Materai' }} 
                                        - Posisi: {{ number_format($overlay['x_percent'] ?? 0, 1) }}%, {{ number_format($overlay['y_percent'] ?? 0, 1) }}%
                                    </small>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                    @else
                    <!-- No Overlays State -->
                    <div class="text-center py-4">
                        <i class="fas fa-signature fa-3x text-muted mb-3"></i>
                        <h6 class="fw-medium text-muted mb-2">Belum Ada Overlay</h6>
                        <p class="text-muted mb-0">
                            Direktur belum menerapkan tanda tangan atau materai pada dokumen ini.
                            <br>Status pengajuan: <strong>{{ ucfirst(str_replace('_', ' ', $pengajuan->status)) }}</strong>
                        </p>
                        @if($pengajuan->status === 'menunggu_validasi' && auth()->user()->role === 'direktur')
                            <div class="mt-3">
                                <a href="{{ route('persetujuan.validation.wizard', $pengajuan->id) }}" 
                                   class="btn btn-primary">
                                    <i class="fas fa-signature me-2"></i>Mulai Validasi & Tanda Tangan
                                </a>
                            </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
            @endif

                    </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Timeline Status -->
            <div class="card border-0 shadow-sm mb-4">
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
                                <h6 class="fw-semibold mb-1">Pengajuan Disubmit</h6>
                                <p class="text-muted small mb-0">{{ $pengajuan->updated_at->format('d M Y, H:i') }}</p>
                                        </div>
                                    </div>
                                    
                        @if($pengajuan->status == 'divalidasi')
                        <div class="timeline-item active">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="fw-semibold mb-1">Pengajuan Divalidasi</h6>
                                <p class="text-muted small mb-0">{{ $pengajuan->tanggal_validasi ? $pengajuan->tanggal_validasi->format('d M Y, H:i') : $pengajuan->updated_at->format('d M Y, H:i') }}</p>
                                @if($pengajuan->catatan_admin)
                                    <div class="mt-2 p-2 bg-success bg-opacity-10 rounded-2">
                                        <small class="text-success">{{ $pengajuan->catatan_admin }}</small>
                                        </div>
                                                @endif
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
                                <h6 class="fw-semibold mb-1 text-muted">Menunggu Keputusan</h6>
                                <p class="text-muted small mb-0">Sedang dalam proses review</p>
                                </div>
                                </div>
                                        @endif
                                </div>
                            </div>
                        </div>

            <!-- Quick Info -->
            <div class="card border-0 shadow-sm mb-4">
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
                            </div>
                                    </div>

            <!-- Action Panel -->
            @if(auth()->user()->role === 'admin')
            <div class="card border-0 shadow-sm mb-4">
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
                        @endif
                        
                        <a href="{{ route('admin.pengajuan.rekapExcel', $pengajuan->id) }}" 
                           class="btn btn-outline-primary">
                            <i class="fas fa-file-excel me-2"></i>Export Excel
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
            @endif
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

<style>
/* Clean Modern Styles */
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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

/* Responsive adjustments */
@media (max-width: 768px) {
    .container-fluid {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .card-body {
        padding: 1.5rem !important;
    }
}
</style>
@endsection 