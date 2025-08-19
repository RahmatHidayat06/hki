@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
		<div class="col-lg-12">
            <!-- Header Info -->
            <div class="card border-0 shadow-sm mb-4">
				<div class="card-body d-flex justify-content-between align-items-center">
					<div>
						<h4 class="text-primary mb-1">
                        <i class="fas fa-signature me-2"></i>Tanda Tangan Digital
                    </h4>
						<div class="small text-muted">
							<strong>Pengajuan:</strong> {{ $pengajuan->judul_karya }} &nbsp; | &nbsp;
							<strong>Nomor:</strong> {{ $pengajuan->nomor_pengajuan ?? 'Belum ada nomor' }} &nbsp; | &nbsp;
							<strong>Pencipta ke:</strong> {{ $signature->pencipta_ke }} &nbsp; | &nbsp;
							<strong>Nama:</strong> {{ $signature->nama_pencipta }}
						</div>
					</div>
					<div class="d-flex align-items-center gap-2">
						<button id="btnOpenNewTab" class="btn btn-outline-primary btn-sm"><i class="fas fa-external-link-alt me-1"></i>Buka di Tab Baru</button>
					</div>
				</div>
			</div>

			<div class="row g-3">
				<!-- LEFT: PDF Viewer -->
				<div class="col-lg-8">
					<div class="card border-0 shadow-sm h-100">
						<div class="card-header bg-light d-flex justify-content-between align-items-center">
							<div class="d-flex align-items-center gap-2">
								<button id="prevPage" class="btn btn-sm btn-outline-secondary"><i class="fas fa-chevron-left"></i></button>
								<span class="small">Halaman <span id="pageNum">1</span> dari <span id="pageCount">1</span></span>
								<button id="nextPage" class="btn btn-sm btn-outline-secondary"><i class="fas fa-chevron-right"></i></button>
							</div>
							<div class="d-flex align-items-center gap-2">
								<button id="zoomOut" class="btn btn-sm btn-outline-secondary"><i class="fas fa-search-minus"></i></button>
								<button id="zoomIn" class="btn btn-sm btn-outline-secondary"><i class="fas fa-search-plus"></i></button>
								<button id="fitWidth" class="btn btn-sm btn-outline-secondary"><i class="fas fa-expand-arrows-alt"></i> Fit Width</button>
							</div>
                        </div>
						<div class="card-body p-2">
							<div id="pdfStage" class="position-relative mx-auto" style="width: 100%; max-width: 850px;">
								<canvas id="pdfCanvas" style="width:100%; height:auto; display:block; background:#fff; border:1px solid #e9ecef; border-radius:6px;"></canvas>
								<!-- draggable signature on top of PDF -->
								<img id="draggableSignature" alt="Tanda tangan" style="position:absolute; left:20px; top:80px; width:20%; display:none; cursor:move; user-select:none;">
                        </div>
                    </div>
                </div>
            </div>

				<!-- RIGHT: Controls panel -->
				<div class="col-lg-4">
					<div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-light">
							<h6 class="mb-0"><i class="fas fa-id-card me-2"></i>Upload KTP</h6>
                </div>
                <div class="card-body">
                    <form id="signatureForm" enctype="multipart/form-data">
                        @csrf
								<input type="file" class="form-control" id="ktpFile" name="ktp_file" accept="image/*" required>
								<div class="form-text">Format: JPG, PNG (Max: 5MB)</div>
								<div id="ktpPreview" class="mt-2" style="display: none;">
									<img id="ktpImage" src="" class="img-thumbnail" style="max-height: 160px;">
									<button type="button" class="btn btn-sm btn-outline-danger mt-1" onclick="removeKtpFile()"><i class="fas fa-trash me-1"></i>Hapus</button>
                            </div>
                            </div>
                        </div>

					<div class="card border-0 shadow-sm mb-3">
						<div class="card-header bg-light d-flex justify-content-between align-items-center">
							<h6 class="mb-0"><i class="fas fa-pen-nib me-2"></i>Buat Tanda Tangan</h6>
							<div class="d-flex align-items-center gap-2">
								<small class="text-muted">Ukuran</small>
								<input type="range" id="sizeRange" min="15" max="45" value="25">
							</div>
						</div>
						<div class="card-body">
                            <ul class="nav nav-pills nav-fill mb-3" id="signatureMethodTabs" role="tablist">
                                <li class="nav-item" role="presentation">
									<button class="nav-link active" id="canvas-tab" data-bs-toggle="pill" data-bs-target="#canvas-signature" type="button" role="tab"><i class="fas fa-pencil-alt me-1"></i>Gambar</button>
                                </li>
                                <li class="nav-item" role="presentation">
									<button class="nav-link" id="upload-tab" data-bs-toggle="pill" data-bs-target="#upload-signature" type="button" role="tab"><i class="fas fa-upload me-1"></i>Upload</button>
                                </li>
                            </ul>
							<div class="tab-content">
								<div class="tab-pane fade show active" id="canvas-signature">
									<canvas id="signatureCanvas" width="600" height="220" style="border:1px dashed #ced4da; width:100%;"></canvas>
                            <div class="d-flex justify-content-between mt-2">
										<small class="text-muted">Gambar tanda tangan Anda lalu tempel pada PDF</small>
										<button type="button" id="clearCanvas" class="btn btn-sm btn-outline-secondary"><i class="fas fa-eraser me-1"></i>Bersihkan</button>
                            </div>
                                </div>
								<div class="tab-pane fade" id="upload-signature">
									<input type="file" class="form-control" id="signatureFile" name="signature_file" accept="image/*">
									<div class="form-text">PNG transparan disarankan</div>
									<div id="signatureImagePreview" class="mt-2" style="display:none;">
										<img id="signatureImageDisplay" class="img-thumbnail" style="max-height:120px;">
										<button type="button" class="btn btn-sm btn-outline-danger mt-1" onclick="removeSignatureFile()"><i class="fas fa-trash me-1"></i>Hapus</button>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="signatureMethod" name="signature_method" value="canvas">
							<input type="hidden" id="placementInput" name="placement">
							<div class="mt-3">
                            <div class="form-check">
									<input class="form-check-input" type="checkbox" id="agreeTerms" required>
									<label class="form-check-label small" for="agreeTerms">Saya menyetujui keabsahan tanda tangan digital ini.</label>
                            </div>
                        </div>
							<div class="d-grid mt-3">
								<button type="submit" id="submitSignature" class="btn btn-primary" disabled>
                                <span id="submitSpinner" class="spinner-border spinner-border-sm me-2 d-none"></span>
									<i class="fas fa-save me-1"></i>Simpan & Kirim
                            </button>
                        </div>
                    </form>
                </div>
            </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
				<h5 class="modal-title text-success"><i class="fas fa-check-circle me-2"></i>Tanda Tangan Berhasil Disimpan</h5>
            </div>
            <div class="modal-body text-center">
                <p class="mb-3">Terima kasih! Tanda tangan Anda telah berhasil disimpan.</p>
                <p class="text-muted small">Anda akan dialihkan dalam beberapa detik...</p>
            </div>
        </div>
    </div>
