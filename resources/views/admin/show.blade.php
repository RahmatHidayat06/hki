@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Detail Pengajuan HKI</h2>
    <div class="card shadow rounded-lg border-0 mb-4">
        <div class="card-body">
            <dl class="row">
                <dt class="col-md-3">Judul Karya</dt>
                <dd class="col-md-9">{{ $pengajuan->judul_karya }}</dd>
                <dt class="col-md-3">Deskripsi</dt>
                <dd class="col-md-9">{{ $pengajuan->deskripsi }}</dd>
                <dt class="col-md-3">Nama Pengusul</dt>
                <dd class="col-md-9">{{ $pengajuan->nama_pengusul }}</dd>
                <dt class="col-md-3">NIP/NIDN</dt>
                <dd class="col-md-9">{{ $pengajuan->nip_nidn }}</dd>
                <dt class="col-md-3">No HP</dt>
                <dd class="col-md-9">{{ $pengajuan->no_hp }}</dd>
                <dt class="col-md-3">ID SINTA</dt>
                <dd class="col-md-9">{{ $pengajuan->id_sinta }}</dd>
                <dt class="col-md-3">Jumlah Pencipta</dt>
                <dd class="col-md-9">{{ $pengajuan->jumlah_pencipta }}</dd>
                <dt class="col-md-3">Identitas Ciptaan</dt>
                <dd class="col-md-9">{{ $pengajuan->identitas_ciptaan }}</dd>
                <dt class="col-md-3">Sub Jenis Ciptaan</dt>
                <dd class="col-md-9">{{ $pengajuan->sub_jenis_ciptaan }}</dd>
                <dt class="col-md-3">Tanggal Pertama Kali Diumumkan</dt>
                <dd class="col-md-9">{{ $pengajuan->tanggal_pertama_kali_diumumkan }}</dd>
                <dt class="col-md-3">Tahun Usulan</dt>
                <dd class="col-md-9">{{ $pengajuan->tahun_usulan }}</dd>
                <dt class="col-md-3">Role</dt>
                <dd class="col-md-9">{{ ucfirst($pengajuan->role) }}</dd>
                <dt class="col-md-3">Status</dt>
                <dd class="col-md-9">{{ $pengajuan->status }}</dd>
                <dt class="col-md-3">File Karya</dt>
                <dd class="col-md-9">
                    @if($pengajuan->file_karya)
                        <a href="{{ asset('storage/' . $pengajuan->file_karya) }}" target="_blank">Lihat File</a>
                    @else
                        -
                    @endif
                </dd>
                <dt class="col-md-3">File Dokumen Pendukung</dt>
                <dd class="col-md-9">
                    @if($pengajuan->file_dokumen_pendukung)
                        @php
                            $dokumen = json_decode($pengajuan->file_dokumen_pendukung, true);
                        @endphp
                        @if(is_array($dokumen))
                            <ul class="mb-0">
                                @foreach($dokumen as $key => $file)
                                    <li>
                                        <strong>{{ ucwords(str_replace('_', ' ', $key)) }}:</strong>
                                        @if($file)
                                            <a href="{{ asset('storage/' . $file) }}" target="_blank">Lihat File</a>
                                        @else
                                            -
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <span class="text-danger">Format dokumen tidak valid</span>
                        @endif
                    @else
                        -
                    @endif
                </dd>
            </dl>
            <a href="{{ route('admin.pengajuan') }}" class="btn btn-secondary">Kembali</a>
            <a href="{{ route('admin.surat.pengalihan', $pengajuan->id) }}" class="btn btn-success ms-2" target="_blank">Generate Surat Pengalihan</a>
            <a href="{{ route('admin.surat.pernyataan', $pengajuan->id) }}" class="btn btn-primary ms-2" target="_blank">Generate Surat Pernyataan</a>
        </div>
    </div>
</div>
@endsection 