#!/bin/bash

# Server Health Check Script untuk Sistem Pengajuan HKI
# Usage: ./server-check.sh

APP_DIR="/var/www/html/pengajuan-hki"
DB_NAME="db_hki"
DB_USER="hki_user"
DB_PASS="password_yang_kuat"

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

print_header() {
    echo -e "${BLUE}===== $1 =====${NC}"
}

print_ok() {
    echo -e "${GREEN}✓${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}⚠${NC} $1"
}

print_error() {
    echo -e "${RED}✗${NC} $1"
}

print_info() {
    echo -e "${BLUE}ℹ${NC} $1"
}

echo "🔍 System Health Check for Sistem Pengajuan HKI"
echo "================================================"
echo "Timestamp: $(date)"
echo ""

# System Information
print_header "SYSTEM INFORMATION"
print_info "Hostname: $(hostname)"
print_info "OS: $(lsb_release -d | cut -f2)"
print_info "Kernel: $(uname -r)"
print_info "Uptime: $(uptime -p)"
echo ""

# Disk Usage
print_header "DISK USAGE"
df -h | grep -E "(Filesystem|/dev/)" | grep -v tmpfs
echo ""

DISK_USAGE=$(df / | awk 'NR==2 {print $5}' | sed 's/%//')
if [ $DISK_USAGE -gt 80 ]; then
    print_warning "Disk usage is above 80%: ${DISK_USAGE}%"
elif [ $DISK_USAGE -gt 90 ]; then
    print_error "Disk usage is critically high: ${DISK_USAGE}%"
else
    print_ok "Disk usage is normal: ${DISK_USAGE}%"
fi
echo ""

# Memory Usage
print_header "MEMORY USAGE"
free -h
echo ""

MEM_USAGE=$(free | awk 'NR==2{printf "%.0f", $3*100/($3+$4)}')
if [ $MEM_USAGE -gt 80 ]; then
    print_warning "Memory usage is above 80%: ${MEM_USAGE}%"
elif [ $MEM_USAGE -gt 90 ]; then
    print_error "Memory usage is critically high: ${MEM_USAGE}%"
else
    print_ok "Memory usage is normal: ${MEM_USAGE}%"
fi
echo ""

# CPU Load
print_header "CPU LOAD"
LOAD_AVG=$(uptime | awk -F'load average:' '{print $2}' | awk '{print $1}' | sed 's/,//')
print_info "Load average: $(uptime | awk -F'load average:' '{print $2}')"

if (( $(echo "$LOAD_AVG > 2.0" | bc -l) )); then
    print_warning "High CPU load detected: $LOAD_AVG"
else
    print_ok "CPU load is normal: $LOAD_AVG"
fi
echo ""

# Service Status
print_header "SERVICE STATUS"

# Web Server
if systemctl is-active --quiet nginx; then
    print_ok "Nginx is running"
elif systemctl is-active --quiet apache2; then
    print_ok "Apache is running"
else
    print_error "Web server is not running"
fi

# Database
if systemctl is-active --quiet mysql; then
    print_ok "MySQL is running"
elif systemctl is-active --quiet mariadb; then
    print_ok "MariaDB is running"
else
    print_error "Database server is not running"
fi

# PHP-FPM
if systemctl is-active --quiet php8.1-fpm; then
    print_ok "PHP-FPM is running"
elif systemctl is-active --quiet php-fpm; then
    print_ok "PHP-FPM is running"
else
    print_warning "PHP-FPM status unknown"
fi
echo ""

# Application Health
print_header "APPLICATION HEALTH"

if [ -d "$APP_DIR" ]; then
    print_ok "Application directory exists"
    
    # Check if .env file exists
    if [ -f "$APP_DIR/.env" ]; then
        print_ok ".env file exists"
    else
        print_error ".env file is missing"
    fi
    
    # Check storage permissions
    if [ -w "$APP_DIR/storage" ]; then
        print_ok "Storage directory is writable"
    else
        print_error "Storage directory is not writable"
    fi
    
    # Check cache permissions  
    if [ -w "$APP_DIR/bootstrap/cache" ]; then
        print_ok "Cache directory is writable"
    else
        print_error "Cache directory is not writable"
    fi
    
else
    print_error "Application directory does not exist"
fi
echo ""

# Database Connection Test
print_header "DATABASE CONNECTION"
cd $APP_DIR
DB_TEST=$(php artisan tinker --execute="try { \DB::connection()->getPdo(); echo 'OK'; } catch(Exception \$e) { echo 'FAILED'; }" 2>/dev/null | tail -1)

if [ "$DB_TEST" = "OK" ]; then
    print_ok "Database connection successful"
else
    print_error "Database connection failed"
fi
echo ""

