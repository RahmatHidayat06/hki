@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h4 class="text-center mb-4"><i class="fas fa-signature me-2"></i>Tanda Tangan Digital</h4>

    <div class="card">
        <div class="card-body text-center">
            <canvas id="signatureCanvas" width="600" height="200" style="border: 2px dashed #ccc;"></canvas>
            <div class="mt-3">
                <button class="btn btn-secondary btn-sm" onclick="clearCanvas()"><i class="fas fa-eraser me-1"></i>Bersihkan</button>
                <button class="btn btn-primary btn-sm" onclick="saveSignature()"><i class="fas fa-save me-1"></i>Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
let isDrawing = false;
const canvas = document.getElementById('signatureCanvas');
const ctx = canvas.getContext('2d');
ctx.lineWidth = 2;
ctx.strokeStyle = "#000";
ctx.lineCap = "round";

canvas.addEventListener('mousedown', e => {
    isDrawing = true;
    ctx.beginPath();
    ctx.moveTo(e.offsetX, e.offsetY);
});
canvas.addEventListener('mousemove', e => {
    if (isDrawing) {
        ctx.lineTo(e.offsetX, e.offsetY);
        ctx.stroke();
    }
});
canvas.addEventListener('mouseup', () => isDrawing = false);
canvas.addEventListener('mouseout', () => isDrawing = false);

function clearCanvas() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
}

function saveSignature() {
    const dataURL = canvas.toDataURL('image/png');

    fetch(`{{ route('signature.save', ['id' => $pengajuan->id]) }}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ signature: dataURL })
    })
    .then(res => res.json())
    .then(data => {
        alert('Tanda tangan berhasil disimpan!');
        window.location.href = data.redirect ?? "{{ route('pengajuan.index') }}";
    })
    .catch(err => {
        console.error(err);
        alert('Gagal menyimpan tanda tangan.');
    });
}
</script>
@endsection
