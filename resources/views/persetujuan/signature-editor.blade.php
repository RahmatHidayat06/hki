@extends('layouts.app')

@section('title', 'Editor Tanda Tangan Direktur')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">
                        <i class="fas fa-signature me-2"></i>
                            Editor Tanda Tangan Direktur
                    </h4>
                        <small class="opacity-75">
                            <i class="fas fa-file-alt me-1"></i>
                            {{ ucfirst(str_replace('_', ' ', $documentType)) }} - {{ $pengajuan->judul_karya }}
                        </small>
                    </div>
                    <a href="{{ route('persetujuan.validation.wizard', $pengajuan->id) }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Kembali ke Validasi
                    </a>
                </div>
                
                <!-- Info Panel -->
                <div class="alert alert-info border-0 rounded-0 mb-0">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-info-circle fa-lg me-3"></i>
                                <div>
                                    <strong>Informasi Dokumen:</strong><br>
                                    <small>File yang akan dihasilkan: <code>{{ $pengajuan->id }}_{{ $documentType }}_[timestamp].pdf</code></small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="badge bg-primary fs-6">Pengajuan #{{ $pengajuan->id }}</span>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="row g-0">
                        <!-- Area Kiri: Tempat Tanda Tangan -->
                        <div class="col-lg-8">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 text-dark">
                                    <i class="fas fa-file-pdf me-2 text-danger"></i>
                                    Preview Dokumen
                                </h5>
                                <div class="btn-group">
                                    <a href="{{ $documentUrl }}" target="_blank" class="btn btn-primary btn-sm">
                                        <i class="fas fa-external-link-alt me-1"></i>Buka di Tab Baru
                                    </a>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="resetViewBtn">
                                        <i class="fas fa-sync-alt me-1"></i>Reset View
                                    </button>
                            </div>
                            </div>
                            
                            <!-- PDF Navigation -->
                            <div id="pdfNavigation" class="pdf-navigation d-none">
                                <div class="d-flex justify-content-between align-items-center p-2 bg-light border-bottom">
                                    <div>
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="prevPage" disabled>
                                            <i class="fas fa-chevron-left"></i> Sebelumnya
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="nextPage">
                                            Selanjutnya <i class="fas fa-chevron-right"></i>
                                        </button>
                                    </div>
                                    <div class="page-info">
                                        <span class="badge bg-secondary">
                                            Halaman <span id="currentPageNum">1</span> dari <span id="totalPagesNum">1</span>
                                        </span>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="zoomOut">
                                            <i class="fas fa-search-minus"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="zoomIn">
                                            <i class="fas fa-search-plus"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-info" id="fitWidth">
                                            <i class="fas fa-arrows-alt-h"></i> Fit Width
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="document-viewer-container position-relative bg-secondary-subtle rounded-bottom shadow-inner" style="min-height: 80vh;">
                                <div id="documentContainer" class="document-container position-relative" style="background: linear-gradient(45deg, #f8f9fa 25%, transparent 25%), linear-gradient(-45deg, #f8f9fa 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #f8f9fa 75%), linear-gradient(-45deg, transparent 75%, #f8f9fa 75%); background-size: 20px 20px; background-position: 0 0, 0 10px, 10px -10px, -10px 0px; border: 2px dashed #ced4da; min-height: 80vh; overflow: auto; max-height: 80vh;">
                                    <canvas id="pdfCanvas" style="width:100%; height:auto; pointer-events:none; display: block; margin: 0 auto; background: white; box-shadow: 0 4px 12px rgba(0,0,0,0.1);"></canvas>
                                    <div id="drag-container" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index:10;">
                                        {{-- Item yang sudah ada akan dimuat di sini oleh JS --}}
                                </div>
                                    
                                    <div class="placeholder-text" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; color: #6c757d; z-index: 0;">
                                        <i class="fas fa-file-signature fa-5x mb-3"></i>
                                        <h5 class="fw-bold">Area Penandatanganan</h5>
                                        <p class="mb-2">Seret tanda tangan dari panel kanan ke area ini</p>
                                        <small class="text-muted">File akan disimpan dengan nama: {{ $pengajuan->id }}_{{ $documentType }}_[timestamp].pdf</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Panel Kanan: Kontrol -->
                        <div class="col-lg-4 border-start bg-light">
                            <div class="controls-panel position-sticky" style="top: 0; height: 100vh; overflow-y: auto;">
                                <div class="p-4">
                                <!-- Signature Selection -->
                                <div class="mb-4">
                                        <h5 class="text-primary d-flex align-items-center mb-3">
                                            <i class="fas fa-pen-fancy me-2"></i>
                                            Tanda Tangan Tersimpan
                                        </h5>
                                        
                                        @if(count($signatures) > 0)
                                            <!-- Dropdown untuk Signature Selection -->
                                            <div class="dropdown mb-3">
                                                <button class="btn btn-outline-primary dropdown-toggle w-100" type="button" id="signatureDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-signature me-2"></i>
                                                    Pilih Tanda Tangan ({{ count($signatures) }} tersedia)
                                                </button>
                                                <div class="dropdown-menu w-100 p-2" aria-labelledby="signatureDropdown" style="max-height: 300px; overflow-y: auto;">
                                                    @foreach($signatures as $index => $signature)
                                                        <div class="dropdown-item draggable-item d-flex justify-content-between align-items-center p-2 border rounded mb-2" 
                                                             data-type="signature" 
                                                             data-url="{{ $signature['url'] }}" 
                                                             data-path="{{ $signature['path'] ?? '' }}" 
                                                             style="cursor: grab; background: white;">
                                                            <div class="d-flex align-items-center flex-grow-1">
                                                                <img src="{{ $signature['url'] }}" class="me-2 border rounded" style="max-height: 30px; max-width: 60px; object-fit: contain; background: white;">
                                                                <div class="flex-grow-1">
                                                                    <div class="fw-semibold small">{{ $signature['name'] }}</div>
                                                                    <small class="text-muted">Seret ke dokumen</small>
                                                </div>
                                            </div>
                                                            <div class="d-flex gap-1">
                                                                <button type="button" class="btn btn-sm btn-success use-signature" data-url="{{ $signature['url'] }}" title="Gunakan tanda tangan">
                                                                    <i class="fas fa-plus"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-sm btn-outline-danger delete-signature" data-path="{{ $signature['path'] ?? '' }}" title="Hapus tanda tangan">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                        </div>
                                </div>

                                            <!-- Quick Access Signatures (Max 3) -->
                                            <div class="quick-signatures mb-3">
                                                <small class="text-muted mb-2 d-block">Akses Cepat:</small>
                                                <div class="row g-2">
                                                    @foreach(array_slice($signatures, 0, 3) as $signature)
                                                        <div class="col-4">
                                                            <div class="signature-quick-item draggable-item border rounded p-2 text-center bg-white" 
                                                                 data-type="signature" 
                                                                 data-url="{{ $signature['url'] }}" 
                                                                 data-path="{{ $signature['path'] ?? '' }}" 
                                                                 style="cursor: grab;" 
                                                                 title="{{ $signature['name'] }}">
                                                                <img src="{{ $signature['url'] }}" class="img-fluid rounded" style="max-height: 25px; max-width: 100%; object-fit: contain; background: white;">
                                                                <div class="small text-muted mt-1">{{ Str::limit($signature['name'], 8) }}</div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @else
                                            <div class="alert alert-info text-center py-3">
                                                <i class="fas fa-info-circle fa-2x mb-2"></i>
                                                <br><strong>Belum ada tanda tangan tersimpan</strong>
                                                <br><small>Buat tanda tangan baru di bawah ini</small>
                                            </div>
                                        @endif
                                    </div>

                                <!-- Create New Signature -->
                                <div class="mb-4">
                                    <h6 class="text-success d-flex align-items-center mb-3">
                                        <i class="fas fa-plus-circle me-2"></i>
                                        Buat Tanda Tangan Baru
                                    </h6>
                            
                            <!-- Signature Method Tabs -->
                            <ul class="nav nav-pills nav-fill mb-3" id="signatureMethodTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="draw-tab" data-bs-toggle="pill" data-bs-target="#draw-signature" type="button" role="tab">
                                        <i class="fas fa-pencil-alt me-1"></i>Gambar
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="upload-tab" data-bs-toggle="pill" data-bs-target="#upload-signature" type="button" role="tab">
                                        <i class="fas fa-upload me-1"></i>Upload
                                    </button>
                                </li>
                            </ul>
                            
                            <!-- Signature Method Content -->
                            <div class="tab-content" id="signatureMethodContent">
                                <!-- Draw Signature -->
                                <div class="tab-pane fade show active" id="draw-signature" role="tabpanel">
                                            <div class="signature-container border rounded-3 p-3 bg-white shadow-sm">
                                                <canvas id="signaturePad" width="300" height="150" class="signature-pad border rounded w-100" style="background: white;"></canvas>
                                                <div class="text-center small text-muted mt-2">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    Gambar tanda tangan di area putih di atas
                                                </div>
                                                <div class="d-flex gap-2 mt-2">
                                            <button type="button" class="btn btn-outline-danger btn-sm flex-fill" onclick="clearSignature()">
                                                <i class="fas fa-eraser me-1"></i>Hapus
                                            </button>
                                                    <button type="button" class="btn btn-success btn-sm flex-fill" id="saveSignatureBtn">
                                                        <i class="fas fa-save me-1"></i>Simpan TTD
                                            </button>
                                        </div>
                                        </div>
                                </div>

                                <!-- Upload Signature -->
                                <div class="tab-pane fade" id="upload-signature" role="tabpanel">
                                            <div class="upload-container border rounded-3 p-3 bg-white shadow-sm">
                                        <input type="file" name="signature_file" id="signatureFile" class="form-control mb-2" accept="image/*">
                                        <div class="preview-container mt-2" id="signaturePreview" style="display: none;">
                                            <img id="signatureImage" src="" class="img-fluid rounded border" style="max-height: 150px; background: white;">
                                                    <button type="button" class="btn btn-outline-danger btn-sm mt-2 w-100" onclick="removeSignatureFile()">
                                                <i class="fas fa-trash me-1"></i>Hapus Gambar
                                            </button>
                                        </div>
                                                <div class="form-text">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    Format: JPG, PNG (Max: 2MB)<br>
                                                    Background transparan disarankan
                                                </div>
                                                <button type="button" class="btn btn-success w-100 mt-2" id="saveUploadBtn">
                                                    <i class="fas fa-save me-1"></i>Simpan TTD Upload
                                        </button>
                                    </div>
                                    </div>
                                </div>

                            <input type="hidden" name="signature_data" id="signatureData">
                            <input type="hidden" name="signature_method" id="signatureMethod" value="draw">
                                    </div>

                                <hr class="my-4">

                                <!-- File Generation Info -->
                                <div class="mb-4">
                                    <h6 class="text-info d-flex align-items-center mb-3">
                                        <i class="fas fa-file-signature me-2"></i>
                                        Informasi File yang Dihasilkan
                                    </h6>
                                    <div class="bg-white border rounded-3 p-3">
                                        <div class="row g-2 small">
                                            <div class="col-12">
                                                <strong>Nama File:</strong><br>
                                                <code class="text-primary">{{ $pengajuan->id }}_{{ $documentType }}_[timestamp].pdf</code>
                                </div>
                                            <div class="col-6">
                                                <strong>Pengaju:</strong><br>
                                                {{ $pengajuan->user->name }}
                                            </div>
                                            <div class="col-6">
                                                <strong>Judul Karya:</strong><br>
                                                {{ Str::limit($pengajuan->judul_karya, 20) }}
                                            </div>
                                            <div class="col-12 mt-2">
                                                <div class="alert alert-warning alert-sm mb-0 py-2">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                    File akan menggantikan dokumen asli setelah ditandatangani
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons - Sticky Bottom -->
                                </div>
                                <div class="action-buttons-container position-sticky bottom-0 bg-light p-4 border-top">
                                <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-success btn-lg shadow" id="saveBtn">
                                            <i class="fas fa-save me-2"></i>Simpan & Tandatangani Dokumen
                                    </button>
                                        <div class="row g-2">
                                            <div class="col-4">
                                                <button type="button" class="btn btn-outline-danger w-100" id="clearBtn">
                                                    <i class="fas fa-trash me-1"></i>Bersihkan
                                    </button>
                                            </div>
                                            <div class="col-4">
                                                <button type="button" class="btn btn-outline-warning w-100" id="discardBtn">
                                                    <i class="fas fa-ban me-1"></i>Buang
                                                </button>
                                            </div>
                                            <div class="col-4">
                                                <button type="button" class="btn btn-outline-secondary w-100" id="previewBtn">
                                                    <i class="fas fa-eye me-1"></i>Preview
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

