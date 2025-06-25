<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Surat Pengalihan Hak Cipta</title>
    <style>
        @page { margin: 2cm; }
        body { 
            font-family: 'DejaVu Sans', sans-serif; 
            font-size: 12px; 
            line-height: 1.5;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }
        .text-center { text-align: center; }
        .text-justify { text-align: justify; }
        .mt-4 { margin-top: 1.5rem; }
        .mt-5 { margin-top: 3rem; }
        .mb-3 { margin-bottom: 1rem; }
        h2 { text-decoration: underline; }

        table {
            border-collapse: collapse;
            width: 100%;
        }
        
        .details-table td {
            padding: 2px 0;
        }

        .signature-section {
            width: 100%;
            margin-top: 50px;
        }

        .signature-block {
            width: 45%;
            float: right;
            text-align: center;
        }
        
        .signature-area {
            position: relative;
            height: 120px; /* Adjust height as needed */
            margin-top: 10px;
        }

        .materai-img, .ttd-img {
            position: absolute;
            bottom: 20px; /* Position above name */
            max-width: 120px;
            max-height: 100px;
        }
        
        .materai-img {
            left: 20px; /* Position materai on the left part of the signature area */
        }
        
        .ttd-img {
            left: 50px; /* Overlap with materai */
            opacity: 0.85; /* Make signature slightly transparent */
        }

        .signature-name {
            margin-top: 80px; /* Space for signature and stamp */
            text-decoration: underline;
            font-weight: bold;
        }
        .clear { clear: both; }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center">SURAT PENGALIHAN HAK CIPTA</h2>

        <p class="mt-4 text-justify">Yang bertanda tangan di bawah ini:</p>
        
        <table class="details-table" style="width: 80%; margin-left: 20px;">
            <tr>
                <td style="width: 30%;">Nama</td>
                <td>: {{ $pengajuan->user->nama_lengkap ?? $pengajuan->nama_pengusul }}</td>
            </tr>
            <tr>
                <td>NIP/NIDN/NIM</td>
                <td>: {{ $pengajuan->nip_nidn ?? '-' }}</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Alamat</td>
                <td style="vertical-align: top;">: {{ $pengajuan->user->alamat ?? 'Alamat tidak tersedia' }}</td>
            </tr>
        </table>

        <p class="mt-4 text-justify">
            Selaku pencipta dari ciptaan dengan judul: <strong>"{{ $pengajuan->judul_karya }}"</strong>.
        </p>
        
        <p class="text-justify">
            Dengan ini mengalihkan hak ekonomi atas ciptaan tersebut kepada Politeknik Negeri Media Kreatif. Pengalihan hak ini bersifat eksklusif dan tanpa batas waktu.
        </p>

        <p class="text-justify">
            Demikian Surat Pengalihan Hak Cipta ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.
        </p>

        <div class="signature-section">
            <div class="signature-block">
                <div>
                    Makassar, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    Yang Mengalihkan,
                </div>
                <div class="signature-area">
                    @if(isset($materaiPath) && file_exists($materaiPath))
                        <img src="{{ $materaiPath }}" class="materai-img" alt="Materai">
                    @endif
                    @if(isset($ttdPath) && file_exists($ttdPath))
                        <img src="{{ $ttdPath }}" class="ttd-img" alt="Tanda Tangan">
                    @endif
                </div>
                <div>
                    <div class="signature-name">
                        {{ $pengajuan->user->nama_lengkap ?? $pengajuan->nama_pengusul }}
                    </div>
                </div>
            </div>
            <div class="clear"></div>
        </div>
    </div>
</body>
</html> 