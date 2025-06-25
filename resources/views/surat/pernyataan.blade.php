<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Surat Pernyataan</title>
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
            height: 120px;
            margin-top: 10px;
        }

        .materai-img, .ttd-img {
            position: absolute;
            bottom: 20px;
            max-width: 120px;
            max-height: 100px;
        }
        
        .materai-img {
            left: 20px;
        }
        
        .ttd-img {
            left: 50px;
            opacity: 0.85;
        }

        .signature-name {
            margin-top: 80px;
            text-decoration: underline;
            font-weight: bold;
        }
        .clear { clear: both; }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center">SURAT PERNYATAAN</h2>

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
            Dengan ini menyatakan bahwa ciptaan dengan judul: <strong>"{{ $pengajuan->judul_karya }}"</strong> adalah benar-benar karya asli saya/kami, tidak menjiplak karya orang lain, dan belum pernah dipublikasikan sebelumnya.
        </p>
        
        <p class="text-justify">
            Apabila di kemudian hari terbukti bahwa pernyataan ini tidak benar, saya/kami bersedia menerima sanksi sesuai dengan peraturan yang berlaku.
        </p>

        <p class="text-justify">
            Demikian Surat Pernyataan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.
        </p>

        <div class="signature-section">
            <div class="signature-block">
                <div>
                    Makassar, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    Yang Membuat Pernyataan,
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