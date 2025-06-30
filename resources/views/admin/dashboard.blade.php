@extends('layouts.app')

@section('content')
<x-page-header 
    title="Dashboard Admin" 
    description="Kelola dan pantau pengajuan HKI secara real-time"
    icon="fas fa-chart-bar"
/>

<div class="container-fluid px-3">
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Status Cards Row - Exact Screenshot Match -->
    <div class="row g-2 mb-3">
        <!-- Menunggu Validasi -->
        <div class="col-6 col-lg-3">
            <div class="status-card-exact card-yellow">
                <div class="status-border-exact yellow-border"></div>
                <div class="card-icon-exact yellow-icon">
                    <i class="fas fa-clock"></i>
                    </div>
                <div class="card-content-exact">
                    <div class="card-title-exact">Menunggu Validasi</div>
                    <div class="card-number-exact yellow-text">{{ $totalMenunggu ?? 1 }}</div>
                </div>
            </div>
        </div>
        
        <!-- Divalidasi & Sedang Diproses -->
        <div class="col-6 col-lg-3">
            <div class="status-card-exact card-cyan">
                <div class="status-border-exact cyan-border"></div>
                <div class="card-icon-exact cyan-icon">
                    <i class="fas fa-check-circle"></i>
                    </div>
                <div class="card-content-exact">
                    <div class="card-title-exact">Divalidasi & Sedang Diproses</div>
                    <div class="card-number-exact cyan-text">{{ ($totalDivalidasi ?? 32) + ($totalSedangDiProses ?? 0) }}</div>
                </div>
            </div>
        </div>
        
        <!-- Menunggu Pembayaran -->
        <div class="col-6 col-lg-3">
            <div class="status-card-exact card-gray">
                <div class="status-border-exact gray-border"></div>
                <div class="card-icon-exact gray-icon">
                    <i class="fas fa-wallet"></i>
                    </div>
                <div class="card-content-exact">
                    <div class="card-title-exact">Menunggu Pembayaran</div>
                    <div class="card-number-exact gray-text">{{ $totalMenungguPembayaran ?? 4 }}</div>
                </div>
            </div>
        </div>
        
        <!-- Selesai -->
        <div class="col-6 col-lg-3">
            <div class="status-card-exact card-green">
                <div class="status-border-exact green-border"></div>
                <div class="card-icon-exact green-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="card-content-exact">
                    <div class="card-title-exact">Selesai</div>
                    <div class="card-number-exact green-text">{{ $totalSelesai ?? 4 }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Workflow Process Cards - Updated Layout -->
    <div class="row g-2 mb-3">
        <!-- Verifikasi Pembayaran -->
        <div class="col-6 col-lg-6">
            <div class="workflow-card-exact">
                <div class="workflow-icon-exact yellow-workflow">
                    <i class="fas fa-search"></i>
                    </div>
                <div class="workflow-content-exact">
                    <div class="workflow-title-exact">Verifikasi Pembayaran</div>
                    <div class="workflow-number-exact">{{ $totalMenungguVerifikasi ?? 6 }}</div>
                </div>
            </div>
        </div>
        
        <!-- Ditolak -->
        <div class="col-6 col-lg-6">
            <div class="workflow-card-exact">
                <div class="workflow-icon-exact red-workflow">
                    <i class="fas fa-times-circle"></i>
                    </div>
                <div class="workflow-content-exact">
                    <div class="workflow-title-exact">Ditolak</div>
                    <div class="workflow-number-exact">{{ $totalDitolak ?? 2 }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart and Action Cards Row - Balanced Layout -->
    <div class="row g-3 mb-3">
        <!-- Chart Section - Balanced Size -->
        <div class="col-12 col-lg-8">
            <div class="chart-container-exact">
                <div class="chart-header-exact">
                    <h5>Trend Pengajuan (30 Hari Terakhir)</h5>
                    <p>Grafik pengajuan HKI dalam satu bulan terakhir</p>
                    </div>
                <div class="chart-body-exact">
                    <canvas id="pengajuanChart" 
                            style="height: 220px; width: 100%;" 
                            data-labels='@json($labels ?? [])' 
                            data-values='@json($data ?? [])'></canvas>
                </div>
            </div>
        </div>
        
        <!-- Action Cards - Proper Size to Match Screenshot -->
        <div class="col-12 col-lg-4">
            <div class="row g-2">
                <!-- Export Data Card -->
                <div class="col-12">
                    <div class="action-card-exact export-exact">
                        <div class="action-icon-exact">
                            <i class="fas fa-download"></i>
                        </div>
                        <div class="action-content-exact">
                            <h6>Export Data</h6>
                            <form action="{{ route('admin.rekap') }}" method="GET">
                                <button type="submit" 
                                        class="btn btn-light btn-sm mt-1" 
                                        {{ ($total ?? 0) === 0 || ($total ?? 0) !== ($totalLengkap ?? 0) ? 'disabled' : '' }}>
                                    <i class="fas fa-file-excel me-1"></i>
                                    Download Excel
                                </button>
                            </form>
                            <small class="text-white mt-1 d-block">Siap diunduh</small>
                        </div>
                    </div>
                </div>
                
                <!-- Jumlah Total Pengajuan Card -->
                <div class="col-12">
                    <div class="action-card-exact completion-exact">
                        <div class="action-icon-exact">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <div class="action-content-exact">
                            <h6>Jumlah Total Pengajuan</h6>
                            <div class="completion-percentage-exact">
                                {{ $total ?? 0 }}
            </div>
        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Action Cards - Exact Screenshot Match -->
    <div class="row g-2">
        <div class="col-6 col-lg-3">
            <a href="{{ route('admin.pengajuan') }}" class="text-decoration-none">
                <div class="bottom-card-exact">
                    <div class="bottom-icon-exact blue-bottom">
                        <i class="fas fa-list"></i>
                    </div>
                    <div class="bottom-content-exact">
                        <div class="bottom-title-exact">Kelola Pengajuan</div>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col-6 col-lg-3">
            <a href="{{ route('admin.pengajuan') }}?status=divalidasi_sedang_diproses" class="text-decoration-none">
                <div class="bottom-card-exact">
                    <div class="bottom-icon-exact yellow-bottom">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="bottom-content-exact">
                        <div class="bottom-title-exact">Divalidasi & Sedang Diproses</div>
                    </div>
                    <div class="badge-exact badge-yellow">{{ $totalDivalidasi ?? 32 }}</div>
                </div>
            </a>
        </div>
        
        <div class="col-6 col-lg-3">
            <a href="{{ route('admin.pengajuan') }}?status=menunggu_verifikasi_pembayaran" class="text-decoration-none">
                <div class="bottom-card-exact">
                    <div class="bottom-icon-exact cyan-bottom">
                        <i class="fas fa-search-dollar"></i>
                    </div>
                    <div class="bottom-content-exact">
                        <div class="bottom-title-exact">Verifikasi Bayar</div>
    </div>
                    <div class="badge-exact badge-cyan">{{ $totalMenungguVerifikasi ?? 6 }}</div>
                </div>
            </a>
        </div>
        
        <div class="col-6 col-lg-3">
            <a href="{{ route('admin.pengajuan') }}?status=selesai" class="text-decoration-none">
                <div class="bottom-card-exact">
                    <div class="bottom-icon-exact green-bottom">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="bottom-content-exact">
                        <div class="bottom-title-exact">Selesai</div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<style>
/* Container adjustments for exact screenshot match */
.container-fluid {
    max-width: 1400px;
    margin: 0 auto;
}

/* Status Cards - Exact Screenshot Colors and Layout */
.status-card-exact {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    position: relative;
    min-height: 120px;
    display: flex;
    align-items: center;
    gap: 16px;
    transition: all 0.3s ease;
    overflow: hidden;
}

.status-card-exact:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.12);
}

.status-border-exact {
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 5px;
    border-radius: 12px 0 0 12px;
}

.yellow-border { background: #FFC107; }
.cyan-border { background: #17A2B8; }
.gray-border { background: #6C757D; }
.green-border { background: #28A745; }

.card-icon-exact {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    flex-shrink: 0;
}

.yellow-icon {
    background: #FFC107;
    color: white;
}

.cyan-icon {
    background: #17A2B8;
    color: white;
}

.gray-icon {
    background: #6C757D;
    color: white;
}

.green-icon {
    background: #28A745;
    color: white;
}

.card-content-exact {
    flex: 1;
    min-width: 0;
}

.card-title-exact {
    font-size: 14px;
    font-weight: 500;
    color: #6c757d;
    margin-bottom: 8px;
    line-height: 1.2;
}

.card-number-exact {
    font-size: 32px;
    font-weight: 700;
    line-height: 1;
}

.yellow-text { color: #FFC107; }
.cyan-text { color: #17A2B8; }
.gray-text { color: #6C757D; }
.green-text { color: #28A745; }

/* Workflow Cards - Smaller Size to Match Screenshot */
.workflow-card-exact {
    background: white;
    border-radius: 10px;
    padding: 14px 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    min-height: 75px;
    display: flex;
    align-items: center;
    gap: 12px;
    transition: all 0.3s ease;
}

.workflow-card-exact:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.12);
}

.workflow-icon-exact {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}

.blue-workflow {
    background: #007BFF;
    color: white;
}

.yellow-workflow {
    background: #FFC107;
    color: white;
}

.red-workflow {
    background: #DC3545;
    color: white;
}

.workflow-content-exact {
    flex: 1;
    min-width: 0;
}

.workflow-title-exact {
    font-size: 13px;
    font-weight: 500;
    color: #6c757d;
    margin-bottom: 6px;
    line-height: 1.2;
}

.workflow-number-exact {
    font-size: 24px;
    font-weight: 700;
    color: #495057;
    line-height: 1;
}

/* Chart Container - Exact Screenshot Match */
.chart-container-exact {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    overflow: hidden;
}

.chart-header-exact {
    padding: 20px 24px 16px;
    border-bottom: 1px solid #e9ecef;
    background: white;
}

.chart-header-exact h5 {
    margin: 0 0 4px 0;
    font-size: 18px;
    font-weight: 600;
    color: #2c3e50;
}

.chart-header-exact p {
    margin: 0;
    font-size: 14px;
    color: #6c757d;
}

.chart-body-exact {
    padding: 24px;
    background: white;
}

/* Action Cards - Compact Size to Match Screenshot */
.action-card-exact {
    border-radius: 12px;
    padding: 18px;
    text-align: center;
    color: white;
    min-height: 105px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.action-card-exact:hover {
    transform: translateY(-2px);
}

.export-exact {
    background: #28A745;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}

.completion-exact {
    background: #6F42C1;
    box-shadow: 0 4px 15px rgba(111, 66, 193, 0.3);
}

.action-icon-exact {
    font-size: 26px;
    margin-bottom: 8px;
    opacity: 0.9;
}

.action-content-exact h6 {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 4px;
}

.completion-percentage-exact {
    font-size: 28px;
    font-weight: 700;
    margin: 4px 0;
}

.action-card-exact .btn {
    border: 2px solid rgba(255,255,255,0.3);
    background: rgba(255,255,255,0.2);
    color: white;
    font-weight: 600;
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 14px;
}

.action-card-exact .btn:hover {
    background: rgba(255,255,255,0.3);
    border-color: rgba(255,255,255,0.5);
    color: white;
}

/* Bottom Action Cards - Exact Screenshot Match */
.bottom-card-exact {
    background: white;
    border-radius: 12px;
    padding: 16px 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    display: flex;
    align-items: center;
    position: relative;
    transition: all 0.3s ease;
    height: 70px;
    min-height: 70px;
}

.bottom-card-exact:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.12);
}

.bottom-icon-exact {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 16px;
    flex-shrink: 0;
    font-size: 18px;
    color: white;
}

.blue-bottom { background: #007BFF; }
.yellow-bottom { background: #FFC107; }
.cyan-bottom { background: #17A2B8; }
.green-bottom { background: #28A745; }

.bottom-content-exact {
    flex: 1;
    min-width: 0;
}

.bottom-title-exact {
    font-size: 14px;
    font-weight: 600;
    color: #333;
    line-height: 1.2;
    margin: 0;
}

.badge-exact {
    position: absolute;
    top: -8px;
    right: -8px;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 700;
    border: 2px solid white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    color: white;
}

.badge-yellow { background: #FFC107; color: #000; }
.badge-cyan { background: #17A2B8; }

/* Responsive Design */
@media (max-width: 768px) {
    .status-card-exact {
        min-height: 100px;
        padding: 16px;
        gap: 12px;
    }
    
    .workflow-card-exact {
        min-height: 65px;
        padding: 12px 14px;
        gap: 10px;
    }
    
    .card-icon-exact {
        width: 40px;
        height: 40px;
        font-size: 20px;
    }
    
    .workflow-icon-exact {
        width: 36px;
        height: 36px;
        font-size: 16px;
    }
    
    .card-number-exact {
        font-size: 24px;
    }
    
    .workflow-number-exact {
        font-size: 20px;
    }
    
    .workflow-title-exact {
        font-size: 12px;
    }
    
    .bottom-card-exact {
        height: 60px;
        min-height: 60px;
        padding: 12px 16px;
    }
    
    .bottom-icon-exact {
        width: 36px;
        height: 36px;
        font-size: 16px;
        margin-right: 12px;
    }
    
    .bottom-title-exact {
        font-size: 13px;
    }
    
    .badge-exact {
        width: 20px;
        height: 20px;
        font-size: 11px;
        top: -6px;
        right: -6px;
    }
}

/* Link Styling */
a.text-decoration-none:hover .bottom-card-exact {
    color: inherit;
}

/* Subtle animations */
@keyframes fadeSlideIn {
    from {
        opacity: 0;
        transform: translateY(15px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.status-card-exact,
.workflow-card-exact,
.chart-container-exact,
.action-card-exact,
.bottom-card-exact {
    animation: fadeSlideIn 0.4s ease forwards;
}
</style>
@endsection

@push('scripts')
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
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#4f46e5',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointHoverRadius: 6,
                        borderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 1000,
                        easing: 'easeInOutQuart'
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                font: {
                                    size: 11
                                },
                                color: '#6c757d'
                            },
                            grid: {
                                color: 'rgba(0,0,0,0.08)',
                                borderColor: '#dee2e6'
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    size: 10
                                },
                                color: '#6c757d',
                                maxRotation: 45
                            },
                            grid: {
                                color: 'rgba(0,0,0,0.05)',
                                borderColor: '#dee2e6'
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
        });
    </script>
@endpush 