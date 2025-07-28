@extends('layouts.app')

@section('content')
<div class="container my-4">
    <h4 class="mb-4"><i class="fas fa-signature me-2"></i>Buat Tanda Tangan Anda</h4>

    <div class="card shadow-sm">
        <div class="card-body">
            <form id="signatureForm" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Metode Tanda Tangan</label>
                    <ul class="nav nav-pills nav-fill mb-3">
                        <li class="nav-item">
                            <button class="nav-link active" type="button" id="canvas-tab">Gambar Digital</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" type="button" id="upload-tab">Upload Gambar</button>
                        </li>
                    </ul>

                    <!-- Canvas -->
                    <div id="canvas-wrapper">
                        <canvas id="signatureCanvas" width="600" height="250" style="border: 1px solid #ccc;"></canvas>
                        <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="clearCanvas">Hapus</button>
                    </div>

                    <!-- Upload -->
                    <div id="upload-wrapper" class="d-none">
                        <input type="file" name="signature_file" id="signatureFile" class="form-control" accept="image/*">
                        <div class="mt-2" id="signaturePreview" style="display:none;">
                            <img id="signatureImageDisplay" class="img-thumbnail" style="max-height: 150px;">
                        </div>
                    </div>
                </div>

                <input type="hidden" name="signature_image" id="signatureImage">
                <input type="hidden" name="pengajuan_id" value="{{ $pengajuan->id }}">

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                    <label class="form-check-label" for="agreeTerms">
                        Saya menyetujui bahwa tanda tangan digital ini sah dan dapat dipertanggungjawabkan.
                    </label>
                </div>

                <button type="submit" id="saveSignature" class="btn btn-primary" disabled>
                    <i class="fas fa-save me-1"></i> Simpan dan Lanjutkan ke Surat
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const canvas = document.getElementById('signatureCanvas');
    const ctx = canvas.getContext('2d');
    let drawing = false;

    canvas.addEventListener('mousedown', () => drawing = true);
    canvas.addEventListener('mouseup', () => drawing = false);
    canvas.addEventListener('mouseout', () => drawing = false);
    canvas.addEventListener('mousemove', draw);

    function draw(e) {
        if (!drawing) return;
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.strokeStyle = '#000';

        const rect = canvas.getBoundingClientRect();
        ctx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
        ctx.stroke();
        ctx.beginPath();
        ctx.moveTo(e.clientX - rect.left, e.clientY - rect.top);
    }

    document.getElementById('clearCanvas').addEventListener('click', () => {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.beginPath();
    });

    document.getElementById('canvas-tab').addEventListener('click', () => {
        document.getElementById('canvas-wrapper').classList.remove('d-none');
        document.getElementById('upload-wrapper').classList.add('d-none');
    });

    document.getElementById('upload-tab').addEventListener('click', () => {
        document.getElementById('canvas-wrapper').classList.add('d-none');
        document.getElementById('upload-wrapper').classList.remove('d-none');
    });

    document.getElementById('signatureFile').addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const imgDisplay = document.getElementById('signatureImageDisplay');
                imgDisplay.src = e.target.result;
                document.getElementById('signaturePreview').style.display = 'block';
                document.getElementById('signatureImage').value = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });

    document.getElementById('agreeTerms').addEventListener('change', function () {
        document.getElementById('saveSignature').disabled = !this.checked;
    });

    document.getElementById('signatureForm').addEventListener('submit', function(e) {
        e.preventDefault();

        let imageData;
        if (!document.getElementById('upload-wrapper').classList.contains('d-none')) {
            // Upload
            imageData = document.getElementById('signatureImage').value;
        } else {
            // Canvas
            imageData = canvas.toDataURL('image/png');
        }

        fetch("{{ route('signature.save') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('[name=_token]').value,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                pengajuan_id: {{ $pengajuan->id }},
                signature_image: imageData
            })
        }).then(res => res.json())
          .then(data => {
              if (data.success) {
                  window.location.href = data.redirect_url;
              } else {
                  alert('Gagal menyimpan tanda tangan');
              }
          });
    });
</script>
@endsection
