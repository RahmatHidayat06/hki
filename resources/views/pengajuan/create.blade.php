@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Form Usulan Hak Cipta</h4>
                </div>

                <div class="card-body">
                    <!-- Navigasi Section -->
                    <div class="mb-4">
                        <div class="nav nav-pills nav-fill" id="form-tabs" role="tablist">
                            <button class="nav-link active" id="data-pengusul-tab" data-bs-toggle="pill" data-bs-target="#data-pengusul" type="button" role="tab">
                                <i class="fas fa-user me-2"></i>Data Pengusul
                            </button>
                            <button class="nav-link" id="data-ciptaan-tab" data-bs-toggle="pill" data-bs-target="#data-ciptaan" type="button" disabled>
                                <i class="fas fa-book me-2"></i>Data Ciptaan
                            </button>
                            <button class="nav-link" id="data-pencipta-tab" data-bs-toggle="pill" data-bs-target="#data-pencipta" type="button" disabled>
                                <i class="fas fa-users me-2"></i>Data Pencipta
                            </button>
                            <button class="nav-link" id="dokumen-tab" data-bs-toggle="pill" data-bs-target="#dokumen" type="button" role="tab" disabled>
                                <i class="fas fa-file me-2"></i>Dokumen
                            </button>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('pengajuan.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="tab-content" id="form-tabs-content">
                            <!-- Section 1: Data Pengusul -->
                            <div class="tab-pane fade show active" id="data-pengusul" role="tabpanel">
                                <div class="card">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0"><i class="fas fa-user me-2"></i>Data Pengusul</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Pilihan Role -->
                                        <div class="mb-4">
                                            <label class="form-label">{{ __('Anda mengajukan sebagai?') }}</label>
                                            <div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="role" id="role-dosen" value="dosen" checked>
                                                    <label class="form-check-label" for="role-dosen">{{ __('Dosen') }}</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="role" id="role-mahasiswa" value="mahasiswa">
                                                    <label class="form-check-label" for="role-mahasiswa">{{ __('Mahasiswa') }}</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="nama_pengusul" class="form-label">{{ __('Nama Lengkap') }}</label>
                                                <input type="text" class="form-control @error('nama_pengusul') is-invalid @enderror" 
                                                       id="nama_pengusul" name="nama_pengusul" value="{{ old('nama_pengusul') }}" required>
                                                @error('nama_pengusul')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3" id="nip-nidn-field">
                                                <label for="nip_nidn" class="form-label">{{ __('NIP/NIDN') }}</label>
                                                <input type="text" class="form-control @error('nip_nidn') is-invalid @enderror" 
                                                       id="nip_nidn" name="nip_nidn" value="{{ old('nip_nidn') }}" required>
                                                @error('nip_nidn')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="no_hp" class="form-label">{{ __('Nomor HP') }}</label>
                                                <input type="text" class="form-control @error('no_hp') is-invalid @enderror" 
                                                       id="no_hp" name="no_hp" value="{{ old('no_hp') }}" required>
                                                @error('no_hp')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="tahun_usulan" class="form-label">Tahun Usulan <span class="text-danger">*</span></label>
                                                <select class="form-select @error('tahun_usulan') is-invalid @enderror" id="tahun_usulan" name="tahun_usulan" required>
                                                    <option value="">Pilih</option>
                                                    <!-- Opsi tahun akan diisi oleh JavaScript -->
                                                </select>
                                                @error('tahun_usulan')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3" id="id-sinta-field">
                                                <label for="id_sinta" class="form-label">{{ __('ID Sinta') }}</label>
                                                <input type="text" class="form-control @error('id_sinta') is-invalid @enderror" 
                                                       id="id_sinta" name="id_sinta" value="{{ old('id_sinta') }}">
                                                @error('id_sinta')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                         <div class="d-flex justify-content-end mt-3">
                                             <button type="button" class="btn btn-primary next-section" data-next="data-ciptaan">
                                                 Selanjutnya <i class="fas fa-arrow-right ms-2"></i>
                                             </button>
                                         </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 2: Data Ciptaan -->
                            <div class="tab-pane fade" id="data-ciptaan" role="tabpanel">
                                <div class="card">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0"><i class="fas fa-book me-2"></i>Data Ciptaan</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="judul" class="form-label">{{ __('Judul Ciptaan') }}</label>
                                            <input type="text" class="form-control @error('judul') is-invalid @enderror" 
                                                   id="judul" name="judul" value="{{ old('judul') }}" required>
                                            @error('judul')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="kategori" class="form-label">{{ __('Kategori') }}</label>
                                            <input type="text" class="form-control @error('kategori') is-invalid @enderror" 
                                                   id="kategori" name="kategori" value="{{ old('kategori') }}" required>
                                            @error('kategori')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="identitas_ciptaan" class="form-label">{{ __('Jenis Ciptaan') }}</label>
                                            <select class="form-select @error('identitas_ciptaan') is-invalid @enderror" 
                                                    id="identitas_ciptaan" name="identitas_ciptaan" required>
                                                <option value="">Pilih Jenis Ciptaan</option>
                                                <option value="karya tulis" {{ old('identitas_ciptaan') == 'karya tulis' ? 'selected' : '' }}>Karya Tulis</option>
                                                <option value="karya audio visual" {{ old('identitas_ciptaan') == 'karya audio visual' ? 'selected' : '' }}>Karya Audio Visual</option>
                                                <option value="karya lainnya" {{ old('identitas_ciptaan') == 'karya lainnya' ? 'selected' : '' }}>Karya Lainnya</option>
                                            </select>
                                            @error('identitas_ciptaan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="sub_jenis_ciptaan" class="form-label">{{ __('Sub Jenis Ciptaan') }}</label>
                                            <select class="form-select @error('sub_jenis_ciptaan') is-invalid @enderror" 
                                                    id="sub_jenis_ciptaan" name="sub_jenis_ciptaan" required>
                                                <option value="">Pilih Sub Jenis Ciptaan</option>
                                                <!-- Opsi akan diisi oleh JavaScript -->
                                            </select>
                                            @error('sub_jenis_ciptaan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="deskripsi" class="form-label">{{ __('Deskripsi Ciptaan') }}</label>
                                            <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                                                      id="deskripsi" name="deskripsi" rows="4" required>{{ old('deskripsi') }}</textarea>
                                            @error('deskripsi')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="tanggal_pertama_kali_diumumkan" class="form-label">{{ __('Tanggal Pertama Kali Diumumkan') }}</label>
                                            <input type="date" class="form-control @error('tanggal_pertama_kali_diumumkan') is-invalid @enderror" 
                                                   id="tanggal_pertama_kali_diumumkan" name="tanggal_pertama_kali_diumumkan" 
                                                   value="{{ old('tanggal_pertama_kali_diumumkan') }}" required>
                                            @error('tanggal_pertama_kali_diumumkan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="d-flex justify-content-between mt-3">
                                            <button type="button" class="btn btn-secondary prev-section" data-prev="data-pengusul">
                                                <i class="fas fa-arrow-left me-2"></i> Sebelumnya
                                            </button>
                                            <button type="button" class="btn btn-primary next-section" data-next="data-pencipta">
                                                Selanjutnya <i class="fas fa-arrow-right ms-2"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 3: Data Pencipta -->
                            <div class="tab-pane fade" id="data-pencipta" role="tabpanel">
                                <div class="card">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0"><i class="fas fa-users me-2"></i>Data Pencipta</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="jumlah_pencipta" class="form-label">{{ __('Jumlah Pencipta') }}</label>
                                            <select class="form-select @error('jumlah_pencipta') is-invalid @enderror" 
                                                    id="jumlah_pencipta" name="jumlah_pencipta" required>
                                                <option value="">Pilih Jumlah Pencipta</option>
                                                <option value="1 orang" {{ old('jumlah_pencipta') == '1 orang' ? 'selected' : '' }}>1 orang</option>
                                                <option value="2 orang" {{ old('jumlah_pencipta') == '2 orang' ? 'selected' : '' }}>2 orang</option>
                                                <option value="3 orang" {{ old('jumlah_pencipta') == '3 orang' ? 'selected' : '' }}>3 orang</option>
                                                <option value="4 orang" {{ old('jumlah_pencipta') == '4 orang' ? 'selected' : '' }}>4 orang</option>
                                                <option value="5 orang" {{ old('jumlah_pencipta') == '5 orang' ? 'selected' : '' }}>5 orang</option>
                                            </select>
                                            @error('jumlah_pencipta')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div id="pencipta-container">
                                            <!-- Container untuk form pencipta akan diisi secara dinamis -->
                                        </div>

                                        <div class="d-flex justify-content-between mt-3">
                                            <button type="button" class="btn btn-secondary prev-section" data-prev="data-ciptaan">
                                                <i class="fas fa-arrow-left me-2"></i> Sebelumnya
                                            </button>
                                            <button type="button" class="btn btn-primary next-section" data-next="dokumen">
                                                Selanjutnya <i class="fas fa-arrow-right ms-2"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 4: Dokumen -->
                            <div class="tab-pane fade" id="dokumen" role="tabpanel">
                                <div class="card">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0"><i class="fas fa-file me-2"></i>Dokumen Pendukung</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="contoh_ciptaan" class="form-label">{{ __('CONTOH CIPTAAN *') }}</label>
                                            <input type="file" class="form-control @error('contoh_ciptaan') is-invalid @enderror" 
                                                   id="contoh_ciptaan" name="contoh_ciptaan" required>
                                            <div class="form-text">Upload 1 supported file: PDF, audio, drawing, image, or video. Max 10 MB.</div>
                                            @error('contoh_ciptaan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="surat_pengalihan_hak_cipta" class="form-label">{{ __('Surat Pengalihan Hak Cipta (Format Pdf) *') }}</label>
                                            <p class="form-text"><a href="https://bit.ly/TemplatePengalihanHakCipta" target="_blank" class="btn btn-sm btn-secondary"><i class="fas fa-download me-2"></i> Download Template</a></p>
                                            <input type="file" class="form-control @error('surat_pengalihan_hak_cipta') is-invalid @enderror" 
                                                   id="surat_pengalihan_hak_cipta" name="surat_pengalihan_hak_cipta" required>
                                            <div class="form-text">Upload 1 supported file: PDF. Max 10 MB.</div>
                                            @error('surat_pengalihan_hak_cipta')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="surat_pernyataan_hak_cipta" class="form-label">{{ __('Surat Pernyataan Hak Cipta (Format Pdf) *') }}</label>
                                             <p class="form-text"><a href="https://bit.ly/TemplatePernyataanHakCipta" target="_blank" class="btn btn-sm btn-secondary"><i class="fas fa-download me-2"></i> Download Template</a></p>
                                            <input type="file" class="form-control @error('surat_pernyataan_hak_cipta') is-invalid @enderror" 
                                                   id="surat_pernyataan_hak_cipta" name="surat_pernyataan_hak_cipta" required>
                                            <div class="form-text">Upload 1 supported file: PDF. Max 10 MB.</div>
                                            @error('surat_pernyataan_hak_cipta')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="ktp_seluruh_pencipta" class="form-label">{{ __('KTP (SELURUH PENCIPTA) *') }}</label>
                                             <p class="form-text"><a href="https://bit.ly/TemplateKTP_HakCipta" target="_blank" class="btn btn-sm btn-secondary"><i class="fas fa-download me-2"></i> Download Template KTP</a> (Lampirkan KTP seluruh pencipta format Pdf)</p>
                                            <input type="file" class="form-control @error('ktp_seluruh_pencipta') is-invalid @enderror" 
                                                   id="ktp_seluruh_pencipta" name="ktp_seluruh_pencipta" required>
                                            <div class="form-text">Upload 1 supported file: PDF. Max 10 MB.</div>
                                            @error('ktp_seluruh_pencipta')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="d-flex justify-content-between mt-3">
                                            <button type="button" class="btn btn-secondary prev-section" data-prev="data-pencipta">
                                                <i class="fas fa-arrow-left me-2"></i> Sebelumnya
                                            </button>
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-save me-2"></i> Simpan Pengajuan
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fungsi untuk navigasi antar section
    const nextButtons = document.querySelectorAll('.next-section');
    const prevButtons = document.querySelectorAll('.prev-section');
    
    // Handle next button click
    nextButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            const currentTabPane = this.closest('.tab-pane');
            // Validasi semua input/select/textarea required di dalam tab-pane
            const requiredFields = currentTabPane.querySelectorAll('input[required], select[required], textarea[required]');
            let isSectionValid = true;
            requiredFields.forEach(field => {
                if (!field.checkValidity()) {
                    isSectionValid = false;
                    field.reportValidity();
                }
            });

            if (isSectionValid) {
                // Jika section valid, langsung picu klik pada tombol tab berikutnya di bagian atas
                const nextSectionId = this.dataset.next;
                const nextTabButton = document.querySelector(`#${nextSectionId}-tab`);
                if (nextTabButton) {
                    // Hapus atribut disabled agar tab bisa diakses
                    nextTabButton.removeAttribute('disabled');
                    nextTabButton.click(); // Panggil event klik pada tombol tab
                }
            }
            // Browser akan menampilkan pesan validasi jika isSectionValid false
        });
    });
    
    // Handle previous button click
    prevButtons.forEach(button => {
        button.addEventListener('click', function() {
            const prevSection = this.dataset.prev;
            const prevTabButton = document.querySelector(`#${prevSection}-tab`);
            if (prevTabButton) {
                prevTabButton.click();
            }
        });
    });

    // Fungsi untuk menangani form pencipta dinamis
    const jumlahPenciptaSelect = document.getElementById('jumlah_pencipta');
    const penciptaContainer = document.getElementById('pencipta-container');

    jumlahPenciptaSelect.addEventListener('change', function() {
        const jumlah = parseInt(this.value);
        penciptaContainer.innerHTML = '';

        for (let i = 0; i < jumlah; i++) {
            const penciptaForm = `
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">Data Pencipta ${i+1}</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" name="pencipta[${i}][nama]" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="pencipta[${i}][email]" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No. HP</label>
                                <input type="text" class="form-control" name="pencipta[${i}][no_hp]">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea class="form-control" name="pencipta[${i}][alamat]" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kecamatan</label>
                                <input type="text" class="form-control" name="pencipta[${i}][kecamatan]">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kode Pos</label>
                                <input type="text" class="form-control" name="pencipta[${i}][kodepos]">
                            </div>
                        </div>
                    </div>
                </div>
            `;
            penciptaContainer.insertAdjacentHTML('beforeend', penciptaForm);
        }
    });

    // Fungsi untuk menampilkan/menyembunyikan field berdasarkan role
    const roleRadios = document.querySelectorAll('input[name="role"]');
    const nipNidnField = document.getElementById('nip-nidn-field');
    const idSintaField = document.getElementById('id-sinta-field');

    function toggleRoleSpecificFields() {
        const selectedRole = document.querySelector('input[name="role"]:checked').value;
        if (selectedRole === 'mahasiswa') {
            // Sembunyikan field NIP/NIDN dan ID Sinta
            nipNidnField.style.display = 'none';
            idSintaField.style.display = 'none';
            // Hapus atribut required dan kosongkan value
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

    // Panggil fungsi toggle saat pertama kali dimuat untuk mengatur tampilan awal
    toggleRoleSpecificFields();

    // Tambahkan event listener pada perubahan pilihan role
    roleRadios.forEach(radio => {
        radio.addEventListener('change', toggleRoleSpecificFields);
    });

    // Menambahkan validasi pada klik tombol tab navigasi di atas
    const tabButtons = document.querySelectorAll('#form-tabs button[data-bs-toggle="pill"]');

    tabButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            const targetTabId = this.getAttribute('data-bs-target').substring(1);
            const currentActiveTabPane = document.querySelector('.tab-pane.show.active');
            if (!currentActiveTabPane) return; // Cegah error jika tidak ada tab-pane aktif

            // Dapatkan semua tab pane untuk menentukan urutan
            const allPanes = document.querySelectorAll('.tab-pane');
            let currentPaneIndex = -1;
            let targetPaneIndex = -1;
            allPanes.forEach((pane, index) => {
                if (pane.id === currentActiveTabPane.id) currentPaneIndex = index;
                if (pane.id === targetTabId) targetPaneIndex = index;
            });

            // Hanya lakukan validasi jika mencoba pindah ke tab berikutnya
            if (targetPaneIndex > currentPaneIndex) {
                let isPathValid = true;
                const sectionIds = ['data-pengusul', 'data-ciptaan', 'data-pencipta', 'dokumen'];
                for (let i = 0; i < targetPaneIndex; i++) {
                     const paneIdToCheck = sectionIds[i];
                     let isCurrentSectionValid = true;
                     const paneElem = document.getElementById(paneIdToCheck);
                     if (paneElem) {
                         const requiredFields = paneElem.querySelectorAll('input[required], select[required], textarea[required]');
                         requiredFields.forEach(field => {
                             if (!field.checkValidity()) {
                                 isCurrentSectionValid = false;
                                 field.reportValidity();
                             }
                         });
                     }
                     if (!isCurrentSectionValid) {
                         isPathValid = false;
                         event.preventDefault();
                         event.stopPropagation();
                         const invalidSectionId = sectionIds[i];
                         const invalidTabButton = document.querySelector(`#${invalidSectionId}-tab`);
                          if (invalidTabButton && !invalidTabButton.classList.contains('active')) {
                              setTimeout(() => {
                                 const invalidTab = new bootstrap.Tab(invalidTabButton);
                                 invalidTab.show();
                                  setTimeout(() => {
                                      const invalidPane = document.getElementById(invalidSectionId);
                                       const firstInvalidField = invalidPane.querySelector('[required]:invalid');
                                       if(firstInvalidField) {
                                           firstInvalidField.focus();
                                           firstInvalidField.reportValidity();
                                       }
                                  }, 150);
                             }, 50);
                         }
                         break;
                     }
                }
            }
        });
    });

    // Validasi seluruh field required sebelum submit form
    const form = document.querySelector('form[action="{{ route('pengajuan.store') }}"]');
    form.addEventListener('submit', function(event) {
        const allRequiredFields = form.querySelectorAll('input[required], select[required], textarea[required]');
        let isFormValid = true;
        let firstInvalidField = null;
        allRequiredFields.forEach(field => {
            if (!field.checkValidity()) {
                isFormValid = false;
                if (!firstInvalidField) firstInvalidField = field;
            }
        });
        if (!isFormValid) {
            event.preventDefault();
            event.stopPropagation();
            // Cari tab-pane dari field yang error
            const invalidPane = firstInvalidField.closest('.tab-pane');
            if (invalidPane && !invalidPane.classList.contains('show')) {
                // Aktifkan tab yang error
                const invalidTabButton = document.querySelector(`#${invalidPane.id}-tab`);
                if (invalidTabButton) {
                    invalidTabButton.removeAttribute('disabled');
                    invalidTabButton.click();
                }
            }
            firstInvalidField.focus();
            firstInvalidField.reportValidity();
        }
    });

    // Field Tahun Usulan otomatis 4 tahun terakhir
    const tahunUsulanSelect = document.getElementById('tahun_usulan');
    if (tahunUsulanSelect) {
        const tahunSekarang = new Date().getFullYear();
        for (let i = 0; i < 4; i++) {
            const tahun = tahunSekarang - i;
            const option = document.createElement('option');
            option.value = tahun;
            option.textContent = tahun;
            if (tahun == "{{ old('tahun_usulan') }}") {
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
        // Simpan value sebelumnya jika ada
        const prevValue = "{{ old('sub_jenis_ciptaan') }}";
        // Kosongkan opsi
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
    // Inisialisasi saat halaman dimuat
    updateSubJenisCiptaan();
    // Update saat jenis ciptaan berubah
    identitasCiptaanSelect.addEventListener('change', updateSubJenisCiptaan);
});
</script>
@endpush
@endsection 