<?php

namespace App\Http\Controllers;

use App\Models\InboundTransaction;
use App\Models\Item;
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
        // Select all items for the dropdown
        $items = Item::orderBy('name')->get(['id', 'sku', 'name']);
        
        return Inertia::render('Inbound/Create', [
            'items' => $items
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'transaction_date' => 'required|date',
            'quantity' => 'required|integer|min:1',
            'supplier' => 'nullable|string|max:255',
            'condition' => 'required|string|in:Good,Damaged',
            'status' => 'required|string|in:pending,completed',
            'notes' => 'nullable|string',
        ]);

        $validated['receipt_id'] = 'RCV-' . date('Ymd') . '-' . strtoupper(Str::random(5));

        $inbound = null;

        DB::transaction(function () use ($validated, &$inbound) {
            // Create transaction
            $inbound = InboundTransaction::create($validated);

            // Update stock only if status is completed
            if ($validated['status'] === 'completed') {
                $item = Item::where('id', $validated['item_id'])->lockForUpdate()->first();
                $item->quantity += $validated['quantity'];
                $item->save();
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
