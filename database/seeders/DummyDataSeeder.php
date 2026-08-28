<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Location;
use App\Models\InboundTransaction;
use App\Models\OutboundTransaction;
use App\Models\PickingList;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        // Clear existing data
        PickingList::truncate();
        InboundTransaction::truncate();
        OutboundTransaction::truncate();
        Item::truncate();
        Location::truncate();

        // --- LOCATIONS ---
        $locations = Location::insert([
            ['name' => 'Rak A1', 'code' => 'RA01', 'zone_name' => 'Zone A', 'storage_type' => 'Rak', 'capacity_percentage' => 75, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Rak A2', 'code' => 'RA02', 'zone_name' => 'Zone A', 'storage_type' => 'Rak', 'capacity_percentage' => 60, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Rak B1', 'code' => 'RB01', 'zone_name' => 'Zone B', 'storage_type' => 'Rak', 'capacity_percentage' => 85, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Rak B2', 'code' => 'RB02', 'zone_name' => 'Zone B', 'storage_type' => 'Rak', 'capacity_percentage' => 45, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Rak C1', 'code' => 'RC01', 'zone_name' => 'Zone C', 'storage_type' => 'Rak', 'capacity_percentage' => 90, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Gudang Utama', 'code' => 'GU01', 'zone_name' => 'Zone Utama', 'storage_type' => 'Gudang', 'capacity_percentage' => 55, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Rak D1', 'code' => 'RD01', 'zone_name' => 'Zone D', 'storage_type' => 'Rak', 'capacity_percentage' => 30, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Rak D2', 'code' => 'RD02', 'zone_name' => 'Zone D', 'storage_type' => 'Rak', 'capacity_percentage' => 70, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // --- ITEMS ---
        $items = [
            ['sku' => 'ATK-001', 'name' => 'Pensil 2B', 'category' => 'ATK', 'brand' => 'Staedtler', 'quantity' => 150, 'unit' => 'Pcs', 'base_price' => 3500, 'low_stock_threshold' => 20, 'location' => 'Rak A1'],
            ['sku' => 'ATK-002', 'name' => 'Pulpen Pilot', 'category' => 'ATK', 'brand' => 'Pilot', 'quantity' => 80, 'unit' => 'Pcs', 'base_price' => 5000, 'low_stock_threshold' => 15, 'location' => 'Rak A1'],
            ['sku' => 'ATK-003', 'name' => 'Buku Tulis 40L', 'category' => 'ATK', 'brand' => 'Sinar Dunia', 'quantity' => 200, 'unit' => 'Buku', 'base_price' => 4000, 'low_stock_threshold' => 30, 'location' => 'Rak A2'],
            ['sku' => 'ATK-004', 'name' => 'Penggaris 30cm', 'category' => 'ATK', 'brand' => 'Butterfly', 'quantity' => 50, 'unit' => 'Pcs', 'base_price' => 8000, 'low_stock_threshold' => 10, 'location' => 'Rak A2'],
            ['sku' => 'ATK-005', 'name' => 'Marker Snowman', 'category' => 'ATK', 'brand' => 'Snowman', 'quantity' => 5, 'unit' => 'Pcs', 'base_price' => 12000, 'low_stock_threshold' => 10, 'location' => 'Rak B1'],
            ['sku' => 'ATK-006', 'name' => 'Penghapus Pentel', 'category' => 'ATK', 'brand' => 'Pentel', 'quantity' => 0, 'unit' => 'Pcs', 'base_price' => 3000, 'low_stock_threshold' => 15, 'location' => 'Rak B1'],
            ['sku' => 'ELK-001', 'name' => 'Keyboard Logitech', 'category' => 'Elektronik', 'brand' => 'Logitech', 'quantity' => 12, 'unit' => 'Unit', 'base_price' => 350000, 'low_stock_threshold' => 5, 'location' => 'Gudang Utama'],
            ['sku' => 'ELK-002', 'name' => 'Mouse Wireless', 'category' => 'Elektronik', 'brand' => 'Logitech', 'quantity' => 18, 'unit' => 'Unit', 'base_price' => 150000, 'low_stock_threshold' => 5, 'location' => 'Gudang Utama'],
            ['sku' => 'ELK-003', 'name' => 'Kabel HDMI 2m', 'category' => 'Elektronik', 'brand' => 'Vention', 'quantity' => 30, 'unit' => 'Pcs', 'base_price' => 75000, 'low_stock_threshold' => 10, 'location' => 'Rak C1'],
            ['sku' => 'ELK-004', 'name' => 'USB Hub 4 Port', 'category' => 'Elektronik', 'brand' => 'Baseus', 'quantity' => 8, 'unit' => 'Unit', 'base_price' => 85000, 'low_stock_threshold' => 5, 'location' => 'Rak C1'],
            ['sku' => 'FUR-001', 'name' => 'Kursi Lipat', 'category' => 'Furniture', 'brand' => 'Olympic', 'quantity' => 25, 'unit' => 'Unit', 'base_price' => 450000, 'low_stock_threshold' => 5, 'location' => 'Gudang Utama'],
            ['sku' => 'FUR-002', 'name' => 'Meja Belajar', 'category' => 'Furniture', 'brand' => 'Olympic', 'quantity' => 10, 'unit' => 'Unit', 'base_price' => 850000, 'low_stock_threshold' => 3, 'location' => 'Gudang Utama'],
            ['sku' => 'FUR-003', 'name' => 'Lemari Arsip', 'category' => 'Furniture', 'brand' => 'Brother', 'quantity' => 4, 'unit' => 'Unit', 'base_price' => 2500000, 'low_stock_threshold' => 2, 'location' => 'Gudang Utama'],
            ['sku' => 'PRS-001', 'name' => 'Proyektor Epson', 'category' => 'Presentasi', 'brand' => 'Epson', 'quantity' => 6, 'unit' => 'Unit', 'base_price' => 5500000, 'low_stock_threshold' => 2, 'location' => 'Rak D1'],
            ['sku' => 'PRS-002', 'name' => 'Screen Proyektor 100"', 'category' => 'Presentasi', 'brand' => 'Eltax', 'quantity' => 3, 'unit' => 'Unit', 'base_price' => 1200000, 'low_stock_threshold' => 1, 'location' => 'Rak D1'],
            ['sku' => 'CLN-001', 'name' => 'Pembersih Lantai', 'category' => 'Kebersihan', 'brand' => 'Wipol', 'quantity' => 40, 'unit' => 'Liter', 'base_price' => 18000, 'low_stock_threshold' => 10, 'location' => 'Rak D2'],
            ['sku' => 'CLN-002', 'name' => 'Tisu Roll', 'category' => 'Kebersihan', 'brand' => 'Paseo', 'quantity' => 60, 'unit' => 'Pack', 'base_price' => 25000, 'low_stock_threshold' => 15, 'location' => 'Rak D2'],
            ['sku' => 'CLN-003', 'name' => 'Sabun Cuci Tangan', 'category' => 'Kebersihan', 'brand' => 'Lifebuoy', 'quantity' => 2, 'unit' => 'Botol', 'base_price' => 15000, 'low_stock_threshold' => 5, 'location' => 'Rak D2'],
            ['sku' => 'SPR-001', 'name' => 'Kertas HVS A4', 'category' => 'Printer', 'brand' => 'Sinar Dunia', 'quantity' => 50, 'unit' => 'Rim', 'base_price' => 42000, 'low_stock_threshold' => 10, 'location' => 'Rak B2'],
            ['sku' => 'SPR-002', 'name' => 'Tinta Printer Canon', 'category' => 'Printer', 'brand' => 'Canon', 'quantity' => 15, 'unit' => 'Botol', 'base_price' => 95000, 'low_stock_threshold' => 5, 'location' => 'Rak B2'],
        ];

        foreach ($items as $item) {
            $locationName = $item['location'];
            unset($item['location']);
            $item['created_at'] = now();
            $item['updated_at'] = now();
            $item['quantity'] = 0; // Start with 0, inbound will add
            $newItem = Item::create($item);
            $loc = Location::where('name', $locationName)->first();
            if ($loc) {
                DB::table('item_location')->insert([
                    'item_id' => $newItem->id,
                    'location_id' => $loc->id,
                    'quantity' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // --- INBOUND TRANSACTIONS (20 data) ---
        $suppliers = ['PT Maju Jaya', 'PT Sejahtera', 'CV Berkah', 'PT Tekno Makmur', 'PT Sumber Rejeki', 'CV Abadi'];
        $statuses = ['completed', 'completed', 'completed', 'pending'];
        $conditions = ['Good', 'Good', 'Good', 'Damaged'];

        for ($i = 1; $i <= 20; $i++) {
            $item = Item::inRandomOrder()->first();
            $qty = rand(5, 50);
            $date = now()->subDays(rand(0, 60));

            InboundTransaction::create([
                'receipt_id' => 'RCV-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'item_id' => $item->id,
                'quantity' => $qty,
                'transaction_date' => $date->format('Y-m-d'),
                'supplier' => $suppliers[array_rand($suppliers)],
                'status' => $statuses[array_rand($statuses)],
                'condition' => $conditions[array_rand($conditions)],
                'notes' => rand(1, 3) === 1 ? 'Pengiriman urgent' : null,
                'created_at' => $date,
                'updated_at' => $date,
            ]);

            // Update item quantity if completed
            if ($statuses[array_rand($statuses)] === 'completed') {
                $item->increment('quantity', $qty);
            }
        }

        // --- OUTBOUND TRANSACTIONS (15 data) ---
        $recipients = ['Pak Guru Budi', 'Ibu Siti', 'Pak Ahmad', 'Laboratorium', 'Ruang Guru', 'Perpustakaan', 'Kantor'];
        $destinations = ['Ruang 101', 'Ruang 202', 'Lab Komputer', 'Guru BK', 'Ruang TU', 'Lantai 3', 'Aula'];

        for ($i = 1; $i <= 15; $i++) {
            $item = Item::where('quantity', '>', 0)->inRandomOrder()->first();
            if (!$item) continue;

            $qty = rand(1, min(10, $item->quantity));
            $date = now()->subDays(rand(0, 30));

            OutboundTransaction::create([
                'item_id' => $item->id,
                'quantity' => $qty,
                'transaction_date' => $date->format('Y-m-d'),
                'recipient' => $recipients[array_rand($recipients)],
                'destination' => $destinations[array_rand($destinations)],
                'status' => $statuses[array_rand($statuses)],
                'notes' => rand(1, 4) === 1 ? 'Untuk kegiatan sekolah' : null,
                'created_at' => $date,
                'updated_at' => $date,
            ]);

            $item->decrement('quantity', $qty);
        }

        // Re-sync quantities from actual transactions
        foreach (Item::all() as $item) {
            $inboundTotal = InboundTransaction::where('item_id', $item->id)
                ->where('status', 'completed')
                ->sum('quantity');
            $outboundTotal = OutboundTransaction::where('item_id', $item->id)
                ->where('status', 'completed')
                ->sum('quantity');
            $item->update(['quantity' => $inboundTotal - $outboundTotal]);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        $this->command->info('Dummy data berhasil dibuat!');
        $this->command->info('Items: ' . Item::count());
        $this->command->info('Locations: ' . Location::count());
        $this->command->info('Inbound Transactions: ' . InboundTransaction::count());
        $this->command->info('Outbound Transactions: ' . OutboundTransaction::count());
    }
}
