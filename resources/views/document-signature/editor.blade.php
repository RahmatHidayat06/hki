@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1 fw-bold text-dark">Editor Tanda Tangan & Materai</h2>
                    <p class="text-muted mb-0">Posisikan tanda tangan dan materai pada dokumen</p>
                </div>
                <div>
                    <a href="{{ route('document-signature.index', $pengajuan->id) }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                    <button type="button" class="btn btn-success" id="applySignatureBtn">
                        <i class="fas fa-save me-1"></i>Terapkan & Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Document Preview -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-primary text-white border-0">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-file-pdf me-2"></i>
                        Preview Dokumen - {{ ucfirst(str_replace('_', ' ', $documentType)) }}
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div id="documentContainer" class="position-relative bg-secondary-subtle">
                        <!-- Gambar pratinjau dimuat di sini -->
                        <img id="documentBg" src="{{ $documentUrl }}" alt="Document Preview" class="img-fluid" style="pointer-events: none;">

                        <!-- Signature overlay -->
                        <div id="signatureOverlay" class="signature-overlay" style="display: none;">
                            <img id="signatureImage" src="" alt="Signature" class="draggable-element">
                            <div class="resize-handles">
                                <div class="resize-handle nw"></div>
                                <div class="resize-handle ne"></div>
                                <div class="resize-handle sw"></div>
                                <div class="resize-handle se"></div>
                            </div>
                        </div>
                        
                        <!-- Stamp overlay -->
                        <div id="stampOverlay" class="stamp-overlay" style="display: none;">
                            <img id="stampImage" src="" alt="Stamp" class="draggable-element">
                            <div class="resize-handles">
                                <div class="resize-handle nw"></div>
                                <div class="resize-handle ne"></div>
                                <div class="resize-handle sw"></div>
                                <div class="resize-handle se"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Controls Panel -->
        <div class="col-lg-4">
            <!-- Signature Selection -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-gradient-success text-white border-0">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-signature me-2"></i>
                        Pilih Tanda Tangan
                    </h5>
                </div>
                <div class="card-body p-3">
                    @if(count($signatures) > 0)
                        <div class="signature-selection">
                            @foreach($signatures as $signature)
                            <div class="signature-option border rounded-3 p-2 mb-2 cursor-pointer" 
                                 data-signature-id="{{ $signature['id'] }}" 
                                 data-signature-url="{{ $signature['url'] }}">
                                <div class="d-flex align-items-center">
                                    <div class="signature-preview me-2">
                                        <img src="{{ $signature['url'] }}" 
                                             alt="{{ $signature['name'] }}" 
                                             class="img-fluid rounded"
                                             style="max-width: 60px; max-height: 30px; object-fit: contain;">
                                    </div>
                                    <div class="flex-grow-1">
                                        <small class="fw-semibold">{{ $signature['name'] }}</small>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-signature text-muted fs-4 mb-2"></i>
                            <p class="text-muted mb-0 small">Tidak ada tanda tangan tersedia</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Stamp Selection -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-gradient-warning text-dark border-0">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-certificate me-2"></i>
                        Pilih Materai (Opsional)
                    </h5>
                </div>
                <div class="card-body p-3">
                    @if(count($stamps) > 0)
                        <div class="stamp-selection">
                            @foreach($stamps as $stamp)
                            <div class="stamp-option border rounded-3 p-2 mb-2 cursor-pointer" 
                                 data-stamp-id="{{ $stamp['id'] }}" 
                                 data-stamp-url="{{ $stamp['url'] }}">
                                <div class="d-flex align-items-center">
                                    <div class="stamp-preview me-2">
                                        <img src="{{ $stamp['url'] }}" 
                                             alt="{{ $stamp['name'] }}" 
                                             class="img-fluid rounded"
                                             style="max-width: 40px; max-height: 40px; object-fit: contain;">
                                    </div>
                                    <div class="flex-grow-1">
                                        <small class="fw-semibold">{{ $stamp['name'] }}</small>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-certificate text-muted fs-4 mb-2"></i>
                            <p class="text-muted mb-0 small">Tidak ada materai tersedia</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Position Controls -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-gradient-info text-white border-0">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-crosshairs me-2"></i>
                        Kontrol Posisi
                    </h5>
                </div>
                <div class="card-body p-3">
                    <!-- Signature Position -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Posisi Tanda Tangan</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small">X (%)</label>
                                <input type="number" class="form-control form-control-sm" id="signatureX" min="0" max="100" value="50">
                            </div>
                            <div class="col-6">
                                <label class="form-label small">Y (%)</label>
                                <input type="number" class="form-control form-control-sm" id="signatureY" min="0" max="100" value="80">
                            </div>
                        </div>
                        <div class="row g-2 mt-1">
                            <div class="col-6">
                                <label class="form-label small">Lebar (%)</label>
                                <input type="number" class="form-control form-control-sm" id="signatureWidth" min="10" max="50" value="20">
                            </div>
                            <div class="col-6">
                                <label class="form-label small">Tinggi (%)</label>
                                <input type="number" class="form-control form-control-sm" id="signatureHeight" min="10" max="50" value="15">
                            </div>
                        </div>
                    </div>

                    <!-- Stamp Position -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Posisi Materai</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small">X (%)</label>
                                <input type="number" class="form-control form-control-sm" id="stampX" min="0" max="100" value="20">
                            </div>
                            <div class="col-6">
                                <label class="form-label small">Y (%)</label>
                                <input type="number" class="form-control form-control-sm" id="stampY" min="0" max="100" value="80">
                            </div>
                        </div>
                        <div class="row g-2 mt-1">
                            <div class="col-6">
                                <label class="form-label small">Lebar (%)</label>
                                <input type="number" class="form-control form-control-sm" id="stampWidth" min="10" max="30" value="15">
                            </div>
                            <div class="col-6">
                                <label class="form-label small">Tinggi (%)</label>
                                <input type="number" class="form-control form-control-sm" id="stampHeight" min="10" max="30" value="15">
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary btn-sm" id="resetPositionBtn">
                            <i class="fas fa-undo me-1"></i>Reset Posisi
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm" id="clearOverlaysBtn">
                            <i class="fas fa-trash me-1"></i>Hapus Semua
                        </button>
                    </div>
                </div>
            </div>

            <!-- Instructions -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-secondary text-white border-0">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-info-circle me-2"></i>
                        Petunjuk
                    </h6>
                </div>
                <div class="card-body p-3">
                    <ul class="list-unstyled mb-0 small">
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Pilih tanda tangan dari panel kiri</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Pilih materai (opsional)</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Seret untuk memindahkan posisi</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Gunakan kontrol untuk posisi presisi</li>
                        <li class="mb-0"><i class="fas fa-check text-success me-2"></i>Klik "Terapkan & Simpan" untuk menyimpan</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden form for submission -->
