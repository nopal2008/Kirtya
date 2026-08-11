#!/bin/bash

# ============================================
# Deployment Script for Laravel Docker App
# ============================================

set -e  # Exit on error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
APP_DIR="/var/www/perpus-app"
BACKUP_DIR="/home/$USER/backups/perpus"
LOG_FILE="/var/log/perpus-deploy.log"

# Functions
log() {
    echo -e "${BLUE}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1" | tee -a "$LOG_FILE"
}

error() {
    echo -e "${RED}[ERROR]${NC} $1" | tee -a "$LOG_FILE"
    exit 1
}

success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1" | tee -a "$LOG_FILE"
}

warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1" | tee -a "$LOG_FILE"
}

# Check if running as correct user
if [ "$EUID" -eq 0 ]; then
    error "Please do not run as root. Use regular user with docker permissions."
fi

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    error "Docker is not running. Please start Docker and try again."
fi

# Navigate to app directory
cd "$APP_DIR" || error "Cannot access application directory: $APP_DIR"

log "Starting deployment process..."

# Step 1: Create backup
log "Creating backup..."
mkdir -p "$BACKUP_DIR"
BACKUP_DATE=$(date +%Y%m%d_%H%M%S)

if docker compose exec -T db mysqladmin ping -h localhost -u root -p${DB_ROOT_PASSWORD} > /dev/null 2>&1; then
    docker compose exec -T db mysqldump -u root -p${DB_ROOT_PASSWORD} ${DB_DATABASE} > "$BACKUP_DIR/db_$BACKUP_DATE.sql"
    success "Database backup created: db_$BACKUP_DATE.sql"
else
    warning "Database backup skipped (database not accessible)"
fi

# Step 2: Pull latest code
log "Pulling latest code from Git..."
git fetch origin
CURRENT_BRANCH=$(git rev-parse --abbrev-ref HEAD)
git pull origin "$CURRENT_BRANCH" || error "Failed to pull latest code"
success "Code updated successfully"

# Step 3: Pull latest Docker images
log "Pulling latest Docker images..."
docker compose pull || warning "Failed to pull some images"

# Step 4: Stop containers gracefully
log "Stopping containers..."
docker compose down || error "Failed to stop containers"
success "Containers stopped"

# Step 5: Build and start containers
log "Building and starting containers..."
docker compose up -d --build || error "Failed to start containers"
success "Containers started"

# Step 6: Wait for containers to be healthy
log "Waiting for containers to be healthy..."
sleep 10

# Check app health
MAX_RETRIES=30
RETRY_COUNT=0
while [ $RETRY_COUNT -lt $MAX_RETRIES ]; do
    if curl -f http://localhost:8000/health > /dev/null 2>&1; then
        success "Application is healthy"
        break
    fi
    RETRY_COUNT=$((RETRY_COUNT + 1))
    log "Waiting for app to be ready... ($RETRY_COUNT/$MAX_RETRIES)"
    sleep 2
done

if [ $RETRY_COUNT -eq $MAX_RETRIES ]; then
    error "Application did not become healthy in time"
fi

# Step 7: Run migrations
log "Running database migrations..."
docker compose exec -T app php artisan migrate --force || error "Migration failed"
success "Migrations completed"

# Step 8: Clear and cache config
log "Optimizing application..."
docker compose exec -T app php artisan config:cache || warning "Config cache failed"
docker compose exec -T app php artisan route:cache || warning "Route cache failed"
docker compose exec -T app php artisan view:cache || warning "View cache failed"
success "Application optimized"

# Step 9: Restart queue workers
log "Restarting queue workers..."
docker compose restart queue || warning "Queue restart failed"
success "Queue workers restarted"

# Step 10: Clean up old Docker images
log "Cleaning up old Docker images..."
docker image prune -af > /dev/null 2>&1 || warning "Cleanup failed"
success "Cleanup completed"

# Step 11: Clean old backups (keep last 30 days)
log "Cleaning old backups..."
find "$BACKUP_DIR" -type f -mtime +30 -delete
success "Old backups cleaned"

# Final status check
log "Checking final status..."
docker compose ps

# Display deployment info
echo ""
echo "============================================"
success "Deployment completed successfully!"
echo "============================================"
echo "Deployment time: $(date)"
echo "Git branch: $CURRENT_BRANCH"
echo "Git commit: $(git rev-parse --short HEAD)"
echo "Backup location: $BACKUP_DIR/db_$BACKUP_DATE.sql"
echo "============================================"
echo ""

# Test application
log "Testing application..."
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/health)
if [ "$HTTP_STATUS" -eq 200 ]; then
    success "Application is responding correctly (HTTP $HTTP_STATUS)"
else
    warning "Application returned HTTP $HTTP_STATUS"
fi

log "Deployment process finished!"
