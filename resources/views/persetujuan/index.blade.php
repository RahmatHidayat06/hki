@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Daftar Persetujuan HKI</h4>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Judul</th>
                                    <th>Jenis HKI</th>
                                    <th>Status</th>
                                    <th>Tanggal Validasi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pengajuan as $item)
                                    <tr>
                                        <td>{{ $item->judul }}</td>
                                        <td>{{ $item->jenis_hki }}</td>
                                        <td>
                                            <span class="badge 
                                                @if($item->status === 'validated') bg-success
                                                @elseif($item->status === 'approved') bg-primary
                                                @elseif($item->status === 'rejected') bg-danger
                                                @else bg-secondary @endif">
                                                @if($item->status === 'validated') Tervalidasi
                                                @elseif($item->status === 'approved') Disetujui
                                                @elseif($item->status === 'rejected') Ditolak
                                                @else {{ ucfirst($item->status) }} @endif
                                            </span>
                                        </td>
                                        <td>{{ $item->validated_at ? $item->validated_at->format('d/m/Y H:i') : '-' }}</td>
                                        <td>
                                            <a href="{{ route('persetujuan.show', $item) }}" class="btn btn-sm btn-info">Lihat Detail</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $pengajuan->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection