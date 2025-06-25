@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="mb-1 fw-bold text-white">Editor Tanda Tangan & Materai - Validasi</h2>
                        <a href="{{ route('validasi.show', $pengajuan->id) }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Kembali ke Validasi
                        </a>
                    </div>
                    <p class="mb-0 opacity-75">
                        <i class="fas fa-file-alt me-1"></i>
                        {{ ucfirst(str_replace('_', ' ', $documentType)) }} - {{ $pengajuan->judul_karya }}
                    </p>
                </div>
                
                <div class="card-body p-0">
                    <div class="row g-0">
                        <!-- Sidebar Panel -->
                        <div class="col-lg-3 border-end bg-light">
                            <div class="p-4">
                                <!-- Signature Selection -->
                                <div class="mb-4">
                                    <h5 class="fw-bold text-dark mb-3">
                                        <i class="fas fa-signature text-primary me-2"></i>Tanda Tangan
                                    </h5>
                                    <div class="signature-list">
                                        @forelse($signatures as $signature)
                                            <div class="signature-item mb-3 p-3 border rounded cursor-pointer" 
                                                 data-type="signature" 
                                                 data-id="{{ $signature['id'] }}"
                                                 data-url="{{ $signature['url'] }}"
                                                 data-name="{{ $signature['name'] }}">
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $signature['url'] }}" 
                                                         alt="{{ $signature['name'] }}" 
                                                         class="signature-preview me-2"
                                                         style="max-width: 60px; max-height: 30px; object-fit: contain;">
                                                    <div>
                                                        <div class="fw-medium">{{ $signature['name'] }}</div>
                                                        <small class="text-muted">{{ ucfirst($signature['type']) }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-muted text-center py-3">
                                                <i class="fas fa-exclamation-circle mb-2"></i><br>
                                                Tidak ada tanda tangan tersedia
                                            </div>
                                        @endforelse
                                    </div>
                                </div>

                                <!-- Stamp Selection -->
                                <div class="mb-4">
                                    <h5 class="fw-bold text-dark mb-3">
                                        <i class="fas fa-stamp text-success me-2"></i>Materai
                                    </h5>
                                    <div class="stamp-list">
                                        @forelse($stamps as $stamp)
                                            <div class="stamp-item mb-3 p-3 border rounded cursor-pointer" 
                                                 data-type="stamp" 
                                                 data-id="{{ $stamp['id'] }}"
                                                 data-url="{{ $stamp['url'] }}"
                                                 data-name="{{ $stamp['name'] }}">
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $stamp['url'] }}" 
                                                         alt="{{ $stamp['name'] }}" 
                                                         class="stamp-preview me-2"
                                                         style="max-width: 40px; max-height: 40px; object-fit: contain;">
                                                    <div>
                                                        <div class="fw-medium">{{ $stamp['name'] }}</div>
                                                        <small class="text-muted">{{ ucfirst($stamp['type']) }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-muted text-center py-3">
                                                <i class="fas fa-exclamation-circle mb-2"></i><br>
                                                Tidak ada materai tersedia
                                            </div>
                                        @endforelse
                                    </div>
                                </div>

                                <!-- Controls -->
                                <div class="mb-4">
                                    <h5 class="fw-bold text-dark mb-3">
                                        <i class="fas fa-cogs text-warning me-2"></i>Kontrol
                                    </h5>
                                    
                                    <div class="position-controls mb-3" style="display: none;">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <label class="form-label small">X (%)</label>
                                                <input type="number" class="form-control form-control-sm" id="posX" min="0" max="100" step="0.01">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small">Y (%)</label>
                                                <input type="number" class="form-control form-control-sm" id="posY" min="0" max="100" step="0.01">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small">Lebar (%)</label>
                                                <input type="number" class="form-control form-control-sm" id="width" min="1" max="50" step="0.01">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small">Tinggi (%)</label>
                                                <input type="number" class="form-control form-control-sm" id="height" min="1" max="50" step="0.01">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-success" id="saveOverlays">
                                            <i class="fas fa-save me-1"></i>Simpan Posisi
                                        </button>
                                        <button type="button" class="btn btn-warning" id="resetOverlays">
                                            <i class="fas fa-undo me-1"></i>Reset
                                        </button>
                                        <button type="button" class="btn btn-danger" id="clearOverlays">
                                            <i class="fas fa-trash me-1"></i>Hapus Semua
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PDF Viewer -->
                        <div class="col-lg-9">
                            <div class="pdf-container position-relative" style="height: 80vh; overflow: auto;">
                                <div id="documentContainer" class="document-container position-relative">
                                    <!-- Latar belakang dokumen dimuat di sini -->
                                    <img id="documentBg" src="{{ $documentUrl }}" alt="Document Preview" class="img-fluid" style="pointer-events: none;">

                                    <!-- Signature Overlay -->
                                    <div id="signatureOverlay" class="overlay-element signature-overlay" style="display: none;">
                                        <img id="signatureImage" src="" alt="Signature" class="draggable-element">
                                    </div>

                                    <!-- Stamp Overlay -->
                                    <div id="stampOverlay" class="overlay-element stamp-overlay" style="display: none;">
                                        <img id="stampImage" src="" alt="Stamp" class="draggable-element">
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

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <h5>Menyimpan posisi overlay...</h5>
                <p class="text-muted mb-0">Mohon tunggu sebentar</p>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.cursor-pointer { cursor: pointer; }
.signature-item:hover, .stamp-item:hover {
    background-color: #e3f2fd;
    border-color: #2196f3 !important;
}
.signature-item.selected, .stamp-item.selected {
    background-color: #e8f5e8;
    border-color: #4caf50 !important;
}
.overlay-item {
    position: absolute;
    border: 2px dashed #2196f3;
    cursor: move;
    pointer-events: all;
}
.overlay-item:hover {
    border-color: #ff9800;
}
.overlay-item.selected {
    border-color: #4caf50;
}
.resize-handle {
    position: absolute;
    width: 10px;
    height: 10px;
    background: #2196f3;
    border: 1px solid #fff;
    cursor: nw-resize;
}
.resize-handle.se { bottom: -5px; right: -5px; }

#pdfCanvas {
    border: 1px solid #dee2e6;
    background: white;
    display: block;
    margin: 0 auto;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

#overlayContainer {
    position: absolute;
    top: 0;
    left: 0;
    background: transparent;
    pointer-events: none;
    transform-origin: top left;
}

