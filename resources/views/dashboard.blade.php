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
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div class="bg-purple-100 p-6 rounded-lg">
                                <h3 class="text-lg font-semibold text-purple-800 mb-2">Persetujuan HKI</h3>
                                <p class="text-purple-600">Berikan persetujuan untuk pengajuan HKI yang telah divalidasi.</p>
                                <a href="{{ route('persetujuan.index') }}" class="mt-4 inline-block bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600">Lihat Pengajuan</a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>