# 🔒 PANDUAN KEAMANAN APLIKASI PERPUSTAKAAN

Dokumen ini berisi panduan keamanan untuk deployment aplikasi perpustakaan ke server produksi.

## 📋 DAFTAR ISI

1. [Konfigurasi Environment Production](#konfigurasi-environment-production)
2. [Proteksi yang Diimplementasikan](#proteksi-yang-diimplementasikan)
3. [Checklist Sebelum Deploy](#checklist-sebelum-deploy)
4. [Konfigurasi Server Rocky Linux](#konfigurasi-server-rocky-linux)
5. [Monitoring & Logging](#monitoring--logging)
6. [Backup & Recovery](#backup--recovery)

---

## 🚀 KONFIGURASI ENVIRONMENT PRODUCTION

### 1. Setup File `.env` untuk Production

```bash
# Copy dari example
cp .env.example .env

# Generate APP_KEY
php artisan key:generate
```

### 2. Edit `.env` dengan Nilai Production

```env
APP_NAME="Perpus App"
APP_ENV=production
APP_DEBUG=false  # ⚠️ WAJIB false di production!
APP_URL=https://your-domain.com

# Security Settings
SECURITY_RATE_LIMIT_ENABLED=true
SECURITY_SQL_INJECTION_PROTECTION=true
SECURITY_XSS_PROTECTION=true
SECURITY_FORCE_HTTPS=true

# Logging - hanya error yang di-log
LOG_CHANNEL=daily
LOG_LEVEL=error
LOG_DAILY_DAYS=7

# Database - Gunakan MySQL/PostgreSQL untuk production
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=perpus_production
DB_USERNAME=perpus_user
DB_PASSWORD=STRONG_PASSWORD_HERE

# Session - Gunakan Redis atau Database untuk production
SESSION_DRIVER=redis
SESSION_LIFETIME=120

# Cache - Gunakan Redis untuk performance
CACHE_STORE=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=STRONG_REDIS_PASSWORD
REDIS_PORT=6379
```

---

## 🛡️ PROTEKSI YANG DIIMPLEMENTASIKAN

### ✅ 1. SQL Injection Protection

- **Middleware**: `SqlInjectionProtection`
- **Fitur**:
    - Deteksi pattern SQL berbahaya (SELECT, DROP, UNION, dll)
    - Logging otomatis untuk percobaan serangan
    - Block request yang mencurigakan
    - Recursive checking untuk array inputs

### ✅ 2. XSS (Cross-Site Scripting) Protection

- **Middleware**: `XssProtection`
- **Fitur**:
    - Sanitize semua input dari user
    - Strip tags berbahaya (script, iframe, object, dll)
    - HTML entities encoding
    - Logging untuk percobaan XSS

### ✅ 3. CSRF (Cross-Site Request Forgery) Protection

- **Built-in Laravel**: Token CSRF otomatis di semua form
- **Verifikasi**: Setiap POST/PUT/PATCH/DELETE request harus include CSRF token

### ✅ 4. Clickjacking Protection

- **Header**: `X-Frame-Options: DENY`
- **CSP**: `frame-ancestors 'none'`
- **Mencegah**: Website dimuat dalam iframe

### ✅ 5. Brute Force Protection

- **Rate Limiting**: Login dibatasi 5 percobaan per 15 menit
- **Throttling**: API dan web routes dibatasi request
- **Auto Block**: IP yang melanggar akan di-block sementara

### ✅ 6. Man-in-the-Middle (MitM) Protection

- **HTTPS**: Force HTTPS di production
- **HSTS**: Header `Strict-Transport-Security` aktif
- **Secure Cookies**: Cookie hanya dikirim via HTTPS

### ✅ 7. Information Disclosure Prevention

- **Error Handling**: Custom exception handler menyembunyikan detail error
- **Debug Mode**: `APP_DEBUG=false` di production
- **Server Headers**: Sembunyikan `X-Powered-By`, `Server` headers
- **Database Errors**: Error query tidak di-expose ke user

### ✅ 8. API Security

- **Input Validation**: Semua input di-validasi
- **Output Sanitization**: Response di-sanitize
- **CORS**: Configured untuk domain yang diizinkan
- **Rate Limiting**: Throttle untuk API endpoints

### ✅ 9. Session Security

- **Regenerate**: Session ID regenerate setelah login
- **Secure Flag**: Session cookie dengan secure & httpOnly flags
- **SameSite**: Cookie dengan SameSite policy
- **Timeout**: Session expire setelah inaktif

### ✅ 10. Database Security

- **Prepared Statements**: Eloquent ORM menggunakan prepared statements
- **Password Hashing**: Bcrypt dengan 12 rounds
- **Connection Encryption**: SSL/TLS untuk koneksi database
- **Least Privilege**: User database dengan permission minimal

---

## 📝 CHECKLIST SEBELUM DEPLOY

### 🔐 Keamanan

- [ ] `APP_DEBUG=false` di `.env`
- [ ] `APP_ENV=production` di `.env`
- [ ] APP_KEY sudah di-generate
- [ ] Database password kuat (min 16 karakter, mixed case, symbols)
- [ ] Redis password sudah di-set
- [ ] File `.env` tidak masuk ke git (sudah ada di `.gitignore`)
- [ ] File `database.sqlite` tidak masuk ke git
- [ ] File backup dan dump tidak masuk ke git
- [ ] HTTPS sudah aktif dengan SSL certificate valid
- [ ] Firewall server sudah dikonfigurasi
- [ ] SSH key authentication aktif, password auth disabled
- [ ] Rate limiting sudah ditest

### 🗂️ File & Permission

- [ ] Storage dan cache directories writable
    ```bash
    chmod -R 775 storage bootstrap/cache
    ```
- [ ] File .env permission 600
    ```bash
    chmod 600 .env
    ```
- [ ] Vendor directory sudah di-install
    ```bash
    composer install --no-dev --optimize-autoloader
    ```
- [ ] Asset sudah di-build
    ```bash
    npm run build
    ```

### ⚙️ Optimisasi

- [ ] Config cached
    ```bash
    php artisan config:cache
    ```
- [ ] Routes cached
    ```bash
    php artisan route:cache
    ```
- [ ] Views cached
    ```bash
    php artisan view:cache
    ```
- [ ] Autoloader optimized
    ```bash
    composer dump-autoload --optimize
    ```

### 🔄 Database

- [ ] Migration sudah dijalankan
    ```bash
    php artisan migrate --force
    ```
- [ ] Seeder untuk user admin sudah dijalankan
- [ ] Database backup otomatis sudah dikonfigurasi

---

## 🐧 KONFIGURASI SERVER ROCKY LINUX

### 1. Update System

```bash
sudo dnf update -y
```

### 2. Install PHP 8.2+ dan Extensions

```bash
# Add Remi repository
sudo dnf install -y epel-release
sudo dnf install -y https://rpms.remirepo.net/enterprise/remi-release-9.rpm

# Enable PHP 8.2
sudo dnf module reset php -y
sudo dnf module enable php:remi-8.2 -y

# Install PHP dan extensions
sudo dnf install -y php php-cli php-fpm php-mysqlnd php-pdo \
  php-mbstring php-xml php-gd php-curl php-zip php-opcache \
  php-redis php-bcmath php-intl php-soap php-tokenizer
```

### 3. Install Nginx

```bash
# Install Nginx
sudo dnf install -y nginx

# Start dan enable
sudo systemctl start nginx
sudo systemctl enable nginx

# Konfigurasi Nginx untuk Laravel
sudo nano /etc/nginx/conf.d/perpus.conf
```

**Konfigurasi Nginx** (`/etc/nginx/conf.d/perpus.conf`):

```nginx
server {
    listen 80;
    server_name your-domain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name your-domain.com;
    root /var/www/perpus-app/public;

    # SSL Configuration
    ssl_certificate /etc/ssl/certs/your-cert.crt;
    ssl_certificate_key /etc/ssl/private/your-key.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;

    # Security Headers (tambahan dari middleware Laravel)
    add_header X-Frame-Options "DENY" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains; preload" always;

    # Hide Nginx version
    server_tokens off;

    index index.php;

    charset utf-8;

    # Laravel public directory
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP-FPM
    location ~ \.php$ {
        fastcgi_pass unix:/run/php-fpm/www.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;

        # Security
        fastcgi_hide_header X-Powered-By;
    }

    # Deny access to sensitive files
    location ~ /\. {
        deny all;
    }

    location ~ /\.env {
        deny all;
    }

    location ~ /\.git {
        deny all;
    }

    # Logs
    access_log /var/log/nginx/perpus_access.log;
    error_log /var/log/nginx/perpus_error.log;
}
```

### 4. Install MySQL/MariaDB

```bash
# Install MariaDB
sudo dnf install -y mariadb-server mariadb

# Start dan enable
sudo systemctl start mariadb
sudo systemctl enable mariadb

# Secure installation
sudo mysql_secure_installation
```

**Setup Database**:

```sql
-- Login ke MySQL
sudo mysql -u root -p

-- Buat database dan user
CREATE DATABASE perpus_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'perpus_user'@'localhost' IDENTIFIED BY 'STRONG_PASSWORD_HERE';
GRANT ALL PRIVILEGES ON perpus_production.* TO 'perpus_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 5. Install Redis

```bash
# Install Redis
sudo dnf install -y redis

# Start dan enable
sudo systemctl start redis
sudo systemctl enable redis

# Konfigurasi password
sudo nano /etc/redis/redis.conf
# Tambahkan/edit: requirepass STRONG_REDIS_PASSWORD

# Restart Redis
sudo systemctl restart redis
```

### 6. Install Composer

```bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
sudo mv composer.phar /usr/local/bin/composer
php -r "unlink('composer-setup.php');"
```

### 7. Install Node.js & NPM

```bash
# Install Node.js 18.x
curl -fsSL https://rpm.nodesource.com/setup_18.x | sudo bash -
sudo dnf install -y nodejs
```

### 8. Setup Application Directory

```bash
# Buat directory
sudo mkdir -p /var/www
cd /var/www

# Clone/copy aplikasi
sudo git clone https://github.com/your-repo/perpus-app.git
# Atau upload via SCP/SFTP

cd perpus-app

# Set ownership ke nginx user
sudo chown -R nginx:nginx /var/www/perpus-app
sudo chmod -R 755 /var/www/perpus-app
sudo chmod -R 775 storage bootstrap/cache

# Install dependencies
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Setup .env
cp .env.example .env
nano .env  # Edit dengan nilai production

# Generate key
php artisan key:generate

# Run migrations
php artisan migrate --force

# Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 9. Configure Firewall

```bash
# Install firewalld
sudo dnf install -y firewalld
sudo systemctl start firewalld
sudo systemctl enable firewalld

# Allow HTTP dan HTTPS
sudo firewall-cmd --permanent --add-service=http
sudo firewall-cmd --permanent --add-service=https

# Allow SSH (pastikan dulu!)
sudo firewall-cmd --permanent --add-service=ssh

# Reload firewall
sudo firewall-cmd --reload

# Check status
sudo firewall-cmd --list-all
```

### 10. Configure SELinux (Jika aktif)

```bash
# Set SELinux context untuk Laravel
sudo semanage fcontext -a -t httpd_sys_rw_content_t "/var/www/perpus-app/storage(/.*)?"
sudo semanage fcontext -a -t httpd_sys_rw_content_t "/var/www/perpus-app/bootstrap/cache(/.*)?"
sudo restorecon -Rv /var/www/perpus-app

# Allow Nginx connect to network (untuk Redis, MySQL)
sudo setsebool -P httpd_can_network_connect 1
sudo setsebool -P httpd_can_network_connect_db 1
```

### 11. Setup SSL Certificate (Let's Encrypt)

```bash
# Install Certbot
sudo dnf install -y certbot python3-certbot-nginx

# Dapatkan certificate
sudo certbot --nginx -d your-domain.com

# Auto-renewal sudah otomatis via systemd timer
sudo systemctl list-timers | grep certbot
```

### 12. Setup Cron Jobs

```bash
# Edit crontab untuk user nginx atau aplikasi
sudo crontab -e -u nginx

# Tambahkan Laravel scheduler
* * * * * cd /var/www/perpus-app && php artisan schedule:run >> /dev/null 2>&1

# Backup database harian (jam 2 pagi)
0 2 * * * /usr/bin/mysqldump -u perpus_user -p'PASSWORD' perpus_production > /backup/perpus_$(date +\%Y\%m\%d).sql
```

---

## 📊 MONITORING & LOGGING

### 1. Application Logs

```bash
# Monitor logs
tail -f storage/logs/laravel.log

# Log rotation sudah otomatis (daily, 7 hari)
```

### 2. Nginx Logs

```bash
# Access log
tail -f /var/log/nginx/perpus_access.log

# Error log
tail -f /var/log/nginx/perpus_error.log
```

### 3. Audit Logging

Aplikasi sudah include audit logging untuk:

- Login/logout attempts
- Data modifications
- Admin actions
- Security events

Lihat di: `/admin/audit-logs`

### 4. Security Monitoring

Check log file untuk security events:

```bash
# Cek SQL injection attempts
grep "SQL Injection attempt" storage/logs/laravel.log

# Cek XSS attempts
grep "XSS attempt" storage/logs/laravel.log

# Cek failed login attempts
grep "Failed login" storage/logs/laravel.log
```

---

## 💾 BACKUP & RECOVERY

### 1. Database Backup (Manual)

```bash
# Backup database
mysqldump -u perpus_user -p perpus_production > backup_$(date +%Y%m%d_%H%M%S).sql

# Restore database
mysql -u perpus_user -p perpus_production < backup_file.sql
```

### 2. File Backup

```bash
# Backup aplikasi (tanpa vendor dan node_modules)
tar -czf perpus_app_$(date +%Y%m%d).tar.gz \
  --exclude='vendor' \
  --exclude='node_modules' \
  --exclude='storage/logs/*' \
  --exclude='storage/framework/cache/*' \
  /var/www/perpus-app
```

### 3. Automated Backup Script

Buat script `/usr/local/bin/backup-perpus.sh`:

```bash
#!/bin/bash
BACKUP_DIR="/backup/perpus"
DATE=$(date +%Y%m%d_%H%M%S)

# Buat directory jika belum ada
mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u perpus_user -p'PASSWORD' perpus_production | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Backup storage dan uploads
tar -czf $BACKUP_DIR/storage_$DATE.tar.gz /var/www/perpus-app/storage

# Hapus backup lama (>30 hari)
find $BACKUP_DIR -type f -mtime +30 -delete

echo "Backup completed: $DATE"
```

Jadwalkan via cron:

```bash
# Setiap hari jam 2 pagi
0 2 * * * /usr/local/bin/backup-perpus.sh >> /var/log/perpus-backup.log 2>&1
```

---

## 🚨 INCIDENT RESPONSE

### Jika Terjadi Serangan:

1. **Isolasi**: Putuskan koneksi jaringan sementara jika perlu
2. **Analisis Log**: Check semua log untuk pattern serangan
3. **Block IP**: Tambahkan IP attacker ke firewall
    ```bash
    sudo firewall-cmd --permanent --add-rich-rule="rule family='ipv4' source address='ATTACKER_IP' reject"
    sudo firewall-cmd --reload
    ```
4. **Change Credentials**: Ganti semua password dan keys
5. **Update**: Pastikan semua software up-to-date
6. **Restore**: Restore dari backup jika ada data corruption

### Kontak Darurat:

- Admin: [email/phone]
- Security Team: [email/phone]

---

## 📚 REFERENSI

- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [Laravel Security Best Practices](https://laravel.com/docs/security)
- [Rocky Linux Documentation](https://docs.rockylinux.org/)
- [Nginx Security](https://nginx.org/en/docs/http/ngx_http_ssl_module.html)

---

**Terakhir Diupdate**: $(date)
**Versi Dokumen**: 1.0
