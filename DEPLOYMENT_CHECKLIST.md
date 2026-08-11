# 📋 CHECKLIST DEPLOYMENT PRODUCTION

Gunakan checklist ini sebelum dan sesudah deployment ke server production.

## ⏰ SEBELUM DEPLOYMENT

### 🔐 Keamanan & Konfigurasi

- [ ] File `.env` sudah dibuat dan dikonfigurasi untuk production
- [ ] `APP_ENV=production` di `.env`
- [ ] `APP_DEBUG=false` di `.env` (WAJIB!)
- [ ] `APP_KEY` sudah di-generate (`php artisan key:generate`)
- [ ] `APP_URL` sesuai dengan domain production (https://...)
- [ ] Database credentials sudah diupdate dan aman
- [ ] Redis password sudah di-set (jika pakai Redis)
- [ ] `SESSION_SECURE_COOKIE=true` di `.env`
- [ ] `SESSION_HTTP_ONLY=true` di `.env`
- [ ] `SECURITY_FORCE_HTTPS=true` di `.env`
- [ ] `SECURITY_SQL_INJECTION_PROTECTION=true` di `.env`
- [ ] `SECURITY_XSS_PROTECTION=true` di `.env`
- [ ] `LOG_LEVEL=error` di `.env`

### 📦 Dependencies & Assets

- [ ] `composer install --no-dev --optimize-autoloader` sudah dijalankan
- [ ] `npm install` dan `npm run build` sudah dijalankan
- [ ] Semua dependencies up-to-date

### 🗄️ Database

- [ ] Backup database production yang ada (jika update)
- [ ] Migration sudah ditest di staging
- [ ] Seeder untuk admin user sudah siap

### 📁 Files & Permissions

- [ ] File `.env` permission = 600 (`chmod 600 .env`)
- [ ] Directory `storage` writable = 775 (`chmod -R 775 storage`)
- [ ] Directory `bootstrap/cache` writable = 775 (`chmod -R 775 bootstrap/cache`)
- [ ] File `.env` ada di `.gitignore`
- [ ] File `database.sqlite` ada di `.gitignore`
- [ ] File backup dan dump (_.sql, _.bak) ada di `.gitignore`

### 🌐 Server & Network

- [ ] SSL certificate valid dan aktif (HTTPS)
- [ ] Domain sudah pointing ke server
- [ ] Firewall dikonfigurasi (allow HTTP/HTTPS, block lainnya)
- [ ] SSH key authentication aktif
- [ ] Root login disabled
- [ ] Nginx/Apache sudah dikonfigurasi
- [ ] PHP-FPM running dengan user non-root
- [ ] MySQL/PostgreSQL running dan secure

### 🔒 Security Headers

- [ ] Nginx config include security headers
- [ ] HSTS enabled
- [ ] CSP (Content Security Policy) configured
- [ ] X-Frame-Options set
- [ ] X-XSS-Protection set

---

## 🚀 SAAT DEPLOYMENT

### Jalankan Script Deployment

```bash
# Masuk ke directory aplikasi
cd /var/www/perpus-app

# Jalankan deployment script
bash deploy.sh
```

### Atau Manual:

1. **Maintenance Mode**

    ```bash
    php artisan down
    ```

2. **Pull Latest Code**

    ```bash
    git pull origin main
    ```

3. **Install Dependencies**

    ```bash
    composer install --no-dev --optimize-autoloader
    npm ci --production
    npm run build
    ```

4. **Run Migrations**

    ```bash
    php artisan migrate --force
    ```

5. **Clear & Cache**

    ```bash
    php artisan cache:clear
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    ```

6. **Restart Services**

    ```bash
    sudo systemctl restart php-fpm
    sudo systemctl reload nginx
    ```

7. **Go Live**
    ```bash
    php artisan up
    ```

---

## ✅ SETELAH DEPLOYMENT

### 🧪 Testing

- [ ] Homepage loading dengan benar
- [ ] Login berfungsi normal
- [ ] Logout berfungsi normal
- [ ] Dashboard loading tanpa error
- [ ] CRUD operations berfungsi (Create, Read, Update, Delete)
- [ ] Upload file berfungsi
- [ ] Pencarian berfungsi
- [ ] Pagination berfungsi
- [ ] Form validation berfungsi

### 🔐 Security Testing

- [ ] HTTPS aktif (kunci gembok di browser)
- [ ] HTTP redirect ke HTTPS
- [ ] CSRF protection berfungsi (test submit form tanpa token)
- [ ] Rate limiting berfungsi (test login berulang kali)
- [ ] SQL Injection protection (test dengan input: `' OR '1'='1`)
- [ ] XSS protection (test dengan input: `<script>alert('XSS')</script>`)
- [ ] Session timeout berfungsi (idle 120 menit)
- [ ] Password hashing berfungsi (check di database, tidak plaintext)

### 🔍 Security Headers Check

Buka browser Dev Tools (F12) → Network → pilih request → Headers

Check response headers:

- [ ] `Strict-Transport-Security` ada
- [ ] `X-Frame-Options: DENY` ada
- [ ] `X-Content-Type-Options: nosniff` ada
- [ ] `X-XSS-Protection: 1; mode=block` ada
- [ ] `Content-Security-Policy` ada
- [ ] `Referrer-Policy` ada
- [ ] `X-Powered-By` TIDAK ada (hidden)
- [ ] `Server` header TIDAK ada atau generic (hidden)

### 📊 Performance Check

- [ ] Response time halaman < 2 detik
- [ ] Assets (CSS/JS) ter-minify
- [ ] Images ter-optimize
- [ ] Gzip compression aktif
- [ ] Browser caching aktif

### 📝 Logs & Monitoring

- [ ] Check Laravel logs tidak ada error

    ```bash
    tail -f storage/logs/laravel.log
    ```

- [ ] Check Nginx error log

    ```bash
    tail -f /var/log/nginx/perpus_error.log
    ```

- [ ] Check PHP-FPM error log

    ```bash
    tail -f /var/log/php-fpm/www-error.log
    ```

- [ ] Check system resources (CPU, Memory, Disk)
    ```bash
    htop
    df -h
    ```

### 🔔 Monitoring Setup

- [ ] Cron jobs berjalan (Laravel scheduler)

    ```bash
    crontab -l
    ```

- [ ] Database backup otomatis setup
- [ ] Log rotation setup
- [ ] Disk space monitoring
- [ ] Uptime monitoring (optional: UptimeRobot, Pingdom)

### 🚨 Error Handling

- [ ] Test 404 page (akses URL yang tidak ada)
- [ ] Test 500 error (tidak menampilkan detail error)
- [ ] Test 403 forbidden page
- [ ] Database error tidak expose query

---

## 🐛 TROUBLESHOOTING

### Error: 500 Internal Server Error

**Kemungkinan Penyebab:**

1. File permission salah

    ```bash
    chmod -R 775 storage bootstrap/cache
    ```

2. `.env` file tidak ada atau salah

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

3. Cache issue

    ```bash
    php artisan cache:clear
    php artisan config:clear
    php artisan view:clear
    ```

4. Check logs
    ```bash
    tail -f storage/logs/laravel.log
    tail -f /var/log/nginx/error.log
    ```

### Error: CSRF Token Mismatch

**Solusi:**

1. Clear browser cookies
2. Check session configuration
3. Pastikan session driver berfungsi (file/redis/database)
4. Check storage/framework/sessions writable

### Error: Database Connection Failed

**Solusi:**

1. Check database credentials di `.env`
2. Check database service running
    ```bash
    sudo systemctl status mariadb
    ```
3. Test koneksi
    ```bash
    mysql -u username -p
    ```

### Error: Permission Denied

**Solusi:**

```bash
# Set owner
sudo chown -R nginx:nginx /var/www/perpus-app

# Set permission
sudo chmod -R 755 /var/www/perpus-app
sudo chmod -R 775 storage bootstrap/cache

# SELinux (jika aktif)
sudo restorecon -Rv /var/www/perpus-app
```

### Website Lambat

**Optimasi:**

1. Enable OPcache

    ```bash
    # Check di php.ini
    opcache.enable=1
    opcache.memory_consumption=256
    ```

2. Use Redis untuk cache & session

    ```env
    CACHE_DRIVER=redis
    SESSION_DRIVER=redis
    ```

3. Optimize database

    ```sql
    OPTIMIZE TABLE books, transactions, users;
    ```

4. Enable query cache
5. Use CDN untuk static assets

---

## 📞 KONTAK DARURAT

Jika terjadi masalah kritis:

1. **Rollback ke versi sebelumnya:**

    ```bash
    git reset --hard HEAD~1
    php artisan migrate:rollback
    composer install --no-dev
    php artisan config:cache
    ```

2. **Aktifkan maintenance mode:**

    ```bash
    php artisan down --message="Sedang dalam perbaikan"
    ```

3. **Restore database dari backup:**

    ```bash
    mysql -u username -p database_name < backup.sql
    ```

4. **Contact:**
    - Developer: [email/phone]
    - System Admin: [email/phone]
    - Database Admin: [email/phone]

---

## 📚 REFERENSI

- [SECURITY.md](SECURITY.md) - Panduan lengkap keamanan
- [Laravel Deployment](https://laravel.com/docs/deployment)
- [Nginx Config](https://nginx.org/en/docs/)
- [Rocky Linux Docs](https://docs.rockylinux.org/)

---

**Last Updated:** $(date +"%Y-%m-%d %H:%M:%S")
