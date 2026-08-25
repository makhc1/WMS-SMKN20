A. Modul Autentikasi | No | Skenario Pengujian | Aksi Pengguna (Input) | Ekspektasi
Sistem | Status | |:—|:—|:—|:—|:—:| | 1 | Login dengan kredensial valid | Memasukkan Email dan
Password benar, klik “Login”. | Sistem mengarahkan user ke halaman Dashboard utama. |
PASS | | 2 | Login dengan password salah | Memasukkan Email benar, Password salah. | Sistem
menolak akses dan memunculkan alert peringatan merah. | PASS | | 3 | Akses halaman Admin
oleh Staf | User login sebagai Staf, lalu memaksa mengetik URL /users di browser. | Sistem
memblokir akses dan mengarahkan kembali ke Dashboard (Error 403 Forbidden). | PASS |
B. Modul Manajemen Transaksi Logistik | No | Skenario Pengujian | Aksi Pengguna
(Input) | Ekspektasi Sistem | Status | |:—|:—|:—|:—|:—:| | 4 | Input Outbound melebihi stok | User
menginput form keluar barang “Buku Tulis” sebanyak 100 pcs, padahal sisa stok hanya 10 pcs.
| Sistem menolak menyimpan dan memunculkan error batas stok tidak cukup. Stok di gudang
tidak berubah. | PASS | | 5 | Input Inbound berhasil | User memindai SKU barang “Kardus”,
mengisi kuantitas 50, dan menyimpannya. | Sistem menampilkan notifikasi sukses hijau. Stok
total barang “Kardus” otomatis bertambah 50 di database. | PASS | | 6 | Required Fields (Form
Kosong) | User menekan tombol “Simpan Transaksi” tanpa mengisi kolom ‘Nama Penerima’.
| Sistem menahan proses submit dan memberi warna merah pada kolom inputan yang wajib
diisi. | PASS |
Catatan: Seluruh hasil pengujian pada iterasi terakhir menunjukkan status “Pass” 