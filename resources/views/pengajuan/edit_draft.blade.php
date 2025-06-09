@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Draft Ciptaan</h2>
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <div id="global-error-alert" class="alert alert-danger d-none"></div>
    <div id="global-error-list" class="alert alert-warning d-none"></div>
    <!-- Navigasi Section -->
    <div class="mb-4">
        <div class="nav nav-pills nav-fill" id="form-tabs" role="tablist">
            <button class="nav-link active" id="data-pengusul-tab" data-bs-toggle="pill" data-bs-target="#data-pengusul" type="button" role="tab">
                <i class="fas fa-user me-2"></i>Data Pengusul <span class="tab-error-indicator d-none" id="err-ind-data-pengusul" data-bs-toggle="tooltip" data-bs-placement="top" title="Ada data yang belum lengkap di bagian ini"><i class="fas fa-exclamation-circle text-danger"></i></span>
            </button>
            <button class="nav-link" id="data-ciptaan-tab" data-bs-toggle="pill" data-bs-target="#data-ciptaan" type="button" disabled>
                <i class="fas fa-book me-2"></i>Data Ciptaan <span class="tab-error-indicator d-none" id="err-ind-data-ciptaan" data-bs-toggle="tooltip" data-bs-placement="top" title="Ada data yang belum lengkap di bagian ini"><i class="fas fa-exclamation-circle text-danger"></i></span>
            </button>
            <button class="nav-link" id="data-pencipta-tab" data-bs-toggle="pill" data-bs-target="#data-pencipta" type="button" disabled>
                <i class="fas fa-users me-2"></i>Data Pencipta <span class="tab-error-indicator d-none" id="err-ind-data-pencipta" data-bs-toggle="tooltip" data-bs-placement="top" title="Ada data yang belum lengkap di bagian ini"><i class="fas fa-exclamation-circle text-danger"></i></span>
            </button>
            <button class="nav-link" id="dokumen-tab" data-bs-toggle="pill" data-bs-target="#dokumen" type="button" disabled>
                <i class="fas fa-file me-2"></i>Dokumen <span class="tab-error-indicator d-none" id="err-ind-dokumen" data-bs-toggle="tooltip" data-bs-placement="top" title="Ada data yang belum lengkap di bagian ini"><i class="fas fa-exclamation-circle text-danger"></i></span>
            </button>
        </div>
    </div>
    <form id="form-edit-draft" method="POST" action="{{ route('draft.update', $pengajuan->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <div class="tab-content" id="form-tabs-content">
            <!-- Section 1: Data Pengusul -->
            <div class="tab-pane fade show active" id="data-pengusul" role="tabpanel">
                <div class="card">
                    <div class="card-header bg-primary text-white position-relative">
                        <h5 class="mb-0"><i class="fas fa-user me-2"></i>Data Pengusul</h5>
                        <small class="text-danger position-absolute end-0 top-50 translate-middle-y d-none" id="err-text-data-pengusul">Ada data yang belum lengkap di bagian ini</small>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Anda mengajukan sebagai?</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input role-radio" type="radio" name="role" id="role-dosen" value="dosen" {{ old('role', $pengajuan->role) == 'dosen' || old('role', $pengajuan->role) == null ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="role-dosen">Dosen</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input role-radio" type="radio" name="role" id="role-mahasiswa" value="mahasiswa" {{ old('role', $pengajuan->role) == 'mahasiswa' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="role-mahasiswa">Mahasiswa</label>
                                </div>
                                @if($pengajuan->role)
                                    <div class="mt-2">
                                        <span class="text-success fw-bold">Sudah dipilih: {{ ucfirst($pengajuan->role) }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nama_pengusul" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="nama_pengusul" name="nama_pengusul" value="{{ $pengajuan->nama_pengusul }}" required>
                                <div class="invalid-feedback">Field ini wajib diisi untuk melanjutkan atau mengirim.</div>
                            </div>
                            <div class="col-md-6 mb-3" id="nip-nidn-field">
                                <label for="nip_nidn" class="form-label">NIP/NIDN</label>
                                <input type="text" class="form-control" id="nip_nidn" name="nip_nidn" value="{{ $pengajuan->nip_nidn }}" required pattern="^[0-9]{8,20}$" maxlength="20">
                                <div class="invalid-feedback">NIP/NIDN wajib diisi, hanya angka 8-20 digit.</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="no_hp" class="form-label">Nomor HP</label>
                                <input type="tel" class="form-control" id="no_hp" name="no_hp" value="{{ $pengajuan->no_hp }}" required pattern="^08[0-9]{8,11}$" maxlength="15">
                                <div class="invalid-feedback">Nomor HP wajib diisi dan harus dimulai 08, 10-13 digit angka.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tahun_usulan" class="form-label">Tahun Usulan</label>
                                <select class="form-select" id="tahun_usulan" name="tahun_usulan" required>
                                    <option value="">Pilih</option>
                                </select>
                                <div class="invalid-feedback">Field ini wajib diisi untuk melanjutkan atau mengirim.</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3" id="id-sinta-field">
                                <label for="id_sinta" class="form-label">ID Sinta</label>
                                <input type="text" class="form-control" id="id_sinta" name="id_sinta" value="{{ $pengajuan->id_sinta }}" required>
                                <div class="invalid-feedback">Field ini wajib diisi untuk melanjutkan atau mengirim.</div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            <button type="button" class="btn btn-primary next-section" data-next="data-ciptaan">
                                Selanjutnya <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                            <button type="submit" name="save_as_draft" value="1" class="btn btn-secondary ms-2" formnovalidate>Simpan sebagai Draft</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Section 2: Data Ciptaan -->
            <div class="tab-pane fade" id="data-ciptaan" role="tabpanel">
                <div class="card">
                    <div class="card-header bg-primary text-white position-relative">
                        <h5 class="mb-0"><i class="fas fa-book me-2"></i>Data Ciptaan</h5>
                        <small class="text-danger position-absolute end-0 top-50 translate-middle-y d-none" id="err-text-data-ciptaan">Ada data yang belum lengkap di bagian ini</small>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="judul" class="form-label">Judul Ciptaan</label>
                            <input type="text" class="form-control" id="judul" name="judul" value="{{ $pengajuan->judul_karya }}" required>
                            <div class="invalid-feedback">Field ini wajib diisi untuk melanjutkan atau mengirim.</div>
                        </div>
                        <div class="mb-3">
                            <label for="identitas_ciptaan" class="form-label">Jenis Ciptaan</label>
                            <select class="form-select" id="identitas_ciptaan" name="identitas_ciptaan" required>
                                <option value="">Pilih Jenis Ciptaan</option>
                                <option value="karya tulis" {{ $pengajuan->identitas_ciptaan == 'karya tulis' ? 'selected' : '' }}>Karya Tulis</option>
                                <option value="karya audio visual" {{ $pengajuan->identitas_ciptaan == 'karya audio visual' ? 'selected' : '' }}>Karya Audio Visual</option>
                                <option value="karya lainnya" {{ $pengajuan->identitas_ciptaan == 'karya lainnya' ? 'selected' : '' }}>Karya Lainnya</option>
                            </select>
                            <div class="invalid-feedback">Field ini wajib diisi untuk melanjutkan atau mengirim.</div>
                        </div>
                        <div class="mb-3">
                            <label for="sub_jenis_ciptaan" class="form-label">Sub Jenis Ciptaan</label>
                            <select class="form-select" id="sub_jenis_ciptaan" name="sub_jenis_ciptaan" required>
                                <option value="">Pilih Sub Jenis Ciptaan</option>
                            </select>
                            <div class="invalid-feedback">Field ini wajib diisi untuk melanjutkan atau mengirim.</div>
                        </div>
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi Ciptaan</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" required>{{ $pengajuan->deskripsi }}</textarea>
                            <div class="invalid-feedback">Field ini wajib diisi untuk melanjutkan atau mengirim.</div>
                        </div>
                        <div class="mb-3">
                            <label for="tanggal_pertama_kali_diumumkan" class="form-label">Tanggal Pertama Kali Diumumkan</label>
                            <input type="date" class="form-control" id="tanggal_pertama_kali_diumumkan" name="tanggal_pertama_kali_diumumkan" value="{{ $pengajuan->tanggal_pertama_kali_diumumkan }}" required>
                            <div class="invalid-feedback">Field ini wajib diisi untuk melanjutkan atau mengirim.</div>
                        </div>
                        <div class="d-flex justify-content-between mt-3">
                            <button type="button" class="btn btn-secondary prev-section" data-prev="data-pengusul">
                                <i class="fas fa-arrow-left me-2"></i> Sebelumnya
                            </button>
                            <div>
                                <button type="button" class="btn btn-primary next-section" data-next="data-pencipta">
                                    Selanjutnya <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                                <button type="submit" name="save_as_draft" value="1" class="btn btn-secondary ms-2" formnovalidate>Simpan sebagai Draft</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Section 3: Data Pencipta -->
            <div class="tab-pane fade" id="data-pencipta" role="tabpanel">
                <div class="card">
                    <div class="card-header bg-primary text-white position-relative">
                        <h5 class="mb-0"><i class="fas fa-users me-2"></i>Data Pencipta</h5>
                        <small class="text-danger position-absolute end-0 top-50 translate-middle-y d-none" id="err-text-data-pencipta">Ada data yang belum lengkap di bagian ini</small>
                    </div>
                    <div class="card-body">
                        <div id="notif-pencipta-draft" class="alert alert-danger d-none"></div>
                        <div class="mb-3">
                            <label for="jumlah_pencipta" class="form-label">Jumlah Pencipta</label>
                            @php 
                                $oldPencipta = old('pencipta');
                                if ($oldPencipta) {
                                    $jumlahPencipta = count($oldPencipta);
                                    $pencipta = collect($oldPencipta);
                                } else {
                                    $pencipta = $pengajuan->pengaju ?? collect();
                                    $jumlahPencipta = $pencipta->count();
                                }
                            @endphp
                            <input type="hidden" id="jumlah_pencipta_hidden" value="{{ $jumlahPencipta }}">
                            <select class="form-select" id="jumlah_pencipta" name="jumlah_pencipta">
                                <option value="">Pilih Jumlah Pencipta</option>
                                @for($j = 1; $j <= 5; $j++)
                                    <option value="{{ $j }} orang"
                                        @if(old('jumlah_pencipta'))
                                            {{ old('jumlah_pencipta') == $j.' orang' ? 'selected' : '' }}
                                        @elseif(isset($jumlahPencipta) && $jumlahPencipta == $j)
                                            selected
                                        @endif
                                    >{{ $j }} orang</option>
                                @endfor
                            </select>
                            <div class="invalid-feedback">Field ini wajib diisi untuk melanjutkan atau mengirim.</div>
                        </div>
                        <div id="pencipta-container">
                            @for($i = 0; $i < $jumlahPencipta; $i++)
                                @php $p = (object) ($pencipta[$i] ?? []); @endphp
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h6 class="mb-0">Data Pencipta {{ $i+1 }}</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="pencipta[{{ $i }}][nama]" id="pencipta-nama-{{ $i }}" value="{{ old('pencipta.'.$i.'.nama', $p->nama ?? '') }}" required>
                                                <div class="invalid-feedback d-none" id="error-nama-{{ $i }}">Nama wajib diisi</div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control" name="pencipta[{{ $i }}][email]" id="pencipta-email-{{ $i }}" value="{{ old('pencipta.'.$i.'.email', $p->email ?? '') }}" required>
                                                <div class="invalid-feedback d-none" id="error-email-{{ $i }}">Email wajib diisi</div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">No. HP <span class="text-danger">*</span></label>
                                                <input type="tel" class="form-control" name="pencipta[{{ $i }}][no_hp]" value="{{ old('pencipta.'.$i.'.no_hp', $p->no_hp ?? '') }}" required pattern="^08[0-9]{8,11}$" maxlength="15">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Alamat <span class="text-danger">*</span></label>
                                                <textarea class="form-control" name="pencipta[{{ $i }}][alamat]" rows="2" required>{{ old('pencipta.'.$i.'.alamat', $p->alamat ?? '') }}</textarea>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Kecamatan <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="pencipta[{{ $i }}][kecamatan]" value="{{ old('pencipta.'.$i.'.kecamatan', $p->kecamatan ?? '') }}" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Kode Pos <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="pencipta[{{ $i }}][kodepos]" value="{{ old('pencipta.'.$i.'.kodepos', $p->kodepos ?? '') }}" required pattern="^[0-9]{5}$" maxlength="5">
                                                <div class="invalid-feedback">Kode Pos harus 5 digit angka.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                        <div class="d-flex justify-content-between mt-3">
                            <button type="button" class="btn btn-secondary prev-section" data-prev="data-ciptaan">
                                <i class="fas fa-arrow-left me-2"></i> Sebelumnya
                            </button>
                            <div>
                                <button type="button" class="btn btn-primary next-section" data-next="dokumen">
                                    Selanjutnya <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                                <button type="submit" name="save_as_draft" value="1" class="btn btn-secondary ms-2" formnovalidate>Simpan sebagai Draft</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Section 4: Dokumen -->
            <div class="tab-pane fade" id="dokumen" role="tabpanel">
                <div class="card">
                    <div class="card-header bg-primary text-white position-relative">
                        <h5 class="mb-0"><i class="fas fa-file me-2"></i>Dokumen Pendukung</h5>
                        <small class="text-danger position-absolute end-0 top-50 translate-middle-y d-none" id="err-text-dokumen">Ada data yang belum lengkap di bagian ini</small>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="contoh_ciptaan" class="form-label">CONTOH CIPTAAN</label>
                            @if($pengajuan->file_karya)
                                <div class="mb-2 file-lama-exists d-flex align-items-center gap-2">
                                    <a href="{{ asset('storage/'.$pengajuan->file_karya) }}" target="_blank" class="btn btn-sm btn-success"><i class="fas fa-eye"></i> Lihat File</a>
                                    <button type="button" class="btn btn-sm btn-warning" onclick="toggleFileInput('contoh_ciptaan')"><i class="fas fa-sync-alt"></i> Ganti File</button>
                                </div>
                                <input type="file" class="form-control d-none" id="contoh_ciptaan" name="contoh_ciptaan">
                            @else
                                <input type="file" class="form-control" id="contoh_ciptaan" name="contoh_ciptaan">
                            @endif
                        </div>
                        <div class="mb-3">
                            <label for="surat_pengalihan_hak_cipta" class="form-label">Surat Pengalihan Hak Cipta (PDF)</label>
                            @php
                                $dokumen = $pengajuan->file_dokumen_pendukung;
                                if (is_string($dokumen)) $dokumen = json_decode($dokumen, true);
                            @endphp
                            @if(isset($dokumen['surat_pengalihan']) && $dokumen['surat_pengalihan'])
                                <div class="mb-2 file-lama-exists d-flex align-items-center gap-2">
                                    <a href="{{ asset('storage/'.$dokumen['surat_pengalihan']) }}" target="_blank" class="btn btn-sm btn-success"><i class="fas fa-eye"></i> Lihat File</a>
                                    <button type="button" class="btn btn-sm btn-warning" onclick="toggleFileInput('surat_pengalihan_hak_cipta')"><i class="fas fa-sync-alt"></i> Ganti File</button>
                                </div>
                                <input type="file" class="form-control d-none" id="surat_pengalihan_hak_cipta" name="surat_pengalihan_hak_cipta">
                            @else
                                <input type="file" class="form-control" id="surat_pengalihan_hak_cipta" name="surat_pengalihan_hak_cipta">
                            @endif
                        </div>
                        <div class="mb-3">
                            <label for="surat_pernyataan_hak_cipta" class="form-label">Surat Pernyataan Hak Cipta (PDF)</label>
                            @if(isset($dokumen['surat_pernyataan']) && $dokumen['surat_pernyataan'])
                                <div class="mb-2 file-lama-exists d-flex align-items-center gap-2">
                                    <a href="{{ asset('storage/'.$dokumen['surat_pernyataan']) }}" target="_blank" class="btn btn-sm btn-success"><i class="fas fa-eye"></i> Lihat File</a>
                                    <button type="button" class="btn btn-sm btn-warning" onclick="toggleFileInput('surat_pernyataan_hak_cipta')"><i class="fas fa-sync-alt"></i> Ganti File</button>
                                </div>
                                <input type="file" class="form-control d-none" id="surat_pernyataan_hak_cipta" name="surat_pernyataan_hak_cipta">
                            @else
                                <input type="file" class="form-control" id="surat_pernyataan_hak_cipta" name="surat_pernyataan_hak_cipta">
                            @endif
                        </div>
                        <div class="mb-3">
                            <label for="ktp_seluruh_pencipta" class="form-label">KTP (SELURUH PENCIPTA)</label>
                            @if(isset($dokumen['ktp']) && $dokumen['ktp'])
                                <div class="mb-2 file-lama-exists d-flex align-items-center gap-2">
                                    <a href="{{ asset('storage/'.$dokumen['ktp']) }}" target="_blank" class="btn btn-sm btn-success"><i class="fas fa-eye"></i> Lihat File</a>
                                    <button type="button" class="btn btn-sm btn-warning" onclick="toggleFileInput('ktp_seluruh_pencipta')"><i class="fas fa-sync-alt"></i> Ganti File</button>
                                </div>
                                <input type="file" class="form-control d-none" id="ktp_seluruh_pencipta" name="ktp_seluruh_pencipta">
                            @else
                                <input type="file" class="form-control" id="ktp_seluruh_pencipta" name="ktp_seluruh_pencipta">
                            @endif
                        </div>
                        <div class="d-flex justify-content-between mt-3">
                            <button type="button" class="btn btn-secondary prev-section" data-prev="data-pencipta">
                                <i class="fas fa-arrow-left me-2"></i> Sebelumnya
                            </button>
                            <div>
                                <button type="submit" name="save_as_draft" value="1" class="btn btn-secondary" formnovalidate>Simpan sebagai Draft</button>
                                <button type="submit" name="ajukan" value="1" class="btn btn-primary ms-2" id="btn-ajukan" style="display:none;">Kirim</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
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

    // Toggle field NIP/NIDN dan ID Sinta sesuai role (dosen/mahasiswa)
    const roleRadios = document.querySelectorAll('input[name="role"]');
    const nipNidnField = document.getElementById('nip-nidn-field');
    const idSintaField = document.getElementById('id-sinta-field');
    function toggleRoleSpecificFields() {
        const selectedRoleRadio = document.querySelector('input[name="role"]:checked');
        const selectedRole = selectedRoleRadio ? selectedRoleRadio.value : '';
        if (selectedRole === 'mahasiswa') {
            // Sembunyikan field NIP/NIDN dan ID Sinta
            nipNidnField.style.display = 'none';
            idSintaField.style.display = 'none';
            // Hapus required dan kosongkan value
            if (nipNidnField.querySelector('input')) {
                nipNidnField.querySelector('input').removeAttribute('required');
                nipNidnField.querySelector('input').value = '';
            }
            if (idSintaField.querySelector('input')) {
                idSintaField.querySelector('input').removeAttribute('required');
                idSintaField.querySelector('input').value = '';
            }
        } else {
            // Tampilkan kembali field NIP/NIDN dan ID Sinta
            nipNidnField.style.display = '';
            idSintaField.style.display = '';
            if (nipNidnField.querySelector('input')) {
                nipNidnField.querySelector('input').setAttribute('required', true);
            }
        }
    }
    toggleRoleSpecificFields();
    roleRadios.forEach(radio => {
        radio.addEventListener('change', toggleRoleSpecificFields);
    });

    // Tampilkan tombol Ajukan jika semua field required di seluruh form terisi (support radio group)
    const btnAjukan = document.getElementById('btn-ajukan');
    function cekFieldTerisi() {
        let filled = true;
        const requiredFields = form.querySelectorAll('input[required], select[required], textarea[required]');
        const radioNames = new Set();
        requiredFields.forEach(function(el) {
            if (el.type === 'radio') {
                radioNames.add(el.name);
            } else if (el.offsetParent !== null && !el.value) {
                filled = false;
            }
        });
        radioNames.forEach(function(name) {
            const radios = form.querySelectorAll('input[type="radio"][name="' + name + '"]');
            let checked = false;
            radios.forEach(function(radio) { if (radio.checked && radio.offsetParent !== null) checked = true; });
            if (!checked) filled = false;
        });
        btnAjukan.style.display = '';
        btnAjukan.disabled = !filled;
    }
    form.querySelectorAll('input,select,textarea').forEach(function(el) {
        el.addEventListener('input', cekFieldTerisi);
        el.addEventListener('change', cekFieldTerisi);
    });
    cekFieldTerisi();

    // Tambahkan arahan jika user klik tombol Kirim saat data belum lengkap
    if (btnAjukan) {
        btnAjukan.addEventListener('click', function(e) {
            // Validasi dokumen wajib (frontend)
            let dokumenError = '';
            // Cek file lama sudah ada atau belum
            function dokumenLamaAda(tipe) {
                // Cek apakah ada tombol "Lihat File" untuk tipe dokumen tsb
                // tipe: 'surat_pengalihan', 'surat_pernyataan', 'ktp'
                const dokumen = document.querySelectorAll('.file-lama-exists a');
                for (let i = 0; i < dokumen.length; i++) {
                    // Cek href mengandung nama file lama dari value dokumen di backend
                    if (dokumen[i].href && dokumen[i].href !== '#' && dokumen[i].href !== '') return true;
                }
                return false;
            }
            // Cek dokumen wajib
            if (
                (!document.getElementById('surat_pengalihan_hak_cipta').files.length && !dokumenLamaAda('surat_pengalihan'))
            ) {
                dokumenError = 'Surat pengalihan hak cipta wajib diupload.';
            } else if (
                (!document.getElementById('surat_pernyataan_hak_cipta').files.length && !dokumenLamaAda('surat_pernyataan'))
            ) {
                dokumenError = 'Surat pernyataan hak cipta wajib diupload.';
            } else if (
                (!document.getElementById('ktp_seluruh_pencipta').files.length && !dokumenLamaAda('ktp'))
            ) {
                dokumenError = 'KTP seluruh pencipta wajib diupload.';
            }
            if (dokumenError) {
                e.preventDefault();
                alert(dokumenError);
                return false;
            }
            // Validasi field lain (seperti sebelumnya)
            if (btnAjukan.disabled) {
                e.preventDefault();
                alert('Lengkapi semua field wajib sebelum mengirim pengajuan!');
                return false;
            }
        });
    }

    // Filter baris pencipta kosong sebelum submit form
    document.getElementById('form-edit-draft').addEventListener('submit', function(e) {
        // Hanya filter jika ada field pencipta
        const penciptaContainer = document.getElementById('pencipta-container');
        if (penciptaContainer) {
            penciptaContainer.querySelectorAll('.card').forEach(function(card) {
                const nama = card.querySelector('input[name*="[nama]"]');
                const email = card.querySelector('input[name*="[email]"]');
                if (nama && email && (!nama.value.trim() && !email.value.trim())) {
                    // Hapus semua input di card ini agar tidak terkirim
                    card.querySelectorAll('input,textarea,select').forEach(function(input) {
                        input.disabled = true;
                    });
                }
            });
        }
    });

    // Sinkronisasi value dropdown jumlah_pencipta dengan jumlah data pencipta terakhir
    const jumlahPenciptaSelect = document.getElementById('jumlah_pencipta');
    const jumlahPenciptaHidden = document.getElementById('jumlah_pencipta_hidden');
    if (jumlahPenciptaSelect && jumlahPenciptaHidden && !jumlahPenciptaSelect.value) {
        const val = jumlahPenciptaHidden.value;
        if (val && parseInt(val) > 0) {
            jumlahPenciptaSelect.value = val + ' orang';
        }
    }
    // Render field pencipta sesuai value dropdown saat halaman dimuat
    const penciptaContainer = document.getElementById('pencipta-container');
    function getPenciptaData() {
        const data = [];
        penciptaContainer.querySelectorAll('.card').forEach(function(card, i) {
            data[i] = {
                nama: card.querySelector('input[name$="[nama]"]')?.value || '',
                email: card.querySelector('input[name$="[email]"]')?.value || '',
                no_hp: card.querySelector('input[name$="[no_hp]"]')?.value || '',
                alamat: card.querySelector('textarea[name$="[alamat]"]')?.value || '',
                kecamatan: card.querySelector('input[name$="[kecamatan]"]')?.value || '',
                kodepos: card.querySelector('input[name$="[kodepos]"]')?.value || ''
            };
        });
        return data;
    }
    function renderPenciptaForm(jumlah) {
        const penciptaData = getPenciptaData();
        penciptaContainer.innerHTML = '';
        for (let i = 0; i < jumlah; i++) {
            const p = penciptaData[i] || {};
            penciptaContainer.insertAdjacentHTML('beforeend', `
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">Data Pencipta ${i+1}</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="pencipta[${i}][nama]" value="${p.nama || ''}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="pencipta[${i}][email]" value="${p.email || ''}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No. HP <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" name="pencipta[${i}][no_hp]" value="${p.no_hp || ''}" required pattern="^08[0-9]{8,11}$" maxlength="15">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Alamat <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="pencipta[${i}][alamat]" rows="2" required>${p.alamat || ''}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kecamatan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="pencipta[${i}][kecamatan]" value="${p.kecamatan || ''}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kode Pos <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="pencipta[${i}][kodepos]" value="${p.kodepos || ''}" required pattern="^[0-9]{5}$" maxlength="5">
                                <div class="invalid-feedback">Kode Pos harus 5 digit angka.</div>
                            </div>
                        </div>
                    </div>
                </div>
            `);
        }
    }
    if (jumlahPenciptaSelect && penciptaContainer) {
        // Hanya render field pencipta via JS jika user mengubah dropdown
        jumlahPenciptaSelect.addEventListener('change', function() {
            const jumlah = parseInt(this.value) || 0;
            renderPenciptaForm(jumlah);
        });
    }

    // Disable tombol Simpan sebagai Draft jika ada pencipta yang belum isi nama/email
    function cekPenciptaDraftValid() {
        let valid = true;
        const penciptaCards = document.querySelectorAll('#pencipta-container .card');
        penciptaCards.forEach(function(card, idx) {
            const nama = card.querySelector('input[name*="[nama]"]');
            const email = card.querySelector('input[name*="[email]"]');
            const errorNama = card.querySelector('.invalid-feedback#error-nama-' + idx);
            const errorEmail = card.querySelector('.invalid-feedback#error-email-' + idx);
            let filled = false;
            card.querySelectorAll('input,textarea').forEach(function(input) {
                if (input.value && input.value.trim() !== '') filled = true;
            });
            if (filled && (!nama.value.trim() || !email.value.trim())) {
                valid = false;
                if (errorNama && !nama.value.trim()) errorNama.classList.remove('d-none');
                else if (errorNama) errorNama.classList.add('d-none');
                if (errorEmail && !email.value.trim()) errorEmail.classList.remove('d-none');
                else if (errorEmail) errorEmail.classList.add('d-none');
            } else {
                if (errorNama) errorNama.classList.add('d-none');
                if (errorEmail) errorEmail.classList.add('d-none');
            }
        });
        return valid;
    }
    document.getElementById('pencipta-container')?.addEventListener('input', cekPenciptaDraftValid);
    cekPenciptaDraftValid();

    // Intercept tombol Simpan sebagai Draft
    document.querySelectorAll('button[name="save_as_draft"]').forEach(function(btn) {
        btn.disabled = false; // pastikan selalu enable
        btn.addEventListener('click', function(e) {
            const valid = cekPenciptaDraftValid();
            const notif = document.getElementById('notif-pencipta-draft');
            if (!valid) {
                e.preventDefault();
                if (notif) {
                    notif.textContent = 'Lengkapi Nama dan Email untuk setiap pencipta sebelum menyimpan draft.';
                    notif.classList.remove('d-none');
                    notif.scrollIntoView({behavior: 'smooth', block: 'center'});
                }
            } else if (notif) {
                notif.classList.add('d-none');
            }
        });
    });

    // Simpan tab aktif ke localStorage saat user klik tab
    document.querySelectorAll('#form-tabs .nav-link').forEach(function(tabBtn) {
        tabBtn.addEventListener('click', function() {
            localStorage.setItem('draftTabActive', this.getAttribute('data-bs-target'));
        });
    });
    // Aktifkan tab terakhir saat halaman dimuat
    const lastTab = localStorage.getItem('draftTabActive');
    if (lastTab) {
        const tabBtn = document.querySelector(`#form-tabs .nav-link[data-bs-target='${lastTab}']`);
        if (tabBtn && !tabBtn.disabled) {
            new bootstrap.Tab(tabBtn).show();
        }
    }

    // Navigasi otomatis ke tab error, tampilkan daftar link ke tab error
    form.addEventListener('submit', function(e) {
        let firstInvalid = null;
        let errorTab = null;
        let errorMsg = '';
        let errorTabs = new Set();
        let errorFields = [];
        // Cek semua required field yang visible
        form.querySelectorAll('input[required], select[required], textarea[required]').forEach(function(el) {
            if (el.offsetParent !== null && !el.value) {
                if (!firstInvalid) firstInvalid = el;
                // Cari tab/section
                let tabPane = el.closest('.tab-pane');
                if (tabPane) {
                    errorTab = tabPane.id;
                    errorTabs.add(tabPane.id);
                    let label = tabPane.querySelector('label[for="'+el.id+'"]')?.textContent || el.name || 'Field';
                    errorFields.push({tab: tabPane.id, label: label, el: el});
                }
                el.classList.add('is-invalid');
                errorMsg = 'Ada field wajib yang belum diisi. Silakan lengkapi.';
            } else {
                el.classList.remove('is-invalid');
            }
        });
        // Cek radio group (khusus role)
        const roleRadios = form.querySelectorAll('input[name="role"]');
        if (roleRadios.length && !Array.from(roleRadios).some(r=>r.checked && r.offsetParent!==null)) {
            if (!firstInvalid) firstInvalid = roleRadios[0];
            let tabPane = roleRadios[0].closest('.tab-pane');
            if (tabPane) {
                errorTab = tabPane.id;
                errorTabs.add(tabPane.id);
                errorFields.push({tab: tabPane.id, label: 'Peran Pengusul', el: roleRadios[0]});
            }
            roleRadios.forEach(r=>r.classList.add('is-invalid'));
            errorMsg = 'Pilih peran pengusul (Dosen/Mahasiswa)';
        } else {
            roleRadios.forEach(r=>r.classList.remove('is-invalid'));
        }
        // Highlight tab error
        document.querySelectorAll('#form-tabs .nav-link').forEach(function(tabBtn) {
            tabBtn.classList.remove('bg-danger','text-white');
            let target = tabBtn.getAttribute('data-bs-target')?.replace('#','');
            if (errorTabs.has(target)) {
                tabBtn.classList.add('bg-danger','text-white');
            }
        });
        // Tampilkan daftar link ke tab error
        const errorList = document.getElementById('global-error-list');
        if (errorTabs.size > 0 && errorList) {
            let html = '<b>Bagian yang perlu diperbaiki:</b><ul>';
            let tabNames = {
                'data-pengusul': 'Data Pengusul',
                'data-ciptaan': 'Data Ciptaan',
                'data-pencipta': 'Data Pencipta',
                'dokumen': 'Dokumen Pendukung'
            };
            errorTabs.forEach(function(tab) {
                html += `<li><a href="#" onclick="gotoTab('${tab}');return false;">${tabNames[tab]||tab}</a></li>`;
            });
            html += '</ul>';
            errorList.innerHTML = html;
            errorList.classList.remove('d-none');
            errorList.scrollIntoView({behavior:'smooth', block:'center'});
        } else if (errorList) {
            errorList.classList.add('d-none');
        }
        if (firstInvalid) {
            e.preventDefault();
            // Tampilkan pesan error global
            const alert = document.getElementById('global-error-alert');
            if (alert) {
                alert.textContent = errorMsg || 'Ada field wajib yang belum diisi.';
                alert.classList.remove('d-none');
                alert.scrollIntoView({behavior:'smooth', block:'center'});
            }
            // Pindah ke tab error
            if (errorTab) {
                gotoTab(errorTab);
            }
            // Fokus ke field error
            setTimeout(()=>{firstInvalid.focus();}, 300);
            return false;
        } else {
            const alert = document.getElementById('global-error-alert');
            if (alert) alert.classList.add('d-none');
            if (errorList) errorList.classList.add('d-none');
        }
        // Tampilkan/hilangkan tanda error di tab dan teks error di judul section
        ['data-pengusul','data-ciptaan','data-pencipta','dokumen'].forEach(function(tab) {
            const ind = document.getElementById('err-ind-' + tab.replace('data-','data-'));
            const txt = document.getElementById('err-text-' + tab.replace('data-','data-'));
            if (errorTabs.has(tab)) {
                if (ind) ind.classList.remove('d-none');
                if (txt) txt.classList.remove('d-none');
            } else {
                if (ind) ind.classList.add('d-none');
                if (txt) txt.classList.add('d-none');
            }
        });
        // Inisialisasi tooltip Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
    // Fungsi lompat ke tab tertentu
    function gotoTab(tabId) {
        const tabBtn = document.querySelector(`#form-tabs .nav-link[data-bs-target='#${tabId}']`);
        if (tabBtn) {
            tabBtn.removeAttribute('disabled');
            tabBtn.click();
        }
    }

    // --- LOGIKA TAB HANYA BISA DIKLIK JIKA SUDAH PERNAH DIISI/DIBUKA ---
    // Key untuk localStorage
    const visitedKey = 'draftTabVisited';
    // Ambil status visited dari localStorage
    let visitedTabs = JSON.parse(localStorage.getItem(visitedKey) || '{}');
    // Section urut
    const sectionIds = ['data-pengusul', 'data-ciptaan', 'data-pencipta', 'dokumen'];

    // Enable tab yang sudah pernah diisi/dibuka (visited)
    sectionIds.forEach((sectionId, idx) => {
        const tabBtn = document.querySelector(`#${sectionId}-tab`);
        if (tabBtn) {
            if (idx === 0 || visitedTabs[sectionId]) {
                tabBtn.removeAttribute('disabled');
            } else {
                tabBtn.setAttribute('disabled', 'disabled');
            }
        }
    });

    // Saat klik Selanjutnya, enable tab berikutnya dan tandai visited
    document.querySelectorAll('.next-section').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            const nextSectionId = btn.dataset.next;
            const nextTabButton = document.querySelector(`#${nextSectionId}-tab`);
            if (nextTabButton) {
                nextTabButton.removeAttribute('disabled');
                // Tandai visited
                visitedTabs[nextSectionId] = true;
                localStorage.setItem(visitedKey, JSON.stringify(visitedTabs));
                // Aktifkan tab berikutnya
                new bootstrap.Tab(nextTabButton).show();
            }
        });
    });

    // Saat klik tab, hanya izinkan jika sudah enable
    document.querySelectorAll('#form-tabs .nav-link').forEach(function(tabBtn) {
        tabBtn.addEventListener('click', function(e) {
            if (tabBtn.hasAttribute('disabled')) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
            // Tandai tab ini sebagai visited
            const sectionId = tabBtn.getAttribute('data-bs-target').replace('#','');
            visitedTabs[sectionId] = true;
            localStorage.setItem(visitedKey, JSON.stringify(visitedTabs));
        });
    });

    // Reset visited jika user reload dan data draft baru (opsional, bisa disesuaikan)
    // ... existing code ...
});

function toggleFileInput(id) {
    var input = document.getElementById(id);
    if (input) {
        input.classList.toggle('d-none');
        input.value = '';
        input.scrollIntoView({behavior: 'smooth', block: 'center'});
    }
}
</script>
@endpush
@push('styles')
<style>
/* Pastikan radio checked tetap biru walau readonly */
input[readonly].role-radio:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}
input[readonly].role-radio {
    pointer-events: none;
}
input[type="radio"].role-radio:checked:disabled {
    accent-color: #198754; /* hijau bootstrap */
    border-color: #198754;
    background-color: #198754;
}
input[type="radio"].role-radio:disabled {
    border-color: #ccc;
    background-color: #f8f9fa;
}
</style>
@endpush
@endsection 