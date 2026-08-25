# 📦 Dokumen Spesifikasi & Detail Fitur WMS Berdikari Jaya
Sistem Manajemen Gudang (*Warehouse Management System*) terpadu untuk pemantauan stok, penerimaan, pengeluaran, dan pelaporan logistik.

---

## 1. 🔐 Autentikasi & Keamanan (Security & Access)
Modul keamanan untuk membatasi hak akses operasional.
*   **Sign In (`/login`)**
    *   **Field Data:** Email, Password.
    *   **Fitur Ekstra:** *Remember Me*, *Show/Hide Password*, Validasi *Error* (kredensial salah).
*   **Register (`/register`)**
    *   **Field Data:** Nama Lengkap, Email, Password, Konfirmasi Password.
*   **Session & Middleware**
    *   Sistem memblokir akses ke URL `/dashboard` dan rute internal lainnya jika pengguna belum masuk.

---

## 2. 📊 Dashboard Utama (`/dashboard`)
Pusat kendali operasional (Control Center) untuk memantau aktivitas gudang secara *real-time*.

*   **Top level Metrics (Statistik Cepat):**
    *   **Total Stock:** Kalkulasi akumulatif semua barang yang ada di gudang.
    *   **Inbound Today:** Jumlah unit barang yang masuk pada hari ini.
    *   **Outbound Today:** Jumlah unit barang yang keluar pada hari ini.
    *   **Low Stock Alert:** Indikator merah / *pulse* untuk barang yang hampir habis.
*   **Visualisasi Data (Charts):**
    *   **Arus Barang Mingguan (Line Chart):** Menampilkan tren barang masuk vs barang keluar selama 7 hari terakhir.
    *   **Distribusi Kategori (Pie Chart):** Persentase stok berdasarkan kategori barang (mis. Elektronik, Makanan, dsb).
*   **Tabel Peringatan Stok Menipis (Low Stock Notifier):**
    *   Menampilkan data `Item`, `SKU`, `Stock Tersisa`, dan tombol `Action` (opsi untuk auto-order / re-stock).

---

## 3. 📦 Master Data Produk (`/master` / `/product`)
Pusat pengelolaan katalog dan basis data SKU barang di dalam gudang.

*   **Tabel Master Data:**
    *   Kolom yang ditampilkan: Foto Produk, SKU, Nama Barang, Kategori, Harga Dasar, dan Total Stok.
    *   *Badge Status*: **In Stock** (Hijau), **Low Stock** (Kuning), **Out of Stock** (Merah).
*   **Fitur Aksi (CRUD):**
    *   **Tambah Data (Add Item):** Form mencakup Input SKU (bisa auto-generate), Nama Barang, Deskripsi, Satuan (Pcs/Box/Kg), Kategori, Batas Minimum Stok (Reorder Point), dan Unggah Foto.
    *   **Edit Data:** Mengubah atribut barang yang sudah ada.
    *   **Hapus Data:** Menghapus SKU dari sistem (biasanya dicegah jika masih ada stok fisik).
*   **Pencarian & Filter:**
    *   Kolom pencarian *real-time* berdasarkan SKU atau Nama Barang.
    *   Filter berdasarkan Kategori.

---

## 4. 📥 Inbound (Penerimaan Barang Masuk) (`/inbound`)
Modul operasional untuk mencatat kedatangan barang dari *supplier* ke gudang.

*   **Live Barcode Scanner (Kamera):**
    *   Fitur pemindaian langsung (mengakses *webcam* atau kamera *device*).
    *   Auto-fill SKU ke dalam form setelah berhasil di-scan.
*   **Formulir Inbound:**
    *   **Field Data:** SKU Barang, Nama Barang, Jumlah (Qty) Masuk, Nama Supplier, Tanggal Kedatangan, dan Kondisi Barang (*Good* / *Damaged*).
    *   *Auto-calculate*: Stok di Master Data akan otomatis bertambah saat Inbound diproses.
*   **Inbound History Table (Riwayat Penerimaan):**
    *   Kolom: ID Penerimaan (Receipt ID), Waktu Masuk, Item, Qty, Supplier, dan Petugas Penerima.

---

## 5. 📤 Outbound (Pengeluaran Barang) (`/outbound`)
Modul untuk memproses pengiriman / pengeluaran barang dari gudang ke tujuan.

*   **Pembuatan Pengiriman (Create Shipment):**
    *   **Data Tujuan:** Nama Customer, Alamat Pengiriman, Ekspedisi/Kurir, dan Estimasi Tanggal Kirim.
*   **Picking List (Daftar Pengambilan):**
    *   Petugas dapat memilih SKU apa saja yang akan dikeluarkan.
    *   Input Qty yang dikeluarkan. Sistem akan memvalidasi apakah stok gudang cukup.
    *   *Auto-calculate*: Stok di Master Data otomatis berkurang setelah Outbound dikonfirmasi.
*   **Document Generator (Surat Jalan):**
    *   Mencetak (*Print/PDF*) dokumen Surat Jalan resmi (Delivery Note).
    *   Mencakup detail Pengirim, Penerima, List Barang, dan Barcode Surat Jalan untuk dilacak oleh kurir.

---

## 6. 🏢 Manajemen Lokasi Gudang (`/warehouses`)
Modul untuk memetakan ruang penyimpanan / tata letak fisik gudang.

*   **Tabel Area & Rak:**
    *   Kolom: Kode Area (mis. A-01, B-02), Nama Zona, Tipe Penyimpanan (*Dry*, *Cold Storage*, *Hazardous*), dan Kapasitas Terpakai (dalam %).
*   **Kapasitas Visual:**
    *   Progress bar (indikator warna: Hijau untuk lega, Merah untuk penuh).
*   **Formulir Lokasi:**
    *   Penambahan rak baru, pengubahan kapasitas maksimal, dan pengaturan Status (*Active* atau *Under Maintenance*).

---

## 7. 👥 Manajemen Pengguna & Staf (`/users`)
Pusat kontrol hak akses untuk karyawan yang menggunakan sistem.

*   **Daftar Pengguna:**
    *   Menampilkan Foto Profil/Avatar, Nama Lengkap, Email, Role, dan Tanggal Bergabung.
*   **Role Management (Hak Akses):**
    *   Pengaturan jabatan (Misalnya: *Admin*, *Warehouse Manager*, *Staff Picker*).
*   **Security Actions:**
    *   Fitur menonaktifkan (*Suspend*) akun.
    *   *Reset Password* untuk staf yang lupa sandi.

---
