# Panduan Deployment Sistem Pengajuan HKI

## 1. Persiapan Server

### Kebutuhan Server Minimum:
- **PHP**: 8.1 atau lebih tinggi
- **MySQL/MariaDB**: 5.7 atau lebih tinggi
- **Nginx/Apache**: Web server
- **Composer**: Untuk dependency management
- **Node.js & NPM**: Untuk asset compilation
- **SSL Certificate**: Untuk HTTPS

### Extensions PHP yang diperlukan:
```bash
php-cli, php-fpm, php-mysql, php-zip, php-gd, php-mbstring, php-curl, php-xml, php-bcmath
```

## 2. Upload dan Setup Files

### 2.1 Upload Project ke Server
```bash
# Clone atau upload project ke directory server
cd /var/www/html
git clone [repository-url] pengajuan-hki
# atau upload via FTP/SFTP

cd pengajuan-hki
```

### 2.2 Install Dependencies
```bash
# Install Composer dependencies
composer install --optimize-autoloader --no-dev

# Install NPM dependencies dan build assets
npm install
npm run build
```

### 2.3 Setup Environment
```bash
# Copy dan edit file environment
cp .env.example .env
nano .env
```

### 2.4 Generate Application Key
```bash
php artisan key:generate
```

## 3. Konfigurasi Database

### 3.1 Buat Database
```sql
CREATE DATABASE db_hki CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'hki_user'@'localhost' IDENTIFIED BY 'password_yang_kuat';
GRANT ALL PRIVILEGES ON db_hki.* TO 'hki_user'@'localhost';
FLUSH PRIVILEGES;
```

### 3.2 Update File .env
```env
APP_NAME="Sistem Pengajuan HKI"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_hki
DB_USERNAME=hki_user
DB_PASSWORD=password_yang_kuat

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Sistem Pengajuan HKI"
```

### 3.3 Run Migrations
```bash
php artisan migrate --force
```

### 3.4 Seed Database (Optional)
```bash
php artisan db:seed --force
```

## 4. Setup Web Server

### 4.1 Nginx Configuration
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;
    return 302 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/html/pengajuan-hki/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # SSL Configuration
    ssl_certificate /path/to/ssl/certificate.crt;
    ssl_certificate_key /path/to/ssl/private.key;
}
```

### 4.2 Apache Configuration (.htaccess sudah ada di Laravel)
```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    Redirect permanent / https://yourdomain.com/
</VirtualHost>

<VirtualHost *:443>
    ServerName yourdomain.com
    DocumentRoot /var/www/html/pengajuan-hki/public
    
    SSLEngine on
    SSLCertificateFile /path/to/ssl/certificate.crt
    SSLCertificateKeyFile /path/to/ssl/private.key
    
    <Directory /var/www/html/pengajuan-hki/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

## 5. Setup Permissions

```bash
# Set ownership
sudo chown -R www-data:www-data /var/www/html/pengajuan-hki
sudo chmod -R 755 /var/www/html/pengajuan-hki

# Set specific permissions untuk storage dan cache
sudo chmod -R 775 /var/www/html/pengajuan-hki/storage
sudo chmod -R 775 /var/www/html/pengajuan-hki/bootstrap/cache
```

## 6. Setup Task Scheduling (Cron Jobs)

```bash
# Edit crontab
sudo crontab -e

# Tambahkan line ini:
* * * * * cd /var/www/html/pengajuan-hki && php artisan schedule:run >> /dev/null 2>&1
```

## 7. Setup Queue Worker (Optional)

```bash
# Install supervisor
sudo apt install supervisor

# Buat config file
sudo nano /etc/supervisor/conf.d/laravel-worker.conf
```

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/pengajuan-hki/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/html/pengajuan-hki/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

## 8. Optimisasi Performance

```bash
# Cache config, routes, dan views
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize

# Create storage link
php artisan storage:link
```

## 9. Setup SSL Certificate

### Menggunakan Let's Encrypt (Gratis):
```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

## 10. Monitoring dan Backup

### 10.1 Setup Log Rotation
```bash
sudo nano /etc/logrotate.d/laravel

/var/www/html/pengajuan-hki/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    notifempty
    create 0640 www-data www-data
}
```

### 10.2 Database Backup Script
```bash
#!/bin/bash
BACKUP_DIR="/backup/database"
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u hki_user -p'password_yang_kuat' db_hki > $BACKUP_DIR/db_hki_$DATE.sql
gzip $BACKUP_DIR/db_hki_$DATE.sql

# Keep only last 7 days
find $BACKUP_DIR -name "*.sql.gz" -mtime +7 -delete
```

## 11. Testing

### 11.1 Cek Status Aplikasi
```bash
# Test database connection
php artisan tinker
>>> DB::connection()->getPdo();

# Test cache
php artisan cache:clear
php artisan config:clear

# Test queue
php artisan queue:work --once
```

### 11.2 Performance Testing
- Test load time halaman utama
- Test upload file
- Test generate PDF
- Test email notification

## 12. Security Checklist

- [ ] SSL Certificate aktif
- [ ] File .env tidak accessible dari web
- [ ] Database user dengan minimal privilege
- [ ] Firewall configured (port 80, 443, 22 only)
- [ ] Regular security updates
- [ ] Application logs monitoring
- [ ] Backup strategy implemented

## 13. Troubleshooting Common Issues

### File Permission Issues:
```bash
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

### Cache Issues:
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### Database Connection Issues:
- Cek credential di .env
- Cek MySQL service status
- Cek firewall rules

### 500 Internal Server Error:
- Cek Laravel logs: `storage/logs/laravel.log`
- Cek web server error logs
- Cek file permissions

---

## Maintenance Schedule

**Harian:**
- Monitor disk space
- Check application logs
- Verify backup completion

**Mingguan:**
- Security updates
- Performance monitoring
- Database optimization

**Bulanan:**
- Full system backup
- Security audit
- Performance review 