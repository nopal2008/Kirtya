# ⚡ QUICK FIX - Common Issues

Solusi cepat untuk error yang sering terjadi.

---

## 🔴 Error: Class "Redis" not found

**Error Message:**

```
Class "Redis" not found
vendor\laravel\framework\src\Illuminate\Redis\Connectors\PhpRedisConnector.php:80
```

**Penyebab:** Aplikasi mencoba pakai Redis tapi ekstensi tidak terinstall (XAMPP tidak include Redis).

**✅ Solusi:**

```bash
# 1. Edit .env
CACHE_STORE=file           # Ubah dari redis/database ke file
SESSION_DRIVER=file        # Ubah dari redis ke file
QUEUE_CONNECTION=sync      # Ubah dari database/redis ke sync

# Comment Redis config (tambahkan # di depan):
# REDIS_CLIENT=phpredis
# REDIS_HOST=127.0.0.1
# REDIS_PASSWORD=null
# REDIS_PORT=6379

# 2. Clear cache
php artisan config:clear
php artisan cache:clear

# 3. Test lagi
```

**📝 File yang sudah diperbaiki:** `.env` dan `bootstrap/app.php`

---

## 🔴 Icons/Images Tidak Muncul

**Penyebab:** Assets (CSS, JS, icons) belum di-compile.

**✅ Solusi:**

```bash
# 1. Install dependencies
npm install

# 2. Build assets
npm run build

# 3. Refresh browser (Ctrl + F5)
```

**Atau untuk development:**

```bash
npm run dev
# Biarkan running, auto-reload saat edit file
```

---

## 🔴 Error: No application encryption key

**Error Message:**

```
No application encryption key has been specified.
```

**✅ Solusi:**

```bash
# Generate APP_KEY
php artisan key:generate

# Reload page
```

---

## 🔴 Error: SQLSTATE[HY000] [1045] Access denied

**Penyebab:** Database credentials salah.

**✅ Solusi:**

```bash
# 1. Edit .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=perpus_db      # Nama database Anda
DB_USERNAME=root           # Default XAMPP
DB_PASSWORD=               # Kosong untuk XAMPP default

# 2. Buat database jika belum ada
# Via phpMyAdmin: http://localhost/phpmyadmin
# Atau via command:
mysql -u root -p
CREATE DATABASE perpus_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

# 3. Run migrations
php artisan migrate
```

---

## 🔴 Error: 419 Page Expired (CSRF Token Mismatch)

**Penyebab:** CSRF token expired atau invalid.

**✅ Solusi:**

```bash
# 1. Clear cache
php artisan cache:clear
php artisan config:clear

# 2. Clear browser cache (Ctrl + Shift + Delete)

# 3. Reload page dan login lagi
```

---

## 🔴 Error: 500 Internal Server Error

**✅ Solusi:**

```bash
# 1. Check logs
# storage/logs/laravel.log

# 2. Clear all cache
php artisan optimize:clear

# 3. Fix permissions (Windows)
# Right-click folder storage -> Properties -> Security
# Give Full Control to your user

# 4. Check .env configuration
# Pastikan APP_KEY sudah di-generate
php artisan key:generate
```

---

## 🔴 Middleware Not Working

**Penyebab:** Cache atau routing issue.

**✅ Solusi:**

```bash
# Clear all cache
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear

# Regenerate autoload
composer dump-autoload
```

---

## 🔴 Error: Class 'App\...' not found

**✅ Solusi:**

```bash
# Regenerate autoload
composer dump-autoload -o

# Clear cache
php artisan optimize:clear
```

---

## 🔴 Storage Not Writable

**Error:** Permission denied di storage/logs

**✅ Solusi Windows:**

```bash
# Via GUI:
# 1. Right-click folder "storage"
# 2. Properties -> Security
# 3. Edit -> Add your user -> Full Control
# 4. Apply to subfolders

# Via Command (PowerShell as Admin):
icacls "C:\xampp\htdocs\perpus-app\storage" /grant Users:F /t
icacls "C:\xampp\htdocs\perpus-app\bootstrap\cache" /grant Users:F /t
```

---

## 🔴 XAMPP MySQL Won't Start

**Penyebab:** Port 3306 sudah digunakan.

**✅ Solusi:**

```bash
# 1. Check port
netstat -ano | findstr :3306

# 2. Kill process (PowerShell as Admin)
Stop-Process -Id <PID> -Force

# 3. Atau gunakan SQLite untuk development:
# Edit .env:
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

# Buat file database:
type nul > database\database.sqlite

# Run migrations:
php artisan migrate
```

