@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card shadow rounded-lg border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Validasi Pengajuan HKI</h4>
                    <a href="{{ route('validasi.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Nomor Pengajuan</dt>
                        <dd class="col-sm-8">{{ $pengajuan->nomor_pengajuan ?? '-' }}</dd>
                        <dt class="col-sm-4">Judul Karya</dt>
                        <dd class="col-sm-8">{{ $pengajuan->judul_karya ?? '-' }}</dd>
                        <dt class="col-sm-4">Deskripsi</dt>
                        <dd class="col-sm-8">{{ $pengajuan->deskripsi ?? '-' }}</dd>
                        <dt class="col-sm-4">Jenis Ciptaan</dt>
                        <dd class="col-sm-8">{{ $pengajuan->identitas_ciptaan ?? '-' }}</dd>
                        <dt class="col-sm-4">Sub Jenis Ciptaan</dt>
                        <dd class="col-sm-8">{{ $pengajuan->sub_jenis_ciptaan ?? '-' }}</dd>
                        <dt class="col-sm-4">Tahun Usulan</dt>
                        <dd class="col-sm-8">{{ $pengajuan->tahun_usulan ?? '-' }}</dd>
                        <dt class="col-sm-4">Jumlah Pencipta</dt>
                        <dd class="col-sm-8">{{ $pengajuan->jumlah_pencipta ?? '-' }}</dd>
                        <dt class="col-sm-4">Status</dt>
                        <dd class="col-sm-8">{{ ucfirst(str_replace('_', ' ', $pengajuan->status)) }}</dd>
                        <dt class="col-sm-4">Tanggal Pengajuan</dt>
                        <dd class="col-sm-8">{{ $pengajuan->tanggal_pengajuan ? $pengajuan->tanggal_pengajuan->format('d/m/Y H:i') : '-' }}</dd>
                        <dt class="col-sm-4">Nama Pengusul</dt>
                        <dd class="col-sm-8">{{ $pengajuan->nama_pengusul ?? '-' }}</dd>
                        <dt class="col-sm-4">NIP/NIDN</dt>
                        <dd class="col-sm-8">{{ $pengajuan->nip_nidn ?? '-' }}</dd>
                        <dt class="col-sm-4">No HP</dt>
                        <dd class="col-sm-8">{{ $pengajuan->no_hp ?? '-' }}</dd>
                        <dt class="col-sm-4">ID Sinta</dt>
                        <dd class="col-sm-8">{{ $pengajuan->id_sinta ?? '-' }}</dd>
                        <dt class="col-sm-4">Tanggal Pertama Kali Diumumkan</dt>
                        <dd class="col-sm-8">{{ $pengajuan->tanggal_pertama_kali_diumumkan ?? '-' }}</dd>
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
                    <h5>Berkas/Dokumen</h5>
                    <div class="mb-3">
                        <strong>File Karya:</strong>
                        @if($pengajuan->file_karya)
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
                                             <iframe src="{{ $url }}#view=FitV&scrollbar=1&toolbar=1&navpanes=1&statusbar=1" width="100%" height="600px" style="border: none;">
                                                 <p>Browser Anda tidak mendukung iframe. <a href="{{ $url }}" target="_blank">Klik di sini untuk membuka PDF.</a></p>
                                             </iframe>
                                         </div>
                                    </div>
                                </div>
                            @else
                                <a href="{{ $url }}" class="btn btn-primary btn-sm ms-2" target="_blank">Download File Karya</a>
                            @endif
                        @else
                            <span class="text-muted">Tidak ada file</span>
                        @endif
                    </div>
                    @php $dokumen = is_string($pengajuan->file_dokumen_pendukung) ? json_decode($pengajuan->file_dokumen_pendukung, true) : ($pengajuan->file_dokumen_pendukung ?? []); @endphp
                    <div class="mb-3">
                        <strong>Surat Pengalihan Hak Cipta:</strong>
                        @php
                            // Use enhanced file selection logic
                            $suratPengalihanPath = $dokumen['surat_pengalihan'] ?? null;
                            
                            // If there's a display override (fallback to original), use it
                            if (isset($dokumen['display']['surat_pengalihan'])) {
                                $suratPengalihanPath = $dokumen['display']['surat_pengalihan'];
                            } elseif (isset($dokumen['signed']) && isset($dokumen['signed']['surat_pengalihan'])) {
                                // Otherwise use signed file if available
                                $suratPengalihanPath = $dokumen['signed']['surat_pengalihan'];
                            }
                        @endphp
                        @if($suratPengalihanPath)
                            @php $url = Storage::url($suratPengalihanPath); $ext = pathinfo($suratPengalihanPath, PATHINFO_EXTENSION); @endphp
                            @if(strtolower($ext) === 'pdf')
                                <div class="my-2">
                                    <div class="pdf-viewer-container border rounded" style="background: #f8f9fa;">
                                        <!-- View Mode Toggle -->
                                        <div class="d-flex justify-content-center align-items-center p-2 bg-light border-bottom">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <input type="radio" class="btn-check" name="viewMode_suratPengalihan" id="canvasMode_suratPengalihan" checked>
                                                <label class="btn btn-outline-primary" for="canvasMode_suratPengalihan">
                                                    <i class="fas fa-edit me-1"></i>Mode Interaktif
                                                </label>
                                                
                                                <input type="radio" class="btn-check" name="viewMode_suratPengalihan" id="iframeMode_suratPengalihan">
                                                <label class="btn btn-outline-success" for="iframeMode_suratPengalihan">
                                                    <i class="fas fa-file-pdf me-1"></i>Tampilkan Semua Halaman
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Navigation Controls -->
                                        <div class="d-flex justify-content-center align-items-center p-2 bg-light border-bottom" id="navControls_suratPengalihan" style="display: none !important;">
                                            <button id="prevPage_suratPengalihan" class="btn btn-outline-primary btn-sm me-2">
                                                <i class="fas fa-chevron-left"></i>
                                            </button>
                                            <span class="mx-3">
                                                <strong>Halaman <span id="pageNum_suratPengalihan">1</span> dari <span id="pageCount_suratPengalihan">1</span></strong>
                                            </span>
                                            <button id="nextPage_suratPengalihan" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-chevron-right"></i>
                                            </button>
                                        </div>

                                        <!-- PDF Container - Canvas Mode -->
                                        <div id="pdfWrapper_suratPengalihan" style="position:relative; overflow:auto; background: #f8f9fa; min-height: 400px; display: none;">
                                            <div style="position: relative; text-align: center; padding: 20px;">
                                                <canvas id="pdfCanvas_suratPengalihan" style="max-width: 100%; display: block; margin: 0 auto; box-shadow: 0 4px 8px rgba(0,0,0,0.1);"></canvas>
                                            </div>
                                        </div>

                                                                                 <!-- PDF Container - Iframe Mode -->
                                         <div id="pdfWrapperIframe_suratPengalihan" style="position:relative; overflow:auto; background: #f8f9fa; min-height: 400px;">
                                             <iframe src="{{ $url }}#view=FitV&scrollbar=1&toolbar=1&navpanes=1&statusbar=1" width="100%" height="600px" style="border: none;">
                                                 <p>Browser Anda tidak mendukung iframe. <a href="{{ $url }}" target="_blank">Klik di sini untuk membuka PDF.</a></p>
                                             </iframe>
                                         </div>

                                        <!-- Debug Info -->
                                        <div class="p-2 bg-light border-top">
                                            <small class="text-muted">
                                                <strong>Debug:</strong> 
                                                @if(isset($dokumen['display']['surat_pengalihan']))
                                                    Using original file (signed file invalid)
                                                @elseif(isset($dokumen['signed']['surat_pengalihan']))
                                                    Using signed file
                                                @else
                                                    Using original file
                                                @endif
                                                - {{ basename($url) }}
                                                @if(isset($documentsPageInfo['surat_pengalihan']))
                                                    | Backend detected: {{ $documentsPageInfo['surat_pengalihan']['pageCount'] }} pages
                                                @endif
                                            </small>
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
                        @php
                            // Use enhanced file selection logic
                            $suratPernyataanPath = $dokumen['surat_pernyataan'] ?? null;
                            
                            // If there's a display override (fallback to original), use it
                            if (isset($dokumen['display']['surat_pernyataan'])) {
                                $suratPernyataanPath = $dokumen['display']['surat_pernyataan'];
                            } elseif (isset($dokumen['signed']) && isset($dokumen['signed']['surat_pernyataan'])) {
                                // Otherwise use signed file if available
                                $suratPernyataanPath = $dokumen['signed']['surat_pernyataan'];
                            }
                        @endphp
                        @if($suratPernyataanPath)
                            @php $url = Storage::url($suratPernyataanPath); $ext = pathinfo($suratPernyataanPath, PATHINFO_EXTENSION); @endphp
                            @if(strtolower($ext) === 'pdf')
                                <div class="my-2">
                                    <div class="pdf-viewer-container border rounded" style="background: #f8f9fa;">
                                        <!-- View Mode Toggle -->
                                        <div class="d-flex justify-content-center align-items-center p-2 bg-light border-bottom">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <input type="radio" class="btn-check" name="viewMode_suratPernyataan" id="canvasMode_suratPernyataan" checked>
                                                <label class="btn btn-outline-primary" for="canvasMode_suratPernyataan">
                                                    <i class="fas fa-edit me-1"></i>Mode Interaktif
                                                </label>
                                                
                                                <input type="radio" class="btn-check" name="viewMode_suratPernyataan" id="iframeMode_suratPernyataan">
                                                <label class="btn btn-outline-success" for="iframeMode_suratPernyataan">
                                                    <i class="fas fa-file-pdf me-1"></i>Tampilkan Semua Halaman
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Navigation Controls -->
                                        <div class="d-flex justify-content-center align-items-center p-2 bg-light border-bottom" id="navControls_suratPernyataan" style="display: none !important;">
                                            <button id="prevPage_suratPernyataan" class="btn btn-outline-primary btn-sm me-2">
                                                <i class="fas fa-chevron-left"></i>
                                            </button>
                                            <span class="mx-3">
                                                <strong>Halaman <span id="pageNum_suratPernyataan">1</span> dari <span id="pageCount_suratPernyataan">1</span></strong>
                                            </span>
                                            <button id="nextPage_suratPernyataan" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-chevron-right"></i>
                                            </button>
                                        </div>

                                        <!-- PDF Container - Canvas Mode -->
                                        <div id="pdfWrapper_suratPernyataan" style="position:relative; overflow:auto; background: #f8f9fa; min-height: 400px; display: none;">
                                            <div style="position: relative; text-align: center; padding: 20px;">
                                                <canvas id="pdfCanvas_suratPernyataan" style="max-width: 100%; display: block; margin: 0 auto; box-shadow: 0 4px 8px rgba(0,0,0,0.1);"></canvas>
                                            </div>
                                        </div>

                                                                                 <!-- PDF Container - Iframe Mode -->
                                         <div id="pdfWrapperIframe_suratPernyataan" style="position:relative; overflow:auto; background: #f8f9fa; min-height: 400px;">
                                             <iframe src="{{ $url }}#view=FitV&scrollbar=1&toolbar=1&navpanes=1&statusbar=1" width="100%" height="600px" style="border: none;">
                                                 <p>Browser Anda tidak mendukung iframe. <a href="{{ $url }}" target="_blank">Klik di sini untuk membuka PDF.</a></p>
                                             </iframe>
                                         </div>

                                        <!-- Debug Info -->
                                        <div class="p-2 bg-light border-top">
                                            <small class="text-muted">
                                                <strong>Debug:</strong> 
                                                @if(isset($dokumen['display']['surat_pernyataan']))
                                                    Using original file (signed file invalid)
                                                @elseif(isset($dokumen['signed']['surat_pernyataan']))
                                                    Using signed file
                                                @else
                                                    Using original file
                                                @endif
                                                - {{ basename($url) }}
                                                @if(isset($documentsPageInfo['surat_pernyataan']))
                                                    | Backend detected: {{ $documentsPageInfo['surat_pernyataan']['pageCount'] }} pages
                                                @endif
                                            </small>
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
                                             <iframe src="{{ $url }}#view=FitV&scrollbar=1&toolbar=1&navpanes=1&statusbar=1" width="100%" height="600px" style="border: none;">
                                                 <p>Browser Anda tidak mendukung iframe. <a href="{{ $url }}" target="_blank">Klik di sini untuk membuka PDF.</a></p>
                                             </iframe>
                                         </div>

                                        <!-- Debug Info -->
                                        <div class="p-2 bg-light border-top">
                                            <small class="text-muted">
                                                <strong>Debug:</strong> 
                                                KTP Document - {{ basename($url) }}
                                                @if(isset($documentsPageInfo['ktp']))
                                                    | Backend detected: {{ $documentsPageInfo['ktp']['pageCount'] }} pages
                                                @endif
                                            </small>
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
                    
                    <!-- Panel Tanda Tangan & Materai -->
                    @if(in_array(auth()->user()->role, ['admin', 'direktur']))
                    <hr>
                    <div class="card border-primary mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-signature me-2"></i>Tanda Tangan & Materai Dokumen
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-3">
                                <i class="fas fa-info-circle me-1"></i>
                                Kelola tanda tangan dan materai untuk dokumen surat pengalihan dan surat pernyataan.
                            </p>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body text-center">
                                            <i class="fas fa-file-signature fa-3x text-primary mb-3"></i>
                                            <h6 class="card-title">Surat Pengalihan</h6>
                                            <p class="card-text small text-muted">Tambahkan tanda tangan dan materai pada surat pengalihan hak cipta</p>
                                            @if(isset($dokumen['surat_pengalihan']) && $dokumen['surat_pengalihan'])
                                                <a href="{{ route('validasi.signature.editor', [$pengajuan->id, 'surat_pengalihan']) }}" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-edit me-1"></i>Kelola TTD & Materai
                                                </a>
                                            @else
                                                <button class="btn btn-secondary btn-sm" disabled>
                                                    <i class="fas fa-times me-1"></i>Dokumen Tidak Ada
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body text-center">
                                            <i class="fas fa-file-contract fa-3x text-success mb-3"></i>
                                            <h6 class="card-title">Surat Pernyataan</h6>
                                            <p class="card-text small text-muted">Tambahkan tanda tangan dan materai pada surat pernyataan hak cipta</p>
                                            @if(isset($dokumen['surat_pernyataan']) && $dokumen['surat_pernyataan'])
                                                <a href="{{ route('validasi.signature.editor', [$pengajuan->id, 'surat_pernyataan']) }}" class="btn btn-success btn-sm">
                                                    <i class="fas fa-edit me-1"></i>Kelola TTD & Materai
                                                </a>
                                            @else
                                                <button class="btn btn-secondary btn-sm" disabled>
                                                    <i class="fas fa-times me-1"></i>Dokumen Tidak Ada
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-lightbulb me-2"></i>
                                <strong>Tips:</strong> Pastikan untuk menambahkan tanda tangan dan materai sebelum menyelesaikan proses validasi.
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <hr>
                    @if(in_array(auth()->user()->role, ['admin']))
                    <form action="{{ route('validasi.validasi', $pengajuan->id) }}" method="POST" class="mt-4">
                        @csrf

                        <div class="mb-3">
                            <label for="status" class="form-label">Status Validasi</label>
                            <select id="status_validasi" name="status_validasi" class="form-select" required>
                                <option value="">Pilih Status</option>
                                <option value="disetujui">Setujui</option>
                                <option value="ditolak">Tolak</option>
                            </select>
                            @error('status_validasi')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="catatan_validasi" class="form-label">Catatan</label>
                            <textarea id="catatan_validasi" name="catatan_validasi" rows="4" class="form-control" required></textarea>
                            @error('catatan_validasi')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <button type="submit" class="btn btn-success">Simpan Validasi</button>
                    </form>
                    @else
                    <div class="mt-4">
                        <div class="mb-2"><strong>Status Validasi:</strong> {{ ucfirst($pengajuan->status_validasi ?? '-') }}</div>
                        <div><strong>Catatan:</strong> {{ $pengajuan->catatan_validasi ?? '-' }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- PDF.js Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<!-- Page count data from backend -->
<script>
    window.backendPageCounts = @json($documentsPageInfo ?? []);
</script>
<script>
// PDF Viewer functionality
document.addEventListener('DOMContentLoaded', function() {
    // Check if PDF.js is available
    if (typeof pdfjsLib === 'undefined') {
        console.error('PDF.js library not loaded!');
        return;
    }
    
    // Set PDF.js worker
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    
    // Backend page count data
    const backendPageCounts = window.backendPageCounts || {};
    
    // Initialize PDF viewers for each document type
    const documentTypes = ['karya', 'suratPengalihan', 'suratPernyataan', 'ktp'];
    
    documentTypes.forEach(type => {
        initializePdfViewer(type, backendPageCounts);
    });
});

function initializePdfViewer(type, backendPageCounts) {
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
                if (currentViewMode === 'canvas') {
                    renderPage(currentPage);
                } else {
                    // For iframe mode, simulate page navigation
                    navigateIframePage(currentPage);
                }
            }
        });
    }
    
    if (nextPageBtn) {
        nextPageBtn.addEventListener('click', function() {
            if (currentPage < totalPages) {
                currentPage++;
                if (currentViewMode === 'canvas') {
                    renderPage(currentPage);
                } else {
                    // For iframe mode, simulate page navigation
                    navigateIframePage(currentPage);
                }
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
            
            // In iframe mode, update navigation based on backend count
            const documentKey = getDocumentKey(type);
            const backendPageCount = backendPageCounts[documentKey]?.pageCount || 0;
            
            if (backendPageCount > 0) {
                totalPages = backendPageCount;
                console.log(`[${type}] Iframe mode: Using backend count ${totalPages} pages`);
                updateNavigation();
            } else if (navControls) {
                navControls.style.display = 'none';
            }
        }
    }
    
    function loadPDF() {
        console.log(`[${type}] Loading PDF from: ${pdfUrl}`);
        
        // Check if we have backend page count data
        const documentKey = getDocumentKey(type);
        const backendPageCount = backendPageCounts[documentKey]?.pageCount || 0;
        
        console.log(`[${type}] Backend page count data:`, backendPageCount);
        
        // If we have backend data, use it immediately for navigation
        if (backendPageCount > 0) {
            totalPages = backendPageCount;
            console.log(`[${type}] AUTHORITATIVE: Using backend page count: ${totalPages}`);
            
            // Update navigation immediately based on backend count
            if (currentViewMode === 'canvas') {
                updateNavigation();
            }
        }
        
        pdfjsLib.getDocument(pdfUrl).promise.then(function(pdf) {
            pdfDoc = pdf;
            
            console.log(`[${type}] PDF.js detected: ${pdf.numPages} pages`);
            
            // Always prioritize backend count if available
            if (backendPageCount > 0) {
                totalPages = backendPageCount;
                console.log(`[${type}] FINAL: Using backend count ${totalPages} (overriding PDF.js ${pdf.numPages})`);
            } else {
                totalPages = pdf.numPages;
                console.log(`[${type}] FINAL: Using PDF.js count ${totalPages} (no backend data)`);
            }
            
            currentPage = 1;
            
            // Calculate initial scale
            calculateFitScale().then(function(fitScale) {
                scale = fitScale;
                console.log(`[${type}] Using initial scale: ${scale.toFixed(2)}`);
                renderPage(currentPage);
            }).catch(function(error) {
                console.log(`[${type}] Scale calculation failed, using default scale: ${error.message}`);
                scale = 1.0;
                renderPage(currentPage);
            });
            
        }).catch(function(error) {
            console.error(`[${type}] Error loading PDF:`, error);
            
            // Fallback to backend page count if PDF.js fails
            if (backendPageCount > 0) {
                totalPages = backendPageCount;
                console.log(`[${type}] PDF.js failed, using backend count: ${backendPageCount} pages`);
                console.log(`[${type}] Switching to iframe mode for better compatibility`);
                
                // Force switch to iframe mode
                if (iframeMode) {
                    iframeMode.checked = true;
                    switchViewMode('iframe');
                }
            } else {
                console.error(`[${type}] No fallback available - both PDF.js and backend failed`);
            }
        });
    }
    
    function getDocumentKey(type) {
        const keyMap = {
            'karya': 'karya',
            'suratPengalihan': 'surat_pengalihan',
            'suratPernyataan': 'surat_pernyataan',
            'ktp': 'ktp'
        };
        return keyMap[type] || type;
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
    
    function navigateIframePage(pageNumber) {
        const iframe = pdfWrapperIframe.querySelector('iframe');
        if (iframe && iframe.contentWindow) {
            try {
                // Try to navigate to specific page in PDF viewer
                const newSrc = iframe.src.split('#')[0] + `#page=${pageNumber}&view=FitV&scrollbar=1&toolbar=1&navpanes=1&statusbar=1`;
                iframe.src = newSrc;
                console.log(`[${type}] Iframe navigated to page ${pageNumber}`);
            } catch (error) {
                console.log(`[${type}] Could not navigate iframe, using scroll approximation`);
                // Fallback: scroll based on page estimation
                const scrollPercent = (pageNumber - 1) / (totalPages - 1);
                if (iframe.contentDocument) {
                    iframe.contentDocument.documentElement.scrollTop = 
                        iframe.contentDocument.documentElement.scrollHeight * scrollPercent;
                }
            }
        }
        updateNavigation();
    }

    function updateNavigation() {
        if (pageNumElement) pageNumElement.textContent = currentPage;
        if (pageCountElement) pageCountElement.textContent = totalPages;
        
        if (prevPageBtn) prevPageBtn.disabled = (currentPage <= 1);
        if (nextPageBtn) nextPageBtn.disabled = (currentPage >= totalPages);
        
        console.log(`[${type}] updateNavigation: totalPages=${totalPages}, mode=${currentViewMode}`);
        
        // Show navigation controls if more than 1 page (regardless of mode)
        if (totalPages > 1 && navControls) {
            navControls.style.display = 'flex';
            console.log(`[${type}] Navigation shown: ${totalPages} pages detected`);
        } else if (navControls) {
            navControls.style.display = 'none';
            console.log(`[${type}] Navigation hidden: only ${totalPages} page(s)`);
        }
    }
    
    // Initialize with backend page count if available
    const documentKey = getDocumentKey(type);
    const backendPageCount = backendPageCounts[documentKey]?.pageCount || 0;
    
    if (backendPageCount > 0) {
        totalPages = backendPageCount;
        console.log(`[${type}] Initial setup: Using backend count ${totalPages} pages`);
    }
    
    // Start with iframe mode (default)
    switchViewMode('iframe');
}
</script>
@endpush