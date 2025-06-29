# Panduan Lengkap Generate CDM, LDM, dan PDM di PowerDesigner

## Overview
Panduan ini menjelaskan cara membuat **Conceptual Data Model (CDM)**, **Logical Data Model (LDM)**, dan **Physical Data Model (PDM)** untuk Sistem Pengajuan HKI menggunakan PowerDesigner.

---

## 📁 File yang Diperlukan

1. **`hki-cdm-structure.txt`** - Struktur Conceptual Data Model
2. **`hki-pdm-ddl.sql`** - Script SQL DDL untuk Physical Data Model
3. **`database-erd.puml`** - ERD PlantUML (untuk referensi visual)

---

## 🎯 Langkah 1: Membuat CDM (Conceptual Data Model)

### A. Buka PowerDesigner
1. Launch **SAP PowerDesigner**
2. Pilih **File → New Model**
3. Pilih **Conceptual Data Model**
4. Tentukan nama model: `HKI_CDM`
5. Pilih DBMS target: **Generic**

### B. Buat Entities Manual
Berdasarkan file `hki-cdm-structure.txt`, buat entities berikut:

#### 1. Entity PENGGUNA
- Klik **Entity** tool di palette
- Gambar di canvas, beri nama `PENGGUNA`
- Double-click entity → tab **Attributes**
- Tambahkan atribut:
  ```
  ID_Pengguna (Primary Key, Integer)
  Username (Varchar(255), Unique)
  Nama_Lengkap (Varchar(255))
  Email (Varchar(255), Unique)
  Password (Varchar(255))
  Role (Varchar(50))
  NIP_NIM (Varchar(50))
  No_HP (Varchar(20))
  TTD_Path (Varchar(255))
  ```

#### 2. Entity MAHASISWA
- Tambahkan atribut:
  ```
  ID_Mahasiswa (Primary Key, Integer)
  NIM (Varchar(20), Unique)
  Nama (Varchar(255))
  Program_Studi (Varchar(255))
  Fakultas (Varchar(255))
  Semester (Integer)
  ```

#### 3. Entity DOSEN
- Tambahkan atribut:
  ```
  ID_Dosen (Primary Key, Integer)
  NIP (Varchar(20), Unique)
  Nama (Varchar(255))
  Fakultas (Varchar(255))
  Jabatan (Varchar(100))
  Bidang_Keahlian (Varchar(255))
  ```

*Lanjutkan untuk 7 entity lainnya sesuai file `hki-cdm-structure.txt`*

### C. Buat Relationships
1. Gunakan **Relationship** tool
2. Hubungkan entities sesuai dengan spesifikasi:
   - PENGGUNA → MAHASISWA (1:1)
   - PENGGUNA → DOSEN (1:1)
   - PENGGUNA → PENGAJUAN_HKI (1:N)
   - dst.

### D. Set Business Rules
1. Klik **Model → Business Rules**
2. Tambahkan rules dari file `hki-cdm-structure.txt`

---

## 🎯 Langkah 2: Generate LDM dari CDM

### A. Generate LDM Otomatis
1. Dengan CDM terbuka, pilih **Tools → Generate Logical Data Model**
2. **Generation Options**:
   - ✅ Generate new Logical Data Model
   - ✅ Copy conceptual naming conventions
   - ✅ Generate referential integrity constraints
3. **Target LDM**: Pilih target DBMS → **MySQL 8.0**
4. Klik **OK**

### B. Review dan Refine LDM
1. PowerDesigner akan membuka LDM baru
2. **Review Tables**: Pastikan semua entities menjadi tables
3. **Check Columns**: Verify data types sesuai target DBMS
4. **Review Indexes**: Tambahkan indexes untuk performa
5. **Constraints**: Pastikan foreign keys dan constraints terbuat

### C. Optimasi LDM
```sql
-- Contoh optimasi yang bisa ditambahkan:
-- Index untuk kolom yang sering di-query
CREATE INDEX idx_pengajuan_status ON pengajuan_hkis(status);
CREATE INDEX idx_pengajuan_user_id ON pengajuan_hkis(user_id);

-- Constraints tambahan
ALTER TABLE pengajuan_hkis 
ADD CONSTRAINT chk_status 
CHECK (status IN ('draft','menunggu_validasi','divalidasi_sedang_diproses',
                  'menunggu_pembayaran','menunggu_verifikasi_pembayaran',
                  'disetujui','selesai','ditolak'));
```

---

## 🎯 Langkah 3: Generate PDM dari LDM

