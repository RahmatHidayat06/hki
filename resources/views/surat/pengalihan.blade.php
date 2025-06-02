<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Pengalihan Hak Cipta</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .ttd-area { margin-top: 60px; position: relative; height: 120px; }
        .materai { position: absolute; left: 0; bottom: 0; width: 120px; }
        .ttd { position: absolute; left: 140px; bottom: 0; width: 120px; }
    </style>
</head>
<body>
    <h2 style="text-align:center;">SURAT PENGALIHAN HAK CIPTA</h2>
    <p>Yang bertanda tangan di bawah ini:</p>
    <table style="margin-bottom:20px;">
        <tr><td width="150">Nama Pengusul</td><td>: {{ $pengajuan->nama_pengusul }}</td></tr>
        <tr><td>NIP/NIDN</td><td>: {{ $pengajuan->nip_nidn }}</td></tr>
        <tr><td>Judul Karya</td><td>: {{ $pengajuan->judul_karya }}</td></tr>
        <tr><td>Kategori</td><td>: {{ $pengajuan->kategori }}</td></tr>
    </table>
    <p>Dengan ini mengalihkan hak cipta atas karya tersebut kepada institusi.</p>
    <div class="ttd-area">
        @if($materaiPath)
            <img src="{{ $materaiPath }}" class="materai">
        @endif
        @if($ttdPath)
            <img src="{{ $ttdPath }}" class="ttd">
        @endif
        <div style="position:absolute; left:140px; bottom:-30px; width:200px; text-align:center;">
            <span>Direktur</span><br><br><br>
            <span style="text-decoration:underline;">(........................................)</span>
        </div>
    </div>
</body>
</html> 