---

## 🔴 npm run dev/build Gagal

**✅ Solusi:**

```bash
# 1. Clear npm cache
npm cache clean --force

# 2. Delete node_modules & package-lock.json
rmdir /s /q node_modules
del package-lock.json

# 3. Install ulang
npm install

# 4. Build
npm run build
```

---

## 🔴 Composer Install Gagal

**✅ Solusi:**

```bash
# 1. Clear composer cache
composer clear-cache

# 2. Update composer
composer self-update

# 3. Install dengan memory unlimited
php -d memory_limit=-1 composer.phar install

# Atau di php.ini:
# memory_limit = 512M
```

---

## 🔴 Error: Too Many Redirects

**Penyebab:** Force HTTPS aktif di local.

**✅ Solusi:**

```bash
# Edit .env
SECURITY_FORCE_HTTPS=false  # Untuk development

# Clear cache
php artisan config:clear
```

---

## 🔴 Session Data Hilang Terus

**✅ Solusi:**

```bash
# 1. Check session configuration di .env
SESSION_DRIVER=file
SESSION_LIFETIME=120

# 2. Clear sessions
php artisan session:clear
# Atau delete files di storage/framework/sessions

# 3. Regenerate key
php artisan key:generate

# 4. Clear browser cookies
```

---

## 🔴 SQL Injection/XSS Middleware Blocking Valid Input

**Penyebab:** False positive pada security middleware.

**✅ Solusi Sementara (Development Only):**

```bash
# Edit .env
SECURITY_SQL_INJECTION_PROTECTION=false
SECURITY_XSS_PROTECTION=false

# Clear cache
php artisan config:clear

# ⚠️ JANGAN disable di production!
# Review input pattern dan adjust middleware jika perlu
```

---

## 🔴 Vite Manifest Not Found

**Error:** Vite manifest not found at public/build/manifest.json

**✅ Solusi:**

```bash
# Build assets
npm run build

# Atau untuk development:
npm run dev
```

---

## 🚑 EMERGENCY: Clear Everything

Jika semua gagal, reset semua:

```bash
# 1. Clear all Laravel cache
php artisan optimize:clear

# 2. Clear config
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 3. Regenerate autoload
composer dump-autoload -o

# 4. Clear sessions
php artisan session:clear

# 5. Rebuild assets
npm run build

# 6. Restart XAMPP (Apache & MySQL)

# 7. Clear browser cache (Ctrl + Shift + Delete)

# 8. Test lagi
```

---

## 📞 MASIH BERMASALAH?

### 1. Check Logs:

```bash
# Laravel logs
type storage\logs\laravel.log

# Apache logs
type C:\xampp\apache\logs\error.log

# PHP logs
type C:\xampp\php\logs\php_error_log
```

### 2. Enable Debug Mode:

```bash
# Edit .env (development only!)
APP_DEBUG=true
LOG_LEVEL=debug
```

### 3. Check Documentation:

- `DEVELOPMENT_SETUP.md` - Setup guide
- `DEPLOYMENT_CHECKLIST.md` - Troubleshooting section
- `QUICK_SECURITY_GUIDE.md` - Security config

### 4. Test Configuration:

```bash
# Test database connection
php artisan tinker
>>> DB::connection()->getPdo();

# Test routes
php artisan route:list

# Test config
php artisan config:show
```

---

## 💡 TIPS MENCEGAH ERROR

### ✅ Best Practices:

1. **Selalu backup sebelum update**

    ```bash
    # Backup database
    mysqldump -u root -p perpus_db > backup.sql
    ```

2. **Update dependencies secara berkala**

    ```bash
    composer update
    npm update
    ```

3. **Clear cache setelah perubahan config**

    ```bash
    php artisan config:clear
    ```

4. **Test di development dulu**
    - Jangan langsung edit di production
    - Test semua fitur sebelum deploy

5. **Monitor logs**
    - Check `storage/logs/laravel.log` rutin
    - Review error patterns

---

## 🔗 QUICK COMMANDS

```bash
# Laravel
php artisan optimize:clear      # Clear semua cache
php artisan migrate:fresh       # Reset database
php artisan db:seed            # Seed data

# Composer
composer install               # Install dependencies
composer dump-autoload        # Regenerate autoload

# NPM
npm install                   # Install dependencies
npm run build                # Build assets
npm run dev                  # Dev mode dengan HMR

# Clear Cache Windows
ipconfig /flushdns           # DNS cache
```

---

**Simpan file ini untuk referensi cepat! 📌**

Last Updated: 2026-08-11
