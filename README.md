# Sistem Absensi Berbasis Sidik Jari

Sistem absensi berbasis sidik jari menggunakan **ESP32** dan **Fingerprint Sensor AS608**, terintegrasi dengan aplikasi **Laravel 12** sebagai backend dan dashboard pengelolaan data.

Fitur utama:

-   Manajemen pengguna (admin & guru)
-   Enroll & verifikasi sidik jari
-   Logging absensi real-time
-   Integrasi ESP32 → REST API Laravel

---

## 1. Persiapan Environment

**Software yang dibutuhkan:**

-   PHP 8.2+
-   Composer
-   Node.js + NPM
-   Git
-   MySQL / MariaDB
-   ESP32 + Sensor Sidik Jari AS608

---

## 2. Clone Project

```bash
git clone https://github.com/Fdhlzz/Aplikasi-Absensi.git
cd Aplikasi-Absensi
```

## 3. Install Dependencies

Install Composer

```bash
composer install
```

Install Node.js

```bash
npm install
```

## 4. konfigurasi Environment

buat file .env

```bash
cp .env.example .env
```

generate key

```bash
php artisan key:generate
```

## 5. Setup Database

jalankan perintah migrate + seeder

```bash
php artisan migrate:fresh --seed
```

## 6. Menjalankan server

menjalankan server npm

```bash
npm run dev
```

menjalankan server laravel

```bash
php artisan serve
```
