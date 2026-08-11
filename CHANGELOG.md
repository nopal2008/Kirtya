# 📝 CHANGELOG - Aplikasi Kirtya

## [1.1.0] - 2026-08-11

### ✨ Changed

- **Nama Aplikasi:** "SIPerpus" → **"Kirtya"**
    - Updated `.env`
    - Updated `.env.example`

### 🔧 Fixed

- **CSP (Content Security Policy)** updated to allow external resources
    - ✅ Google Fonts sekarang bisa dimuat
    - ✅ Font Awesome CDN sekarang bisa dimuat
    - ✅ Icons dan fonts muncul dengan benar

### 📚 Added Documentation

- **`CSP_CONFIG.md`** - Panduan lengkap konfigurasi Content Security Policy
    - Penjelasan setiap CSP directive
    - Cara menambahkan domain baru
    - Troubleshooting CSP issues
    - Best practices

### 🛡️ Security Headers Updated

File: `app/Http/Middleware/SecurityHeaders.php`

**Old CSP:**

```php
"style-src 'self' 'unsafe-inline'",
"font-src 'self' data:",
```

**New CSP:**

```php
"style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com",
"style-src-elem 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com",
"font-src 'self' data: https://fonts.gstatic.com https://cdnjs.cloudflare.com",
```

**Allowed External Domains:**

- ✅ `fonts.googleapis.com` - Google Fonts CSS
- ✅ `fonts.gstatic.com` - Google Fonts files
- ✅ `cdnjs.cloudflare.com` - Font Awesome & other CDN resources

---

## [1.0.0] - 2026-08-11

### 🎉 Initial Security Implementation

#### 🛡️ Security Features

- SQL Injection Protection middleware
- XSS Protection middleware
- CSRF Protection (Laravel built-in)
- Security Headers middleware
- Force HTTPS middleware
- Custom Exception Handler
- Rate Limiting for brute force protection

#### 📦 Files Created

**Middleware:**

- `app/Http/Middleware/SqlInjectionProtection.php`
- `app/Http/Middleware/XssProtection.php`
- `app/Http/Middleware/SecurityHeaders.php`
- `app/Http/Middleware/ForceHttps.php`

**Exception Handler:**

- `app/Exceptions/Handler.php`

**Configuration:**

- `config/security.php`
- `.env.example` (updated)
- `.gitignore` (enhanced)

**Documentation:**

- `SECURITY.md` - Full security & server setup guide
- `SECURITY_INDEX.md` - Documentation index
- `README_SECURITY.md` - Security overview
- `QUICK_SECURITY_GUIDE.md` - Quick reference
- `SECURITY_IMPLEMENTATION_SUMMARY.md` - Implementation details
- `DEPLOYMENT_CHECKLIST.md` - Deployment guide
- `DEVELOPMENT_SETUP.md` - Development setup guide
- `QUICK_FIX.md` - Common issues solutions
- `nginx.conf.example` - Nginx configuration
- `.htaccess.production` - Apache security headers

**Scripts:**

- `deploy.sh` - Automated deployment
- `security-check.sh` - Security verification

#### 🔧 Configuration Updates

- Updated `bootstrap/app.php` with security middleware
- Updated `routes/web.php` with rate limiting
- Updated `config/app.php` with Indonesian locale

#### 🐛 Fixed

- Redis dependency error (switched to file-based cache/session for XAMPP)
- Assets not loading (npm build)

---

## 📊 Version Summary

| Version | Date       | Major Changes                                |
| ------- | ---------- | -------------------------------------------- |
| 1.1.0   | 2026-08-11 | Rename to Kirtya, Fix CSP for external fonts |
| 1.0.0   | 2026-08-11 | Initial security implementation              |

---

## 🔜 Planned Features

### v1.2.0 (Future)

- [ ] Two-Factor Authentication (2FA)
- [ ] API Token authentication
- [ ] Advanced audit logging dashboard
- [ ] Email notifications for security events
- [ ] Automated backup system
- [ ] Performance monitoring

### v2.0.0 (Future)

- [ ] Multi-tenant support
- [ ] Advanced reporting
- [ ] Mobile app integration
- [ ] Real-time notifications

---

## 🆘 Upgrade Guide

### From 1.0.0 to 1.1.0

1. **Update .env**

    ```bash
    # Change app name
    APP_NAME=Kirtya
    ```

2. **Clear cache**

    ```bash
    php artisan config:clear
    php artisan cache:clear
    php artisan view:clear
    ```

3. **No database changes required**

4. **Test application**
    - Check fonts loading
    - Check icons showing
    - Test all features

---

## 📞 Support

Jika mengalami issues setelah update:

1. Check `QUICK_FIX.md` untuk common issues
2. Check `CSP_CONFIG.md` untuk CSP-related issues
3. Clear all cache: `php artisan optimize:clear`
4. Check browser console (F12) untuk errors

---

**Maintained by:** Development Team
**Last Updated:** 2026-08-11
