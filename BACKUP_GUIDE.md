# PANDUAN BACKUP & RESTORE SISTEM HKI POLIBAN

## 📋 OVERVIEW
Dokumentasi ini menjelaskan cara melakukan backup dan restore sistem HKI Poliban dengan lengkap dan aman.

## 🔧 TOOLS BACKUP YANG TERSEDIA

### 1. Script Backup Otomatis (`create_backup.bat`)
Script ini akan membuat backup lengkap dari:
- ✅ Source code aplikasi
- ✅ Database MySQL (otomatis jika tersedia)
- ✅ File konfigurasi (.env)
- ✅ Panduan restore

**Cara menggunakan:**
```bash
# Double-click file create_backup.bat
# atau jalankan dari command prompt:
create_backup.bat
```

### 2. Script Backup Database (`backup_database.bat`)
Script khusus untuk backup database saja.

**Cara menggunakan:**
```bash
# Double-click file backup_database.bat
backup_database.bat
```

### 3. Backup Manual
Jika script otomatis tidak berfungsi, lakukan backup manual.

## 📦 CARA BACKUP OTOMATIS

### Langkah 1: Jalankan Script Backup
```bash
# Buka Command Prompt di direktori project
cd C:\xampp\htdocs\PengajuanHKI
create_backup.bat
```

### Langkah 2: Verifikasi Hasil Backup
Setelah script selesai, akan terbuat folder `backup_hki_YYYYMMDD_HHMMSS` yang berisi:
```
backup_hki_20241201_143025/
├── source_code/          # Seluruh source code
├── database_backup.sql   # Backup database
├── .env.backup          # File konfigurasi
├── backup_info.txt      # Informasi backup
└── restore_guide.txt    # Panduan restore
```

## 🔄 CARA BACKUP MANUAL

### 1. Backup Source Code
```bash
# Copy seluruh folder project ke lokasi backup
xcopy /E /I /H "C:\xampp\htdocs\PengajuanHKI" "D:\backup\hki_backup_manual"
```

### 2. Backup Database
#### Opsi A: Via phpMyAdmin
1. Buka http://localhost/phpmyadmin
2. Pilih database `hki_db`
3. Klik tab "Export"
4. Pilih "Quick" export method
5. Format: SQL
6. Klik "Go" dan simpan file

#### Opsi B: Via Command Line
```bash
# Buka Command Prompt
cd C:\xampp\mysql\bin
mysqldump -u root -p hki_db > backup_database.sql
```

### 3. Backup File Konfigurasi
```bash
# Copy file .env
copy .env .env.backup
```

## 🔧 CARA RESTORE SISTEM

### 1. Persiapan Environment
Pastikan tersedia:
- ✅ XAMPP (Apache + MySQL + PHP)
- ✅ Composer
- ✅ Node.js dan NPM

### 2. Restore Source Code
```bash
# Copy source code ke direktori web server
xcopy /E /I /H "backup_hki_20241201_143025\source_code" "C:\xampp\htdocs\PengajuanHKI_restored"
```

### 3. Restore Database
#### Opsi A: Via phpMyAdmin
1. Buka http://localhost/phpmyadmin
2. Buat database baru: `hki_db_restored`
3. Pilih database tersebut
4. Klik tab "Import"
5. Pilih file `database_backup.sql`
6. Klik "Go"

#### Opsi B: Via Command Line
```bash
# Buat database baru
mysql -u root -p -e "CREATE DATABASE hki_db_restored;"

# Import backup
mysql -u root -p hki_db_restored < backup_hki_20241201_143025\database_backup.sql
```

### 4. Konfigurasi Environment
```bash
# Masuk ke direktori project yang di-restore
cd C:\xampp\htdocs\PengajuanHKI_restored

# Copy file environment
copy .env.backup .env

# Edit file .env sesuaikan konfigurasi database
# DB_DATABASE=hki_db_restored
```

### 5. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 6. Konfigurasi Laravel
```bash
# Generate application key
php artisan key:generate

# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Create storage link
php artisan storage:link

# Cache configuration
php artisan config:cache
php artisan route:cache
```

### 7. Test Aplikasi
```bash
# Jalankan development server
php artisan serve

# Buka browser: http://localhost:8000
```

## ⚠️ TROUBLESHOOTING

### Database Connection Error
- Periksa konfigurasi database di file `.env`
- Pastikan MySQL service berjalan
- Periksa username/password database

### Permission Error
- Set permission folder storage dan bootstrap/cache
- Di Windows: Properties → Security → Edit permissions
- Di Linux/Mac: `chmod -R 775 storage bootstrap/cache`

### Missing Dependencies
```bash
# Jika ada error vendor tidak ditemukan
composer install

# Jika ada error node_modules
npm install

# Jika ada error APP_KEY
php artisan key:generate
```

### File Storage Issues
```bash
# Recreate storage link
php artisan storage:link

# Periksa folder storage/app/public
# Pastikan file uploaded dapat diakses
```

## 📝 CHECKLIST BACKUP RUTIN

### Harian
- [ ] Backup database via phpMyAdmin/script

### Mingguan  
- [ ] Backup lengkap dengan script otomatis
- [ ] Verifikasi backup dapat di-restore

### Bulanan
- [ ] Copy backup ke storage eksternal
- [ ] Test restore di environment terpisah
- [ ] Update dokumentasi jika ada perubahan

## 🔐 KEAMANAN BACKUP

### File yang Harus Di-backup
- ✅ Source code lengkap
- ✅ Database
- ✅ File .env (konfigurasi)
- ✅ Storage files (uploaded documents)

### File yang TIDAK perlu di-backup
- ❌ node_modules/
- ❌ vendor/
- ❌ storage/logs/
- ❌ storage/framework/cache/
- ❌ .git/ (jika ada)

### Keamanan Data
- 🔒 Simpan backup di lokasi yang aman
- 🔒 Encrypt backup jika berisi data sensitif  
- 🔒 Jangan commit file .env ke repository
- 🔒 Gunakan strong password untuk database

## 📞 SUPPORT

Jika mengalami masalah:
1. Periksa log error di `storage/logs/laravel.log`
2. Periksa error log Apache/MySQL di XAMPP
3. Pastikan semua service berjalan normal
4. Konsultasi dengan tim developer

---
**Dibuat pada:** $(Get-Date)  
**Versi Sistem:** Laravel 10.x  
**Environment:** Windows + XAMPP 