<style>
    .draggable-item-clone { 
        z-index: 1050; 
        cursor: grabbing; 
        border: 2px solid #0d6efd; 
        background: rgba(255, 255, 255, 0.9); 
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .placed-item { 
        position: absolute; 
        cursor: move; 
        border: 1px solid transparent; 
        background: transparent;
        transition: all 0.2s ease;
    }
    .placed-item:hover {
        border: 1px solid rgba(0, 123, 255, 0.3);
        box-shadow: 0 2px 8px rgba(0, 123, 255, 0.2);
    }
    .placed-item img { 
        width: 100%; 
        height: 100%; 
        mix-blend-mode: multiply; 
        object-fit: contain;
    }
    .ui-resizable-handle { 
        background: rgba(0, 123, 255, 0.7); 
        border: 1px solid rgba(0, 123, 255, 0.9); 
        width: 8px; 
        height: 8px; 
        z-index: 90; 
        border-radius: 50%;
        opacity: 0.7;
    }
    .ui-resizable-handle:hover {
        opacity: 1;
        background: rgba(0, 123, 255, 0.9);
    }
    .placeholder-text { 
        display: none !important; 
    }

    /* PDF Navigation */
    .pdf-navigation {
        background: #f8f9fa !important;
        border-bottom: 1px solid #dee2e6 !important;
        position: sticky;
        top: 0;
        z-index: 100;
    }

    .pdf-navigation .btn {
        font-size: 0.875rem;
    }

    .page-info .badge {
        font-size: 0.875rem;
        padding: 0.5rem 0.75rem;
    }

    /* Canvas container improvements */
    .document-container {
        overflow: auto;
        max-height: 80vh;
    }

    #pdfCanvas {
        border: 1px solid #dee2e6;
        background: white;
        display: block;
        margin: 0 auto;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    #dragContainer {
        position: relative;
        margin: 0 auto;
        background: transparent;
        /* Ensure consistent positioning regardless of container size */
        transform-origin: top left;
    }

    .placed-item {
        border: 2px dashed transparent;
        transition: all 0.2s ease;
        user-select: none;
    }

    .placed-item:hover {
        border-color: #007bff;
        box-shadow: 0 2px 8px rgba(0,123,255,0.3);
    }

    .placed-item img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        background: white;
        border-radius: 2px;
        pointer-events: none;
    }

    /* List Tanda Tangan - tampilan lebih rapi */
    .list-group-item {
        transition: all 0.2s ease;
        border-left: 4px solid transparent;
    }
    .list-group-item:hover {
        background: #e3f2fd;
        border-left-color: #2196f3;
        transform: translateX(2px);
    }
    .list-group-item img {
        border: 1px solid #dee2e6;
        padding: 2px;
    }

    /* Signature Pad */
    #signaturePad {
        border: 2px solid #e9ecef;
        transition: border-color 0.2s ease;
    }
    #signaturePad:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    /* Overlay controls */
    .overlay-ctrl {
        position: absolute;
        top: -15px;
        right: -15px;
        display: flex;
        gap: 3px;
        z-index: 100;
    }
    .overlay-ctrl .btn {
        padding: 3px 6px;
        font-size: 10px;
        line-height: 1;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Alert custom */
    .alert-sm {
        font-size: 0.8rem;
        padding: 0.5rem 0.75rem;
    }

    /* Loading animation */
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }

    /* Custom SweetAlert styles */
    .swal-wide {
        width: 600px !important;
    }
    
    .swal-wide .swal2-html-container {
        text-align: left !important;
    }
    .loading { animation: pulse 1.5s infinite; }

    /* Sticky Panel Improvements */
    .controls-panel {
        background: #f8f9fa;
        border-left: 1px solid #dee2e6;
        box-shadow: -2px 0 10px rgba(0,0,0,0.1);
    }

    .action-buttons-container {
        background: rgba(248, 249, 250, 0.95) !important;
        backdrop-filter: blur(10px);
        border-top: 2px solid #dee2e6 !important;
        margin: 0 -1rem -1rem -1rem;
    }

    /* Signature Dropdown */
    .dropdown-menu {
        border: 1px solid #dee2e6;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border-radius: 0.5rem;
    }

    .dropdown-item {
        border: none !important;
        transition: all 0.2s ease;
    }

    .dropdown-item:hover {
        background: #e3f2fd !important;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    /* Quick Access Signatures */
    .signature-quick-item {
        transition: all 0.2s ease;
        cursor: grab;
    }

    .signature-quick-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border-color: #0d6efd !important;
    }

    .signature-quick-item:active {
        cursor: grabbing;
    }

    /* Compact buttons */
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }

    /* Scrollable dropdown */
    .dropdown-menu {
        max-height: 300px;
        overflow-y: auto;
    }

    .dropdown-menu::-webkit-scrollbar {
        width: 6px;
    }

    .dropdown-menu::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    .dropdown-menu::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }

    .dropdown-menu::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    /* Consistent positioning across different screen sizes */
    @media (max-width: 768px) {
        #pdfCanvas {
            max-width: 100%;
            height: auto;
        }
        
        #dragContainer {
            max-width: 100%;
        }
        
        .placed-item {
            min-width: 40px;
            min-height: 20px;
        }
    }

    /* High DPI display support */
    @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
        #pdfCanvas {
            image-rendering: -webkit-optimize-contrast;
            image-rendering: crisp-edges;
        }
    }

    /* Locked overlay */
    .placed-item.locked { cursor: default; }
