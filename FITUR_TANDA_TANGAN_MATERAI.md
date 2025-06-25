# Fitur Tanda Tangan Digital dan Materai

## Overview
Sistem ini menyediakan fitur tanda tangan digital dan penempelan materai untuk dokumen pengajuan HKI. Fitur ini terintegrasi dalam dua workflow utama:

1. **Workflow Validasi** - Untuk admin/admin_p3m melakukan validasi pengajuan
2. **Workflow Persetujuan** - Untuk direktur melakukan persetujuan dengan tanda tangan digital

## Fitur Utama

### 1. Tanda Tangan Digital & Upload
- **Tanda Tangan Digital**: Canvas untuk membuat tanda tangan digital secara real-time
- **Upload Tanda Tangan**: Upload file gambar tanda tangan (PNG, JPG, JPEG)
- Dua opsi yang dapat dipilih: digital drawing atau upload file
- Validasi tanda tangan sebelum submit
- Penyimpanan tanda tangan dalam format PNG
- Integrasi dengan proses approval

### 2. Penempelan Materai
- Upload file materai dalam format gambar
- Preview materai sebelum submit
- Validasi file materai
- Penyimpanan materai di storage

### 3. Editor Drag & Drop untuk Dokumen PDF
- Viewer PDF menggunakan PDF.js
- Drag & drop positioning untuk tanda tangan dan materai
- Resize handles untuk mengatur ukuran overlay
- Kontrol posisi presisi (X, Y, Width, Height dalam persentase)
- Zoom in/out untuk PDF viewer
- Real-time preview overlay

### 4. Manajemen Overlay
- Daftar overlay yang sudah diterapkan
- Hapus overlay individual atau semua sekaligus
- Penyimpanan posisi overlay dalam database (JSON format)
- Load overlay yang sudah tersimpan

## Workflow Validasi (Admin/Admin P3M)

### Akses
- URL: `/pengajuan` → klik tombol "Validasi"
- Role: admin, admin_p3m, direktur

### Langkah-langkah:
1. **Akses Halaman Validasi**
   - Dari daftar pengajuan, klik tombol "Validasi"
   - Sistem akan menampilkan halaman validasi dengan panel tanda tangan & materai

2. **Kelola Tanda Tangan & Materai pada Dokumen**
   - Panel menampilkan dokumen yang tersedia (Surat Pengalihan, Surat Pernyataan)
   - Klik "Kelola TTD & Materai" untuk membuka editor drag & drop
   - Pilih tanda tangan atau materai dari panel kanan
   - Drag & drop ke posisi yang diinginkan pada PDF
   - Gunakan resize handles atau kontrol presisi untuk mengatur posisi
   - Klik "Simpan Overlay" untuk menyimpan posisi

3. **Validasi Final**
   - Kembali ke halaman validasi utama
   - Isi form validasi (status, catatan)
   - Submit untuk menyelesaikan validasi

### Routes Validasi:
```php
Route::get('validasi/{pengajuan}', [ValidasiController::class, 'show'])->name('validasi.show');
Route::get('validasi/{pengajuan}/signature/{documentType}', [ValidasiController::class, 'showSignatureEditor'])->name('validasi.signature.editor');
Route::post('validasi/{pengajuan}/signature/{documentType}/apply', [ValidasiController::class, 'applyOverlay'])->name('validasi.signature.apply');
Route::put('validasi/{pengajuan}', [ValidasiController::class, 'update'])->name('validasi.update');
```

## Workflow Persetujuan (Direktur)

### Akses
- URL: `/persetujuan` → klik tombol "Validasi"
- Role: direktur

### Langkah-langkah:
1. **Akses Halaman Persetujuan dengan Tanda Tangan**
   - Dari daftar persetujuan, klik tombol "Validasi"
   - Sistem akan menampilkan halaman persetujuan dengan fitur tanda tangan digital

2. **Kelola Tanda Tangan & Materai pada Dokumen**
   - Panel menampilkan dokumen yang tersedia (Surat Pengalihan, Surat Pernyataan)
   - Klik "Kelola TTD & Materai" untuk membuka editor drag & drop
   - Pilih tanda tangan direktur atau materai dari panel kanan
   - Drag & drop ke posisi yang diinginkan pada PDF
   - Gunakan resize handles atau kontrol presisi untuk mengatur posisi
   - Klik "Simpan Overlay" untuk menyimpan posisi

3. **Tanda Tangan Digital/Upload & Upload Materai**
   - **Pilih metode tanda tangan**: Digital (canvas) atau Upload file
   - **Tanda Tangan Digital**: Buat tanda tangan pada canvas yang disediakan
   - **Upload Tanda Tangan**: Upload file gambar tanda tangan (PNG, JPG, JPEG, max 2MB)
   - **Upload Materai**: Upload file materai (format gambar, max 2MB)
   - Tanda tangan dan materai wajib diisi

4. **Persetujuan Final**
   - Isi catatan validasi (opsional)
   - Klik "Validasi & Setujui Pengajuan" untuk menyelesaikan proses

### Routes Persetujuan:
```php
Route::get('persetujuan/{id}/validation-signature', [PersetujuanController::class, 'showValidationWithSignature'])->name('persetujuan.validation.signature');
Route::get('persetujuan/{pengajuan}/signature/{documentType}', [PersetujuanController::class, 'showSignatureEditor'])->name('persetujuan.signature.editor');
Route::post('persetujuan/{pengajuan}/signature/{documentType}/apply', [PersetujuanController::class, 'applyOverlay'])->name('persetujuan.signature.apply');
Route::put('persetujuan/{id}/approve', [PersetujuanController::class, 'approve'])->name('persetujuan.approve');
```

