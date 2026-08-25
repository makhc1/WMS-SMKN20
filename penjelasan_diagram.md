# Penjelasan Alur Diagram Manajemen Logistik WMS

Dokumen ini berisi penjelasan naratif dari diagram-diagram yang telah dibuat untuk sistem *Warehouse Management System* (WMS) agar mudah dibaca dan dipahami oleh anggota tim untuk kebutuhan penulisan laporan atau presentasi.

## 1. Penjelasan Business Process (Proses Bisnis)
Proses bisnis aplikasi WMS ini dibagi menjadi dua siklus utama:
- **Penerimaan Barang (Inbound):** Alur dimulai saat Staf Gudang menerima barang dari *supplier*. Staf mengecek kesesuaian fisik barang. Jika tidak sesuai, barang akan diretur (dikembalikan). Jika sesuai, staf akan mencatat penerimaan tersebut ke dalam sistem. Sistem lalu memproses penambahan stok barang secara otomatis. Terakhir, staf menyimpan barang tersebut di lokasi rak yang telah ditentukan.
- **Pengeluaran Barang (Outbound):** Alur ini memproses permintaan pengeluaran barang. Staf membuat dokumen *Outbound*. Sistem kemudian melakukan validasi untuk memastikan jumlah stok mencukupi. Jika stok kurang, sistem menolak dan memberikan peringatan kepada staf. Jika stok cukup, sistem akan mengunci stok (mengurangi jumlah kuantitas) dan mengizinkan staf mencetak Surat Jalan (*Picking List*) sebagai bukti pengeluaran barang.

## 2. Penjelasan Use Case Diagram
Diagram ini memetakan apa saja fungsi yang bisa dilakukan oleh pengguna berdasarkan hak aksesnya. Terdapat 3 peran (aktor) utama:
- **Staf Gudang:** Bertugas menangani operasional harian di lapangan. Mereka bisa login, mengelola master barang, serta mencatat transaksi barang masuk (*Inbound*) dan barang keluar (*Outbound*).
- **Admin:** Memiliki hak akses penuh ke keseluruhan sistem. Selain kelola barang dan melihat laporan, tugas spesifik Admin adalah mengelola akun pengguna (menambah, mengedit, atau menghapus hak akses).
- **Warehouse Manager (Manager Gudang):** Bertugas mengawasi operasional secara umum. Manager dapat mengakses master barang dan sangat berfokus pada pemantauan operasional melalui menu Dashboard & Cetak Laporan tanpa harus terlibat pada pencatatan rutin keluar-masuk barang.

## 3. Penjelasan Activity Diagram
Activity diagram ini difokuskan pada contoh skenario pembuatan *Outbound* (Barang Keluar) dengan interaksi dua arah antara **Staf Gudang** dan **Sistem**:
1. Staf gudang melakukan login dan mengakses menu Barang Keluar.
2. Staf mencari item yang ingin dikeluarkan (bisa juga dengan fitur pemindai *barcode/QR*).
3. Staf memasukkan jumlah kuantitas yang diminta beserta data pihak penerima.
4. Setelah data di- *submit*, **Sistem WMS mengambil alih** untuk melakukan verifikasi sisa stok di database.
5. Jika stok di database kurang dari permintaan, sistem akan memblokir transaksi dan menampilkan pesan error kembali kepada staf.
6. Jika stok cukup, sistem lanjut memproses dengan menyimpan rekaman transaksi *Outbound* dan langsung memotong total kuantitas barang secara *real-time*.
7. Setelah penyimpanan selesai, staf diarahkan oleh sistem untuk mencetak dokumen Surat Jalan (*Picking List*).

## 4. Penjelasan Class Diagram
Class diagram merepresentasikan arsitektur kode (Model) dalam bahasa pemrograman yang digunakan untuk *backend*:
- **Class User:** Menangani autentikasi, menyimpan data identitas, dan wewenang (Role) pengguna. Class ini memiliki metode seperti `login()` dan `logout()`.
- **Class Item:** Merupakan *blueprint* untuk master data barang. Menyimpan atribut penting seperti `sku` (kode unik) serta batasan stok (`min_stock`). Class ini juga memegang fungsi-fungsi pengecekan seperti memeriksa sisa stok (`checkStockLevel()`).
- **Class InboundTransaction & OutboundTransaction:** Entitas logikal yang merekam riwayat pergerakan (mutasi) barang. Menyimpan data kuantitas, tanggal masuk/keluar, asal/tujuan barang, serta metode pemrosesannya (`processInbound()`/`processOutbound()`).
- **Hubungan Asosiasi:** Ditunjukkan bahwa satu `User` dapat menciptakan (*creates*) banyak transaksi. Sementara itu, satu `Item` juga dapat tergabung (*includes*) dalam banyak transaksi Inbound dan Outbound.

## 5. Penjelasan Entity Relationship Diagram (ERD)
ERD mendefinisikan rancangan relasi database secara konseptual:
- Terdiri dari 4 entitas utama: `USERS`, `ITEMS`, `INBOUND_TRANSACTIONS`, dan `OUTBOUND_TRANSACTIONS`.
- **Relasi USERS ke Transaksi (One-to-Many):** Setiap satu *User* bisa melakukan puluhan/ratusan transaksi. Relasi ini ditambahkan untuk menunjang keamanan (*audit trail*), sehingga selalu jelas siapa staf yang melakukan pencatatan.
- **Relasi ITEMS ke Transaksi (One-to-Many):** Setiap jenis *Item* bisa memiliki banyak riwayat barang masuk dan barang keluar.
- Seluruh relasi telah terhubung penuh, sehingga aplikasi dapat menelusuri secara presisi riwayat siklus masing-masing barang dan divalidasi siapa yang memprosesnya.

## 6. Penjelasan Logical Record Structure (LRS)
LRS menggambarkan wujud fisik dari ERD ketika sudah diimplementasikan menjadi deretan tabel database (seperti MySQL/PostgreSQL):
- Tabel `users` memiliki PK (*Primary Key*) pada kolom `id`.
- Tabel `items` memiliki PK pada `id` dengan aturan pengenal unik tambahan di kolom `sku`.
- Tabel `inbound_transactions` maupun `outbound_transactions` berperan sebagai *Child Table*. Kedua tabel transaksi tersebut menyematkan nilai tamu (FK / *Foreign Key*) dari tabel lain, yaitu `user_id` (menyambung ke tabel `users`) dan `item_id` (menyambung ke tabel `items`).
- Garis panduan relasi (panah) dengan jelas ditarik dari *Primary Key* milik tabel induk menuju *Foreign Key* milik tabel transaksi, membuktikan bahwa keseluruhan entitas saling melengkapi satu sama lain.
