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
                                <label for="no_telp" class="form-label">Nomor Telp</label>
                                <input type="tel" class="form-control" id="no_telp" name="no_telp" value="{{ $pengajuan->no_telp }}" required pattern="^08[0-9]{8,11}$" maxlength="15">
                                <div class="invalid-feedback">Nomor Telp wajib diisi dan harus dimulai 08, 10-13 digit angka.</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3" id="id-sinta-field">
                                <label for="id_sinta" class="form-label">ID Sinta <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="id_sinta" name="id_sinta" value="{{ $pengajuan->id_sinta }}">
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
                            $jumlahPencipta = $pengajuan->jumlah_pencipta ? (int) $pengajuan->jumlah_pencipta : 1;
                            $pencipta = $pengajuan->pengaju->toArray();
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
                                                <label for="no_telp" class="form-label">No. Telp</label>
                                                <input type="tel" class="form-control" id="no_telp" name="pencipta[{{ $i }}][no_telp]" value="{{ old('pencipta.'.$i.'.no_telp', $p->no_telp ?? '') }}" required maxlength="15">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Alamat <span class="text-danger">*</span></label>
                                                <textarea class="form-control" name="pencipta[{{ $i }}][alamat]" rows="2" required>{{ old('pencipta.'.$i.'.alamat', $p->alamat ?? '') }}</textarea>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Kewarganegaraan <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="pencipta[{{ $i }}][kewarganegaraan]" value="{{ old('pencipta.'.$i.'.kewarganegaraan', $p->kewarganegaraan ?? '') }}" required>
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
                            <label class="form-label">CONTOH CIPTAAN <span class="text-danger">*</span></label>
                            <div class="d-flex align-items-center mb-2">
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="radio" name="contoh_ciptaan_type" id="contoh_ciptaan_upload" value="upload" 
                                           {{ (old('contoh_ciptaan_type', 'upload') === 'upload') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="contoh_ciptaan_upload">Upload</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="contoh_ciptaan_type" id="contoh_ciptaan_link" value="link"
                                           {{ (old('contoh_ciptaan_type') === 'link') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="contoh_ciptaan_link">Link</label>
                                </div>
                            </div>
                            <div id="contoh-ciptaan-upload-field">
                            @if($pengajuan->file_karya && !str_starts_with($pengajuan->file_karya, 'http'))
                                <div class="mb-2 file-lama-exists d-flex align-items-center gap-2">
                                    <a href="{{ Storage::url(ltrim(preg_replace('#^storage/#', '', $pengajuan->file_karya), '/')) }}" target="_blank" class="btn btn-sm btn-success"><i class="fas fa-eye"></i> Lihat File</a>
                                    <button type="button" class="btn btn-sm btn-warning" onclick="toggleFileInput('contoh_ciptaan')"><i class="fas fa-sync-alt"></i> Ganti File</button>
                                </div>
                                <input type="file" class="form-control d-none" id="contoh_ciptaan" name="contoh_ciptaan">
                            @else
                                <input type="file" class="form-control" id="contoh_ciptaan" name="contoh_ciptaan">
                            @endif
                                <div class="form-text">Upload 1 supported file: PDF, audio, drawing, image, or video. Max 10 MB.</div>
                            </div>
                            <div id="contoh-ciptaan-link-field" style="display:none;">
                                <input type="url" class="form-control" name="contoh_ciptaan_link" placeholder="https://">
                                <div class="invalid-feedback">Field ini wajib diisi untuk melanjutkan atau mengirim.</div>
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
                            @php $dokumen = $pengajuan->file_dokumen_pendukung; if (is_string($dokumen)) $dokumen = json_decode($dokumen, true); @endphp
                            @if(isset($dokumen['surat_pengalihan']) && $dokumen['surat_pengalihan'])
                                <div class="mb-2 file-lama-exists d-flex align-items-center gap-2">
                                    <a href="{{ Storage::url(ltrim(preg_replace('#^storage/#', '', $dokumen['surat_pengalihan']), '/')) }}" target="_blank" class="btn btn-sm btn-success"><i class="fas fa-eye"></i> Lihat File</a>
                                    <button type="button" class="btn btn-sm btn-warning" onclick="toggleFileInput('surat_pengalihan_hak_cipta')"><i class="fas fa-sync-alt"></i> Ganti File</button>
                                </div>
                                <input type="file" class="form-control d-none" id="surat_pengalihan_hak_cipta_hidden" accept="application/pdf">
                            @else
                                <input type="file" class="form-control" id="surat_pengalihan_hak_cipta" name="surat_pengalihan_hak_cipta" accept="application/pdf">
                            @endif
                            <div class="form-text">Upload 1 supported file: PDF. Max 10 MB.</div>
                        </div>
                        <div class="mb-3">
                            <label for="surat_pernyataan_hak_cipta" class="form-label">Surat Pernyataan Hak Cipta (Format Pdf) <span class="text-danger">*</span></label>
                            <p class="form-text"><a href="https://bit.ly/TemplatePernyataanHakCipta" target="_blank" class="btn btn-sm btn-secondary"><i class="fas fa-download me-2"></i> Download Template</a></p>
                            @if(isset($dokumen['surat_pernyataan']) && $dokumen['surat_pernyataan'])
                                <div class="mb-2 file-lama-exists d-flex align-items-center gap-2">
                                    <a href="{{ Storage::url(ltrim(preg_replace('#^storage/#', '', $dokumen['surat_pernyataan']), '/')) }}" target="_blank" class="btn btn-sm btn-success"><i class="fas fa-eye"></i> Lihat File</a>
                                    <button type="button" class="btn btn-sm btn-warning" onclick="toggleFileInput('surat_pernyataan_hak_cipta')"><i class="fas fa-sync-alt"></i> Ganti File</button>
                                </div>
                                <input type="file" class="form-control d-none" id="surat_pernyataan_hak_cipta_hidden">
                            @else
                                <input type="file" class="form-control" id="surat_pernyataan_hak_cipta" name="surat_pernyataan_hak_cipta">
                            @endif
                            <div class="form-text">Upload 1 supported file: PDF. Max 10 MB.</div>
                        </div>
                        <div class="mb-3">
                            <label for="ktp_seluruh_pencipta" class="form-label">KTP (SELURUH PENCIPTA) <span class="text-danger">*</span></label>
                            <p class="form-text"><a href="https://bit.ly/TemplateKTP_HakCipta" target="_blank" class="btn btn-sm btn-secondary"><i class="fas fa-download me-2"></i> Download Template KTP</a> (Lampirkan KTP seluruh pencipta format Pdf)</p>
                            @if(isset($dokumen['ktp']) && $dokumen['ktp'])
                                <div class="mb-2 file-lama-exists d-flex align-items-center gap-2">
                                    <a href="{{ Storage::url(ltrim(preg_replace('#^storage/#', '', $dokumen['ktp']), '/')) }}" target="_blank" class="btn btn-sm btn-success"><i class="fas fa-eye"></i> Lihat File</a>
                                    <button type="button" class="btn btn-sm btn-warning" onclick="toggleFileInput('ktp_seluruh_pencipta')"><i class="fas fa-sync-alt"></i> Ganti File</button>
                                </div>
                                <input type="file" class="form-control d-none" id="ktp_seluruh_pencipta_hidden">
                            @else
                                <input type="file" class="form-control" id="ktp_seluruh_pencipta" name="ktp_seluruh_pencipta">
                            @endif
                            <div class="form-text">Upload 1 supported file: PDF. Max 10 MB.</div>
                        </div>
                        <div class="d-flex justify-content-between mt-3">
                            <button type="button" class="btn btn-secondary prev-section" data-prev="data-pencipta">
                                <i class="fas fa-arrow-left me-2"></i> Sebelumnya
                            </button>
                            <div>
                                <button type="submit" name="save_as_draft" value="1" class="btn btn-secondary" formnovalidate>Simpan sebagai Draft</button>
                                <button type="submit" name="ajukan" value="1" class="btn btn-primary ms-2" id="btn-ajukan">Kirim</button>
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
// Menunggu hingga semua DOM dan stylesheets dimuat
document.addEventListener('DOMContentLoaded', function() {
    // Tambahkan delay kecil untuk memastikan semua CSS sudah dimuat
    setTimeout(function() {
        initializeForm();
    }, 100);
});

function initializeForm() {
    const form = document.getElementById('form-edit-draft');
    const btnAjukan = document.getElementById('btn-ajukan');
    
    if (!form) {
        console.error('Form tidak ditemukan');
        return;
    }

    // Navigasi antar section/tab - sama seperti di create
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

    // Function to unlock completed sections on page load
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

    // Call unlock function on page load
    unlockCompletedSections();

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

    // Re-check unlocked sections when any field changes
    document.addEventListener('input', function() {
        setTimeout(unlockCompletedSections, 100);
    });
    
    document.addEventListener('change', function() {
        setTimeout(unlockCompletedSections, 100);
    });

    // Data sub jenis ciptaan - sama seperti di create
    const identitasCiptaanSelect = document.getElementById('identitas_ciptaan');
    const subJenisCiptaanSelect = document.getElementById('sub_jenis_ciptaan');
    
    const opsiSubJenis = {
        'karya tulis': ['Buku', 'E-Book', 'Diktat', 'Modul', 'Buku Panduan/Petunjuk', 'Karya Ilmiah', 'Karya Tulis/Artikel', 'Laporan Penelitian', 'Jurnal'],
        'karya audio visual': ['Kuliah', 'Karya Rekaman Video', 'Karya Siaran Video'],
        'karya lainnya': ['Program Komputer', 'Permainan Video', 'Basis Data']
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

    // Toggle field NIP/NIDN dan ID Sinta sesuai role
    const roleRadios = document.querySelectorAll('input[name="role"]');
    const nipNidnField = document.getElementById('nip-nidn-field');
    const idSintaField = document.getElementById('id-sinta-field');
    
    function toggleRoleFields() {
        const selectedRole = document.querySelector('input[name="role"]:checked')?.value;
        const nipInput = document.getElementById('nip_nidn');
        const sintaInput = document.getElementById('id_sinta');

        if (selectedRole === 'dosen') {
            nipNidnField?.classList.remove('d-none');
            idSintaField?.classList.remove('d-none');

            // Aktifkan input & jadikan required
            if (nipInput) {
                nipInput.removeAttribute('disabled');
                nipInput.setAttribute('required', 'required');
            }
            if (sintaInput) {
                sintaInput.removeAttribute('disabled');
                sintaInput.setAttribute('required', 'required');
            }
        } else {
            nipNidnField?.classList.add('d-none');
            idSintaField?.classList.add('d-none');

            // Nonaktifkan input & hapus required
            if (nipInput) {
                nipInput.removeAttribute('required');
                nipInput.setAttribute('disabled', 'disabled');
                nipInput.value = '';
            }
            if (sintaInput) {
                sintaInput.removeAttribute('required');
                sintaInput.setAttribute('disabled', 'disabled');
                sintaInput.value = '';
            }
        }
    }
    roleRadios.forEach(radio => {
        radio.addEventListener('change', toggleRoleFields);
    });
    toggleRoleFields();

    // Toggle contoh ciptaan upload/link
    const contohCiptaanRadios = document.querySelectorAll('input[name="contoh_ciptaan_type"]');
    const uploadField = document.getElementById('contoh-ciptaan-upload-field');
    const linkField = document.getElementById('contoh-ciptaan-link-field');

    function toggleContohCiptaan() {
        const selectedType = document.querySelector('input[name="contoh_ciptaan_type"]:checked')?.value;
        if (selectedType === 'upload') {
            uploadField.style.display = 'block';
            linkField.style.display = 'none';
        } else if (selectedType === 'link') {
            uploadField.style.display = 'none';
            linkField.style.display = 'block';
        }
    }
    contohCiptaanRadios.forEach(radio => {
        radio.addEventListener('change', toggleContohCiptaan);
    });
    toggleContohCiptaan();

    // Fungsi untuk menangani form pencipta dinamis
    const jumlahPenciptaSelect = document.getElementById('jumlah_pencipta');
    const penciptaContainer = document.getElementById('pencipta-container');
    
    // Fungsi render form pencipta dinamis
    function renderPenciptaForm(jumlah) {
        const existingData = getPenciptaData();
        penciptaContainer.innerHTML = '';
        
        for (let i = 0; i < jumlah; i++) {
            const currentData = existingData[i] || {};
            const cardHtml = `
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">Data Pencipta ${i+1}</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="pencipta[${i}][nama]" id="pencipta-nama-${i}" value="${currentData.nama || ''}" required>
                                <div class="invalid-feedback">Nama wajib diisi</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="pencipta[${i}][email]" id="pencipta-email-${i}" value="${currentData.email || ''}" required>
                                <div class="invalid-feedback">Email wajib diisi</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="no_telp" class="form-label">No. Telp</label>
                                <input type="tel" class="form-control" id="no_telp" name="pencipta[${i}][no_telp]" value="${currentData.no_telp || ''}" required maxlength="15">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Alamat <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="pencipta[${i}][alamat]" rows="2" required>${currentData.alamat || ''}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kewarganegaraan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="pencipta[${i}][kewarganegaraan]" value="${currentData.kewarganegaraan || ''}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kode Pos <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="pencipta[${i}][kodepos]" value="${currentData.kodepos || ''}" pattern="^[0-9]{5}$" maxlength="5">
                            </div>
                        </div>
                    </div>
                </div>
            `;
            penciptaContainer.insertAdjacentHTML('beforeend', cardHtml);
        }
    }

    function getPenciptaData() {
        const data = [];
        penciptaContainer.querySelectorAll('.card').forEach(function(card, i) {
            data[i] = {
                nama: card.querySelector('input[name$="[nama]"]')?.value || '',
                email: card.querySelector('input[name$="[email]"]')?.value || '',
                no_telp: card.querySelector('input[name$="[no_telp]"]')?.value || '',
                alamat: card.querySelector('textarea[name$="[alamat]"]')?.value || '',
                kewarganegaraan: card.querySelector('input[name$="[kewarganegaraan]"]')?.value || '',
                kodepos: card.querySelector('input[name$="[kodepos]"]')?.value || ''
            };
        });
        return data;
    }

    if (jumlahPenciptaSelect && penciptaContainer) {
        // Render initial form based on existing data
        const initialJumlah = parseInt(document.getElementById('jumlah_pencipta_hidden')?.value) || 1;
        if (penciptaContainer.children.length === 0) {
            renderPenciptaForm(initialJumlah);
        }
        
        jumlahPenciptaSelect.addEventListener('change', function() {
            const jumlah = parseInt(this.value) || 0;
            renderPenciptaForm(jumlah);
        });
    }

    // Handle tombol Kirim dengan validasi yang benar
    if (btnAjukan) {
        btnAjukan.addEventListener('click', function(e) {
            e.preventDefault();
            
            console.log('Tombol Kirim diklik');
            
            // Validasi semua field wajib
            let isValid = true;
            let errorMessages = [];
            
            // Validasi field utama
            const requiredFields = [
                { field: 'judul', name: 'Judul Karya' },
                { field: 'deskripsi', name: 'Deskripsi' },
                { field: 'nama_pengusul', name: 'Nama Pengusul' },
                { field: 'no_telp', name: 'Nomor Telp' },
                { field: 'jumlah_pencipta', name: 'Jumlah Pencipta' },
                { field: 'identitas_ciptaan', name: 'Jenis Ciptaan' },
                { field: 'sub_jenis_ciptaan', name: 'Sub Jenis Ciptaan' },
                { field: 'tanggal_pertama_kali_diumumkan', name: 'Tanggal Pertama Kali Diumumkan' }
            ];
            
            requiredFields.forEach(({field, name}) => {
                const element = document.getElementById(field) || document.querySelector(`[name="${field}"]`);
                if (element && !element.value.trim()) {
                    isValid = false;
                    errorMessages.push(name);
                    element.classList.add('is-invalid');
                    console.log(`Field kosong: ${name}`);
                } else if (element) {
                    element.classList.remove('is-invalid');
                }
            });
            
            // Validasi role
            const selectedRole = document.querySelector('input[name="role"]:checked');
            if (!selectedRole) {
                isValid = false;
                errorMessages.push('Role (Dosen/Mahasiswa)');
                console.log('Role tidak dipilih');
            } else {
                console.log('Role dipilih:', selectedRole.value);
                
                // Validasi khusus untuk dosen
                if (selectedRole.value === 'dosen') {
                    const nipNidn = document.getElementById('nip_nidn');
                    const idSinta = document.getElementById('id_sinta');
                    if (nipNidn && !nipNidn.value.trim()) {
                        isValid = false;
                        errorMessages.push('NIP/NIDN (wajib untuk dosen)');
                        nipNidn.classList.add('is-invalid');
                        console.log('NIP/NIDN kosong untuk dosen');
                    } else if (nipNidn) {
                        nipNidn.classList.remove('is-invalid');
                    }
                    
                    if (idSinta && !idSinta.value.trim()) {
                        isValid = false;
                        errorMessages.push('ID SINTA (wajib untuk dosen)');
                        idSinta.classList.add('is-invalid');
                        console.log('ID SINTA kosong untuk dosen');
                    } else if (idSinta) {
                        idSinta.classList.remove('is-invalid');
                    }
                }
            }
            
            // Validasi contoh ciptaan
            const contohCiptaanType = document.querySelector('input[name="contoh_ciptaan_type"]:checked');
            if (!contohCiptaanType) {
                isValid = false;
                errorMessages.push('Tipe Contoh Ciptaan');
                console.log('Tipe contoh ciptaan tidak dipilih');
            } else {
                console.log('Tipe contoh ciptaan:', contohCiptaanType.value);
                
                if (contohCiptaanType.value === 'upload') {
                    const fileInput = document.getElementById('contoh_ciptaan');
                    const fileExists = document.querySelector('.file-lama-exists a[href*="storage/"]');
                    
                    if (fileInput && !fileInput.files.length && !fileExists) {
                        isValid = false;
                        errorMessages.push('File Contoh Ciptaan');
                        console.log('File contoh ciptaan tidak ada');
                    } else {
                        console.log('File contoh ciptaan OK:', fileInput?.files.length > 0 ? 'New file' : 'Existing file');
                    }
                } else if (contohCiptaanType.value === 'link') {
                    const linkInput = document.querySelector('input[name="contoh_ciptaan_link"]');
                    if (linkInput && !linkInput.value.trim()) {
                        isValid = false;
                        errorMessages.push('Link Contoh Ciptaan');
                        console.log('Link contoh ciptaan kosong');
                    } else if (linkInput) {
                        console.log('Link contoh ciptaan OK:', linkInput.value);
                    }
                }
            }
            
            // cek dokumen pendukung
            function checkDocument(baseName, displayName) {
                const hiddenInput = document.getElementById(baseName + '_hidden'); // hanya ada bila file lama tersedia
                const visibleInput = document.getElementById(baseName); // ada bila belum ada file lama / atau setelah "Ganti" ditekan

                const hasExistingFile = !!hiddenInput; // ada file lama
                const hasNewFile = (hiddenInput && hiddenInput.files && hiddenInput.files.length > 0) ||
                                   (visibleInput && visibleInput.files && visibleInput.files.length > 0);

                if (!hasExistingFile && !hasNewFile) {
                    isValid = false;
                    errorMessages.push(displayName);
                    console.log(`${displayName} tidak ada (existing: ${hasExistingFile}, new: ${hasNewFile})`);
            } else {
                    console.log(`${displayName} OK:`, hasNewFile ? 'New file' : 'Existing file');
                }
            }
            
            checkDocument('surat_pengalihan_hak_cipta', 'Surat Pengalihan Hak Cipta');
            checkDocument('surat_pernyataan_hak_cipta', 'Surat Pernyataan Hak Cipta');
            checkDocument('ktp_seluruh_pencipta', 'KTP Seluruh Pencipta');
            
            // Validasi data pencipta
            const penciptaCards = document.querySelectorAll('#pencipta-container .card');
            console.log('Jumlah pencipta cards:', penciptaCards.length);
            
            penciptaCards.forEach(function(card, idx) {
                const nama = card.querySelector('input[name*="[nama]"]');
                const email = card.querySelector('input[name*="[email]"]');
                
                if (!nama || !nama.value.trim()) {
                    isValid = false;
                    errorMessages.push(`Nama Pencipta ${idx + 1}`);
                    if (nama) nama.classList.add('is-invalid');
                    console.log(`Nama pencipta ${idx + 1} kosong`);
                } else if (nama) {
                    nama.classList.remove('is-invalid');
                }
                
                if (!email || !email.value.trim()) {
                    isValid = false;
                    errorMessages.push(`Email Pencipta ${idx + 1}`);
                    if (email) email.classList.add('is-invalid');
                    console.log(`Email pencipta ${idx + 1} kosong`);
                } else if (email) {
                    email.classList.remove('is-invalid');
                }
            });
            
            console.log('Validasi selesai. Valid:', isValid, 'Errors:', errorMessages);
            
            if (!isValid) {
                alert('Data berikut masih belum lengkap:\n- ' + errorMessages.join('\n- ') + '\n\nSilakan lengkapi semua data wajib sebelum mengirim.');
                // Scroll ke field pertama yang error
                const firstInvalidField = document.querySelector('.is-invalid');
                if (firstInvalidField) {
                    firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    setTimeout(() => firstInvalidField.focus(), 500);
                }
                return false;
            }
            
            // Konfirmasi sebelum submit
            if (!confirm('Apakah Anda yakin ingin mengirim pengajuan ini? Setelah dikirim, data tidak dapat diubah lagi.')) {
                console.log('User membatalkan pengiriman');
            return false;
            }
            
            console.log('User mengkonfirmasi pengiriman');
            
            // Tambahkan loading state
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...';
            
            // Hapus input ajukan yang mungkin sudah ada
            const existingInputs = form.querySelectorAll('input[name="ajukan"]');
            existingInputs.forEach(input => {
                console.log('Menghapus input ajukan yang sudah ada');
                input.remove();
            });
            
            // Tambahkan input ajukan
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'ajukan';
            hiddenInput.value = '1';
            form.appendChild(hiddenInput);
            
            console.log('Input ajukan ditambahkan, form akan disubmit');
            console.log('Form action:', form.action);
            console.log('Form method:', form.method);
            
            // Submit form secara manual
            try {
                form.submit();
                console.log('Form submitted successfully');
            } catch (error) {
                console.error('Error submitting form:', error);
                this.disabled = false;
                this.innerHTML = 'Kirim';
                alert('Terjadi kesalahan saat mengirim form. Silakan coba lagi.');
            }
        });
    }

    // Handle tombol Simpan sebagai Draft
    document.querySelectorAll('button[name="save_as_draft"]').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            // Hapus input ajukan jika ada
            const existingAjukanInputs = form.querySelectorAll('input[name="ajukan"]');
            existingAjukanInputs.forEach(input => input.remove());
        });
    });

    // Simpan tab aktif ke localStorage
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

    // Toggle file input (accessible from inline onclick)
    window.toggleFileInput = function(baseName) {
        const hiddenInput = document.getElementById(baseName + '_hidden');
        if (!hiddenInput) return;

        // Reveal the hidden input
        hiddenInput.classList.remove('d-none');

        // Set proper name so it will be uploaded
        hiddenInput.setAttribute('name', baseName);

        // Optional: add required when user chooses to replace
        hiddenInput.setAttribute('required', true);

        // Trigger click to open file dialog
        hiddenInput.click();
    }

    // ==============================
    // Realtime preview untuk file baru
    // ==============================
    function attachRealtimePreview(fileInput) {
        if (!fileInput) return;

        fileInput.addEventListener('change', function () {
            if (!this.files || !this.files.length) return;

            // Buat/ambil object URL
            const file = this.files[0];
            const newUrl = URL.createObjectURL(file);

            // Cari (atau buat) link preview setelah input
            let previewLink = document.getElementById('preview-' + this.id);
            if (!previewLink) {
                previewLink = document.createElement('a');
                previewLink.id = 'preview-' + this.id;
                previewLink.className = 'btn btn-sm btn-info mt-2';
                previewLink.target = '_blank';
                previewLink.innerHTML = '<i class="fas fa-eye me-1"></i> Lihat File (baru)';
                // Sisipkan setelah input
                this.parentNode.insertBefore(previewLink, this.nextSibling);
            }

            // Jika sebelumnya ada URL, revoke
            if (previewLink.dataset.currentUrl) {
                URL.revokeObjectURL(previewLink.dataset.currentUrl);
            }

            previewLink.href = newUrl;
            previewLink.dataset.currentUrl = newUrl;
        });
    }

    // Inisialisasi preview untuk semua input file yang relevan
    const fileInputIds = [
        'surat_pengalihan_hak_cipta_hidden',
        'surat_pengalihan_hak_cipta',
        'surat_pernyataan_hak_cipta_hidden',
        'surat_pernyataan_hak_cipta',
        'ktp_seluruh_pencipta_hidden',
        'ktp_seluruh_pencipta',
        'contoh_ciptaan'
    ];

    fileInputIds.forEach(id => {
        const input = document.getElementById(id);
        if (input) attachRealtimePreview(input);
    });
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