## Struktur Database

### Penyimpanan Overlay
Data overlay disimpan dalam field `file_dokumen_pendukung` sebagai JSON:

```json
{
  "surat_pengalihan": "path/to/file.pdf",
  "surat_pernyataan": "path/to/file.pdf",
  "overlays": {
    "surat_pengalihan": [
      {
        "type": "signature",
        "signature_id": "ttd_direktur",
        "stamp_id": null,
        "x": 10.5,
        "y": 20.3,
        "width": 15.0,
        "height": 8.0,
        "created_at": "2024-01-01T10:00:00.000Z",
        "created_by": 1
      }
    ],
    "surat_pernyataan": [...]
  }
}
```

### Penyimpanan Tanda Tangan Digital
- Field: `tanda_tangan_direktur`
- Format: Path ke file PNG yang disimpan di storage
- Lokasi: `storage/app/public/signatures/`

### Penyimpanan Materai
- Field: `matrai_direktur` 
- Format: Path ke file gambar yang disimpan di storage
- Lokasi: `storage/app/public/matrai/`

## Assets dan Dependencies

### Frontend Libraries
- **PDF.js**: Untuk rendering dan viewing PDF
- **Signature Pad**: Untuk canvas tanda tangan digital
- **Bootstrap 5**: Untuk UI components
- **Font Awesome**: Untuk icons

### Storage Folders
```
storage/app/public/
├── signatures/
│   ├── ttd_direktur.png
│   └── digital_signatures/
├── matrai/
│   ├── materai_default.png
│   └── uploaded_materai/
└── dokumen_pendukung/
    ├── surat_pengalihan/
    └── surat_pernyataan/
```

### Public Assets
```
public/
├── images/
│   └── materai-default.png
└── js/
    └── signature-pad.min.js
```

## Keamanan dan Validasi

### Validasi File
- **Tanda Tangan Digital**: Otomatis disimpan sebagai PNG dari canvas
- **Tanda Tangan Upload**: Format PNG, JPG, JPEG, maksimal 2MB
- **Materai**: Format gambar (PNG, JPG, JPEG), maksimal 2MB
- **PDF**: Validasi eksistensi file sebelum processing

### Kontrol Akses
- **Validasi**: admin, admin_p3m, direktur
- **Persetujuan**: direktur only
- Middleware role-based authentication

### Validasi Data
- Tanda tangan wajib (digital canvas tidak boleh kosong ATAU file upload harus ada)
- File materai wajib diupload
- Validasi ukuran file (maksimal 2MB untuk tanda tangan dan materai)
- Validasi format file (PNG, JPG, JPEG untuk tanda tangan dan materai)
- Validasi posisi overlay (0-100% untuk X,Y dan 1-50% untuk width,height)

## Troubleshooting

### PDF Tidak Muncul
- Pastikan PDF.js library ter-load dengan benar
- Check console browser untuk error
- Pastikan file PDF dapat diakses via URL

### Overlay Tidak Tersimpan
- Check CSRF token
- Pastikan route dan method sesuai
- Check permission write ke database

### Tanda Tangan Tidak Muncul
- Clear browser cache
- Pastikan signature pad library ter-load
- Check canvas element initialization

## Pengembangan Selanjutnya

### Fitur yang Bisa Ditambahkan
1. **Multi-page PDF Support**: Saat ini hanya support halaman pertama
2. **Template Signature**: Menyimpan template posisi untuk dokumen serupa
3. **Batch Processing**: Apply signature ke multiple dokumen sekaligus
4. **Digital Certificate**: Integrasi dengan sertifikat digital resmi
5. **Audit Trail**: Log semua aktivitas tanda tangan dan materai
6. **Preview Final**: Preview dokumen final dengan overlay sebelum submit

### Optimisasi Performance
1. **Lazy Loading**: Load PDF hanya saat dibutuhkan
2. **Caching**: Cache rendered PDF pages
3. **Compression**: Kompres signature images
4. **Background Processing**: Process overlay application di background

## Changelog

### v2.1.0 (Current)
- ✅ **Fitur Upload Tanda Tangan**: Opsi untuk upload file tanda tangan sebagai alternatif digital signature
- ✅ **Dual Signature Method**: Toggle antara tanda tangan digital (canvas) dan upload file
- ✅ **Enhanced Validation**: Validasi terpisah untuk kedua metode tanda tangan
- ✅ **File Management**: Penamaan file yang lebih terorganisir (digital_signature_ vs uploaded_signature_)
- ✅ **UI/UX Improvements**: Radio button selection dan smooth transitions

### v2.0.0 (Previous)
- ✅ Integrasi fitur tanda tangan & materai ke workflow persetujuan direktur
- ✅ Halaman validasi dengan tanda tangan digital untuk direktur
- ✅ Editor drag & drop untuk dokumen PDF di persetujuan
- ✅ Manajemen overlay tanda tangan dan materai di persetujuan
- ✅ Routes dan controller methods untuk persetujuan
- ✅ View templates untuk persetujuan dengan signature

### v1.0.0 (Previous)
- ✅ Drag & drop PDF editor dengan PDF.js
- ✅ Canvas tanda tangan digital dengan Signature Pad
- ✅ Upload dan preview materai
- ✅ Kontrol posisi presisi (X, Y, Width, Height)
- ✅ Resize handles untuk overlay
- ✅ Zoom controls untuk PDF viewer
- ✅ Penyimpanan overlay dalam format JSON
- ✅ Integrasi dengan workflow validasi
- ✅ Role-based access control
- ✅ File validation dan security 