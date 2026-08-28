# Task List: Warehouse Manager Maintenance Mode

## Task 1: Database Migration & Model for System Settings
**Description:** Buat migrasi tabel `system_settings` dengan struktur key-value dan Model `SystemSetting`.

**Acceptance criteria:**
- [ ] Tabel `system_settings` memiliki kolom: `key` (unique, string), `value` (longtext/json nullable), timestamps.
- [ ] Model `App\Models\SystemSetting` dibuat dengan method helper `get($key, $default)` dan `set($key, $value)`.

**Verification:**
- [ ] Run migration: `php artisan migrate`
- [ ] Unit test create & retrieve setting value

**Dependencies:** None
**Files touched:**
- `database/migrations/xxxx_xx_xx_create_system_settings_table.php`
- `app/Models/SystemSetting.php`
**Estimated scope:** Small (2 files)

---

## Task 2: Maintenance Mode Middleware
**Description:** Buat middleware `CheckMaintenanceMode` untuk mencegat request pengguna saat maintenance aktif.

**Acceptance criteria:**
- [ ] Middleware memeriksa status `maintenance_mode` dari cache/database.
- [ ] Mengizinkan akses jika: maintenance OFF, route login/logout, route healthcheck `up`, atau user memiliki `role === 'Warehouse Manager'`.
- [ ] Me-render halaman `Maintenance` (status 503) untuk pengguna non-manager saat maintenance ON.

**Verification:**
- [ ] Daftarkan middleware di `bootstrap/app.php`
- [ ] Test request dengan user non-manager mengembalikan 503

**Dependencies:** Task 1
**Files touched:**
- `app/Http/Middleware/CheckMaintenanceMode.php`
- `bootstrap/app.php`
**Estimated scope:** Small (2 files)

---

## Task 3: Backend Controller & Routes for Maintenance Settings
**Description:** Buat controller dan endpoint bagi Warehouse Manager untuk mengubah status dan pesan pemeliharaan.

**Acceptance criteria:**
- [ ] Endpoint `GET /maintenance-settings` merender UI pengaturan pemeliharaan.
- [ ] Endpoint `POST /maintenance-settings` memvalidasi input (`is_enabled`, `message`, `estimated_finish`) dan menyimpan ke database.
- [ ] Route hanya bisa diakses oleh `role:Warehouse Manager`.

**Verification:**
- [ ] Route list mencatat rute maintenance-settings
- [ ] Test status code 200 untuk manager, 403 untuk non-manager

**Dependencies:** Task 1, Task 2
**Files touched:**
- `app/Http/Controllers/MaintenanceController.php`
- `routes/web.php`
**Estimated scope:** Small (2 files)

---

## Checkpoint: Backend Foundation
- [ ] Migrasi berhasil
- [ ] Endpoint backend dan middleware berfungsi dengan otorisasi role

---

## Task 4: Maintenance View Page (Frontend)
**Description:** Buat halaman tampilan pemeliharaan yang modern untuk pengguna umum.

**Acceptance criteria:**
- [ ] Halaman `resources/js/Pages/Maintenance.vue` menampilkan ilustrasi/ikon maintenance, pesan kustom, estimasi selesai, dan tombol "Login Warehouse Manager".
- [ ] Desain konsisten dengan UI WMS SMKN 20 (terracotta theme, tipografi rapi, responsive).

**Verification:**
- [ ] `npm run build` sukses
- [ ] Visual inspection saat maintenance aktif

**Dependencies:** Task 2
**Files touched:**
- `resources/js/Pages/Maintenance.vue`
**Estimated scope:** Small (1 file)

---

## Task 5: Maintenance Settings UI & Manager Warning Banner
**Description:** Buat halaman pengaturan pemeliharaan untuk Warehouse Manager dan tambahkan indikator/banner di sidebar & layout.

**Acceptance criteria:**
- [ ] Halaman `resources/js/Pages/Settings/Maintenance.vue` memiliki toggle switch ON/OFF, form pesan kustom, dan estimasi waktu.
- [ ] Menu "Mode Pemeliharaan" muncul di sidebar khusus Warehouse Manager di `AuthenticatedLayout.vue`.
- [ ] Floating Warning Banner merah/oranye muncul di atas layout jika maintenance mode sedang aktif.

**Verification:**
- [ ] Toggle switch mengubah status secara instan
- [ ] Warning banner terlihat oleh manager saat mode aktif

**Dependencies:** Task 3, Task 4
**Files touched:**
- `resources/js/Pages/Settings/Maintenance.vue`
- `resources/js/Layouts/AuthenticatedLayout.vue`
- `app/Http/Middleware/HandleInertiaRequests.php`
**Estimated scope:** Medium (3 files)

---

## Task 6: Feature Testing & Build Verification
**Description:** Buat automated test untuk skenario end-to-end mode pemeliharaan dan build asset produksi.

**Acceptance criteria:**
- [ ] Automated Feature Test menguji akses saat maintenance OFF vs ON untuk role yang berbeda.
- [ ] `npm run build` dan `php artisan test` lulus 100%.

**Verification:**
- [ ] `php artisan test --filter=MaintenanceModeTest` lulus
- [ ] `npm run build` berhasil tanpa error

**Dependencies:** Task 1 - Task 5
**Files touched:**
- `tests/Feature/MaintenanceModeTest.php`
**Estimated scope:** Small (1-2 files)
