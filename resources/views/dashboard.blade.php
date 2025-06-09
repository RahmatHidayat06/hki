<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(auth()->user()->role === 'dosen')
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div class="bg-blue-100 p-6 rounded-lg">
                                <h3 class="text-lg font-semibold text-blue-800 mb-2">Pengajuan HKI</h3>
                                <p class="text-blue-600">Buat pengajuan HKI baru atau kelola pengajuan yang sudah ada.</p>
                                <a href="{{ route('pengajuan.index') }}" class="mt-4 inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Kelola Pengajuan</a>
                            </div>
                        </div>
                    @elseif(auth()->user()->role === 'admin_p3m')
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div class="bg-green-100 p-6 rounded-lg">
                                <h3 class="text-lg font-semibold text-green-800 mb-2">Validasi Pengajuan</h3>
                                <p class="text-green-600">Validasi pengajuan HKI dari dosen.</p>
                                <a href="{{ route('validasi.index') }}" class="mt-4 inline-block bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Lihat Pengajuan</a>
                            </div>
                        </div>
                    @elseif(auth()->user()->role === 'direktur')
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div class="bg-purple-100 p-6 rounded-lg">
                                <h3 class="text-lg font-semibold text-purple-800 mb-2">Menunggu Persetujuan</h3>
                                <div class="text-3xl font-bold text-purple-700">{{ $menunggu }}</div>
                            </div>
                            <div class="bg-green-100 p-6 rounded-lg">
                                <h3 class="text-lg font-semibold text-green-800 mb-2">Disetujui</h3>
                                <div class="text-3xl font-bold text-green-700">{{ $disetujui }}</div>
                            </div>
                            <div class="bg-red-100 p-6 rounded-lg">
                                <h3 class="text-lg font-semibold text-red-800 mb-2">Ditolak</h3>
                                <div class="text-3xl font-bold text-red-700">{{ $ditolak }}</div>
                            </div>
                        </div>
                        <div class="mb-4 flex flex-wrap gap-2">
                            <a href="{{ route('persetujuan.index') }}" class="btn btn-primary">Lihat Daftar Persetujuan</a>
                            <a href="{{ route('direktur.ttd.form') }}" class="btn btn-secondary">Upload Tanda Tangan</a>
                        </div>
                        <div class="card mb-4">
                            <div class="card-header bg-light"><b>5 Pengajuan Menunggu Persetujuan Terbaru</b></div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-bordered mb-0">
                                        <thead>
                                            <tr>
                                                <th>Judul</th>
                                                <th>Pengusul</th>
                                                <th>Tanggal Pengajuan</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($pengajuanBaru as $item)
                                            <tr>
                                                <td>{{ $item->judul_karya }}</td>
                                                <td>{{ $item->nama_pengusul }}</td>
                                                <td>{{ $item->tanggal_pengajuan ? $item->tanggal_pengajuan->format('d/m/Y H:i') : '-' }}</td>
                                                <td><a href="{{ route('persetujuan.show', $item->id) }}" class="btn btn-sm btn-info">Detail</a></td>
                                            </tr>
                                            @empty
                                            <tr><td colspan="4" class="text-center">Tidak ada pengajuan menunggu persetujuan.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>