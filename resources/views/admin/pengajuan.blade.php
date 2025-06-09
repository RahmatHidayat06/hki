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
            <form method="GET" class="row g-2 mb-4 align-items-end">
                <div class="col-md-3">
                    <label class="form-label mb-1">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="draft" {{ request('status')=='draft'?'selected':'' }}>Draft</option>
                        <option value="menunggu_validasi" {{ request('status')=='menunggu_validasi'?'selected':'' }}>Menunggu Validasi</option>
                        <option value="divalidasi" {{ request('status')=='divalidasi'?'selected':'' }}>Divalidasi</option>
                        <option value="disetujui" {{ request('status')=='disetujui'?'selected':'' }}>Disetujui</option>
                        <option value="ditolak" {{ request('status')=='ditolak'?'selected':'' }}>Ditolak</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-1">Tahun</label>
                    <select name="tahun" class="form-select">
                        <option value="">Semua Tahun</option>
                        @for($y = date('Y'); $y >= date('Y')-5; $y--)
                            <option value="{{ $y }}" {{ request('tahun')==$y?'selected':'' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label mb-1">Nama Pengusul</label>
                    <input type="text" name="nama_pengusul" class="form-control" placeholder="Cari nama..." value="{{ request('nama_pengusul') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label mb-1">Kategori</label>
                    <input type="text" name="kategori" class="form-control" placeholder="Cari kategori..." value="{{ request('kategori') }}">
                </div>
                <div class="col-md-1 d-grid">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Cari</button>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Judul Karya</th>
                            <th>Nama Pengusul</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengajuan as $item)
                        <tr>
                            <td>{{ ($pengajuan->currentPage() - 1) * $pengajuan->perPage() + $loop->iteration }}</td>
                            <td>{{ $item->judul_karya }}</td>
                            <td>{{ $item->nama_pengusul }}</td>
                            <td>{{ $item->status ?? '-' }}</td>
                            <td>
                                <a href="{{ route('admin.pengajuan.show', $item->id) }}" class="btn btn-sm btn-info">Detail</a>
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
                            <td colspan="6" class="text-center">Belum ada data pengajuan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                {{ $pengajuan->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 