# 🐳 Docker Deployment Guide

Panduan lengkap untuk deployment aplikasi Laravel Perpus dengan Docker, CI/CD, dan Cloudflare Tunnel.

## 📋 Prasyarat

### 1. Server Requirements

- VPS/Server dengan minimal 2GB RAM
- Ubuntu 20.04 atau lebih baru
- Docker & Docker Compose terinstall
- Git terinstall
- Domain yang sudah diarahkan ke Cloudflare

### 2. Akun yang Diperlukan

- Akun GitHub (untuk repository dan CI/CD)
- Akun Cloudflare (untuk tunnel)
- Akses SSH ke server

## 🚀 Setup Awal

### 1. Install Docker di Server

```bash
# Update sistem
sudo apt update && sudo apt upgrade -y

# Install dependencies
sudo apt install apt-transport-https ca-certificates curl software-properties-common -y

# Tambah Docker GPG key
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg

# Tambah Docker repository
echo "deb [arch=amd64 signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

# Install Docker
sudo apt update
sudo apt install docker-ce docker-ce-cli containerd.io docker-compose-plugin -y

# Tambahkan user ke docker group
sudo usermod -aG docker $USER

# Verifikasi instalasi
docker --version
docker compose version
```

### 2. Setup Cloudflare Tunnel

```bash
# Install cloudflared
wget -q https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-amd64.deb
sudo dpkg -i cloudflared-linux-amd64.deb

# Login ke Cloudflare
cloudflared tunnel login

# Buat tunnel baru
cloudflared tunnel create perpus-app

# Catat Tunnel ID dan Token yang diberikan
```

**Konfigurasi Tunnel di Cloudflare Dashboard:**

