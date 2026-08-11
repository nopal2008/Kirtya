# 🖥️ SETUP DEVELOPMENT (Windows/XAMPP)

Panduan setup aplikasi untuk development di Windows menggunakan XAMPP.

---

## ⚙️ PERBEDAAN DEVELOPMENT vs PRODUCTION

| Feature       | Development (XAMPP) | Production (Rocky Linux) |
| ------------- | ------------------- | ------------------------ |
| OS            | Windows             | Linux (Rocky)            |
| Web Server    | Apache (XAMPP)      | Nginx                    |
| PHP           | XAMPP built-in      | PHP-FPM                  |
| Database      | MySQL (XAMPP)       | MariaDB/MySQL            |
| Cache/Session | File-based          | Redis (optional)         |
| Queue         | Sync                | Database/Redis           |
| HTTPS         | Optional            | Required                 |
| Debug Mode    | Enabled             | Disabled                 |

---

## 📋 REQUIREMENTS

- XAMPP (PHP 8.2+, MySQL, Apache)
- Composer
- Node.js & NPM
- Git (optional)

---

## 🚀 SETUP DEVELOPMENT

### 1. Clone/Copy Aplikasi

```bash
# Jika menggunakan git
git clone https://github.com/your-repo/perpus-app.git
cd perpus-app

# Atau copy folder ke:
# C:\xampp\htdocs\perpus-app
```

### 2. Install Dependencies

```bash
# Install Composer dependencies
composer install

# Install NPM dependencies
npm install
```

### 3. Setup Environment

```bash
# Copy .env file
copy .env.example .env

# Generate APP_KEY
php artisan key:generate
```

### 4. Konfigurasi .env untuk Development

Edit file `.env`:

```env
APP_NAME=SIPerpus
APP_ENV=local
APP_DEBUG=true              # Boleh true untuk development
APP_URL=http://localhost/perpus-app/public

# Database - MySQL XAMPP
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=perpus_db
DB_USERNAME=root
DB_PASSWORD=                # Kosong untuk XAMPP default

# Session & Cache - File based (tanpa Redis)
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync

# Redis - Disabled (tidak tersedia di XAMPP)
# Uncomment jika Anda install Redis di Windows
# REDIS_CLIENT=phpredis
# REDIS_HOST=127.0.0.1
# REDIS_PASSWORD=null
# REDIS_PORT=6379

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=debug            # Debug untuk development

# Security - Relaxed untuk development
SECURITY_FORCE_HTTPS=false  # Tidak perlu HTTPS di local
```

### 5. Setup Database

```bash
# Buat database via phpMyAdmin atau command line
# Akses: http://localhost/phpmyadmin

# Atau via command line:
mysql -u root -p
CREATE DATABASE perpus_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

# Run migrations
php artisan migrate

# (Optional) Seed data
php artisan db:seed
```

### 6. Build Assets

```bash
# Development mode (with hot reload)
npm run dev

# Atau build untuk production
npm run build
```

### 7. Jalankan Aplikasi

**Pilihan 1: Menggunakan Laravel Built-in Server**

```bash
php artisan serve
# Akses: http://127.0.0.1:8000
```

**Pilihan 2: Menggunakan Apache XAMPP**

```
Akses: http://localhost/perpus-app/public
```

---

## 🔧 TROUBLESHOOTING

### Error: Redis Extension Not Found

**Penyebab:** Konfigurasi mencoba menggunakan Redis tapi ekstensi tidak terinstall.

**Solusi:**

```bash
# 1. Edit .env
# Ubah:
CACHE_STORE=file           # Bukan redis atau database
SESSION_DRIVER=file        # Bukan redis
QUEUE_CONNECTION=sync      # Bukan database atau redis

# Comment Redis config:
# REDIS_CLIENT=phpredis
# REDIS_HOST=127.0.0.1
# ...

# 2. Clear config cache
php artisan config:clear
php artisan cache:clear
```

### Icons/Assets Tidak Muncul

**Penyebab:** Assets belum di-compile.

**Solusi:**

```bash
# Install dependencies
npm install

# Build assets
npm run build

# Atau untuk development dengan hot reload:
npm run dev
```

### Error: XAMPP MySQL Won't Start

**Penyebab:** Port 3306 sudah digunakan aplikasi lain.

**Solusi:**

```bash
# 1. Check port yang digunakan
netstat -ano | findstr :3306

# 2. Stop aplikasi yang menggunakan port tersebut
# Atau ubah MySQL port di XAMPP config

# 3. Atau gunakan SQLite untuk development:
# Di .env:
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

### Permission Denied di Storage

**Solusi Windows:**

```bash
# Pastikan folder writable
# Klik kanan folder -> Properties -> Security
# Berikan Full Control untuk user Anda

