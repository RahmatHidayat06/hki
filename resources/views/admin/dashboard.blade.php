@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4 g-3">
        <div class="col-md-4">
            <div class="card shadow rounded-lg border-0 bg-primary text-white h-100">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <h5 class="card-title mb-1">Total Pengajuan</h5>
                    <h2 class="mb-0">{{ $total ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow rounded-lg border-0 bg-success text-white h-100">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <h5 class="card-title mb-1">Pengajuan Lengkap</h5>
                    <h2 class="mb-0">{{ $totalLengkap ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4 d-flex align-items-center">
            <form action="{{ route('admin.rekap') }}" method="GET" class="w-100">
                <button type="submit" class="btn btn-warning w-100 py-3 fw-bold shadow-sm" {{ ($total ?? 0) === 0 || ($total ?? 0) !== ($totalLengkap ?? 0) ? 'disabled' : '' }}>
                    <i class="fas fa-file-excel me-2"></i> Rekap Data (Excel)
                </button>
            </form>
        </div>
    </div>
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card shadow rounded-lg border-0 mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">Hak Cipta Yang Tidak Lengkap</h5>
                    <table class="table table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Pengguna</th>
                                <th>Judul</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="3" class="text-center text-muted">Tidak ada data</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow rounded-lg border-0 mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">Hak Cipta Yang Belum Disetujui</h5>
                    <table class="table table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Pengguna</th>
                                <th>Judul</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="3" class="text-center text-muted">Tidak ada data</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 