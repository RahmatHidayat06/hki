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
                            <button class="nav-link active" id="data-pengusul-tab" data-bs-toggle="pill" data-bs-target="#data-pengusul" type="button" role="tab">
                                <i class="fas fa-user me-2"></i>Data Pengusul
                            </button>
                            <button class="nav-link" id="data-ciptaan-tab" data-bs-toggle="pill" data-bs-target="#data-ciptaan" type="button" role="tab">
                                <i class="fas fa-book me-2"></i>Data Ciptaan
                            </button>
                            <button class="nav-link" id="data-pencipta-tab" data-bs-toggle="pill" data-bs-target="#data-pencipta" type="button" role="tab">
                                <i class="fas fa-users me-2"></i>Data Pencipta
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
                                                    <input class="form-check-input" type="radio" name="role" id="role-dosen" value="dosen" {{ (old('role') ?? ($draft->role ?? '')) == 'dosen' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="role-dosen">{{ __('Dosen') }}</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="role" id="role-mahasiswa" value="mahasiswa" {{ (old('role') ?? ($draft->role ?? '')) == 'mahasiswa' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="role-mahasiswa">{{ __('Mahasiswa') }}</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="nama_pengusul" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('nama_pengusul') is-invalid @enderror" 
                                                       id="nama_pengusul" name="nama_pengusul" value="{{ old('nama_pengusul') }}" required>
                                                <div class="invalid-feedback">Field ini wajib diisi untuk melanjutkan atau mengirim.</div>
                                                @error('nama_pengusul')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3" id="nip-nidn-field">
                                                <label for="nip_nidn" class="form-label">NIP/NIDN <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('nip_nidn') is-invalid @enderror" 
                                                       id="nip_nidn" name="nip_nidn" value="{{ old('nip_nidn') }}" required pattern="^[0-9]{8,20}$" maxlength="20">
                                                <div class="invalid-feedback">NIP/NIDN wajib diisi, hanya angka 8-20 digit.</div>
                                                @error('nip_nidn')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="no_hp" class="form-label">Nomor HP <span class="text-danger">*</span></label>
                                                <input type="tel" class="form-control @error('no_hp') is-invalid @enderror" id="no_hp" name="no_hp" value="{{ old('no_hp') }}" required pattern="^08[0-9]{8,11}$" maxlength="15">
                                                <div class="invalid-feedback">Nomor HP wajib diisi dan harus dimulai 08, 10-13 digit angka.</div>
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
                                                <div class="invalid-feedback">Field ini wajib diisi untuk melanjutkan atau mengirim.</div>
                                                @error('tahun_usulan')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3" id="id-sinta-field">
                                                <label for="id_sinta" class="form-label">{{ __('ID Sinta') }} <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('id_sinta') is-invalid @enderror" 
                                                       id="id_sinta" name="id_sinta" value="{{ old('id_sinta') }}">
                                                <div class="invalid-feedback">Field ini wajib diisi untuk melanjutkan atau mengirim.</div>
                                                @error('id_sinta')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
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

                                        <div class="d-flex justify-content-between mt-3">
                                            <button type="button" class="btn btn-secondary prev-section" data-prev="data-pengusul">
                                                <i class="fas fa-arrow-left me-2"></i> Sebelumnya
                                            </button>
                                            <button type="button" class="btn btn-primary next-section" data-next="data-pencipta">
                                                Selanjutnya <i class="fas fa-arrow-right ms-2"></i>
                                            </button>
                                            <button type="submit" name="save_as_draft" value="1" class="btn btn-secondary ms-2" formnovalidate>Simpan sebagai Draft</button>
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
                                            <button type="button" class="btn btn-primary next-section" data-next="dokumen">
                                                Selanjutnya <i class="fas fa-arrow-right ms-2"></i>
                                            </button>
                                            <button type="submit" name="save_as_draft" value="1" class="btn btn-secondary ms-2" formnovalidate>Simpan sebagai Draft</button>
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
                                            <label class="form-label">Surat Pengalihan Hak Cipta (Format Pdf) <span class="text-danger">*</span></label>
                                            <label for="jumlah_pencipta_template" class="form-label">Pilih Jumlah Pemegang Hak Cipta (Pencipta)</label>
                                            <select id="jumlah_pencipta_template" class="form-select mb-2">
                                                <option value="">Pilih Jumlah Pencipta</option>
                                                <option value="1">1 Pencipta</option>
                                                <option value="2">2 Pencipta</option>
                                                <option value="3">3 Pencipta</option>
                                                <option value="4">4 Pencipta</option>
                                                <option value="5">5 Pencipta</option>
                                            </select>
                                            <div id="template-download-buttons">
                                                <a href="https://docs.google.com/document/d/1JHJuY8oK3UOMQQ3Q5Ib6lP2y4cb_6TYPzZxbYhXAWyk/edit?tab=t.0" target="_blank" class="btn btn-sm btn-secondary d-none" id="template-link-1"><i class="fas fa-download me-2"></i> Download Template (1 Pencipta)</a>
                                                <a href="https://docs.google.com/document/d/1G9cnmujI2JVc9h6Dirt0JPIWjxmmksF-Dt4-OJtOi64/edit?pli=1&tab=t.0" target="_blank" class="btn btn-sm btn-secondary d-none" id="template-link-2"><i class="fas fa-download me-2"></i> Download Template (2 Pencipta)</a>
                                                <a href="https://docs.google.com/document/d/1uXTmaxGsJJ1Aj23eFVysJM9-EwwH-U7hQDmTq49YU34/edit?tab=t.0" target="_blank" class="btn btn-sm btn-secondary d-none" id="template-link-3"><i class="fas fa-download me-2"></i> Download Template (3 Pencipta)</a>
                                                <a href="https://docs.google.com/document/d/1XPza3uNeThyAovGovW0PfO9acg_Or_FMHOGwFAvTotk/edit?tab=t.0" target="_blank" class="btn btn-sm btn-secondary d-none" id="template-link-4"><i class="fas fa-download me-2"></i> Download Template (4 Pencipta)</a>
                                                <a href="https://docs.google.com/document/d/1kTomZpze6Oz4nxeyZ3wmp0hxfIRj4451wVRI5OSCqcM/edit?tab=t.0" target="_blank" class="btn btn-sm btn-secondary d-none" id="template-link-5"><i class="fas fa-download me-2"></i> Download Template (5 Pencipta)</a>
                                            </div>
                                            <input type="file" class="form-control mt-2 @error('surat_pengalihan_hak_cipta') is-invalid @enderror" id="surat_pengalihan_hak_cipta" name="surat_pengalihan_hak_cipta" accept="application/pdf" required>
                                            <div class="form-text">Upload 1 supported file: PDF. Max 10 MB.</div>
                                            <div class="invalid-feedback">Field ini wajib diisi untuk melanjutkan atau mengirim.</div>
                                            @error('surat_pengalihan_hak_cipta')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="surat_pernyataan_hak_cipta" class="form-label">Surat Pernyataan Hak Cipta (Format Pdf) <span class="text-danger">*</span></label>
                                             <p class="form-text"><a href="https://bit.ly/TemplatePernyataanHakCipta" target="_blank" class="btn btn-sm btn-secondary"><i class="fas fa-download me-2"></i> Download Template</a></p>
                                            <input type="file" class="form-control @error('surat_pernyataan_hak_cipta') is-invalid @enderror" 
                                                   id="surat_pernyataan_hak_cipta" name="surat_pernyataan_hak_cipta" required>
                                            <div class="form-text">Upload 1 supported file: PDF. Max 10 MB.</div>
                                            @error('surat_pernyataan_hak_cipta')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="ktp_seluruh_pencipta" class="form-label">KTP (SELURUH PENCIPTA) <span class="text-danger">*</span></label>
                                             <p class="form-text"><a href="https://bit.ly/TemplateKTP_HakCipta" target="_blank" class="btn btn-sm btn-secondary"><i class="fas fa-download me-2"></i> Download Template KTP</a> (Lampirkan KTP seluruh pencipta format Pdf)</p>
                                            <input type="file" class="form-control @error('ktp_seluruh_pencipta') is-invalid @enderror" 
                                                   id="ktp_seluruh_pencipta" name="ktp_seluruh_pencipta" required>
                                            <div class="form-text">Upload 1 supported file: PDF. Max 10 MB.</div>
                                            <div class="invalid-feedback">Field ini wajib diisi untuk melanjutkan atau mengirim.</div>
                                            @error('ktp_seluruh_pencipta')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="d-flex justify-content-between mt-3">
                                            <button type="button" class="btn btn-secondary prev-section" data-prev="data-pencipta">
                                                <i class="fas fa-arrow-left me-2"></i> Sebelumnya
                                            </button>
                                            <div>
                                                <button type="submit" name="save_as_draft" value="1" class="btn btn-secondary" id="btn-save-draft" formnovalidate>
                                                    <span class="spinner-border spinner-border-sm d-none" id="spinner-draft" role="status" aria-hidden="true"></span>
                                                    Simpan sebagai Draft
                                                </button>
                                                <button type="submit" name="submit_final" value="1" class="btn btn-primary ms-2" id="btn-submit">
                                                    <span class="spinner-border spinner-border-sm d-none" id="spinner-submit" role="status" aria-hidden="true"></span>
                                                    Kirim
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
        if (idx !== 0) btn.setAttribute('disabled', 'disabled');
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
        
        return isCompleted;
    }

    // Function to unlock completed sections
    function unlockCompletedSections() {
        const sections = ['data-pengusul', 'data-ciptaan', 'data-pencipta', 'dokumen'];
        
        sections.forEach((sectionId, index) => {
            if (index === 0) return; // First section always unlocked
            
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
                    if (label && !missingFields.includes(label)) missingFields.push(label.trim());
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            if (!isSectionValid) {
                event.preventDefault();
                alert('Lengkapi data berikut sebelum melanjutkan:\n- ' + missingFields.join('\n- '));
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
                                <label class=\"form-label\">No. HP <span class=\"text-danger\">*</span></label>
                                <input type=\"tel\" class=\"form-control\" name=\"pencipta[${i}][no_hp]\" required pattern=\"^08[0-9]{8,11}$\" maxlength=\"15\">
                            </div>
                            <div class=\"col-md-6 mb-3\">
                                <label class=\"form-label\">Alamat <span class=\"text-danger\">*</span></label>
                                <textarea class=\"form-control\" name=\"pencipta[${i}][alamat]\" rows=\"2\" required></textarea>
                            </div>
                        </div>
                        <div class=\"row\">
                            <div class=\"col-md-6 mb-3\">
                                <label class=\"form-label\">Kecamatan <span class=\"text-danger\">*</span></label>
                                <input type=\"text\" class=\"form-control\" name=\"pencipta[${i}][kecamatan]\" required>
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

    // Fungsi untuk menampilkan/menyembunyikan field berdasarkan role
    const roleRadios = document.querySelectorAll('input[name="role"]');
    const nipNidnField = document.getElementById('nip-nidn-field');
    const idSintaField = document.getElementById('id-sinta-field');
    function toggleRoleSpecificFields() {
        const selectedRole = document.querySelector('input[name="role"]:checked')?.value || '';
        const nipInput   = nipNidnField.querySelector('input');
        const sintaInput = idSintaField.querySelector('input');

        if (selectedRole === 'mahasiswa') {
            // Sembunyikan & non-aktifkan
            nipNidnField.style.display = 'none';
            idSintaField.style.display = 'none';

            nipInput.removeAttribute('required');
            sintaInput.removeAttribute('required');
            nipInput.setAttribute('disabled', true);
            sintaInput.setAttribute('disabled', true);
        } else {
            // Tampilkan & aktifkan (wajib)
            nipNidnField.style.display = '';
            idSintaField.style.display = '';

            nipInput.removeAttribute('disabled');
            sintaInput.removeAttribute('disabled');
            nipInput.setAttribute('required', true);
            sintaInput.setAttribute('required', true);
        }
    }
    // Panggil fungsi toggle saat pertama kali dimuat untuk mengatur tampilan awal
    toggleRoleSpecificFields();
    // Tambahkan event listener pada perubahan pilihan role
    roleRadios.forEach(radio => {
        radio.addEventListener('change', toggleRoleSpecificFields);
    });

    // Populate tahun usulan dropdown - 5 tahun ke belakang dari sekarang
    function populateYearDropdown() {
        const yearSelect = document.getElementById('tahun_usulan');
        if (!yearSelect) return;
        
        const currentYear = new Date().getFullYear();
        const savedValue = "{{ old('tahun_usulan') }}";
        
        // Clear existing options
        yearSelect.innerHTML = '<option value="">Pilih Tahun</option>';
        
        // Generate 5 tahun: tahun sekarang sampai 4 tahun ke belakang
        let yearList = [];
        for (let i = 0; i < 5; i++) {
            yearList.push(currentYear - i);
        }
        
        // Jika ada nilai tersimpan yang tidak ada di list, tambahkan
        if (savedValue && !yearList.includes(parseInt(savedValue))) {
            yearList.push(parseInt(savedValue));
        }
        
        // Urutkan descending dan buat unique
        yearList = [...new Set(yearList)].sort((a, b) => b - a);
        
        // Add year options
        yearList.forEach(function(year) {
            const option = document.createElement('option');
            option.value = year;
            option.textContent = year;
            
            if (year.toString() === savedValue) {
                option.selected = true;
            }
            
            yearSelect.appendChild(option);
        });
    }
    
    // Initialize year dropdown
    populateYearDropdown();

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
    const templateLinks = [
        null,
        document.getElementById('template-link-1'),
        document.getElementById('template-link-2'),
        document.getElementById('template-link-3'),
        document.getElementById('template-link-4'),
        document.getElementById('template-link-5')
    ];
    jumlahPenciptaTemplate.addEventListener('change', function() {
        templateLinks.forEach((btn, idx) => { if (btn) btn.classList.add('d-none'); });
        const val = parseInt(this.value);
        if (val && templateLinks[val]) templateLinks[val].classList.remove('d-none');
    });

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