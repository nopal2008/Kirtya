# 🐳 Docker Quick Start

## Local Development

### 1. Setup

```bash
# Copy environment file
cp .env.docker.example .env

# Edit if needed
nano .env
```

### 2. Start

```bash
# Build and start
docker compose up -d --build

# Run migrations
docker compose exec app php artisan migrate

# Create admin user (optional)
docker compose exec app php artisan db:seed
```

### 3. Access

- Application: http://localhost:8000
- Health check: http://localhost:8000/health

### 4. Common Commands

```bash
# Stop
docker compose down

# View logs
docker compose logs -f

# Shell access
docker compose exec app sh

# Run artisan commands
docker compose exec app php artisan [command]

# Database shell
docker compose exec db mysql -u root -p

# Redis CLI
docker compose exec redis redis-cli
```

---

## Production Deployment

### Prerequisites

- Server dengan Docker installed
- Domain di Cloudflare
- GitHub repository

### Quick Deploy

```bash
# 1. Clone repo
git clone https://github.com/your-username/perpus-app.git
cd perpus-app

# 2. Setup environment
cp .env.production.example .env
nano .env  # Fill in your values

# 3. Deploy
make install
```

### Setup Cloudflare Tunnel

```bash
# Install and login
wget -q https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-amd64.deb
sudo dpkg -i cloudflared-linux-amd64.deb
cloudflared tunnel login

# Create tunnel
cloudflared tunnel create perpus-app

# Get token from Cloudflare Dashboard:
# https://one.dash.cloudflare.com/ → Access → Tunnels
# Add token to .env as CLOUDFLARE_TUNNEL_TOKEN
```

---

## Makefile Commands

```bash
make help            # Show all commands
make up              # Start containers
make down            # Stop containers
make restart         # Restart containers
make logs            # View all logs
make shell           # Access app shell
make migrate         # Run migrations
make cache-clear     # Clear cache
make cache-optimize  # Optimize cache
make test            # Run tests
make backup-db       # Backup database
make deploy          # Full deployment
make status          # Show status
```

---

## File Structure

```
perpus-app/
├── Dockerfile                 # Production image
├── docker-compose.yml         # Docker Compose config
├── .dockerignore             # Docker ignore patterns
├── Makefile                   # Helper commands
│
├── docker/                    # Docker configurations
│   ├── nginx/                # Nginx configs
│   ├── php/                  # PHP configs
│   ├── mysql/                # MySQL configs
│   ├── redis/                # Redis configs
│   └── supervisor/           # Process manager
│
├── .github/workflows/        # CI/CD pipelines
│   ├── deploy.yml           # Production deployment
│   └── ci.yml               # Tests and checks
│
└── Documentation:
    ├── README_DEPLOYMENT.md   # Quick guide
    ├── DOCKER_DEPLOYMENT.md   # Complete guide
    └── SETUP_CHECKLIST.md     # Full checklist
```

---

## Environment Files

- `.env.example` - XAMPP/Local development
- `.env.docker.example` - Docker local development
- `.env.production.example` - Production deployment

---

## Troubleshooting

### Container not starting

```bash
docker compose logs app
docker compose ps
```

### Database connection failed

```bash
docker compose exec app php artisan tinker
>>> DB::connection()->getPdo();
```

### Permission errors

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Reset everything

```bash
docker compose down -v
docker compose up -d --build
docker compose exec app php artisan migrate --fresh --seed
```

---

## Resources

📚 **Full Documentation:**

- [README_DEPLOYMENT.md](README_DEPLOYMENT.md) - Quick start guide
- [DOCKER_DEPLOYMENT.md](DOCKER_DEPLOYMENT.md) - Complete deployment guide
- [SETUP_CHECKLIST.md](SETUP_CHECKLIST.md) - Step-by-step checklist

🔗 **External Links:**

- Laravel Docs: https://laravel.com/docs
- Docker Docs: https://docs.docker.com
- Cloudflare Tunnel: https://developers.cloudflare.com/cloudflare-one/

---

**Happy Coding! 🚀**
