# ✅ Setup Checklist untuk Deployment Docker + CI/CD + Cloudflare Tunnel

Checklist lengkap untuk memastikan semua setup berjalan dengan baik.

## 📋 Pre-Deployment Checklist

### Server Preparation

- [ ] Server/VPS tersedia dengan minimal 2GB RAM
- [ ] Ubuntu 20.04+ terinstall
- [ ] Akses SSH ke server sudah dikonfigurasi
- [ ] Domain sudah terdaftar dan mengarah ke Cloudflare

### Software Installation

- [ ] Docker terinstall di server
- [ ] Docker Compose terinstall
- [ ] Git terinstall di server
- [ ] Cloudflared terinstall (untuk setup tunnel)

### Accounts & Access

- [ ] Akun GitHub sudah ada
- [ ] Repository GitHub sudah dibuat
- [ ] Akun Cloudflare sudah ada
- [ ] Domain sudah ditambahkan ke Cloudflare

## 🔧 Configuration Checklist

### 1. Repository Setup

- [ ] Clone repository ke server: `/var/www/perpus-app`
- [ ] Set proper ownership: `sudo chown -R $USER:$USER /var/www/perpus-app`
- [ ] Create `.env` dari `.env.production.example`

### 2. Environment Variables (.env)

- [ ] `APP_NAME` diisi dengan nama aplikasi
- [ ] `APP_ENV=production` (untuk production)
- [ ] `APP_DEBUG=false` (untuk production)
- [ ] `APP_URL` diisi dengan domain lengkap (https://yourdomain.com)
- [ ] `APP_KEY` di-generate dengan `php artisan key:generate`
- [ ] `DB_DATABASE` diisi dengan nama database
- [ ] `DB_USERNAME` diisi dengan username database
- [ ] `DB_PASSWORD` diisi dengan password database yang kuat
- [ ] `DB_ROOT_PASSWORD` diisi dengan root password yang kuat
- [ ] `REDIS_PASSWORD` diisi dengan password Redis yang kuat
- [ ] `CLOUDFLARE_TUNNEL_TOKEN` diisi dengan token dari Cloudflare
- [ ] `SESSION_SECURE_COOKIE=true` (untuk production HTTPS)
- [ ] `SESSION_DOMAIN` diisi dengan domain Anda

### 3. Cloudflare Tunnel Setup

- [ ] Login ke Cloudflare: `cloudflared tunnel login`
- [ ] Buat tunnel baru: `cloudflared tunnel create perpus-app`
- [ ] Catat Tunnel ID dan Token
- [ ] Konfigurasi Public Hostname di Cloudflare Dashboard
    - [ ] Subdomain: perpus (atau sesuai keinginan)
    - [ ] Domain: pilih domain Anda
    - [ ] Service: `http://app:80`
- [ ] Copy Tunnel Token ke `.env` sebagai `CLOUDFLARE_TUNNEL_TOKEN`

### 4. Directory Permissions

- [ ] Buat storage directories: `mkdir -p storage/framework/{cache,sessions,views}`
- [ ] Buat logs directory: `mkdir -p storage/logs`
- [ ] Buat bootstrap cache: `mkdir -p bootstrap/cache`
- [ ] Set permissions: `chmod -R 775 storage bootstrap/cache`

### 5. Docker Files

Verifikasi file-file berikut sudah ada:

- [ ] `Dockerfile`
- [ ] `docker-compose.yml`
- [ ] `.dockerignore`
- [ ] `docker/php/php.ini`
- [ ] `docker/php/opcache.ini`
- [ ] `docker/nginx/nginx.conf`
- [ ] `docker/nginx/default.conf`
- [ ] `docker/mysql/my.cnf`
- [ ] `docker/redis/redis.conf`
- [ ] `docker/supervisor/supervisord.conf`

## 🚀 Deployment Checklist

### Initial Deployment

- [ ] Build containers: `docker compose build`
- [ ] Start containers: `docker compose up -d`
- [ ] Verify containers running: `docker compose ps`
- [ ] Check logs: `docker compose logs -f`
- [ ] Run migrations: `docker compose exec app php artisan migrate --force`
- [ ] Run seeders (if any): `docker compose exec app php artisan db:seed --force`
- [ ] Cache config: `docker compose exec app php artisan config:cache`
- [ ] Test health endpoint: `curl http://localhost:8000/health`
- [ ] Test via Cloudflare: `curl https://yourdomain.com/health`

### Security Configuration

- [ ] Change all default passwords in `.env`
- [ ] Verify `APP_DEBUG=false` di production
- [ ] Verify `APP_ENV=production`
- [ ] Setup firewall (UFW):
    - [ ] `sudo ufw allow 22/tcp` (SSH)
    - [ ] `sudo ufw enable`
- [ ] Verify SSL certificate via Cloudflare
- [ ] Test security headers: `curl -I https://yourdomain.com`

## 🤖 CI/CD Setup Checklist

### GitHub Repository Settings

- [ ] Repository sudah public/private sesuai kebutuhan
- [ ] GitHub Actions enabled

### GitHub Secrets Configuration

Tambahkan secrets berikut di Settings → Secrets and variables → Actions:

- [ ] `SERVER_HOST` - IP/hostname server
- [ ] `SERVER_USER` - Username SSH
- [ ] `SERVER_PORT` - Port SSH (default: 22)
- [ ] `SSH_PRIVATE_KEY` - Private key untuk SSH
- [ ] `DEPLOY_PATH` - Path aplikasi di server (e.g., `/var/www/perpus-app`)
- [ ] `SLACK_WEBHOOK` - Webhook Slack (optional)

### SSH Key Setup

- [ ] Generate SSH key pair untuk GitHub Actions
- [ ] Add public key ke `~/.ssh/authorized_keys` di server
- [ ] Test SSH connection dari lokal
- [ ] Add private key sebagai `SSH_PRIVATE_KEY` secret di GitHub

### GitHub Actions Configuration

- [ ] File `.github/workflows/deploy.yml` sudah ada
- [ ] File `.github/workflows/ci.yml` sudah ada
- [ ] Workflow permissions set ke "Read and write permissions"
- [ ] "Allow GitHub Actions to create and approve pull requests" dicentang

### Container Registry Setup

- [ ] GitHub Container Registry (ghcr.io) enabled
- [ ] Test build Docker image locally
- [ ] Test push ke ghcr.io (akan otomatis via Actions)

### Test CI/CD Pipeline

- [ ] Push ke branch main/master
- [ ] Monitor workflow di GitHub Actions tab
- [ ] Verifikasi semua jobs (test, security, build, deploy) berhasil
- [ ] Cek aplikasi masih berjalan setelah auto-deploy

## 📊 Monitoring Setup Checklist

### Application Monitoring

- [ ] Setup health check monitoring (optional: UptimeRobot, Pingdom)
- [ ] Configure error logging
- [ ] Setup log rotation
- [ ] Configure Prometheus + Grafana (optional)

### Backup Strategy

- [ ] Create backup script (lihat dokumentasi)
- [ ] Setup automatic backups dengan crontab
- [ ] Test restore dari backup
- [ ] Verify backup retention policy (30 hari)

### Performance Monitoring

- [ ] Monitor container resources: `docker stats`
- [ ] Check disk usage: `df -h`
- [ ] Monitor logs: `docker compose logs -f`
- [ ] Setup alerts untuk resource usage tinggi (optional)

## 🧪 Testing Checklist

### Functional Testing

- [ ] Test login functionality
- [ ] Test main features aplikasi
- [ ] Test database operations
- [ ] Test file uploads (jika ada)
- [ ] Test email sending (jika dikonfigurasi)

### Performance Testing

- [ ] Test response time halaman utama
- [ ] Test database query performance
- [ ] Test dengan multiple concurrent users
- [ ] Verify caching berfungsi (Redis)

### Security Testing

- [ ] Test SQL injection protection
- [ ] Test XSS protection
- [ ] Test CSRF protection
- [ ] Verify HTTPS redirect
- [ ] Check security headers
- [ ] Test rate limiting

## 🔄 Post-Deployment Checklist

### Verification

- [ ] Website accessible via domain
- [ ] HTTPS berfungsi dengan baik
- [ ] Cloudflare Tunnel connected
- [ ] Database migrations completed
- [ ] All containers healthy
- [ ] Queue workers running
- [ ] Scheduler running

### Documentation

- [ ] Update README.md dengan info deployment
- [ ] Document any custom configurations
- [ ] Create runbook untuk common operations
- [ ] Share credentials dengan team (secure method)

### Communication

- [ ] Notify team deployment selesai
- [ ] Share access information (if needed)
- [ ] Schedule training session (if needed)

## 🔧 Maintenance Checklist

### Regular Maintenance (Weekly)

- [ ] Check container health: `docker compose ps`
- [ ] Review logs for errors: `docker compose logs`
- [ ] Check disk space: `df -h`
- [ ] Verify backups are running

### Regular Maintenance (Monthly)

- [ ] Update Docker images: `docker compose pull`
- [ ] Update system packages: `sudo apt update && sudo apt upgrade`
- [ ] Review and clean old Docker images: `docker image prune -af`
- [ ] Review backup files and cleanup old backups
- [ ] Check security advisories untuk dependencies

### Before Major Updates

- [ ] Create full backup
- [ ] Test updates di staging environment (jika ada)
- [ ] Review changelog
- [ ] Plan rollback strategy
- [ ] Notify users tentang maintenance window

## 📞 Emergency Contacts & Resources

### Documentation

- [ ] Laravel Docs: https://laravel.com/docs
- [ ] Docker Docs: https://docs.docker.com
- [ ] Cloudflare Docs: https://developers.cloudflare.com

### Support

- [ ] Team contact information documented
- [ ] Server access credentials secured
- [ ] Emergency rollback procedure documented

## ✅ Final Sign-off

- [ ] All checklist items completed
- [ ] Application running smoothly
- [ ] Monitoring in place
- [ ] Team notified
- [ ] Documentation updated

---

**Deployment Date:** ********\_********

**Deployed By:** ********\_********

**Verified By:** ********\_********

**Notes:**

```
[Add any deployment-specific notes here]
```

---

🎉 **Congratulations! Your application is now deployed!** 🎉
