@echo off
setlocal enabledelayedexpansion

:: Set backup directory with timestamp
set "timestamp=%date:~10,4%%date:~4,2%%date:~7,2%_%time:~0,2%%time:~3,2%%time:~6,2%"
set "timestamp=%timestamp: =0%"
set "backup_dir=backup_hki_%timestamp%"

echo =====================================
echo    BACKUP SISTEM HKI POLIBAN
echo =====================================
echo.
echo Membuat backup sistem pada: %backup_dir%
echo.

:: Create backup directory
mkdir "%backup_dir%" 2>nul

:: 1. Backup source code (exclude node_modules, vendor, storage/logs, etc.)
echo [1/5] Backup source code...
xcopy /E /I /H /Y /EXCLUDE:backup_exclude.txt . "%backup_dir%\source_code\" >nul 2>&1

:: 2. Create database backup (if MySQL is available)
echo [2/5] Backup database...
if exist "C:\xampp\mysql\bin\mysqldump.exe" (
    echo   - Mencoba backup database MySQL...
    "C:\xampp\mysql\bin\mysqldump.exe" -u root -p --routines --triggers hki_db > "%backup_dir%\database_backup.sql" 2>nul
    if !errorlevel! equ 0 (
        echo   - Database berhasil di-backup
    ) else (
        echo   - Gagal backup database otomatis, silakan backup manual
    )
) else (
    echo   - MySQL tidak ditemukan, silakan backup database manual
)

:: 3. Backup environment files
echo [3/5] Backup configuration files...
if exist ".env" copy ".env" "%backup_dir%\.env.backup" >nul 2>&1
if exist ".env.example" copy ".env.example" "%backup_dir%\.env.example" >nul 2>&1

:: 4. Create backup info file
echo [4/5] Membuat informasi backup...
(
    echo BACKUP SISTEM HKI POLIBAN
    echo =========================
    echo.
    echo Tanggal Backup: %date% %time%
    echo Direktori Source: %cd%
    echo Versi Laravel: 
    php artisan --version 2>nul
    echo.
    echo STRUKTUR BACKUP:
    echo - source_code/     : Seluruh source code aplikasi
    echo - database_backup.sql : Backup database ^(jika berhasil^)
    echo - .env.backup      : File konfigurasi environment
    echo - backup_info.txt  : File informasi ini
    echo - restore_guide.txt: Panduan restore
    echo.
    echo CARA RESTORE:
    echo 1. Copy folder source_code ke lokasi yang diinginkan
    echo 2. Restore database dari file database_backup.sql
    echo 3. Copy .env.backup menjadi .env dan sesuaikan konfigurasi
    echo 4. Jalankan: composer install
    echo 5. Jalankan: npm install
    echo 6. Jalankan: php artisan key:generate
    echo 7. Jalankan: php artisan migrate ^(jika diperlukan^)
    echo 8. Jalankan: php artisan storage:link
) > "%backup_dir%\backup_info.txt"

:: 5. Create restore guide
echo [5/5] Membuat panduan restore...
(
    echo PANDUAN RESTORE SISTEM HKI POLIBAN
    echo ==================================
    echo.
    echo LANGKAH RESTORE:
    echo.
    echo 1. PERSIAPAN
    echo    - Pastikan XAMPP sudah terinstall
    echo    - Pastikan PHP, Composer, dan Node.js tersedia
    echo.
    echo 2. RESTORE SOURCE CODE
    echo    - Copy seluruh isi folder source_code/ ke lokasi web server ^(misal: C:\xampp\htdocs\PengajuanHKI_restored^)
    echo.
    echo 3. RESTORE DATABASE
    echo    - Buka phpMyAdmin atau MySQL command line
    echo    - Buat database baru: CREATE DATABASE hki_db;
    echo    - Import file database_backup.sql ke database tersebut
    echo    - Atau gunakan command: mysql -u root -p hki_db ^< database_backup.sql
    echo.
    echo 4. KONFIGURASI ENVIRONMENT
    echo    - Copy file .env.backup menjadi .env
    echo    - Sesuaikan konfigurasi database di .env:
    echo      DB_CONNECTION=mysql
    echo      DB_HOST=127.0.0.1
    echo      DB_PORT=3306
    echo      DB_DATABASE=hki_db
    echo      DB_USERNAME=root
    echo      DB_PASSWORD=
    echo.
    echo 5. INSTALL DEPENDENCIES
    echo    - Buka terminal di direktori project
    echo    - Jalankan: composer install
    echo    - Jalankan: npm install
    echo.
    echo 6. KONFIGURASI LARAVEL
    echo    - Jalankan: php artisan key:generate
    echo    - Jalankan: php artisan config:cache
    echo    - Jalankan: php artisan route:cache
    echo    - Jalankan: php artisan storage:link
    echo.
    echo 7. SET PERMISSIONS ^(jika di Linux/Mac^)
    echo    - chmod -R 775 storage
    echo    - chmod -R 775 bootstrap/cache
    echo.
    echo 8. TEST APLIKASI
    echo    - Jalankan: php artisan serve
    echo    - Buka browser: http://localhost:8000
    echo.
    echo CATATAN PENTING:
    echo - Pastikan semua file storage/app/public ada dan dapat diakses
    echo - Periksa file .env untuk konfigurasi email dan lainnya
    echo - Jika ada error permission, sesuaikan ownership file/folder
    echo - Backup ini dibuat pada: %date% %time%
) > "%backup_dir%\restore_guide.txt"

:: Create exclude file for xcopy
(
    echo node_modules\
    echo vendor\
    echo storage\logs\
    echo storage\framework\cache\
    echo storage\framework\sessions\
    echo storage\framework\views\
    echo .git\
    echo backup_*\
    echo *.log
) > backup_exclude.txt

echo.
echo =====================================
echo    BACKUP SELESAI!
echo =====================================
echo.
echo Backup disimpan di: %backup_dir%
echo.
echo File yang di-backup:
echo - Source code aplikasi
echo - Konfigurasi (.env)
echo - Database (jika berhasil)
echo - Panduan restore
echo.
echo Untuk restore, baca file: %backup_dir%\restore_guide.txt
echo.

:: Clean up temporary files
del backup_exclude.txt >nul 2>&1

pause 