# Perpus App

Perpus App adalah aplikasi manajemen perpustakaan berbasis Laravel yang dirancang untuk membantu admin mengelola buku, anggota, peminjaman, pengembalian, denda keterlambatan, dan dashboard operasional perpustakaan.

## Fitur Utama

- Manajemen data buku dan stok
- Manajemen data anggota
- Proses peminjaman dan pengembalian
- Pengaturan denda keterlambatan
- Dashboard admin untuk melihat aktivitas perpustakaan
- Integrasi monitoring dengan Prometheus dan Grafana

## Teknologi yang Digunakan

- PHP 8.2
- Laravel 12
- MySQL
- Vite untuk frontend assets

## Cara Menjalankan

1. Install dependency PHP:
   ```bash
   composer install
   ```
2. Install dependency frontend:
   ```bash
   npm install
   ```
3. Salin file environment:
   ```bash
   copy .env.example .env
   ```
4. Generate application key:
   ```bash
   php artisan key:generate
   ```
5. Jalankan migrasi database:
   ```bash
   php artisan migrate
   ```
6. Jalankan aplikasi:
   ```bash
   php artisan serve
   ```
   dan di terminal lain:
   ```bash
   npm run dev
   ```

## Catatan Keamanan

Pastikan file berikut tidak pernah dipublikasikan ke repository:

- file `.env`
- file database lokal seperti `.sqlite`, `.db`, `.sql`, dan dump database
- folder `storage/`, `public/uploads/`, serta kredensial dan token sensitif

## Lisensi

Proyek ini menggunakan lisensi MIT.
