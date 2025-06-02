@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Edit Pengajuan HKI</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('pengajuan.update', $pengajuan->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="judul_karya" class="form-label">Judul Karya</label>
                            <input type="text" class="form-control" id="judul_karya" name="judul" value="{{ old('judul', $pengajuan->judul_karya) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="kategori" class="form-label">Kategori</label>
                            <input type="text" class="form-control" id="kategori" name="kategori" value="{{ old('kategori', $pengajuan->kategori) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required>{{ old('deskripsi', $pengajuan->deskripsi) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="identitas_ciptaan" class="form-label">Jenis Ciptaan</label>
                            <select class="form-select" id="identitas_ciptaan" name="identitas_ciptaan" required>
                                <option value="">Pilih Jenis Ciptaan</option>
                                <option value="karya tulis" {{ old('identitas_ciptaan', $pengajuan->identitas_ciptaan) == 'karya tulis' ? 'selected' : '' }}>Karya Tulis</option>
                                <option value="karya audio visual" {{ old('identitas_ciptaan', $pengajuan->identitas_ciptaan) == 'karya audio visual' ? 'selected' : '' }}>Karya Audio Visual</option>
                                <option value="karya lainnya" {{ old('identitas_ciptaan', $pengajuan->identitas_ciptaan) == 'karya lainnya' ? 'selected' : '' }}>Karya Lainnya</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="sub_jenis_ciptaan" class="form-label">Sub Jenis Ciptaan</label>
                            <select class="form-select" id="sub_jenis_ciptaan" name="sub_jenis_ciptaan" required>
                                <option value="">Pilih Sub Jenis Ciptaan</option>
                                <!-- Opsi akan diisi oleh JavaScript -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="tahun_usulan" class="form-label">Tahun Usulan</label>
                            <select class="form-select" id="tahun_usulan" name="tahun_usulan">
                                <option value="">Pilih</option>
                                <!-- Opsi tahun akan diisi oleh JavaScript -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="jumlah_pencipta" class="form-label">Jumlah Pencipta</label>
                            <select class="form-select" id="jumlah_pencipta" name="jumlah_pencipta" required>
                                <option value="">Pilih Jumlah Pencipta</option>
                                <option value="1 orang" {{ old('jumlah_pencipta', $pengajuan->jumlah_pencipta) == '1 orang' ? 'selected' : '' }}>1 orang</option>
                                <option value="2 orang" {{ old('jumlah_pencipta', $pengajuan->jumlah_pencipta) == '2 orang' ? 'selected' : '' }}>2 orang</option>
                                <option value="3 orang" {{ old('jumlah_pencipta', $pengajuan->jumlah_pencipta) == '3 orang' ? 'selected' : '' }}>3 orang</option>
                                <option value="4 orang" {{ old('jumlah_pencipta', $pengajuan->jumlah_pencipta) == '4 orang' ? 'selected' : '' }}>4 orang</option>
                                <option value="5 orang" {{ old('jumlah_pencipta', $pengajuan->jumlah_pencipta) == '5 orang' ? 'selected' : '' }}>5 orang</option>
                            </select>
                        </div>
                        <hr>
                        <h5>Data Pencipta</h5>
                        @foreach($pengajuan->pengaju as $i => $pencipta)
                        <div class="mb-3 border rounded p-3">
                            <div class="mb-2">
                                <label class="form-label">Nama</label>
                                <input type="text" class="form-control" name="pencipta[{{ $i }}][nama]" value="{{ old('pencipta.'.$i.'.nama', $pencipta->nama) }}" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="pencipta[{{ $i }}][email]" value="{{ old('pencipta.'.$i.'.email', $pencipta->email) }}" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">No HP</label>
                                <input type="text" class="form-control" name="pencipta[{{ $i }}][no_hp]" value="{{ old('pencipta.'.$i.'.no_hp', $pencipta->no_hp) }}">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Alamat</label>
                                <textarea class="form-control" name="pencipta[{{ $i }}][alamat]" rows="2">{{ old('pencipta.'.$i.'.alamat', $pencipta->alamat) }}</textarea>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Kecamatan</label>
                                <input type="text" class="form-control" name="pencipta[{{ $i }}][kecamatan]" value="{{ old('pencipta.'.$i.'.kecamatan', $pencipta->kecamatan) }}">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Kode Pos</label>
                                <input type="text" class="form-control" name="pencipta[{{ $i }}][kodepos]" value="{{ old('pencipta.'.$i.'.kodepos', $pencipta->kodepos) }}">
                            </div>
                        </div>
                        @endforeach
                        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                        <button type="submit" name="simpan_draft" value="1" class="btn btn-warning ms-2">Simpan Draft</button>
                        <a href="{{ route('pengajuan.index') }}" class="btn btn-secondary ms-2">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tahun Usulan otomatis 4 tahun terakhir
    const tahunUsulanSelect = document.getElementById('tahun_usulan');
    if (tahunUsulanSelect) {
        const tahunSekarang = new Date().getFullYear();
        const tahunTerpilih = "{{ old('tahun_usulan', $pengajuan->tahun_usulan) }}";
        for (let i = 0; i < 4; i++) {
            const tahun = tahunSekarang - i;
            const option = document.createElement('option');
            option.value = tahun;
            option.textContent = tahun;
            if (tahun == tahunTerpilih) {
                option.selected = true;
            }
            tahunUsulanSelect.appendChild(option);
        }
    }
    // Sub Jenis Ciptaan dinamis sesuai Jenis Ciptaan
    const identitasCiptaanSelect = document.getElementById('identitas_ciptaan');
    const subJenisCiptaanSelect = document.getElementById('sub_jenis_ciptaan');
    const opsiSubJenis = {
        'karya tulis': [
            'Buku', 'E-Book', 'Diktat', 'Modul', 'Buku Panduan/Petunjuk', 'Karya Ilmiah', 'Karya Tulis/Artikel', 'Laporan Penelitian', 'Jurnal'
        ],
        'karya audio visual': [
            'Kuliah', 'Karya Rekaman Video', 'Karya Siaran Video'
        ],
        'karya lainnya': [
            'Program Komputer', 'Permainan Video', 'Basis Data'
        ]
    };
    function updateSubJenisCiptaan() {
        const jenis = identitasCiptaanSelect.value;
        const prevValue = "{{ old('sub_jenis_ciptaan', $pengajuan->sub_jenis_ciptaan) }}";
        subJenisCiptaanSelect.innerHTML = '<option value="">Pilih Sub Jenis Ciptaan</option>';
        if (opsiSubJenis[jenis]) {
            opsiSubJenis[jenis].forEach(function(opt) {
                const option = document.createElement('option');
                option.value = opt;
                option.textContent = opt;
                if (opt === prevValue) option.selected = true;
                subJenisCiptaanSelect.appendChild(option);
            });
        }
    }
    updateSubJenisCiptaan();
    identitasCiptaanSelect.addEventListener('change', updateSubJenisCiptaan);
});
</script>
@endpush 