<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FORM PERMOHONAN PENDAFTARAN CIPTAAN</title>
    <style>
        @page {
            size: A4;
            margin: 2cm;
        }
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            color: black;
        }
        .header {
            text-align: center;
            font-weight: bold;
            font-size: 14pt;
            margin-bottom: 30px;
            text-transform: uppercase;
        }
        .creator-section {
            margin-bottom: 18px;
        }
        .creator-title {
            font-weight: bold;
            margin-bottom: 2px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }
        .data-table td {
            vertical-align: top;
            padding: 0 0 2px 0;
            font-size: 12pt;
        }
        .data-table .label {
            width: 2%;
        }
        .data-table .field {
            width: 25%;
        }
        .data-table .colon {
            width: 2%;
        }
        .data-table .value {
            width: 71%;
        }
        .work-data-section {
            margin-top: 30px;
        }
        .uraian-multiline {
            white-space: pre-line;
        }
        .signature-section {
            margin-top: 60px;
            text-align: right;
            margin-right: 30px;
        }
        .signature-box {
            margin-top: 60px;
            text-align: center;
        }
        .signature-name {
            text-decoration: underline;
            font-weight: bold;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        FORM PERMOHONAN PENDAFTARAN CIPTAAN
    </div>
    @php
        $jumlahPencipta = (int) ($pengajuan->jumlah_pencipta ?? 1);
        $penciptaData = $pengajuan->pengaju ?? collect();
        if (is_array($penciptaData)) $penciptaData = collect($penciptaData);
        $roman = ['I','II','III','IV','V','VI','VII','VIII','IX','X'];
    @endphp
    @for($i = 1; $i <= $jumlahPencipta; $i++)
        @php $pencipta = $penciptaData->get($i-1); @endphp
        <div class="creator-section">
            <div class="creator-title">{{ $roman[$i-1] ?? $i }}. Data Pencipta</div>
            <table class="data-table">
                <tr><td class="label">a.</td><td class="field">Nama</td><td class="colon">:</td><td class="value">{{ $pencipta->nama ?? '………………………………………………………………………' }}</td></tr>
                <tr><td class="label">b.</td><td class="field">Kewarganegaraan</td><td class="colon">:</td><td class="value">{{ $pencipta->kewarganegaraan ?? $pengajuan->user->nationality ?? 'Indonesia' }}</td></tr>
                <tr><td class="label">c.</td><td class="field">Alamat</td><td class="colon">:</td><td class="value">{{ $pencipta->alamat ?? '………………………………………………………………………' }}</td></tr>
                <tr><td class="label">d.</td><td class="field">Kode pos</td><td class="colon">:</td><td class="value">{{ $pencipta->kodepos ?? '………………………………………………………………………' }}</td></tr>
                <tr><td class="label">e.</td><td class="field">No. Telp</td><td class="colon">:</td><td class="value">{{ $pencipta->no_telp ?? '.........................' }}</td></tr>
                <tr><td class="label">f.</td><td class="field">Email</td><td class="colon">:</td><td class="value">{{ $pencipta->email ?? '………………………………………………………………………' }}</td></tr>
            </table>
        </div>
    @endfor
    <div class="work-data-section page-break">
        <div class="creator-title">V. Data Ciptaan</div>
        <table class="data-table">
            <tr><td class="label">a.</td><td class="field">Jenis Ciptaan</td><td class="colon">:</td><td class="value">{{ $pengajuan->identitas_ciptaan ? ucfirst($pengajuan->identitas_ciptaan) : '………………………………………………………………………' }}</td></tr>
            <tr><td class="label">b.</td><td class="field">Sub Jenis Ciptaan</td><td class="colon">:</td><td class="value">{{ $pengajuan->sub_jenis_ciptaan ?? '………………………………………………………………………' }}</td></tr>
            <tr><td class="label">c.</td><td class="field">Judul Ciptaan</td><td class="colon">:</td><td class="value">{{ $pengajuan->judul_karya ?? '………………………………………………………………………' }}</td></tr>
            <tr><td class="label">d.</td><td class="field">Uraian (Deskripsi) Singkat Ciptaan</td><td class="colon">:</td><td class="value uraian-multiline">{{ $pengajuan->deskripsi ?? '………………………………………………………………………' }}</td></tr>
            <tr><td class="label">e.</td><td class="field">Tanggal Pertama Kali Diumumkan</td><td class="colon">:</td><td class="value">@if($pengajuan->tanggal_pertama_kali_diumumkan){{ \Carbon\Carbon::parse($pengajuan->tanggal_pertama_kali_diumumkan)->translatedFormat('d F Y') }}@else………………………………………………………………………@endif</td></tr>
            <tr><td class="label">f.</td><td class="field">Kota Pertama Kali Diumumkan</td><td class="colon">:</td><td class="value">{{ $pengajuan->kota_pertama_kali_diumumkan ?? '………………………………………………………………………' }}</td></tr>
        </table>
    </div>
    <div class="signature-section">
        {{ $pengajuan->kota_pertama_kali_diumumkan ?? 'Banjarmasin' }},
        @if($pengajuan->tanggal_surat)
            {{ \Carbon\Carbon::parse($pengajuan->tanggal_surat)->translatedFormat('d F Y') }}
        @else
            {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
        @endif
        <br> Pemohon,
        <div class="signature-box">
    @if($pengajuan->ttd_path)
        <img src="{{ asset($pengajuan->ttd_path) }}" alt="Tanda Tangan" style="width: 100px; max-height: 50px;">
    @endif
    <div class="signature-name">
        ({{ $penciptaData->get(0)->nama ?? $pengajuan->user->nama_lengkap ?? $pengajuan->nama_pengusul ?? '…………………………………' }})
    </div>
</div>
                
                
                
                -width: 100px; max-height: 50px;">
            @endif
            <div class="signature-name">
                ({{ $penciptaData->get(0)->nama ?? $pengajuan->user->nama_lengkap ?? $pengajuan->nama_pengusul ?? '…………………………………' }})
            </div>
        </div>
    </div>
</body>
</html> 