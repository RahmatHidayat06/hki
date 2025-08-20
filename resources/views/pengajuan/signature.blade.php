@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
		<div class="col-lg-10">
			<div class="card border-0 shadow-sm mb-3">
				<div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-2">
					<div>
						<h4 class="text-primary mb-1">
                        <i class="fas fa-signature me-2"></i>Tanda Tangan Digital Permohonan Pendaftaran
                    </h4>
						<div class="small text-muted">
							<strong>Pengajuan:</strong> {{ $pengajuan->judul_karya }}
							<span class="mx-2">|</span>
							<strong>Nomor:</strong> {{ $pengajuan->nomor_pengajuan ?? 'Belum ada nomor' }}
						</div>
					</div>
					@if(isset($pdfUrl) && $pdfUrl)
						<a href="{{ $pdfUrl }}" target="_blank" class="btn btn-outline-secondary btn-sm">
							<i class="fas fa-external-link-alt me-1"></i>Buka di Tab Baru
						</a>
					@endif
				</div>
			</div>

			<div class="row g-3">
				<!-- LEFT: PDF Viewer -->
				<div class="col-lg-8">
					<div class="card border-0 shadow-sm h-100">
						<div class="card-header bg-light d-flex align-items-center gap-2">
							<button id="prevPage" class="btn btn-sm btn-light border"><i class="fas fa-chevron-left"></i></button>
							<div class="small">Halaman <span id="pageNum">1</span> dari <span id="pageCount">1</span></div>
							<button id="nextPage" class="btn btn-sm btn-light border"><i class="fas fa-chevron-right"></i></button>
							<div class="ms-auto d-flex align-items-center gap-2">
								<button id="zoomOut" class="btn btn-sm btn-light border"><i class="fas fa-search-minus"></i></button>
								<button id="zoomIn" class="btn btn-sm btn-light border"><i class="fas fa-search-plus"></i></button>
								<button id="fitWidth" class="btn btn-sm btn-light border"><i class="fas fa-compress-arrows-alt"></i> Fit Width</button>
							</div>
						</div>
						<div class="card-body p-2">
							<div id="pdfStage" class="position-relative mx-auto" style="max-width: 100%;">
								<canvas id="pdfCanvas" style="width:100%; height:auto; z-index:1; position:relative;"></canvas>
								<img id="draggableSignature" alt="Tanda tangan" draggable="false" style="position:absolute; display:none; cursor:move; user-select:none; -webkit-user-drag:none; z-index:5; left:10px; top:10px;">
							</div>
							<div class="d-flex justify-content-between align-items-center mt-2 px-1">
								<small class="text-muted">Tip: klik pada area PDF untuk meletakkan tanda tangan tepat di titik tersebut</small>
								<small class="text-muted" id="coords">X: - % | Y: - % | W: - %</small>
							</div>
                </div>
            </div>
                </div>

				<!-- RIGHT: Controls -->
				<div class="col-lg-4">
					<div class="card border-0 shadow-sm mb-3">
						<div class="card-header bg-light fw-semibold">Buat / Upload Tanda Tangan</div>
                <div class="card-body">
                    <form id="signatureForm" method="POST" action="{{ route('pengajuan.signature.save', $pengajuan->id) }}">
                        @csrf
								<div class="mb-2">
									<div class="btn-group w-100">
										<button type="button" id="tabDraw" class="btn btn-outline-primary active"><i class="fas fa-pen me-1"></i>Gambar</button>
										<button type="button" id="tabUpload" class="btn btn-outline-secondary"><i class="fas fa-upload me-1"></i>Upload</button>
									</div>
								</div>
								<div id="drawPane" class="mb-2">
									<canvas id="drawCanvas" width="500" height="150" style="border:2px dashed #dee2e6; border-radius:8px; width:100%; height:150px;"></canvas>
									<div class="d-flex justify-content-between mt-1">
                                <small class="text-muted">Gambar tanda tangan Anda di area di atas</small>
										<button type="button" id="clearDraw" class="btn btn-sm btn-outline-secondary"><i class="fas fa-eraser me-1"></i>Bersihkan</button>
                            </div>
                        </div>
								<div id="uploadPane" class="mb-2" style="display:none;">
									<input type="file" id="uploadInput" accept="image/png,image/jpeg" class="form-control">
									<small class="text-muted">Format JPG/PNG, max 5MB</small>
								</div>
								<div class="mb-3">
									<label class="form-label">Ukuran</label>
									<input type="range" id="sizeSlider" min="5" max="30" value="15" class="form-range">
                        </div>
                        <input type="hidden" name="signature_data" id="signatureData">
								<input type="hidden" name="placement" id="placementInput">
								<div class="form-check mb-3">
									<input class="form-check-input" type="checkbox" id="agree" required>
									<label class="form-check-label" for="agree">Saya menyetujui keabsahan tanda tangan digital ini</label>
                        </div>
                                <div class="d-grid gap-2">
                                    <button type="button" id="autoSnapBtn" class="btn btn-outline-success"><i class="fas fa-magic me-1"></i>Pasang Otomatis (Rekomendasi)</button>
                                    <button type="button" id="resetPosBtn" class="btn btn-outline-secondary">Reset Posisi</button>
                                    <button type="submit" id="submitBtn" class="btn btn-primary"><i class="fas fa-save me-1"></i>Simpan & Tempel</button>
                                </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
	</div>
