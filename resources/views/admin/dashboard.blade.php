@extends('layouts.app')

@section('content')
<x-page-header 
    title="Dashboard Admin" 
    description="Ikhtisar kinerja dan status pengajuan HKI"
    icon="fas fa-chart-bar"
/>

<div class="container-fluid px-4">
    <!-- Statistik Cards -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <!-- Baris Pertama: Status Workflow (5 card) - Mengikuti alur proses -->
    <div class="row mb-3 g-3">
        <!-- Step 1: Menunggu Validasi -->
        <div class="col-6 col-lg-2 col-xl-2">
            <div class="card border-0 shadow-sm text-center h-100" style="background-color: #fff3cd; border-left: 4px solid #856404;">
                <div class="card-body py-3">
                    <div class="mb-2">
                        <i class="fas fa-hourglass-half fa-2x" style="color: #856404;"></i>
                        <div class="badge bg-warning text-dark position-absolute top-0 start-50 translate-middle px-2 py-1" style="font-size: 0.65rem;">STEP 1</div>
                    </div>
                    <div class="fw-semibold" style="color: #856404;">Menunggu Validasi</div>
                    <div class="fs-4 fw-bold" style="color: #856404;">{{ $totalMenunggu ?? 0 }}</div>
                </div>
            </div>
        </div>
        
        <!-- Step 2: Divalidasi -->
        <div class="col-6 col-lg-2 col-xl-2">
            <div class="card border-0 shadow-sm text-center h-100" style="background-color: #d1ecf1; border-left: 4px solid #0c5460;">
                <div class="card-body py-3">
                    <div class="mb-2">
                        <i class="fas fa-check-circle fa-2x" style="color: #0c5460;"></i>
                        <div class="badge bg-info text-white position-absolute top-0 start-50 translate-middle px-2 py-1" style="font-size: 0.65rem;">STEP 2</div>
                    </div>
                    <div class="fw-semibold" style="color: #0c5460;">Divalidasi</div>
                    <div class="fs-4 fw-bold" style="color: #0c5460;">{{ $totalDivalidasi ?? 0 }}</div>
                </div>
            </div>
        </div>
        
        <!-- Step 3: Sedang Di Proses -->
        <div class="col-6 col-lg-2 col-xl-2">
            <div class="card border-0 shadow-sm text-center h-100" style="background-color: #e7f3ff; border-left: 4px solid #0056b3;">
                <div class="card-body py-3">
                    <div class="mb-2">
                        <i class="fas fa-cogs fa-2x" style="color: #0056b3;"></i>
                        <div class="badge text-white position-absolute top-0 start-50 translate-middle px-2 py-1" style="background-color: #0056b3; font-size: 0.65rem;">STEP 3</div>
                    </div>
                    <div class="fw-semibold" style="color: #0056b3;">Sedang Di Proses</div>
                    <div class="fs-4 fw-bold" style="color: #0056b3;">{{ $totalSedangDiProses ?? 0 }}</div>
                </div>
            </div>
        </div>
        
        <!-- Step 4: Menunggu Pembayaran -->
        <div class="col-6 col-lg-2 col-xl-2">
            <div class="card border-0 shadow-sm text-center h-100" style="background-color: #e2e3e5; border-left: 4px solid #41464b;">
                <div class="card-body py-3">
                    <div class="mb-2">
                        <i class="fas fa-wallet fa-2x" style="color: #41464b;"></i>
                        <div class="badge bg-secondary text-white position-absolute top-0 start-50 translate-middle px-2 py-1" style="font-size: 0.65rem;">STEP 4</div>
                    </div>
                    <div class="fw-semibold" style="color: #41464b;">Menunggu Pembayaran</div>
                    <div class="fs-4 fw-bold" style="color: #41464b;">{{ $totalMenungguPembayaran ?? 0 }}</div>
                </div>
            </div>
        </div>
        
        <!-- Step 5: Verifikasi Pembayaran -->
        <div class="col-6 col-lg-2 col-xl-2">
            <div class="card border-0 shadow-sm text-center h-100" style="background-color: #fce4ec; border-left: 4px solid #880e4f;">
                <div class="card-body py-3">
                    <div class="mb-2">
                        <i class="fas fa-search-dollar fa-2x" style="color: #880e4f;"></i>
                        <div class="badge text-white position-absolute top-0 start-50 translate-middle px-2 py-1" style="background-color: #880e4f; font-size: 0.65rem;">STEP 5</div>
                    </div>
                    <div class="fw-semibold" style="color: #880e4f; font-size: 0.85rem;">Verifikasi Pembayaran</div>
                    <div class="fs-4 fw-bold" style="color: #880e4f;">{{ $totalMenungguVerifikasi ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Baris Kedua: Status Final & Total (3 card) -->
    <div class="row mb-4 g-3">
        <!-- Status Selesai -->
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm text-center h-100" style="background-color: #d4edda; border-left: 4px solid #155724;">
                <div class="card-body py-3">
                    <div class="mb-2">
                        <i class="fas fa-check-double fa-2x" style="color: #155724;"></i>
                        <div class="badge bg-success text-white position-absolute top-0 start-50 translate-middle px-2 py-1" style="font-size: 0.65rem;">FINAL</div>
                    </div>
                    <div class="fw-semibold" style="color: #155724;">Selesai</div>
                    <div class="fs-4 fw-bold" style="color: #155724;">{{ $totalSelesai ?? 0 }}</div>
                </div>
            </div>
        </div>
        
        <!-- Status Ditolak -->
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm text-center h-100" style="background-color: #f8d7da; border-left: 4px solid #721c24;">
                <div class="card-body py-3">
                    <div class="mb-2">
                        <i class="fas fa-times-circle fa-2x" style="color: #721c24;"></i>
                        <div class="badge bg-danger text-white position-absolute top-0 start-50 translate-middle px-2 py-1" style="font-size: 0.65rem;">REJECT</div>
                    </div>
                    <div class="fw-semibold" style="color: #721c24;">Ditolak</div>
                    <div class="fs-4 fw-bold" style="color: #721c24;">{{ $totalDitolak ?? 0 }}</div>
                </div>
            </div>
        </div>
        
        <!-- Total Pengajuan -->
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm text-center h-100" style="background-color: #e3f2fd; border-left: 4px solid #1976d2;">
                <div class="card-body py-3">
                    <div class="mb-2">
                        <i class="fas fa-chart-bar fa-2x" style="color: #1976d2;"></i>
                        <div class="badge bg-primary text-white position-absolute top-0 start-50 translate-middle px-2 py-1" style="font-size: 0.65rem;">TOTAL</div>
                    </div>
                    <div class="fw-semibold" style="color: #1976d2;">Total Pengajuan</div>
                    <div class="fs-4 fw-bold" style="color: #1976d2;">{{ $total ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>
    <!-- Baris Ketiga: Summary & Actions -->
    <div class="row mb-4 g-3">
        <div class="col-md-6">
            <div class="card shadow rounded-lg border-0 text-white h-100" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border-left: 4px solid #155724;">
                <div class="card-body d-flex flex-column justify-content-center align-items-center py-4">
                    <div class="mb-2"><i class="fas fa-check-circle fa-3x text-white opacity-75"></i></div>
                    <h5 class="card-title mb-1 fw-bold">Pengajuan Lengkap</h5>
                    <h2 class="mb-0 fw-bold">{{ $totalLengkap ?? 0 }}</h2>
                    <small class="opacity-75 mt-1">dari {{ $total ?? 0 }} total pengajuan</small>
                </div>
            </div>
        </div>
        <div class="col-md-6 d-flex align-items-center">
            <div class="card shadow rounded-lg border-0 w-100 h-100" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%); border-left: 4px solid #d39e00;">
                <div class="card-body d-flex flex-column justify-content-center align-items-center py-4">
                    <div class="mb-2"><i class="fas fa-download fa-2x text-white"></i></div>
            <form action="{{ route('admin.rekap') }}" method="GET" class="w-100">
                        <button type="submit" class="btn btn-light w-100 py-2 fw-bold shadow-sm border-0" {{ ($total ?? 0) === 0 || ($total ?? 0) !== ($totalLengkap ?? 0) ? 'disabled' : '' }}>
                            <i class="fas fa-file-excel me-2 text-success"></i> 
                            Rekap Data (Excel)
                </button>
            </form>
                    <small class="text-white opacity-75 mt-2">
                        @if(($total ?? 0) === 0 || ($total ?? 0) !== ($totalLengkap ?? 0))
                            Tersedia setelah semua data lengkap
                        @else
                            Siap untuk diunduh
                        @endif
                    </small>
                </div>
            </div>
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
    <!-- Grafik Pengajuan 30 Hari Terakhir -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="bg-white shadow rounded-lg p-4">
                <h5 class="text-lg font-semibold mb-3">Grafik Pengajuan 30 Hari Terakhir</h5>
                <canvas id="pengajuanChart" class="w-full" style="height:260px" data-labels='@json($labels ?? [])' data-values='@json($data ?? [])'></canvas>
            </div>
        </div>
    </div>
</div>

<style>
.card{transition:all .3s ease}.card:hover{transform:translateY(-2px)}.table-responsive{border-radius:.5rem}.table th{font-weight:600;text-transform:uppercase;font-size:.75rem;letter-spacing:.5px}.table td{vertical-align:middle}.badge{font-weight:500;letter-spacing:.25px}.btn{font-weight:500;border-radius:.375rem;transition:all .2s ease}.btn:hover{transform:translateY(-1px)}.form-control:focus{border-color:#0d6efd;box-shadow:0 0 0 .2rem rgba(13,110,253,.25)}.input-group-text{background-color:#f8f9fa;border-color:#dee2e6}

/* Status Cards Responsiveness */
@media (max-width: 768px) {
    .card-body {
        padding: 1rem 0.75rem !important;
    }
    .fs-4 {
        font-size: 1.75rem !important;
    }
    .fa-2x {
        font-size: 1.5em !important;
    }
    .fw-semibold {
        font-size: 0.85rem !important;
    }
}

@media (min-width: 769px) and (max-width: 1199px) {
    .fw-semibold {
        font-size: 0.9rem;
    }
}

/* Gradient card hover effects */
.card[style*="gradient"]:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

/* Ensure equal height for all status cards */
.row .card {
    min-height: 120px;
}

/* Better button spacing in gradient cards */
.card[style*="gradient"] .btn {
    margin-top: 0.5rem;
}

/* Status badge positioning */
.card {
    position: relative;
    overflow: visible;
}

.card .badge.position-absolute {
    z-index: 10;
    border: 2px solid white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    font-weight: 600;
    letter-spacing: 0.5px;
}

/* Mobile badge adjustments */
@media (max-width: 768px) {
    .card .badge.position-absolute {
        font-size: 0.6rem !important;
        padding: 0.25rem 0.5rem !important;
    }
}
</style>
@endsection

@push('scripts')
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const canvasEl = document.getElementById('pengajuanChart');
            const labels = JSON.parse(canvasEl.dataset.labels || '[]');
            const dataPengajuan = JSON.parse(canvasEl.dataset.values || '[]');

            const ctx = canvasEl.getContext('2d');
            const chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Pengajuan',
                        data: dataPengajuan,
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.15)',
                        fill: false,
                        tension: 0.35,
                        pointRadius: 3,
                        pointBackgroundColor: '#2563eb'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    animation: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush 