<x-app-layout>
    <div class="container-fluid py-4">
        <!-- Konten Dashboard -->
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Hak Cipta Terdaftar</h5>
                <p class="card-text fs-1 fw-bold text-primary">{{ $totalSelesai }}</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-4">
            <div class="card-body">
                <h5 class="card-title">Hak Cipta Yang Tidak Lengkap</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
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
</x-app-layout>

<style>
/* Dashboard Table Layout Fixes */
.table th:nth-child(1) { width: 40%; } /* Judul */
.table th:nth-child(2) { width: 25%; } /* Pengusul */
.table th:nth-child(3) { width: 25%; } /* Tanggal */
.table th:nth-child(4) { width: 10%; } /* Aksi */

.nama-pencipta {
    word-wrap: break-word;
    word-break: break-word;
    white-space: normal;
    line-height: 1.3;
    max-height: 3.9em;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
}

.col-nama {
    max-width: 150px;
    min-width: 120px;
}

.col-judul {
    max-width: 250px;
}

/* Perbaikan layout untuk direktur */
.bg-purple-100 { background-color: #e9d5ff !important; }
.bg-green-100 { background-color: #dcfce7 !important; }
.bg-red-100 { background-color: #fee2e2 !important; }
.text-purple-800 { color: #6b21a8 !important; }
.text-purple-700 { color: #7c3aed !important; }
.text-green-800 { color: #166534 !important; }
.text-green-700 { color: #15803d !important; }
.text-red-800 { color: #991b1b !important; }
.text-red-700 { color: #dc2626 !important; }

/* Gradient backgrounds */
.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

/* Card hover effects */
.card {
    transition: all 0.3s ease;
}
.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

/* Responsive improvements for direktur */
@media (max-width: 768px) {
    .btn.w-100.py-3 {
        padding: 1rem !important;
        font-size: 1.1rem;
    }
}

/* Primary button enhancements */
.btn-primary.py-3 {
    border-radius: 8px;
    font-weight: 500;
    box-shadow: 0 2px 4px rgba(0,123,255,0.3);
    transition: all 0.2s ease;
}

.btn-primary.py-3:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,123,255,0.4);
}

/* Dashboard table container - Remove any padding/margin */
.table-container {
    margin: 0 !important;
    padding: 0 !important;
}

.table-container .table-responsive {
    margin: 0 !important;
    padding: 0 !important;
}

/* Dashboard table styling - Full width hover */
.table-hover tbody tr td {
    border: none !important;
    vertical-align: middle !important;
    background-color: #ffffff !important;
    padding-left: 1.5rem !important;
    padding-right: 1.5rem !important;
    margin: 0 !important;
}

/* Hover effect yang penuh di seluruh baris - Full edge to edge */
.table-hover tbody tr:hover td {
    background-color: rgba(0, 0, 0, 0.075) !important;
    transition: background-color 0.15s ease-in-out !important;
}

/* Ensure table width is 100% */
.table-hover {
    width: 100% !important;
    margin: 0 !important;
}

.nama-pencipta {
    color: inherit !important;
    background: transparent !important;
}
</style>