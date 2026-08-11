#!/bin/bash

###############################################################################
# DEPLOYMENT SCRIPT UNTUK APLIKASI PERPUSTAKAAN
# Script ini untuk deploy aplikasi ke server production (Rocky Linux)
###############################################################################

set -e  # Exit on error

echo "=========================================="
echo "🚀 PERPUS APP DEPLOYMENT SCRIPT"
echo "=========================================="
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function untuk print colored text
print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_info() {
    echo -e "ℹ $1"
}

# Check if running as root
if [ "$EUID" -eq 0 ]; then
    print_error "Jangan run script ini sebagai root!"
    exit 1
fi

# Confirmation
echo "Script ini akan melakukan deployment aplikasi perpustakaan."
echo "Pastikan Anda sudah:"
echo "  1. Backup database"
echo "  2. Backup file aplikasi"
echo "  3. Update file .env dengan nilai production"
echo ""
read -p "Lanjutkan? (y/n) " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    print_warning "Deployment dibatalkan"
    exit 1
fi

echo ""
echo "=========================================="
echo "1. Checking Environment..."
echo "=========================================="

# Check .env file
if [ ! -f .env ]; then
    print_error "File .env tidak ditemukan!"
    print_info "Copy dari .env.example dan edit dengan nilai production"
    exit 1
fi

# Check APP_ENV
APP_ENV=$(grep "^APP_ENV=" .env | cut -d '=' -f2)
if [ "$APP_ENV" != "production" ]; then
    print_warning "APP_ENV bukan production ($APP_ENV)"
    read -p "Lanjutkan? (y/n) " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        exit 1
    fi
fi

# Check APP_DEBUG
APP_DEBUG=$(grep "^APP_DEBUG=" .env | cut -d '=' -f2)
if [ "$APP_DEBUG" == "true" ]; then
    print_error "APP_DEBUG masih true! Harus false untuk production"
    exit 1
fi

# Check APP_KEY
APP_KEY=$(grep "^APP_KEY=" .env | cut -d '=' -f2)
if [ -z "$APP_KEY" ]; then
    print_error "APP_KEY belum di-generate!"
    print_info "Jalankan: php artisan key:generate"
    exit 1
fi

print_success "Environment check passed"

echo ""
echo "=========================================="
echo "2. Enabling Maintenance Mode..."
echo "=========================================="

php artisan down --render="errors::503" --retry=60 || true
print_success "Aplikasi dalam maintenance mode"

echo ""
echo "=========================================="
echo "3. Pulling Latest Changes..."
echo "=========================================="

if [ -d .git ]; then
    git pull origin main || git pull origin master
    print_success "Git pull completed"
else
    print_warning "Bukan git repository, skip git pull"
fi

echo ""
echo "=========================================="
echo "4. Installing Dependencies..."
echo "=========================================="

print_info "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction
print_success "Composer dependencies installed"

print_info "Installing NPM dependencies..."
npm ci --production
print_success "NPM dependencies installed"

echo ""
echo "=========================================="
echo "5. Building Frontend Assets..."
echo "=========================================="

npm run build
print_success "Assets compiled"

echo ""
echo "=========================================="
echo "6. Running Database Migrations..."
echo "=========================================="

read -p "Jalankan migrations? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    php artisan migrate --force
    print_success "Database migrations completed"
else
    print_warning "Migrations skipped"
fi

echo ""
echo "=========================================="
echo "7. Clearing & Caching..."
echo "=========================================="

# Clear caches
print_info "Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
print_success "Caches cleared"

# Optimize
print_info "Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer dump-autoload --optimize
print_success "Application optimized"

echo ""
echo "=========================================="
echo "8. Setting Permissions..."
echo "=========================================="

# Set proper permissions
chmod -R 755 storage bootstrap/cache
print_success "Permissions set"

echo ""
echo "=========================================="
echo "9. Restarting Services..."
echo "=========================================="

# Restart PHP-FPM
if systemctl is-active --quiet php-fpm; then
    sudo systemctl restart php-fpm
    print_success "PHP-FPM restarted"
fi

# Restart Nginx
if systemctl is-active --quiet nginx; then
    sudo systemctl reload nginx
    print_success "Nginx reloaded"
fi

# Restart Redis
if systemctl is-active --quiet redis; then
    sudo systemctl restart redis
    print_success "Redis restarted"
fi

echo ""
echo "=========================================="
echo "10. Running Security Check..."
echo "=========================================="

# Check file permissions
print_info "Checking .env permissions..."
ENV_PERM=$(stat -c %a .env)
if [ "$ENV_PERM" != "600" ]; then
    chmod 600 .env
    print_warning ".env permissions updated to 600"
fi

# Check sensitive files not in git
print_info "Checking .gitignore..."
if git check-ignore -q .env; then
    print_success ".env is ignored by git"
else
    print_warning ".env is NOT ignored by git!"
fi

echo ""
echo "=========================================="
echo "11. Disabling Maintenance Mode..."
echo "=========================================="

php artisan up
print_success "Application is now live!"

echo ""
echo "=========================================="
echo "✅ DEPLOYMENT COMPLETED!"
echo "=========================================="
echo ""
print_success "Aplikasi berhasil di-deploy!"
echo ""
echo "Langkah selanjutnya:"
echo "  1. Test aplikasi di browser"
echo "  2. Check logs: tail -f storage/logs/laravel.log"
echo "  3. Monitor nginx: tail -f /var/log/nginx/perpus_error.log"
echo "  4. Check security headers dengan browser dev tools"
echo ""
print_info "Jika ada masalah, rollback dengan:"
echo "  git reset --hard HEAD~1"
echo "  php artisan migrate:rollback"
echo ""
