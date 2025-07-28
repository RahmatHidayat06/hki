<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Pengajuan HKI</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1,h2,h3,h4 { margin:0; padding:0; }
        .header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom:20px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; }
        th { background: #f2f2f2; }
        .doc-table th, .doc-table td { text-align: left; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Dokumen Pengajuan Hak Cipta</h2>
        <h4>{{ $pengajuan->judul_karya }}</h4>
    </div>

    <h3>Data Umum</h3>
    <table>
        <tbody>
            <tr><th>ID Pengajuan</th><td>{{ $pengajuan->id }}</td></tr>
            <tr><th>Nomor Pengajuan</th><td>{{ $pengajuan->nomor_pengajuan ?? '-' }}</td></tr>
            <tr><th>Judul Karya</th><td>{{ $pengajuan->judul_karya }}</td></tr>
            <tr><th>Deskripsi</th><td>{{ $pengajuan->deskripsi_karya ?? $pengajuan->deskripsi }}</td></tr>
            <tr><th>Jenis Ciptaan</th><td>{{ $pengajuan->identitas_ciptaan }}</td></tr>
            <tr><th>Sub Jenis</th><td>{{ $pengajuan->sub_jenis_ciptaan }}</td></tr>
            <tr><th>Tanggal Pengajuan</th><td>{{ optional($pengajuan->created_at)->format('d-m-Y H:i') }}</td></tr>
            <tr><th>Jumlah Pencipta</th><td>{{ $pengajuan->pengaju->count() }}</td></tr>
        </tbody>
    </table>

    <h3>Daftar Pencipta</h3>
    <table>
        <thead><tr><th>No</th><th>Nama</th><th>Email</th><th>No. Telp</th><th>Alamat</th><th>Kewarganegaraan</th><th>Kode Pos</th></tr></thead>
        <tbody>
            @foreach($pengajuan->pengaju as $idx=>$creator)
            <tr>
                <td>{{ $idx+1 }}</td>
                <td>{{ $creator->nama }}</td>
                <td>{{ $creator->email }}</td>
                <td>{{ $creator->no_telp }}</td>
                <td>{{ $creator->alamat }}</td>
                <td>{{ $creator->kewarganegaraan }}</td>
                <td>{{ $creator->kodepos }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Daftar Dokumen</h3>
    <table class="doc-table">
        <thead>
            <tr>
                <th>Jenis Dokumen</th>
                <th>Nama File</th>
                <th>Ukuran</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($documents as $key=>$doc)
            <tr>
                @php
                    $label = $doc['label'];
                    if($label == 'Contoh Ciptaan' || $label == 'File Karya Ciptaan') $label = 'File Karya Ciptaan';
                @endphp
                <td>{{ $label }}</td>
                <td>{{ $doc['file_info']['filename'] ?? 'N/A' }}</td>
                <td>{{ $doc['file_info']['size_formatted'] ?? '-' }}</td>
                <td>Tersedia{{ isset($dokumen['signed'][$key]) ? ' (Signed)' : '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p style="font-size:10px; text-align:center;">Dokumen ini dihasilkan secara otomatis oleh sistem HKI - {{ date('d/m/Y H:i') }}</p>
</body>
</html> 