# Sistem Informasi Sekolah Minggu Buddha pada Vihara Dharma Cattra

Aplikasi berbasis web ini dikembangkan untuk memudahkan para pengajar sekolah minggu buddha dalam melakukan pencatatan kehadiran dan pendaftaran siswa.

## 🚀 Fitur Utama
* **Manajemen Data Induk:** Fitur *CRUD* untuk mencatat, mengedit, dan mengelola data siswa, pengajar, kelas, agenda, pemberitahuan, dan tahun ajaran pada sekolah minggu
* **Pencatatan Kehadiran:** Fitur pencatatan kehadiran menggunakan sistem scanner atau pemindai barcode yang dapat memudahkan dan mempercepat dalam melakukan pencatatan
* **Pendaftaran Siswa:** Fitur pendaftaran siswa baru yang dilakukan oleh orang tua siswa, dan verifikasi data siswa yang dilakukan di sisi pengajar atau admin


## 🛠️ Teknologi yang Digunakan
* **Backend:** PHP, Laravel Framework
* **Database:** MySQL
* **Frontend:** Blade Templating, Tailwind CSS, JavaScript

## ⚙️ Cara Instalasi (Local Development)
Jika ingin menjalankan proyek ini secara lokal, ikuti langkah-langkah berikut:

1. Clone *repository* ini:
   ```bash
   git clone [https://github.com/kean30-08/SI-MAINTENANCE-LA.git](https://github.com/kean30-08/SI-MAINTENANCE-LA.git)

2. Masuk ke direktori proyek dan install dependencies:
   ```bash
   composer install
   npm install

3. Salin file .env.example menjadi .env dan atur konfigurasi database Anda.

4. Buat application key:
    ```bash
    php artisan key:generate

5. Jalankan migrasi database:
    ```bash
    php artisan migrate

6. Jalankan server lokal dan kompilasi aset frontend (Tailwind CSS). Buka dua tab terminal yang berbeda:
-  Terminal 1:
    ```bash
    php artisan serve

- Terminal 2:
  ```bash
  npm run dev

7. Login menggunakan kredensial:
- email: admin@smbvdc.ac.id
- password: admin123