# Atau via command:
icacls "C:\xampp\htdocs\perpus-app\storage" /grant Users:F /t
icacls "C:\xampp\htdocs\perpus-app\bootstrap\cache" /grant Users:F /t
```

### Error: Class Not Found

**Solusi:**

```bash
# Clear semua cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Regenerate autoload
composer dump-autoload
```

---

## 🔄 WORKFLOW DEVELOPMENT

### Daily Development:

```bash
# 1. Start XAMPP (Apache & MySQL)

# 2. Start dev server (pilih salah satu):
php artisan serve
# Atau gunakan Apache XAMPP

# 3. Watch assets (terminal terpisah):
npm run dev

# 4. Code & test

# 5. Commit changes
git add .
git commit -m "Your message"
```

### Sebelum Commit:

```bash
# 1. Test aplikasi
php artisan test   # Jika ada tests

# 2. Check code quality
# (Optional: PHP CS Fixer, PHPStan)

# 3. Build assets untuk production
npm run build

# 4. Pastikan .env tidak ke-commit
git status

# 5. Commit
git commit -m "Your message"
```

---

## 📦 DEPENDENCIES UPDATE

### Update Composer:

```bash
# Check outdated packages
composer outdated

# Update all
composer update

# Update specific package
composer update vendor/package
```

### Update NPM:

```bash
# Check outdated packages
npm outdated

# Update all
npm update

# Update specific package
npm update package-name
```

---

## 🔐 SECURITY UNTUK DEVELOPMENT

### ✅ DO (Development):

- Gunakan `APP_DEBUG=true` untuk debugging
- Gunakan file-based cache & session
- Test fitur security middleware
- Review logs di `storage/logs/`

### ❌ DON'T (Development):

- Jangan commit `.env` ke git
- Jangan commit `database.sqlite` ke git
- Jangan commit `node_modules/` & `vendor/`
- Jangan gunakan password production di local

---

## 🚀 PERSIAPAN DEPLOY KE PRODUCTION

Sebelum deploy, pastikan:

```bash
# 1. Run security check
bash security-check.sh

# 2. Update .env untuk production:
APP_ENV=production
APP_DEBUG=false           # WAJIB false!
APP_URL=https://your-domain.com
CACHE_STORE=redis         # Gunakan Redis di production
SESSION_DRIVER=redis
LOG_LEVEL=error

# 3. Test di staging dulu

# 4. Build assets production
npm run build

# 5. Optimize
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Deploy
# Ikuti panduan di DEPLOYMENT_CHECKLIST.md
```

---

## 📚 REFERENSI

- [SECURITY.md](SECURITY.md) - Security untuk production
- [DEPLOYMENT_CHECKLIST.md](DEPLOYMENT_CHECKLIST.md) - Deployment guide
- [QUICK_SECURITY_GUIDE.md](QUICK_SECURITY_GUIDE.md) - Quick reference
- [Laravel Documentation](https://laravel.com/docs)
- [XAMPP Documentation](https://www.apachefriends.org/docs/)

---

## 💡 TIPS DEVELOPMENT

### 1. Use Laravel Debugbar (Optional)

```bash
composer require barryvdh/laravel-debugbar --dev
```

### 2. Use Laravel Telescope (Optional)

```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

### 3. Hot Module Replacement (HMR)

```bash
# Jalankan dev server dengan HMR
npm run dev

# Edit file CSS/JS, browser auto-reload
```

### 4. Database GUI Tools

- **phpMyAdmin** - http://localhost/phpmyadmin
- **TablePlus** - https://tableplus.com/
- **DBeaver** - https://dbeaver.io/

### 5. API Testing

- **Postman** - https://www.postman.com/
- **Insomnia** - https://insomnia.rest/
- **Thunder Client** (VS Code Extension)

---

## 🆘 BANTUAN

Jika mengalami masalah:

1. **Check Logs:**
    - Laravel: `storage/logs/laravel.log`
    - Apache: `C:\xampp\apache\logs\error.log`
    - PHP: `C:\xampp\php\logs\php_error_log`

2. **Clear All Cache:**

    ```bash
    php artisan optimize:clear
    ```

3. **Restart Services:**
    - Restart Apache & MySQL di XAMPP Control Panel

4. **Check Documentation:**
    - Review dokumentasi yang relevan
    - Check Laravel documentation

---

**Happy Coding! 🚀**

Last Updated: 2026-08-11