</style>

<script>
$(function() {
    // Global variables using window object to avoid linter conflicts
    window.dragContainer = $("#drag-container");
    window.pdfDoc = null;
    window.currentPage = 1;
    window.totalPages = 0;
    window.scale = 1;
    window.documentDimensions = { width: 0, height: 0 }; // Consistent document dimensions storage
    window.overlaysLoaded = false; // Flag to prevent double loading
    window.creatingOverlay = false; // Flag to prevent duplicate overlay creation

    // Pre-load existing overlays
    var existingOverlays = {!! json_encode($overlays) !!};
    
    $(".draggable-item").draggable({
        helper: 'clone', appendTo: 'body', cursor: 'grabbing', revert: 'invalid',
        start: function(event, ui) { $(ui.helper).addClass('draggable-item-clone'); }
    });

    window.dragContainer.droppable({
        accept: ".draggable-item",
        drop: function(event, ui) {
            var type = $(ui.draggable).data('type');
            var url = $(ui.draggable).data('url');
            
            // Get precise drop coordinates with better offset calculation
            var containerOffset = window.dragContainer.offset();
            var dropX = ui.offset.left - containerOffset.left + (ui.helper.width() / 2);
            var dropY = ui.offset.top - containerOffset.top + (ui.helper.height() / 2);
            
            // PERBAIKAN: Konversi koordinat scaled ke document coordinates
            // Gunakan scale factor untuk konsistensi dengan PDF generation
            var documentX = dropX / window.scale;
            var documentY = dropY / window.scale;
            
            // Gunakan dimensi dokumen asli (unscaled) untuk percentage calculation
            var fullWidth = window.documentDimensions.width / window.scale;
            var fullHeight = window.documentDimensions.height / window.scale;
            
            var x_percent = parseFloat(((documentX / fullWidth) * 100).toFixed(4));
            var y_percent = parseFloat(((documentY / fullHeight) * 100).toFixed(4));
            
            console.log('PERBAIKAN Drop coordinates:', {
                scaled_coordinates: {dropX: dropX, dropY: dropY},
                document_coordinates: {documentX: documentX, documentY: documentY},
                canvas_dimensions: {width: window.documentDimensions.width, height: window.documentDimensions.height},
                document_dimensions: {width: fullWidth, height: fullHeight},
                scale_factor: window.scale,
                percentages: {x_percent: x_percent, y_percent: y_percent}
            });
            
            $('.placeholder-text').hide();
            createPlacedItem(type, url, x_percent, y_percent);
        }
    });

    function createPlacedItem(type, url, x_percent, y_percent, width_percent, height_percent) {
        var itemId = 'item-' + Date.now();
        var default_width = (type === 'signature') ? 25 : 15;
        var default_height = (type === 'signature') ? 12 : 15;
        
        // Ensure we have valid dimensions before calculation
        if (!window.documentDimensions.width || !window.documentDimensions.height) {
            console.warn('Document dimensions not available, waiting for PDF to load');
            return;
        }
        
        // Normalize percentages to ensure consistency across different document sizes
        var normalized_x = Math.max(0, Math.min(100, parseFloat(x_percent) || 0));
        var normalized_y = Math.max(0, Math.min(100, parseFloat(y_percent) || 0));
        var normalized_width = Math.max(5, Math.min(50, parseFloat(width_percent) || default_width));
        var normalized_height = Math.max(5, Math.min(50, parseFloat(height_percent) || default_height));
        
        // High-precision coordinate calculation with rounding for pixel-perfect positioning
        var absoluteLeft = Math.round((normalized_x / 100) * window.documentDimensions.width);
        var absoluteTop = Math.round((normalized_y / 100) * window.documentDimensions.height);
        var absoluteWidth = Math.round((normalized_width / 100) * window.documentDimensions.width);
        var absoluteHeight = Math.round((normalized_height / 100) * window.documentDimensions.height);
        
        // Ensure item stays within document bounds
        absoluteLeft = Math.max(0, Math.min(absoluteLeft, window.documentDimensions.width - absoluteWidth));
        absoluteTop = Math.max(0, Math.min(absoluteTop, window.documentDimensions.height - absoluteHeight));
        
        var newItem = $('<div id="' + itemId + '" class="placed-item" data-page="' + window.currentPage + '" style="position: absolute; left: ' + absoluteLeft + 'px; top: ' + absoluteTop + 'px; width: ' + absoluteWidth + 'px; height: ' + absoluteHeight + 'px; z-index:999;">' +
            '<img src="' + url + '" alt="' + type + '" style="width: 100%; height: 100%; object-fit: contain; background: transparent; pointer-events: none;">' +
            '<div class="overlay-ctrl">' +
                '<button type="button" class="btn btn-success btn-sm confirm-overlay"><i class="fas fa-check"></i></button>' +
                '<button type="button" class="btn btn-danger btn-sm delete-overlay ms-1"><i class="fas fa-times"></i></button>' +
            '</div>' +
            '<div class="page-indicator" style="position: absolute; top: -20px; left: 0; background: #007bff; color: white; padding: 2px 6px; border-radius: 3px; font-size: 10px;">Hal. ' + window.currentPage + '</div>' +
        '</div>');

        newItem.appendTo(window.dragContainer).draggable({
            containment: 'parent',
            drag: function(event, ui) {
                // Real-time coordinate synchronization during drag
                updateItemPercentages($(this));
            },
            stop: function(event, ui) {
                // Prevent event conflicts and update position data
                event.stopPropagation();
                updateItemPercentages($(this));
            }
        }).resizable({
            aspectRatio: (type === 'stamp'),
            handles: 'n, e, s, w, ne, se, sw, nw',
            resize: function(event, ui) {
                // Real-time size synchronization during resize
                updateItemPercentages($(this));
            },
            stop: function(event, ui) {
                // Prevent event conflicts and update size data
                event.stopPropagation();
                updateItemPercentages($(this));
            }
        });
        
        // Store high-precision percentage values as data attributes
        newItem.data('x-percent', parseFloat(normalized_x.toFixed(4)));
        newItem.data('y-percent', parseFloat(normalized_y.toFixed(4)));
        newItem.data('width-percent', parseFloat((normalized_width).toFixed(4)));
        newItem.data('height-percent', parseFloat((normalized_height).toFixed(4)));
    }
    
    function updateItemPercentages(item) {
        if (!window.documentDimensions.width || !window.documentDimensions.height) return;
        
        // PERBAIKAN: Konversi koordinat scaled ke document coordinates untuk konsistensi
        // Gunakan scale factor untuk konsistensi dengan PDF generation  
        var documentX = item.position().left / window.scale;
        var documentY = item.position().top / window.scale;
        var documentWidth = item.width() / window.scale;
        var documentHeight = item.height() / window.scale;
        
        // Gunakan dimensi dokumen asli (unscaled) untuk percentage calculation
        var fullWidth = window.documentDimensions.width / window.scale;
        var fullHeight = window.documentDimensions.height / window.scale;
        
        var x_percent = parseFloat(((documentX / fullWidth) * 100).toFixed(4));
        var y_percent = parseFloat(((documentY / fullHeight) * 100).toFixed(4));
        var width_percent = parseFloat(((documentWidth / fullWidth) * 100).toFixed(4));
        var height_percent = parseFloat(((documentHeight / fullHeight) * 100).toFixed(4));
        
        console.log('PERBAIKAN Update percentages:', {
            scaled_position: {left: item.position().left, top: item.position().top, width: item.width(), height: item.height()},
            document_position: {x: documentX, y: documentY, width: documentWidth, height: documentHeight},
            canvas_dimensions: {width: window.documentDimensions.width, height: window.documentDimensions.height},
            document_dimensions: {width: fullWidth, height: fullHeight},
            scale_factor: window.scale,
            percentages: {x: x_percent, y: y_percent, w: width_percent, h: height_percent}
        });
        
        // Store updated percentages
        item.data('x-percent', x_percent);
        item.data('y-percent', y_percent);
        item.data('width-percent', width_percent);
        item.data('height-percent', height_percent);
    }
    
    function repositionExistingOverlays() {
        // Only load existing overlays once to prevent duplicates
        if(!window.overlaysLoaded && Object.keys(existingOverlays).length > 0) {
            $.each(existingOverlays, function(index, overlay) {
                createPlacedItem(overlay.type, overlay.url, overlay.x_percent, overlay.y_percent, overlay.width_percent, overlay.height_percent);
            });
            $('.placeholder-text').hide();
            window.overlaysLoaded = true; // Mark as loaded
        }
    }
    
    function updateExistingOverlayPositions() {
        // Update positions of existing overlays without creating new ones
        $(".placed-item").each(function() {
            var item = $(this);
            var x_percent = item.data('x-percent') || 0;
            var y_percent = item.data('y-percent') || 0;
            var width_percent = item.data('width-percent') || 25;
            var height_percent = item.data('height-percent') || 12;
            
            // Recalculate absolute positions based on current canvas size with pixel-perfect rounding
            var absoluteLeft = Math.round((x_percent / 100) * window.documentDimensions.width);
            var absoluteTop = Math.round((y_percent / 100) * window.documentDimensions.height);
            var absoluteWidth = Math.round((width_percent / 100) * window.documentDimensions.width);
            var absoluteHeight = Math.round((height_percent / 100) * window.documentDimensions.height);
            
            // Apply constraints to keep overlays within document bounds
            absoluteLeft = Math.max(0, Math.min(absoluteLeft, window.documentDimensions.width - absoluteWidth));
            absoluteTop = Math.max(0, Math.min(absoluteTop, window.documentDimensions.height - absoluteHeight));
            
            item.css({
                'left': absoluteLeft + 'px',
                'top': absoluteTop + 'px',
                'width': absoluteWidth + 'px',
                'height': absoluteHeight + 'px'
            });
        });
    }
    
    function showOverlaysForPage(pageNum) {
        $(".placed-item").each(function() {
            var itemPage = $(this).data('page');
            if(itemPage === pageNum) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    $("#clearBtn").on('click', function() {
        Swal.fire({
            title: 'Bersihkan area tanda tangan?',
            text: 'Semua tanda tangan yang sudah ditempatkan akan dihapus.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, bersihkan',
            cancelButtonText: 'Batal'
        }).then(function(result) {
            if (result.isConfirmed) {
        window.dragContainer.empty();
        $('.placeholder-text').show();
                Swal.fire({
                    icon: 'success',
                    title: 'Area dibersihkan',
                    text: 'Semua tanda tangan telah dihapus.',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });
    });

    $("#resetViewBtn").on('click', function() {
        location.reload();
    });

    $("#previewBtn").on('click', function() {
        var overlaysData = [];
            $(".placed-item").each(function() {
            var item = $(this);
            
            // Use stored high-precision percentage data for consistent preview
            var x_percent = item.data('x-percent') || 0;
            var y_percent = item.data('y-percent') || 0;
            var width_percent = item.data('width-percent') || 25;
            var height_percent = item.data('height-percent') || 12;
            
                overlaysData.push({
                    type: item.find('img').attr('alt'),
                    url: item.find('img').attr('src'),
                    page: item.data('page') || window.currentPage,
                x_percent: parseFloat(x_percent.toFixed(4)),
                y_percent: parseFloat(y_percent.toFixed(4)),
                width_percent: parseFloat(width_percent.toFixed(4)),
                height_percent: parseFloat(height_percent.toFixed(4)),
                });
            });

        if (overlaysData.length === 0) {
            Swal.fire({
                icon: 'info',
                title: 'Belum ada tanda tangan',
                text: 'Silakan tambahkan tanda tangan terlebih dahulu untuk melihat preview.'
            });
            return;
        }

        // Deduplicate: keep overlay with bigger y_percent when type & page are identical
        var deduped = {};
        overlaysData.forEach(function(ov) {
            var key = (ov.type || 'signature') + '_' + (ov.page || 1);
            if (!deduped[key] || ov.y_percent > deduped[key].y_percent) {
                deduped[key] = ov;
            }
        });

        // Convert back to array preserving insertion order for preview
        var uniqueOverlays = Object.values(deduped);

        var previewHtml = '<div class="text-center"><strong>Preview Posisi Tanda Tangan:</strong></div><hr>';
        uniqueOverlays.forEach(function(overlay, index) {
            previewHtml += '<div class="mb-2"><strong>Tanda Tangan ' + (index + 1) + ':</strong><br>';
            previewHtml += 'Posisi: ' + overlay.x_percent.toFixed(2) + '%, ' + overlay.y_percent.toFixed(2) + '%<br>';
            previewHtml += 'Ukuran: ' + overlay.width_percent.toFixed(2) + '% x ' + overlay.height_percent.toFixed(2) + '%</div>';
        });

        Swal.fire({
            title: 'Preview Hasil',
            html: previewHtml,
            icon: 'info',
            confirmButtonText: 'OK'
        });
    });

    $("#discardBtn").on('click', function() {
        Swal.fire({
            title: 'Buang Perubahan?',
            text: 'Perubahan yang belum disimpan akan hilang dan Anda akan kembali ke halaman validasi.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, buang',
            cancelButtonText: 'Batal'
        }).then(function(result) {
            if (result.isConfirmed) {
                window.location.href = "{{ route('persetujuan.validation.wizard', $pengajuan->id) }}";
            }
        });
    });

    $("#saveBtn").on('click', function() {
        var overlaysData = [];
        $(".placed-item").each(function() {
            var item = $(this);
            // Pastikan data persentase terbaru dihitung
            updateItemPercentages(item);

            // Use stored percentage data for consistent coordinates with data validation
            var x_percent = item.data('x-percent');
            var y_percent = item.data('y-percent');
            var width_percent = item.data('width-percent');
            var height_percent = item.data('height-percent');

            // Fallback calculation if data attributes not available
            if (typeof x_percent === 'undefined' || typeof y_percent === 'undefined') {
                x_percent = (item.position().left / window.documentDimensions.width) * 100;
                y_percent = (item.position().top / window.documentDimensions.height) * 100;
                width_percent = (item.width() / window.documentDimensions.width) * 100;
                height_percent = (item.height() / window.documentDimensions.height) * 100;
            }

            // Data validation and normalization with parseFloat before saving
            overlaysData.push({
                type: item.find('img').attr('alt'),
                url: item.find('img').attr('src'),
                page: item.data('page') || window.currentPage,
                x_percent: parseFloat(parseFloat(x_percent).toFixed(4)),
                y_percent: parseFloat(parseFloat(y_percent).toFixed(4)),
                width_percent: parseFloat(parseFloat(width_percent).toFixed(4)),
                height_percent: parseFloat(parseFloat(height_percent).toFixed(4))
            });
        });

        if (overlaysData.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Tidak ada tanda tangan',
                text: 'Silakan tambahkan tanda tangan atau materai terlebih dahulu sebelum menyimpan.'
            });
            return;
        }

        Swal.fire({
            title: 'Konfirmasi Penyimpanan',
            text: 'Apakah Anda yakin ingin menyimpan dan menandatangani dokumen ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                sendOverlay(overlaysData);
            }
        });
    });

    function sendOverlay(overlaysData) {
            $.ajax({
                url: '{{ route("persetujuan.signature.apply", ["pengajuan" => $pengajuan->id, "documentType" => $documentType]) }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    overlays: JSON.stringify(overlaysData),
                },
            beforeSend: function() { 
                $('#saveBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan & Menandatangani...').addClass('loading');
            },
                success: function(response) {
                    var messageHtml = '<div class="text-start">' +
                        '<strong>Dokumen berhasil ditandatangani!</strong><br><br>' +
                        '<strong>File yang dihasilkan:</strong><br>' +
                        '<code class="text-primary">' + (response.filename || 'File signed berhasil dibuat') + '</code><br><br>';
                    
                    if (response.overlays_applied) {
                        messageHtml += '<strong>Tanda tangan diterapkan:</strong> ' + response.overlays_applied + ' overlay<br>';
                    }
                    
                    if (response.page_info && response.page_info.original_pages) {
                        messageHtml += '<br><strong>Informasi Halaman:</strong><br>' +
                            'Halaman asli: ' + response.page_info.original_pages + '<br>' +
                            'Halaman final: ' + response.page_info.final_pages + '<br>' +
                            'Status: ' + response.page_info.status + '<br>';
                    }
                    
                    messageHtml += '<br><small class="text-muted">Dokumen akan tersedia di halaman detail pengajuan</small></div>';

                    Swal.fire({ 
                        icon: 'success', 
                        title: 'Berhasil!', 
                        html: messageHtml,
                        showConfirmButton: true,
                        confirmButtonText: 'Lanjutkan ke Validasi',
                        timer: 5000
                    }).then(function() { 
                        window.location.href = '{{ route("persetujuan.validation.wizard", $pengajuan->id) }}'; 
                    });
                },
            error: function(xhr) { 
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error tidak diketahui.';
                Swal.fire('Error', msg, 'error'); 
            },
            complete: function() { 
                $('#saveBtn').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Simpan & Tandatangani Dokumen').removeClass('loading');
            }
        });
    }

    // PDF Rendering with Multi-page Support
    var pdfUrl = '{{ $documentUrl }}';
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    
    function calculateScale() {
        var containerWidth = window.dragContainer.width();
        // Use consistent scale calculation that accounts for actual PDF page dimensions
        if (window.pdfDoc) {
            return window.pdfDoc.getPage(1).then(function(page) {
                var viewport = page.getViewport({scale: 1});
                var fitScale = containerWidth / viewport.width;
                // Ensure minimum scale for readability but cap maximum for performance
                return Math.max(0.5, Math.min(2.0, fitScale));
            });
        }
        // Fallback calculation
        return Promise.resolve(Math.max(0.5, Math.min(2.0, containerWidth / 595)));
    }
    
    function renderPage(pageNum) {
        if (!window.pdfDoc) return;
        
        window.pdfDoc.getPage(pageNum).then(function(page) {
            var canvas = document.getElementById('pdfCanvas');
            var context = canvas.getContext('2d');
            
            var viewport = page.getViewport({ scale: window.scale });
            
            // Set canvas dimensions and store for coordinate calculations
            canvas.height = viewport.height;
            canvas.width = viewport.width;
            
            // Standardize document dimensions for consistent positioning across different PDFs
            window.documentDimensions.width = viewport.width;
            window.documentDimensions.height = viewport.height;
            
            // Store page ratio for proportional scaling
            window.pageRatio = viewport.width / viewport.height;
            
            // Set canvas CSS size to match actual size for proper coordinate mapping
            canvas.style.width = viewport.width + 'px';
            canvas.style.height = viewport.height + 'px';
            
            var renderContext = {
                canvasContext: context,
                viewport: viewport
            };
            
            // Clear canvas before rendering
            context.clearRect(0, 0, canvas.width, canvas.height);
            
            page.render(renderContext).promise.then(function() {
                // Make drag container exactly match canvas size for 1:1 coordinate mapping
                window.dragContainer.css({
                    'width': viewport.width + 'px',
                    'height': viewport.height + 'px'
                });
                $('#documentContainer').css({
                    'width': viewport.width + 'px',
                    'height': viewport.height + 'px'
                });
                
                // Update page info
                $('#currentPageNum').text(pageNum);
                $('#totalPagesNum').text(window.totalPages);
                
                // Update navigation buttons
                $('#prevPage').prop('disabled', pageNum <= 1);
                $('#nextPage').prop('disabled', pageNum >= window.totalPages);
                
                // Load overlays with timeout to ensure PDF rendering completes
                setTimeout(function() {
                    if (!window.overlaysLoaded) {
                    repositionExistingOverlays();
                } else {
                    updateExistingOverlayPositions();
                }
                
                // Show overlays for current page after rendering
                showOverlaysForPage(pageNum);
                }, 100);
            });
        });
    }
    
    // Initialize PDF
    pdfjsLib.getDocument(pdfUrl).promise.then(function(pdf) {
        window.pdfDoc = pdf;
        window.totalPages = pdf.numPages;
        
        // Calculate scale after PDF is loaded
        calculateScale().then(function(calculatedScale) {
            window.scale = calculatedScale;
        
        // Show navigation if more than 1 page
            if (window.totalPages > 1) {
            $('#pdfNavigation').removeClass('d-none');
        }
        
        // Render first page
            window.currentPage = 1;
            renderPage(window.currentPage);
        });
        
    }).catch(function(error) {
        console.error('Error loading PDF:', error);
                Swal.fire({
            icon: 'error',
            title: 'Error Loading PDF',
            text: 'Gagal memuat dokumen PDF. Silakan coba lagi atau hubungi administrator.'
        });
    });

    // Page navigation event handlers
    $(document).on('click', '#prevPage', function() {
        if (window.currentPage > 1) {
            // Hide overlays for current page
            hideOverlaysForPage(window.currentPage);
            window.currentPage--;
            renderPage(window.currentPage);
            // Show overlays for new page
            showOverlaysForPage(window.currentPage);
        }
    });
    
    $(document).on('click', '#nextPage', function() {
        if (window.currentPage < window.totalPages) {
            // Hide overlays for current page
            hideOverlaysForPage(window.currentPage);
            window.currentPage++;
            renderPage(window.currentPage);
            // Show overlays for new page
            showOverlaysForPage(window.currentPage);
        }
    });

    // Function to hide overlays for specific page
    function hideOverlaysForPage(pageNum) {
        $('.placed-item').each(function() {
            var item = $(this);
            var itemPage = item.data('page') || 1;
            if (itemPage === pageNum) {
                item.hide();
            }
        });
    }

    // Zoom controls
    $(document).on('click', '#zoomIn', function() {
        window.scale *= 1.2;
        renderPage(window.currentPage);
    });
    
    $(document).on('click', '#zoomOut', function() {
        window.scale /= 1.2;
        renderPage(window.currentPage);
    });
    
    $(document).on('click', '#fitWidth', function() {
        calculateScale().then(function(calculatedScale) {
            window.scale = calculatedScale;
            renderPage(window.currentPage);
        });
    });
    
    // Keyboard navigation
    $(document).on('keydown', function(e) {
        if (e.target.tagName.toLowerCase() === 'input' || e.target.tagName.toLowerCase() === 'textarea') {
            return; // Don't interfere with input fields
        }
        
        switch(e.which) {
            case 37: // Left arrow
            case 38: // Up arrow
                if (window.currentPage > 1) {
                    hideOverlaysForPage(window.currentPage);
                    window.currentPage--;
                    renderPage(window.currentPage);
                    showOverlaysForPage(window.currentPage);
                }
                e.preventDefault();
                break;
            case 39: // Right arrow
            case 40: // Down arrow
                if (window.currentPage < window.totalPages) {
                    hideOverlaysForPage(window.currentPage);
                    window.currentPage++;
                    renderPage(window.currentPage);
                    showOverlaysForPage(window.currentPage);
                }
                e.preventDefault();
                break;
        }
    });

    // Add paper size calibration for consistent positioning across different document sizes
    function calibrateDocumentDimensions() {
        // Standard paper size references for calibration
        const standardSizes = {
            'A4_PORTRAIT': { width: 595, height: 842 },
            'A4_LANDSCAPE': { width: 842, height: 595 },
            'LETTER': { width: 612, height: 792 },
            'LEGAL': { width: 612, height: 1008 }
        };
        
        // Detect document type based on dimensions
        let documentType = 'A4_PORTRAIT'; // default
        let tolerance = 20; // pixels tolerance for size detection
        
        for (let type in standardSizes) {
            let std = standardSizes[type];
            if (Math.abs(window.documentDimensions.width - std.width * window.scale) < tolerance &&
                Math.abs(window.documentDimensions.height - std.height * window.scale) < tolerance) {
                documentType = type;
                break;
            }
        }
        
        // Store calibration info for consistent positioning
        window.documentCalibration = {
            type: documentType,
            originalWidth: standardSizes[documentType].width,
            originalHeight: standardSizes[documentType].height,
            scaledWidth: window.documentDimensions.width,
            scaledHeight: window.documentDimensions.height,
            scaleX: window.documentDimensions.width / standardSizes[documentType].width,
            scaleY: window.documentDimensions.height / standardSizes[documentType].height
        };
        
        console.log('Document calibrated as:', documentType, 'Scale:', window.scale);
    }

    // PERBAIKAN: Click handler untuk tombol "+" - dengan pencegahan duplikasi
    $(document).on('click', '.use-signature', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // PERBAIKAN: Prevent duplicate overlay creation
        if (window.creatingOverlay) {
            console.log('Already creating overlay, ignoring duplicate click');
            return;
        }
        window.creatingOverlay = true;
        
        var url = $(this).data('url');
        if (!url) {
            window.creatingOverlay = false;
            return;
        }
        
        createPlacedItem('signature', url, 50, 10);
        $('.placeholder-text').hide();
        
        // Reset flag after short delay
        setTimeout(function() {
            window.creatingOverlay = false;
        }, 500);
    });

    // PERBAIKAN: Click handler untuk container - dengan pencegahan duplikasi
    $(document).on('click', '.draggable-item', function(e) {
        // Enhanced detection untuk internal buttons (+ dan trash)
        if ($(e.target).closest('.use-signature, .delete-signature').length) {
            console.log('Click on internal button, ignoring draggable-item click');
            return;
        }
        
        // Prevent drag-induced duplicates
        if ($(this).hasClass('ui-draggable-dragging')) {
            console.log('Item is being dragged, ignoring click');
            return;
        }
        
        // PERBAIKAN: Prevent duplicate overlay creation
        if (window.creatingOverlay) {
            console.log('Already creating overlay, ignoring duplicate click');
            return;
        }
        window.creatingOverlay = true;

        var url = $(this).data('url');
        var type = $(this).data('type') || 'signature';
        if (!url) {
            window.creatingOverlay = false;
            return;
        }
        
        createPlacedItem(type, url, 50, 10);
        $('.placeholder-text').hide();
        
        // Reset flag after short delay
        setTimeout(function() {
            window.creatingOverlay = false;
        }, 500);
    });

    // Overlay lock/unlock handlers
    $(document).on('click', '.confirm-overlay', function(e) {
        e.preventDefault();
        var item = $(this).closest('.placed-item');
        // Disable drag & resize
        if (item.hasClass('locked')) return; // already locked
        item.draggable('disable');
        item.resizable('disable');
        item.addClass('locked');
        // Visual cue: hide control bar except unlock button
        $(this).hide();
        $(this).siblings('.delete-overlay').removeClass('btn-danger').addClass('btn-warning').attr('title','Buka Kunci');
    });

    $(document).on('click', '.delete-overlay', function(e) {
        e.preventDefault();
        var item = $(this).closest('.placed-item');
        if (item.hasClass('locked')) {
            // Unlock: enable drag & resize again
            item.draggable('enable');
            item.resizable('enable');
            item.removeClass('locked');
            // Restore buttons
            $(this).addClass('btn-danger').removeClass('btn-warning').attr('title','Hapus Overlay');
            $(this).siblings('.confirm-overlay').show();
        } else {
            // Not locked => remove overlay
                    item.remove(); 
            if ($('.placed-item').length === 0) $('.placeholder-text').show();
        }
    });
});
</script>

/* ================================ */
/*  PENAMBAHAN: LOGIKA TANDA TANGAN */
/* ================================ */

<script>
// ----- Inisialisasi Signature Pad (Tab "Gambar") -----
const canvas = document.getElementById('signaturePad');
if (canvas) {
    const signaturePad = new SignaturePad(canvas, {
        backgroundColor: 'rgba(0,0,0,0)', // transparan
        penColor: '#000'
    });

    // Bersihkan kanvas
    window.clearSignature = function() {
        signaturePad.clear();
    }

    // Simpan hasil gambar
    document.getElementById('saveSignatureBtn').addEventListener('click', function () {
        if (signaturePad.isEmpty()) {
            Swal.fire('Info', 'Silakan gambar tanda tangan terlebih dahulu.', 'info');
            return;
        }

        const dataUrl = signaturePad.toDataURL('image/png');

        $.ajax({
            url: '{{ url("/signature/save") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                signature_data: dataUrl
            },
            beforeSend: function () {
                $('#saveSignatureBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...');
            },
            success: function (res) {
                Swal.fire('Berhasil', res.message || 'Tanda tangan tersimpan.', 'success').then(()=> location.reload());
            },
            error: function (err) {
                var msg = (err.responseJSON && err.responseJSON.message) ? err.responseJSON.message : 'Tidak dapat menyimpan tanda tangan.';
                Swal.fire('Gagal', msg, 'error');
            },
            complete: function () {
                $('#saveSignatureBtn').prop('disabled', false).html('<i class="fas fa-save me-1"></i>Simpan TTD');
            }
        });
    });
}

// ----- Logika Upload Tanda Tangan (Tab "Upload") -----
$('#signatureFile').on('change', function () {
    const file = this.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (e) {
        $('#signatureImage').attr('src', e.target.result);
        $('#signaturePreview').show();
    };
    reader.readAsDataURL(file);
});

window.removeSignatureFile = function () {
    $('#signatureFile').val('');
    $('#signaturePreview').hide();
};

$('#saveUploadBtn').on('click', function () {
    const fileInput = document.getElementById('signatureFile');
    if (!fileInput.files.length) {
        Swal.fire('Info', 'Silakan pilih file tanda tangan terlebih dahulu.', 'info');
        return;
    }

    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('signature_file', fileInput.files[0]);

    $.ajax({
        url: '{{ url("/signature/save") }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function () {
            $('#saveUploadBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...');
        },
        success: function (res) {
            Swal.fire('Berhasil', res.message || 'Tanda tangan tersimpan.', 'success').then(()=> location.reload());
        },
        error: function (err) {
            var msg = (err.responseJSON && err.responseJSON.message) ? err.responseJSON.message : 'Tidak dapat menyimpan tanda tangan.';
            Swal.fire('Gagal', msg, 'error');
        },
        complete: function () {
            $('#saveUploadBtn').prop('disabled', false).html('<i class="fas fa-save me-1"></i>Simpan TTD Upload');
        }
    });
});
</script>
@endpush 