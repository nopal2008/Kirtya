# 🛡️ Content Security Policy (CSP) Configuration

Dokumentasi konfigurasi CSP untuk aplikasi Kirtya.

---

## 📋 Apa itu CSP?

Content Security Policy (CSP) adalah security header yang mencegah:

- **XSS (Cross-Site Scripting)** attacks
- **Code injection** attacks
- **Clickjacking**
- **Data injection**

CSP mengatur sumber mana saja yang boleh dimuat oleh browser (scripts, styles, fonts, images, dll).

---

## ⚙️ Konfigurasi CSP Aplikasi Kirtya

### Current Configuration

File: `app/Http/Middleware/SecurityHeaders.php`

```php
$csp = implode('; ', [
    "default-src 'self'",                   // Default: hanya dari domain sendiri
    "script-src 'self' 'unsafe-inline' 'unsafe-eval'",  // JavaScript
    "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com",  // CSS
    "style-src-elem 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com",  // CSS elements
    "font-src 'self' data: https://fonts.gstatic.com https://cdnjs.cloudflare.com",  // Fonts
    "img-src 'self' data: https:",          // Images
    "connect-src 'self'",                   // AJAX/WebSocket
    "frame-ancestors 'none'",               // Prevent clickjacking
    "base-uri 'self'",                      // Base URL
    "form-action 'self'",                   // Form submissions
]);
```

---

## 🌐 External Resources yang Diizinkan

### 1. Google Fonts

**URL:** `https://fonts.googleapis.com` dan `https://fonts.gstatic.com`

**Digunakan untuk:**

- Font Inter untuk UI
- Font custom lainnya

**CSP Directives:**

```
style-src https://fonts.googleapis.com
style-src-elem https://fonts.googleapis.com
font-src https://fonts.gstatic.com
```

### 2. Font Awesome CDN

**URL:** `https://cdnjs.cloudflare.com`

**Digunakan untuk:**

- Icons Font Awesome
- Font untuk icons

**CSP Directives:**

```
style-src https://cdnjs.cloudflare.com
style-src-elem https://cdnjs.cloudflare.com
font-src https://cdnjs.cloudflare.com
```

---

## 🔧 Cara Menambahkan Domain Baru

Jika Anda ingin menggunakan CDN atau service eksternal lain:

### 1. Edit SecurityHeaders.php

```php
// Contoh: Menambahkan Bootstrap CDN
$csp = implode('; ', [
    "default-src 'self'",
    "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net",  // ← Tambahkan di sini
    "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net",  // ← Dan di sini
    // ... rest of config
]);
```

### 2. Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
```

### 3. Test di Browser

Buka Developer Tools (F12) → Console

- Tidak boleh ada error CSP violation

---

## ⚠️ CSP Directives Explained

### default-src

Default policy untuk semua resource types.

```
"default-src 'self'"
```

Artinya: Secara default, hanya muat resource dari domain sendiri.

### script-src

Control dari mana JavaScript boleh dimuat.

```
"script-src 'self' 'unsafe-inline' 'unsafe-eval'"
```

- `'self'` - Dari domain sendiri
- `'unsafe-inline'` - Allow inline scripts (`<script>...</script>`)
- `'unsafe-eval'` - Allow `eval()` dan `new Function()`

⚠️ **Note:** `unsafe-inline` dan `unsafe-eval` mengurangi security tapi diperlukan untuk beberapa library (Laravel Mix, Vue, dll).

### style-src & style-src-elem

Control dari mana CSS boleh dimuat.

```
"style-src 'self' 'unsafe-inline' https://fonts.googleapis.com"
"style-src-elem 'self' 'unsafe-inline' https://fonts.googleapis.com"
```

- `style-src` - CSS inline dan imports
- `style-src-elem` - `<style>` dan `<link>` elements

### font-src

Control dari mana fonts boleh dimuat.

```
"font-src 'self' data: https://fonts.gstatic.com"
```

- `data:` - Allow data: URIs (base64 fonts)

### img-src

Control dari mana images boleh dimuat.

```
"img-src 'self' data: https:"
```

- `data:` - Allow data: URIs (base64 images)
- `https:` - Allow semua HTTPS URLs (untuk user-uploaded images, dll)

### connect-src

Control kemana AJAX/fetch/WebSocket bisa connect.

```
"connect-src 'self'"
```

### frame-ancestors

Prevent clickjacking - control siapa yang bisa embed halaman Anda dalam iframe.

```
"frame-ancestors 'none'"
```

Artinya: Tidak ada yang boleh embed halaman ini dalam iframe.

### base-uri

Control `<base>` tag URLs.

```
"base-uri 'self'"
```

### form-action

Control kemana forms bisa submit.

```
"form-action 'self'"
```

---

## 🧪 Testing CSP

### 1. Check di Browser Console

Buka Developer Tools (F12) → Console

**✅ Good (No errors):**

```
(Tidak ada pesan CSP violation)
```

**❌ Bad (CSP blocking resource):**

```
Refused to load the stylesheet 'https://example.com/style.css'
because it violates the following Content Security Policy directive:
"style-src 'self' 'unsafe-inline'".
```

**Solution:** Tambahkan `https://example.com` ke `style-src`.

