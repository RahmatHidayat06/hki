@extends('layouts.app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<!-- Header -->
			<div class="card border-0 shadow-sm mb-4">
				<div class="card-body">
					<div class="d-flex justify-content-between align-items-center">
						<div>
							<h4 class="mb-2 text-primary">
								<i class="fas fa-signature me-2"></i>Kelola Tanda Tangan
							</h4>
							<p class="mb-0 text-muted">
								<strong>{{ $pengajuan->judul_karya }}</strong><br>
								Nomor: {{ $pengajuan->nomor_pengajuan ?? 'Belum ada nomor' }}
							</p>
						</div>
						<div>
							<a href="{{ route('tracking.show', $pengajuan->id) }}" class="btn btn-outline-primary">
								<i class="fas fa-route me-2"></i>Lihat Tracking
							</a>
						</div>
					</div>
				</div>
			</div>

			<!-- Progress Overview -->
			<div class="card border-0 shadow-sm mb-4">
				<div class="card-body">
					<div class="row">
						<div class="col-md-8">
							<h6 class="text-muted mb-3">Progress Tanda Tangan</h6>
							<div class="progress" style="height: 12px;">
								<div class="progress-bar bg-success" 
									role="progressbar" 
									style="width: {{ $pengajuan->getSignatureProgress() }}%"
									aria-valuenow="{{ $pengajuan->getSignatureProgress() }}" 
									aria-valuemin="0" 
									aria-valuemax="100">
								</div>
							</div>
						</div>
						<div class="col-md-4 text-end">
							<div class="badge bg-primary fs-6 px-3 py-2">
								{{ $pengajuan->signatures->where('status', 'signed')->count() }}/{{ $pengajuan->signatures->count() }} Lengkap
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Signatures List -->
			<div class="row">
				@foreach($pengajuan->signatures->sortBy('pencipta_ke') as $signature)
				<div class="col-lg-6 mb-4">
					<div class="card border-0 shadow-sm h-100">
						<div class="card-header bg-light border-0">
							<div class="d-flex justify-content-between align-items-center">
								<h6 class="mb-0">
									<i class="fas fa-user me-2"></i>Pencipta {{ $signature->pencipta_ke }}
								</h6>
								<span class="badge bg-{{ $signature->status === 'signed' ? 'success' : 'warning' }}">
									{{ $signature->status === 'signed' ? 'Sudah Ditandatangani' : 'Menunggu' }}
								</span>
							</div>
						</div>
						<div class="card-body">
							<div class="mb-3">
								<label class="form-label small text-muted">Nama Pencipta</label>
								<p class="mb-1 fw-medium">{{ $signature->nama_pencipta }}</p>
							</div>
							
							<div class="mb-3">
								<label class="form-label small text-muted">Nama di Tanda Tangan</label>
								<p class="mb-1">{{ $signature->nama_ttd }}</p>
							</div>

							@if($signature->email_pencipta)
							<div class="mb-3">
								<label class="form-label small text-muted">Email</label>
								<p class="mb-1">{{ $signature->email_pencipta }}</p>
							</div>
							@endif

							<div class="mb-3">
								<label class="form-label small text-muted">Posisi Tanda Tangan</label>
								<p class="mb-1">{{ ucfirst($signature->posisi) }}</p>
							</div>

							@if($signature->status === 'signed')
							<div class="mb-3">
								<label class="form-label small text-muted">Ditandatangani</label>
								<p class="mb-1 text-success">
									<i class="fas fa-check-circle me-1"></i>
									{{ $signature->signed_at->format('d/m/Y H:i') }}
								</p>
								@if($signature->signedBy)
								<small class="text-muted">oleh {{ $signature->signedBy->nama_lengkap }}</small>
								@endif
							</div>

							@if($signature->signature_path)
							<div class="mb-3">
								<a href="{{ Storage::url($signature->signature_path) }}" 
								   target="_blank" 
								   class="btn btn-sm btn-outline-primary">
									<i class="fas fa-eye me-1"></i>Lihat Tanda Tangan
								</a>
							</div>
							@endif
							@endif
						</div>
						<div class="card-footer bg-transparent border-0">
							<div class="d-flex gap-2 flex-wrap">
								@if($signature->status === 'pending')
								<button type="button" 
										class="btn btn-primary btn-sm"
										onclick="copySignatureLink('{{ route('signatures.sign', $signature->signature_token) }}')">
									<i class="fas fa-copy me-1"></i>Copy Link Tanda Tangan
								</button>
								<button type="button" 
										class="btn btn-success btn-sm"
										onclick="sendWaInvite('{{ route('signatures.sign', $signature->signature_token) }}', '{{ $signature->nama_pencipta }}', '{{ $pengajuan->judul_karya }}')">
									<i class="fab fa-whatsapp me-1"></i>Kirim via WA
								</button>
								<button type="button" 
										class="btn btn-outline-dark btn-sm"
										onclick="showQrInvite('{{ route('signatures.sign', $signature->signature_token) }}')">
									<i class="fas fa-qrcode me-1"></i>QR
								</button>
								
								@if($signature->email_pencipta)
								<button type="button" 
										class="btn btn-outline-primary btn-sm"
										onclick="sendReminder({{ $signature->id }})">
									<i class="fas fa-envelope me-1"></i>Kirim Reminder
								</button>
								@endif
								@endif

								@if(auth()->user()->isAdmin())
								<button type="button" 
										class="btn btn-outline-warning btn-sm"
										onclick="resetSignature({{ $signature->id }})">
									<i class="fas fa-undo me-1"></i>Reset
								</button>
								@endif
							</div>
						</div>
					</div>
				</div>
				@endforeach
			</div>

			<!-- Action Buttons -->
			<div class="row mt-4">
				<div class="col-12">
					<div class="d-flex justify-content-between">
						<a href="{{ route('pengajuan.show', $pengajuan->id) }}" class="btn btn-secondary">
							<i class="fas fa-arrow-left me-2"></i>Kembali ke Detail
						</a>
						
						@if($pengajuan->allSignaturesSigned())
						<div>
							<a href="{{ route('signatures.preview-document', $pengajuan->id) }}" class="btn btn-success">
								<i class="fas fa-file-pdf me-2"></i>Preview Dokumen Final
							</a>
						</div>
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Toast untuk notifikasi -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
	<div id="signatureToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
		<div class="toast-header">
			<i class="fas fa-info-circle text-primary me-2"></i>
			<strong class="me-auto">Notifikasi</strong>
			<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
		</div>
		<div class="toast-body">
			<!-- Message akan diisi via JavaScript -->
		</div>
	</div>
</div>

<!-- Modal QR Invite -->
<div class="modal fade" id="qrInviteModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><i class="fas fa-qrcode me-2"></i>QR Undangan Tanda Tangan</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body text-center">
				<div id="qrContainer" class="d-inline-block"></div>
				<p class="mt-3 small text-muted" id="qrLinkText"></p>
				<button type="button" class="btn btn-sm btn-outline-primary" id="copyQrLinkBtn"><i class="fas fa-copy me-1"></i>Copy Link</button>
			</div>
		</div>
	</div>
</div>

<script>
function copySignatureLink(url) {
	navigator.clipboard.writeText(url).then(function() {
		showToast('Link tanda tangan berhasil disalin ke clipboard!');
	}).catch(function(err) {
		console.error('Could not copy text: ', err);
		// Fallback untuk browser lama
		const textArea = document.createElement('textarea');
		textArea.value = url;
		document.body.appendChild(textArea);
		textArea.select();
		document.execCommand('copy');
		document.body.removeChild(textArea);
		showToast('Link tanda tangan berhasil disalin!');
	});
}

function sendReminder(signatureId) {
	fetch(`/signatures/${signatureId}/reminder`, {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
			'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
		}
	})
	.then(response => response.json())
	.then(data => {
		showToast(data.message);
	})
	.catch(error => {
		console.error('Error:', error);
		showToast('Terjadi kesalahan saat mengirim reminder', 'error');
	});
}

