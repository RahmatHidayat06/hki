@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Detail Pengajuan HKI</h4>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Judul Karya</dt>
                        <dd class="col-sm-8">{{ $pengajuan->judul_karya }}</dd>
                        <dt class="col-sm-4">Kategori</dt>
                        <dd class="col-sm-8">{{ $pengajuan->kategori }}</dd>
                        <dt class="col-sm-4">Deskripsi</dt>
                        <dd class="col-sm-8">{{ $pengajuan->deskripsi }}</dd>
                        <dt class="col-sm-4">Jenis Ciptaan</dt>
                        <dd class="col-sm-8">{{ $pengajuan->identitas_ciptaan }}</dd>
                        <dt class="col-sm-4">Sub Jenis Ciptaan</dt>
                        <dd class="col-sm-8">{{ $pengajuan->sub_jenis_ciptaan }}</dd>
                        <dt class="col-sm-4">Tahun Usulan</dt>
                        <dd class="col-sm-8">{{ $pengajuan->tahun_usulan ?? '-' }}</dd>
                        <dt class="col-sm-4">Jumlah Pencipta</dt>
                        <dd class="col-sm-8">{{ $pengajuan->jumlah_pencipta }}</dd>
                        <dt class="col-sm-4">Status</dt>
                        <dd class="col-sm-8">{{ ucfirst(str_replace('_', ' ', $pengajuan->status)) }}</dd>
                        <dt class="col-sm-4">Tanggal Pengajuan</dt>
                        <dd class="col-sm-8">{{ $pengajuan->tanggal_pengajuan }}</dd>
                    </dl>
                    <hr>
                    <h5>Data Pencipta</h5>
                    @foreach($pengajuan->pengaju as $pencipta)
                        <div class="mb-3 border rounded p-3">
                            <strong>Nama:</strong> {{ $pencipta->nama }}<br>
                            <strong>Email:</strong> {{ $pencipta->email }}<br>
                            <strong>No HP:</strong> {{ $pencipta->no_hp }}<br>
                            <strong>Alamat:</strong> {{ $pencipta->alamat }}<br>
                            <strong>Kecamatan:</strong> {{ $pencipta->kecamatan }}<br>
                            <strong>Kode Pos:</strong> {{ $pencipta->kodepos }}<br>
                        </div>
                    @endforeach
                    <a href="{{ route('pengajuan.index') }}" class="btn btn-secondary mt-3">Kembali ke Daftar</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 