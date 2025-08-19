<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Surat Pernyataan</title>
    <style>
        @page { margin: 2cm; }
        body { 
            /* Gunakan Times New Roman 12pt sesuai contoh surat */
            font-family: 'Times New Roman', Times, serif !important; 
            font-size: 12pt !important; 
            line-height: 1.5;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }
        .text-center { 
            text-align: center; 
            font-weight: bold;
            text-decoration: underline;
            font-size: 14px;
            margin-bottom: 2rem;
        }
        .content-text { 
            text-align: justify; 
            line-height: 1.5; 
            margin: 1.2rem 0;
        }
        .signature-section {
            width: 100%;
            margin-top: 60px;
            page-break-inside: avoid; /* keep signature together */
        }
        .signature-block {
            width: 60%;
            margin-left: auto; /* push to right without float */
            text-align: center;
        }
        .signature-name {
            margin-top: 80px;
            text-decoration: underline;
            font-weight: bold;
        }
        .materai-text {
            font-size: 10px;
            margin: 2rem 0;
            font-style: italic;
        }
        .clear { clear: both; }
        ol.decimal-list {
            margin: 0 0 0 20px;
            padding: 0;
            list-style-type: decimal;
        }
        ol.decimal-list li { margin: 0 0 0.5rem 0; text-align: justify; }
        ul.bullet-list { margin: 0 0 0 20px; padding: 0; list-style-type: disc; }
        ul.bullet-list li { margin: 0 0 0.5rem 0; text-align: justify; }

        /* Tabel identitas untuk merapikan titik dua */
        .identity-table { width:100%; border-collapse:collapse; line-height:1.5; margin: 1.2rem 0; }
        .identity-table td { padding:0 0.3rem 0 0; vertical-align: top; }

        /* List huruf a), b), c) */
        ol.lower-alpha { list-style-type: lower-alpha; margin: 0 0 0 1.4rem; padding:0; }
        ol.lower-alpha li { margin: 0 0 0.5rem 0; text-align: justify; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">SURAT PERNYATAAN</h1>

        <div class="content-text">Yang bertanda tangan di bawah ini, pemegang hak cipta:</div>
        
        <table class="identity-table">
            <tr>
                <td>N&nbsp;a&nbsp;m&nbsp;a</td>
                <td>:</td>
                <td>Politeknik Negeri Banjarmasin</td>
            </tr>
            <tr>
                <td>Kewarganegaraan</td>
                <td>:</td>
                <td>Indonesia</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td>Jl. Brigjen H. Hasan Basri, Kayutangi, Banjarmasin, Kalimantan Selatan</td>
            </tr>
        </table>

        <div class="content-text" style="margin-top: 1.5rem;">Dengan ini menyatakan bahwa:</div>
        
        <!-- ===== BAGIAN PERNYATAAN UTAMA - MENGIKUTI FORMAT NUMERIK ===== -->
        <ol class="decimal-list">
            <!-- 1 -->
            <li>
                Karya Cipta yang saya mohonkan:
                <div style="margin: 1.2rem 0; line-height: 1.5;">
                    <div>Berupa&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;{{ $pengajuan->sub_jenis_ciptaan ?? '…………………………………………………………………………………….' }}</div>
                    <div>Berjudul&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;{{ $pengajuan->judul_karya ?? '…………………………………………………………………………………….' }}</div>
        </div>
            <ul class="bullet-list">
                <li>Tidak meniru dan tidak sama secara esensial dengan Karya Cipta milik pihak lain atau obyek kekayaan intelektual lainnya sebagaimana dimaksud dalam Pasal 68 ayat (2);</li>
                <li>Bukan merupakan Ekspresi Budaya Tradisional sebagaimana dimaksud dalam Pasal 38;</li>
                <li>Bukan merupakan Ciptaan yang tidak diketahui penciptanya sebagaimana dimaksud dalam Pasal 39;</li>
                <li>Bukan merupakan hasil karya yang tidak dilindungi Hak Cipta sebagaimana dimaksud dalam Pasal 41 dan 42;</li>
                <li>Bukan merupakan Ciptaan seni lukis yang berupa logo atau tanda pembeda yang digunakan sebagai merek dalam perdagangan barang/jasa atau digunakan sebagai lambang organisasi, badan usaha, atau badan hukum sebagaimana dimaksud dalam Pasal 65;</li>
                    <li>Bukan merupakan Ciptaan yang melanggar norma agama, norma susila, ketertiban umum, pertahanan dan keamanan negara atau melanggar peraturan perundang-undangan sebagaimana dimaksud dalam Pasal 74 ayat (1) huruf d Undang-Undang Nomor 28 Tahun 2014 tentang Hak Cipta.</li>
            </ul>
            </li>

            <!-- 2 -->
            <li>Sebagai pemohon mempunyai kewajiban untuk menyimpan asli contoh ciptaan yang dimohonkan dan harus memberikan apabila dibutuhkan untuk kepentingan penyelesaian sengketa perdata maupun pidana sesuai dengan ketentuan perundang-undangan.</li>

            <!-- 3 -->
            <li>Karya Cipta yang saya mohonkan pada Angka 1 tersebut di atas tidak pernah dan tidak sedang dalam sengketa pidana dan/atau perdata di Pengadilan.</li>

            <!-- 4 -->
            <li>
                Dalam hal ketentuan sebagaimana dimaksud dalam Angka 1 dan Angka 3 tersebut di atas saya / kami langgar, maka saya / kami bersedia secara sukarela bahwa:
                <ol class="lower-alpha">
                    <li>permohonan karya cipta yang saya ajukan dianggap ditarik kembali; atau</li>
                    <li>Karya Cipta yang telah terdaftar dalam Daftar Umum Ciptaan Direktorat Hak Cipta, Direktorat Jenderal Hak Kekayaan Intelektual, Kementerian Hukum Dan Hak Asasi Manusia R.I dihapuskan sesuai dengan ketentuan perundang-undangan yang berlaku.</li>
                    <li>Dalam hal kepemilikan Hak Cipta yang dimohonkan secara elektronik sedang dalam berperkara dan/atau sedang dalam gugatan di Pengadilan maka status kepemilikan surat pencatatan elektronik tersebut ditangguhkan menunggu putusan Pengadilan yang berkekuatan hukum tetap.</li>
                </ol>
            </li>
        </ol>

        <div class="content-text">
            Demikian Surat pernyataan ini saya/kami buat dengan sebenarnya dan untuk dipergunakan sebagaimana mestinya.
        </div>

        <div class="signature-section">
            <div class="signature-block">
                <div>
                    Banjarmasin, {{ $tanggalSurat ?: '……………………….' }}<br>
                    Politeknik Negeri Banjarmasin
                </div>
                <div class="materai-text">Materai 10.000, -</div>
                    <div class="signature-name">
                    (Joni Riadi, SST, MT)<br>
                    Pemegang Hak Cipta
                </div>
            </div>
        </div>
    </div>
</body>
</html> 