</div>

<!-- PDF.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
<script>
(function(){
	const pdfUrl = @json($pdfUrl ?? null);
	const pdfjsLib = window['pdfjs-dist/build/pdf'];
	pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';

	let pdfDoc = null, pageNum = 1, scale = 1.2;
	const canvas = document.getElementById('pdfCanvas');
    const ctx = canvas.getContext('2d');
	const stage = document.getElementById('pdfStage');
	const draggable = document.getElementById('draggableSignature');
	const pageNumEl = document.getElementById('pageNum');
	const pageCountEl = document.getElementById('pageCount');
	const coordsEl = document.getElementById('coords');
    // Preset rekomendasi untuk Form Permohonan (bottom-right sesuai template)
    // Preset menggunakan posisi yang sesuai dengan signature-box di template
    const AUTO_SNAP = { xPercent: 70.0, yPercent: 80.0, widthPercent: 20.0 };

	function renderPage(num){
		if(!pdfDoc) return;
		pdfDoc.getPage(num).then(function(page){
			const viewport = page.getViewport({ scale });
			canvas.width = viewport.width;
			canvas.height = viewport.height;
			stage.style.width = viewport.width + 'px';
			stage.style.height = viewport.height + 'px';
			const renderContext = { canvasContext: ctx, viewport };
			page.render(renderContext);
			pageNumEl.textContent = num; pageCountEl.textContent = pdfDoc.numPages;
			updatePlacementInput();
		});
	}
	function queueRenderPage(num){ renderPage(num); }
	function onPrevPage(){ if(pageNum<=1) return; pageNum--; queueRenderPage(pageNum);} 
	function onNextPage(){ if(!pdfDoc||pageNum>=pdfDoc.numPages) return; pageNum++; queueRenderPage(pageNum);} 
	function onZoomIn(){ scale = Math.min(scale+0.1, 3); queueRenderPage(pageNum);} 
	function onZoomOut(){ scale = Math.max(scale-0.1, 0.5); queueRenderPage(pageNum);} 
	function onFitWidth(){ if(!pdfDoc) return; const parentW = stage.parentElement.clientWidth; pdfDoc.getPage(1).then(p=>{const v=p.getViewport({scale:1}); scale = parentW / v.width; queueRenderPage(pageNum);}); }

	document.getElementById('prevPage').addEventListener('click', onPrevPage);
	document.getElementById('nextPage').addEventListener('click', onNextPage);
	document.getElementById('zoomIn').addEventListener('click', onZoomIn);
	document.getElementById('zoomOut').addEventListener('click', onZoomOut);
	document.getElementById('fitWidth').addEventListener('click', onFitWidth);

	if(pdfUrl){
		pdfjsLib.getDocument(pdfUrl).promise.then(function(pdf){ pdfDoc = pdf; pageNum = pdf.numPages; renderPage(pageNum); });
	}

	// Draw/Upload -> image data
	const drawCanvas = document.getElementById('drawCanvas');
	const dctx = drawCanvas.getContext('2d');
	let drawing=false;
	dctx.strokeStyle = '#000'; dctx.lineWidth = 2; dctx.lineCap='round'; dctx.lineJoin='round';
	drawCanvas.addEventListener('mousedown',e=>{drawing=true; dctx.beginPath(); dctx.moveTo(e.offsetX,e.offsetY); refreshFromDraw();});
	drawCanvas.addEventListener('mousemove',e=>{if(!drawing) return; dctx.lineTo(e.offsetX,e.offsetY); dctx.stroke(); refreshFromDraw();});
	drawCanvas.addEventListener('mouseup',()=>{drawing=false; refreshFromDraw();});
	drawCanvas.addEventListener('mouseout',()=>{drawing=false;});
	document.getElementById('clearDraw').addEventListener('click',()=>{dctx.clearRect(0,0,drawCanvas.width,drawCanvas.height); refreshFromDraw();});

	function refreshFromDraw(){
		const data = drawCanvas.toDataURL('image/png');
		draggable.src = data;
		draggable.style.display='block';
		fitSignatureBySlider();
		updateSignatureData();
		updatePlacementInput();
	}

	const uploadInput=document.getElementById('uploadInput');
	uploadInput.addEventListener('change', function(){
		const f=this.files[0]; if(!f) return; const r=new FileReader();
		r.onload=()=>{draggable.src=r.result; draggable.style.display='block'; fitSignatureBySlider(); updateSignatureData(); setInitialPosition();};
		r.readAsDataURL(f);
	});

	// Tabs
	document.getElementById('tabDraw').addEventListener('click',()=>{
		document.getElementById('tabDraw').classList.add('active');
		document.getElementById('tabUpload').classList.remove('active');
		document.getElementById('drawPane').style.display='block';
		document.getElementById('uploadPane').style.display='none';
	});
	document.getElementById('tabUpload').addEventListener('click',()=>{
		document.getElementById('tabUpload').classList.add('active');
		document.getElementById('tabDraw').classList.remove('active');
		document.getElementById('uploadPane').style.display='block';
		document.getElementById('drawPane').style.display='none';
	});

	// Draggable signature
	let offsetX=0, offsetY=0, isDragging=false;
	draggable.addEventListener('dragstart', e=>{ e.preventDefault(); return false; });
	draggable.addEventListener('mousedown',e=>{isDragging=true; offsetX=e.offsetX; offsetY=e.offsetY; e.preventDefault();});
	document.addEventListener('mousemove',e=>{
		if(!isDragging) return; const rect=stage.getBoundingClientRect();
		let x=e.clientX-rect.left-offsetX; let y=e.clientY-rect.top-offsetY;
		x=Math.max(0, Math.min(x, stage.clientWidth-draggable.clientWidth));
		y=Math.max(0, Math.min(y, stage.clientHeight-draggable.clientHeight));
		draggable.style.left=x+'px'; draggable.style.top=y+'px'; updatePlacementInput(); updateCoordsLabel();
	});
	document.addEventListener('mouseup',()=>{isDragging=false;});

	// Klik untuk menaruh di titik yang diklik
	stage.addEventListener('click', (e)=>{
		const rect = stage.getBoundingClientRect();
		const x = e.clientX - rect.left - (draggable.clientWidth/2);
		const y = e.clientY - rect.top  - (draggable.clientHeight/2);
		draggable.style.left = Math.max(0, Math.min(x, stage.clientWidth-draggable.clientWidth)) + 'px';
		draggable.style.top  = Math.max(0, Math.min(y, stage.clientHeight-draggable.clientHeight)) + 'px';
		draggable.style.display='block';
		updatePlacementInput(); updateCoordsLabel();
	});

	// Keyboard nudge
	document.addEventListener('keydown', (e)=>{
		if(draggable.style.display==='none') return;
		const step = e.shiftKey ? 1 : 0.2; // persen
		let left = parseFloat(draggable.style.left)||0;
		let top  = parseFloat(draggable.style.top)||0;
		if(e.key==='ArrowLeft')  left -= stage.clientWidth * (step/100);
		if(e.key==='ArrowRight') left += stage.clientWidth * (step/100);
		if(e.key==='ArrowUp')    top  -= stage.clientHeight * (step/100);
		if(e.key==='ArrowDown')  top  += stage.clientHeight * (step/100);
		left = Math.max(0, Math.min(left, stage.clientWidth-draggable.clientWidth));
		top  = Math.max(0, Math.min(top,  stage.clientHeight-draggable.clientHeight));
		draggable.style.left = left + 'px';
		draggable.style.top  = top  + 'px';
		updatePlacementInput(); updateCoordsLabel();
	});

	// Size slider -> change width
	const slider=document.getElementById('sizeSlider');
	slider.addEventListener('input',()=>{ fitSignatureBySlider(); updatePlacementInput(); updateCoordsLabel();});
	function fitSignatureBySlider(){
		const wPercent=parseFloat(slider.value); if(!stage.clientWidth) return;
		draggable.style.width=(stage.clientWidth*(wPercent/100))+'px';
	}

	function setInitialPosition(){
		if(!pdfDoc) return;
		
		// PERBAIKAN: Posisi initial yang konsisten dengan koordinat dokumen
		pdfDoc.getPage(pageNum).then(function(page) {
			const viewport = page.getViewport({ scale: 1.0 });
			const originalW = viewport.width;
			const originalH = viewport.height;
			
			// Posisi default: 60% horizontal, 20% vertical (area aman)
			const docX = 0.6 * originalW;
			const docY = 0.2 * originalH;
			
			// Konversi ke canvas display
			const currentCanvasW = stage.clientWidth;
			const currentCanvasH = stage.clientHeight;
			const scaleX = currentCanvasW / originalW;
			const scaleY = currentCanvasH / originalH;
			
			const displayX = docX * scaleX - (draggable.clientWidth || 0) / 2;
			const displayY = docY * scaleY;
			
			draggable.style.left = Math.max(0, displayX) + 'px';
			draggable.style.top = Math.max(0, displayY) + 'px';
			
			updatePlacementInput(); 
			updateCoordsLabel();
		});
	}

	function updateSignatureData(){
		// ambil gambar dari drawCanvas jika aktif
		if(document.getElementById('tabDraw').classList.contains('active')){
			document.getElementById('signatureData').value = drawCanvas.toDataURL('image/png');
		}else{
			// untuk upload, gunakan src gambar sebagai signature_data
			document.getElementById('signatureData').value = draggable.src || '';
		}
	}

	function updatePlacementInput(){
		if(draggable.style.display==='none'){ document.getElementById('placementInput').value=''; return; }
		
		// PERBAIKAN: Gunakan dimensi PDF dokumen asli (unscaled) untuk akurasi koordinat
		if(!pdfDoc) { document.getElementById('placementInput').value=''; return; }
		
		pdfDoc.getPage(pageNum).then(function(page) {
			const viewport = page.getViewport({ scale: 1.0 }); // Scale 1.0 = ukuran dokumen asli
			const originalW = viewport.width;
			const originalH = viewport.height;
			
			// Konversi posisi dari canvas scaled ke dokumen original
			const currentCanvasW = stage.clientWidth;
			const currentCanvasH = stage.clientHeight;
			
			// Rasio untuk konversi dari tampilan ke dokumen asli
			const scaleX = originalW / currentCanvasW;
			const scaleY = originalH / currentCanvasH;
			
			// Koordinat dalam dokumen asli
			const docX = (parseFloat(draggable.style.left) || 0) * scaleX;
			const docY = (parseFloat(draggable.style.top) || 0) * scaleY;
			const docW = draggable.clientWidth * scaleX;
			const docH = draggable.clientHeight * scaleY;
			
			// Perhitungan persentase berdasarkan ukuran dokumen asli
			const xPercent = (docX / originalW) * 100;
			const yPercent = (docY / originalH) * 100;
			const wPercent = (docW / originalW) * 100;
			const hPercent = (docH / originalH) * 100;
			
			document.getElementById('placementInput').value = JSON.stringify({ 
				page: pageNum, 
				x_percent: +xPercent.toFixed(4), 
				y_percent: +yPercent.toFixed(4), 
				width_percent: +wPercent.toFixed(4), 
				height_percent: +hPercent.toFixed(4) 
			});
		});
	}

	function updateCoordsLabel(){
		if(!coordsEl || !pdfDoc) return;
		
		// PERBAIKAN: Gunakan dimensi PDF dokumen asli untuk label koordinat yang akurat
		pdfDoc.getPage(pageNum).then(function(page) {
			const viewport = page.getViewport({ scale: 1.0 }); // Scale 1.0 = ukuran dokumen asli
			const originalW = viewport.width;
			const originalH = viewport.height;
			
			// Konversi posisi dari canvas scaled ke dokumen original
			const currentCanvasW = stage.clientWidth;
			const currentCanvasH = stage.clientHeight;
			
			// Rasio untuk konversi dari tampilan ke dokumen asli
			const scaleX = originalW / currentCanvasW;
			const scaleY = originalH / currentCanvasH;
			
			// Koordinat dalam dokumen asli
			const docX = (parseFloat(draggable.style.left) || 0) * scaleX;
			const docY = (parseFloat(draggable.style.top) || 0) * scaleY;
			const docW = draggable.clientWidth * scaleX;
			
			// Perhitungan persentase berdasarkan ukuran dokumen asli
			const xPercent = (docX / originalW) * 100;
			const yPercent = (docY / originalH) * 100;
			const wPercent = (docW / originalW) * 100;
			
		coordsEl.textContent = `X: ${xPercent.toFixed(2)}% | Y: ${yPercent.toFixed(2)}% | W: ${wPercent.toFixed(2)}%`;
		});
	}

	// Pasang Otomatis
	document.getElementById('autoSnapBtn').addEventListener('click', ()=>{
		setTimeout(()=>{
			if(!pdfDoc) return;
			
			// PERBAIKAN: Konversi dari persentase dokumen PDF ke posisi canvas display
			pdfDoc.getPage(pageNum).then(function(page) {
				const viewport = page.getViewport({ scale: 1.0 }); // Scale 1.0 = ukuran dokumen asli
				const originalW = viewport.width;
				const originalH = viewport.height;
				
				// Konversi persentase preset ke koordinat dokumen asli
				const docX = (AUTO_SNAP.xPercent / 100) * originalW;
				const docY = (AUTO_SNAP.yPercent / 100) * originalH;
				const docW = (AUTO_SNAP.widthPercent / 100) * originalW;
				
				// Konversi dari dokumen asli ke canvas display
				const currentCanvasW = stage.clientWidth;
				const currentCanvasH = stage.clientHeight;
				const scaleX = currentCanvasW / originalW;
				const scaleY = currentCanvasH / originalH;
				
				const displayX = docX * scaleX;
				const displayY = docY * scaleY;
				const displayW = docW * scaleX;
				
				// Set posisi dan ukuran pada canvas display
				draggable.style.width = displayW + 'px';
				draggable.style.left = Math.max(0, Math.min(displayX, stage.clientWidth - displayW)) + 'px';
				draggable.style.top = Math.max(0, Math.min(displayY, stage.clientHeight - (displayW * 0.4))) + 'px';
				draggable.style.display = 'block';
				
				updatePlacementInput(); 
				updateCoordsLabel();
			});
		}, 200);
	});

	// Reset posisi
	document.getElementById('resetPosBtn').addEventListener('click', ()=>{
		fitSignatureBySlider(); setInitialPosition(); updatePlacementInput(); updateCoordsLabel();
	});

	// Inisialisasi
	setTimeout(()=>{draggable.src = drawCanvas.toDataURL('image/png'); draggable.style.display='block'; fitSignatureBySlider(); setInitialPosition(); updateSignatureData(); updatePlacementInput();}, 300);

	// Submit
	document.getElementById('signatureForm').addEventListener('submit', function(){ updateSignatureData(); updatePlacementInput(); });
})();
</script>
@endsection