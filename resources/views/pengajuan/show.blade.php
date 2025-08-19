@extends('layouts.app')

@section('content')
<x-page-header 
    title="{{ $pengajuan->judul_karya }}" 
    description="Detail pengajuan oleh {{ $pengajuan->user->name }} • {{ $pengajuan->created_at->format('d M Y, H:i') }}{{ $pengajuan->nomor_pengajuan ? ' • #' . $pengajuan->nomor_pengajuan : '' }}"
    icon="fas fa-eye"
    :breadcrumbs="[
        ['title' => 'Hak Cipta', 'url' => route('pengajuan.index')],
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
                                <label class="form-label text-muted small fw-medium">No. Telp</label>
                                <p class="mb-0">{{ $pengajuan->no_telp ?? '-' }}</p>
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
                                        <label class="form-label text-muted small fw-medium">No. Telp</label>
                                        <p class="mb-0">{{ $creator->no_telp ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-medium">Email</label>
                                        <p class="mb-0">{{ $creator->email ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-medium">Kewarganegaraan</label>
                                        <p class="mb-0">{{ $creator->kewarganegaraan ?? '-' }}</p>
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
                        
                        @if($pengajuan->status === 'menunggu_pembayaran')
                        <div class="col-12">
                            <div class="alert alert-warning">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Cara Pembayaran:</strong> Silakan lakukan pembayaran sesuai kode billing di atas, kemudian upload bukti pembayaran pada tombol di panel sebelah kanan.
                            </div>
                        </div>
                        @endif
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
                        @php
                            $dokumen = is_string($pengajuan->file_dokumen_pendukung) ? json_decode($pengajuan->file_dokumen_pendukung, true) : ($pengajuan->file_dokumen_pendukung ?? []);
                        @endphp
            @foreach($documents as $field => $docInfo)
                <div class="col-md-6">
                                <div class="border rounded-3 p-3 h-100 {{ $docInfo['file_info'] ? 'border-success bg-success bg-opacity-10' : 'border-danger bg-danger bg-opacity-10' }}">
                        <div class="d-flex align-items-center mb-2">
                                        <i class="{{ $docInfo['icon'] }} text-{{ $docInfo['color'] }} fs-5 me-2"></i>
                                        <h6 class="mb-0 fw-medium">{{ $docInfo['label'] }}</h6>
                        </div>
                                    <p class="text-muted small mb-2">{{ $docInfo['description'] }}</p>

                                    @php
                                        $path = '';
                                        if ($field === 'contoh_ciptaan') {
                                            if (filter_var($pengajuan->file_karya, FILTER_VALIDATE_URL)) {
                                                $path = $pengajuan->file_karya;
                                            } else {
                                                $path = $pengajuan->file_karya;
                                            }
                                        } else {
                                            // Prioritaskan file signed jika ada
                                            if (isset($dokumen['signed'][$field]) && $dokumen['signed'][$field]) {
                                                $path = $dokumen['signed'][$field];
                                            } else {
                                                $path = $dokumen[$field] ?? '';
                                            }
                                        }
                                        $path = ltrim($path, '/');
                                        if (str_starts_with($path, 'storage/')) {
                                            $path = substr($path, strlen('storage/'));
                                        }
                                        if (filter_var($path, FILTER_VALIDATE_URL)) {
                                            $fileUrl = $path;
                                        } else {
                                            $fileUrl = $path ? Storage::url($path) : '';
                                        }
                                    @endphp
                                    @if($docInfo['file_info'] || $fileUrl)
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="badge bg-success">
                                    <i class="fas fa-check me-1"></i>Tersedia
                                </span>
                                <div class="btn-group btn-group-sm">
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
                                            
                    @if(isset($dokumen['signed'][$field]) && $dokumen['signed'][$field])
                        <div class="mt-1">
                            <span class="badge bg-info text-white">
                                <i class="fas fa-signature me-1"></i>Sudah Ditandatangani
                            </span>
                        </div>
                    @elseif(in_array($field, ['form_permohonan_pendaftaran', 'surat_pengalihan']))
                        <div class="mt-1">
                            <span class="badge bg-warning text-dark">
                                <i class="fas fa-exclamation-triangle me-1"></i>Belum Ditandatangani
                            </span>
                        </div>
                    @endif
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
                        </div>
        
        <!-- Right Column -->
        <div class="col-lg-4">
                <!-- Status Pengajuan -->
                <div class="card border-0 shadow-sm mb-4 order-lg-1">
                    <div class="card-header bg-light border-0 py-3">
                        <h5 class="mb-0 fw-semibold text-dark">
                            <i class="fas fa-info-circle text-primary me-2"></i>Status Pengajuan
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="status-timeline">
                            <!-- Status Badge -->
                            <div class="text-center mb-4">
                                @switch($pengajuan->status)
                                    @case('menunggu_validasi_direktur')
                                        <span class="badge bg-warning text-dark fs-6 px-4 py-3">
                                            <i class="fas fa-clock me-2"></i>Menunggu Validasi
                                        </span>
                                        @break
                                    @case('divalidasi_sedang_diproses')
                                        <span class="badge bg-success fs-6 px-4 py-3">
                                            <i class="fas fa-check-circle me-2"></i>Divalidasi & Sedang Diproses
                                        </span>
                                        @break
                                    @case('ditolak')
                                        <span class="badge bg-danger fs-6 px-4 py-3">
                                            <i class="fas fa-times-circle me-2"></i>Ditolak
                                        </span>
                                        @break
                                    @case('menunggu_pembayaran')
                                        <span class="badge bg-info fs-6 px-4 py-3">
                                            <i class="fas fa-credit-card me-2"></i>Menunggu Pembayaran
                                        </span>
                                        @break
                                    @case('menunggu_verifikasi_pembayaran')
                                        <span class="badge bg-warning text-dark fs-6 px-4 py-3">
                                            <i class="fas fa-hourglass-half me-2"></i>Menunggu Verifikasi
                                        </span>
                                        @break
                                    @case('selesai')
                                        <span class="badge bg-success fs-6 px-4 py-3">
                                            <i class="fas fa-medal me-2"></i>Selesai
                                        </span>
                                        @break
                                    @case('menunggu_tanda_tangan')
                                        <span class="badge bg-warning text-dark fs-6 px-4 py-3">
                                            <i class="fas fa-pen-nib me-2"></i>Menunggu Tanda Tangan
                                        </span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary fs-6 px-4 py-3">
                                            <i class="fas fa-question-circle me-2"></i>{{ ucfirst(str_replace('_', ' ', $pengajuan->status)) }}
                                        </span>
                                @endswitch
                            </div>

                            <!-- Timeline -->
                            <div class="timeline">
                                <div class="timeline-item {{ $pengajuan->created_at ? 'active' : '' }}">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <h6 class="fw-semibold mb-1">Pengajuan Dibuat</h6>
                                        <small class="text-muted">{{ $pengajuan->created_at ? $pengajuan->created_at->format('d M Y, H:i') : '-' }}</small>
                                </div>
                            </div>
                            
                            <div class="timeline-item {{ in_array($pengajuan->status, ['divalidasi_sedang_diproses', 'menunggu_pembayaran', 'menunggu_verifikasi_pembayaran', 'selesai']) ? 'active' : '' }}">
                                <div class="timeline-marker {{ in_array($pengajuan->status, ['divalidasi_sedang_diproses', 'menunggu_pembayaran', 'menunggu_verifikasi_pembayaran', 'selesai']) ? 'bg-success' : 'bg-light' }}"></div>
                                <div class="timeline-content">
                                    <h6 class="fw-semibold mb-1">Divalidasi & Sedang Diproses</h6>
                                    <small class="text-muted">{{ $pengajuan->tanggal_validasi ? $pengajuan->tanggal_validasi->format('d M Y, H:i') : 'Belum divalidasi' }}</small>
                                </div>
                            </div>
                            
                            <div class="timeline-item {{ in_array($pengajuan->status, ['menunggu_pembayaran', 'menunggu_verifikasi_pembayaran', 'selesai']) ? 'active' : '' }}">
                                <div class="timeline-marker {{ in_array($pengajuan->status, ['menunggu_pembayaran', 'menunggu_verifikasi_pembayaran', 'selesai']) ? 'bg-warning' : 'bg-light' }}"></div>
                                <div class="timeline-content">
                                    <h6 class="fw-semibold mb-1">Menunggu Pembayaran</h6>
                                    <small class="text-muted">{{ in_array($pengajuan->status, ['menunggu_pembayaran', 'menunggu_verifikasi_pembayaran', 'selesai']) ? 'Proses pembayaran' : 'Belum sampai tahap ini' }}</small>
                                </div>
                            </div>
                            
                            <div class="timeline-item {{ $pengajuan->status === 'selesai' ? 'active' : '' }}">
                                <div class="timeline-marker {{ $pengajuan->status === 'selesai' ? 'bg-success' : 'bg-light' }}"></div>
                                <div class="timeline-content">
                                    <h6 class="fw-semibold mb-1">Selesai</h6>
                                    <small class="text-muted">{{ $pengajuan->status === 'selesai' ? 'Sertifikat tersedia' : 'Belum selesai' }}</small>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel Aksi -->
                @php
                    $dokumen = is_string($pengajuan->file_dokumen_pendukung) ? json_decode($pengajuan->file_dokumen_pendukung, true) : ($pengajuan->file_dokumen_pendukung ?? []);
                    $semuaDokumenSudahTtd = $pengajuan->allSignaturesSigned();
                @endphp
                <div class="card mb-4">
                    <div class="card-header bg-light fw-bold d-flex align-items-center justify-content-between">
                        <span>Panel Aksi</span>
                        @if($semuaDokumenSudahTtd)
                            <span class="badge bg-success ms-2">Lengkap</span>
                        @endif
                    </div>
                    <div class="card-body d-flex flex-column gap-2">
                        <a href="{{ route('tracking.show', $pengajuan->id) }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-route me-1"></i> Tracking Status
                        </a>
                        @php
                            $docsJson = is_string($pengajuan->file_dokumen_pendukung)
                                ? json_decode($pengajuan->file_dokumen_pendukung, true)
                                : ($pengajuan->file_dokumen_pendukung ?? []);
                            $sudahTtdPengalihan = !empty($docsJson['signed']['surat_pengalihan']);
                            $sudahTtdForm = !empty($docsJson['signed']['form_permohonan_pendaftaran']);
                        @endphp

                        <a href="{{ $sudahTtdPengalihan ? 'javascript:void(0)' : route('signatures.index', $pengajuan->id) }}" 
                           class="btn {{ $sudahTtdPengalihan ? 'btn-secondary disabled' : 'btn-outline-warning' }} w-100"
                           @if($sudahTtdPengalihan) onclick="Swal.fire({icon:'info',title:'Sudah Dilakukan',text:'Tanda Tangan Surat Pengalihan & Upload KTP sudah dilakukan dan hanya bisa 1 kali.'});" @endif>
                            <i class="fas fa-file-signature me-1"></i> Tanda Tangan Surat Pengalihan & Upload KTP
                        </a>
                        <a href="{{ $sudahTtdForm ? 'javascript:void(0)' : route('pengajuan.signature.form', $pengajuan->id) }}" 
                           class="btn {{ $sudahTtdForm ? 'btn-secondary disabled' : 'btn-outline-primary' }} w-100"
                           @if($sudahTtdForm) onclick="Swal.fire({icon:'info',title:'Sudah Dilakukan',text:'Tanda Tangan Form Permohonan Pendaftaran sudah dilakukan dan hanya bisa 1 kali.'});" @endif>
                            <i class="fas fa-file-signature me-1"></i> Tanda Tangan Form Permohonan Pendaftaran
                        </a>
                        @if($semuaDokumenSudahTtd && !in_array($pengajuan->status, ['menunggu_validasi_direktur','divalidasi_sedang_diproses','menunggu_pembayaran','menunggu_verifikasi_pembayaran','selesai','ditolak']))
                            <form id="form-konfirmasi-kirim" action="{{ route('pengajuan.konfirmasiSelesai', $pengajuan->id) }}" method="POST" class="mt-2">
                                @csrf
                                <button type="submit" class="btn btn-success w-100" id="btn-konfirmasi-kirim">
                                    <i class="fas fa-paper-plane me-1"></i>Konfirmasi & Kirim ke Direktur
                                </button>
                            </form>
                        @elseif(!$semuaDokumenSudahTtd)
                            <button type="button" class="btn btn-success w-100 mt-2" onclick="Swal.fire({icon:'info',title:'Belum Lengkap',text:'Silakan selesaikan tanda tangan dokumen terlebih dahulu sebelum mengirim ke Direktur.'});">
                                <i class="fas fa-paper-plane me-1"></i>Konfirmasi & Kirim ke Direktur
                            </button>
                        @else
                            <button type="button" class="btn btn-secondary w-100 mt-2" onclick="Swal.fire({icon:'info',title:'Sudah Dikirim',text:'Pengajuan sudah dikirim dan tidak bisa dikirim ulang.'});" disabled>
                                <i class="fas fa-paper-plane me-1"></i>Konfirmasi & Kirim ke Direktur
                            </button>
                        @endif
                        <a href="{{ route('pengajuan.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
                        </a>
                    </div>
                </div>

                @if($semuaDokumenSudahTtd)
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function(){
                        const form = document.getElementById('form-konfirmasi-kirim');
                        const btn = document.getElementById('btn-konfirmasi-kirim');
                        if(form && btn){
                            btn.addEventListener('click', function(e){
                                e.preventDefault();
                                Swal.fire({
                                    icon: 'question',
                                    title: 'Kirim ke Direktur?',
                                    text: 'Pastikan semua dokumen sudah benar. Tindakan ini akan mengirim pengajuan ke Direktur.',
                                    showCancelButton: true,
                                    confirmButtonText: 'Ya, kirim sekarang',
                                    cancelButtonText: 'Batal',
                                    customClass: { confirmButton: 'btn btn-success', cancelButton: 'btn btn-secondary ms-2' },
                                    buttonsStyling: false
                                }).then((result) => {
                                    if(result.isConfirmed){
                                        form.submit();
                                    }
                                });
                            });
                        }
                    });
                </script>
                @endif

                @if(session('success'))
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function(){
                        Swal.fire({
                            icon: 'success',
                            title: 'Terkirim!',
                            text: @json(session('success')),
                            confirmButtonText: 'OK',
                            customClass: { confirmButton: 'btn btn-primary' },
                            buttonsStyling: false
                        });
                    });
                </script>
                @endif

                <!-- Informasi Tambahan -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light border-0 py-3">
                        <h5 class="mb-0 fw-semibold text-dark">
                            <i class="fas fa-info text-info me-2"></i>Informasi Tambahan
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="info-item mb-3">
                            <label class="form-label text-muted small fw-medium">Nomor Pengajuan</label>
                            <p class="mb-0 fw-bold text-primary">{{ $pengajuan->nomor_pengajuan ?? 'Belum tersedia' }}</p>
                        </div>
                        <div class="info-item mb-3">
                            <label class="form-label text-muted small fw-medium">Tanggal Pengajuan</label>
                            <p class="mb-0">{{ $pengajuan->tanggal_pengajuan ? $pengajuan->tanggal_pengajuan->format('d M Y, H:i') : '-' }}</p>
                        </div>
                        @if($pengajuan->catatan_admin)
                        <div class="info-item">
                            <label class="form-label text-muted small fw-medium">Catatan Admin</label>
                            <div class="p-3 bg-light rounded-3">
                                <p class="mb-0 small">{{ $pengajuan->catatan_admin }}</p>
                            </div>
                        </div>
                        @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
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

.form-label {
    font-weight: 500;
    margin-bottom: 0.25rem;
}

.btn {
    border-radius: 0.5rem;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

.badge {
    font-size: 0.75rem;
    padding: 0.35rem 0.6rem;
    font-weight: 600;
}
</style>

<script>
function copyBilling(code) {
    navigator.clipboard.writeText(code).then(function() {
        document.getElementById('copyMsg').style.display = 'block';
        setTimeout(function() {
            document.getElementById('copyMsg').style.display = 'none';
        }, 2000);
    });
}
</script>

@if(session('success_signature_permohonan'))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: @json(session('success_signature_permohonan')),
                confirmButtonText: 'OK',
                customClass: { confirmButton: 'btn btn-primary' },
                buttonsStyling: false
            });
        });
    </script>
@endif

@endsection 