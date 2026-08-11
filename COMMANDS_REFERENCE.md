# 🎯 Quick Commands Reference

Reference cepat untuk perintah-perintah yang sering digunakan.

---

## 🐳 Docker Compose Commands

### Container Management

```bash
# Start all containers
docker compose up -d

# Start with build
docker compose up -d --build

# Stop all containers
docker compose down

# Stop and remove volumes
docker compose down -v

# Restart all containers
docker compose restart

# Restart specific service
docker compose restart app
docker compose restart queue
docker compose restart cloudflared
```

### Status & Monitoring

```bash
# Show container status
docker compose ps

# Show container logs (all)
docker compose logs -f

# Show logs for specific service
docker compose logs -f app
docker compose logs -f db
docker compose logs -f queue
docker compose logs -f cloudflared

# Show resource usage
docker stats

# Show container processes
docker compose top
```

### Container Access

```bash
# Access app container shell
docker compose exec app sh

# Access database shell
docker compose exec db mysql -u root -p

# Access Redis CLI
docker compose exec redis redis-cli -a ${REDIS_PASSWORD}

# Run artisan command
docker compose exec app php artisan [command]

# Run composer command
docker compose exec app composer [command]
```

---

## 🔧 Laravel Artisan Commands

### Database

```bash
# Run migrations
docker compose exec app php artisan migrate

# Run migrations (force in production)
docker compose exec app php artisan migrate --force

# Rollback migrations
docker compose exec app php artisan migrate:rollback

# Fresh migration (WARNING: deletes all data)
docker compose exec app php artisan migrate:fresh

# Run seeders
docker compose exec app php artisan db:seed

# Check database connection
docker compose exec app php artisan tinker
>>> DB::connection()->getPdo();
```

### Cache Management

```bash
# Clear all caches
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:clear
docker compose exec app php artisan route:clear
docker compose exec app php artisan view:clear

# Optimize for production
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
docker compose exec app php artisan optimize

# Clear compiled files
docker compose exec app php artisan clear-compiled
```

### Queue Management

```bash
# Work queue jobs
docker compose exec app php artisan queue:work

# Listen to queue
docker compose exec app php artisan queue:listen

# Show failed jobs
docker compose exec app php artisan queue:failed

# Retry failed job
docker compose exec app php artisan queue:retry [id]

# Retry all failed jobs
docker compose exec app php artisan queue:retry all

# Flush failed jobs
docker compose exec app php artisan queue:flush
```

### Maintenance

```bash
# Put app in maintenance mode
docker compose exec app php artisan down

# Take app out of maintenance mode
docker compose exec app php artisan up

# Generate app key
docker compose exec app php artisan key:generate

# Create symbolic link for storage
docker compose exec app php artisan storage:link
```

---

## 💾 Database Operations

### Backup

```bash
# Manual backup
docker compose exec -T db mysqldump -u root -p${DB_ROOT_PASSWORD} ${DB_DATABASE} > backup_$(date +%Y%m%d_%H%M%S).sql

# Backup specific table
docker compose exec -T db mysqldump -u root -p${DB_ROOT_PASSWORD} ${DB_DATABASE} [table_name] > table_backup.sql

# Backup with gzip
docker compose exec -T db mysqldump -u root -p${DB_ROOT_PASSWORD} ${DB_DATABASE} | gzip > backup.sql.gz
```

### Restore

```bash
# Restore from backup
docker compose exec -T db mysql -u root -p${DB_ROOT_PASSWORD} ${DB_DATABASE} < backup.sql

# Restore from gzip
gunzip < backup.sql.gz | docker compose exec -T db mysql -u root -p${DB_ROOT_PASSWORD} ${DB_DATABASE}
```

### Database Shell

```bash
# Access MySQL shell
docker compose exec db mysql -u root -p${DB_ROOT_PASSWORD}

# Show databases
docker compose exec db mysql -u root -p${DB_ROOT_PASSWORD} -e "SHOW DATABASES;"

# Show tables
docker compose exec db mysql -u root -p${DB_ROOT_PASSWORD} ${DB_DATABASE} -e "SHOW TABLES;"
```

---

## 🔴 Redis Commands

### Access Redis

```bash
# Access Redis CLI
docker compose exec redis redis-cli -a ${REDIS_PASSWORD}

# Test connection
docker compose exec redis redis-cli -a ${REDIS_PASSWORD} ping
```

### Redis Operations

```bash
# Inside Redis CLI:

# Show all keys
KEYS *

# Get key value
GET key_name

# Set key value
SET key_name value

# Delete key
DEL key_name

# Flush all data
FLUSHALL

# Show database info
INFO

# Show memory usage
INFO MEMORY

# Monitor commands
MONITOR
```

---

## 🛠️ Makefile Commands

### Container Management

```bash
make up              # Start containers
make down            # Stop containers
make restart         # Restart containers
make ps              # Show status
make build           # Build containers
```

### Logs & Shell

```bash
make logs            # View all logs
make logs-app        # View app logs
make logs-db         # View database logs
make logs-queue      # View queue logs
make shell           # Access app shell
make shell-db        # Access database shell
make shell-redis     # Access Redis CLI
```