</div>

<style>
.drawing { border-color:#198754 !important; }
</style>

@php
	$dokumen = is_string($pengajuan->file_dokumen_pendukung) ? json_decode($pengajuan->file_dokumen_pendukung, true) : ($pengajuan->file_dokumen_pendukung ?? []);
	$pdfPath = $dokumen['surat_pengalihan'] ?? null;
	$pdfUrl = $pdfPath ? Storage::url($pdfPath) : '';
@endphp

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
	// ====== PDF.js setup ======
	const url = @json($pdfUrl);
	if (!url) {
		alert('Dokumen Surat Pengalihan belum tersedia.');
		return;
	}
	const pdfjsLib = window['pdfjs-dist/build/pdf'];
	pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
	const canvas = document.getElementById('pdfCanvas');
    const ctx = canvas.getContext('2d');
	const pageNumSpan = document.getElementById('pageNum');
	const pageCountSpan = document.getElementById('pageCount');
	let pdfDoc = null, pageNum = {{ $signature->pencipta_ke === 1 ? 1 : 2 }}, scale = 1.2, rendering = false;

	function renderPage(num){
		rendering = true;
		pdfDoc.getPage(num).then(function(page){
			const viewport = page.getViewport({ scale });
			canvas.height = viewport.height;
			canvas.width = viewport.width;
			const renderContext = { canvasContext: ctx, viewport };
			const renderTask = page.render(renderContext);
			renderTask.promise.then(function(){
				rendering = false;
				pageNumSpan.textContent = num;
			});
		});
	}
	pdfjsLib.getDocument(url).promise.then(function(pdf){
		pdfDoc = pdf;
		pageCountSpan.textContent = pdf.numPages;
		if (pageNum > pdf.numPages) pageNum = 1;
		renderPage(pageNum);
	});
	// Controls
	document.getElementById('prevPage').addEventListener('click', ()=>{ if(!pdfDoc||rendering||pageNum<=1) return; pageNum--; renderPage(pageNum); updatePlacementInput(); });
	document.getElementById('nextPage').addEventListener('click', ()=>{ if(!pdfDoc||rendering||pageNum>=pdfDoc.numPages) return; pageNum++; renderPage(pageNum); updatePlacementInput(); });
	document.getElementById('zoomIn').addEventListener('click', ()=>{ if(!pdfDoc) return; scale = Math.min(scale+0.1, 2.0); renderPage(pageNum); setTimeout(updatePlacementInput, 50); });
	document.getElementById('zoomOut').addEventListener('click', ()=>{ if(!pdfDoc) return; scale = Math.max(scale-0.1, 0.6); renderPage(pageNum); setTimeout(updatePlacementInput, 50); });
	document.getElementById('fitWidth').addEventListener('click', ()=>{ if(!pdfDoc) return; const stage = document.getElementById('pdfStage'); const target = stage.clientWidth; pdfDoc.getPage(pageNum).then(p=>{ const vp = p.getViewport({ scale:1 }); scale = target / vp.width; renderPage(pageNum); setTimeout(updatePlacementInput, 50); }); });
	document.getElementById('btnOpenNewTab').addEventListener('click', ()=> window.open(url,'_blank'));

	// ====== Signature draw/upload -> draggable overlay ======
	const drawCanvas = document.getElementById('signatureCanvas');
	const drawCtx = drawCanvas.getContext('2d');
	let drawing = false; let hasSignature = false; let dragState = { dragging:false, offsetX:0, offsetY:0 };
	const draggable = document.getElementById('draggableSignature');
	const sizeRange = document.getElementById('sizeRange');
	const placementInput = document.getElementById('placementInput');
    const agreeCheckbox = document.getElementById('agreeTerms');
    const submitBtn = document.getElementById('submitSignature');

	function updateSubmit(){
		const ktpReady = !!document.getElementById('ktpFile').files.length;
		const method = document.getElementById('signatureMethod').value;
		const uploadReady = method==='upload' ? !!document.getElementById('signatureFile').files.length : hasSignature;
		const placementReady = !!placementInput.value;
		submitBtn.disabled = !(ktpReady && uploadReady && placementReady && agreeCheckbox.checked);
	}

	// Draw
	drawCanvas.addEventListener('mousedown', e=>{ drawing=true; drawCtx.beginPath(); const r=drawCanvas.getBoundingClientRect(); drawCtx.moveTo(e.clientX-r.left, e.clientY-r.top); });
	drawCanvas.addEventListener('mousemove', e=>{ if(!drawing) return; drawCtx.lineWidth=2; drawCtx.lineCap='round'; drawCtx.strokeStyle='#000'; const r=drawCanvas.getBoundingClientRect(); drawCtx.lineTo(e.clientX-r.left, e.clientY-r.top); drawCtx.stroke(); hasSignature=true; showOverlayFromCanvas(); updateSubmit(); });
	drawCanvas.addEventListener('mouseup', ()=> drawing=false);
	drawCanvas.addEventListener('mouseleave', ()=> drawing=false);
	document.getElementById('clearCanvas').addEventListener('click', ()=>{ drawCtx.clearRect(0,0,drawCanvas.width,drawCanvas.height); hasSignature=false; draggable.style.display='none'; placementInput.value=''; updateSubmit(); });

	function showOverlayFromCanvas(){ const dataUrl = drawCanvas.toDataURL('image/png'); draggable.src = dataUrl; draggable.style.display='block'; applySize(); centerVertically(); updatePlacementInput(); }
	function centerVertically(){ const stage = document.getElementById('pdfStage'); draggable.style.left = '20px'; draggable.style.top = Math.max(0, (stage.clientHeight - draggable.clientHeight)/2) + 'px'; }
	function applySize(){ draggable.style.width = sizeRange.value + '%'; }
	sizeRange.addEventListener('input', ()=>{ applySize(); updatePlacementInput(); updateSubmit(); });

	// Upload signature image
	document.getElementById('signatureFile').addEventListener('change', function(){ const f=this.files[0]; if(!f) return; const rd=new FileReader(); rd.onload=function(e){ document.getElementById('signatureImageDisplay').src=e.target.result; document.getElementById('signatureImagePreview').style.display='block'; draggable.src=e.target.result; draggable.style.display='block'; hasSignature=true; applySize(); centerVertically(); updatePlacementInput(); updateSubmit(); }; rd.readAsDataURL(f); });

	// Switch tabs
	document.querySelectorAll('#signatureMethodTabs button').forEach(b=> b.addEventListener('click', function(){ const method=this.id==='canvas-tab'?'canvas':'upload'; document.getElementById('signatureMethod').value=method; updateSubmit(); }));

	// Drag overlay on top of PDF
	draggable.addEventListener('mousedown', (e)=>{ dragState.dragging=true; const r=draggable.getBoundingClientRect(); dragState.offsetX = e.clientX - r.left; dragState.offsetY = e.clientY - r.top; });
	document.addEventListener('mousemove', (e)=>{ if(!dragState.dragging) return; const stage = document.getElementById('pdfStage').getBoundingClientRect(); let x = e.clientX - stage.left - dragState.offsetX; let y = e.clientY - stage.top - dragState.offsetY; const img = document.getElementById('draggableSignature'); const stageEl = document.getElementById('pdfStage'); x = Math.max(0, Math.min(x, stageEl.clientWidth - img.clientWidth)); y = Math.max(0, Math.min(y, stageEl.clientHeight - img.clientHeight)); img.style.left = x + 'px'; img.style.top = y + 'px'; updatePlacementInput(); updateSubmit(); });
	document.addEventListener('mouseup', ()=>{ dragState.dragging=false; });

	function updatePlacementInput(){ const stageEl=document.getElementById('pdfStage'); if(draggable.style.display==='none') { placementInput.value=''; return; } const xPercent = (parseFloat(draggable.style.left)||0)/stageEl.clientWidth*100; const yPercent = (parseFloat(draggable.style.top)||0)/stageEl.clientHeight*100; const wPercent = (draggable.clientWidth/stageEl.clientWidth)*100; const hPercent = (draggable.clientHeight/stageEl.clientHeight)*100; placementInput.value = JSON.stringify({ page: pageNum, x_percent:+xPercent.toFixed(2), y_percent:+yPercent.toFixed(2), width_percent:+wPercent.toFixed(2), height_percent:+hPercent.toFixed(2) }); }

	// KTP preview
	document.getElementById('ktpFile').addEventListener('change', function(){ const f=this.files[0]; if(!f) return; const rd=new FileReader(); rd.onload=function(e){ document.getElementById('ktpImage').src=e.target.result; document.getElementById('ktpPreview').style.display='block'; }; rd.readAsDataURL(f); updateSubmit(); });
	window.removeKtpFile = function(){ document.getElementById('ktpFile').value=''; document.getElementById('ktpPreview').style.display='none'; updateSubmit(); }
	window.removeSignatureFile = function(){ document.getElementById('signatureFile').value=''; document.getElementById('signatureImagePreview').style.display='none'; draggable.style.display='none'; placementInput.value=''; hasSignature=false; updateSubmit(); }

	// Form submit (same endpoint)
	const form = document.getElementById('signatureForm');
	form.addEventListener('submit', function(e){
        e.preventDefault();
		if (submitBtn.disabled) return;
		document.getElementById('submitSpinner').classList.remove('d-none');
		submitBtn.disabled = true; submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
        const method = document.getElementById('signatureMethod').value;
		const fd = new FormData();
		fd.append('_token', document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content'));
		fd.append('ktp_file', document.getElementById('ktpFile').files[0]);
		fd.append('signature_method', method);
		if (method==='canvas'){ fd.append('signature_data', drawCanvas.toDataURL('image/png')); } else { fd.append('signature_file', document.getElementById('signatureFile').files[0]); }
		fd.append('signed_by_name', @json($signature->nama_ttd));
		fd.append('placement', document.getElementById('placementInput').value);
		fetch(@json(route('signatures.save', $signature->signature_token)), { method:'POST', headers:{ 'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content') }, body: fd })
		.then(async (res)=>{ const t=await res.text(); let d; try{ d=JSON.parse(t);}catch(e){ throw new Error('Terjadi kesalahan di server.'); } if(!res.ok){ throw new Error(d.message||'Error'); } return d; })
		.then(d=>{ const m=new bootstrap.Modal(document.getElementById('successModal')); m.show(); setTimeout(()=>{ window.location.href = d.redirect || @json(route('pengajuan.index')); }, 2500); })
		.catch(err=>{ alert(err.message||'Gagal menyimpan.'); document.getElementById('submitSpinner').classList.add('d-none'); submitBtn.disabled=false; submitBtn.innerHTML='<i class="fas fa-save me-1"></i>Simpan & Kirim'; })
	});

	// Init
	updateSubmit();
});
</script>
@endsection 