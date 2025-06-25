@extends('layouts.app')

@section('title', 'Preview Dokumen')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Preview – {{ ucfirst(str_replace('_', ' ', $documentType)) }}</h4>
        <a href="javascript:window.close();" class="btn btn-secondary btn-sm"><i class="fas fa-times me-1"></i>Tutup</a>
    </div>

    <!-- Navigation Controls -->
    <div class="d-flex justify-content-center align-items-center mb-3 bg-light p-3 rounded" id="navControls" style="display: none;">
        <button id="prevPage" class="btn btn-outline-primary btn-sm me-2">
            <i class="fas fa-chevron-left"></i> Sebelumnya
        </button>
        <span class="mx-3">
            <strong>Halaman <span id="pageNum">1</span> dari <span id="pageCount">1</span></strong>
        </span>
        <button id="nextPage" class="btn btn-outline-primary btn-sm me-3">
            Selanjutnya <i class="fas fa-chevron-right"></i>
        </button>
        <div class="btn-group ms-3">
            <button id="zoomOut" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-search-minus"></i>
            </button>
            <button id="fitWidth" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-expand-arrows-alt"></i>
            </button>
            <button id="zoomIn" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-search-plus"></i>
            </button>
        </div>
    </div>

    <!-- Document Status Info -->
    @if(isset($isSignedDocument) && $isSignedDocument)
        <div class="alert alert-success border-0 mb-3">
            <div class="d-flex align-items-center">
                <i class="fas fa-certificate fa-lg text-success me-3"></i>
                <div>
                    <strong>Dokumen Tertanda Tangan</strong><br>
                    <small>Menampilkan dokumen final yang sudah ditandatangani. Tanda tangan sudah terintegrasi dalam PDF.</small>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-info border-0 mb-3">
            <div class="d-flex align-items-center">
                <i class="fas fa-file-pdf fa-lg text-primary me-3"></i>
                <div>
                    <strong>Dokumen Asli dengan Overlay</strong><br>
                    <small>Menampilkan dokumen asli dengan tanda tangan sebagai overlay. Total overlay: {{ count($overlays) }}</small>
                </div>
            </div>
        </div>
    @endif

    <!-- Status Display -->
    <div id="statusDisplay" class="alert alert-info">
        <i class="fas fa-spinner fa-spin me-2"></i>Memuat dokumen PDF...
    </div>

    <!-- View Mode Toggle -->
    <div class="d-flex justify-content-center align-items-center mb-3 bg-light p-3 rounded">
        <div class="btn-group" role="group">
            <input type="radio" class="btn-check" name="viewMode" id="canvasMode" checked>
            <label class="btn btn-outline-primary btn-sm" for="canvasMode">
                <i class="fas fa-edit me-1"></i>Mode Interaktif
            </label>
            
            <input type="radio" class="btn-check" name="viewMode" id="iframeMode">
            <label class="btn btn-outline-success btn-sm" for="iframeMode">
                <i class="fas fa-file-pdf me-1"></i>Tampilkan Semua Halaman
            </label>
        </div>
    </div>

    <!-- PDF Container - Canvas Mode (Interactive) -->
    <div id="pdfWrapper" style="border:1px solid #ced4da; position:relative; overflow:auto; background: #f8f9fa; min-height: 500px; display: none;">
        <div id="pdfContainer" style="position: relative; text-align: center; padding: 20px;">
            <canvas id="pdfCanvas" style="max-width: 100%; display: block; margin: 0 auto; box-shadow: 0 4px 8px rgba(0,0,0,0.1);"></canvas>
            <!-- Overlays will be dynamically positioned here -->
            <div id="overlayContainer" style="position: absolute; top: 20px; left: 50%; transform: translateX(-50%); pointer-events: none;">
                <!-- Overlays for current page will be inserted here -->
            </div>
        </div>
    </div>

    <!-- PDF Container - Iframe Mode (All Pages) -->
    <div id="pdfWrapperIframe" style="border:1px solid #ced4da; position:relative; overflow:auto; background: #f8f9fa; min-height: 500px; display: none;">
        <div class="alert alert-info mb-2">
            <i class="fas fa-info-circle me-1"></i>
            <strong>Mode Iframe:</strong> Menampilkan semua halaman dokumen. Gunakan scroll atau zoom browser untuk navigasi.
            @if(($actualPageCount ?? 0) > 1)
                <br><small>Dokumen ini memiliki {{ $actualPageCount }} halaman. Scroll ke bawah untuk melihat halaman berikutnya.</small>
            @endif
        </div>
        <iframe src="{{ $fileUrl }}#view=FitH&pagemode=none&toolbar=1&navpanes=0&scrollbar=1" width="100%" height="700px" style="border: none;">
            <p>Browser Anda tidak mendukung iframe. <a href="{{ $fileUrl }}" target="_blank">Klik di sini untuk membuka PDF.</a></p>
        </iframe>
    </div>

    <!-- Debug Info (can be hidden in production) -->
    <div class="mt-3">
        <details open>
            <summary class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-bug me-1"></i>Debug Information
            </summary>
            <div class="mt-2 p-3 bg-light border rounded">
                <div class="row">
                    <div class="col-md-6">
                        <small>
                            <strong>Document Type:</strong> {{ $documentType }}<br>
                            <strong>File URL:</strong> <a href="{{ $fileUrl }}" target="_blank">{{ $fileUrl }}</a><br>
                            <strong>Overlays Count:</strong> {{ count($overlays) }}<br>
                            <strong>Document Type:</strong> {{ isset($isSignedDocument) && $isSignedDocument ? 'Signed (Final)' : 'Original (with overlays)' }}<br>
                            <strong>Backend Data:</strong><br>
                            @php
                                $originalFile = $dokumen[$documentType] ?? 'Not found';
                                $signedFile = $dokumen['signed'][$documentType] ?? 'Not found';
                                
                                // Check file existence
                                $originalExists = false;
                                $signedExists = false;
                                
                                if ($originalFile !== 'Not found') {
                                    $originalPath = ltrim($originalFile, '/');
                                    $originalExists = Storage::disk('public')->exists($originalPath);
                                }
                                
                                if ($signedFile !== 'Not found') {
                                    $signedPath = ltrim($signedFile, '/');
                                    $signedExists = Storage::disk('public')->exists($signedPath);
                                }
                            @endphp
                            &nbsp;&nbsp;- Original: {{ $originalFile }} {{ $originalExists ? '✅' : '❌' }}<br>
                            &nbsp;&nbsp;- Signed: {{ $signedFile }} {{ $signedExists ? '✅' : '❌' }}<br>
                            &nbsp;&nbsp;- Currently viewing: {{ basename($fileUrl) }}
                        </small>
                    </div>
                    <div class="col-md-6">
                        <div id="pdfDebugInfo">
                            <small>
                                <strong>PDF Info:</strong> Loading...<br>
                                <strong>Current Page:</strong> <span id="debugCurrentPage">-</span><br>
                                <strong>Total Pages:</strong> <span id="debugTotalPages">-</span><br>
                                <strong>Backend Pages:</strong> {{ $actualPageCount ?? 'Unknown' }}<br>
                                <strong>Scale:</strong> <span id="debugScale">-</span>
                            </small>
                        </div>
                        <div class="mt-2">
                            <button id="checkOriginalBtn" class="btn btn-outline-info btn-sm">Check Original PDF</button>
                        </div>
                    </div>
                </div>
                <div class="mt-2">
                    <small><strong>Console Log:</strong></small>
                    <pre id="debugConsole" style="background: #f8f9fa; padding: 10px; max-height: 200px; overflow-y: auto; font-size: 11px; border: 1px solid #dee2e6; border-radius: 4px;"></pre>
                </div>
            </div>
        </details>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== PDF Preview Debug Start ===');
    
    // Configuration
    var pdfUrl = "{{ $fileUrl }}";
    var overlaysData = @json($overlays);
    
    // Debug console element
    var debugConsole = document.getElementById('debugConsole');
    
    // Enhanced logging function
    function debugLog(message, type) {
        var timestamp = new Date().toLocaleTimeString();
        var logMessage = timestamp + ': ' + message;
        console.log(logMessage);
        
        if (debugConsole) {
            debugConsole.textContent += logMessage + '\n';
            debugConsole.scrollTop = debugConsole.scrollHeight;
        }
    }
    
    debugLog('PDF URL: ' + pdfUrl);
    debugLog('Overlays count: ' + (overlaysData ? overlaysData.length : 0));
    debugLog('Backend page count: {{ $actualPageCount ?? 0 }}');
    
    // Check if PDF.js is available
    if (typeof pdfjsLib === 'undefined') {
        debugLog('ERROR: PDF.js library not loaded!');
        document.getElementById('statusDisplay').innerHTML = '<i class="fas fa-exclamation-triangle text-danger me-2"></i>Error: PDF.js library not loaded';
        return;
    }
    
    debugLog('PDF.js version: ' + pdfjsLib.version);
    
    // Set PDF.js worker
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    
    // Get DOM elements
    var statusDisplay = document.getElementById('statusDisplay');
    var pdfWrapper = document.getElementById('pdfWrapper');
    var pdfWrapperIframe = document.getElementById('pdfWrapperIframe');
    var canvas = document.getElementById('pdfCanvas');
    var overlayContainer = document.getElementById('overlayContainer');
    var navControls = document.getElementById('navControls');
    var canvasMode = document.getElementById('canvasMode');
    var iframeMode = document.getElementById('iframeMode');
    var pageNumElement = document.getElementById('pageNum');
    var pageCountElement = document.getElementById('pageCount');
    var prevPageBtn = document.getElementById('prevPage');
    var nextPageBtn = document.getElementById('nextPage');
    var zoomInBtn = document.getElementById('zoomIn');
    var zoomOutBtn = document.getElementById('zoomOut');
    var fitWidthBtn = document.getElementById('fitWidth');
    
    // Debug info elements
    var debugCurrentPage = document.getElementById('debugCurrentPage');
    var debugTotalPages = document.getElementById('debugTotalPages');
    var debugScale = document.getElementById('debugScale');
    
    // Check if required elements exist
    if (!canvas) {
        debugLog('ERROR: Canvas element not found!');
        statusDisplay.innerHTML = '<i class="fas fa-exclamation-triangle text-danger me-2"></i>Error: Canvas element not found';
        return;
    }
    
    var ctx = canvas.getContext('2d');
    
    // PDF state
    var pdfDoc = null;
    var currentPage = 1;
    var totalPages = 1;
    var scale = 1.0;
    var isRendering = false;
    var currentViewMode = 'canvas';
    
    // Update debug info
    function updateDebugInfo() {
        if (debugCurrentPage) debugCurrentPage.textContent = currentPage;
        if (debugTotalPages) debugTotalPages.textContent = totalPages;
        if (debugScale) debugScale.textContent = scale.toFixed(2);
    }
    
    // Show status message
    function showStatus(message, type) {
        type = type || 'info';
        var iconClass = {
            'info': 'fas fa-info-circle text-info',
            'success': 'fas fa-check-circle text-success',
            'error': 'fas fa-exclamation-triangle text-danger',
            'loading': 'fas fa-spinner fa-spin text-primary'
        }[type] || 'fas fa-info-circle text-info';
        
        statusDisplay.innerHTML = '<i class="' + iconClass + ' me-2"></i>' + message;
        statusDisplay.className = 'alert alert-' + (type === 'error' ? 'danger' : type === 'success' ? 'success' : 'info');
        debugLog('Status: ' + message);
    }
    
    // Calculate scale to fit width
    function calculateFitScale() {
        if (!pdfDoc) return Promise.resolve(1);
        
        return pdfDoc.getPage(1).then(function(page) {
            var viewport = page.getViewport({scale: 1});
            var containerWidth = pdfWrapper.clientWidth - 80; // More padding for better fit
            var calculatedScale = containerWidth / viewport.width;
            debugLog('Fit scale calculated: ' + calculatedScale.toFixed(3) + ' (container: ' + containerWidth + 'px, page: ' + viewport.width + 'px)');
            return Math.max(0.3, Math.min(3, calculatedScale));
        });
    }
    
    // Render specific page
    function renderPage(pageNum) {
        if (!pdfDoc || isRendering) {
            debugLog('Cannot render: PDF not loaded (' + (!pdfDoc) + ') or already rendering (' + isRendering + ')');
            return;
        }
        
        if (pageNum < 1 || pageNum > totalPages) {
            debugLog('Invalid page number: ' + pageNum + ' (valid range: 1-' + totalPages + ')');
            return;
        }
        
        isRendering = true;
        currentPage = pageNum;
        showStatus('Rendering page ' + pageNum + ' of ' + totalPages + '...', 'loading');
        
        debugLog('Rendering page ' + pageNum + ' at scale ' + scale.toFixed(2));
        
        // Check if this page can be rendered by PDF.js
        var pdfJsPages = pdfDoc.numPages;
        
        // For consistency, use embedded viewer for all pages beyond what PDF.js can access
        // or when backend detects more pages than PDF.js
        var backendPages = parseInt('{{ $actualPageCount ?? 0 }}');
        var shouldUseEmbedded = (pageNum > pdfJsPages) || (backendPages > pdfJsPages && pageNum > 1);
        
        if (shouldUseEmbedded) {
            debugLog('Page ' + pageNum + ' requires embedded rendering (PDF.js: ' + pdfJsPages + ', Backend: ' + backendPages + ')');
            renderPageWithEmbeddedViewer(pageNum);
            return;
        }
        
        pdfDoc.getPage(pageNum).then(function(page) {
            var viewport = page.getViewport({scale: scale});
            
            // Hide embedded viewer if it was shown for previous page
            hideEmbeddedViewer();
            
            // Set canvas size
            canvas.width = viewport.width;
            canvas.height = viewport.height;
            canvas.style.width = viewport.width + 'px';
            canvas.style.height = viewport.height + 'px';
            
            debugLog('Canvas dimensions: ' + viewport.width + 'x' + viewport.height);
            
            // Clear canvas
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            // Render the page
            var renderContext = {
                canvasContext: ctx,
                viewport: viewport
            };
            
            return page.render(renderContext).promise;
        }).then(function() {
            debugLog('Page ' + pageNum + ' rendered successfully');
            
            // Render overlays
            renderOverlays(pageNum);
            
            // Update navigation
            updateNavigation();
            updateDebugInfo();
            
            // Show success status
            showStatus('Page ' + pageNum + ' of ' + totalPages + ' loaded successfully', 'success');
            
            isRendering = false;
        }).catch(function(error) {
            debugLog('ERROR rendering page: ' + error.message);
            
            // If this is a page that backend detected but PDF.js can't render
            if (error.message.includes('Invalid page request') || error.message.includes('page does not exist')) {
                debugLog('Page ' + pageNum + ' cannot be rendered by PDF.js - showing via embedded viewer');
                
                // Show page via embedded PDF viewer
                renderPageWithEmbeddedViewer(pageNum);
                return; // Exit early since function handles completion
            } else {
                showStatus('Error rendering page: ' + error.message, 'error');
            }
            
            isRendering = false;
        });
    }
    
    // Render overlays for current page
    function renderOverlays(pageNum) {
        // Clear existing overlays first to prevent duplicates
        if (overlayContainer) {
            overlayContainer.innerHTML = '';
        }
        
        if (!overlaysData || overlaysData.length === 0) {
            debugLog('No overlays to render');
            return;
        }
        
        // Filter overlays for current page
        var pageOverlays = overlaysData.filter(function(overlay) {
            return parseInt(overlay.page || 1) === pageNum;
        });
        
        debugLog('Rendering ' + pageOverlays.length + ' overlays for page ' + pageNum);
        
        if (pageOverlays.length === 0) return;
        
        // Get page viewport for coordinate calculation
        pdfDoc.getPage(pageNum).then(function(page) {
            var viewport = page.getViewport({scale: scale});
            
            // Ensure overlay container matches canvas size exactly for 1:1 mapping
            if (overlayContainer) {
                overlayContainer.style.width = viewport.width + 'px';
                overlayContainer.style.height = viewport.height + 'px';
            }
            
            pageOverlays.forEach(function(overlay, index) {
                // Validate overlay data before creating element
                if (!overlay.url || overlay.x_percent === undefined || overlay.y_percent === undefined) {
                    debugLog('Skipping invalid overlay: ' + JSON.stringify(overlay));
                    return;
                }
                
                var img = document.createElement('img');
                img.src = overlay.url;
                img.alt = 'Overlay ' + (index + 1);
                img.style.position = 'absolute';
                img.style.pointerEvents = 'none';
                img.style.zIndex = '10';
                
                // Calculate positions using consistent percentage to pixel conversion
                var x = (parseFloat(overlay.x_percent) / 100) * viewport.width;
                var y = (parseFloat(overlay.y_percent) / 100) * viewport.height;
                var w = (parseFloat(overlay.width_percent) / 100) * viewport.width;
                var h = (parseFloat(overlay.height_percent) / 100) * viewport.height;
                
                // Apply pixel-perfect positioning
                img.style.left = Math.round(x) + 'px';
                img.style.top = Math.round(y) + 'px';
                img.style.width = Math.round(w) + 'px';
                img.style.height = Math.round(h) + 'px';
                img.style.objectFit = 'contain';
                
                // Store percentage data for consistency checks
                img.dataset.xPercent = overlay.x_percent;
                img.dataset.yPercent = overlay.y_percent;
                img.dataset.widthPercent = overlay.width_percent;
                img.dataset.heightPercent = overlay.height_percent;
                
                if (overlayContainer) {
                    overlayContainer.appendChild(img);
                    debugLog('Overlay ' + (index + 1) + ' positioned at ' + Math.round(x) + ',' + Math.round(y) + ' (page ' + pageNum + ')');
                }
            });
            
            debugLog('Overlay container sized to ' + viewport.width + 'x' + viewport.height);
        }).catch(function(error) {
            console.error('Error rendering overlays for page ' + pageNum + ':', error);
        });
    }
    
    // Render page using embedded viewer for consistent display
    function renderPageWithEmbeddedViewer(pageNum) {
        debugLog('Rendering page ' + pageNum + ' with embedded viewer for consistency');
        
        // Get container dimensions for proper sizing
        var canvasContainer = canvas.parentElement;
        var containerWidth = canvasContainer.clientWidth || 800;
        var containerHeight = canvasContainer.clientHeight || 600;
        
        // Clear canvas first
        if (canvas.width > 0 && canvas.height > 0) {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }
        
        // Create or update embedded viewer
        var embeddedViewer = document.getElementById('embeddedPDFViewer');
        if (!embeddedViewer) {
            embeddedViewer = document.createElement('div');
            embeddedViewer.id = 'embeddedPDFViewer';
            embeddedViewer.style.position = 'absolute';
            embeddedViewer.style.top = '0';
            embeddedViewer.style.left = '0';
            embeddedViewer.style.width = '100%';
            embeddedViewer.style.height = '100%';
            embeddedViewer.style.backgroundColor = '#ffffff';
            embeddedViewer.style.zIndex = '3';
            embeddedViewer.style.display = 'none';
            canvasContainer.appendChild(embeddedViewer);
        }
        
        // Create iframe with proper sizing and page targeting
        var iframeHeight = Math.max(containerHeight, 500);
        var headerText = 'Halaman ' + pageNum + ' dari ' + totalPages + ' - Mode Interaktif (Tampilan Konsisten)';
        
        embeddedViewer.innerHTML = 
            '<div style="padding: 8px; background: #e3f2fd; border-bottom: 1px solid #2196f3; font-size: 12px; color: #1565c0;">' +
            '<i class="fas fa-file-pdf text-primary me-1"></i>' +
            headerText +
            '</div>' +
            '<iframe src="' + pdfUrl + '#page=' + pageNum + '&view=FitH&zoom=page-width&toolbar=1" ' +
            'width="100%" height="' + (iframeHeight - 40) + 'px" ' +
            'style="border: none; display: block; background: white;">' +
            '<p>Browser tidak mendukung iframe. <a href="' + pdfUrl + '#page=' + pageNum + '" target="_blank">Buka PDF halaman ' + pageNum + '</a></p>' +
            '</iframe>';
        
        // Show the embedded viewer
        embeddedViewer.style.display = 'block';
        
        // Update navigation and status
        updateNavigation();
        updateDebugInfo();
        showStatus('Halaman ' + pageNum + ' dari ' + totalPages + ' - Mode Interaktif (Rendering Konsisten)', 'success');
        
        isRendering = false;
    }
    
    // Hide embedded viewer when rendering normal pages
    function hideEmbeddedViewer() {
        var embeddedViewer = document.getElementById('embeddedPDFViewer');
        if (embeddedViewer) {
            embeddedViewer.style.display = 'none';
        }
    }
    
    // Switch view modes
    function switchViewMode(mode) {
        currentViewMode = mode;
        debugLog('Switching to ' + mode + ' view mode');
        
        if (mode === 'canvas') {
            pdfWrapper.style.display = 'block';
            pdfWrapperIframe.style.display = 'none';
            if (navControls && totalPages > 1) {
                navControls.style.display = 'flex';
            }
            // If PDF is loaded, render current page
            if (pdfDoc) {
                renderPage(currentPage);
            }
        } else {
            pdfWrapper.style.display = 'none';
            pdfWrapperIframe.style.display = 'block';
            if (navControls) {
                navControls.style.display = 'none';
            }
            var backendPages = parseInt('{{ $actualPageCount ?? 0 }}');
            if (backendPages > 1) {
                showStatus('Menampilkan semua ' + backendPages + ' halaman dalam mode iframe - scroll untuk navigasi', 'success');
            } else {
                showStatus('Menampilkan semua halaman dalam mode iframe', 'success');
            }
        }
    }


    // Update navigation controls
    function updateNavigation() {
        if (pageNumElement) pageNumElement.textContent = currentPage;
        if (pageCountElement) pageCountElement.textContent = totalPages;
        
        if (prevPageBtn) prevPageBtn.disabled = (currentPage <= 1);
        if (nextPageBtn) nextPageBtn.disabled = (currentPage >= totalPages);
        
        // Show navigation controls only in canvas mode and if more than 1 page
        if (totalPages > 1 && navControls && currentViewMode === 'canvas') {
            navControls.style.display = 'flex';
            debugLog('Navigation controls shown (canvas mode)');
        } else if (navControls) {
            navControls.style.display = 'none';
            debugLog('Navigation controls hidden');
        }
    }
    
    // Load PDF document with enhanced page detection and fallback
    function loadPDF() {
        showStatus('Loading PDF document...', 'loading');
        
        if (!pdfUrl) {
            debugLog('ERROR: No PDF URL provided');
            showStatus('No PDF URL provided', 'error');
            return;
        }
        
        debugLog('Loading PDF from: ' + pdfUrl);
        
        // Use backend page count as authoritative source
        var backendPages = parseInt('{{ $actualPageCount ?? 0 }}');
        debugLog('Backend page count (authoritative): ' + backendPages);
        
        pdfjsLib.getDocument(pdfUrl).promise.then(function(pdf) {
            pdfDoc = pdf;
            var pdfJsPages = pdf.numPages;
            
            debugLog('PDF.js detected pages: ' + pdfJsPages);
            debugLog('Backend detected pages: ' + backendPages);
            
            // ALWAYS prioritize backend page count if it's available and reasonable
            // Backend uses comprehensive analysis including FPDI and multiple regex methods
            if (backendPages > 0) {
                totalPages = backendPages;
                debugLog('Using backend page count: ' + backendPages + ' (more reliable than PDF.js: ' + pdfJsPages + ')');
                
                // Log discrepancy if PDF.js found different count
                if (pdfJsPages !== backendPages) {
                    debugLog('Page count discrepancy: PDF.js=' + pdfJsPages + ', Backend=' + backendPages);
                }
            } else {
                // No backend count, fall back to PDF.js
                totalPages = pdfJsPages;
                debugLog('No backend count, using PDF.js: ' + pdfJsPages);
            }
            
            // Ensure totalPages is at least 1
            if (totalPages < 1) {
                totalPages = 1;
                debugLog('Corrected totalPages to minimum value: 1');
            }
            
            currentPage = 1;
            
            // Show PDF container
            pdfWrapper.style.display = 'block';
            
            debugLog('Final page count decision: ' + totalPages + ' pages');
            
            // Calculate initial scale and render first page
            calculateFitScale().then(function(fitScale) {
                scale = fitScale;
                debugLog('Using initial scale: ' + scale.toFixed(2));
                // Start with canvas mode
                switchViewMode('canvas');
            }).catch(function(error) {
                debugLog('Scale calculation failed, using default scale: ' + error.message);
                scale = 1.0;
                // Start with canvas mode
                switchViewMode('canvas');
            });
            
        }).catch(function(error) {
            debugLog('ERROR loading PDF with PDF.js: ' + error.message);
            
            // If PDF.js fails but we have backend page count, use embedded mode for all pages
            if (backendPages > 0) {
                debugLog('PDF.js failed but backend detected ' + backendPages + ' pages, switching to embedded-only mode');
                totalPages = backendPages;
                currentPage = 1;
                
                // Show PDF container
                pdfWrapper.style.display = 'block';
                
                // Force use embedded viewer for all pages since PDF.js failed
                showStatus('Dokumen memiliki ' + backendPages + ' halaman - menggunakan mode embedded viewer', 'info');
                
                // Update navigation and render first page with embedded viewer
                updateNavigation();
                renderPageWithEmbeddedViewer(1);
                
            } else {
                // Complete failure - no PDF.js and no backend count
                debugLog('Complete failure: PDF.js failed and no backend page count');
                showStatus('Error: Tidak dapat memuat dokumen PDF. Error: ' + error.message, 'error');
                
                // Try to show something anyway with embedded iframe
                totalPages = 1;
                currentPage = 1;
                switchViewMode('iframe');
            }
        });
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

    // Event listeners
    if (prevPageBtn) {
        prevPageBtn.addEventListener('click', function() {
            debugLog('Previous page button clicked');
            if (currentPage > 1 && !isRendering) {
                renderPage(currentPage - 1);
            }
        });
    }
    
    if (nextPageBtn) {
        nextPageBtn.addEventListener('click', function() {
            debugLog('Next page button clicked');
            if (currentPage < totalPages && !isRendering) {
                renderPage(currentPage + 1);
            }
        });
    }

    if (zoomInBtn) {
        zoomInBtn.addEventListener('click', function() {
            debugLog('Zoom in button clicked');
            if (!isRendering && currentViewMode === 'canvas') {
                scale *= 1.25;
                renderPage(currentPage);
            }
        });
    }
    
    if (zoomOutBtn) {
        zoomOutBtn.addEventListener('click', function() {
            debugLog('Zoom out button clicked');
            if (!isRendering && currentViewMode === 'canvas') {
                scale /= 1.25;
                renderPage(currentPage);
            }
        });
    }
    
    if (fitWidthBtn) {
        fitWidthBtn.addEventListener('click', function() {
            debugLog('Fit width button clicked');
            if (!isRendering && currentViewMode === 'canvas') {
                calculateFitScale().then(function(fitScale) {
                    scale = fitScale;
                    renderPage(currentPage);
                });
            }
        });
    }
    
    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.target.tagName.toLowerCase() === 'input' || e.target.tagName.toLowerCase() === 'textarea') {
            return;
        }
        
        switch(e.which) {
            case 37: // Left arrow
            case 38: // Up arrow
                if (currentPage > 1 && !isRendering) {
                    debugLog('Keyboard navigation: Previous page');
                    renderPage(currentPage - 1);
                    e.preventDefault();
                }
                break;
            case 39: // Right arrow
            case 40: // Down arrow
                if (currentPage < totalPages && !isRendering) {
                    debugLog('Keyboard navigation: Next page');
                    renderPage(currentPage + 1);
                    e.preventDefault();
                }
                break;
        }
    });
    
    // Check original PDF button
    var checkOriginalBtn = document.getElementById('checkOriginalBtn');
    if (checkOriginalBtn) {
        checkOriginalBtn.addEventListener('click', function() {
            debugLog('Checking original PDF...');
            
            // Get original file URL
            var originalUrl = "{{ isset($dokumen[$documentType]) ? Storage::url($dokumen[$documentType]) : '' }}";
            if (!originalUrl) {
                debugLog('ERROR: No original file URL found');
                return;
            }
            
            debugLog('Original PDF URL: ' + originalUrl);
            
            pdfjsLib.getDocument(originalUrl).promise.then(function(pdf) {
                debugLog('Original PDF loaded successfully. Total pages: ' + pdf.numPages);
                
                // Compare with current PDF
                if (pdfDoc) {
                    debugLog('COMPARISON: Original=' + pdf.numPages + ' pages, Signed=' + totalPages + ' pages');
                    if (pdf.numPages !== totalPages) {
                        debugLog('WARNING: Page count mismatch! Original has ' + pdf.numPages + ' pages but signed has ' + totalPages + ' pages');
                    }
                } else {
                    debugLog('Current PDF not loaded yet');
                }
            }).catch(function(error) {
                debugLog('ERROR loading original PDF: ' + error.message);
            });
        });
    }
    
    // Start loading PDF
    debugLog('Initializing PDF preview...');
    loadPDF();
});
</script>
@endpush 