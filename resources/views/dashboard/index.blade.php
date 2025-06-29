@extends('layouts.app')

@section('content')

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow rounded-lg border-0" style="background: linear-gradient(90deg, #002366 0%, #0a2a6c 100%);">
                <div class="card-body d-flex align-items-center justify-content-between py-4">
                    <div>
                        <h5 class="card-title mb-1 text-white" style="font-weight: 600;">Hak Cipta Terdaftar</h5>
                        <h1 class="display-4 mb-0" style="color: #FFD600; font-size: 3rem; font-weight: bold;">{{ $totalPengajuan ?? 0 }}</h1>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 mb-4">
            <div class="card shadow rounded-lg border-0">
                <div class="card-body py-4">
                    <h5 class="card-title mb-3" style="font-weight: 600; color: #002366;">Hak Cipta Yang Tidak Lengkap</h5>
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0 align-middle">
                            <thead class="bg-light">
                                <tr style="font-weight: 600; color: #002366;">
                                    <th>Pengguna</th>
                                    <th>Judul</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tidakLengkap as $item)
                                    <tr>
                                        <td>{{ $item->user->name ?? '-' }}</td>
                                        <td>{{ $item->judul_karya }}</td>
                                        <td>{{ $item->created_at->format('d/m/Y') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted">Tidak ada data</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 mb-4">
            <div class="card shadow rounded-lg border-0">
                <div class="card-body py-4">
                    <h5 class="card-title mb-3" style="font-weight: 600; color: #002366;">Hak Cipta Yang Belum Disetujui</h5>
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0 align-middle">
                            <thead class="bg-light">
                                <tr style="font-weight: 600; color: #002366;">
                                    <th>Pengguna</th>
                                    <th>Judul</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($belumDisetujui as $item)
                                    <tr>
                                        <td>{{ $item->user->name ?? '-' }}</td>
                                        <td>{{ $item->judul_karya }}</td>
                                        <td>{{ $item->created_at->format('d/m/Y') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted">Tidak ada data</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 