<form id="signatureForm" style="display: none;">
    @csrf
    <input type="hidden" name="signature_id" id="selectedSignatureId">
    <input type="hidden" name="stamp_id" id="selectedStampId">
    <input type="hidden" name="signature_x" id="finalSignatureX">
    <input type="hidden" name="signature_y" id="finalSignatureY">
    <input type="hidden" name="signature_width" id="finalSignatureWidth">
    <input type="hidden" name="signature_height" id="finalSignatureHeight">
    <input type="hidden" name="stamp_x" id="finalStampX">
    <input type="hidden" name="stamp_y" id="finalStampY">
    <input type="hidden" name="stamp_width" id="finalStampWidth">
    <input type="hidden" name="stamp_height" id="finalStampHeight">
</form>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
    color: #333 !important;
}

.bg-gradient-info {
    background: linear-gradient(135deg, #667eea 0%, #f093fb 100%);
}

.bg-gradient-secondary {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

#documentContainer {
    min-height: 600px;
    background: #f8f9fa;
    overflow: hidden;
}

.signature-overlay, .stamp-overlay {
    position: absolute;
    border: 2px dashed #007bff;
    cursor: move;
    z-index: 10;
}

.signature-overlay:hover, .stamp-overlay:hover {
    border-color: #0056b3;
}

.draggable-element {
    width: 100%;
    height: 100%;
    object-fit: contain;
    pointer-events: none;
}

.resize-handles {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    pointer-events: none;
}

.resize-handle {
    position: absolute;
    width: 8px;
    height: 8px;
    background: #007bff;
    border: 1px solid #fff;
    pointer-events: all;
    cursor: nw-resize;
}

.resize-handle.nw { top: -4px; left: -4px; cursor: nw-resize; }
.resize-handle.ne { top: -4px; right: -4px; cursor: ne-resize; }
.resize-handle.sw { bottom: -4px; left: -4px; cursor: sw-resize; }
.resize-handle.se { bottom: -4px; right: -4px; cursor: se-resize; }

.signature-option, .stamp-option {
    transition: all 0.3s ease;
    cursor: pointer;
}

.signature-option:hover, .stamp-option:hover {
    background: #e9ecef;
    transform: translateY(-1px);
}

.signature-option.selected, .stamp-option.selected {
    background: #d4edda;
    border-color: #28a745 !important;
}

.cursor-pointer {
    cursor: pointer;
}

#pdfViewer canvas {
    max-width: 100%;
    height: auto;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

@media (max-width: 768px) {
    .container-fluid {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    #documentContainer {
        min-height: 400px;
    }
}
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const documentBg = document.getElementById('documentBg');
    let initialOverlays = JSON.parse('{!! addslashes(json_encode($overlays ?? [])) !!}');

    function initializeApp() {
        // Logika untuk inisialisasi overlay dan fungsionalitas drag/resize
    }

    if (documentBg.complete) {
        initializeApp();
    } else {
        documentBg.addEventListener('load', initializeApp);
    }

    let selectedSignatureId = null;
    let selectedStampId = null;
    let isDragging = false;
    let isResizing = false;
    let currentElement = null;
    let startX, startY, startWidth, startHeight, startLeft, startTop;

    // PDF.js setup
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    // Load PDF
    loadPDF();

    // Signature selection
    document.querySelectorAll('.signature-option').forEach(option => {
        option.addEventListener('click', function() {
            // Remove previous selection
            document.querySelectorAll('.signature-option').forEach(opt => opt.classList.remove('selected'));
            
            // Add selection
            this.classList.add('selected');
            selectedSignatureId = this.dataset.signatureId;
            
            // Show signature overlay
            showSignatureOverlay(this.dataset.signatureUrl);
        });
    });

    // Stamp selection
    document.querySelectorAll('.stamp-option').forEach(option => {
        option.addEventListener('click', function() {
            // Remove previous selection
            document.querySelectorAll('.stamp-option').forEach(opt => opt.classList.remove('selected'));
            
            // Add selection
            this.classList.add('selected');
            selectedStampId = this.dataset.stampId;
            
            // Show stamp overlay
            showStampOverlay(this.dataset.stampUrl);
        });
    });

    // Position controls
    ['signatureX', 'signatureY', 'signatureWidth', 'signatureHeight'].forEach(id => {
        document.getElementById(id).addEventListener('input', updateSignaturePosition);
    });

    ['stampX', 'stampY', 'stampWidth', 'stampHeight'].forEach(id => {
        document.getElementById(id).addEventListener('input', updateStampPosition);
    });

    // Control buttons
    document.getElementById('resetPositionBtn').addEventListener('click', resetPositions);
    document.getElementById('clearOverlaysBtn').addEventListener('click', clearOverlays);
    document.getElementById('applySignatureBtn').addEventListener('click', applySignature);

    function loadPDF() {
        const documentUrl = '{{ Storage::url($documentPath) }}';
        
        pdfjsLib.getDocument(documentUrl).promise.then(function(pdf) {
            return pdf.getPage(1);
        }).then(function(page) {
            const scale = 1.5;
            const viewport = page.getViewport({ scale: scale });

            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            canvas.height = viewport.height;
            canvas.width = viewport.width;

            const renderContext = {
                canvasContext: context,
                viewport: viewport
            };

            page.render(renderContext).promise.then(function() {
                document.getElementById('pdfViewer').innerHTML = '';
                document.getElementById('pdfViewer').appendChild(canvas);
            });
        }).catch(function(error) {
            console.error('Error loading PDF:', error);
            document.getElementById('pdfViewer').innerHTML = '<div class="alert alert-danger">Error loading PDF</div>';
        });
    }

    function showSignatureOverlay(imageUrl) {
        const overlay = document.getElementById('signatureOverlay');
        const image = document.getElementById('signatureImage');
        
        image.src = imageUrl;
        overlay.style.display = 'block';
        
        updateSignaturePosition();
        setupDragAndResize(overlay);
    }

    function showStampOverlay(imageUrl) {
        const overlay = document.getElementById('stampOverlay');
        const image = document.getElementById('stampImage');
        
        image.src = imageUrl;
        overlay.style.display = 'block';
        
        updateStampPosition();
        setupDragAndResize(overlay);
    }

    function updateSignaturePosition() {
        const overlay = document.getElementById('signatureOverlay');
        const container = document.getElementById('documentContainer');
        
        if (overlay.style.display === 'none') return;
        
        const x = parseFloat(document.getElementById('signatureX').value);
        const y = parseFloat(document.getElementById('signatureY').value);
        const width = parseFloat(document.getElementById('signatureWidth').value);
        const height = parseFloat(document.getElementById('signatureHeight').value);
        
        overlay.style.left = x + '%';
        overlay.style.top = y + '%';
        overlay.style.width = width + '%';
        overlay.style.height = height + '%';
    }

    function updateStampPosition() {
        const overlay = document.getElementById('stampOverlay');
        const container = document.getElementById('documentContainer');
        
        if (overlay.style.display === 'none') return;
        
        const x = parseFloat(document.getElementById('stampX').value);
        const y = parseFloat(document.getElementById('stampY').value);
        const width = parseFloat(document.getElementById('stampWidth').value);
        const height = parseFloat(document.getElementById('stampHeight').value);
        
        overlay.style.left = x + '%';
        overlay.style.top = y + '%';
        overlay.style.width = width + '%';
        overlay.style.height = height + '%';
    }

    function setupDragAndResize(element) {
        element.addEventListener('mousedown', function(e) {
            if (e.target.classList.contains('resize-handle')) {
                isResizing = true;
                currentElement = element;
                startX = e.clientX;
                startY = e.clientY;
                startWidth = parseInt(window.getComputedStyle(element).width);
                startHeight = parseInt(window.getComputedStyle(element).height);
            } else {
                isDragging = true;
                currentElement = element;
                startX = e.clientX - element.offsetLeft;
                startY = e.clientY - element.offsetTop;
            }
            
            e.preventDefault();
        });
    }

    document.addEventListener('mousemove', function(e) {
        if (isDragging && currentElement) {
            const container = document.getElementById('documentContainer');
            const containerRect = container.getBoundingClientRect();
            
            let newLeft = ((e.clientX - startX - containerRect.left) / containerRect.width) * 100;
            let newTop = ((e.clientY - startY - containerRect.top) / containerRect.height) * 100;
            
            newLeft = Math.max(0, Math.min(100, newLeft));
            newTop = Math.max(0, Math.min(100, newTop));
            
            currentElement.style.left = newLeft + '%';
            currentElement.style.top = newTop + '%';
            
            // Update input values
            if (currentElement.id === 'signatureOverlay') {
                document.getElementById('signatureX').value = newLeft.toFixed(1);
                document.getElementById('signatureY').value = newTop.toFixed(1);
            } else if (currentElement.id === 'stampOverlay') {
                document.getElementById('stampX').value = newLeft.toFixed(1);
                document.getElementById('stampY').value = newTop.toFixed(1);
            }
        }
    });

    document.addEventListener('mouseup', function() {
        isDragging = false;
        isResizing = false;
        currentElement = null;
    });

    function resetPositions() {
        document.getElementById('signatureX').value = 50;
        document.getElementById('signatureY').value = 80;
        document.getElementById('signatureWidth').value = 20;
        document.getElementById('signatureHeight').value = 15;
        
        document.getElementById('stampX').value = 20;
        document.getElementById('stampY').value = 80;
        document.getElementById('stampWidth').value = 15;
        document.getElementById('stampHeight').value = 15;
        
        updateSignaturePosition();
        updateStampPosition();
    }

    function clearOverlays() {
        document.getElementById('signatureOverlay').style.display = 'none';
        document.getElementById('stampOverlay').style.display = 'none';
        
        document.querySelectorAll('.signature-option').forEach(opt => opt.classList.remove('selected'));
        document.querySelectorAll('.stamp-option').forEach(opt => opt.classList.remove('selected'));
        
        selectedSignatureId = null;
        selectedStampId = null;
    }

    function applySignature() {
        if (!selectedSignatureId) {
            alert('Mohon pilih tanda tangan terlebih dahulu!');
            return;
        }

        // Prepare form data
        document.getElementById('selectedSignatureId').value = selectedSignatureId;
        document.getElementById('selectedStampId').value = selectedStampId || '';
        
        document.getElementById('finalSignatureX').value = document.getElementById('signatureX').value;
        document.getElementById('finalSignatureY').value = document.getElementById('signatureY').value;
        document.getElementById('finalSignatureWidth').value = document.getElementById('signatureWidth').value;
        document.getElementById('finalSignatureHeight').value = document.getElementById('signatureHeight').value;
        
        if (selectedStampId) {
            document.getElementById('finalStampX').value = document.getElementById('stampX').value;
            document.getElementById('finalStampY').value = document.getElementById('stampY').value;
            document.getElementById('finalStampWidth').value = document.getElementById('stampWidth').value;
            document.getElementById('finalStampHeight').value = document.getElementById('stampHeight').value;
        }

        // Show loading
        const btn = document.getElementById('applySignatureBtn');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Memproses...';
        btn.disabled = true;

        // Submit via AJAX
        const formData = new FormData(document.getElementById('signatureForm'));
        
        fetch('{{ route("document-signature.apply", [$pengajuan->id, $documentType]) }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Dokumen berhasil ditandatangani!');
                window.location.href = '{{ route("document-signature.index", $pengajuan->id) }}';
            } else {
                alert('Error: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memproses dokumen.');
        })
        .finally(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    }
});
</script>
@endsection 