### 2. Check di Network Tab

Developer Tools (F12) → Network

- Semua resources harus status 200 (OK)
- Tidak ada yang di-block oleh CSP

### 3. Online CSP Validator

- [CSP Evaluator](https://csp-evaluator.withgoogle.com/)
- [Mozilla Observatory](https://observatory.mozilla.org/)

---

## 🔒 Security Levels

### Strict (Production)

```php
"script-src 'self'",                // No inline scripts
"style-src 'self'",                 // No inline styles
```

**Pro:** Sangat aman
**Con:** Perlu refactor semua inline scripts/styles

### Moderate (Current - Recommended)

```php
"script-src 'self' 'unsafe-inline' 'unsafe-eval'",
"style-src 'self' 'unsafe-inline' https://trusted-cdn.com",
```

**Pro:** Balance antara security & compatibility
**Con:** Masih ada risk dari inline scripts

### Permissive (Development Only)

```php
"default-src *",                    // Allow semua
```

**Pro:** Tidak ada blocking
**Con:** Tidak ada protection!

⚠️ **JANGAN gunakan permissive di production!**

---

## 🐛 Common Issues & Solutions

### Issue 1: Google Fonts Not Loading

**Error:**

```
Loading stylesheet violates CSP directive: "style-src 'self'"
```

**Solution:**

```php
"style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
"style-src-elem 'self' 'unsafe-inline' https://fonts.googleapis.com",
"font-src 'self' data: https://fonts.gstatic.com",
```

### Issue 2: Font Awesome Icons Not Showing

**Error:**

```
Loading stylesheet from cdnjs violates CSP
```

**Solution:**

```php
"style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com",
"style-src-elem 'self' 'unsafe-inline' https://cdnjs.cloudflare.com",
"font-src 'self' data: https://cdnjs.cloudflare.com",
```

### Issue 3: Inline Scripts Blocked

**Error:**

```
Refused to execute inline script because it violates CSP
```

**Solution Option 1 (Quick):**

```php
"script-src 'self' 'unsafe-inline'",
```

**Solution Option 2 (Secure):**
Move inline scripts ke external .js files.

### Issue 4: API Calls Blocked

**Error:**

```
Refused to connect to 'https://api.example.com'
```

**Solution:**

```php
"connect-src 'self' https://api.example.com",
```

---

## 📝 Best Practices

### ✅ DO:

1. **Use nonces for inline scripts (Advanced)**

    ```php
    $nonce = base64_encode(random_bytes(16));
    "script-src 'self' 'nonce-{$nonce}'";
    ```

2. **Whitelist specific domains only**

    ```php
    // Good
    "script-src 'self' https://cdn.example.com"

    // Bad (too permissive)
    "script-src *"
    ```

3. **Use report-uri for monitoring**

    ```php
    "report-uri /csp-violation-report-endpoint"
    ```

4. **Test in staging first**
    - Deploy CSP changes ke staging dulu
    - Test semua fitur
    - Baru deploy ke production

### ❌ DON'T:

1. **Jangan disable CSP completely**

    ```php
    // BAD!
    // $response->headers->remove('Content-Security-Policy');
    ```

2. **Jangan gunakan wildcard di production**

    ```php
    // BAD!
    "script-src *"
    "style-src *"
    ```

3. **Jangan allow `unsafe-inline` tanpa perlu**
    - Hanya gunakan jika library require it

---

## 🔄 CSP untuk Different Environments

### Development (.env)

```env
APP_ENV=local
APP_DEBUG=true
# CSP lebih relaxed untuk debugging
```

### Production (.env)

```env
APP_ENV=production
APP_DEBUG=false
# CSP strict untuk security
```

**Note:** Bisa buat conditional CSP berdasarkan environment:

```php
$scriptSrc = config('app.env') === 'production'
    ? "'self'"
    : "'self' 'unsafe-inline' 'unsafe-eval'";

$csp = "script-src {$scriptSrc}; ...";
```

---

## 📚 Resources

- [MDN Web Docs - CSP](https://developer.mozilla.org/en-US/docs/Web/HTTP/CSP)
- [CSP Cheat Sheet (OWASP)](https://cheatsheetseries.owasp.org/cheatsheets/Content_Security_Policy_Cheat_Sheet.html)
- [Google CSP Guide](https://developers.google.com/web/fundamentals/security/csp)
- [CSP Evaluator Tool](https://csp-evaluator.withgoogle.com/)

---

## 🆘 Need Help?

Jika CSP masih blocking resources:

1. **Check browser console untuk exact error**
2. **Identify domain yang di-block**
3. **Tambahkan domain ke appropriate directive**
4. **Clear cache & test**

Example debugging:

```bash
# 1. Clear cache
php artisan cache:clear
php artisan config:clear

# 2. Hard refresh browser
Ctrl + Shift + R (Windows)
Cmd + Shift + R (Mac)

# 3. Check console
F12 → Console tab
```

---

**Last Updated:** 2026-08-11
**Version:** 1.0 - Kirtya Edition
