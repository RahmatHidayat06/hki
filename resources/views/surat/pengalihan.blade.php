<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Surat Pengalihan Hak Cipta</title>
    <style>
        @page { 
            margin: 2cm; 
            size: A4;
        }
        body, table, td, th, div, span, p {
            font-family: 'Times New Roman', Times, serif !important;
            font-size: 12pt !important;
        }
        body { 
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }
        .text-center { 
            text-align: center; 
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 1.5rem;
        }
        .content-text { 
            text-align: justify; 
            line-height: 1.5;
            margin: 1.2rem 0 1.2rem 0;
        }
        .pencipta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.2rem;
        }
        .pencipta-table td {
            padding: 0.1rem 0.2rem 0.1rem 0.2rem;
            vertical-align: top;
            line-height: 1.3;
        }
        .dots {
            letter-spacing: 2px;
        }
        .institution-info {
            margin: 1.2rem 0 1.2rem 0;
            line-height: 1.5;
        }
        .signature-section {
            margin-top: 6.5rem;
            position: relative;
        }
        .signature-date {
            text-align: right;
            margin-bottom: 3.5rem;
        }
        .signature-grid {
            display: table;
            width: 100%;
            table-layout: fixed;
        }
        .signature-cell {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0 1rem;
            box-sizing: border-box;
        }
        .signature-name {
            margin-top: 4rem;
            text-decoration: underline;
            font-weight: bold;
        }
        .materai-text {
            font-size: 10px;
            font-style: italic;
            margin-bottom: 1.5rem;
        }
        .signature-spacing {
            margin-top: 3.5rem;
            margin-bottom: 1rem;
        }
        .page-break {
            page-break-before: always;
        }
        .ttd-parent {
            margin-top: 4.5rem;
        }
        .ttd-label {
            margin-bottom: 1.2rem;
        }
        .ttd-container {
            display: flex;
            justify-content: flex-start; /* left align inside cell */
            align-items: flex-start;
            gap: 1rem;
            margin: 1rem 0 2rem 0;
        }
        .ttd-materai {
            font-size: 9pt !important;
            font-style: italic;
            flex: 0 0 auto;
            text-align: left;
            min-width: 90px;
            visibility: hidden; /* sembunyikan teks, tetap sisakan ruang untuk materai opsional */
        }
        .ttd-signbox {
            flex: 1 1 auto;
            height: 60px; /* ruang tanda tangan ~3 baris */
        }
        .ttd-nama { margin-top: 1.2rem; }
        /* Hapus garis horizontal jika ada */
        hr { display: none; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">SURAT PENGALIHAN HAK CIPTA</h1>

        <div class="content-text">
            Yang bertanda tangan di bawah ini :
        </div>
        @php
            $penciptaData = $pengajuan->alamat_pencipta ?? [];
            $jumlahPencipta = intval($pengajuan->jumlah_pencipta ?? 1);
            $maxPencipta = max($jumlahPencipta, 1);
        @endphp
        <table class="pencipta-table"> <!-- Hapus min-height agar tidak ada spasi kosong -->
            @for($i = 1; $i <= $maxPencipta; $i++)
                @php
                    $pencipta = $penciptaData[$i] ?? [];
                    $nama = trim($pencipta['nama'] ?? '');                                                  
                    $alamat = trim($pencipta['alamat'] ?? '');
                @endphp
                <tr>
                    <td style="width:2%; vertical-align:top;">{{ $i }}.</td>
                    <td style="width:12%;">Nama</td>
                    <td style="width:2%;">:</td>
                    <td style="width:84%;">
                        @if($nama)
                            {{ $nama }}
                        @else
                            <span class="dots">…………………………………………………………………………………………………………………………</span>
                        @endif
                    </td>
            </tr>
            <tr>
                    <td></td>
                    <td>Alamat</td>
                    <td>:</td>
                    <td>
                        @if($alamat)
                            {{ $alamat }}
                        @else
                            <span class="dots">…………………………………………………………………………………………………………………………</span>
                        @endif
                    </td>
            </tr>
            @endfor
        </table>

        <div class="content-text">
            Adalah <b>Pihak I</b> selaku pencipta, dengan ini menyerahkan karya ciptaan saya kepada :
        </div>
        <div class="institution-info">
            <div>Nama&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;Politeknik Negeri Banjarmasin</div>
            <div>Alamat&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;Jl. Brigjen H. Hasan Basri, Kayutangi, Banjarmasin, Kalimantan Selatan</div>
        </div>
        <div class="content-text">
            Adalah <b>Pihak II</b> selaku Pemegang Hak Cipta berupa {{ $pengajuan->sub_jenis_ciptaan ?? '…………………………………….' }} untuk didaftarkan di Direktorat Hak Cipta dan Desain Industri, Direktorat Jenderal Kekayaan Intelektual, Kementerian Hukum dan Hak Asasi Manusia Republik Indonesia.
        </div>
        <div class="content-text">
            Demikianlah surat pengalihan hak ini kami buat, agar dapat dipergunakan sebagaimana mestinya.
        </div>
        <div class="signature-section" style="position: fixed; left: 0; right: 0; bottom: 0px; width: 100%; background: white; padding-bottom: 0px;">
            <div class="signature-date">
                Banjarmasin, {{ $tanggalSurat ?: '…………………' }}
            </div>
            <div class="signature-grid">
                <div class="signature-cell">
                    <div class="ttd-label">Pemegang Hak Cipta,</div>
                    <div class="ttd-label">Politeknik Negeri Banjarmasin</div>
                    <div class="ttd-container">
                        <div class="ttd-signbox"></div>
                    </div>
                    <div class="ttd-parent ttd-nama ttd-pemegang" style="margin-top: 2rem;">( Joni Riadi, SST, MT )</div>
                </div>
                <div class="signature-cell">
                    <div class="ttd-label">Pencipta I,</div>
                    <div class="ttd-container">
                    <div class="ttd-materai">Materai 10.000</div>
                        <div class="ttd-signbox"></div>
                    </div>
                    <div class="ttd-parent ttd-nama" style="margin-top: 2rem;">
                        @php $nama1 = $penciptaData[1]['nama'] ?? '';
                        @endphp
                        {{ $nama1 ? '( ' . $nama1 . ' )' : '(……………………………………………)'}}
                    </div>
                </div>
            </div>
        </div>
        @if($maxPencipta > 1)
            <div class="page-break"></div>
            <div class="container">
                <br><br>
                <table style="width:100%; border-collapse:collapse;">
                    @php
                        $rowCount = ceil(($maxPencipta-1)/2);
                        $romawiArr = ['II', 'III', 'IV', 'V','VI','VII','VIII','IX','X'];
                        $idx = 0;
                    @endphp
                    @for($row = 0; $row < $rowCount; $row++)
                        <tr>
                            @for($col = 0; $col < 2; $col++)
                                @php
                                    $i = 2 + $row*2 + $col;
                                    if ($i > $maxPencipta) break;
                                    $namaI = $penciptaData[$i]['nama'] ?? '';
                                    $romawi = $romawiArr[$i-2] ?? $i;
                                @endphp
                                <td style="width:50%; text-align:left; vertical-align:top; padding-bottom: 6.5rem;">
                                    <div class="ttd-label">Pencipta {{ $romawi }},</div>
                                    <div class="ttd-container" style="justify-content: flex-start;">
                                    <div class="ttd-materai">Materai 10.000</div>
                                        <div class="ttd-signbox"></div>
                                    </div>
                                    <div class="ttd-parent ttd-nama">
                                        {{ $namaI ? '( ' . $namaI . ' )' : '(……………………………………………)' }}
                                    </div>
                                </td>
                            @endfor
                            @if(($row*2+2) > $maxPencipta)
                                <td style="width:50%;"></td>
                            @endif
                        </tr>
                    @endfor
                </table>
            </div>
        @endif
    </div>
</body>
</html> 