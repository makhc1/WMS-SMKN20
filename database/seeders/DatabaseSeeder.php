<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Item;
use App\Models\InboundTransaction;
use App\Models\OutboundTransaction;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(UserSeeder::class);

        // Dummy Items for SMKN 20
        $dummyItems = [
            ['name' => 'Kertas HVS A4 80gr', 'category' => 'ATK', 'sku' => 'ATK-001', 'brand' => 'PaperOne', 'location' => 'Rak A1', 'low_stock_threshold' => 10],
            ['name' => 'Kertas HVS F4 70gr', 'category' => 'ATK', 'sku' => 'ATK-002', 'brand' => 'Sinar Dunia', 'location' => 'Rak A1', 'low_stock_threshold' => 10],
            ['name' => 'Tinta Printer Hitam L3110', 'category' => 'ATK', 'sku' => 'ATK-003', 'brand' => 'Epson', 'location' => 'Lemari B1', 'low_stock_threshold' => 5],
            ['name' => 'Tinta Printer Warna L3110', 'category' => 'ATK', 'sku' => 'ATK-004', 'brand' => 'Epson', 'location' => 'Lemari B1', 'low_stock_threshold' => 5],
            ['name' => 'Spidol Boardmarker Hitam', 'category' => 'ATK', 'sku' => 'ATK-005', 'brand' => 'Snowman', 'location' => 'Rak A2', 'low_stock_threshold' => 20],
            ['name' => 'Penghapus Papan Tulis', 'category' => 'ATK', 'sku' => 'ATK-006', 'brand' => 'Kenko', 'location' => 'Rak A2', 'low_stock_threshold' => 10],
            ['name' => 'Router Board RB941', 'category' => 'TKJ', 'sku' => 'TKJ-001', 'brand' => 'MikroTik', 'location' => 'Lab TKJ 1', 'low_stock_threshold' => 2],
            ['name' => 'Kabel UTP Cat6 305m', 'category' => 'TKJ', 'sku' => 'TKJ-002', 'brand' => 'Belden', 'location' => 'Gudang Belakang', 'low_stock_threshold' => 1],
            ['name' => 'Konektor RJ45 (Isi 50)', 'category' => 'TKJ', 'sku' => 'TKJ-003', 'brand' => 'CommScope', 'location' => 'Lemari C1', 'low_stock_threshold' => 3],
            ['name' => 'Sapu Ijuk', 'category' => 'Kebersihan', 'sku' => 'KBR-001', 'brand' => 'Nagoya', 'location' => 'Gudang Kebersihan', 'low_stock_threshold' => 5],
            ['name' => 'Pembersih Lantai 4L', 'category' => 'Kebersihan', 'sku' => 'KBR-002', 'brand' => 'Soklin', 'location' => 'Gudang Kebersihan', 'low_stock_threshold' => 3],
            ['name' => 'Proyektor WXGA', 'category' => 'Elektronik', 'sku' => 'ELK-001', 'brand' => 'Epson', 'location' => 'Ruang Multimedia', 'low_stock_threshold' => 1],
            ['name' => 'Webcam 1080p', 'category' => 'Elektronik', 'sku' => 'ELK-002', 'brand' => 'Logitech', 'location' => 'Ruang Multimedia', 'low_stock_threshold' => 2],
        ];

        // Insert items and keep track of their cumulative stock manually via transactions
        foreach ($dummyItems as $data) {
            $data['quantity'] = 0; 
            
            // Check if item already exists by SKU to avoid duplicates if seeder runs multiple times
            $item = Item::where('sku', $data['sku'])->first();
            if (!$item) {
                $item = Item::create($data);
            } else {
                continue; // Skip if already seeded
            }

            // Generate 2-4 Inbound Transactions for each item over the last 3 months
            $inboundCount = rand(2, 4);
            $totalIn = 0;
            
            for ($i = 0; $i < $inboundCount; $i++) {
                $qty = rand(10, 50);
                $totalIn += $qty;
                $date = Carbon::now()->subDays(rand(10, 90));
                
                InboundTransaction::create([
                    'item_id' => $item->id,
                    'transaction_date' => $date->format('Y-m-d'),
                    'quantity' => $qty,
                    'supplier' => collect(['CV Maju Jaya', 'PT Indotama', 'Toko ABC', 'Bantuan Dinas'])->random(),
                    'notes' => collect(['Pembelian Rutin', 'Restock Bulanan', 'Dana BOS', ''])->random(),
                ]);
            }

            // Generate 1-5 Outbound Transactions over the last 2 months
            $outboundCount = rand(1, 5);
            $totalOut = 0;
            
            for ($i = 0; $i < $outboundCount; $i++) {
                $qty = rand(1, 5);
                // Ensure we don't negative stock
                if ($totalOut + $qty > $totalIn) {
                    break;
                }
                
                $totalOut += $qty;
                $date = Carbon::now()->subDays(rand(1, 45));
                
                OutboundTransaction::create([
                    'item_id' => $item->id,
                    'transaction_date' => $date->format('Y-m-d'),
                    'quantity' => $qty,
                    'recipient' => collect(['Pak Budi (Guru)', 'Bu Ani (TU)', 'Siswa RPL 1', 'Pak Yanto (Sarpras)'])->random(),
                    'destination' => collect(['Lab Komputer 1', 'Ruang Guru', 'Kelas 10 RPL', 'Perpustakaan'])->random(),
                    'notes' => collect(['Kebutuhan Praktik', 'Penggantian rusak', 'Dipinjam sebentar', ''])->random(),
                ]);
            }
            
            // Finalize current stock
            $item->update(['quantity' => $totalIn - $totalOut]);
        }
    }
}
