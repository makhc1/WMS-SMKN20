# Spec: WMS SMKN 20 (Hybrid Scanner UI)

## Objective
Membangun WMS (Warehouse Management System) berbasis web untuk operasional jurusan Manajemen Logistik SMKN 20 Jakarta. Sistem difokuskan pada alur kerja fundamental (Inbound & Outbound) dengan efisiensi tinggi layaknya standar JNE.
Target utama perangkat adalah **Komputer (Desktop/Laptop)**. 

Aplikasi ini mendukung **Dual-Mode Scanning** secara bersamaan:
1. **Mode Utama (Physical Scanner):** Dukungan penuh untuk alat Barcode Scanner Tembak (USB/Bluetooth). UI memiliki kolom input besar yang siap menampung ketikan dari *scanner* secara instan.
2. **Mode Fallback (Webcam):** Sebagai cadangan (*jaga-jaga* jika alat rusak/tidak ada), UI tetap menyediakan *border/viewfinder* kamera yang tertanam di halaman web (menggunakan webcam PC/Laptop).

## Tech Stack
- **Backend:** PHP 8.x, Laravel 11
- **Frontend:** Blade Templates, Tailwind CSS, Alpine.js (untuk state interaktif dan event listener scanner)
- **Database:** MySQL
- **Scanner UI (Fallback):** `html5-qrcode` (Library JS untuk akses webcam & decode barcode di browser)

## Commands
- Instalasi awal: `composer install`, `npm install`
- Dev server: `php artisan serve`
- Frontend build: `npm run dev`
- Migrasi DB: `php artisan migrate --seed`
- Test: `php artisan test`

## Project Structure
```text
app/
  Models/        -> Model Item, InboundTransaction, OutboundTransaction, dll
  Http/
    Controllers/ -> Controller logika Inbound & Outbound
resources/
  views/
    inbound/     -> Halaman Inbound (Input Field Auto-focus + Webcam Viewfinder)
    outbound/    -> Halaman Outbound & Picking List (Input Field + Webcam Viewfinder)
docs/            -> Dokumen spesifikasi dan plan
```

## Code Style
```php
// Backend Laravel
public function storeInbound(Request $request)
{
    $validated = $request->validate([
        'tracking_number' => 'required|string|unique:items',
        'location_id'     => 'required|exists:locations,id',
    ]);

    Item::create($validated);
    return response()->json(['status' => 'success']);
}
```

## Testing Strategy
- Menggunakan **PHPUnit** (bawaan Laravel).
- Test pengulangan: Mencegah *double scan* (input dari scanner tembak sangat cepat, *debounce* di frontend dan validasi unik di backend sangat krusial).
- Validasi relasi data (Outbound harus sinkron dengan Picking List).

## Boundaries
- **Always do:** Menggunakan *debounce* di Alpine.js untuk kolom input agar tidak *double submit* akibat *scanner* fisik, serta memberi notifikasi suara (beep sukses/gagal).
- **Ask first:** Mengubah arsitektur mode ganda ini (misal memisahkan halamannya). Keduanya harus hidup berdampingan di satu layar.
- **Never do:** Memaksa pengguna membuka halaman baru/popup hanya untuk beralih antara *scanner* fisik dan *webcam*.

## Success Criteria
1. Halaman Inbound dan Outbound memiliki **Kolom Input Teks** yang otomatis aktif (*auto-focus*), sehingga operator bisa langsung menggunakan alat Barcode Scanner Tembak untuk memproses barang berulang-ulang tanpa klik mouse.
2. Di halaman yang sama, terdapat area **Webcam Viewfinder**. Jika *viewfinder* diklik, webcam komputer menyala untuk memindai resi secara optikal (sebagai cadangan).
3. Di Outbound, operator dapat melihat Picking List, dan saat resi di-scan (lewat alat fisik maupun webcam), status paket di Picking List otomatis tercoret/ter-update.
