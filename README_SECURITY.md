# 🔐 KEAMANAN APLIKASI PERPUSTAKAAN

Aplikasi perpustakaan ini telah dilengkapi dengan sistem keamanan berlapis untuk melindungi dari berbagai jenis serangan cyber.

---

## 🎯 PROTEKSI YANG DIIMPLEMENTASIKAN

### ✅ Proteksi Database

- **SQL Injection** - Pattern matching & prepared statements
- **Brute Force** - Rate limiting & account lockout
- **Credential Stuffing** - Strong password hashing (Bcrypt)
- **Database Exploitation** - Connection encryption & least privilege

### ✅ Proteksi UI/Frontend

- **XSS (Cross-Site Scripting)** - Input sanitization & CSP headers
- **CSRF (Cross-Site Request Forgery)** - Token validation
- **Clickjacking** - X-Frame-Options & frame-ancestors
- **Form Hijacking** - CSRF tokens & secure forms
- **Website Defacement** - File permissions & access control

### ✅ Proteksi Jaringan/API

- **Man-in-the-Middle** - Force HTTPS & HSTS
- **API Exploitation** - Input validation & authentication
- **BOLA (Broken Object Level Auth)** - Authorization checks

### ✅ Proteksi Umum

- **Information Disclosure** - Error handling & log sanitization
- **Privilege Escalation** - RBAC & authorization
- **Insider Threat** - Audit logging & monitoring
- **Security Misconfiguration** - Hardened configuration

---

## 📁 FILE & DIREKTORI

### 📄 Dokumentasi Keamanan

| File                                 | Deskripsi                                                 |
| ------------------------------------ | --------------------------------------------------------- |
| `SECURITY.md`                        | Panduan lengkap keamanan & konfigurasi server Rocky Linux |
| `DEPLOYMENT_CHECKLIST.md`            | Checklist lengkap untuk deployment production             |
| `QUICK_SECURITY_GUIDE.md`            | Panduan cepat & referensi command                         |
| `SECURITY_IMPLEMENTATION_SUMMARY.md` | Ringkasan implementasi keamanan                           |
| `README_SECURITY.md`                 | File ini - overview keamanan                              |

### 🔧 Script Otomasi

| File                | Deskripsi                                    |
| ------------------- | -------------------------------------------- |
| `deploy.sh`         | Script otomatis untuk deployment production  |
| `security-check.sh` | Script untuk verifikasi konfigurasi keamanan |

### 🛡️ Middleware Keamanan

| File                                             | Fungsi                        |
| ------------------------------------------------ | ----------------------------- |
| `app/Http/Middleware/SqlInjectionProtection.php` | Deteksi & block SQL injection |
| `app/Http/Middleware/XssProtection.php`          | Sanitize XSS attacks          |
| `app/Http/Middleware/SecurityHeaders.php`        | Set security HTTP headers     |
| `app/Http/Middleware/ForceHttps.php`             | Redirect HTTP ke HTTPS        |

### ⚙️ Konfigurasi

| File                   | Fungsi                                |
| ---------------------- | ------------------------------------- |
| `config/security.php`  | Konfigurasi keamanan terpusat         |
| `.env.example`         | Template environment production-ready |
| `.htaccess.production` | Security headers untuk Apache         |

### 🔍 Exception Handler

| File                         | Fungsi                                  |
| ---------------------------- | --------------------------------------- |
| `app/Exceptions/Handler.php` | Custom handler untuk hide error details |

---

## 🚀 QUICK START

### 1. Persiapan Development

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Install dependencies
composer install
npm install

# Build assets
npm run dev

# Run migrations
php artisan migrate
```

### 2. Persiapan Production

```bash
# Jalankan security check
bash security-check.sh

# Jika semua OK, jalankan deployment
bash deploy.sh
```

### 3. Verifikasi Keamanan

```bash
# Check security headers di browser DevTools
# Atau gunakan online tools:
# - https://securityheaders.com
# - https://observatory.mozilla.org
```

---

## 📖 CARA MENGGUNAKAN DOKUMENTASI

### Untuk Developer

1. **Mulai dari** `QUICK_SECURITY_GUIDE.md`
    - Referensi cepat commands
    - Testing checklist
    - Emergency procedures

2. **Baca** `SECURITY_IMPLEMENTATION_SUMMARY.md`
    - Pahami apa saja yang sudah diimplementasikan
    - Review setiap proteksi

### Untuk System Administrator

1. **Baca** `SECURITY.md`
    - Konfigurasi server Rocky Linux lengkap
    - Nginx, PHP-FPM, MySQL, Redis setup
    - Firewall & SELinux configuration

2. **Ikuti** `DEPLOYMENT_CHECKLIST.md`
    - Checklist sebelum deployment
    - Testing procedures
    - Troubleshooting guide

### Untuk Project Manager

1. **Review** `SECURITY_IMPLEMENTATION_SUMMARY.md`
    - Status implementasi keamanan
    - Compliance dengan requirements
    - Testing & monitoring procedures

---

## 🔒 KONFIGURASI KEAMANAN PENTING

### Environment Variables (`.env`)

```env
# WAJIB DI PRODUCTION
APP_ENV=production
APP_DEBUG=false              # ⚠️ HARUS false!
APP_URL=https://domain.com   # Gunakan HTTPS

# Security Features
SECURITY_SQL_INJECTION_PROTECTION=true
SECURITY_XSS_PROTECTION=true
SECURITY_FORCE_HTTPS=true

