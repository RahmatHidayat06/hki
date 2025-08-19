@extends('layouts.app')

@section('content')
<x-page-header 
    title="Form Usulan Hak Cipta" 
    description="Lengkapi data berikut untuk mengajukan permohonan HKI"
    icon="fas fa-file-alt"
    :breadcrumbs="[
        ['title' => 'Hak Cipta', 'url' => '#'],
        ['title' => 'Permohonan Baru']
    ]"
/>

<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h5 class="mb-0 fw-semibold text-dark">
                        <i class="fas fa-edit me-2 text-primary"></i>Form Usulan Hak Cipta
                    </h5>
                </div>

                <div class="card-body">
                    <!-- Navigasi Section -->
                    <div class="mb-4">
                        <div class="nav nav-pills nav-fill" id="form-tabs" role="tablist">
                            {{-- Data Pengusul tab removed --}}
                            <button class="nav-link active" id="data-ciptaan-tab" data-bs-toggle="pill" data-bs-target="#data-ciptaan" type="button" role="tab">
                                <i class="fas fa-book me-2"></i>Data Ciptaan
                            </button>
                            <button class="nav-link" id="data-pencipta-tab" data-bs-toggle="pill" data-bs-target="#data-pencipta" type="button" role="tab">
                                <i class="fas fa-users me-2"></i>Data Pencipta
                            </button>
                            <button class="nav-link" id="detail-surat-tab" data-bs-toggle="pill" data-bs-target="#detail-surat" type="button" role="tab">
                                <i class="fas fa-file-signature me-2"></i>Detail Surat
                            </button>
                            <button class="nav-link" id="dokumen-tab" data-bs-toggle="pill" data-bs-target="#dokumen" type="button" role="tab">
                                <i class="fas fa-file me-2"></i>Dokumen
                            </button>
                        </div>
                    </div>

                    <div id="global-error-alert" class="alert alert-danger d-none"></div>

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form id="form-pengajuan" method="POST" action="{{ route('pengajuan.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="tab-content" id="form-tabs-content">
                            <!-- Section 2: Data Ciptaan -->
                            <div class="tab-pane fade show active" id="data-ciptaan" role="tabpanel">
                                <div class="card">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0"><i class="fas fa-book me-2"></i>Data Ciptaan</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="judul" class="form-label">Judul Ciptaan <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('judul') is-invalid @enderror" 
                                                   id="judul" name="judul" value="{{ old('judul') }}" required>
                                            <div class="invalid-feedback">Field ini wajib diisi untuk melanjutkan atau mengirim.</div>
                                            @error('judul')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="identitas_ciptaan" class="form-label">Jenis Ciptaan <span class="text-danger">*</span></label>
                                            <select class="form-select @error('identitas_ciptaan') is-invalid @enderror" 
                                                    id="identitas_ciptaan" name="identitas_ciptaan" required>
                                                <option value="">Pilih Jenis Ciptaan</option>
                                                <option value="karya tulis" {{ old('identitas_ciptaan') == 'karya tulis' ? 'selected' : '' }}>Karya Tulis</option>
                                                <option value="karya audio visual" {{ old('identitas_ciptaan') == 'karya audio visual' ? 'selected' : '' }}>Karya Audio Visual</option>
                                                <option value="karya lainnya" {{ old('identitas_ciptaan') == 'karya lainnya' ? 'selected' : '' }}>Karya Lainnya</option>
                                            </select>
                                            <div class="invalid-feedback">Field ini wajib diisi untuk melanjutkan atau mengirim.</div>
                                            @error('identitas_ciptaan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="sub_jenis_ciptaan" class="form-label">Sub Jenis Ciptaan <span class="text-danger">*</span></label>
                                            <select class="form-select @error('sub_jenis_ciptaan') is-invalid @enderror" 
                                                    id="sub_jenis_ciptaan" name="sub_jenis_ciptaan" required>
                                                <option value="">Pilih Sub Jenis Ciptaan</option>
                                                <!-- Opsi akan diisi oleh JavaScript -->
                                            </select>
                                            <div class="invalid-feedback">Field ini wajib diisi untuk melanjutkan atau mengirim.</div>
                                            @error('sub_jenis_ciptaan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="deskripsi" class="form-label">Deskripsi Ciptaan <span class="text-danger">*</span></label>
                                            <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                                                      id="deskripsi" name="deskripsi" rows="4" required>{{ old('deskripsi') }}</textarea>
                                            <div class="invalid-feedback">Field ini wajib diisi untuk melanjutkan atau mengirim.</div>
                                            @error('deskripsi')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="tanggal_pertama_kali_diumumkan" class="form-label">Tanggal Pertama Kali Diumumkan <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control @error('tanggal_pertama_kali_diumumkan') is-invalid @enderror" 
                                                   id="tanggal_pertama_kali_diumumkan" name="tanggal_pertama_kali_diumumkan" 
                                                   value="{{ old('tanggal_pertama_kali_diumumkan') }}" required>
                                            <div class="invalid-feedback">Field ini wajib diisi untuk melanjutkan atau mengirim.</div>
                                            @error('tanggal_pertama_kali_diumumkan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="kota_pertama_kali_diumumkan" class="form-label">Kota Pertama Kali Diumumkan <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('kota_pertama_kali_diumumkan') is-invalid @enderror" 
                                                   id="kota_pertama_kali_diumumkan" name="kota_pertama_kali_diumumkan" 
                                                   value="{{ old('kota_pertama_kali_diumumkan') }}" placeholder="" required>
                                            <div class="form-text">Nama kota tempat ciptaan pertama kali diumumkan atau dipublikasikan</div>
                                            <div class="invalid-feedback">Field ini wajib diisi untuk melanjutkan atau mengirim.</div>
                                            @error('kota_pertama_kali_diumumkan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="d-flex justify-content-between mt-3">
                                            <button type="button" class="btn btn-secondary prev-section d-none" data-prev="data-pengusul">
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
                                            <label for="jumlah_pencipta" class="form-label">Jumlah Pencipta <span class="text-danger">*</span></label>
                                            <select class="form-select" id="jumlah_pencipta" name="jumlah_pencipta" required>
                                                <option value="">Pilih Jumlah Pencipta</option>
                                                <option value="1">1 Pencipta</option>
                                                <option value="2">2 Pencipta</option>
                                                <option value="3">3 Pencipta</option>
                                                <option value="4">4 Pencipta</option>
                                                <option value="5">5 Pencipta</option>
                                            </select>
                                        </div>
                                        <div id="pencipta-container"></div>
                                        <div class="d-flex justify-content-between mt-3">
                                            <button type="button" class="btn btn-secondary prev-section" data-prev="data-ciptaan">
                                                <i class="fas fa-arrow-left me-2"></i> Sebelumnya
                                            </button>
                                            <button type="button" class="btn btn-primary next-section" data-next="detail-surat">
                                                Selanjutnya <i class="fas fa-arrow-right ms-2"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 4: Detail Surat Pengalihan -->
                            <div class="tab-pane fade" id="detail-surat" role="tabpanel">
                                <div class="card">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0"><i class="fas fa-file-contract me-2"></i>Detail Surat Pengalihan Hak Cipta</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-info border-0 shadow-sm mb-4">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-info-circle text-primary me-3 fs-4"></i>
                                                <div>
                                                    <h6 class="mb-1 fw-bold">📋 Lengkapi Detail untuk Auto-Generate Surat</h6>
                                                    <p class="mb-0 small">
                                                        Data ini akan digunakan untuk mengisi template surat pengalihan hak cipta secara otomatis sesuai format resmi.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="tanggal_surat" class="form-label">Tanggal Surat <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control @error('tanggal_surat') is-invalid @enderror" 
                                                   id="tanggal_surat" name="tanggal_surat" 
                                                   value="{{ old('tanggal_surat', date('Y-m-d')) }}" required>
                                            <div class="form-text">Tanggal yang akan muncul di surat pengalihan</div>
                                            <div class="invalid-feedback">Field ini wajib diisi untuk melanjutkan atau mengirim.</div>
                                            @error('tanggal_surat')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div id="alamat-pencipta-container">
                                            <h6 class="mb-3 text-primary"><i class="fas fa-map-marker-alt me-2"></i>Alamat Lengkap Para Pencipta</h6>
                                            <div class="alert alert-warning border-0">
                                                <small><i class="fas fa-exclamation-triangle me-1"></i> 
                                                Alamat lengkap diperlukan untuk surat pengalihan. Isi sesuai KTP masing-masing pencipta.</small>
                                            </div>
                                            <!-- Alamat akan diisi dinamis oleh JavaScript -->
                                        </div>

                                        <div id="signature-details-container" class="mt-4">
                                            <h6 class="mb-3 text-primary"><i class="fas fa-signature me-2"></i>Detail Tanda Tangan</h6>
                                            <!-- Detail tanda tangan akan diisi dinamis oleh JavaScript -->
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Penggunaan Materai <span class="text-muted">(Opsional)</span></label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="gunakan_materai" id="gunakan_materai" value="1" 
                                                       {{ old('gunakan_materai') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="gunakan_materai">
                                                    Sertakan materai Rp 10.000 pada surat pengalihan
                                                </label>
                                            </div>
                                            <div class="form-text">Materai akan ditampilkan pada template surat jika dipilih</div>
                                        </div>

                                        <div class="d-flex justify-content-between mt-3">
                                            <button type="button" class="btn btn-secondary prev-section" data-prev="data-pencipta">
                                                <i class="fas fa-arrow-left me-2"></i> Sebelumnya
                                            </button>
                                            <button type="button" class="btn btn-primary next-section" data-next="dokumen">
                                                Selanjutnya <i class="fas fa-arrow-right ms-2"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 5: Dokumen -->
                            <div class="tab-pane fade" id="dokumen" role="tabpanel">
                                <div class="card">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0"><i class="fas fa-file me-2"></i>Dokumen Pendukung</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">CONTOH CIPTAAN <span class="text-danger">*</span></label>
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="form-check me-3">
                                                    <input class="form-check-input" type="radio" name="contoh_ciptaan_type" id="contoh_ciptaan_upload" value="upload" checked>
                                                    <label class="form-check-label" for="contoh_ciptaan_upload">Upload</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="contoh_ciptaan_type" id="contoh_ciptaan_link" value="link">
                                                    <label class="form-check-label" for="contoh_ciptaan_link">Link</label>
                                                </div>
                                            </div>
                                            <div id="contoh-ciptaan-upload-field">
                                                <input type="file" class="form-control @error('contoh_ciptaan') is-invalid @enderror" id="contoh_ciptaan" name="contoh_ciptaan" required>
                                                <div class="form-text">Upload 1 supported file: PDF, audio, drawing, image, or video. Max 10 MB.</div>
                                                <div class="invalid-feedback">Field ini wajib diisi untuk melanjutkan atau mengirim.</div>
                                                @error('contoh_ciptaan')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div id="contoh-ciptaan-link-field" style="display:none;">
                                                <input type="url" class="form-control @error('contoh_ciptaan_link') is-invalid @enderror" name="contoh_ciptaan_link" placeholder="https://">
                                                <div class="invalid-feedback">Field ini wajib diisi untuk melanjutkan atau mengirim.</div>
                                                @error('contoh_ciptaan_link')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="alert alert-success border-0 shadow-sm">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-check-circle text-success me-3 fs-4"></i>
                                                    <div>
                                                        <h6 class="mb-1 fw-bold">✅ Surat Otomatis Dibuat!</h6>
                                                        <p class="mb-0 small">
                                                            Sistem akan otomatis membuat <strong>Surat Pengalihan</strong> dan <strong>Surat Pernyataan</strong> Hak Cipta berdasarkan data yang Anda inputkan. 
                                                            <strong>Tidak perlu upload manual!</strong>
                                                        </p>
                                                        <div class="mt-2">
                                                            <span class="badge bg-success text-white me-2">
                                                                <i class="fas fa-magic me-1"></i>Surat Pengalihan: Auto-Generated
                                                            </span>
                                                            <span class="badge bg-success text-white">
                                                                <i class="fas fa-magic me-1"></i>Surat Pernyataan: Auto-Generated
                                                            </span>
                                            </div>
                                                        <div class="mt-2">
                                                            <small class="text-muted">
                                                                <i class="fas fa-info-circle me-1"></i>
                                                                Pastikan data di section "Detail Surat" sudah lengkap untuk hasil yang optimal.
                                                            </small>
                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="alert alert-info border-0">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-info-circle me-3 fs-4"></i>
                                                    <div>
                                                        <h6 class="mb-1">Upload KTP melalui Proses Tanda Tangan</h6>
                                                        <p class="mb-0 small">
                                                            KTP setiap pencipta akan diupload langsung saat proses tanda tangan digital. 
                                                            Sistem akan secara otomatis menggabungkan semua KTP menjadi satu dokumen PDF.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between mt-3">
                                            <button type="button" class="btn btn-secondary prev-section" data-prev="detail-surat">
                                                <i class="fas fa-arrow-left me-2"></i> Sebelumnya
                                            </button>
                                            <div>
                                                <button type="submit" name="action" value="submit" class="btn btn-primary ms-2" id="btn-submit">
                                                    <span class="spinner-border spinner-border-sm d-none" id="spinner-submit" role="status" aria-hidden="true"></span>
                                                    <i class="fas fa-magic me-1"></i>Kirim (dengan Auto-Generate Surat)
                                                </button>
                                            </div>
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

<style>
.card{transition:all .3s ease}.card:hover{transform:translateY(-2px)}.table-responsive{border-radius:.5rem}.table th{font-weight:600;text-transform:uppercase;font-size:.75rem;letter-spacing:.5px}.table td{vertical-align:middle}.badge{font-weight:500;letter-spacing:.25px}.btn{font-weight:500;border-radius:.375rem;transition:all .2s ease}.btn:hover{transform:translateY(-1px)}.form-control:focus{border-color:#0d6efd;box-shadow:0 0 0 .2rem rgba(13,110,253,.25)}.input-group-text{background-color:#f8f9fa;border-color:#dee2e6}
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Navigasi antar section/tab
    const nextButtons = document.querySelectorAll('.next-section');
    const prevButtons = document.querySelectorAll('.prev-section');
    const tabButtons = document.querySelectorAll('#form-tabs button[data-bs-toggle="pill"]');
    // Disable semua tab navigasi kecuali section pertama
    tabButtons.forEach((btn, idx) => {
        if (idx !== 0) btn.setAttribute('disabled', 'disabled'); // Data Ciptaan adalah index 0
    });

    // Function to check if a section is completed
    function isSectionCompleted(sectionId) {
        const section = document.getElementById(sectionId);
        if (!section) return false;
        
        const requiredFields = section.querySelectorAll('input[required], select[required], textarea[required]');
        let isCompleted = true;
        
        requiredFields.forEach(field => {
            if (!field.value || !field.value.trim()) {
                isCompleted = false;
            }
        });
        
        // Special check for radio buttons
        const radioGroups = section.querySelectorAll('input[type="radio"][required]');
        const checkedGroups = new Set();
        radioGroups.forEach(radio => {
            if (radio.checked) {
                checkedGroups.add(radio.name);
            }
        });
        
        // Get unique radio group names
        const allGroups = new Set();
        radioGroups.forEach(radio => allGroups.add(radio.name));
        
        if (allGroups.size !== checkedGroups.size) {
            isCompleted = false;
        }
        
        // Special handling for detail-surat section
        if (sectionId === 'detail-surat') {
            const jumlahPencipta = parseInt(document.getElementById('jumlah_pencipta')?.value) || 0;
            if (jumlahPencipta === 0) {
                isCompleted = false;
            }
        }
        
        return isCompleted;
    }

    // Function to unlock completed sections
    function unlockCompletedSections() {
        const sections = ['data-ciptaan', 'data-pencipta', 'detail-surat', 'dokumen'];
        
        sections.forEach((sectionId, index) => {
            if (index === 0) return; // First section (data-ciptaan) always unlocked
            
            // Check if all previous sections are completed
            let allPreviousCompleted = true;
            for (let i = 0; i < index; i++) {
                if (!isSectionCompleted(sections[i])) {
                    allPreviousCompleted = false;
                    break;
                }
            }
            
            if (allPreviousCompleted) {
                const tabButton = document.querySelector(`#${sectionId}-tab`);
                if (tabButton) {
                    tabButton.removeAttribute('disabled');
                }
            }
        });
    }

    // Re-check unlocked sections when any field changes
    document.addEventListener('input', function() {
        setTimeout(unlockCompletedSections, 100);
    });
    
    document.addEventListener('change', function() {
        setTimeout(unlockCompletedSections, 100);
    });

    // Tombol Selanjutnya: validasi section aktif sebelum lanjut
    nextButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            const currentTabPane = this.closest('.tab-pane');
            const requiredFields = currentTabPane.querySelectorAll('input[required], select[required], textarea[required]');
            let isSectionValid = true;
            let missingFields = [];
            
            requiredFields.forEach(field => {
                if (!field.checkValidity()) {
                    isSectionValid = false;
                    field.classList.add('is-invalid');
                    let label = field.closest('.mb-3,.col-md-6,.col-md-12')?.querySelector('label')?.textContent || field.name || 'Field';
                    if (label && !missingFields.includes(label.trim())) missingFields.push(label.trim());
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            // Special validation for detail-surat section
            if (currentTabPane.id === 'detail-surat') {
                const jumlahPencipta = parseInt(document.getElementById('jumlah_pencipta').value) || 0;
                if (jumlahPencipta === 0) {
                    isSectionValid = false;
                    missingFields.push('Jumlah Pencipta belum dipilih');
                }
                
                // Check if alamat_pencipta fields are filled
                for (let i = 1; i <= jumlahPencipta; i++) {
                    const namaField = document.querySelector(`input[name="alamat_pencipta[${i}][nama]"]`);
                    const alamatField = document.querySelector(`textarea[name="alamat_pencipta[${i}][alamat]"]`);
                    const ttdField = document.querySelector(`input[name="signature_pencipta[${i}][nama_ttd]"]`);
                    
                    if (!namaField || !namaField.value.trim()) {
                        isSectionValid = false;
                        missingFields.push(`Nama Pencipta ${i}`);
                    }
                    if (!alamatField || !alamatField.value.trim()) {
                        isSectionValid = false;
                        missingFields.push(`Alamat Pencipta ${i}`);
                    }
                    if (!ttdField || !ttdField.value.trim()) {
                        isSectionValid = false;
                        missingFields.push(`Nama Tanda Tangan Pencipta ${i}`);
                    }
                }
            }
            
            if (!isSectionValid) {
                event.preventDefault();
                
                // Use SweetAlert2 for better UX
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Data Belum Lengkap',
                        html: '<div class="text-start">Lengkapi data berikut sebelum melanjutkan:<br>• ' + missingFields.join('<br>• ') + '</div>',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                } else {
                alert('Lengkapi data berikut sebelum melanjutkan:\n- ' + missingFields.join('\n- '));
                }
                
                const firstInvalid = currentTabPane.querySelector('.is-invalid');
                if (firstInvalid) setTimeout(()=>{firstInvalid.focus();}, 300);
                return false;
            }
            
            // Jika valid, enable tab berikutnya dan pindah
                const nextSectionId = this.dataset.next;
                const nextTabButton = document.querySelector(`#${nextSectionId}-tab`);
                if (nextTabButton) {
                nextTabButton.removeAttribute('disabled');
                nextTabButton.click();
            }
        });
    });
    // Tombol Sebelumnya
    prevButtons.forEach(button => {
        button.addEventListener('click', function() {
            const prevSection = this.dataset.prev;
            const prevTabButton = document.querySelector(`#${prevSection}-tab`);
            if (prevTabButton) {
                prevTabButton.click();
            }
        });
    });
    // Prevent klik tab navigasi manual jika belum valid
    tabButtons.forEach((btn, idx) => {
        btn.addEventListener('click', function(e) {
            if (btn.hasAttribute('disabled')) {
                e.preventDefault();
                return false;
            }
        });
    });

    // Fungsi untuk menangani form pencipta dinamis
    const jumlahPenciptaSelect = document.getElementById('jumlah_pencipta');
    const penciptaContainer = document.getElementById('pencipta-container');

    jumlahPenciptaSelect.addEventListener('change', function() {
        const jumlah = parseInt(this.value);
        penciptaContainer.innerHTML = '';
        if (!jumlah || jumlah < 1) return;
        for (let i = 0; i < jumlah; i++) {
            const penciptaForm = `
                <div class=\"card mb-3\">
                    <div class=\"card-header\">
                        <h6 class=\"mb-0\">Data Pencipta ${i+1}</h6>
                    </div>
                    <div class=\"card-body\">
                        <div class=\"row\">
                            <div class=\"col-md-6 mb-3\">
                                <label class=\"form-label\">Nama Lengkap <span class=\"text-danger\">*</span></label>
                                <input type=\"text\" class=\"form-control\" name=\"pencipta[${i}][nama]\" required>
                            </div>
                            <div class=\"col-md-6 mb-3\">
                                <label class=\"form-label\">Email <span class=\"text-danger\">*</span></label>
                                <input type=\"email\" class=\"form-control\" name=\"pencipta[${i}][email]\" required>
                            </div>
                        </div>
                        <div class=\"row\">
                            <div class=\"col-md-6 mb-3\">
                                <label class=\"form-label\">No. Telp <span class=\"text-danger\">*</span></label>
                                <input type=\"tel\" class=\"form-control\" name=\"pencipta[${i}][no_telp]\" required maxlength=\"15\">
                            </div>
                            <div class=\"col-md-6 mb-3\">
                                <label class=\"form-label\">Alamat <span class=\"text-danger\">*</span></label>
                                <textarea class=\"form-control\" name=\"pencipta[${i}][alamat]\" rows=\"2\" required></textarea>
                            </div>
                        </div>
                        <div class=\"row\">
                            <div class=\"col-md-6 mb-3\">
                                <label class=\"form-label\">Kewarganegaraan <span class=\"text-danger\">*</span></label>
                                <input type=\"text\" class=\"form-control\" name=\"pencipta[${i}][kewarganegaraan]\" required>
                            </div>
                            <div class=\"col-md-6 mb-3\">
                                <label class="form-label">Kode Pos <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="pencipta[${i}][kodepos]" required pattern="^[0-9]{5}$" maxlength="5">
                                <div class="invalid-feedback">Kode Pos harus 5 digit angka.</div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            penciptaContainer.insertAdjacentHTML('beforeend', penciptaForm);
        }
    });

    // Role-specific fields are now handled server-side with Blade template conditionals

    // Dynamic Alamat dan Signature Details untuk Surat Pengalihan
    function updateDetailSurat() {
        const jumlahPencipta = parseInt(document.getElementById('jumlah_pencipta').value) || 0;
        const alamatContainer = document.getElementById('alamat-pencipta-container');
        const signatureContainer = document.getElementById('signature-details-container');
        
        if (!alamatContainer || !signatureContainer || jumlahPencipta === 0) {
            // Reset containers if no pencipta selected
            if (alamatContainer) {
                alamatContainer.innerHTML = `
                    <h6 class="mb-3 text-primary"><i class="fas fa-map-marker-alt me-2"></i>Alamat Lengkap Para Pencipta</h6>
                    <div class="alert alert-warning border-0">
                        <small><i class="fas fa-exclamation-triangle me-1"></i> 
                        Pilih jumlah pencipta terlebih dahulu untuk menampilkan field alamat.</small>
                    </div>`;
            }
            if (signatureContainer) {
                signatureContainer.innerHTML = `
                    <h6 class="mb-3 text-primary"><i class="fas fa-signature me-2"></i>Detail Tanda Tangan</h6>
                    <div class="alert alert-warning border-0">
                        <small><i class="fas fa-exclamation-triangle me-1"></i> 
                        Pilih jumlah pencipta terlebih dahulu untuk menampilkan field tanda tangan.</small>
                    </div>`;
            }
            return;
        }
        
        // Update alamat pencipta
        let alamatHTML = `
            <h6 class="mb-3 text-primary"><i class="fas fa-map-marker-alt me-2"></i>Alamat Lengkap Para Pencipta</h6>
            <div class="alert alert-warning border-0">
                <small><i class="fas fa-exclamation-triangle me-1"></i> 
                Alamat lengkap diperlukan untuk surat pengalihan. Isi sesuai KTP masing-masing pencipta.</small>
            </div>`;
            
        for (let i = 1; i <= jumlahPencipta; i++) {
            alamatHTML += `
                <div class="mb-3 border p-3 rounded">
                    <h6 class="text-secondary mb-2">
                        <i class="fas fa-user me-2"></i>${i == 1 ? 'Nama Pencipta' : 'Nama Pencipta ' + i}
                    </h6>
                    <div class="row">
                        <div class="col-md-12 mb-2">
                            <label class="form-label small">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" 
                                   name="alamat_pencipta[${i}][nama]" 
                                   placeholder="Nama lengkap pencipta ${i}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small">Alamat Lengkap <span class="text-danger">*</span></label>
                            <textarea class="form-control form-control-sm" 
                                      name="alamat_pencipta[${i}][alamat]" 
                                      placeholder="Alamat lengkap sesuai KTP pencipta ${i}" 
                                      rows="2" required></textarea>
                        </div>
                    </div>
                </div>`;
        }
        
        alamatContainer.innerHTML = alamatHTML;
        
        // Update signature details
        let signatureHTML = `
            <h6 class="mb-3 text-primary"><i class="fas fa-signature me-2"></i>Detail Tanda Tangan</h6>`;
            
        for (let i = 1; i <= jumlahPencipta; i++) {
            signatureHTML += `
                <div class="mb-3 border p-3 rounded">
                    <h6 class="text-secondary mb-2">
                        <i class="fas fa-signature me-2"></i>Tanda Tangan ${i == 1 ? 'Nama Pencipta' : 'Nama Pencipta ' + i}
                    </h6>
                    <div class="row">
                        <div class="col-md-8 mb-2">
                            <label class="form-label small">Nama yang akan muncul di bawah tanda tangan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" 
                                   name="signature_pencipta[${i}][nama_ttd]" 
                                   placeholder="" required>
                            <div class="form-text small">Format: (Nama Lengkap dengan gelar)</div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label small">Posisi di Surat</label>
                            <select class="form-select form-select-sm" name="signature_pencipta[${i}][posisi]">
                                <option value="kanan">Sebelah Kanan</option>
                                <option value="kiri">Sebelah Kiri</option>
                            </select>
                        </div>
                    </div>
                </div>`;
        }
        
        signatureContainer.innerHTML = signatureHTML;
        
        // Re-check section completion after dynamic fields are added
        setTimeout(() => {
            unlockCompletedSections();
        }, 100);
    }

    // Event listener untuk jumlah pencipta
    document.getElementById('jumlah_pencipta').addEventListener('change', updateDetailSurat);
    
    // Initialize detail surat when page loads
    setTimeout(() => {
        updateDetailSurat();
        // Force initial check for completion
        unlockCompletedSections();
    }, 500);

    // Auto-sync nama pencipta dari section sebelumnya
    function autoSyncPenciptaData() {
        const jumlahPencipta = parseInt(document.getElementById('jumlah_pencipta').value) || 0;
        
        for (let i = 1; i <= jumlahPencipta; i++) {
            const penciptaNamaInput = document.querySelector(`input[name="pencipta[${i-1}][nama]"]`);
            const alamatNamaInput = document.querySelector(`input[name="alamat_pencipta[${i}][nama]"]`);
            
            if (penciptaNamaInput && alamatNamaInput && penciptaNamaInput.value && !alamatNamaInput.value) {
                alamatNamaInput.value = penciptaNamaInput.value;
            }
        }
    }

    // Event listener untuk auto-sync data
    document.addEventListener('input', function(e) {
        if (e.target.name && e.target.name.includes('pencipta[') && e.target.name.includes('[nama]')) {
            setTimeout(autoSyncPenciptaData, 100);
        }
    });

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

    // Toggle upload/link contoh ciptaan
    const radioUpload = document.getElementById('contoh_ciptaan_upload');
    const radioLink = document.getElementById('contoh_ciptaan_link');
    const uploadField = document.getElementById('contoh-ciptaan-upload-field');
    const linkField = document.getElementById('contoh-ciptaan-link-field');
    radioUpload.addEventListener('change', function() {
        if (this.checked) {
            uploadField.style.display = '';
            linkField.style.display = 'none';
            uploadField.querySelector('input').setAttribute('required', true);
            linkField.querySelector('input').removeAttribute('required');
        }
    });
    radioLink.addEventListener('change', function() {
        if (this.checked) {
            uploadField.style.display = 'none';
            linkField.style.display = '';
            linkField.querySelector('input').setAttribute('required', true);
            uploadField.querySelector('input').removeAttribute('required');
        }
    });

    const jumlahPenciptaTemplate = document.getElementById('jumlah_pencipta_template');
    if (jumlahPenciptaTemplate) {
    const templateLinks = [
        null,
        document.getElementById('template-link-1'),
        document.getElementById('template-link-2'),
        document.getElementById('template-link-3'),
        document.getElementById('template-link-4'),
        document.getElementById('template-link-5')
    ];

    jumlahPenciptaTemplate.addEventListener('change', function() {
            templateLinks.forEach(btn => { if (btn) btn.classList.add('d-none'); });
        const val = parseInt(this.value);
        if (val && templateLinks[val]) templateLinks[val].classList.remove('d-none');
    });
    }

    document.getElementById('btn-save-draft')?.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Pastikan hanya parameter save_as_draft yang dikirim
        const form = document.getElementById('form-pengajuan');
        
        // Hapus semua input submit_final yang mungkin ada
        const existingSubmitFinal = form.querySelectorAll('input[name="submit_final"]');
        existingSubmitFinal.forEach(input => input.remove());
        
        // Hapus juga tombol submit_final agar tidak ikut terkirim
        const submitButton = form.querySelector('button[name="submit_final"]');
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.removeAttribute('name');
        }
        
        // Disable tombol dan tampilkan spinner
        this.disabled = true;
        document.getElementById('spinner-draft').classList.remove('d-none');
        
        // Pastikan field action=update ikut terkirim
        let actionField = form.querySelector('input[name="action"]');
        if (!actionField) {
            actionField = document.createElement('input');
            actionField.type = 'hidden';
            actionField.name = 'action';
            form.appendChild(actionField);
        }
        actionField.value = 'update';
        // Submit form
        form.submit();
    });

    const submitBtn = document.getElementById('btn-submit');
    if (submitBtn) {
        submitBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = document.getElementById('form-pengajuan');

            if (!form.checkValidity()) {
                form.reportValidity();
                Swal.fire({
                    title: 'Data Belum Lengkap',
                    text: 'Masih ada field wajib yang belum diisi.',
                    icon: 'warning'
                });
                return;
            }

            // Konfirmasi sebelum submit
            Swal.fire({
                title: 'Kirim Pengajuan?',
                text: 'Pastikan semua data sudah benar.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, kirim',
                cancelButtonText: 'Batal'
            }).then(result => {
                if (result.isConfirmed) {
                    submitBtn.disabled = true;
        document.getElementById('spinner-submit').classList.remove('d-none');
                    // Pastikan field action=submit ikut terkirim
                    let actionField = form.querySelector('input[name="action"]');
                    if (!actionField) {
                        actionField = document.createElement('input');
                        actionField.type = 'hidden';
                        actionField.name = 'action';
                        form.appendChild(actionField);
                    }
                    actionField.value = 'submit';
                    form.submit();
                }
            });
        });
    }

    /* =============================
       Realtime preview file upload
    ============================== */
    function attachRealtimePreview(fileInput) {
        if (!fileInput) return;
        fileInput.addEventListener('change', function() {
            if (!this.files || !this.files.length) return;

            const file = this.files[0];
            const newUrl = URL.createObjectURL(file);

            let previewLink = document.getElementById('preview-' + this.id);
            if (!previewLink) {
                previewLink = document.createElement('a');
                previewLink.id = 'preview-' + this.id;
                previewLink.className = 'btn btn-sm btn-info mt-2';
                previewLink.target = '_blank';
                previewLink.innerHTML = '<i class="fas fa-eye me-1"></i> Lihat File (baru)';
                this.parentNode.insertBefore(previewLink, this.nextSibling);
            }

            if (previewLink.dataset.currentUrl) {
                URL.revokeObjectURL(previewLink.dataset.currentUrl);
            }
            previewLink.href = newUrl;
            previewLink.dataset.currentUrl = newUrl;
        });
    }

    const uploadInputs = [
        'contoh_ciptaan',
        'surat_pengalihan_hak_cipta',
        'surat_pernyataan_hak_cipta',
        'ktp_seluruh_pencipta'
    ];
    uploadInputs.forEach(id => {
        const el = document.getElementById(id);
        if (el) attachRealtimePreview(el);
    });

});
</script>
@endpush
@endsection 