.overlay-item {
    border: 2px dashed transparent;
    transition: all 0.2s ease;
    pointer-events: auto;
    cursor: move;
}

.overlay-item:hover,
.overlay-item.selected {
    border-color: #007bff;
    box-shadow: 0 2px 8px rgba(0,123,255,0.3);
}

.overlay-item img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    background: white;
    border-radius: 2px;
    pointer-events: none;
}

/* Consistent positioning across different screen sizes */
@media (max-width: 768px) {
    #pdfCanvas {
        max-width: 100%;
        height: auto;
    }
    
    .overlay-item {
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
</style>
@endpush

@push('scripts')
<script>
    // Initialize data from server
    window.overlaysData = @json($overlays ?? []);
    window.signaturesData = @json($signatures);
    window.stampsData = @json($stamps);
    window.documentUrl = '{{ $documentUrl }}';
    window.saveUrl = '{{ route("validasi.signature.apply", [$pengajuan->id, $documentType]) }}';
    window.csrfToken = '{{ csrf_token() }}';
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let pdfDoc = null;
    let pageNum = 1;
    let pageRendering = false;
    let pageNumPending = null;
    let scale = 1.0; // Will be calculated dynamically
    let canvas = document.getElementById('pdfCanvas');
    let ctx = canvas.getContext('2d');
    let overlayContainer = document.getElementById('overlayContainer');
    let overlays = window.overlaysData || [];
    let selectedOverlay = null;
    let isDragging = false;
    let isResizing = false;
    let dragOffset = { x: 0, y: 0 };
    let documentDimensions = { width: 0, height: 0 }; // Store actual document dimensions

    // PDF.js setup
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    // Calculate optimal scale based on container width and actual PDF dimensions
    function calculateScale() {
        if (!pdfDoc) return Promise.resolve(1.2);
        
        return pdfDoc.getPage(1).then(function(page) {
            var viewport = page.getViewport({scale: 1});
            var container = document.getElementById('pdfContainer') || canvas.parentElement;
            var containerWidth = container.clientWidth - 40; // Account for padding
            var fitScale = containerWidth / viewport.width;
            // Ensure minimum scale for readability but cap maximum for performance
            return Math.max(0.5, Math.min(2.0, fitScale));
        });
    }

    // Load PDF
    const loadingTask = pdfjsLib.getDocument(window.documentUrl);
    loadingTask.promise.then(function(pdf) {
        pdfDoc = pdf;
        
        // Calculate optimal scale then render
        calculateScale().then(function(calculatedScale) {
            scale = calculatedScale;
        renderPage(pageNum);
            // Wait for rendering to complete before loading overlays
            setTimeout(loadExistingOverlays, 500);
        });
    });

    function renderPage(num) {
        pageRendering = true;
        pdfDoc.getPage(num).then(function(page) {
            const viewport = page.getViewport({ scale: scale });
            
            // Store document dimensions for accurate positioning
            documentDimensions.width = viewport.width;
            documentDimensions.height = viewport.height;
            
            canvas.height = viewport.height;
            canvas.width = viewport.width;
            
            // Update overlay container size to match canvas exactly
            overlayContainer.style.width = canvas.width + 'px';
            overlayContainer.style.height = canvas.height + 'px';
            overlayContainer.style.position = 'absolute';
            overlayContainer.style.top = '0';
            overlayContainer.style.left = '0';

            const renderContext = {
                canvasContext: ctx,
                viewport: viewport
            };
            
            const renderTask = page.render(renderContext);
            renderTask.promise.then(function() {
                pageRendering = false;
                if (pageNumPending !== null) {
                    renderPage(pageNumPending);
                    pageNumPending = null;
                }
                updateOverlayPositions();
            });
        });
    }

    function loadExistingOverlays() {
        overlays.forEach(function(overlay, index) {
            createOverlayElement(overlay, index);
        });
    }

    function createOverlayElement(overlay, index) {
        const overlayEl = document.createElement('div');
        overlayEl.className = 'overlay-item';
        overlayEl.dataset.index = index;
        overlayEl.dataset.type = overlay.type;
        
        const img = document.createElement('img');
        if (overlay.type === 'signature') {
            const signatures = window.signaturesData || [];
            const signature = signatures.find(s => s.id === overlay.signature_id);
            if (signature) {
                img.src = signature.url;
                img.alt = signature.name;
            }
        } else if (overlay.type === 'stamp') {
            const stamps = window.stampsData || [];
            const stamp = stamps.find(s => s.id === overlay.stamp_id);
            if (stamp) {
                img.src = stamp.url;
                img.alt = stamp.name;
            }
        }
        
        img.style.width = '100%';
        img.style.height = '100%';
        img.style.objectFit = 'contain';
        img.draggable = false;
        
        overlayEl.appendChild(img);
        
        // Add resize handle
        const resizeHandle = document.createElement('div');
        resizeHandle.className = 'resize-handle se';
        overlayEl.appendChild(resizeHandle);
        
        overlayContainer.appendChild(overlayEl);
        
        // Position overlay using precise calculation
        updateOverlayPosition(overlayEl, overlay);
        
        // Add event listeners
        addOverlayEventListeners(overlayEl);
    }

    function updateOverlayPosition(overlayEl, overlay) {
        // Normalize overlay data to ensure consistency
        const normalizedX = Math.max(0, Math.min(100, parseFloat(overlay.x) || 0));
        const normalizedY = Math.max(0, Math.min(100, parseFloat(overlay.y) || 0));
        const normalizedWidth = Math.max(5, Math.min(50, parseFloat(overlay.width) || 25));
        const normalizedHeight = Math.max(5, Math.min(50, parseFloat(overlay.height) || 12));
        
        // Use direct pixel positioning based on document dimensions
        const x = (normalizedX / 100) * documentDimensions.width;
        const y = (normalizedY / 100) * documentDimensions.height;
        const width = (normalizedWidth / 100) * documentDimensions.width;
        const height = (normalizedHeight / 100) * documentDimensions.height;
        
        overlayEl.style.position = 'absolute';
        overlayEl.style.left = Math.round(x) + 'px';
        overlayEl.style.top = Math.round(y) + 'px';
        overlayEl.style.width = Math.round(width) + 'px';
        overlayEl.style.height = Math.round(height) + 'px';
    }

    function updateOverlayPositions() {
        document.querySelectorAll('.overlay-item').forEach(function(overlayEl) {
            const index = parseInt(overlayEl.dataset.index);
            if (overlays[index]) {
                updateOverlayPosition(overlayEl, overlays[index]);
            }
        });
    }

    function addOverlayEventListeners(overlayEl) {
        overlayEl.addEventListener('mousedown', function(e) {
            if (e.target.classList.contains('resize-handle')) {
                isResizing = true;
                selectedOverlay = overlayEl;
            } else {
                isDragging = true;
                selectedOverlay = overlayEl;
                const rect = overlayEl.getBoundingClientRect();
                const containerRect = overlayContainer.getBoundingClientRect();
                dragOffset.x = e.clientX - containerRect.left - overlayEl.offsetLeft;
                dragOffset.y = e.clientY - containerRect.top - overlayEl.offsetTop;
            }
            
            // Update selection
            document.querySelectorAll('.overlay-item').forEach(el => el.classList.remove('selected'));
            overlayEl.classList.add('selected');
            
            // Update position controls
            updatePositionControls(overlayEl);
            
            e.preventDefault();
            e.stopPropagation();
        });
    }

    function updatePositionControls(overlayEl) {
        const index = parseInt(overlayEl.dataset.index);
        const overlay = overlays[index];
        
        if (overlay) {
            document.getElementById('posX').value = overlay.x.toFixed(2);
            document.getElementById('posY').value = overlay.y.toFixed(2);
            document.getElementById('width').value = overlay.width.toFixed(2);
            document.getElementById('height').value = overlay.height.toFixed(2);
            
            document.querySelector('.position-controls').style.display = 'block';
        }
    }

    // Mouse events for dragging and resizing
    document.addEventListener('mousemove', function(e) {
        if (isDragging && selectedOverlay) {
            const containerRect = overlayContainer.getBoundingClientRect();
            
            // Calculate new position with precise offset
            const newLeft = e.clientX - containerRect.left - dragOffset.x;
            const newTop = e.clientY - containerRect.top - dragOffset.y;
            
            // Constrain within bounds
            const constrainedLeft = Math.max(0, Math.min(newLeft, documentDimensions.width - selectedOverlay.offsetWidth));
            const constrainedTop = Math.max(0, Math.min(newTop, documentDimensions.height - selectedOverlay.offsetHeight));
            
            selectedOverlay.style.left = Math.round(constrainedLeft) + 'px';
            selectedOverlay.style.top = Math.round(constrainedTop) + 'px';
            
            updateOverlayData(selectedOverlay);
        } else if (isResizing && selectedOverlay) {
            const containerRect = overlayContainer.getBoundingClientRect();
            const newWidth = e.clientX - containerRect.left - selectedOverlay.offsetLeft;
            const newHeight = e.clientY - containerRect.top - selectedOverlay.offsetTop;
            
            // Constrain size
            const constrainedWidth = Math.max(20, Math.min(newWidth, documentDimensions.width - selectedOverlay.offsetLeft));
            const constrainedHeight = Math.max(20, Math.min(newHeight, documentDimensions.height - selectedOverlay.offsetTop));
            
            selectedOverlay.style.width = Math.round(constrainedWidth) + 'px';
            selectedOverlay.style.height = Math.round(constrainedHeight) + 'px';
            
            updateOverlayData(selectedOverlay);
        }
    });

    document.addEventListener('mouseup', function() {
        isDragging = false;
        isResizing = false;
        selectedOverlay = null;
    });

    function updateOverlayData(overlayEl) {
        const index = parseInt(overlayEl.dataset.index);
        const overlay = overlays[index];
        
        if (overlay && documentDimensions.width > 0 && documentDimensions.height > 0) {
            // Calculate percentages with high precision
            overlay.x = parseFloat(((overlayEl.offsetLeft / documentDimensions.width) * 100).toFixed(4));
            overlay.y = parseFloat(((overlayEl.offsetTop / documentDimensions.height) * 100).toFixed(4));
            overlay.width = parseFloat(((overlayEl.offsetWidth / documentDimensions.width) * 100).toFixed(4));
            overlay.height = parseFloat(((overlayEl.offsetHeight / documentDimensions.height) * 100).toFixed(4));
            
            updatePositionControls(overlayEl);
        }
    }

    // Position control inputs with debouncing
    let inputTimeout;
    ['posX', 'posY', 'width', 'height'].forEach(function(id) {
        document.getElementById(id).addEventListener('input', function() {
            clearTimeout(inputTimeout);
            inputTimeout = setTimeout(function() {
            if (selectedOverlay) {
                const index = parseInt(selectedOverlay.dataset.index);
                const overlay = overlays[index];
                
                if (overlay) {
                    overlay.x = parseFloat(document.getElementById('posX').value) || 0;
                    overlay.y = parseFloat(document.getElementById('posY').value) || 0;
                    overlay.width = parseFloat(document.getElementById('width').value) || 10;
                    overlay.height = parseFloat(document.getElementById('height').value) || 10;
                    
                    updateOverlayPosition(selectedOverlay, overlay);
                }
            }
            }, 100);
        });
    });

    // Signature and stamp selection
    document.querySelectorAll('.signature-item, .stamp-item').forEach(function(item) {
        item.addEventListener('click', function() {
            const type = this.dataset.type;
            const id = this.dataset.id;
            const url = this.dataset.url;
            const name = this.dataset.name;
            
            // Create new overlay with consistent default positioning
            const newOverlay = {
                type: type,
                [type === 'signature' ? 'signature_id' : 'stamp_id']: parseInt(id),
                x: 10,
                y: 10,
                width: type === 'signature' ? 20 : 15,
                height: type === 'signature' ? 12 : 15
            };
            
            const index = overlays.length;
            overlays.push(newOverlay);
            createOverlayElement(newOverlay, index);
            
            // Update selection
            document.querySelectorAll('.signature-item, .stamp-item').forEach(el => el.classList.remove('selected'));
            this.classList.add('selected');
        });
    });

    // Control buttons
    document.getElementById('saveOverlays').addEventListener('click', function() {
        // Validate that we have consistent data
        const validatedOverlays = overlays.map(overlay => ({
            ...overlay,
            x: parseFloat(overlay.x.toFixed(4)),
            y: parseFloat(overlay.y.toFixed(4)),
            width: parseFloat(overlay.width.toFixed(4)),
            height: parseFloat(overlay.height.toFixed(4))
        }));
        
        const modal = new bootstrap.Modal(document.getElementById('loadingModal'));
        modal.show();
        
        fetch(window.saveUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken
            },
            body: JSON.stringify({
                overlays: JSON.stringify(validatedOverlays)
            })
        })
        .then(response => response.json())
        .then(data => {
            modal.hide();
            if (data.message) {
                alert('Posisi overlay berhasil disimpan!');
                // Update local overlays with saved data
                overlays = validatedOverlays;
            } else {
                alert('Gagal menyimpan: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            modal.hide();
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menyimpan');
        });
    });

    document.getElementById('resetOverlays').addEventListener('click', function() {
        overlays.forEach(function(overlay, index) {
            overlay.x = 10 + (index * 5);
            overlay.y = 10 + (index * 5);
            overlay.width = overlay.type === 'signature' ? 20 : 15;
            overlay.height = overlay.type === 'signature' ? 12 : 15;
            
            const overlayEl = document.querySelector(`[data-index="${index}"]`);
            if (overlayEl) {
                updateOverlayPosition(overlayEl, overlay);
            }
        });
        
        if (selectedOverlay) {
            updatePositionControls(selectedOverlay);
        }
    });

    document.getElementById('clearOverlays').addEventListener('click', function() {
        if (confirm('Apakah Anda yakin ingin menghapus semua overlay?')) {
            overlays = [];
            overlayContainer.innerHTML = '';
            document.querySelector('.position-controls').style.display = 'none';
            selectedOverlay = null;
        }
    });

    // Prevent accidental drags on the document
    const documentBg = document.getElementById('documentBg');
    if (documentBg) {
        documentBg.addEventListener('dragstart', function(e) {
            e.preventDefault();
        });
    }

    // Initialize app after document loads
    function initializeApp() {
        // Additional initialization if needed
        console.log('Signature editor initialized with document dimensions:', documentDimensions);
    }

    if (documentBg && documentBg.complete) {
        initializeApp();
    } else if (documentBg) {
        documentBg.addEventListener('load', initializeApp);
    }
});
</script>
@endpush 