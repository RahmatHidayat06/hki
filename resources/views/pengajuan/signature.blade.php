@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center">
                    <h4 class="text-primary mb-3">
                        <i class="fas fa-signature me-2"></i>Tanda Tangan Digital Permohonan Pendaftaran
                    </h4>
                    <p class="mb-1"><strong>Pengajuan:</strong> {{ $pengajuan->judul_karya }}</p>
                    <p class="mb-1"><strong>Nomor:</strong> {{ $pengajuan->nomor_pengajuan ?? 'Belum ada nomor' }}</p>
                </div>
            </div>
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-pen me-2"></i>Buat Tanda Tangan Anda
                    </h6>
                </div>
                <div class="card-body">
                    <form id="signatureForm" method="POST" action="{{ route('pengajuan.signature.save', $pengajuan->id) }}">
                        @csrf
                        <div class="mb-4">
                            <canvas id="signatureCanvas" width="700" height="200" style="border:2px dashed #dee2e6; border-radius:8px; width:100%; max-width:700px; height:200px; cursor:crosshair;">
                                Browser Anda tidak mendukung HTML5 Canvas
                            </canvas>
                            <div class="d-flex justify-content-between mt-2">
                                <small class="text-muted">Gambar tanda tangan Anda di area di atas</small>
                                <button type="button" id="clearCanvas" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-eraser me-1"></i>Hapus
                                </button>
                            </div>
                        </div>
                        <input type="hidden" name="signature_data" id="signatureData">
                        <div class="d-grid">
                            <button type="submit" id="submitSignature" class="btn btn-primary btn-lg" disabled>
                                <i class="fas fa-signature me-2"></i>Simpan Tanda Tangan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('signatureCanvas');
    const ctx = canvas.getContext('2d');
    const clearBtn = document.getElementById('clearCanvas');
    const submitBtn = document.getElementById('submitSignature');
    const form = document.getElementById('signatureForm');
    let isDrawing = false;
    let hasSignature = false;

    canvas.width = 700;
    canvas.height = 200;
    ctx.strokeStyle = '#000';
    ctx.lineWidth = 2;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';

    canvas.addEventListener('mousedown', startDrawing);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', stopDrawing);
    canvas.addEventListener('mouseout', stopDrawing);

    canvas.addEventListener('touchstart', handleTouch);
    canvas.addEventListener('touchmove', handleTouch);
    canvas.addEventListener('touchend', stopDrawing);

    function startDrawing(e) {
        isDrawing = true;
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
        submitBtn.disabled = false;
    }
    function stopDrawing() {
        isDrawing = false;
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
    clearBtn.addEventListener('click', function() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        hasSignature = false;
        submitBtn.disabled = true;
    });
    form.addEventListener('submit', function(e) {
        if (!hasSignature) {
            e.preventDefault();
            alert('Silakan gambar tanda tangan terlebih dahulu');
            return;
        }
        document.getElementById('signatureData').value = canvas.toDataURL('image/png');
    });
});
</script>
@endsection