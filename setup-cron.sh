#!/bin/bash

# Setup Cron Jobs untuk Sistem Pengajuan HKI
# Usage: ./setup-cron.sh

SCRIPT_DIR="/opt/hki-scripts"
APP_DIR="/var/www/html/pengajuan-hki"

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

print_status() {
    echo -e "${GREEN}✓${NC} $1"
}

print_info() {
    echo -e "${BLUE}ℹ${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}⚠${NC} $1"
}

echo "🔧 Setting up automated cron jobs for Sistem Pengajuan HKI"
echo "=========================================================="

# Create scripts directory
print_status "Creating scripts directory..."
sudo mkdir -p $SCRIPT_DIR
sudo chown $USER:$USER $SCRIPT_DIR

# Copy scripts to system directory
print_status "Installing scripts..."
cp deploy.sh $SCRIPT_DIR/
cp backup.sh $SCRIPT_DIR/
cp server-check.sh $SCRIPT_DIR/

# Make scripts executable
sudo chmod +x $SCRIPT_DIR/*.sh

# Create log directory for cron jobs
sudo mkdir -p /var/log/hki-automation
sudo chown $USER:$USER /var/log/hki-automation

# Create cron job entries
print_status "Setting up cron jobs..."

# Create temporary cron file
TEMP_CRON="/tmp/hki-crontab"

# Get existing crontab (if any)
crontab -l 2>/dev/null > $TEMP_CRON || echo "" > $TEMP_CRON

# Remove existing HKI cron jobs to avoid duplicates
sed -i '/# HKI System/d' $TEMP_CRON
sed -i '/hki-scripts/d' $TEMP_CRON

# Add new cron jobs
cat >> $TEMP_CRON << 'EOF'

# HKI System - Laravel Task Scheduler (every minute)
* * * * * cd /var/www/html/pengajuan-hki && php artisan schedule:run >> /dev/null 2>&1

# HKI System - Daily Backup (2:00 AM)
0 2 * * * /opt/hki-scripts/backup.sh daily >> /var/log/hki-automation/backup.log 2>&1

# HKI System - Weekly Backup (2:30 AM on Sunday)
30 2 * * 0 /opt/hki-scripts/backup.sh weekly >> /var/log/hki-automation/backup.log 2>&1

# HKI System - Monthly Backup (3:00 AM on 1st of month)
0 3 1 * * /opt/hki-scripts/backup.sh monthly >> /var/log/hki-automation/backup.log 2>&1

# HKI System - Server Health Check (every 30 minutes)
*/30 * * * * /opt/hki-scripts/server-check.sh >> /var/log/hki-automation/health-check.log 2>&1

# HKI System - Log Rotation (daily at 4:00 AM)
0 4 * * * find /var/www/html/pengajuan-hki/storage/logs -name "*.log" -size +100M -exec truncate -s 0 {} \; >> /var/log/hki-automation/maintenance.log 2>&1

# HKI System - Clean temporary files (daily at 4:30 AM)
30 4 * * * find /tmp -name "hki_*" -mtime +1 -delete >> /var/log/hki-automation/maintenance.log 2>&1

EOF

# Install new crontab
crontab $TEMP_CRON
rm $TEMP_CRON

print_status "Cron jobs installed successfully!"

# Create email notification script
print_status "Creating notification script..."
cat > $SCRIPT_DIR/send-notification.sh << 'EOF'
#!/bin/bash

SUBJECT="$1"
MESSAGE="$2"
ADMIN_EMAIL="admin@yourdomain.com"

# Simple email notification (requires mail to be configured)
if command -v mail &> /dev/null; then
    echo "$MESSAGE" | mail -s "$SUBJECT" $ADMIN_EMAIL
fi

# Webhook notification (optional - uncomment and configure)
# curl -X POST -H 'Content-type: application/json' \
#   --data '{"text":"'"$SUBJECT: $MESSAGE"'"}' \
#   YOUR_SLACK_WEBHOOK_URL

# Log notification
echo "$(date): $SUBJECT - $MESSAGE" >> /var/log/hki-automation/notifications.log
EOF

chmod +x $SCRIPT_DIR/send-notification.sh

# Create monitoring script for critical alerts
print_status "Creating alert monitoring script..."
cat > $SCRIPT_DIR/alert-monitor.sh << 'EOF'
#!/bin/bash

APP_DIR="/var/www/html/pengajuan-hki"
ALERT_LOG="/var/log/hki-automation/alerts.log"

# Check disk space
DISK_USAGE=$(df / | awk 'NR==2 {print $5}' | sed 's/%//')
if [ $DISK_USAGE -gt 85 ]; then
    /opt/hki-scripts/send-notification.sh "ALERT: High Disk Usage" "Disk usage is at ${DISK_USAGE}% on $(hostname)"
    echo "$(date): ALERT - Disk usage ${DISK_USAGE}%" >> $ALERT_LOG
fi

# Check if application is responding
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://localhost)
if [ $HTTP_STATUS -ne 200 ]; then
    /opt/hki-scripts/send-notification.sh "ALERT: Application Down" "HTTP Status: $HTTP_STATUS on $(hostname)"
    echo "$(date): ALERT - Application not responding (HTTP $HTTP_STATUS)" >> $ALERT_LOG
fi

