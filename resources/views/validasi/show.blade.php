<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Validasi Pengajuan HKI') }}
            </h2>
            <a href="{{ route('validasi.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Pengajuan</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Judul</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $pengajuan->judul }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Jenis HKI</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $pengajuan->jenis_hki }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $pengajuan->deskripsi }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Pengaju</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $pengajuan->user->name }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tanggal Pengajuan</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $pengajuan->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">File Pengajuan</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">File</label>
                                    <a href="{{ Storage::url('pengajuan/' . $pengajuan->file_path) }}" 
                                       class="mt-1 inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 focus:bg-blue-600 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                       target="_blank">
                                        Download File
                                    </a>
                                </div>
                            </div>

                            <form action="{{ route('validasi.update', $pengajuan) }}" method="POST" class="mt-6 space-y-4">
                                @csrf
                                @method('PUT')

                                <div>
                                    <x-input-label for="status" :value="__('Status Validasi')" />
                                    <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                        <option value="">Pilih Status</option>
                                        <option value="approved">Setujui</option>
                                        <option value="rejected">Tolak</option>
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('status')" />
                                </div>

                                <div>
                                    <x-input-label for="catatan" :value="__('Catatan')" />
                                    <textarea id="catatan" name="catatan" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required></textarea>
                                    <x-input-error class="mt-2" :messages="$errors->get('catatan')" />
                                </div>

                                <div class="flex items-center gap-4">
                                    <x-primary-button>{{ __('Simpan Validasi') }}</x-primary-button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>