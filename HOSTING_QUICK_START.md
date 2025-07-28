# 🚀 Quick Start Guide - Hosting Sistem Pengajuan HKI

## 📋 Checklist Hosting Cepat

### ✅ Persiapan Server
- [ ] Server dengan Ubuntu 20.04/22.04 atau CentOS 8+
- [ ] Domain name sudah di-pointing ke server
- [ ] Akses SSH ke server
- [ ] Minimal 2GB RAM, 20GB storage

### ✅ Instalasi Dependencies
```bash
# Update sistem
sudo apt update && sudo apt upgrade -y

# Install web server dan database
sudo apt install nginx mysql-server -y

# Install PHP 8.1 dan extensions
sudo apt install php8.1-fpm php8.1-mysql php8.1-gd php8.1-xml php8.1-curl php8.1-zip php8.1-mbstring php8.1-bcmath -y

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js dan NPM
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install nodejs -y
```

### ✅ Setup Database
```bash
sudo mysql_secure_installation

# Login ke MySQL
sudo mysql -u root -p

# Buat database dan user
CREATE DATABASE db_hki CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'hki_user'@'localhost' IDENTIFIED BY 'password_yang_kuat';
GRANT ALL PRIVILEGES ON db_hki.* TO 'hki_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### ✅ Deploy Aplikasi
```bash
# Clone/Upload aplikasi
cd /var/www/html
sudo git clone your-repository pengajuan-hki
# atau upload via FTP

cd pengajuan-hki

# Install dependencies
composer install --optimize-autoloader --no-dev
npm install && npm run build

# Setup environment
cp production.env.example .env
nano .env  # Edit sesuai konfigurasi server

# Generate app key
php artisan key:generate

# Run migrations
php artisan migrate --force

# Set permissions
sudo chown -R www-data:www-data .
sudo chmod -R 775 storage bootstrap/cache

# Create storage link
php artisan storage:link
```

### ✅ Konfigurasi Web Server
Gunakan konfigurasi Nginx dari file `deployment-guide.md`

### ✅ Setup SSL
```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d yourdomain.com
```

### ✅ Setup Monitoring & Backup
```bash
# Install automation scripts
chmod +x *.sh
./setup-cron.sh

# Test scripts
./server-check.sh
./backup.sh daily
```

---

## 🔧 Maintenance Schedule

### Daily (Otomatis via Cron)
- ✅ 02:00 - Database backup
- ✅ 03:30 - System maintenance  
- ✅ 04:00 - Log cleanup
- ✅ Setiap 15 menit - Alert monitoring
- ✅ Setiap 30 menit - Health check

### Weekly (Manual Check)
- [ ] Review system logs
- [ ] Check disk space trends
- [ ] Monitor database size
- [ ] Review backup integrity
- [ ] Security updates

### Monthly (Manual)
- [ ] Full system backup test
- [ ] SSL certificate check
- [ ] Performance review
- [ ] Security audit
- [ ] Database optimization

---

## 🚨 Emergency Procedures

### Aplikasi Down
```bash
# Check service status
sudo systemctl status nginx mysql php8.1-fpm

# Restart services
sudo systemctl restart nginx
sudo systemctl restart mysql
sudo systemctl restart php8.1-fpm

# Check application logs
tail -f /var/www/html/pengajuan-hki/storage/logs/laravel.log
```

### Database Issues
```bash
# Check MySQL status
sudo systemctl status mysql

# Check connections
mysql -u hki_user -p -e "SHOW PROCESSLIST;"

# Repair tables if needed
mysql -u hki_user -p db_hki -e "REPAIR TABLE pengajuan_hki;"
```

### High Traffic / Performance Issues
```bash
# Enable maintenance mode
cd /var/www/html/pengajuan-hki
php artisan down

# Clear all caches
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Bring back online
php artisan up
```

### Restore from Backup
```bash
# Database restore
cd /backup
gunzip database/db_backup_YYYYMMDD_HHMMSS.sql.gz
mysql -u hki_user -p db_hki < database/db_backup_YYYYMMDD_HHMMSS.sql

# Files restore
tar -xzf files/app_files_YYYYMMDD_HHMMSS.tar.gz -C /var/www/html/
sudo chown -R www-data:www-data /var/www/html/pengajuan-hki
```

---

## 📞 Contact Information

**System Administrator:**
- Email: admin@yourdomain.com
- Phone: +62-xxx-xxxx-xxxx

**Hosting Provider Support:**
- Support URL: 
- Phone: 
- Emergency Contact:

---

## 📚 Useful Commands

```bash
# Check system status
./server-check.sh

# Manual backup
./backup.sh daily

# Deploy updates
./deploy.sh production

# Monitor logs
tail -f /var/log/hki-automation/health-check.log
tail -f /var/www/html/pengajuan-hki/storage/logs/laravel.log

# Check disk space
df -h

# Check memory usage
free -h

# Check active connections
ss -tuln | grep :80
ss -tuln | grep :443

# PHP-FPM status
sudo systemctl status php8.1-fpm

# Nginx status
sudo systemctl status nginx

# MySQL status
sudo systemctl status mysql
```

---

## 🔗 Important URLs

- **Production Site:** https://yourdomain.com
- **Admin Panel:** https://yourdomain.com/admin
- **Server Status:** https://yourdomain.com/server-status (jika dikonfigurasi)
- **PHPInfo:** https://yourdomain.com/phpinfo.php (hapus setelah testing)

---

## 📱 Monitoring Dashboard (Optional)

Untuk monitoring yang lebih advanced, pertimbangkan setup:

1. **Grafana + Prometheus** - Monitoring metrics
2. **ELK Stack** - Log analysis  
3. **Uptime Robot** - External monitoring
4. **New Relic/DataDog** - APM monitoring

---

**✅ Setup completed successfully!**

Sistem Pengajuan HKI Anda sudah siap untuk production. Pastikan untuk melakukan testing menyeluruh sebelum go-live.

*Last updated: $(date)* 