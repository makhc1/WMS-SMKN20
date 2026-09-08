
# Warehouse Management System - SMKN 20 Jakarta

Sistem Manajemen Gudang (Warehouse Management System) berbasis web untuk SMKN 20 Jakarta. Aplikasi ini dirancang untuk mengelola inventaris barang, mencatat transaksi barang masuk dan keluar, memantau stok secara real-time, serta menghasilkan laporan operasional gudang dalam format PDF.

---

## Daftar Isi

- [Fitur Utama](#fitur-utama)
- [Teknologi yang Digunakan](#teknologi-yang-digunakan)
- [Persyaratan Sistem](#persyaratan-sistem)
- [Instalasi](#instalasi)
- [Konfigurasi Environment](#konfigurasi-environment)
- [Perintah yang Tersedia](#perintah-yang-tersedia)
- [Struktur Proyek](#struktur-proyek)
- [Arsitektur Aplikasi](#arsitektur-aplikasi)
- [Skema Database](#skema-database)
- [Sistem Peran dan Otorisasi](#sistem-peran-dan-otorisasi)
- [Daftar Rute](#daftar-rute)
- [Modul Aplikasi](#modul-aplikasi)
- [Lisensi](#lisensi)

---

## Fitur Utama

### Dashboard
- Ringkasan stok total dan utilisasi kapasitas gudang.
- Jumlah barang masuk dan keluar pada hari berjalan.
- Jumlah transaksi dengan status pending.
- Grafik tren barang masuk dan keluar selama 7 hari terakhir.
- Daftar 5 barang paling aktif berdasarkan volume transaksi 30 hari terakhir.
- Peringatan stok rendah (low stock alert).

### Manajemen Barang (Master Item)
- CRUD data barang dengan atribut SKU, nama, kategori, merek, satuan, harga dasar, deskripsi, dan foto.
- Pencarian berdasarkan SKU atau nama barang.
- Filter berdasarkan kategori.
- Pengaturan ambang batas stok rendah (low stock threshold) per barang.
- Validasi penghapusan: barang dengan stok lebih dari nol tidak dapat dihapus.

### Transaksi Barang Masuk (Inbound)
- Pencatatan penerimaan barang dengan nomor tanda terima otomatis (format: `RCV-YYYYMMDD-XXXXX`).
- Pemilihan barang, tanggal transaksi, jumlah, pemasok, kondisi barang, dan catatan.
- Status transaksi: `pending` atau `completed`.
- Perubahan stok hanya terjadi ketika status transaksi adalah `completed`.
- Fitur konfirmasi penyelesaian transaksi pending (mark as completed) yang secara otomatis menambah stok.
- Penggunaan database locking (`lockForUpdate`) untuk mencegah race condition pada pembaruan stok.

### Transaksi Barang Keluar (Outbound)
- Pencatatan pengeluaran barang dengan nomor surat jalan otomatis (format: `DO-YYYYMMDD-XXXXX`).
- Data penerima, alamat, kurir, estimasi pengiriman, dan catatan.
- Validasi ketersediaan stok sebelum transaksi `completed` diproses.
- Fitur konfirmasi penyelesaian transaksi pending dengan validasi stok ulang.
- Penggunaan database transaction dan row locking untuk integritas data.

### Manajemen Lokasi Gudang
- CRUD data lokasi penyimpanan dengan kode unik, nama, zona, tipe penyimpanan, persentase kapasitas, dan status.
- Tipe penyimpanan: `Dry`, `Cold Storage`, `Hazardous`.
- Status lokasi: `Active`, `Under Maintenance`.
- Pencarian berdasarkan kode, nama, atau zona.

### Laporan
- Laporan Stok Gudang: daftar seluruh barang beserta stok terkini, diekspor ke PDF.
- Laporan Mutasi Barang: rekap transaksi masuk dan keluar dalam rentang tanggal tertentu, diekspor ke PDF.
- Laporan dihasilkan menggunakan DomPDF.

### Manajemen Pengguna
- CRUD data pengguna dengan nama, email, password, peran, dan status.
- Tiga tingkat peran: `Admin`, `Warehouse Manager`, `Staff Picker`.
- Status pengguna: `Active`, `Suspended`.
- Perlindungan penghapusan akun sendiri (self-delete protection).

---

## Teknologi yang Digunakan

### Backend
| Komponen | Teknologi | Versi |
|----------|-----------|-------|
| Bahasa Pemrograman | PHP | >= 8.3 |
| Framework | Laravel | 13.x |
| Autentikasi | Laravel Breeze | 2.x |
| API Token | Laravel Sanctum | 4.x |
| PDF Generator | barryvdh/laravel-dompdf | 3.x |
| Barcode Generator | picqer/php-barcode-generator | 3.x |
| URL Generator | Ziggy | 2.x |

### Frontend
| Komponen | Teknologi | Versi |
|----------|-----------|-------|
| JavaScript Framework | Vue.js | 3.4+ |
| Server-Side Rendering | Inertia.js | 2.x |
| CSS Framework | Tailwind CSS | 3.x |
| Build Tool | Vite | 8.x |
| Grafik | Chart.js + vue-chartjs | 4.x / 5.x |
| Grafik (alternatif) | ApexCharts + vue3-apexcharts | 6.x / 1.x |
| Ikon | Heroicons, Phosphor Icons | 2.x |
| Barcode (client) | JsBarcode | 3.x |
| QR Code | qrcode.vue, vue-qrcode-reader | 3.x / 5.x |
| Animasi | @vueuse/motion | 3.x |
| Utilitas | @vueuse/core | 14.x |

### Database
| Komponen | Teknologi |
|----------|-----------|
| RDBMS | MySQL 8.0 |
| Session Driver | Database |
| Cache Driver | Database |
| Queue Driver | Database |

---

## Persyaratan Sistem

- PHP >= 8.3 dengan ekstensi: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, PDO_MySQL, Tokenizer, XML.
- Composer >= 2.x.
- Node.js >= 18.x dan npm >= 9.x.
- MySQL >= 8.0.
- Web server: Apache atau Nginx (atau Laragon untuk lingkungan Windows).

---

## Instalasi

### 1. Clone Repositori

```bash
git clone https://github.com/makhc1/Manajemen-Logistik.git
cd Manajemen-Logistik
```

### 2. Instal Dependensi Backend

```bash
composer install
```

### 3. Instal Dependensi Frontend

```bash
npm install
```

### 4. Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit file `.env` dan sesuaikan konfigurasi database:

```env
APP_NAME="WMS SMKN 20"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-anda.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smkn20_wm
DB_USERNAME=root
DB_PASSWORD=password_anda
```

### 5. Migrasi Database

```bash
php artisan migrate
```

### 6. Build Aset Frontend

```bash
npm run build
```

### 7. Buat Symlink Storage

```bash
php artisan storage:link
```

### 8. Buat Akun Admin Pertama

Gunakan Laravel Tinker untuk membuat akun administrator pertama:

```bash
php artisan tinker
```

```php
\App\Models\User::create([
    'name' => 'Administrator',
    'email' => 'admin@smkn20.sch.id',
    'password' => bcrypt('password_anda'),
    'role' => 'Admin',
    'status' => 'Active',
]);
```

### Instalasi Cepat (Alternatif)

Proyek ini menyediakan script Composer untuk instalasi otomatis:

```bash
composer setup
```

Perintah ini akan menjalankan `composer install`, menyalin `.env.example`, membuat application key, menjalankan migrasi, serta menginstal dan membangun aset frontend secara berurutan.

---

## Konfigurasi Environment

Berikut adalah variabel environment utama yang perlu dikonfigurasi:

| Variabel | Deskripsi | Contoh Nilai |
|----------|-----------|--------------|
| `APP_NAME` | Nama aplikasi | `"WMS SMKN 20"` |
| `APP_ENV` | Lingkungan aplikasi | `production` |
| `APP_DEBUG` | Mode debug (nonaktifkan di production) | `false` |
| `APP_URL` | URL dasar aplikasi | `https://domain-anda.com` |
| `APP_LOCALE` | Bahasa default | `id` |
| `DB_CONNECTION` | Driver database | `mysql` |
| `DB_HOST` | Host database | `127.0.0.1` |
| `DB_PORT` | Port database | `3306` |
| `DB_DATABASE` | Nama database | `smkn20_wm` |
| `DB_USERNAME` | Username database | `root` |
| `DB_PASSWORD` | Password database | - |
| `SESSION_DRIVER` | Driver sesi | `database` |
| `QUEUE_CONNECTION` | Driver antrian | `database` |
| `CACHE_STORE` | Driver cache | `database` |

---

## Perintah yang Tersedia

| Perintah | Deskripsi |
|----------|-----------|
| `composer setup` | Instalasi lengkap (install, key:generate, migrate, build) |
| `composer dev` | Menjalankan development server |
| `composer test` | Menjalankan PHPUnit test suite |
| `npm run dev` | Menjalankan Vite development server dengan HMR |
| `npm run build` | Membangun aset frontend untuk production |
| `php artisan migrate` | Menjalankan migrasi database |
| `php artisan migrate:fresh` | Menghapus semua tabel dan menjalankan ulang migrasi |
| `php artisan storage:link` | Membuat symbolic link untuk storage publik |

---

## Struktur Proyek

```
Manajemen-Logistik/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/                           # Controller autentikasi (Breeze)
│   │   │   ├── InboundTransactionController.php # Transaksi barang masuk
│   │   │   ├── ItemController.php               # Master data barang
│   │   │   ├── LocationController.php           # Lokasi penyimpanan
│   │   │   ├── OutboundTransactionController.php# Transaksi barang keluar
│   │   │   ├── ProfileController.php            # Profil pengguna
│   │   │   ├── ReportController.php             # Ekspor laporan PDF
│   │   │   └── UserController.php               # Manajemen pengguna
│   │   └── Middleware/
│   │       ├── CheckRole.php                    # Middleware otorisasi peran
│   │       └── HandleInertiaRequests.php         # Middleware Inertia.js
│   └── Models/
│       ├── InboundTransaction.php
│       ├── Item.php
│       ├── Location.php
│       ├── OutboundTransaction.php
│       └── User.php
├── database/
│   ├── migrations/                              # File migrasi database
│   ├── factories/                               # Factory untuk testing
│   └── seeders/                                 # Seeder database
├── resources/
│   ├── js/
│   │   ├── Components/                          # Komponen Vue.js reusable
│   │   ├── Layouts/                             # Layout halaman
│   │   │   ├── AuthenticatedLayout.vue          # Layout utama (setelah login)
│   │   │   ├── GuestLayout.vue                  # Layout tamu
│   │   │   └── SplitAuthLayout.vue              # Layout halaman autentikasi
│   │   └── Pages/                               # Halaman aplikasi
│   │       ├── Auth/                            # Halaman login, register, dll.
│   │       ├── Dashboard.vue                    # Halaman dashboard
│   │       ├── Inbound/                         # Halaman transaksi masuk
│   │       ├── Items/                           # Halaman master barang
│   │       ├── Locations/                       # Halaman lokasi gudang
│   │       ├── Outbound/                        # Halaman transaksi keluar
│   │       ├── Reports/                         # Halaman laporan
│   │       └── Users/                           # Halaman manajemen pengguna
│   ├── css/
│   │   └── app.css                              # Entry point Tailwind CSS
│   └── views/
│       ├── app.blade.php                        # Template root Inertia
│       └── reports/                             # Template Blade untuk PDF
│           ├── stock.blade.php                  # Template laporan stok
│           └── mutations.blade.php              # Template laporan mutasi
├── routes/
│   ├── web.php                                  # Rute aplikasi utama
│   ├── auth.php                                 # Rute autentikasi
│   └── console.php                              # Rute perintah Artisan
├── public/                                      # Aset publik
├── config/                                      # File konfigurasi Laravel
├── tests/                                       # Unit dan feature tests
├── composer.json                                # Dependensi PHP
├── package.json                                 # Dependensi JavaScript
├── vite.config.js                               # Konfigurasi Vite
└── tailwind.config.js                           # Konfigurasi Tailwind CSS
```

---

## Arsitektur Aplikasi

Aplikasi ini menggunakan arsitektur monolitik dengan pendekatan Server-Side Rendering (SSR) melalui Inertia.js. Inertia.js menghubungkan backend Laravel dengan frontend Vue.js tanpa memerlukan API REST terpisah, sehingga routing dan controller tetap berada di sisi server sementara tampilan dirender menggunakan komponen Vue.js.

```
┌─────────────────────────────────────────────────────┐
│                     Browser                         │
│  ┌───────────────────────────────────────────────┐  │
│  │           Vue.js 3 + Tailwind CSS             │  │
│  │    (Pages, Components, Layouts, Charts)        │  │
│  └───────────────────┬───────────────────────────┘  │
└──────────────────────┼──────────────────────────────┘
                       │ Inertia.js Protocol
┌──────────────────────┼──────────────────────────────┐
│                 Laravel 13.x                        │
│  ┌───────────────────┴───────────────────────────┐  │
│  │              Inertia Adapter                   │  │
│  ├───────────────────────────────────────────────┤  │
│  │  Middleware (Auth, CheckRole, Inertia)         │  │
│  ├───────────────────────────────────────────────┤  │
│  │  Controllers (Item, Inbound, Outbound, ...)   │  │
│  ├───────────────────────────────────────────────┤  │
│  │  Eloquent Models + Database Transactions      │  │
│  └───────────────────┬───────────────────────────┘  │
└──────────────────────┼──────────────────────────────┘
                       │
┌──────────────────────┼──────────────────────────────┐
│              MySQL 8.0 Database                     │
└─────────────────────────────────────────────────────┘
```

### Pola Desain yang Diterapkan

- **Database Transaction dengan Row Locking**: seluruh operasi yang mengubah stok barang dibungkus dalam `DB::transaction()` dengan `lockForUpdate()` untuk mencegah race condition.
- **Status-Based Stock Update**: perubahan stok hanya terjadi ketika status transaksi diubah menjadi `completed`, bukan pada saat pencatatan transaksi.
- **Role-Based Access Control (RBAC)**: middleware `CheckRole` membatasi akses fitur berdasarkan peran pengguna yang disimpan di kolom `role` pada tabel `users`.
- **Soft Validation**: validasi stok dilakukan sebelum transaksi outbound diproses untuk mencegah stok negatif.

---

## Skema Database

### Tabel `users`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint (PK) | Primary key, auto-increment |
| `name` | varchar(255) | Nama lengkap pengguna |
| `email` | varchar(255), unique | Alamat email |
| `email_verified_at` | timestamp, nullable | Waktu verifikasi email |
| `password` | varchar(255) | Password (hashed) |
| `avatar` | varchar(255), nullable | Path foto profil |
| `role` | varchar(255) | Peran: `Admin`, `Warehouse Manager`, `Staff Picker` |
| `status` | varchar(255) | Status: `Active`, `Suspended` |
| `remember_token` | varchar(100), nullable | Token remember me |
| `created_at` | timestamp | Waktu pembuatan |
| `updated_at` | timestamp | Waktu pembaruan terakhir |

### Tabel `items`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint (PK) | Primary key, auto-increment |
| `sku` | varchar(255), unique | Stock Keeping Unit |
| `name` | varchar(255) | Nama barang |
| `category` | varchar(255) | Kategori barang |
| `brand` | varchar(255), nullable | Merek |
| `quantity` | integer | Jumlah stok saat ini (default: 0) |
| `location` | varchar(255), nullable | Lokasi penyimpanan |
| `receipt_date` | date, nullable | Tanggal penerimaan |
| `origin` | varchar(255), nullable | Asal barang |
| `low_stock_threshold` | integer | Ambang batas stok rendah (default: 10) |
| `base_price` | decimal(15,2), nullable | Harga dasar |
| `photo` | varchar(255), nullable | Path foto barang |
| `description` | text, nullable | Deskripsi barang |
| `unit` | varchar(255) | Satuan barang (default: `Pcs`) |
| `created_at` | timestamp | Waktu pembuatan |
| `updated_at` | timestamp | Waktu pembaruan terakhir |

### Tabel `inbound_transactions`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint (PK) | Primary key, auto-increment |
| `item_id` | bigint (FK) | Referensi ke tabel `items` (cascade on delete) |
| `transaction_date` | date | Tanggal transaksi |
| `quantity` | integer | Jumlah barang masuk |
| `supplier` | varchar(255), nullable | Nama pemasok |
| `notes` | text, nullable | Catatan transaksi |
| `status` | varchar(255) | Status: `pending`, `completed` (default: `completed`) |
| `receipt_id` | varchar(255), nullable, unique | Nomor tanda terima (auto-generated) |
| `condition` | varchar(255) | Kondisi barang: `Good`, `Damaged` (default: `Good`) |
| `created_at` | timestamp | Waktu pembuatan |
| `updated_at` | timestamp | Waktu pembaruan terakhir |

### Tabel `outbound_transactions`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint (PK) | Primary key, auto-increment |
| `item_id` | bigint (FK) | Referensi ke tabel `items` (cascade on delete) |
| `transaction_date` | date | Tanggal transaksi |
| `quantity` | integer | Jumlah barang keluar |
| `recipient` | varchar(255), nullable | Nama penerima |
| `destination` | varchar(255), nullable | Tujuan pengiriman |
| `notes` | text, nullable | Catatan transaksi |
| `status` | varchar(255) | Status: `pending`, `completed` (default: `completed`) |
| `customer_name` | varchar(255), nullable | Nama pelanggan |
| `customer_address` | text, nullable | Alamat pelanggan |
| `courier` | varchar(255), nullable | Nama kurir |
| `estimated_delivery_date` | date, nullable | Estimasi tanggal pengiriman |
| `receipt_id` | varchar(255), nullable, unique | Nomor surat jalan (auto-generated) |
| `created_at` | timestamp | Waktu pembuatan |
| `updated_at` | timestamp | Waktu pembaruan terakhir |

### Tabel `locations`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint (PK) | Primary key, auto-increment |
| `code` | varchar(255), unique | Kode lokasi |
| `name` | varchar(255) | Nama lokasi |
| `zone_name` | varchar(255), nullable | Nama zona |
| `storage_type` | varchar(255) | Tipe: `Dry`, `Cold Storage`, `Hazardous` (default: `Dry`) |
| `capacity_percentage` | integer | Persentase kapasitas terpakai (default: 0) |
| `status` | varchar(255) | Status: `Active`, `Under Maintenance` (default: `Active`) |
| `created_at` | timestamp | Waktu pembuatan |
| `updated_at` | timestamp | Waktu pembaruan terakhir |

### Tabel Pendukung

- `password_reset_tokens` - Token reset password.
- `sessions` - Data sesi pengguna (session driver: database).
- `cache` dan `cache_locks` - Data cache aplikasi.
- `jobs`, `job_batches`, dan `failed_jobs` - Antrian pekerjaan (queue driver: database).

### Relasi Antar Tabel

```
items (1) ──── (*) inbound_transactions
items (1) ──── (*) outbound_transactions
users (1) ──── (*) sessions
```

---

## Sistem Peran dan Otorisasi

Aplikasi ini menerapkan Role-Based Access Control (RBAC) melalui middleware `CheckRole` yang memeriksa kolom `role` pada model `User`.

### Matriks Hak Akses

| Fitur | Admin | Warehouse Manager | Staff Picker |
|-------|:-----:|:-----------------:|:------------:|
| Dashboard | Ya | Ya | Ya |
| Lihat daftar barang | Ya | Ya | Ya |
| Lihat detail barang | Ya | Ya | Ya |
| Tambah/edit/hapus barang | Ya | Ya | Tidak |
| Transaksi barang masuk | Ya | Ya | Ya |
| Konfirmasi transaksi masuk | Ya | Ya | Ya |
| Transaksi barang keluar | Ya | Ya | Ya |
| Konfirmasi transaksi keluar | Ya | Ya | Ya |
| Manajemen lokasi gudang | Ya | Ya | Tidak |
| Laporan dan ekspor PDF | Ya | Ya | Tidak |
| Manajemen pengguna | Ya | Ya | Tidak |
| Profil pengguna | Ya | Ya | Ya |
| Koreksi stok manual | Ya | Ya | Tidak |

---

## Daftar Rute

### Rute Publik

| Metode | URI | Deskripsi |
|--------|-----|-----------|
| GET | `/` | Redirect ke `/login` |
| GET | `/login` | Halaman login |
| POST | `/login` | Proses login |
| GET | `/register` | Halaman registrasi |
| POST | `/register` | Proses registrasi |
| POST | `/logout` | Proses logout |

### Rute Terautentikasi (Semua Peran)

| Metode | URI | Nama Rute | Deskripsi |
|--------|-----|-----------|-----------|
| GET | `/dashboard` | `dashboard` | Halaman dashboard |
| GET | `/items` | `items.index` | Daftar barang |
| GET | `/items/{item}` | `items.show` | Detail barang |
| GET | `/inbound` | `inbound.index` | Daftar transaksi masuk |
| GET | `/inbound/create` | `inbound.create` | Form transaksi masuk baru |
| POST | `/inbound` | `inbound.store` | Simpan transaksi masuk |
| GET | `/inbound/{id}` | `inbound.show` | Detail transaksi masuk |
| POST | `/inbound/{id}/complete` | `inbound.complete` | Konfirmasi transaksi masuk |
| GET | `/outbound` | `outbound.index` | Daftar transaksi keluar |
| GET | `/outbound/create` | `outbound.create` | Form transaksi keluar baru |
| POST | `/outbound` | `outbound.store` | Simpan transaksi keluar |
| GET | `/outbound/{id}` | `outbound.show` | Detail transaksi keluar |
| POST | `/outbound/{id}/complete` | `outbound.complete` | Konfirmasi transaksi keluar |
| GET | `/profile` | `profile.edit` | Edit profil |
| PATCH | `/profile` | `profile.update` | Perbarui profil |
| DELETE | `/profile` | `profile.destroy` | Hapus akun |

### Rute Terbatas (Admin, Warehouse Manager)

| Metode | URI | Nama Rute | Deskripsi |
|--------|-----|-----------|-----------|
| GET | `/items/create` | `items.create` | Form tambah barang |
| POST | `/items` | `items.store` | Simpan barang baru |
| GET | `/items/{item}/edit` | `items.edit` | Form edit barang |
| PUT | `/items/{item}` | `items.update` | Perbarui barang |
| DELETE | `/items/{item}` | `items.destroy` | Hapus barang |
| GET | `/reports` | `reports.index` | Halaman laporan |
| POST | `/reports/stock/pdf` | `reports.stock.pdf` | Ekspor laporan stok (PDF) |
| POST | `/reports/mutations/pdf` | `reports.mutations.pdf` | Ekspor laporan mutasi (PDF) |
| GET | `/locations` | `locations.index` | Daftar lokasi |
| GET | `/locations/create` | `locations.create` | Form tambah lokasi |
| POST | `/locations` | `locations.store` | Simpan lokasi baru |
| GET | `/locations/{location}/edit` | `locations.edit` | Form edit lokasi |
| PUT | `/locations/{location}` | `locations.update` | Perbarui lokasi |
| DELETE | `/locations/{location}` | `locations.destroy` | Hapus lokasi |
| GET | `/users` | `users.index` | Daftar pengguna |
| GET | `/users/create` | `users.create` | Form tambah pengguna |
| POST | `/users` | `users.store` | Simpan pengguna baru |
| GET | `/users/{user}/edit` | `users.edit` | Form edit pengguna |
| PUT | `/users/{user}` | `users.update` | Perbarui pengguna |
| DELETE | `/users/{user}` | `users.destroy` | Hapus pengguna |

---

## Modul Aplikasi

### Dashboard (`/dashboard`)
Menampilkan ringkasan operasional gudang secara real-time, meliputi total stok, utilisasi kapasitas, volume transaksi harian, jumlah tugas pending, grafik tren 7 hari, daftar barang paling aktif, dan peringatan stok rendah.

### Master Barang (`/items`)
Mengelola data induk barang. Setiap barang memiliki SKU unik sebagai identifikasi. Stok barang dihitung secara otomatis berdasarkan transaksi masuk dan keluar yang berstatus `completed`. Penambahan stok hanya dapat dilakukan melalui modul transaksi barang masuk, kecuali untuk koreksi manual oleh Admin atau Warehouse Manager.

### Transaksi Masuk (`/inbound`)
Mencatat setiap penerimaan barang ke gudang. Setiap transaksi mendapatkan nomor tanda terima (receipt ID) secara otomatis. Transaksi dapat disimpan dengan status `pending` terlebih dahulu dan dikonfirmasi kemudian, yang memungkinkan alur persetujuan sebelum stok diperbarui.

### Transaksi Keluar (`/outbound`)
Mencatat setiap pengeluaran barang dari gudang. Sistem memvalidasi ketersediaan stok sebelum memproses transaksi. Setiap transaksi mendapatkan nomor surat jalan (delivery order) secara otomatis dan menyimpan informasi lengkap pengiriman.

### Lokasi Gudang (`/locations`)
Mengelola data lokasi penyimpanan fisik di dalam gudang. Setiap lokasi memiliki kode unik, dapat dikelompokkan ke dalam zona, dan dikategorikan berdasarkan tipe penyimpanan.

### Laporan (`/reports`)
Menyediakan dua jenis laporan yang dapat diekspor ke format PDF:
- **Laporan Stok**: menampilkan posisi stok seluruh barang pada saat laporan dibuat.
- **Laporan Mutasi**: menampilkan seluruh transaksi masuk dan keluar dalam periode waktu yang ditentukan.

### Manajemen Pengguna (`/users`)
Mengelola akun pengguna aplikasi. Setiap pengguna memiliki peran yang menentukan hak akses terhadap fitur-fitur aplikasi. Akun dapat dinonaktifkan dengan mengubah status menjadi `Suspended`.

---

## Lisensi

Proyek ini dikembangkan untuk keperluan internal SMKN 20 Jakarta.
