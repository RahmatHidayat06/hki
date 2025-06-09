@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card shadow rounded-lg border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Validasi Pengajuan HKI</h4>
                    <a href="{{ route('validasi.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Nomor Pengajuan</dt>
                        <dd class="col-sm-8">{{ $pengajuan->nomor_pengajuan ?? '-' }}</dd>
                        <dt class="col-sm-4">Judul Karya</dt>
                        <dd class="col-sm-8">{{ $pengajuan->judul_karya ?? '-' }}</dd>
                        <dt class="col-sm-4">Deskripsi</dt>
                        <dd class="col-sm-8">{{ $pengajuan->deskripsi ?? '-' }}</dd>
                        <dt class="col-sm-4">Jenis Ciptaan</dt>
                        <dd class="col-sm-8">{{ $pengajuan->identitas_ciptaan ?? '-' }}</dd>
                        <dt class="col-sm-4">Sub Jenis Ciptaan</dt>
                        <dd class="col-sm-8">{{ $pengajuan->sub_jenis_ciptaan ?? '-' }}</dd>
                        <dt class="col-sm-4">Tahun Usulan</dt>
                        <dd class="col-sm-8">{{ $pengajuan->tahun_usulan ?? '-' }}</dd>
                        <dt class="col-sm-4">Jumlah Pencipta</dt>
                        <dd class="col-sm-8">{{ $pengajuan->jumlah_pencipta ?? '-' }}</dd>
                        <dt class="col-sm-4">Status</dt>
                        <dd class="col-sm-8">{{ ucfirst(str_replace('_', ' ', $pengajuan->status)) }}</dd>
                        <dt class="col-sm-4">Tanggal Pengajuan</dt>
                        <dd class="col-sm-8">{{ $pengajuan->tanggal_pengajuan ? $pengajuan->tanggal_pengajuan->format('d/m/Y H:i') : '-' }}</dd>
                        <dt class="col-sm-4">Nama Pengusul</dt>
                        <dd class="col-sm-8">{{ $pengajuan->nama_pengusul ?? '-' }}</dd>
                        <dt class="col-sm-4">NIP/NIDN</dt>
                        <dd class="col-sm-8">{{ $pengajuan->nip_nidn ?? '-' }}</dd>
                        <dt class="col-sm-4">No HP</dt>
                        <dd class="col-sm-8">{{ $pengajuan->no_hp ?? '-' }}</dd>
                        <dt class="col-sm-4">ID Sinta</dt>
                        <dd class="col-sm-8">{{ $pengajuan->id_sinta ?? '-' }}</dd>
                        <dt class="col-sm-4">Tanggal Pertama Kali Diumumkan</dt>
                        <dd class="col-sm-8">{{ $pengajuan->tanggal_pertama_kali_diumumkan ?? '-' }}</dd>
                        <dt class="col-sm-4">Role</dt>
                        <dd class="col-sm-8">{{ ucfirst($pengajuan->role ?? '-') }}</dd>
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
                    <h5>Berkas/Dokumen</h5>
                    <div class="mb-3">
                        <strong>File Karya:</strong>
                        @if($pengajuan->file_karya)
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
                    <hr>
                    @if(auth()->user()->role === 'admin')
                    <form action="{{ route('validasi.update', $pengajuan) }}" method="POST" class="mt-4">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="status" class="form-label">Status Validasi</label>
                            <select id="status" name="status" class="form-select" required>
                                <option value="">Pilih Status</option>
                                <option value="approved">Setujui</option>
                                <option value="rejected">Tolak</option>
                            </select>
                            @error('status')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="catatan" class="form-label">Catatan</label>
                            <textarea id="catatan" name="catatan" rows="4" class="form-control" required></textarea>
                            @error('catatan')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <button type="submit" class="btn btn-success">Simpan Validasi</button>
                    </form>
                    @else
                    <div class="mt-4">
                        <div class="mb-2"><strong>Status Validasi:</strong> {{ ucfirst($pengajuan->status_validasi ?? '-') }}</div>
                        <div><strong>Catatan:</strong> {{ $pengajuan->catatan_validasi ?? '-' }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection