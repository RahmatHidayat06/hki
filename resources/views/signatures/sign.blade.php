@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center">
                    <h4 class="text-primary mb-3">
                        <i class="fas fa-signature me-2"></i>Tanda Tangan Digital
                    </h4>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Pengajuan:</strong> {{ $pengajuan->judul_karya }}</p>
                            <p class="mb-1"><strong>Nomor:</strong> {{ $pengajuan->nomor_pengajuan ?? 'Belum ada nomor' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Pencipta ke:</strong> {{ $signature->pencipta_ke }}</p>
                            <p class="mb-1"><strong>Nama:</strong> {{ $signature->nama_pencipta }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Signature Form -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-pen me-2"></i>Buat Tanda Tangan Anda
                    </h6>
                </div>
                <div class="card-body">
                    <form id="signatureForm" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Upload KTP -->
                        <div class="mb-4">
                            <label class="form-label">Upload KTP <span class="text-danger">*</span></label>
                            <input type="file" 
                                   class="form-control" 
                                   id="ktpFile" 
                                   name="ktp_file" 
                                   accept="image/*" 
                                   required>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Upload foto/scan KTP Anda. Format: JPG, PNG (Max: 5MB)
                            </div>
                            <div id="ktpPreview" class="mt-2" style="display: none;">
                                <img id="ktpImage" src="" class="img-thumbnail" style="max-height: 200px;">
                                <button type="button" class="btn btn-sm btn-outline-danger mt-1" onclick="removeKtpFile()">
                                    <i class="fas fa-trash me-1"></i>Hapus
                                </button>
                            </div>
                        </div>

                        <!-- Pilihan Metode Tanda Tangan -->
                        <div class="mb-4">
                            <label class="form-label">Metode Tanda Tangan <span class="text-danger">*</span></label>
                            
                            <!-- Navigation Pills -->
                            <ul class="nav nav-pills nav-fill mb-3" id="signatureMethodTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="canvas-tab" data-bs-toggle="pill" data-bs-target="#canvas-signature" type="button" role="tab">
                                        <i class="fas fa-pencil-alt me-1"></i>Gambar Digital
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="upload-tab" data-bs-toggle="pill" data-bs-target="#upload-signature" type="button" role="tab">
                                        <i class="fas fa-upload me-1"></i>Upload Gambar
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content" id="signatureMethodContent">
                                <!-- Canvas Signature -->
                                <div class="tab-pane fade show active" id="canvas-signature" role="tabpanel">
                            <div class="border rounded" style="background: #f8f9fa;">
                                <canvas id="signatureCanvas" 
                                        width="700" 
                                        height="300" 
                                        style="cursor: crosshair; width: 100%; max-width: 700px; height: 300px;">
                                    Browser Anda tidak mendukung HTML5 Canvas
                                </canvas>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <small class="text-muted">Gambar tanda tangan Anda di area di atas</small>
                                <button type="button" id="clearCanvas" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-eraser me-1"></i>Hapus
                                </button>
                            </div>
                                </div>

                                <!-- Upload Signature -->
                                <div class="tab-pane fade" id="upload-signature" role="tabpanel">
                                    <input type="file" 
                                           class="form-control" 
                                           id="signatureFile" 
                                           name="signature_file" 
                                           accept="image/*">
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Upload gambar tanda tangan Anda. Format: JPG, PNG (Max: 2MB)
                                        <br>Background transparan disarankan untuk hasil terbaik
                                    </div>
                                    <div id="signatureImagePreview" class="mt-2" style="display: none;">
                                        <img id="signatureImageDisplay" src="" class="img-thumbnail" style="max-height: 150px;">
                                        <button type="button" class="btn btn-sm btn-outline-danger mt-1" onclick="removeSignatureFile()">
                                            <i class="fas fa-trash me-1"></i>Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" id="signatureMethod" name="signature_method" value="canvas">
                        </div>

                        <!-- Konfirmasi nama -->
                        <div class="mb-4">
                            <label class="form-label">Konfirmasi Nama Lengkap</label>
                            <input type="text" 
                                   class="form-control" 
                                   value="{{ $signature->nama_ttd }}" 
                                   readonly>
                            <div class="form-text">Nama ini akan muncul di bawah tanda tangan Anda</div>
                        </div>

                        <!-- Persetujuan -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="agreeTerms" 
                                       required>
                                <label class="form-check-label" for="agreeTerms">
                                    Saya menyetujui bahwa tanda tangan digital ini memiliki kekuatan hukum yang sama dengan tanda tangan fisik dan saya bertanggung jawab penuh atas penggunaan tanda tangan ini.
                                </label>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid">
                            <button type="submit" 
                                    id="submitSignature" 
                                    class="btn btn-primary btn-lg" 
                                    disabled>
                                <span id="submitSpinner" class="spinner-border spinner-border-sm me-2 d-none"></span>
                                <i class="fas fa-signature me-2"></i>Simpan Tanda Tangan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Section -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body">
                    <h6 class="text-muted mb-3">
                        <i class="fas fa-info-circle me-2"></i>Informasi Penting
                    </h6>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Tanda tangan digital ini akan digunakan pada dokumen resmi pengajuan HKI
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Pastikan tanda tangan Anda jelas dan sesuai dengan dokumen identitas
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Setelah disimpan, tanda tangan tidak dapat diubah kecuali oleh administrator
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check text-success me-2"></i>
                            Proses akan berlanjut setelah semua pencipta menandatangani dokumen
                        </li>
                    </ul>
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
                <h5 class="modal-title text-success">
                    <i class="fas fa-check-circle me-2"></i>Tanda Tangan Berhasil Disimpan
                </h5>
            </div>
            <div class="modal-body text-center">
                <p class="mb-3">Terima kasih! Tanda tangan Anda telah berhasil disimpan.</p>
                <p class="text-muted small">Anda akan dialihkan dalam beberapa detik...</p>
            </div>
        </div>
    </div>
</div>

<style>
#signatureCanvas {
    border: 2px dashed #dee2e6;
    border-radius: 0.375rem;
}

#signatureCanvas:hover {
    border-color: #0d6efd;
}

.drawing {
    border-color: #198754 !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('signatureCanvas');
    const ctx = canvas.getContext('2d');
    const clearBtn = document.getElementById('clearCanvas');
    const agreeCheckbox = document.getElementById('agreeTerms');
    const submitBtn = document.getElementById('submitSignature');
    const form = document.getElementById('signatureForm');
    
    let isDrawing = false;
    let hasSignature = false;
    
    // Set canvas size
    canvas.width = 700;
    canvas.height = 300;
    
    // Canvas drawing setup
    ctx.strokeStyle = '#000';
    ctx.lineWidth = 2;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
    
    // Mouse events
    canvas.addEventListener('mousedown', startDrawing);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', stopDrawing);
    canvas.addEventListener('mouseout', stopDrawing);
    
    // Touch events for mobile
    canvas.addEventListener('touchstart', handleTouch);
    canvas.addEventListener('touchmove', handleTouch);
    canvas.addEventListener('touchend', stopDrawing);
    
    function startDrawing(e) {
        isDrawing = true;
        canvas.classList.add('drawing');
        const rect = canvas.getBoundingClientRect();
        const scaleX = canvas.width / rect.width;
        const scaleY = canvas.height / rect.height;
        
        ctx.beginPath();
        ctx.moveTo((e.clientX - rect.left) * scaleX, (e.clientY - rect.top) * scaleY);
    }
    
    function draw(e) {
        if (!isDrawing) return;
        
        const rect = canvas.getBoundingClientRect();
        const scaleX = canvas.width / rect.width;
        const scaleY = canvas.height / rect.height;
        
        ctx.lineTo((e.clientX - rect.left) * scaleX, (e.clientY - rect.top) * scaleY);
        ctx.stroke();
        
        hasSignature = true;
        updateSubmitButton();
    }
    
    function stopDrawing() {
        isDrawing = false;
        canvas.classList.remove('drawing');
    }
    
    function handleTouch(e) {
        e.preventDefault();
        const touch = e.touches[0];
        const mouseEvent = new MouseEvent(e.type === 'touchstart' ? 'mousedown' : 
                                        e.type === 'touchmove' ? 'mousemove' : 'mouseup', {
            clientX: touch.clientX,
            clientY: touch.clientY
        });
        canvas.dispatchEvent(mouseEvent);
    }
    
    // Clear canvas
    clearBtn.addEventListener('click', function() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        hasSignature = false;
        updateSubmitButton();
    });
    
    // Agreement checkbox
    agreeCheckbox.addEventListener('change', updateSubmitButton);
    
    // Submit Button logic update (enable/disable)
    function updateSubmitButton() {
        const method = document.getElementById('signatureMethod').value;
        const ktpReady = !!document.getElementById('ktpFile').files.length;
        let signatureReady = false;
        if (method === 'canvas') {
            signatureReady = hasSignature;
        } else {
            signatureReady = !!document.getElementById('signatureFile').files.length;
        }
        submitBtn.disabled = !(ktpReady && signatureReady && agreeCheckbox.checked);
    }
    // Observe file inputs change to call updateSubmitButton
    document.getElementById('ktpFile').addEventListener('change', updateSubmitButton);
    document.getElementById('signatureFile').addEventListener('change', updateSubmitButton);
    
    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const method = document.getElementById('signatureMethod').value;
        const ktpInput = document.getElementById('ktpFile');
        const signatureInputFile = document.getElementById('signatureFile');

        if (!ktpInput.files.length) {
            alert('Silakan upload foto KTP terlebih dahulu');
            return;
        }
        
        if (method === 'canvas' && !hasSignature) {
            alert('Silakan gambar tanda tangan terlebih dahulu');
            return;
        }
        if (method === 'upload' && !signatureInputFile.files.length) {
            alert('Silakan upload gambar tanda tangan terlebih dahulu');
            return;
        }
        if (!agreeCheckbox.checked) {
            alert('Silakan setujui syarat dan ketentuan');
            return;
        }
        
        // Show loading
        document.getElementById('submitSpinner').classList.remove('d-none');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
        
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        formData.append('ktp_file', ktpInput.files[0]);
        formData.append('signature_method', method);

        if (method === 'canvas') {
        const signatureData = canvas.toDataURL('image/png');
            formData.append('signature_data', signatureData);
        } else {
            formData.append('signature_file', signatureInputFile.files[0]);
        }
        formData.append('signed_by_name', '{{ $signature->nama_ttd }}');
        
        fetch(`{{ route('signatures.save', $signature->signature_token) }}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(async (response) => {
            const text = await response.text();
            let data;
            try {
                data = JSON.parse(text);
            } catch (err) {
                throw new Error('Terjadi kesalahan di server, silakan coba lagi.');
            }
            if (!response.ok) {
                throw new Error(data.message || 'Error');
            }
            return data;
        })
        .then(data => {
            if (data.message) {
                const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
                setTimeout(function() {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        window.location.href = '{{ route("pengajuan.index") }}';
                    }
                }, 3000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(error.message || 'Terjadi kesalahan saat menyimpan tanda tangan. Silakan coba lagi.');
            document.getElementById('submitSpinner').classList.add('d-none');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-signature me-2"></i>Simpan Tanda Tangan';
        });
    });

    // KTP file preview
    document.getElementById('ktpFile').addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('ktpImage').src = e.target.result;
                document.getElementById('ktpPreview').style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    // Signature file preview
    document.getElementById('signatureFile').addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('signatureImageDisplay').src = e.target.result;
                document.getElementById('signatureImagePreview').style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    // Tab switching
    document.querySelectorAll('#signatureMethodTabs button').forEach(tab => {
        tab.addEventListener('click', function() {
            const method = this.id === 'canvas-tab' ? 'canvas' : 'upload';
            document.getElementById('signatureMethod').value = method;
            updateSubmitButton(); // Update button state after tab change
        });
    });

    // updateSubmitButton after initialization
    updateSubmitButton();
});

// Remove KTP file function
function removeKtpFile() {
    document.getElementById('ktpFile').value = '';
    document.getElementById('ktpPreview').style.display = 'none';
    updateSubmitButton(); // Update button state after removing KTP file
}

// Remove signature file function
function removeSignatureFile() {
    document.getElementById('signatureFile').value = '';
    document.getElementById('signatureImagePreview').style.display = 'none';
    updateSubmitButton(); // Update button state after removing signature file
}
</script>
@endsection 