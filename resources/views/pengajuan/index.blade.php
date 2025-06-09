@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Daftar Pengajuan HKI</h4>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <a href="{{ route('pengajuan.create') }}" class="btn btn-primary">
                                Buat Pengajuan Baru
                            </a>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Judul Karya</th>
                                    <th>Jenis Ciptaan</th>
                                    <th>Sub Jenis Ciptaan</th>
                                    <th>Tahun Usulan</th>
                                    <th>Jumlah Pencipta</th>
                                    <th>Status</th>
                                    <th>Tanggal Pengajuan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pengajuan as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->judul_karya }}</td>
                                    <td>{{ $item->identitas_ciptaan }}</td>
                                    <td>{{ $item->sub_jenis_ciptaan }}</td>
                                    <td>{{ $item->tahun_usulan ?? '-' }}</td>
                                    <td>{{ $item->jumlah_pencipta }}</td>
                                    <td>
                                        <span class="badge bg-{{ $item->status === 'menunggu_validasi' ? 'warning' : 
                                            ($item->status === 'divalidasi' ? 'info' : 
                                            ($item->status === 'disetujui' ? 'success' : 'danger')) }}">
                                            {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                                        </span>
                                    </td>
                                    <td>{{ $item->tanggal_pengajuan ? $item->tanggal_pengajuan->format('d/m/Y H:i') : '-' }}</td>
                                    <td>
                                        <a href="{{ route('pengajuan.show', $item->id) }}" class="btn btn-info btn-sm" title="Lihat Detail">
                                            Detail
                                        </a>
                                        @if(auth()->user()->role === 'admin')
                                            <div class="d-flex flex-row gap-1 align-items-center mt-1">
                                                @if($item->status === 'menunggu_validasi')
                                                    <a href="{{ route('pengajuan.edit', $item->id) }}" class="btn btn-warning btn-sm" title="Edit Pengajuan">
                                                        Edit
                                                    </a>
                                                    <form action="{{ route('pengajuan.destroy', $item->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus Pengajuan" onclick="return confirm('Apakah Anda yakin ingin menghapus pengajuan ini?')">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        @else
                                            @if($item->status === 'menunggu_pembayaran')
                                                <a href="#" class="btn btn-success btn-sm ms-2" title="Bayar Sekarang">
                                                    Bayar Sekarang
                                                </a>
                                            @endif
                                            @if($item->status === 'disetujui' || $item->status === 'selesai')
                                                <a href="#" class="btn btn-primary btn-sm ms-2" title="Unduh Sertifikat">
                                                    Unduh Sertifikat
                                                </a>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">Tidak ada data pengajuan</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $pengajuan->links() }}
                    </div>
                    <div class="alert alert-info">
                        <strong>User ID yang login:</strong> {{ auth()->id() }}<br>
                        <strong>Jumlah data pengajuan:</strong> {{ $pengajuan->total() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
