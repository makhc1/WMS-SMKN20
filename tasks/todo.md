## Task 1: Setup Database & Models
**Description:** Membuat skema database untuk merekam lokasi gudang dan data barang/paket yang di-scan.
**Acceptance criteria:**
- [ ] Tabel `locations` (id, name, code) terbuat.
- [ ] Tabel `items` (id, tracking_number, status, location_id, timestamps) terbuat.
- [ ] Model Eloquent untuk Item dan Location siap digunakan.
**Verification:**
- [ ] Tests pass: `php artisan migrate` berjalan tanpa error.
- [ ] Manual check: Tabel terlihat di phpMyAdmin/HeidiSQL Laragon.
**Dependencies:** None
**Files likely touched:** `database/migrations/*`, `app/Models/*`
**Estimated scope:** Small

## Task 2: Inbound Controller & UI (Physical Scanner)
**Description:** Membuat halaman Inbound di mana operator bisa menembak resi menggunakan scanner fisik berulang kali.
**Acceptance criteria:**
- [ ] Input field secara otomatis *focus* saat halaman dibuka.
- [ ] Ketikan *enter* (dari scanner) otomatis menyimpan data ke DB via AJAX/Fetch API.
- [ ] Mencegah *double submit* (debounce) dan memberi suara *beep* sukses.
**Verification:**
- [ ] Manual check: Bisa scan 3 barang berturut-turut dengan cepat dan ketiganya masuk DB.
**Dependencies:** Task 1
**Files likely touched:** `routes/web.php`, `app/Http/Controllers/InboundController.php`, `resources/views/inbound.blade.php`
**Estimated scope:** Medium

## Checkpoint 1: Inbound Works
- [ ] Data tersimpan, *debounce* berfungsi. Review dengan user.

## Task 3: Outbound Controller & Picking List UI
**Description:** Membuat halaman Outbound yang menampilkan daftar barang yang harus diambil (Picking List) dan memproses pengeluarannya.
**Acceptance criteria:**
- [ ] Menampilkan daftar barang dengan status 'in_warehouse'.
- [ ] Kolom input scanner untuk menembak barang yang diambil.
- [ ] Barang yang berhasil di-scan statusnya berubah (misal 'picked') dan UI ter-update tanpa *reload* seluruh halaman (Alpine.js/Fetch).
**Verification:**
- [ ] Manual check: Scan barcode, baris di Picking List otomatis tercoret/hilang.
**Dependencies:** Task 2
**Files likely touched:** `app/Http/Controllers/OutboundController.php`, `resources/views/outbound.blade.php`
**Estimated scope:** Medium

## Checkpoint 2: Outbound Works
- [ ] Alur gudang dari Inbound ke Outbound berjalan mulus menggunakan scanner fisik.

## Task 4: Integrasi Webcam Scanner (Fallback)
**Description:** Menambahkan UI *viewfinder* kamera menggunakan `html5-qrcode` di halaman Inbound dan Outbound.
**Acceptance criteria:**
- [ ] Klik area viewfinder menyalakan kamera.
- [ ] Berhasil scan dari kamera memicu fungsi *submit* yang sama dengan fungsi scanner fisik.
**Verification:**
- [ ] Manual check: Scan QR code/barcode di HP menggunakan webcam terdeteksi dan tersimpan ke sistem.
**Dependencies:** Task 2, Task 3
**Files likely touched:** `resources/views/inbound.blade.php`, `resources/views/outbound.blade.php`
**Estimated scope:** Medium

## Checkpoint 3: Complete
- [ ] Kedua mode scanner berfungsi harmonis. Selesai.
