# 🚀 Quick Start Deployment Guide

Panduan cepat untuk deployment aplikasi Laravel Perpus dengan Docker + CI/CD + Cloudflare Tunnel.

## 🎯 Quick Summary

Setup ini mencakup:

- ✅ Docker containerization (App, MySQL, Redis, Queue, Scheduler)
- ✅ Cloudflare Tunnel (secure access tanpa expose port)
- ✅ GitHub Actions CI/CD (automated testing & deployment)
- ✅ Production-ready configuration
- ✅ Auto-scaling queue workers
- ✅ Redis caching & session management
- ✅ Automated backups

## 📁 File Structure

```
perpus-app/
├── .github/
│   └── workflows/
│       ├── deploy.yml          # Deployment workflow
│       └── ci.yml              # CI/CD pipeline
├── docker/
│   ├── nginx/                  # Nginx configs
│   ├── php/                    # PHP configs
│   ├── mysql/                  # MySQL configs
│   ├── redis/                  # Redis configs
│   └── supervisor/             # Supervisor configs
├── Dockerfile                  # Multi-stage production Dockerfile
├── docker-compose.yml          # Development compose
├── docker-compose.prod.yml     # Production compose
├── .dockerignore              # Docker ignore file
├── .env.production.example     # Production env template
├── Makefile                    # Helper commands
├── deploy-script.sh            # Automated deployment script
├── DOCKER_DEPLOYMENT.md        # Full documentation
└── SETUP_CHECKLIST.md          # Complete checklist
```

## ⚡ Quick Start (5 Minutes)

### 1. Setup Cloudflare Tunnel (One-time)

```bash
# Install cloudflared
wget -q https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-amd64.deb
sudo dpkg -i cloudflared-linux-amd64.deb

# Login and create tunnel
cloudflared tunnel login
cloudflared tunnel create perpus-app

# Copy the tunnel token shown in output
```

**Configure in Cloudflare Dashboard:**

- Go to: https://one.dash.cloudflare.com/
- Navigate to: Access → Tunnels
- Select your tunnel → Configure
- Add Public Hostname:
    - Subdomain: `perpus`
    - Service: `http://app:80`
- Copy the Tunnel Token

### 2. Setup Server

```bash
# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh
sudo usermod -aG docker $USER

# Clone repository
cd /var/www
sudo git clone https://github.com/your-username/perpus-app.git
cd perpus-app
sudo chown -R $USER:$USER .

# Setup environment
cp .env.production.example .env
nano .env  # Fill in your values
```

**Important .env values:**

```env
APP_URL=https://perpus.yourdomain.com
APP_KEY=              # Will generate in next step
DB_PASSWORD=          # Strong password
DB_ROOT_PASSWORD=     # Strong password
REDIS_PASSWORD=       # Strong password
CLOUDFLARE_TUNNEL_TOKEN=  # From step 1
```

### 3. Deploy

```bash
# Generate app key
docker run --rm -v $(pwd):/app -w /app php:8.2-cli php artisan key:generate

# Deploy using Makefile
make install

# Or manually:
docker compose up -d --build
docker compose exec app php artisan migrate --force
docker compose exec app php artisan config:cache
```

### 4. Verify

```bash
# Check containers
docker compose ps

# Test health
curl http://localhost:8000/health

# Test via Cloudflare
curl https://perpus.yourdomain.com/health
```

✅ **Done! Your app is now live!**

## 🤖 Setup CI/CD (Optional but Recommended)

### 1. Generate SSH Key

```bash
# On your local machine
ssh-keygen -t rsa -b 4096 -C "github-actions" -f github-actions-key

# Copy public key
cat github-actions-key.pub
```

### 2. Add Public Key to Server

```bash
# On your server
nano ~/.ssh/authorized_keys
# Paste the public key and save
chmod 600 ~/.ssh/authorized_keys
```

### 3. Configure GitHub Secrets

Go to: GitHub Repository → Settings → Secrets and variables → Actions