1. Login ke [Cloudflare Zero Trust Dashboard](https://one.dash.cloudflare.com/)
2. Pergi ke **Access** → **Tunnels**
3. Klik tunnel yang sudah dibuat
4. Tambahkan Public Hostname:
    - **Subdomain**: `perpus` atau sesuai keinginan
    - **Domain**: pilih domain Anda
    - **Service**: `http://app:80` (nama service dari docker-compose)
5. Copy **Tunnel Token** untuk digunakan di `.env`

### 3. Clone Repository di Server

```bash
# Clone repository
cd /var/www
sudo git clone https://github.com/username/perpus-app.git
cd perpus-app

# Berikan permission
sudo chown -R $USER:$USER /var/www/perpus-app
```

### 4. Setup Environment Variables

```bash
# Copy file .env.example
cp .env.production.example .env

# Edit file .env
nano .env
```

**Konfigurasi penting yang harus diisi:**

```env
# Application
APP_NAME=Kirtya
APP_ENV=production
APP_DEBUG=false
APP_URL=https://perpus.yourdomain.com
APP_KEY=base64:GENERATE_WITH_php_artisan_key:generate

# Database
DB_DATABASE=perpus_production
DB_USERNAME=perpus_user
DB_PASSWORD=STRONG_PASSWORD_HERE
DB_ROOT_PASSWORD=ANOTHER_STRONG_PASSWORD

# Redis
REDIS_PASSWORD=REDIS_STRONG_PASSWORD

# Cloudflare Tunnel Token
CLOUDFLARE_TUNNEL_TOKEN=your_tunnel_token_from_cloudflare

# Session (production)
SESSION_SECURE_COOKIE=true
SESSION_DOMAIN=.yourdomain.com
```

**Generate APP_KEY:**

```bash
# Generate key menggunakan docker
docker run --rm -v $(pwd):/app -w /app composer:2 composer install --no-dev --optimize-autoloader
docker run --rm -v $(pwd):/app -w /app php:8.2-cli php artisan key:generate
```

### 5. Setup Directory Permissions

```bash
# Buat directory yang diperlukan
mkdir -p storage/framework/{cache,sessions,views}
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Set permissions
chmod -R 775 storage bootstrap/cache
```

## 🔧 Deployment Manual

### 1. Build dan Start Containers

```bash
# Build dan start semua services
docker compose up -d --build

# Cek status containers
docker compose ps

# Lihat logs
docker compose logs -f
```

### 2. Run Migrations dan Seeders

```bash
# Run migrations
docker compose exec app php artisan migrate --force

# Run seeders (jika ada)
docker compose exec app php artisan db:seed --force

# Optimize Laravel
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
```

### 3. Verifikasi Deployment

```bash
# Test health endpoint
curl http://localhost:8000/health

# Cek aplikasi via Cloudflare Tunnel
curl https://perpus.yourdomain.com/health

# Cek logs aplikasi
docker compose logs app

# Cek logs cloudflared
docker compose logs cloudflared
```

## 🤖 Setup CI/CD dengan GitHub Actions

### 1. Setup GitHub Repository Secrets

Pergi ke **Settings** → **Secrets and variables** → **Actions** di GitHub repository, lalu tambahkan secrets berikut:

| Secret Name       | Description              | Example                              |
| ----------------- | ------------------------ | ------------------------------------ |
| `SERVER_HOST`     | IP atau hostname server  | `192.168.1.100`                      |
| `SERVER_USER`     | Username SSH             | `ubuntu`                             |
| `SERVER_PORT`     | Port SSH (default 22)    | `22`                                 |
| `SSH_PRIVATE_KEY` | Private key untuk SSH    | `-----BEGIN RSA PRIVATE KEY-----...` |
| `DEPLOY_PATH`     | Path aplikasi di server  | `/var/www/perpus-app`                |
| `SLACK_WEBHOOK`   | Webhook Slack (optional) | `https://hooks.slack.com/...`        |

### 2. Generate SSH Key untuk CI/CD

**Di komputer lokal:**

```bash
# Generate SSH key pair
ssh-keygen -t rsa -b 4096 -C "github-actions" -f github-actions-key

# Copy public key
cat github-actions-key.pub
```

**Di server:**

```bash
# Tambahkan public key ke authorized_keys
nano ~/.ssh/authorized_keys
# Paste public key dari langkah sebelumnya
chmod 600 ~/.ssh/authorized_keys
```

**Di GitHub:**

1. Copy isi file `github-actions-key` (private key)
2. Tambahkan sebagai secret `SSH_PRIVATE_KEY` di GitHub

### 3. Enable GitHub Container Registry

**Di GitHub repository:**

1. Pergi ke **Settings** → **Actions** → **General**
2. Scroll ke **Workflow permissions**
3. Pilih **Read and write permissions**
4. Centang **Allow GitHub Actions to create and approve pull requests**
5. Save

### 4. Test CI/CD Pipeline

```bash
# Push ke branch main/master
git add .
git commit -m "Setup Docker deployment"
git push origin main

# Monitor workflow di GitHub Actions tab
```

## 📊 Monitoring & Maintenance

### 1. Monitoring Containers

```bash
# Cek status semua containers
docker compose ps

# Cek resource usage
docker stats

# Cek logs realtime
docker compose logs -f

# Cek logs spesifik service
docker compose logs -f app
docker compose logs -f cloudflared
```

### 2. Database Management

```bash
# Backup database
docker compose exec db mysqldump -u root -p${DB_ROOT_PASSWORD} ${DB_DATABASE} > backup_$(date +%Y%m%d_%H%M%S).sql

# Restore database
docker compose exec -T db mysql -u root -p${DB_ROOT_PASSWORD} ${DB_DATABASE} < backup.sql

# Access MySQL shell
docker compose exec db mysql -u root -p${DB_ROOT_PASSWORD}
```

### 3. Redis Management

```bash
# Access Redis CLI
docker compose exec redis redis-cli -a ${REDIS_PASSWORD}

# Flush cache
docker compose exec redis redis-cli -a ${REDIS_PASSWORD} FLUSHALL

# Check Redis info
docker compose exec redis redis-cli -a ${REDIS_PASSWORD} INFO
```

### 4. Queue Workers

```bash
# Restart queue workers
docker compose restart queue

# Check queue status
docker compose exec app php artisan queue:work --once

# Check failed jobs
docker compose exec app php artisan queue:failed
```

## 🔄 Update & Rollback

### Update Aplikasi

```bash
# Pull latest code
git pull origin main

# Rebuild containers
docker compose up -d --build

# Run migrations
docker compose exec app php artisan migrate --force

# Clear cache
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache

# Restart queue
docker compose restart queue
```

### Rollback

```bash
# Rollback ke commit sebelumnya
git log --oneline
git reset --hard <commit-hash>

# Rebuild containers
docker compose up -d --build

# Rollback migrations (jika perlu)
docker compose exec app php artisan migrate:rollback --step=1
```

## 🛡️ Security Best Practices

### 1. Firewall Configuration

```bash
# Install UFW
sudo apt install ufw

# Allow SSH
sudo ufw allow 22/tcp

# Allow HTTP/HTTPS (jika perlu direct access)
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Enable firewall
sudo ufw enable

# Check status
sudo ufw status
```

### 2. Regular Updates

```bash
# Update sistem
sudo apt update && sudo apt upgrade -y

# Update Docker images
docker compose pull
docker compose up -d

# Clean old images
docker image prune -af
```

### 3. Backup Strategy

**Setup automatic backup script:**

```bash
# Create backup script
nano /home/$USER/backup-perpus.sh
```

```bash
#!/bin/bash
BACKUP_DIR="/home/$USER/backups/perpus"
DATE=$(date +%Y%m%d_%H%M%S)

mkdir -p $BACKUP_DIR

# Backup database
docker compose exec -T db mysqldump -u root -p${DB_ROOT_PASSWORD} ${DB_DATABASE} > $BACKUP_DIR/db_$DATE.sql

# Backup storage
tar -czf $BACKUP_DIR/storage_$DATE.tar.gz -C /var/www/perpus-app storage

# Keep only last 30 days
find $BACKUP_DIR -type f -mtime +30 -delete

echo "Backup completed: $DATE"
```

```bash
# Make executable
chmod +x /home/$USER/backup-perpus.sh

# Add to crontab (run daily at 2 AM)
crontab -e
# Add line:
0 2 * * * /home/$USER/backup-perpus.sh >> /var/log/perpus-backup.log 2>&1
```

## 🐛 Troubleshooting

### Container tidak start

```bash
# Cek logs detail
docker compose logs app

# Cek status health check
docker compose ps

# Restart specific service
docker compose restart app
```

### Database connection error

```bash
# Cek database container
docker compose logs db

# Test connection
docker compose exec app php artisan tinker
>>> DB::connection()->getPdo();

# Reset database container
docker compose down
docker volume rm perpus-app_mysql_data
docker compose up -d
```

### Cloudflare Tunnel tidak connect

```bash
# Cek logs cloudflared
docker compose logs cloudflared

# Verifikasi token
echo $CLOUDFLARE_TUNNEL_TOKEN

# Restart tunnel
docker compose restart cloudflared
```

### Permission errors

```bash
# Fix storage permissions
docker compose exec app chown -R www-data:www-data storage bootstrap/cache
docker compose exec app chmod -R 775 storage bootstrap/cache
```

## 📚 Useful Commands

```bash
# Stop all containers
docker compose down

# Stop and remove volumes
docker compose down -v

# Rebuild specific service
docker compose up -d --build app

# Execute artisan command
docker compose exec app php artisan [command]

# Access container shell
docker compose exec app sh

# View container resources
docker stats

# Clean up Docker system
docker system prune -af --volumes
```

## 📞 Support

Jika mengalami masalah:

1. Cek logs: `docker compose logs -f`
2. Cek dokumentasi Laravel: https://laravel.com/docs
3. Cek dokumentasi Cloudflare Tunnel: https://developers.cloudflare.com/cloudflare-one/connections/connect-apps/
4. Buka issue di repository GitHub

---

**Happy Deploying! 🚀**
