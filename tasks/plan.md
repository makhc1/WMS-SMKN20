# Implementation Plan: Warehouse Manager Maintenance Mode System

## Overview
Fitur ini memungkinkan pengguna dengan role **Warehouse Manager** untuk mengaktifkan atau menonaktifkan **Mode Pemeliharaan (Maintenance Mode)** langsung melalui antarmuka web (UI). Ketika mode pemeliharaan aktif, seluruh staf dan pengunjung biasa tidak dapat mengakses fitur operasional dan akan diarahkan ke halaman pemeliharaan (*Maintenance Page*), sementara Warehouse Manager tetap memiliki akses penuh untuk melakukan perbaikan, audit, maupun mematikan kembali mode pemeliharaan.

---

## Architecture Decisions

1. **Storage & State Persistence**:
   - Menggunakan tabel `system_settings` (Key-Value store di database) dengan caching (`Cache::rememberForever`) untuk efisiensi tinggi tanpa membebani query database di setiap request.
   - Menyimpan atribut:
     - `maintenance_mode`: boolean (`true`/`false`)
     - `maintenance_message`: string (Pesan kustom alasan pemeliharaan)
     - `maintenance_end_time`: datetime (Estimasi waktu selesai pemeliharaan)
     - `maintenance_updated_by`: ID Warehouse Manager yang mengubah status.

2. **Middleware Interception (`CheckMaintenanceMode`)**:
   - Ditambahkan ke web middleware stack di `bootstrap/app.php`.
   - **Bypass Rule**:
     - Request menuju route autentikasi (`/login`, `/logout`, `/up`, asset static/vite) tetap diizinkan.
     - Pengguna terautentikasi dengan `role === 'Warehouse Manager'` dibebaskan dari pembatasan dan dapat mengakses seluruh aplikasi seperti biasa.
   - **Blocking Rule**:
     - Pengguna selain Warehouse Manager (Staf, Admin biasa, tamu yang mengakses route publik) akan dirender ke halaman `Maintenance.vue` dengan HTTP status code `503 Service Unavailable`.

3. **Inertia Shared State & Floating Banner**:
   - `HandleInertiaRequests.php` membagikan status `is_maintenance` dan `maintenance_info` ke seluruh halaman.
   - Ketika maintenance aktif, Warehouse Manager akan melihat *top floating warning banner* ("⚠️ Mode Pemeliharaan Sedang Aktif") di `AuthenticatedLayout.vue` agar selalu sadar bahwa sistem sedang terkunci untuk publik.

4. **UI & Control Panel**:
   - Halaman **Pengaturan Sistem** (`/settings` atau tab di sidebar Warehouse Manager) dengan switch toggle, editor pesan pengumuman, dan estimasi waktu.
   - Halaman **Maintenance** (`resources/js/Pages/Maintenance.vue`) yang estetik dengan tema terracotta SMKN 20, timer estimasi, dan tombol login darurat untuk manager.

---

## Task List

### Phase 1: Database & Model Foundation
- [ ] Task 1: Buat migrasi tabel `system_settings` dan Model `SystemSetting`.
- [ ] Task 2: Buat `SettingService` / Helper untuk mempermudah get/set setting dengan cache invalidation.

### Checkpoint 1: Foundation
- [ ] Migrasi database berjalan lancar & method get/set unit-tested.

### Phase 2: Middleware & Backend Logic
- [ ] Task 3: Buat `CheckMaintenanceMode` middleware dan daftarkan ke `bootstrap/app.php`.
- [ ] Task 4: Buat `SettingController` untuk route update status maintenance (khusus Warehouse Manager).
- [ ] Task 5: Inject data status maintenance ke Inertia Share props di `HandleInertiaRequests.php`.

### Checkpoint 2: Backend & Middleware
- [ ] Pengujian rute: Warehouse Manager lolos akses, role lain terblokir saat maintenance ON.

### Phase 3: Frontend Views & Interactive Components
- [ ] Task 6: Buat halaman `resources/js/Pages/Maintenance.vue` (Tampilan saat web maintenance).
- [ ] Task 7: Buat halaman/modal **Pengaturan Pemeliharaan** (`resources/js/Pages/Settings/Maintenance.vue`) untuk Warehouse Manager.
- [ ] Task 8: Tambahkan menu sidebar "Mode Pemeliharaan / Pengaturan" di `AuthenticatedLayout.vue` dan Warning Banner saat aktif.

### Checkpoint 3: End-to-End Verification
- [ ] Warehouse Manager dapat menyalakan mode maintenance.
- [ ] Logout / buka browser lain sebagai user biasa -> muncul halaman maintenance 503.
- [ ] Login kembali sebagai Warehouse Manager -> web terbuka normal dengan banner pengingat.
- [ ] Warehouse Manager mematikan maintenance -> akses user lain kembali pulih normal.
- [ ] Semua automated test suite lulus.

---

## Risks and Mitigations

| Risk | Impact | Mitigation |
| :--- | :---: | :--- |
| **Warehouse Manager terkunci di luar sistem saat maintenance aktif** | Tinggi | Rute `/login` dan proses autentikasi selalu dikecualikan dari middleware maintenance sehingga manager tetap bisa login kapan saja. |
| **Beban performa query pengecekan setting di setiap request** | Sedang | Menggunakan `Cache::rememberForever` dengan key `system_setting:maintenance_mode` yang di-*flush* saat toggle diubah. |
| **CSRF / Unauthorized access ke endpoint toggle** | Tinggi | Endpoint pembaruan status diproteksi ketat dengan `Route::middleware(['auth', 'role:Warehouse Manager'])` dan CSRF token. |

---

## Open Questions
- Apakah estimasi waktu selesai (*countdown timer*) wajib diisi atau opsional? *(Dibuat opsional dengan nilai default)*.
