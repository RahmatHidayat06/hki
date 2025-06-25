@extends('layouts.app')

@section('content')
<x-page-header 
    title="Detail Pengajuan HKI" 
    description="Informasi lengkap pengajuan dan status"
    icon="fas fa-eye"
    :breadcrumbs="[
        ['title' => 'Hak Cipta', 'url' => route('pengajuan.index')],
        ['title' => 'Detail #' . $pengajuan->id]
    ]"
/>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Detail Pengajuan HKI</h4>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Judul Karya</dt>
                        <dd class="col-sm-8">{{ $pengajuan->judul_karya }}</dd>
                        <dt class="col-sm-4">Deskripsi</dt>
                        <dd class="col-sm-8">{{ $pengajuan->deskripsi }}</dd>
                        <dt class="col-sm-4">Jenis Ciptaan</dt>
                        <dd class="col-sm-8">{{ $pengajuan->identitas_ciptaan }}</dd>
                        <dt class="col-sm-4">Sub Jenis Ciptaan</dt>
                        <dd class="col-sm-8">{{ $pengajuan->sub_jenis_ciptaan }}</dd>
                        <dt class="col-sm-4">Tahun Usulan</dt>
                        <dd class="col-sm-8">{{ $pengajuan->tahun_usulan ?? '-' }}</dd>
                        <dt class="col-sm-4">Jumlah Pencipta</dt>
                        <dd class="col-sm-8">{{ $pengajuan->jumlah_pencipta }}</dd>
                        <dt class="col-sm-4">Status</dt>
                        <dd class="col-sm-8">{{ ucfirst(str_replace('_', ' ', $pengajuan->status)) }}</dd>
                        <dt class="col-sm-4">Tanggal Pengajuan</dt>
                        <dd class="col-sm-8">{{ $pengajuan->tanggal_pengajuan }}</dd>
                    </dl>
                    <hr>
                    <h5>Data Pengusul</h5>
                    <dl class="row">
                        <dt class="col-sm-4">Nama Pencipta</dt>
                        <dd class="col-sm-8">{{ optional($pengajuan->pengaju->first())->nama ?? '-' }}</dd>
                        <dt class="col-sm-4">NIP / NIDN</dt>
                        <dd class="col-sm-8">{{ $pengajuan->nip_nidn ?? '-' }}</dd>
                        <dt class="col-sm-4">No HP</dt>
                        <dd class="col-sm-8">{{ $pengajuan->no_hp ?? '-' }}</dd>
                        <dt class="col-sm-4">ID SINTA</dt>
                        <dd class="col-sm-8">{{ $pengajuan->id_sinta ?? '-' }}</dd>
                        <dt class="col-sm-4">Role</dt>
                        <dd class="col-sm-8">{{ ucfirst($pengajuan->role ?? '-') }}</dd>
                    </dl>
                    <hr>
                    <h5>Data Pencipta</h5>
                    @foreach($pengajuan->pengaju as $pencipta)
                        <div class="mb-3 border rounded p-3">
                            <strong>Nama:</strong> {{ $pencipta->nama }}<br>
                            <strong>Email:</strong> {{ $pencipta->email }}<br>
                            <strong>No HP:</strong> {{ $pencipta->no_hp }}<br>
                            <strong>Alamat:</strong> {{ $pencipta->alamat }}<br>
                            <strong>Kecamatan:</strong> {{ $pencipta->kecamatan }}<br>
                            <strong>Kode Pos:</strong> {{ $pencipta->kodepos }}<br>
                        </div>
                    @endforeach
                    <hr>
                    <h5>Dokumen Pendukung</h5>
                    <div class="mb-3">
                        <strong>File Karya:</strong>
                        @if($pengajuan->file_karya)
                            @php
                                $isUrl = filter_var($pengajuan->file_karya, FILTER_VALIDATE_URL);
                            @endphp
                            @if($isUrl)
                                <a href="{{ $pengajuan->file_karya }}" class="btn btn-primary btn-sm ms-2" target="_blank">Lihat/Download Link Karya</a>
                            @else
                                @php
                                    $ext = pathinfo($pengajuan->file_karya, PATHINFO_EXTENSION);
                                    $url = Storage::url($pengajuan->file_karya);
                                @endphp
                                @if(in_array(strtolower($ext), ['jpg','jpeg','png','gif','svg','webp']))
                                    <div class="my-2"><img src="{{ $url }}" alt="File Karya" class="img-fluid rounded border"></div>
                                @elseif(in_array(strtolower($ext), ['mp4','webm','ogg']))
                                    <div class="my-2"><video src="{{ $url }}" controls class="w-100 rounded border"></video></div>
                                @elseif(in_array(strtolower($ext), ['mp3','wav','ogg']))
                                    <div class="my-2"><audio src="{{ $url }}" controls class="w-100"></audio></div>
                                @elseif(strtolower($ext) === 'pdf')
                                    <div class="my-2">
                                        <div class="pdf-viewer-container border rounded" style="background: #f8f9fa;">
                                            <!-- View Mode Toggle -->
                                            <div class="d-flex justify-content-center align-items-center p-2 bg-light border-bottom">
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <input type="radio" class="btn-check" name="viewMode_karya" id="canvasMode_karya" checked>
                                                    <label class="btn btn-outline-primary" for="canvasMode_karya">
                                                        <i class="fas fa-edit me-1"></i>Mode Interaktif
                                                    </label>
                                                    
                                                    <input type="radio" class="btn-check" name="viewMode_karya" id="iframeMode_karya">
                                                    <label class="btn btn-outline-success" for="iframeMode_karya">
                                                        <i class="fas fa-file-pdf me-1"></i>Tampilkan Semua Halaman
                                                    </label>
                                                </div>
                                            </div>

                                            <!-- Navigation Controls -->
                                            <div class="d-flex justify-content-center align-items-center p-2 bg-light border-bottom" id="navControls_karya" style="display: none !important;">
                                                <button id="prevPage_karya" class="btn btn-outline-primary btn-sm me-2">
                                                    <i class="fas fa-chevron-left"></i>
                                                </button>
                                                <span class="mx-3">
                                                    <strong>Halaman <span id="pageNum_karya">1</span> dari <span id="pageCount_karya">1</span></strong>
                                                </span>
                                                <button id="nextPage_karya" class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-chevron-right"></i>
                                                </button>
                                            </div>

                                            <!-- PDF Container - Canvas Mode -->
                                            <div id="pdfWrapper_karya" style="position:relative; overflow:auto; background: #f8f9fa; min-height: 400px; display: none;">
                                                <div style="position: relative; text-align: center; padding: 20px;">
                                                    <canvas id="pdfCanvas_karya" style="max-width: 100%; display: block; margin: 0 auto; box-shadow: 0 4px 8px rgba(0,0,0,0.1);"></canvas>
                                                </div>
                                            </div>

                                                                                    <!-- PDF Container - Iframe Mode -->
                                        <div id="pdfWrapperIframe_karya" style="position:relative; overflow:auto; background: #f8f9fa; min-height: 400px;">
                                            <iframe src="{{ $url }}#view=FitH&pagemode=none&toolbar=1" width="100%" height="500px" style="border: none;">
                                                <p>Browser Anda tidak mendukung iframe. <a href="{{ $url }}" target="_blank">Klik di sini untuk membuka PDF.</a></p>
                                            </iframe>
                                        </div>
                                        </div>
                                    </div>
                                @else
                                    <a href="{{ $url }}" class="btn btn-primary btn-sm ms-2" target="_blank">Download File Karya</a>
                                @endif
                            @endif
                        @else
                            <span class="text-muted">Tidak ada file</span>
                        @endif
                    </div>
                    @php $dokumen = is_string($pengajuan->file_dokumen_pendukung) ? json_decode($pengajuan->file_dokumen_pendukung, true) : ($pengajuan->file_dokumen_pendukung ?? []); @endphp
                    <div class="mb-3">
                        <strong>Surat Pengalihan Hak Cipta:</strong>
                        @if(isset($dokumen['surat_pengalihan']) && $dokumen['surat_pengalihan'])
                            @php $url = Storage::url($dokumen['surat_pengalihan']); $ext = pathinfo($dokumen['surat_pengalihan'], PATHINFO_EXTENSION); @endphp
                            @if(strtolower($ext) === 'pdf')
                                <div class="my-2">
                                    <div class="pdf-viewer-container border rounded" style="background: #f8f9fa;">
                                        <!-- View Mode Toggle -->
                                        <div class="d-flex justify-content-center align-items-center p-2 bg-light border-bottom">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <input type="radio" class="btn-check" name="viewMode_pengalihan" id="canvasMode_pengalihan" checked>
                                                <label class="btn btn-outline-primary" for="canvasMode_pengalihan">
                                                    <i class="fas fa-edit me-1"></i>Mode Interaktif
                                                </label>
                                                
                                                <input type="radio" class="btn-check" name="viewMode_pengalihan" id="iframeMode_pengalihan">
                                                <label class="btn btn-outline-success" for="iframeMode_pengalihan">
                                                    <i class="fas fa-file-pdf me-1"></i>Tampilkan Semua Halaman
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Navigation Controls -->
                                        <div class="d-flex justify-content-center align-items-center p-2 bg-light border-bottom" id="navControls_pengalihan" style="display: none !important;">
                                            <button id="prevPage_pengalihan" class="btn btn-outline-primary btn-sm me-2">
                                                <i class="fas fa-chevron-left"></i>
                                            </button>
                                            <span class="mx-3">
                                                <strong>Halaman <span id="pageNum_pengalihan">1</span> dari <span id="pageCount_pengalihan">1</span></strong>
                                            </span>
                                            <button id="nextPage_pengalihan" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-chevron-right"></i>
                                            </button>
                                        </div>

                                        <!-- PDF Container - Canvas Mode -->
                                        <div id="pdfWrapper_pengalihan" style="position:relative; overflow:auto; background: #f8f9fa; min-height: 400px; display: none;">
                                            <div style="position: relative; text-align: center; padding: 20px;">
                                                <canvas id="pdfCanvas_pengalihan" style="max-width: 100%; display: block; margin: 0 auto; box-shadow: 0 4px 8px rgba(0,0,0,0.1);"></canvas>
                                            </div>
                                        </div>

                                                                                 <!-- PDF Container - Iframe Mode -->
                                         <div id="pdfWrapperIframe_pengalihan" style="position:relative; overflow:auto; background: #f8f9fa; min-height: 400px;">
                                             <iframe src="{{ $url }}#view=FitH&pagemode=none&toolbar=1" width="100%" height="500px" style="border: none;">
                                                 <p>Browser Anda tidak mendukung iframe. <a href="{{ $url }}" target="_blank">Klik di sini untuk membuka PDF.</a></p>
                                             </iframe>
                                         </div>
                                    </div>
                                </div>
                            @else
                                <a href="{{ $url }}" class="btn btn-primary btn-sm ms-2" target="_blank">Download Surat Pengalihan</a>
                            @endif
                        @else
                            <span class="text-muted">Tidak ada file</span>
                        @endif
                    </div>
                    <div class="mb-3">
                        <strong>Surat Pernyataan Hak Cipta:</strong>
                        @if(isset($dokumen['surat_pernyataan']) && $dokumen['surat_pernyataan'])
                            @php $url = Storage::url($dokumen['surat_pernyataan']); $ext = pathinfo($dokumen['surat_pernyataan'], PATHINFO_EXTENSION); @endphp
                            @if(strtolower($ext) === 'pdf')
                                <div class="my-2">
                                    <div class="pdf-viewer-container border rounded" style="background: #f8f9fa;">
                                        <!-- View Mode Toggle -->
                                        <div class="d-flex justify-content-center align-items-center p-2 bg-light border-bottom">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <input type="radio" class="btn-check" name="viewMode_pernyataan" id="canvasMode_pernyataan" checked>
                                                <label class="btn btn-outline-primary" for="canvasMode_pernyataan">
                                                    <i class="fas fa-edit me-1"></i>Mode Interaktif
                                                </label>
                                                
                                                <input type="radio" class="btn-check" name="viewMode_pernyataan" id="iframeMode_pernyataan">
                                                <label class="btn btn-outline-success" for="iframeMode_pernyataan">
                                                    <i class="fas fa-file-pdf me-1"></i>Tampilkan Semua Halaman
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Navigation Controls -->
                                        <div class="d-flex justify-content-center align-items-center p-2 bg-light border-bottom" id="navControls_pernyataan" style="display: none !important;">
                                            <button id="prevPage_pernyataan" class="btn btn-outline-primary btn-sm me-2">
                                                <i class="fas fa-chevron-left"></i>
                                            </button>
                                            <span class="mx-3">
                                                <strong>Halaman <span id="pageNum_pernyataan">1</span> dari <span id="pageCount_pernyataan">1</span></strong>
                                            </span>
                                            <button id="nextPage_pernyataan" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-chevron-right"></i>
                                            </button>
                                        </div>

                                        <!-- PDF Container - Canvas Mode -->
                                        <div id="pdfWrapper_pernyataan" style="position:relative; overflow:auto; background: #f8f9fa; min-height: 400px; display: none;">
                                            <div style="position: relative; text-align: center; padding: 20px;">
                                                <canvas id="pdfCanvas_pernyataan" style="max-width: 100%; display: block; margin: 0 auto; box-shadow: 0 4px 8px rgba(0,0,0,0.1);"></canvas>
                                            </div>
                                        </div>

                                                                                 <!-- PDF Container - Iframe Mode -->
                                         <div id="pdfWrapperIframe_pernyataan" style="position:relative; overflow:auto; background: #f8f9fa; min-height: 400px;">
                                             <iframe src="{{ $url }}#view=FitH&pagemode=none&toolbar=1" width="100%" height="500px" style="border: none;">
                                                 <p>Browser Anda tidak mendukung iframe. <a href="{{ $url }}" target="_blank">Klik di sini untuk membuka PDF.</a></p>
                                             </iframe>
                                         </div>
                                    </div>
                                </div>
                            @else
                                <a href="{{ $url }}" class="btn btn-primary btn-sm ms-2" target="_blank">Download Surat Pernyataan</a>
                            @endif
                        @else
                            <span class="text-muted">Tidak ada file</span>
                        @endif
                    </div>
                    <div class="mb-3">
                        <strong>KTP Seluruh Pencipta:</strong>
                        @if(isset($dokumen['ktp']) && $dokumen['ktp'])
                            @php $url = Storage::url($dokumen['ktp']); $ext = pathinfo($dokumen['ktp'], PATHINFO_EXTENSION); @endphp
                            @if(strtolower($ext) === 'pdf')
                                <div class="my-2">
                                    <div class="pdf-viewer-container border rounded" style="background: #f8f9fa;">
                                        <!-- View Mode Toggle -->
                                        <div class="d-flex justify-content-center align-items-center p-2 bg-light border-bottom">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <input type="radio" class="btn-check" name="viewMode_ktp" id="canvasMode_ktp" checked>
                                                <label class="btn btn-outline-primary" for="canvasMode_ktp">
                                                    <i class="fas fa-edit me-1"></i>Mode Interaktif
                                                </label>
                                                
                                                <input type="radio" class="btn-check" name="viewMode_ktp" id="iframeMode_ktp">
                                                <label class="btn btn-outline-success" for="iframeMode_ktp">
                                                    <i class="fas fa-file-pdf me-1"></i>Tampilkan Semua Halaman
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Navigation Controls -->
                                        <div class="d-flex justify-content-center align-items-center p-2 bg-light border-bottom" id="navControls_ktp" style="display: none !important;">
                                            <button id="prevPage_ktp" class="btn btn-outline-primary btn-sm me-2">
                                                <i class="fas fa-chevron-left"></i>
                                            </button>
                                            <span class="mx-3">
                                                <strong>Halaman <span id="pageNum_ktp">1</span> dari <span id="pageCount_ktp">1</span></strong>
                                            </span>
                                            <button id="nextPage_ktp" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-chevron-right"></i>
                                            </button>
                                        </div>

                                        <!-- PDF Container - Canvas Mode -->
                                        <div id="pdfWrapper_ktp" style="position:relative; overflow:auto; background: #f8f9fa; min-height: 400px; display: none;">
                                            <div style="position: relative; text-align: center; padding: 20px;">
                                                <canvas id="pdfCanvas_ktp" style="max-width: 100%; display: block; margin: 0 auto; box-shadow: 0 4px 8px rgba(0,0,0,0.1);"></canvas>
                                            </div>
                                        </div>

                                                                                 <!-- PDF Container - Iframe Mode -->
                                         <div id="pdfWrapperIframe_ktp" style="position:relative; overflow:auto; background: #f8f9fa; min-height: 400px;">
                                             <iframe src="{{ $url }}#view=FitH&pagemode=none&toolbar=1" width="100%" height="500px" style="border: none;">
                                                 <p>Browser Anda tidak mendukung iframe. <a href="{{ $url }}" target="_blank">Klik di sini untuk membuka PDF.</a></p>
                                             </iframe>
                                         </div>
                                    </div>
                                </div>
                            @else
                                <a href="{{ $url }}" class="btn btn-primary btn-sm ms-2" target="_blank">Download KTP</a>
                            @endif
                        @else
                            <span class="text-muted">Tidak ada file</span>
                        @endif
                    </div>
                    @if(auth()->user()->role === 'admin' && $pengajuan->status === 'menunggu_validasi')
                    <hr>
                    <div class="mb-3">
                        <a href="{{ route('pengajuan.edit', $pengajuan->id) }}" class="btn btn-warning">Edit Data Pengajuan</a>
                    </div>
                    <form action="{{ route('validasi.validasi', $pengajuan->id) }}" method="POST" class="mt-3">
                        @csrf
                        <div class="mb-3">
                            <label for="status_validasi" class="form-label">Aksi Validasi</label>
                            <select id="status_validasi" name="status_validasi" class="form-select" required>
                                <option value="">Pilih Aksi</option>
                                <option value="disetujui">Setujui</option>
                                <option value="ditolak">Tolak</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="catatan_validasi" class="form-label">Catatan (wajib diisi jika menolak)</label>
                            <textarea id="catatan_validasi" name="catatan_validasi" class="form-control" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">Simpan Validasi</button>
                    </form>
                    @endif
                    
                    <!-- Informasi Pembayaran -->
                    @if(in_array($pengajuan->status, ['menunggu_pembayaran', 'menunggu_verifikasi_pembayaran', 'selesai']))
                    <hr>
                    <h5>Informasi Pembayaran</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <strong>Kode Billing:</strong>
                            @if($pengajuan->billing_code)
                                <span class="badge bg-success ms-2">{{ $pengajuan->billing_code }}</span>
                            @else
                                <span class="text-muted ms-2">Belum tersedia</span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <strong>Status Pembayaran:</strong>
                            @switch($pengajuan->status)
                                @case('menunggu_pembayaran')
                                    <span class="badge bg-warning text-dark ms-2">
                                        <i class="fas fa-clock me-1"></i>Menunggu Pembayaran
                                    </span>
                                    @break
                                @case('menunggu_verifikasi_pembayaran')
                                    <span class="badge bg-info text-dark ms-2">
                                        <i class="fas fa-hourglass-half me-1"></i>Menunggu Verifikasi
                                    </span>
                                    @break
                                @case('selesai')
                                    <span class="badge bg-success ms-2">
                                        <i class="fas fa-check-circle me-1"></i>Lunas
                                    </span>
                                    @break
                            @endswitch
                        </div>
                    </div>
                    
                    @if($pengajuan->bukti_pembayaran)
                    <div class="mt-3">
                        <strong>Bukti Pembayaran:</strong>
                        <div class="mt-2 d-flex align-items-center gap-2">
                                                                              @php
                         $extension = pathinfo($pengajuan->bukti_pembayaran, PATHINFO_EXTENSION);
                         $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']);
                         $isPdf = strtolower($extension) === 'pdf';
                         $fileType = $isImage ? 'image' : ($isPdf ? 'pdf' : 'other');
                         $fileName = basename($pengajuan->bukti_pembayaran);
                         $fileUrl = route('bukti.serve', $pengajuan->id);
                     @endphp
                            
                            <button type="button" class="btn btn-outline-primary btn-sm" 
                                    onclick="previewPaymentProof('{{ $fileUrl }}', '{{ $fileType }}', '{{ $fileName }}')">
                                <i class="fas fa-eye me-1"></i>Preview Bukti
                            </button>
                            
                                                         <a href="{{ route('bukti.serve', $pengajuan->id) }}" class="btn btn-outline-success btn-sm" target="_blank">
                                 <i class="fas fa-external-link-alt me-1"></i>Buka File
                             </a>
                            
                            <span class="text-muted small">
                                <i class="fas fa-file me-1"></i>{{ $fileName }}
                            </span>
                        </div>
                    </div>
                    @endif
                    
                    @if($pengajuan->status === 'menunggu_pembayaran' && $pengajuan->billing_code)
                    <div class="mt-3">
                        <a href="{{ route('pembayaran.pay', $pengajuan->id) }}" class="btn btn-success">
                            <i class="fas fa-wallet me-1"></i>Bayar Sekarang
                        </a>
                    </div>
                    @endif
                    @endif
                    
                    <!-- Informasi Sertifikat -->
                    @if($pengajuan->status === 'selesai' && $pengajuan->sertifikat)
                    <hr>
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-certificate me-2"></i>Sertifikat HKI</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-success d-flex align-items-center" role="alert">
                                <i class="fas fa-check-circle fs-4 me-3"></i>
                                <div>
                                    <strong>Selamat! Pengajuan Anda telah selesai</strong><br>
                                    <small class="text-muted">Sertifikat HKI sudah tersedia dan dapat diunduh</small>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('sertifikat.serve', $pengajuan->id) }}" target="_blank" class="btn btn-outline-primary">
                                    <i class="fas fa-eye me-1"></i>Preview Sertifikat
                                </a>
                                <a href="{{ route('sertifikat.serve', $pengajuan->id) }}" download class="btn btn-success">
                                    <i class="fas fa-download me-1"></i>Download Sertifikat
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <a href="{{ route('pengajuan.index') }}" class="btn btn-secondary mt-3">Kembali ke Daftar</a>
                </div>
            </div>
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
@endsection 

