# Panduan Instalasi Smart Savings (dengan Docker Compose + MySQL)

Dengan konfigurasi ini, seluruh proses setup project—termasuk instalasi PHP 8.2 (dengan driver MySQL), MySQL Server 8.0, instalasi Composer, penyalinan `.env`, migrasi database, instalasi NPM, hingga menjalankan server backend Laravel dan frontend Vite—telah diotomatisasi di dalam Docker.

## Prasyarat (Prerequisites)

Pastikan Anda memiliki:
- **Docker Desktop** yang terinstal dan sedang aktif.

---

## Langkah Instalasi & Menjalankan Aplikasi

1. Buka terminal (PowerShell / CMD / Bash) di direktori root project Anda.
2. Jalankan perintah berikut untuk membangun ulang container image dan menyalakan service:
   ```bash
   docker compose up --build
   ```

### Apa saja yang dilakukan secara otomatis?
- Membangun container image dengan PHP 8.2, driver `pdo_mysql`, Composer, dan Node.js/NPM.
- Menjalankan container database **MySQL 8.0** dengan database `smart_savings` dan password root `root`.
- Menyalin konfigurasi `.env.example` ke `.env` (jika belum ada).
- Menginstal semua dependensi backend via `composer install`.
- Menunggu hingga database MySQL siap menerima koneksi, lalu menjalankan migrasi database (`php artisan migrate`).
- Menginstal semua dependensi frontend via `npm install`.
- Menjalankan server Laravel di port `8000` dan Vite dev server di port `5173` secara bersamaan.

---

## Akses Aplikasi

👉 **[http://localhost:8000](http://localhost:8000)**

### Akses phpMyAdmin (Database Viewer)
Untuk melihat dan mengelola isi database MySQL:
👉 **[http://localhost:8080](http://localhost:8080)**
* **Username:** `root`
* **Password:** `root`

---

## Perintah Tambahan (Opsional)
* **Menghentikan aplikasi:** Tekan `Ctrl + C` pada terminal, atau jalankan:
  ```bash
  docker compose down
  ```
* **Menjalankan perintah Artisan di dalam container:**
  ```bash
  docker compose exec app php artisan [perintah]
  ```
* **Database Credentials (di dalam Docker network):**
  - **Connection:** `mysql`
  - **Host:** `mysql`
  - **Port:** `3306`
  - **Database Name:** `smart_savings`
  - **Username:** `root`
  - **Password:** `root`
