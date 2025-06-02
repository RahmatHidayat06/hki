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
                    <hr>
                    <h5>Dokumen Pendukung</h5>
                    <div class="mb-3">
                        <strong>File Karya:</strong>
                        @if($pengajuan->file_karya)
                            @php
                                $isUrl = filter_var($pengajuan->file_karya, FILTER_VALIDATE_URL);
                            @endphp
                            @if($isUrl)
                                <a href="{{ $pengajuan->file_karya }}" class="btn btn-primary btn-sm ms-2" target="_blank">Lihat/Download Link Karya</a>
                            @else
                                @php
                                    $ext = pathinfo($pengajuan->file_karya, PATHINFO_EXTENSION);
                                    $url = Storage::url($pengajuan->file_karya);
                                @endphp
                                @if(in_array(strtolower($ext), ['jpg','jpeg','png','gif','svg','webp']))
                                    <div class="my-2"><img src="{{ $url }}" alt="File Karya" class="img-fluid rounded border"></div>
                                @elseif(in_array(strtolower($ext), ['mp4','webm','ogg']))
                                    <div class="my-2"><video src="{{ $url }}" controls class="w-100 rounded border"></video></div>
                                @elseif(in_array(strtolower($ext), ['mp3','wav','ogg']))
                                    <div class="my-2"><audio src="{{ $url }}" controls class="w-100"></audio></div>
                                @elseif(strtolower($ext) === 'pdf')
                                    <div class="my-2"><iframe src="{{ $url }}" width="100%" height="500px" style="border:1px solid #ccc;"></iframe></div>
                                @else
                                    <a href="{{ $url }}" class="btn btn-primary btn-sm ms-2" target="_blank">Download File Karya</a>
                                @endif
                            @endif
                        @else
                            <span class="text-muted">Tidak ada file</span>
                        @endif
                    </div>
                    @php $dokumen = is_string($pengajuan->file_dokumen_pendukung) ? json_decode($pengajuan->file_dokumen_pendukung, true) : ($pengajuan->file_dokumen_pendukung ?? []); @endphp
                    <div class="mb-3">
                        <strong>Surat Pengalihan Hak Cipta:</strong>
                        @if(isset($dokumen['surat_pengalihan']) && $dokumen['surat_pengalihan'])
                            @php $url = Storage::url($dokumen['surat_pengalihan']); $ext = pathinfo($dokumen['surat_pengalihan'], PATHINFO_EXTENSION); @endphp
                            @if(strtolower($ext) === 'pdf')
                                <div class="my-2"><iframe src="{{ $url }}" width="100%" height="500px" style="border:1px solid #ccc;"></iframe></div>
                            @else
                                <a href="{{ $url }}" class="btn btn-primary btn-sm ms-2" target="_blank">Download Surat Pengalihan</a>
                            @endif
                        @else
                            <span class="text-muted">Tidak ada file</span>
                        @endif
                    </div>
                    <div class="mb-3">
                        <strong>Surat Pernyataan Hak Cipta:</strong>
                        @if(isset($dokumen['surat_pernyataan']) && $dokumen['surat_pernyataan'])
                            @php $url = Storage::url($dokumen['surat_pernyataan']); $ext = pathinfo($dokumen['surat_pernyataan'], PATHINFO_EXTENSION); @endphp
                            @if(strtolower($ext) === 'pdf')
                                <div class="my-2"><iframe src="{{ $url }}" width="100%" height="500px" style="border:1px solid #ccc;"></iframe></div>
                            @else
                                <a href="{{ $url }}" class="btn btn-primary btn-sm ms-2" target="_blank">Download Surat Pernyataan</a>
                            @endif
                        @else
                            <span class="text-muted">Tidak ada file</span>
                        @endif
                    </div>
                    <div class="mb-3">
                        <strong>KTP Seluruh Pencipta:</strong>
                        @if(isset($dokumen['ktp']) && $dokumen['ktp'])
                            @php $url = Storage::url($dokumen['ktp']); $ext = pathinfo($dokumen['ktp'], PATHINFO_EXTENSION); @endphp
                            @if(strtolower($ext) === 'pdf')
                                <div class="my-2"><iframe src="{{ $url }}" width="100%" height="500px" style="border:1px solid #ccc;"></iframe></div>
                            @else
                                <a href="{{ $url }}" class="btn btn-primary btn-sm ms-2" target="_blank">Download KTP</a>
                            @endif
                        @else
                            <span class="text-muted">Tidak ada file</span>
                        @endif
                    </div>
                    @if(auth()->user()->role === 'admin' && $pengajuan->status === 'menunggu_validasi')
                    <hr>
                    <div class="mb-3">
                        <a href="{{ route('pengajuan.edit', $pengajuan->id) }}" class="btn btn-warning">Edit Data Pengajuan</a>
                    </div>
                    <form action="{{ route('validasi.validasi', $pengajuan->id) }}" method="POST" class="mt-3">
                        @csrf
                        <div class="mb-3">
                            <label for="status_validasi" class="form-label">Aksi Validasi</label>
                            <select id="status_validasi" name="status_validasi" class="form-select" required>
                                <option value="">Pilih Aksi</option>
                                <option value="disetujui">Setujui</option>
                                <option value="ditolak">Tolak</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="catatan_validasi" class="form-label">Catatan (wajib diisi jika menolak)</label>
                            <textarea id="catatan_validasi" name="catatan_validasi" class="form-control" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">Simpan Validasi</button>
                    </form>
                    @endif
                    <a href="{{ route('pengajuan.index') }}" class="btn btn-secondary mt-3">Kembali ke Daftar</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 