Add these secrets:
| Secret | Value |
|--------|-------|
| `SERVER_HOST` | Your server IP |
| `SERVER_USER` | Your SSH username |
| `SSH_PRIVATE_KEY` | Content of `github-actions-key` |
| `DEPLOY_PATH` | `/var/www/perpus-app` |

### 4. Enable GitHub Actions

1. Go to: Settings → Actions → General
2. Set Workflow permissions to: **Read and write permissions**
3. Check: **Allow GitHub Actions to create and approve pull requests**
4. Save

### 5. Test CI/CD

```bash
git add .
git commit -m "Setup CI/CD"
git push origin main
```

Watch the workflow at: https://github.com/your-username/perpus-app/actions

## 🎨 Useful Commands (Makefile)

```bash
# View all commands
make help

# Start containers
make up

# Stop containers
make down

# View logs
make logs

# Run migrations
make migrate

# Clear cache
make cache-clear

# Optimize cache
make cache-optimize

# Backup database
make backup-db

# Deploy updates
make deploy

# Check status
make status
```

## 🔧 Common Operations

### Update Application

```bash
cd /var/www/perpus-app
./deploy-script.sh
```

Or manually:

```bash
git pull
docker compose pull
docker compose up -d --build
docker compose exec app php artisan migrate --force
docker compose exec app php artisan config:cache
```

### View Logs

```bash
# All logs
docker compose logs -f

# Specific service
docker compose logs -f app
docker compose logs -f cloudflared
```

### Backup Database

```bash
# Manual backup
docker compose exec -T db mysqldump -u root -p${DB_ROOT_PASSWORD} ${DB_DATABASE} > backup.sql

# Using Makefile
make backup-db
```

### Restore Database

```bash
docker compose exec -T db mysql -u root -p${DB_ROOT_PASSWORD} ${DB_DATABASE} < backup.sql
```

### Access Container Shell

```bash
# App container
docker compose exec app sh

# Database
docker compose exec db mysql -u root -p

# Redis
docker compose exec redis redis-cli
```

### Restart Specific Service

```bash
docker compose restart app
docker compose restart queue
docker compose restart cloudflared
```

## 🛡️ Security Checklist

- [x] Docker images from official sources
- [x] Non-root user in containers
- [x] Strong passwords in `.env`
- [x] `APP_DEBUG=false` in production
- [x] Firewall configured (UFW)
- [x] Cloudflare SSL/TLS encryption
- [x] Regular automated backups
- [x] Security headers configured
- [x] SQL injection protection
- [x] XSS protection enabled

## 🐛 Troubleshooting

### Container won't start

```bash
docker compose logs app
# Check for permission or configuration errors
```

### Database connection error

```bash
# Check database is running
docker compose ps db

# Test connection
docker compose exec app php artisan tinker
>>> DB::connection()->getPdo();
```

### Cloudflare Tunnel not connecting

```bash
# Check logs
docker compose logs cloudflared

# Verify token
echo $CLOUDFLARE_TUNNEL_TOKEN

# Restart tunnel
docker compose restart cloudflared
```

### Permission errors

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

## 📚 Full Documentation

- **Complete Guide**: [DOCKER_DEPLOYMENT.md](DOCKER_DEPLOYMENT.md)
- **Setup Checklist**: [SETUP_CHECKLIST.md](SETUP_CHECKLIST.md)
- **Laravel Docs**: https://laravel.com/docs
- **Docker Docs**: https://docs.docker.com
- **Cloudflare Docs**: https://developers.cloudflare.com/cloudflare-one/connections/connect-apps/

## 📞 Support

**Need help?**

- Check logs: `docker compose logs -f`
- Review checklist: `SETUP_CHECKLIST.md`
- Read full guide: `DOCKER_DEPLOYMENT.md`
- Open issue on GitHub

---

**Made with ❤️ for easy deployment**

Quick commands:

```bash
make help     # Show all commands
make up       # Start app
make logs     # View logs
make deploy   # Deploy updates
```
