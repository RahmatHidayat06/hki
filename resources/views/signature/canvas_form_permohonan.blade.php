<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tanda Tangan Permohonan</title>
    <style>
        canvas {
            border: 1px solid #000;
        }
    </style>
</head>
<body>
    <h2>Tanda Tangan Digital</h2>
    <form method="POST" action="{{ route('pengajuan.signature.save', $pengajuan->id) }}">
        @csrf
        <canvas id="signature-pad" width="400" height="200"></canvas><br>
        <input type="hidden" name="signature" id="signature">
        <button type="button" onclick="clearCanvas()">Bersihkan</button>
        <button type="submit" onclick="saveSignature()">Simpan Tanda Tangan</button>
    </form>

    <script>
        const canvas = document.getElementById('signature-pad');
        const ctx = canvas.getContext('2d');
        let drawing = false;

        canvas.addEventListener('mousedown', () => drawing = true);
        canvas.addEventListener('mouseup', () => drawing = false);
        canvas.addEventListener('mousemove', draw);

        function draw(e) {
            if (!drawing) return;
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.strokeStyle = '#000';

            ctx.lineTo(e.offsetX, e.offsetY);
            ctx.stroke();
            ctx.beginPath();
            ctx.moveTo(e.offsetX, e.offsetY);
        }

        function clearCanvas() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }

        function saveSignature() {
            const dataURL = canvas.toDataURL();
            document.getElementById('signature').value = dataURL;
        }
    </script>
</body>
</html>