# Check for critical errors in Laravel log
if [ -f "$APP_DIR/storage/logs/laravel.log" ]; then
    RECENT_ERRORS=$(grep "CRITICAL\|EMERGENCY" $APP_DIR/storage/logs/laravel.log | grep "$(date +'%Y-%m-%d')" | wc -l)
    if [ $RECENT_ERRORS -gt 0 ]; then
        /opt/hki-scripts/send-notification.sh "ALERT: Critical Errors" "Found $RECENT_ERRORS critical errors today on $(hostname)"
        echo "$(date): ALERT - $RECENT_ERRORS critical errors found" >> $ALERT_LOG
    fi
fi
EOF

chmod +x $SCRIPT_DIR/alert-monitor.sh

# Add alert monitoring to cron (every 15 minutes)
TEMP_CRON="/tmp/hki-crontab-alert"
crontab -l > $TEMP_CRON

cat >> $TEMP_CRON << 'EOF'

# HKI System - Alert Monitoring (every 15 minutes)
*/15 * * * * /opt/hki-scripts/alert-monitor.sh >> /var/log/hki-automation/alert-monitor.log 2>&1

EOF

crontab $TEMP_CRON
rm $TEMP_CRON

# Create logrotate configuration
print_status "Setting up log rotation..."
sudo tee /etc/logrotate.d/hki-automation > /dev/null << 'EOF'
/var/log/hki-automation/*.log {
    daily
    missingok
    rotate 14
    compress
    notifempty
    create 0644 www-data www-data
}
EOF

# Create maintenance script
print_status "Creating maintenance script..."
cat > $SCRIPT_DIR/maintenance.sh << 'EOF'
#!/bin/bash

# Maintenance script untuk Sistem Pengajuan HKI

APP_DIR="/var/www/html/pengajuan-hki"
MAINT_LOG="/var/log/hki-automation/maintenance.log"

echo "$(date): Starting maintenance tasks..." >> $MAINT_LOG

# Clear Laravel caches if they get too large
CONFIG_CACHE_SIZE=$(stat -f%z "$APP_DIR/bootstrap/cache/config.php" 2>/dev/null || echo "0")
if [ $CONFIG_CACHE_SIZE -gt 1048576 ]; then # 1MB
    cd $APP_DIR
    php artisan cache:clear
    php artisan config:cache
    echo "$(date): Cleared and rebuilt cache" >> $MAINT_LOG
fi

# Clean up old session files
find $APP_DIR/storage/framework/sessions -type f -mtime +7 -delete 2>/dev/null

# Clean up old cache files
find $APP_DIR/storage/framework/cache -type f -mtime +7 -delete 2>/dev/null

# Optimize database tables (weekly on Sunday)
if [ "$(date +%u)" -eq 7 ]; then
    mysql -u hki_user -p'password_yang_kuat' db_hki -e "OPTIMIZE TABLE pengajuan_hki, users, dokumen_hki;" >> $MAINT_LOG 2>&1
fi

echo "$(date): Maintenance tasks completed" >> $MAINT_LOG
EOF

chmod +x $SCRIPT_DIR/maintenance.sh

# Add maintenance to cron (daily at 3:30 AM)
TEMP_CRON="/tmp/hki-crontab-maint"
crontab -l > $TEMP_CRON

cat >> $TEMP_CRON << 'EOF'

# HKI System - Daily Maintenance (3:30 AM)
30 3 * * * /opt/hki-scripts/maintenance.sh

EOF

crontab $TEMP_CRON
rm $TEMP_CRON

print_status "All automation scripts have been set up!"

echo ""
print_info "📋 Installed Scripts:"
echo "  - $SCRIPT_DIR/deploy.sh (manual deployment)"
echo "  - $SCRIPT_DIR/backup.sh (automated backups)"
echo "  - $SCRIPT_DIR/server-check.sh (health monitoring)"
echo "  - $SCRIPT_DIR/alert-monitor.sh (critical alerts)"
echo "  - $SCRIPT_DIR/maintenance.sh (system maintenance)"
echo "  - $SCRIPT_DIR/send-notification.sh (notifications)"
echo ""

print_info "📅 Scheduled Tasks:"
echo "  - Task Scheduler: Every minute"
echo "  - Daily Backup: 2:00 AM"
echo "  - Weekly Backup: 2:30 AM (Sunday)"
echo "  - Monthly Backup: 3:00 AM (1st of month)"
echo "  - Health Check: Every 30 minutes"
echo "  - Alert Monitor: Every 15 minutes"
echo "  - Maintenance: 3:30 AM daily"
echo "  - Log Cleanup: 4:00 AM daily"
echo ""

print_info "📝 Log Files:"
echo "  - Backup logs: /var/log/hki-automation/backup.log"
echo "  - Health check: /var/log/hki-automation/health-check.log"
echo "  - Alerts: /var/log/hki-automation/alerts.log"
echo "  - Maintenance: /var/log/hki-automation/maintenance.log"
echo "  - Notifications: /var/log/hki-automation/notifications.log"
echo ""

print_warning "⚠️  Next Steps:"
echo "  1. Update email address in send-notification.sh"
echo "  2. Update database credentials in backup.sh and alert-monitor.sh"
echo "  3. Test scripts manually: $SCRIPT_DIR/server-check.sh"
echo "  4. Configure mail server for notifications"
echo "  5. Set up monitoring dashboard (optional)"
echo ""

print_status "Setup completed! 🎉" 