### Development

```bash
make install         # Initial setup
make migrate         # Run migrations
make migrate-fresh   # Fresh migration
make seed            # Run seeders
make test            # Run tests
```

### Cache

```bash
make cache-clear     # Clear all caches
make cache-optimize  # Optimize caches
```

### Maintenance

```bash
make backup-db       # Backup database
make restore-db      # Restore database
make queue-restart   # Restart queue workers
make clean           # Clean Docker resources
make deploy          # Deploy updates
make status          # Show comprehensive status
make health          # Check app health
```

### Custom Commands

```bash
make composer CMD="install"           # Run composer command
make artisan CMD="route:list"         # Run artisan command
make npm CMD="install"                # Run npm command
```

---

## 🚀 Deployment Commands

### Initial Deployment

```bash
# Clone repository
git clone https://github.com/username/perpus-app.git
cd perpus-app

# Setup environment
cp .env.production.example .env
nano .env

# Deploy
make install
```

### Update Deployment

```bash
# Using deploy script
./deploy-script.sh

# Or manually
git pull origin main
docker compose pull
docker compose up -d --build
docker compose exec app php artisan migrate --force
docker compose exec app php artisan config:cache
docker compose restart queue
```

### Rollback

```bash
# View commit history
git log --oneline

# Rollback to specific commit
git reset --hard [commit-hash]

# Rebuild
docker compose up -d --build

# Rollback migrations (if needed)
docker compose exec app php artisan migrate:rollback --step=1
```

---

## 🔥 Emergency Commands

### Quick Restart

```bash
docker compose restart app
```

### Clear Everything

```bash
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:clear
docker compose exec app php artisan route:clear
docker compose exec app php artisan view:clear
docker compose restart app
```

### Fix Permissions

```bash
docker compose exec app chown -R www-data:www-data storage bootstrap/cache
docker compose exec app chmod -R 775 storage bootstrap/cache
```

### Force Rebuild

```bash
docker compose down -v
docker compose up -d --build --force-recreate
```

### View Errors

```bash
# Application logs
docker compose logs -f app

# Laravel logs
docker compose exec app tail -f storage/logs/laravel.log

# Nginx error logs
docker compose exec app tail -f /var/log/nginx/error.log

# PHP errors
docker compose exec app tail -f /var/log/php/error.log
```

---

## 📊 Monitoring Commands

### Health Checks

```bash
# Check app health endpoint
curl http://localhost:8000/health

# Check via domain
curl https://perpus.yourdomain.com/health

# Check database
docker compose exec app php artisan tinker
>>> DB::connection()->getPdo();

# Check Redis
docker compose exec redis redis-cli -a ${REDIS_PASSWORD} ping
```

### Resource Monitoring

```bash
# Container stats
docker stats

# Disk usage
docker system df

# Show images
docker images

# Show volumes
docker volume ls

# Show networks
docker network ls
```

### Cleanup

```bash
# Remove unused images
docker image prune -af

# Remove unused volumes
docker volume prune -f

# Remove unused networks
docker network prune -f

# Full system cleanup
docker system prune -af --volumes
```

---

## 🔐 Security Commands

### Check Security

```bash
# Check security headers
curl -I https://perpus.yourdomain.com

# Test HTTPS
curl -k -v https://perpus.yourdomain.com

# Check SSL certificate
echo | openssl s_client -connect perpus.yourdomain.com:443 2>/dev/null | openssl x509 -noout -dates
```

### Update Dependencies

```bash
# Update composer dependencies
docker compose exec app composer update

# Check for security vulnerabilities
docker compose exec app composer audit

# Update npm packages
docker compose exec app npm update

# Check npm vulnerabilities
docker compose exec app npm audit
```

---

## 🎯 One-Liners

### Quick Status Check

```bash
docker compose ps && docker stats --no-stream
```

### Full Restart

```bash
docker compose down && docker compose up -d && docker compose logs -f
```

### Fresh Start

```bash
docker compose down -v && docker compose up -d --build && docker compose exec app php artisan migrate --fresh --seed
```

### Quick Backup

```bash
docker compose exec -T db mysqldump -u root -p${DB_ROOT_PASSWORD} ${DB_DATABASE} | gzip > backup_$(date +%Y%m%d_%H%M%S).sql.gz
```

### Full Cache Clear & Optimize

```bash
docker compose exec app php artisan cache:clear && docker compose exec app php artisan config:cache && docker compose exec app php artisan route:cache && docker compose exec app php artisan view:cache
```

### Monitor All Logs

```bash
docker compose logs -f --tail=100
```

---

## 📝 Notes

- Ganti `${DB_ROOT_PASSWORD}`, `${DB_DATABASE}`, `${REDIS_PASSWORD}` dengan nilai dari `.env` Anda
- Gunakan `-T` flag untuk non-interactive commands (piping)
- Gunakan `-f` untuk follow/tail logs
- Gunakan `-d` untuk detached mode (background)
- Untuk Windows, beberapa command mungkin perlu disesuaikan

---

**Save this file for quick reference! 📌**
