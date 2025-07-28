#!/bin/bash

# Deploy Script untuk Sistem Pengajuan HKI
# Usage: ./deploy.sh [production|staging]

set -e

ENVIRONMENT=${1:-production}
APP_DIR="/var/www/html/pengajuan-hki"
BACKUP_DIR="/backup"
DATE=$(date +%Y%m%d_%H%M%S)

echo "🚀 Starting deployment for $ENVIRONMENT environment..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

print_status() {
    echo -e "${GREEN}✓${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}⚠${NC} $1"
}

print_error() {
    echo -e "${RED}✗${NC} $1"
}

# Check if running as root
if [[ $EUID -eq 0 ]]; then
   print_error "This script should not be run as root for security reasons"
   exit 1
fi

# Create backup directory if it doesn't exist
sudo mkdir -p $BACKUP_DIR
sudo chown $USER:$USER $BACKUP_DIR

# Backup current application
if [ -d "$APP_DIR" ]; then
    print_status "Creating backup of current application..."
    sudo tar -czf $BACKUP_DIR/app_backup_$DATE.tar.gz -C $(dirname $APP_DIR) $(basename $APP_DIR)
    print_status "Backup created: $BACKUP_DIR/app_backup_$DATE.tar.gz"
fi

# Navigate to application directory
cd $APP_DIR

# Put application in maintenance mode
print_status "Putting application in maintenance mode..."
php artisan down --message="System update in progress. Please try again in a few minutes."

# Pull latest changes (if using git)
if [ -d ".git" ]; then
    print_status "Pulling latest changes from repository..."
    git pull origin main
fi

# Install/Update Composer dependencies
print_status "Installing/Updating Composer dependencies..."
composer install --optimize-autoloader --no-dev --no-interaction

# Clear old caches
print_status "Clearing application caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Install/Update NPM dependencies and build assets
if [ -f "package.json" ]; then
    print_status "Installing NPM dependencies and building assets..."
    npm ci --only=production
    npm run build
fi

# Run database migrations
print_status "Running database migrations..."
php artisan migrate --force

# Optimize application
print_status "Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer dump-autoload --optimize

# Create storage link if it doesn't exist
if [ ! -L "public/storage" ]; then
    print_status "Creating storage symbolic link..."
    php artisan storage:link
fi

# Set proper permissions
print_status "Setting proper file permissions..."
sudo chown -R www-data:www-data $APP_DIR
sudo chmod -R 755 $APP_DIR
sudo chmod -R 775 $APP_DIR/storage
sudo chmod -R 775 $APP_DIR/bootstrap/cache

# Restart queue workers (if using queues)
if pgrep -f "artisan queue:work" > /dev/null; then
    print_status "Restarting queue workers..."
    php artisan queue:restart
fi

# Bring application back online
print_status "Bringing application back online..."
php artisan up

# Test application
print_status "Testing application..."
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://localhost)
if [ $HTTP_STATUS -eq 200 ]; then
    print_status "Application is responding correctly!"
else
    print_error "Application test failed! HTTP Status: $HTTP_STATUS"
    print_warning "You may need to investigate the issue"
fi

# Clean up old backups (keep last 5)
print_status "Cleaning up old backups..."
ls -t $BACKUP_DIR/app_backup_*.tar.gz 2>/dev/null | tail -n +6 | xargs -r rm

print_status "Deployment completed successfully! 🎉"
print_warning "Please test the application thoroughly to ensure everything is working correctly."

echo ""
echo "📊 Deployment Summary:"
echo "Environment: $ENVIRONMENT"
echo "Timestamp: $(date)"
echo "Backup location: $BACKUP_DIR/app_backup_$DATE.tar.gz"
echo ""
echo "🔍 Useful commands for monitoring:"
echo "  - Check logs: tail -f $APP_DIR/storage/logs/laravel.log"
echo "  - Check queue status: php artisan queue:work --once"
echo "  - Monitor processes: top"
echo "" 