### A. Generate PDM Otomatis
1. Dengan LDM terbuka, pilih **Tools → Generate Physical Data Model**
2. **Generation Options**:
   - ✅ Generate new Physical Data Model
   - ✅ Generate referential integrity
   - ✅ Generate check parameters
   - ✅ Generate indexes
   - ✅ Generate triggers
3. **Target PDM**: **MySQL 8.0**
4. Klik **OK**

### B. Import DDL Script (Alternatif)
Jika ingin menggunakan script yang sudah jadi:

1. **File → Reverse Engineer → Database**
2. Pilih **Using a SQL script**
3. Browse dan pilih file `hki-pdm-ddl.sql`
4. **Target DBMS**: MySQL 8.0
5. **Options**:
   - ✅ Reverse tables
   - ✅ Reverse views  
   - ✅ Reverse indexes
   - ✅ Reverse triggers
   - ✅ Reverse stored procedures
6. Klik **OK**

### C. Refine Physical Model
1. **Table Properties**: Set storage engines, charset
   ```sql
   ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
   ```

2. **Performance Tuning**:
   - Partitioning untuk tabel besar
   - Index optimization
   - Storage parameter tuning

3. **Security**:
   - User roles dan permissions
   - Column-level security

---

## 🎯 Langkah 4: Validasi dan Generate Database

### A. Validate Models
1. **Tools → Check Model** di setiap model
2. Fix errors dan warnings
3. **Impact Analysis** untuk perubahan

### B. Generate Database Script
1. Buka PDM
2. **Database → Generate Database**
3. **Target**: MySQL connection atau file
4. **Options**:
   - ✅ Drop statements
   - ✅ Create statements  
   - ✅ Data statements (jika ada sample data)
5. **Preview** script sebelum execute

### C. Documentation
1. **Report → Generate Report**
2. Pilih template: **Data Model Report**
3. Include:
   - ✅ Tables and columns
   - ✅ Relationships
   - ✅ Indexes
   - ✅ Business rules
   - ✅ Data dictionary

---

## 📋 Checklist Hasil Akhir

### ✅ CDM (Conceptual Data Model)
- [ ] 10 entities sesuai business requirements
- [ ] Business rules terdefinisi
- [ ] Relationships antar entities
- [ ] Attributes dengan proper domains

### ✅ LDM (Logical Data Model)  
- [ ] Tables dengan proper normalization
- [ ] Primary keys dan foreign keys
- [ ] Indexes untuk performance
- [ ] Constraints dan business rules
- [ ] Views untuk reporting

### ✅ PDM (Physical Data Model)
- [ ] Platform-specific data types
- [ ] Storage specifications
- [ ] Performance optimizations
- [ ] Security configurations
- [ ] Deployment scripts

---

## 🚀 Tips dan Best Practices

### 1. Naming Conventions
```sql
-- Tables: plural, lowercase with underscore
users, pengajuan_hkis, dokumen_hkis

-- Columns: singular, descriptive
user_id, created_at, status_pembayaran

-- Indexes: descriptive prefix
idx_users_email, idx_pengajuan_status

-- Foreign Keys: descriptive
fk_pengajuan_user_id
```

### 2. Version Control
- Save each model with version numbers
- Use **Model → Compare Models** untuk track changes
- Backup models sebelum major changes

### 3. Collaboration
- Use **Repository** untuk team collaboration
- **Check-in/Check-out** models untuk version control
- Generate reports untuk documentation sharing

### 4. Maintenance
- Regular model validation
- Impact analysis untuk schema changes
- Keep models synchronized dengan actual database

---

## 🔧 Troubleshooting Common Issues

### 1. Generation Errors
```
Error: Cannot generate foreign key
Solution: Check parent table exists dan primary key defined
```

### 2. Data Type Mismatches
```
Error: Invalid data type for target DBMS
Solution: Review column data types in LDM
```

### 3. Naming Conflicts
```
Error: Duplicate object name
Solution: Check naming conventions dan object uniqueness
```

### 4. Constraint Violations
```
Error: Constraint cannot be created
Solution: Review business rules dan data constraints
```

---

## 📖 Resources

- **PowerDesigner Documentation**: SAP Help Portal
- **MySQL Reference**: MySQL 8.0 Reference Manual
- **Data Modeling Best Practices**: Industry standards
- **Sample Models**: PowerDesigner example models

---

**Catatan**: Pastikan PowerDesigner license valid dan DBMS target tersedia untuk testing koneksi database.

Dengan mengikuti panduan ini, Anda akan memiliki CDM, LDM, dan PDM yang lengkap dan siap untuk implementasi sistem HKI. 