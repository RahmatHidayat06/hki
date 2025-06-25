@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow rounded-lg border-0">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Validasi Pengajuan HKI</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if($pengajuan->isEmpty())
                        <div class="text-center py-4">
                            <p class="text-muted">Tidak ada pengajuan yang perlu divalidasi.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Judul</th>
                                        <th>Jenis HKI</th>
                                        <th>Pengaju</th>
                                        <th>Tanggal</th>
                                        <th>Finalisasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pengajuan as $item)
                                    <tr>
                                        <td>{{ $item->judul ?? '-' }}</td>
                                        <td>{{ $item->jenis_hki ?? '-' }}</td>
                                        <td>{{ $item->user->name ?? '-' }}</td>
                                        <td>{{ $item->created_at ? $item->created_at->format('d/m/Y') : '-' }}</td>
                                        <td>
                                            <form action="{{ route('validasi.finalize', $item) }}" method="POST" onsubmit="return confirm('Finalisasi validasi dan ubah status menjadi Menunggu Pembayaran?');">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-primary btn-sm">Finalisasi</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection