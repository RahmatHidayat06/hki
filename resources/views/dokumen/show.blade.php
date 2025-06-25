<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dokumen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height:100vh;">
    <div class="container text-center py-5">
        <h3 class="mb-4">Pratinjau Dokumen Tidak Tersedia</h3>
        <p class="mb-4">Tipe dokumen: {{ $ext }}. Anda dapat mengunduh dokumen melalui tombol di bawah.</p>
        <a href="{{ $url }}?download=1" class="btn btn-primary btn-lg"><i class="bi bi-download"></i> Unduh Dokumen</a>
    </div>
</body>
</html> 