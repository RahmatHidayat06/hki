@echo off
echo =====================================
echo    BACKUP DATABASE HKI POLIBAN
echo =====================================
echo.
echo Script ini akan membantu Anda backup database MySQL
echo.

set /p db_name="Masukkan nama database (default: hki_db): "
if "%db_name%"=="" set db_name=hki_db

set /p db_user="Masukkan username MySQL (default: root): "
if "%db_user%"=="" set db_user=root

set "timestamp=%date:~10,4%%date:~4,2%%date:~7,2%_%time:~0,2%%time:~3,2%%time:~6,2%"
set "timestamp=%timestamp: =0%"
set "backup_file=database_backup_%timestamp%.sql"

echo.
echo Melakukan backup database...
echo Database: %db_name%
echo User: %db_user%
echo File output: %backup_file%
echo.

if exist "C:\xampp\mysql\bin\mysqldump.exe" (
    "C:\xampp\mysql\bin\mysqldump.exe" -u %db_user% -p --routines --triggers %db_name% > %backup_file%
    if %errorlevel% equ 0 (
        echo.
        echo ✓ Backup database berhasil!
        echo File disimpan sebagai: %backup_file%
    ) else (
        echo.
        echo ✗ Backup database gagal!
        echo Periksa nama database dan kredensial MySQL
    )
) else (
    echo ✗ MySQL tidak ditemukan di C:\xampp\mysql\bin\
    echo Silakan sesuaikan path MySQL atau backup manual melalui phpMyAdmin
)

echo.
pause 