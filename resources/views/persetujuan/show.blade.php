<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Persetujuan HKI') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Informasi Pengajuan</h3>
                        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Judul</p>
                                <p class="mt-1 text-sm text-gray-900">{{ $pengajuan->judul }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Jenis HKI</p>
                                <p class="mt-1 text-sm text-gray-900">{{ $pengajuan->jenis_hki }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Status</p>
                                <p class="mt-1">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($pengajuan->status === 'validated') bg-green-100 text-green-800
                                        @elseif($pengajuan->status === 'approved') bg-blue-100 text-blue-800
                                        @elseif($pengajuan->status === 'rejected') bg-red-100 text-red-800
                                        @endif">
                                        @if($pengajuan->status === 'validated') Tervalidasi
                                        @elseif($pengajuan->status === 'approved') Disetujui
                                        @elseif($pengajuan->status === 'rejected') Ditolak
                                        @endif
                                    </span>
                                </p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Tanggal Validasi</p>
                                <p class="mt-1 text-sm text-gray-900">{{ $pengajuan->validated_at ? $pengajuan->validated_at->format('d/m/Y H:i') : '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Deskripsi</h3>
                        <div class="mt-4">
                            <p class="text-sm text-gray-900">{{ $pengajuan->deskripsi }}</p>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900">File Pengajuan</h3>
                        <div class="mt-4">
                            <a href="{{ Storage::url($pengajuan->file_path) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">
                                Unduh File
                            </a>
                        </div>
                    </div>

                    @if($pengajuan->status === 'validated')
                        <div class="mt-6 flex space-x-4">
                            <form action="{{ route('persetujuan.approve', $pengajuan) }}" method="POST" class="inline">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Setujui
                                </button>
                            </form>

                            <button type="button" onclick="document.getElementById('rejectModal').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Tolak
                            </button>
                        </div>

                        <!-- Modal Penolakan -->
                        <div id="rejectModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden">
                            <div class="flex items-center justify-center min-h-screen">
                                <div class="bg-white rounded-lg p-8 max-w-md w-full">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Tolak Pengajuan</h3>
                                    <form action="{{ route('persetujuan.reject', $pengajuan) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="mb-4">
                                            <label for="rejection_reason" class="block text-sm font-medium text-gray-700">Alasan Penolakan</label>
                                            <textarea name="rejection_reason" id="rejection_reason" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required></textarea>
                                        </div>
                                        <div class="flex justify-end space-x-4">
                                            <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                                Batal
                                            </button>
                                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                Tolak
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($pengajuan->status === 'rejected' && $pengajuan->rejection_reason)
                        <div class="mt-6">
                            <h3 class="text-lg font-medium text-gray-900">Alasan Penolakan</h3>
                            <div class="mt-4">
                                <p class="text-sm text-gray-900">{{ $pengajuan->rejection_reason }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="mt-6">
                        <a href="{{ route('persetujuan.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>