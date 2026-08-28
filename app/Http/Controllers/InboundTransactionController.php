<?php

namespace App\Http\Controllers;

use App\Models\InboundTransaction;
use App\Models\Item;
use App\Models\Location;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InboundTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = InboundTransaction::with('item');

        $inbounds = $query->orderBy('transaction_date', 'desc')
                          ->orderBy('created_at', 'desc')
                          ->paginate(10);

        return Inertia::render('Inbound/Index', [
            'inbounds' => $inbounds
        ]);
    }

    public function create()
    {
        $locations = Location::where('status', 'Active')->orderBy('code')->get();
        
        return Inertia::render('Inbound/Create', [
            'locations' => $locations
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'base_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'transaction_date' => 'required|date',
            'quantity' => 'required|integer|min:1',
            'supplier' => 'nullable|string|max:255',
            'condition' => 'required|string|in:Good,Damaged',
            'status' => 'required|string|in:pending,completed',
            'notes' => 'nullable|string',
            'location_id' => 'nullable|exists:locations,id',
            'location_quantity' => 'nullable|integer|min:1',
        ]);

        $receiptId = 'RCV-' . date('Ymd') . '-' . strtoupper(Str::random(5));

        $inbound = null;
        $item = null;

        DB::transaction(function () use ($validated, &$inbound, &$item, $receiptId, $request) {
            // Check if item exists by SKU
            $existingItem = Item::where('sku', $validated['sku'])->first();

            if ($existingItem) {
                // Update existing item
                $item = $existingItem;
                $item->quantity += $validated['quantity'];
                $item->save();
            } else {
                // Create new item
                $itemData = [
                    'sku' => $validated['sku'],
                    'name' => $validated['name'],
                    'category' => $validated['category'],
                    'unit' => $validated['unit'],
                    'base_price' => $validated['base_price'] ?? null,
                    'description' => $validated['description'] ?? null,
                    'quantity' => $validated['status'] === 'completed' ? $validated['quantity'] : 0,
                    'low_stock_threshold' => 10,
                ];

                if ($request->hasFile('photo')) {
                    $itemData['photo'] = $request->file('photo')->store('items', 'public');
                }

                $item = Item::create($itemData);
            }

            // Create inbound transaction
            $inbound = InboundTransaction::create([
                'item_id' => $item->id,
                'transaction_date' => $validated['transaction_date'],
                'quantity' => $validated['quantity'],
                'supplier' => $validated['supplier'] ?? null,
                'condition' => $validated['condition'],
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
                'receipt_id' => $receiptId,
            ]);

            // Allocate to location if specified
            if (!empty($validated['location_id']) && $validated['status'] === 'completed') {
                $locationQty = $validated['location_quantity'] ?? $validated['quantity'];
                
                $item->locations()->syncWithoutDetaching([
                    $validated['location_id'] => ['quantity' => $locationQty]
                ]);
            }
        });

        return redirect()->route('inbound.show', $inbound->id)->with('message', 'Penerimaan barang berhasil dicatat.');
    }

    public function markAsCompleted($id)
    {
        DB::transaction(function () use ($id) {
            $inbound = InboundTransaction::findOrFail($id);
            if ($inbound->status === 'completed') {
                return;
            }

            $inbound->status = 'completed';
            $inbound->save();

            $item = Item::where('id', $inbound->item_id)->lockForUpdate()->first();
            $item->quantity += $inbound->quantity;
            $item->save();
        });

        return redirect()->back()->with('message', 'Status Inbound berhasil diubah menjadi Completed. Stok bertambah.');
    }

    public function show($id)
    {
        $inbound = InboundTransaction::with('item')->findOrFail($id);
        
        return Inertia::render('Inbound/Show', [
            'inbound' => $inbound
        ]);
    }
}