function resetSignature(signatureId) {
	if (confirm('Apakah Anda yakin ingin mereset tanda tangan ini?')) {
		fetch(`/signatures/${signatureId}/reset`, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
			}
		})
		.then(response => response.json())
		.then(data => {
			showToast(data.message);
			setTimeout(() => {
				window.location.reload();
			}, 1500);
		})
		.catch(error => {
			console.error('Error:', error);
			showToast('Terjadi kesalahan saat mereset tanda tangan', 'error');
		});
	}
}

function showToast(message, type = 'success') {
	const toastEl = document.getElementById('signatureToast');
	const toastBody = toastEl.querySelector('.toast-body');
	const toastIcon = toastEl.querySelector('.toast-header i');
	
	toastBody.textContent = message;
	
	// Update icon berdasarkan type
	toastIcon.className = type === 'error' 
		? 'fas fa-exclamation-circle text-danger me-2' 
		: 'fas fa-check-circle text-success me-2';
	
	const toast = new bootstrap.Toast(toastEl);
	toast.show();
}

// Auto refresh progress setiap 10 detik
setInterval(function() {
	fetch(`{{ route('signatures.progress', $pengajuan->id) }}`)
		.then(response => response.json())
		.then(data => {
			// Update progress bar
			const progressBar = document.querySelector('.progress-bar');
			if (progressBar) {
				progressBar.style.width = data.progress + '%';
				progressBar.setAttribute('aria-valuenow', data.progress);
			}
			
			// Update badge
			const badge = document.querySelector('.badge.bg-primary');
			if (badge) {
				badge.textContent = `${data.signed}/${data.total} Lengkap`;
			}
		})
		.catch(error => console.error('Error updating progress:', error));
}, 10000);

// --- Undangan via WhatsApp ---
function sendWaInvite(url, nama, judul) {
	const text = `Halo ${nama}, mohon upload KTP dan tanda tangan Surat Pengalihan \"${judul}\" melalui tautan berikut: ${url}`;
	const waUrl = `https://wa.me/?text=${encodeURIComponent(text)}`;
	window.open(waUrl, '_blank');
}

// --- QR Undangan ---
function showQrInvite(url) {
	const modalEl = document.getElementById('qrInviteModal');
	const qrContainer = document.getElementById('qrContainer');
	qrContainer.innerHTML = '';
	// Gunakan QRCode.js via CDN jika tersedia; fallback ke API chart
	if (window.QRCode) {
		new QRCode(qrContainer, { text: url, width: 256, height: 256 });
	} else {
		const img = document.createElement('img');
		img.src = `https://chart.googleapis.com/chart?chs=256x256&cht=qr&chl=${encodeURIComponent(url)}&choe=UTF-8`;
		img.alt = 'QR Undangan';
		qrContainer.appendChild(img);
	}
	document.getElementById('qrLinkText').textContent = url;
	document.getElementById('copyQrLinkBtn').onclick = function(){ copySignatureLink(url); };
	new bootstrap.Modal(modalEl).show();
}
</script>

<!-- QRCode.js CDN (ringan) -->
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
@endsection 