# Logging
LOG_LEVEL=error              # Jangan log detail sensitif

# Session Security
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
```

### File Permissions

```bash
# .env harus private
chmod 600 .env

# Storage directories writable
chmod -R 775 storage bootstrap/cache

# Application readable
chmod -R 755 /var/www/perpus-app
```

---

## 🧪 TESTING KEAMANAN

### Manual Testing

#### 1. Test SQL Injection

```
URL: /login
Input username: admin' OR '1'='1
Expected: 403 Forbidden atau input di-sanitize
```

#### 2. Test XSS

```
Input di form: <script>alert('XSS')</script>
Expected: Script di-strip, tidak execute
```

#### 3. Test CSRF

```
Submit form tanpa CSRF token
Expected: 419 Page Expired
```

#### 4. Test Brute Force

```
Login salah 5x berturut-turut
Expected: 429 Too Many Requests, IP diblock
```

#### 5. Test HTTPS Redirect

```
Akses: http://domain.com
Expected: Auto redirect ke https://domain.com
```

### Automated Testing

```bash
# Run security check script
bash security-check.sh

# Expected output:
# ✓ Passed: X
# ⚠ Warnings: Y
# ✗ Errors: 0
```

---

## 📊 MONITORING

### Log Files

```bash
# Application logs
tail -f storage/logs/laravel.log

# Nginx logs
tail -f /var/log/nginx/perpus_error.log
tail -f /var/log/nginx/perpus_access.log

# Check security events
grep -E "(SQL Injection|XSS|Failed login)" storage/logs/laravel.log
```

### Security Events

Events yang otomatis di-log:

- SQL Injection attempts
- XSS attempts
- Failed login attempts
- CSRF token mismatches
- Rate limit violations
- Unauthorized access attempts
- Admin actions (via AuditLog)

---

## 🆘 TROUBLESHOOTING

### Error: APP_DEBUG is true

```bash
# Edit .env
nano .env

# Ubah:
APP_DEBUG=false

# Clear config cache
php artisan config:clear
php artisan config:cache
```

### Error: Permission Denied

```bash
# Fix permissions
chmod 600 .env
chmod -R 775 storage bootstrap/cache
chown -R nginx:nginx /var/www/perpus-app

# SELinux (jika aktif)
sudo restorecon -Rv /var/www/perpus-app
```

### Error: CSRF Token Mismatch

```bash
# Clear cache
php artisan cache:clear
php artisan config:clear

# Check session driver
# Pastikan SESSION_DRIVER di .env valid (file/redis/database)
```

### Website Tidak Redirect ke HTTPS

```bash
# Check .env
# SECURITY_FORCE_HTTPS=true

# Check SSL certificate
sudo certbot certificates

# Restart nginx
sudo systemctl restart nginx
```

---

## 🔐 BEST PRACTICES

### ✅ DO

- Gunakan `APP_DEBUG=false` di production
- Gunakan HTTPS dengan valid SSL certificate
- Update dependencies secara regular
- Monitor logs untuk suspicious activity
- Backup database secara regular
- Gunakan strong passwords (min 16 char)
- Review audit logs secara berkala
- Test security sebelum deploy
- Keep documentation up-to-date

### ❌ DON'T

- Jangan commit `.env` ke git
- Jangan expose error details di production
- Jangan disable security middleware
- Jangan gunakan default passwords
- Jangan ignore security warnings
- Jangan skip testing
- Jangan forget backup before update
- Jangan expose admin panel publicly tanpa additional auth

---

## 📞 SUPPORT

### Dokumentasi Lengkap

1. **SECURITY.md** - Konfigurasi server & keamanan lengkap
2. **DEPLOYMENT_CHECKLIST.md** - Checklist & troubleshooting
3. **QUICK_SECURITY_GUIDE.md** - Quick reference
4. **SECURITY_IMPLEMENTATION_SUMMARY.md** - Implementation details

### Scripts

1. **deploy.sh** - Automated deployment
2. **security-check.sh** - Security verification

### External Resources

- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [Laravel Security](https://laravel.com/docs/security)
- [Rocky Linux Documentation](https://docs.rockylinux.org/)
- [Mozilla Observatory](https://observatory.mozilla.org/)
- [Security Headers](https://securityheaders.com/)

---

## 📝 VERSION HISTORY

| Version | Date       | Changes                         |
| ------- | ---------- | ------------------------------- |
| 1.0     | 2026-08-11 | Initial security implementation |
|         |            | - SQL Injection protection      |
|         |            | - XSS protection                |
|         |            | - CSRF protection               |
|         |            | - Security headers              |
|         |            | - Error handling                |
|         |            | - Documentation                 |

---

## ✅ SECURITY CHECKLIST

Sebelum go-live, pastikan:

- [ ] `APP_DEBUG=false`
- [ ] `APP_ENV=production`
- [ ] HTTPS aktif dengan valid certificate
- [ ] Security middleware aktif
- [ ] Rate limiting dikonfigurasi
- [ ] `.env` file permission 600
- [ ] Database password kuat
- [ ] Firewall dikonfigurasi
- [ ] Logs monitoring setup
- [ ] Backup automated
- [ ] Security testing completed
- [ ] Documentation reviewed

---

**🎉 Aplikasi Anda siap untuk di-deploy dengan aman!**

Untuk pertanyaan atau bantuan lebih lanjut, baca dokumentasi lengkap atau hubungi developer.

---

**Last Updated:** 2026-08-11  
**Version:** 1.0  
**Status:** ✅ Production Ready