@push('scripts')
<!-- PDF.js Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

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

// PDF Viewer functionality
document.addEventListener('DOMContentLoaded', function() {
    // Check if PDF.js is available
    if (typeof pdfjsLib === 'undefined') {
        console.error('PDF.js library not loaded!');
        return;
    }
    
    // Set PDF.js worker
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    
    // Initialize PDF viewers for each document type
    const documentTypes = ['karya', 'pengalihan', 'pernyataan', 'ktp'];
    
    documentTypes.forEach(type => {
        initializePdfViewer(type);
    });
});

function initializePdfViewer(type) {
    const canvasMode = document.getElementById(`canvasMode_${type}`);
    const iframeMode = document.getElementById(`iframeMode_${type}`);
    const pdfWrapper = document.getElementById(`pdfWrapper_${type}`);
    const pdfWrapperIframe = document.getElementById(`pdfWrapperIframe_${type}`);
    const navControls = document.getElementById(`navControls_${type}`);
    const canvas = document.getElementById(`pdfCanvas_${type}`);
    const pageNumElement = document.getElementById(`pageNum_${type}`);
    const pageCountElement = document.getElementById(`pageCount_${type}`);
    const prevPageBtn = document.getElementById(`prevPage_${type}`);
    const nextPageBtn = document.getElementById(`nextPage_${type}`);
    
    if (!canvasMode || !iframeMode || !pdfWrapper || !pdfWrapperIframe || !canvas) {
        return; // Skip if elements don't exist
    }
    
    let pdfDoc = null;
    let currentPage = 1;
    let totalPages = 1;
    let scale = 1.0;
    let currentViewMode = 'iframe'; // Start with iframe mode
    
    // Get PDF URL from iframe src
    const iframe = pdfWrapperIframe.querySelector('iframe');
    const pdfUrl = iframe ? iframe.src : null;
    
    if (!pdfUrl) {
        return; // No PDF to load
    }
    
    // View mode event listeners
    if (canvasMode) {
        canvasMode.addEventListener('change', function() {
            if (this.checked) {
                switchViewMode('canvas');
            }
        });
    }
    
    if (iframeMode) {
        iframeMode.addEventListener('change', function() {
            if (this.checked) {
                switchViewMode('iframe');
            }
        });
    }
    
    // Navigation event listeners
    if (prevPageBtn) {
        prevPageBtn.addEventListener('click', function() {
            if (currentPage > 1) {
                currentPage--;
                renderPage(currentPage);
            }
        });
    }
    
    if (nextPageBtn) {
        nextPageBtn.addEventListener('click', function() {
            if (currentPage < totalPages) {
                currentPage++;
                renderPage(currentPage);
            }
        });
    }
    
    function switchViewMode(mode) {
        currentViewMode = mode;
        
        if (mode === 'canvas') {
            pdfWrapper.style.display = 'block';
            pdfWrapperIframe.style.display = 'none';
            
            // Load PDF if not already loaded
            if (!pdfDoc) {
                loadPDF();
            } else {
                renderPage(currentPage);
                updateNavigation();
            }
        } else {
            pdfWrapper.style.display = 'none';
            pdfWrapperIframe.style.display = 'block';
            if (navControls) {
                navControls.style.display = 'none';
            }
        }
    }
    
    function loadPDF() {
        pdfjsLib.getDocument(pdfUrl).promise.then(function(pdf) {
            pdfDoc = pdf;
            totalPages = pdf.numPages;
            currentPage = 1;
            
            // Calculate initial scale
            calculateFitScale().then(function(fitScale) {
                scale = fitScale;
                renderPage(currentPage);
            }).catch(function(error) {
                scale = 1.0;
                renderPage(currentPage);
            });
            
        }).catch(function(error) {
            console.error('Error loading PDF:', error);
        });
    }
    
    function calculateFitScale() {
        return new Promise((resolve, reject) => {
            if (!pdfDoc) {
                reject(new Error('PDF not loaded'));
                return;
            }
            
            pdfDoc.getPage(1).then(function(page) {
                const viewport = page.getViewport({ scale: 1 });
                const containerWidth = pdfWrapper.clientWidth - 40; // Account for padding
                const scaleToFit = containerWidth / viewport.width;
                resolve(Math.min(scaleToFit, 1.5)); // Max scale of 1.5
            }).catch(reject);
        });
    }
    
    function renderPage(pageNumber) {
        if (!pdfDoc) return;
        
        pdfDoc.getPage(pageNumber).then(function(page) {
            const viewport = page.getViewport({ scale: scale });
            const context = canvas.getContext('2d');
            
            canvas.height = viewport.height;
            canvas.width = viewport.width;
            
            const renderContext = {
                canvasContext: context,
                viewport: viewport
            };
            
            page.render(renderContext).promise.then(function() {
                updateNavigation();
            });
        }).catch(function(error) {
            console.error('Error rendering page:', error);
        });
    }
    
    function updateNavigation() {
        if (pageNumElement) pageNumElement.textContent = currentPage;
        if (pageCountElement) pageCountElement.textContent = totalPages;
        
        if (prevPageBtn) prevPageBtn.disabled = (currentPage <= 1);
        if (nextPageBtn) nextPageBtn.disabled = (currentPage >= totalPages);
        
        // Show navigation controls only in canvas mode and if more than 1 page
        if (totalPages > 1 && navControls && currentViewMode === 'canvas') {
            navControls.style.display = 'flex';
        } else if (navControls) {
            navControls.style.display = 'none';
        }
    }
    
    // Start with iframe mode (default)
    switchViewMode('iframe');
}
</script>
@endpush 