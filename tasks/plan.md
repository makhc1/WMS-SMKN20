# Implementation Plan: WMS SMKN 20 (Dual-Mode Scanner)

## Overview
Membangun aplikasi WMS dengan fokus pada kecepatan Inbound dan Outbound menggunakan dukungan Barcode Scanner Tembak (USB/Bluetooth) sebagai antarmuka utama, serta Webcam sebagai fallback.

## Architecture Decisions
- **Vertical Slicing:** Kita tidak akan membangun "seluruh database lalu seluruh UI". Kita akan membangun satu fitur utuh dari DB ke UI (Inbound dulu, baru Outbound).
- **Scanner UI:** Menggunakan Alpine.js untuk *debounce* (mencegah double submit dari scanner fisik) dan memberikan umpan balik suara (*beep*).
- **Webcam Integrasi:** Menambahkan `html5-qrcode` di akhir pembangunan (Phase 4) agar tidak mengganggu alur pengembangan utama (scanner fisik).

## Task List

### Phase 1: Foundation
- [ ] Task 1: Setup Database & Models (Locations, Items)

### Phase 2: Inbound Flow
- [ ] Task 2: Inbound Controller & UI (Physical Scanner Ready)

### Checkpoint 1: Inbound Works
- [ ] Bisa melakukan migrate database.
- [ ] Tembakan dari scanner fisik masuk ke database tanpa *double submit*.

### Phase 3: Outbound Flow
- [ ] Task 3: Outbound Controller & Picking List UI

### Checkpoint 2: Outbound Works
- [ ] Picking list tampil di layar.
- [ ] Menembak barcode dari picking list otomatis mencentang status barang.

### Phase 4: Hybrid Fallback
- [ ] Task 4: Integrasi Webcam Scanner (html5-qrcode)

### Checkpoint 3: Complete
- [ ] Semua kriteria sukses di Spec-Document terpenuhi.
- [ ] Aplikasi siap digunakan/di-deploy.

## Risks and Mitigations
| Risk | Impact | Mitigation |
|------|--------|------------|
| Scanner fisik terlalu cepat menembak (Double submit) | High | Gunakan Alpine.js `x-model.debounce` atau matikan input sejenak saat *request* berjalan. |
| Kamera di browser diblokir HTTPS | Med | Deploy menggunakan HTTPS/SSL, atau gunakan Localhost tunneling (ngrok/laragon SSL) saat tes. |

## Open Questions
- Tidak ada untuk saat ini.
