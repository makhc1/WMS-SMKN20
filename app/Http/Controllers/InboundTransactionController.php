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
        $validated = $request->validate($this->rules($request));

        $receiptId = 'RCV-' . date('Ymd') . '-' . strtoupper(Str::random(5));

        $lastInbound = null;

        DB::transaction(function () use ($request, $validated, &$lastInbound, $receiptId) {
            // Group the line items, each becomes its own InboundTransaction record
            foreach ($this->normalizeItems($request) as $index => $line) {
                $existingItem = Item::where('sku', $line['sku'])->first();

                if ($existingItem) {
                    // Update existing item's master data (don't touch quantity here)
                    $itemData = [
                        'name' => $line['name'],
                        'category' => $line['category'],
                        'unit' => $line['unit'],
                        'base_price' => $line['base_price'] ?? $existingItem->base_price,
                        'description' => $line['description'] ?? $existingItem->description,
                    ];
                    if ($line['photo']) {
                        $itemData['photo'] = $line['photo'];
                    }
                    $existingItem->update($itemData);
                    $item = $existingItem;
                } else {
                    // Create new item
                    $itemData = [
                        'sku' => $line['sku'],
                        'name' => $line['name'],
                        'category' => $line['category'],
                        'unit' => $line['unit'],
                        'base_price' => $line['base_price'] ?? null,
                        'description' => $line['description'] ?? null,
                        'quantity' => 0,
                        'low_stock_threshold' => 10,
                    ];
                    if ($line['photo']) {
                        $itemData['photo'] = $line['photo'];
                    }
                    $item = Item::create($itemData);
                }

                // Create inbound transaction (stock is adjusted automatically via InboundTransactionObserver)
                $lastInbound = InboundTransaction::create([
                    'item_id' => $item->id,
                    'transaction_date' => $validated['transaction_date'],
                    'quantity' => $line['quantity'],
                    'supplier' => $validated['supplier'] ?? null,
                    'condition' => $line['condition'],
                    'status' => $validated['status'],
                    'notes' => $validated['notes'] ?? null,
                    'receipt_id' => $receiptId,
                ]);

                // Allocate to location if specified
                if (!empty($line['location_id']) && $validated['status'] === 'completed') {
                    $locationQty = $line['location_quantity'] ?? $line['quantity'];

                    $pivot = $item->locations()->where('location_id', $line['location_id'])->first();
                    $existingQty = $pivot ? (int) $pivot->pivot->quantity : 0;

                    if ($existingQty > 0) {
                        // Add on top of what is already allocated to this rack
                        $item->locations()->updateExistingPivot($line['location_id'], [
                            'quantity' => $existingQty + $locationQty
                        ]);
                    } else {
                        $item->locations()->attach($line['location_id'], ['quantity' => $locationQty]);
                    }
                }
            }
        });

        return redirect()->route('inbound.show', $lastInbound->id)->with('message', 'Penerimaan barang berhasil dicatat.');
    }

    /**
     * Build the validation rules for a multi-item inbound form.
     *
     * The form posts an array of line items under "items[]", plus a few
     * transaction-level fields shared by all lines.
     */
    protected function rules(Request $request)
    {
        $items = $request->input('items', []);
        $count = is_array($items) ? count($items) : 0;

        return array_merge([
            'transaction_date' => 'required|date',
            'supplier' => 'nullable|string|max:255',
            'status' => 'required|string|in:pending,completed',
            'notes' => 'nullable|string',
            'items' => ['required', 'array', 'min:1'],
        ], $this->lineRules($count));
    }

    /**
     * Per-line validation rules. PHP validation keys are generated so a single
     * validation call can target every row at once.
     */
    protected function lineRules(int $count)
    {
        $rules = [];

        for ($i = 0; $i < $count; $i++) {
            $prefix = "items.$i";
            $rules["$prefix.sku"] = 'required|string|max:255';
            $rules["$prefix.name"] = 'required|string|max:255';
            $rules["$prefix.category"] = 'required|string|max:255';
            $rules["$prefix.unit"] = 'required|string|max:50';
            $rules["$prefix.base_price"] = 'nullable|numeric|min:0';
            $rules["$prefix.description"] = 'nullable|string';
            $rules["$prefix.photo"] = 'nullable|image|max:2048';
            $rules["$prefix.quantity"] = 'required|integer|min:1';
            $rules["$prefix.condition"] = 'required|string|in:Good,Damaged';
            $rules["$prefix.location_id"] = 'nullable|exists:locations,id';
            $rules["$prefix.location_quantity"] = 'nullable|integer|min:1';
        }

        return $rules;
    }

    /**
     * Flatten posted line items into a clean array, assigning the shared
     * transaction-level status and defaults where needed.
     */
    protected function normalizeItems(Request $request)
    {
        $lines = $request->input('items', []);

        return collect($lines)->map(function ($line, $index) use ($request) {
            return [
                'sku' => trim($line['sku'] ?? ''),
                'name' => trim($line['name'] ?? ''),
                'category' => trim($line['category'] ?? ''),
                'unit' => trim($line['unit'] ?? 'Pcs'),
                'base_price' => $line['base_price'] !== '' && $line['base_price'] !== null
                    ? (float) $line['base_price']
                    : null,
                'description' => $line['description'] ?? null,
                'photo' => $request->hasFile("items.$index.photo")
                    ? $request->file("items.$index.photo")->store('items', 'public')
                    : null,
                'quantity' => (int) ($line['quantity'] ?? 1),
                'condition' => $line['condition'] ?? 'Good',
                'location_id' => $line['location_id'] ?? null,
                'location_quantity' => $line['location_quantity'] !== '' && $line['location_quantity'] !== null
                    ? (int) $line['location_quantity']
                    : null,
            ];
        })->values()->all();
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
        });

        return redirect()->back()->with('message', 'Status Inbound berhasil diubah menjadi Completed. Stok bertambah.');
    }

    public function show($id)
    {
        $inbound = InboundTransaction::with('item')->findOrFail($id);

        // If this record belongs to a multi-item receipt, load all sibling
        // lines that share the same receipt_id so every barcode can be printed.
        $inbounds = !empty($inbound->receipt_id)
            ? InboundTransaction::with('item')
                ->where('receipt_id', $inbound->receipt_id)
                ->orderBy('id')
                ->get()
            : collect([$inbound]);

        return Inertia::render('Inbound/Show', [
            'inbound' => $inbound,
            'inbounds' => $inbounds,
        ]);
    }
}
