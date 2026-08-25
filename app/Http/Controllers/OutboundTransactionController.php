<?php

namespace App\Http\Controllers;

use App\Models\OutboundTransaction;
use App\Models\Item;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class OutboundTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = OutboundTransaction::with('item');

        $outbounds = $query->orderBy('transaction_date', 'desc')
                           ->orderBy('created_at', 'desc')
                           ->paginate(10);

        return Inertia::render('Outbound/Index', [
            'outbounds' => $outbounds
        ]);
    }

    public function create()
    {
        // Select all items for the dropdown
        $items = Item::orderBy('name')->get(['id', 'sku', 'name', 'quantity', 'location', 'brand', 'unit']);
        
        return Inertia::render('Outbound/Create', [
            'items' => $items
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'transaction_date' => 'required|date',
            'quantity' => 'required|integer|min:1',
            'customer_name' => 'required|string|max:255',
            'customer_address' => 'required|string',
            'status' => 'required|string|in:pending,completed',
            'courier' => 'nullable|string|max:255',
            'estimated_delivery_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $validated['receipt_id'] = 'DO-' . date('Ymd') . '-' . strtoupper(Str::random(5));

        $outbound = null;

        DB::transaction(function () use ($validated, &$outbound) {
            $item = Item::where('id', $validated['item_id'])->lockForUpdate()->first();
            
            // Check stock BEFORE creating if status is completed
            if ($validated['status'] === 'completed' && $item->quantity < $validated['quantity']) {
                throw ValidationException::withMessages([
                    'quantity' => 'Stok tidak mencukupi. Stok saat ini: ' . $item->quantity,
                ]);
            }

            // Create transaction
            $outbound = OutboundTransaction::create($validated);

            if ($validated['status'] === 'completed') {
                $item->quantity -= $validated['quantity'];
                $item->save();
            }
        });

        return redirect()->route('outbound.show', $outbound->id)->with('message', 'Pengeluaran barang berhasil dicatat.');
    }

    public function markAsCompleted($id)
    {
        DB::transaction(function () use ($id) {
            $outbound = OutboundTransaction::findOrFail($id);
            if ($outbound->status === 'completed') {
                return;
            }

            $item = Item::where('id', $outbound->item_id)->lockForUpdate()->first();
            
            if ($item->quantity < $outbound->quantity) {
                throw ValidationException::withMessages([
                    'general' => 'Stok tidak mencukupi untuk menyelesaikan transaksi ini. Stok saat ini: ' . $item->quantity,
                ]);
            }

            $outbound->status = 'completed';
            $outbound->save();

            $item->quantity -= $outbound->quantity;
            $item->save();
        });

        return redirect()->back()->with('message', 'Status Outbound berhasil diubah menjadi Completed. Stok berkurang.');
    }

    public function show($id)
    {
        $outbound = OutboundTransaction::with('item')->findOrFail($id);
        
        return Inertia::render('Outbound/Show', [
            'outbound' => $outbound
        ]);
    }
}
