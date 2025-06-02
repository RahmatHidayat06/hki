@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow rounded-lg border-0 bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title mb-1">Total Pengajuan</h5>
                    <h2 class="mb-0">{{ $total }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow rounded-lg border-0 bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title mb-1">Pengajuan Lengkap</h5>
                    <h2 class="mb-0">{{ $totalLengkap }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4 d-flex align-items-center">
            <form action="{{ route('admin.rekap') }}" method="GET" class="w-100">
                <button type="submit" class="btn btn-warning w-100" {{ $total === 0 || $total !== $totalLengkap ? 'disabled' : '' }}>
                    <i class="bi bi-download"></i> Rekap Data (Excel)
                </button>
            </form>
        </div>
    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <div class="card shadow rounded-lg border-0">
        <div class="card-body">
            <h5 class="card-title mb-3">Daftar Semua Pengajuan HKI</h5>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th>No</th>
                            <th>Judul Karya</th>
                            <th>Kategori</th>
                            <th>Jenis Ciptaan</th>
                            <th>Sub Jenis</th>
                            <th>Pengusul</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengajuan as $item)
                        <tr>
                            <td>{{ ($pengajuan->currentPage() - 1) * $pengajuan->perPage() + $loop->iteration }}</td>
                            <td>{{ $item->judul_karya }}</td>
                            <td>{{ $item->kategori }}</td>
                            <td>{{ $item->identitas_ciptaan }}</td>
                            <td>{{ $item->sub_jenis_ciptaan }}</td>
                            <td>{{ $item->nama_pengusul }}</td>
                            <td>
                                <span class="badge bg-{{ $item->status === 'menunggu_validasi' ? 'warning' : ($item->status === 'divalidasi' ? 'info' : ($item->status === 'disetujui' ? 'success' : 'danger')) }}">
                                    {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                                </span>
                            </td>
                            <td>{{ $item->tanggal_pengajuan ? $item->tanggal_pengajuan->format('d/m/Y H:i') : '-' }}</td>
                            <td>
                                <a href="{{ route('pengajuan.show', $item->id) }}" class="btn btn-info btn-sm mb-1">Detail</a>
                                @if($item->status === 'menunggu_validasi')
                                    <a href="{{ route('validasi.show', $item->id) }}" class="btn btn-success btn-sm mb-1">Validasi</a>
                                    <a href="{{ route('pengajuan.edit', $item->id) }}" class="btn btn-warning btn-sm mb-1">Edit</a>
                                    <form action="{{ route('pengajuan.destroy', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm mb-1" onclick="return confirm('Yakin hapus data?')">Hapus</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">Tidak ada data pengajuan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $pengajuan->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 