# HTTP Response Test
print_header "HTTP RESPONSE TEST"
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://localhost)
RESPONSE_TIME=$(curl -s -o /dev/null -w "%{time_total}" http://localhost)

if [ $HTTP_STATUS -eq 200 ]; then
    print_ok "HTTP response: $HTTP_STATUS (Response time: ${RESPONSE_TIME}s)"
else
    print_error "HTTP response: $HTTP_STATUS"
fi

# HTTPS Test (if applicable)
HTTPS_STATUS=$(curl -s -o /dev/null -w "%{http_code}" https://localhost 2>/dev/null || echo "000")
if [ $HTTPS_STATUS -eq 200 ]; then
    print_ok "HTTPS response: $HTTPS_STATUS"
elif [ $HTTPS_STATUS -eq "000" ]; then
    print_warning "HTTPS not configured or not accessible"
else
    print_warning "HTTPS response: $HTTPS_STATUS"
fi
echo ""

# Log File Sizes
print_header "LOG FILE ANALYSIS"
if [ -d "$APP_DIR/storage/logs" ]; then
    LOG_SIZE=$(du -sh $APP_DIR/storage/logs 2>/dev/null | cut -f1)
    print_info "Laravel logs size: $LOG_SIZE"
    
    # Check for recent errors
    ERROR_COUNT=$(grep -c "ERROR" $APP_DIR/storage/logs/laravel.log 2>/dev/null | tail -1 || echo "0")
    if [ $ERROR_COUNT -gt 0 ]; then
        print_warning "Found $ERROR_COUNT errors in Laravel log"
    else
        print_ok "No recent errors in Laravel log"
    fi
fi

# System logs
if [ -f "/var/log/nginx/error.log" ]; then
    NGINX_ERRORS=$(grep "$(date +'%Y/%m/%d')" /var/log/nginx/error.log | wc -l)
    if [ $NGINX_ERRORS -gt 0 ]; then
        print_warning "Found $NGINX_ERRORS Nginx errors today"
    else
        print_ok "No Nginx errors today"
    fi
fi
echo ""

# Security Checks
print_header "SECURITY CHECKS"

# Check for failed login attempts
AUTH_FAILURES=$(grep "authentication failure" /var/log/auth.log 2>/dev/null | grep "$(date +'%b %d')" | wc -l || echo "0")
if [ $AUTH_FAILURES -gt 10 ]; then
    print_warning "High number of authentication failures today: $AUTH_FAILURES"
else
    print_ok "Normal authentication activity: $AUTH_FAILURES failures today"
fi

# Check SSL certificate (if exists)
if [ -f "/etc/ssl/certs/your-certificate.crt" ]; then
    CERT_EXPIRY=$(openssl x509 -enddate -noout -in /etc/ssl/certs/your-certificate.crt | cut -d= -f2)
    CERT_EXPIRY_DATE=$(date -d "$CERT_EXPIRY" +%s)
    CURRENT_DATE=$(date +%s)
    DAYS_UNTIL_EXPIRY=$(( (CERT_EXPIRY_DATE - CURRENT_DATE) / 86400 ))
    
    if [ $DAYS_UNTIL_EXPIRY -lt 30 ]; then
        print_warning "SSL certificate expires in $DAYS_UNTIL_EXPIRY days"
    else
        print_ok "SSL certificate valid for $DAYS_UNTIL_EXPIRY days"
    fi
fi
echo ""

# Performance Metrics
print_header "PERFORMANCE METRICS"

# PHP processes
PHP_PROCESSES=$(ps aux | grep php-fpm | grep -v grep | wc -l)
print_info "Active PHP-FPM processes: $PHP_PROCESSES"

# Database connections
DB_CONNECTIONS=$(mysql -u $DB_USER -p$DB_PASS -e "SHOW STATUS LIKE 'Threads_connected';" | awk 'NR==2 {print $2}' 2>/dev/null || echo "N/A")
print_info "Database connections: $DB_CONNECTIONS"

# Application cache status
if [ -f "$APP_DIR/bootstrap/cache/config.php" ]; then
    print_ok "Configuration is cached"
else
    print_warning "Configuration is not cached"
fi

if [ -f "$APP_DIR/bootstrap/cache/routes-v7.php" ]; then
    print_ok "Routes are cached"
else
    print_warning "Routes are not cached"
fi
echo ""

# Summary
print_header "HEALTH CHECK SUMMARY"

TOTAL_CHECKS=0
PASSED_CHECKS=0

# Count this as a basic summary - in a real script you'd track each check
if [ $DISK_USAGE -lt 80 ] && [ $MEM_USAGE -lt 80 ] && [ $HTTP_STATUS -eq 200 ]; then
    print_ok "Overall system health: GOOD"
else
    print_warning "Overall system health: NEEDS ATTENTION"
fi

echo ""
echo "🔍 Health check completed at $(date)"
echo "📋 For detailed logs, check:"
echo "   - Application: $APP_DIR/storage/logs/laravel.log"
echo "   - Web server: /var/log/nginx/ or /var/log/apache2/"
echo "   - System: /var/log/syslog"
echo "" 