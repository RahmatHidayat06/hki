#!/bin/bash

# Backup Script untuk Sistem Pengajuan HKI
# Usage: ./backup.sh [daily|weekly|monthly]

set -e

BACKUP_TYPE=${1:-daily}
BACKUP_DIR="/backup"
APP_DIR="/var/www/html/pengajuan-hki"
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="db_hki"
DB_USER="hki_user"
DB_PASS="password_yang_kuat"

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

print_status() {
    echo -e "${GREEN}✓${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}⚠${NC} $1"
}

echo "🔄 Starting $BACKUP_TYPE backup process..."

# Create backup directories
sudo mkdir -p $BACKUP_DIR/{database,files,logs}
sudo chown -R $USER:$USER $BACKUP_DIR

# Database Backup
print_status "Backing up database..."
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME > $BACKUP_DIR/database/db_backup_$DATE.sql
gzip $BACKUP_DIR/database/db_backup_$DATE.sql
print_status "Database backup completed: db_backup_$DATE.sql.gz"

# Files Backup
print_status "Backing up application files..."
tar -czf $BACKUP_DIR/files/app_files_$DATE.tar.gz -C $(dirname $APP_DIR) \
    --exclude='$(basename $APP_DIR)/storage/logs/*' \
    --exclude='$(basename $APP_DIR)/storage/framework/cache/*' \
    --exclude='$(basename $APP_DIR)/storage/framework/sessions/*' \
    --exclude='$(basename $APP_DIR)/storage/framework/views/*' \
    --exclude='$(basename $APP_DIR)/vendor' \
    --exclude='$(basename $APP_DIR)/node_modules' \
    $(basename $APP_DIR)
print_status "Files backup completed: app_files_$DATE.tar.gz"

# Storage Directory Backup (uploaded files)
print_status "Backing up uploaded files..."
tar -czf $BACKUP_DIR/files/storage_$DATE.tar.gz -C $APP_DIR storage/app/public
print_status "Storage backup completed: storage_$DATE.tar.gz"

# Log Backup
if [ -d "$APP_DIR/storage/logs" ]; then
    print_status "Backing up application logs..."
    tar -czf $BACKUP_DIR/logs/logs_$DATE.tar.gz -C $APP_DIR/storage logs
    print_status "Logs backup completed: logs_$DATE.tar.gz"
fi

# Cleanup old backups based on type
case $BACKUP_TYPE in
    "daily")
        KEEP_DAYS=7
        ;;
    "weekly")
        KEEP_DAYS=30
        ;;
    "monthly")
        KEEP_DAYS=365
        ;;
    *)
        KEEP_DAYS=7
        ;;
esac

print_status "Cleaning up backups older than $KEEP_DAYS days..."

# Clean database backups
find $BACKUP_DIR/database -name "*.sql.gz" -mtime +$KEEP_DAYS -delete 2>/dev/null || true

# Clean file backups
find $BACKUP_DIR/files -name "*.tar.gz" -mtime +$KEEP_DAYS -delete 2>/dev/null || true

# Clean log backups
find $BACKUP_DIR/logs -name "*.tar.gz" -mtime +$KEEP_DAYS -delete 2>/dev/null || true

# Calculate backup sizes
DATABASE_SIZE=$(du -sh $BACKUP_DIR/database/db_backup_$DATE.sql.gz | cut -f1)
FILES_SIZE=$(du -sh $BACKUP_DIR/files/app_files_$DATE.tar.gz | cut -f1)
STORAGE_SIZE=$(du -sh $BACKUP_DIR/files/storage_$DATE.tar.gz | cut -f1)

print_status "Backup process completed! 🎉"

echo ""
echo "📊 Backup Summary:"
echo "Type: $BACKUP_TYPE"
echo "Timestamp: $(date)"
echo "Database backup: $DATABASE_SIZE"
echo "Files backup: $FILES_SIZE" 
echo "Storage backup: $STORAGE_SIZE"
echo "Location: $BACKUP_DIR"
echo ""

# Create backup report
cat > $BACKUP_DIR/backup_report_$DATE.txt << EOF
Backup Report - $BACKUP_TYPE
============================

Date: $(date)
Type: $BACKUP_TYPE backup
Status: Completed successfully

Files created:
- Database: $BACKUP_DIR/database/db_backup_$DATE.sql.gz ($DATABASE_SIZE)
- Application: $BACKUP_DIR/files/app_files_$DATE.tar.gz ($FILES_SIZE)
- Storage: $BACKUP_DIR/files/storage_$DATE.tar.gz ($STORAGE_SIZE)
- Logs: $BACKUP_DIR/logs/logs_$DATE.tar.gz

Retention policy: $KEEP_DAYS days

System info:
- Disk usage: $(df -h $BACKUP_DIR | tail -1)
- Database size: $(mysql -u $DB_USER -p$DB_PASS -e "SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 1) AS 'Database Size (MB)' FROM information_schema.tables WHERE table_schema='$DB_NAME';" | tail -1) MB

EOF

print_status "Backup report saved: backup_report_$DATE.txt"

# Optional: Send notification (uncomment if you have mail configured)
# echo "Backup completed for Sistem Pengajuan HKI" | mail -s "Backup Report - $BACKUP_TYPE" admin@yourdomain.com

print_warning "Remember to test backup restoration periodically!" 