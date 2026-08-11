# 🔒 PANDUAN KEAMANAN CEPAT

## 🚨 WAJIB DILAKUKAN SEBELUM PUBLIKASI

### 1. File `.env` Production

```env
APP_ENV=production
APP_DEBUG=false          # ⚠️ WAJIB FALSE!
APP_URL=https://domain-anda.com
LOG_LEVEL=error          # Jangan log detail di production
```

### 2. Generate APP_KEY

```bash
php artisan key:generate
```

### 3. Database Password Kuat

Gunakan password minimal 16 karakter dengan kombinasi:

- Huruf besar & kecil
- Angka
- Symbol (!@#$%^&\*)

### 4. File Permissions

```bash
chmod 600 .env
chmod -R 775 storage bootstrap/cache
```

### 5. Aktifkan HTTPS

```bash
# Install SSL Certificate (Let's Encrypt)
sudo certbot --nginx -d domain-anda.com
```

---

## 🛡️ PROTEKSI YANG SUDAH DIIMPLEMENTASIKAN

### ✅ SQL Injection Protection

- Middleware otomatis deteksi & block SQL injection
- Log semua percobaan serangan

### ✅ XSS Protection

- Auto sanitize semua input
- Strip HTML tags berbahaya
- Encode special characters

### ✅ CSRF Protection

- Token CSRF otomatis di semua form
- Validasi setiap POST/PUT/DELETE request

### ✅ Brute Force Protection

- Rate limiting: Max 5 login attempts per 15 menit
- Auto block IP yang mencurigakan

### ✅ Clickjacking Protection

- X-Frame-Options: DENY
- CSP frame-ancestors 'none'

### ✅ MitM Protection

- Force HTTPS di production
- HSTS header aktif
- Secure cookies

### ✅ Information Disclosure Prevention

- Error details tersembunyi di production
- Server headers dihidden
- Database errors tidak ter-expose

---

## 📝 FILE YANG HARUS DIAMANKAN

### ⚠️ Jangan Masuk Git:

```
.env                    # Environment variables
.env.*                  # Semua variasi .env
database.sqlite         # Database file
*.sql                   # Backup database
*.bak, *.backup        # File backup
*.key, *.pem           # Private keys
credentials.json       # Credentials
```

✅ Sudah ditambahkan ke `.gitignore`

---

## 🔧 KONFIGURASI SERVER (Rocky Linux)

### 1. Firewall

```bash
sudo firewall-cmd --permanent --add-service=https
sudo firewall-cmd --permanent --add-service=http
sudo firewall-cmd --reload
```

### 2. SELinux (Jika aktif)

```bash
sudo setsebool -P httpd_can_network_connect 1
sudo setsebool -P httpd_can_network_connect_db 1
```

### 3. Disable Root Login SSH

```bash
sudo nano /etc/ssh/sshd_config
# Set: PermitRootLogin no
sudo systemctl restart sshd
```

### 4. Install Fail2ban (Anti Brute Force)

```bash
sudo dnf install -y fail2ban
sudo systemctl enable fail2ban
sudo systemctl start fail2ban
```

---

## 🧪 TEST KEAMANAN SETELAH DEPLOY

### 1. Cek HTTPS

✅ URL harus `https://` bukan `http://`
✅ Browser menampilkan gembok hijau

### 2. Cek Error Handling

❌ Akses URL random → harus tampil 404 generic, bukan stack trace
❌ Database error → harus tampil error generic, bukan SQL query

### 3. Cek CSRF

❌ Submit form tanpa CSRF token → harus ditolak (419 error)

### 4. Cek Rate Limiting

❌ Login salah 5x → harus diblock (429 Too Many Requests)

### 5. Cek Security Headers

Buka Browser DevTools (F12) → Network → Check headers:

```
✅ Strict-Transport-Security: max-age=31536000
✅ X-Frame-Options: DENY
✅ X-Content-Type-Options: nosniff
✅ X-XSS-Protection: 1; mode=block
✅ Content-Security-Policy: ...
❌ X-Powered-By: (harus tidak ada)
❌ Server: (harus tidak ada atau generic)
```

---

## 🔥 QUICK DEPLOYMENT

```bash
# 1. Masuk ke server
ssh user@your-server

# 2. Masuk directory aplikasi
cd /var/www/perpus-app

# 3. Jalankan script deployment
bash deploy.sh

# 4. Test di browser
# https://domain-anda.com
```

---

## 🚨 JIKA TERJADI SERANGAN

### 1. Block IP Attacker

```bash
sudo firewall-cmd --permanent --add-rich-rule="rule family='ipv4' source address='IP_ATTACKER' reject"
sudo firewall-cmd --reload
```

### 2. Check Logs

```bash
# Laravel logs
tail -f storage/logs/laravel.log | grep -E "(SQL Injection|XSS|Failed login)"

# Nginx logs
tail -f /var/log/nginx/perpus_error.log
```

### 3. Maintenance Mode

```bash
php artisan down --message="Sedang dalam maintenance"
```

### 4. Restore dari Backup

```bash
# Database
mysql -u user -p database_name < backup.sql

# Files
tar -xzf backup.tar.gz
```

---

## 📞 KONTAK DARURAT

**Developer:** [Your Contact]
**Sysadmin:** [Admin Contact]

---

## 📚 DOKUMENTASI LENGKAP

- [SECURITY.md](SECURITY.md) - Panduan keamanan detail
- [DEPLOYMENT_CHECKLIST.md](DEPLOYMENT_CHECKLIST.md) - Checklist deployment
- [deploy.sh](deploy.sh) - Script deployment otomatis

---

## ⚡ COMMANDS BERGUNA

```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize untuk production
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer dump-autoload --optimize

# Check status services
sudo systemctl status nginx
sudo systemctl status php-fpm
sudo systemctl status mariadb
sudo systemctl status redis

# Monitor logs realtime
tail -f storage/logs/laravel.log
tail -f /var/log/nginx/perpus_error.log

# Check disk space
df -h

# Check memory usage
free -m

# Check running processes
ps aux | grep php
```

---

**✅ Aplikasi siap untuk publikasi setelah semua langkah di atas dilakukan!**
