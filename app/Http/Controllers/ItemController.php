<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::query();

        if ($request->has('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('sku', 'like', '%' . $search . '%')
                  ->orWhere('name', 'like', '%' . $search . '%');
            });
        }

        if ($request->has('category') && $request->category != '') {
            $query->where('category', $request->category);
        }

        $items = $query->orderBy('name')->paginate(10)->withQueryString();

        $categories = Item::select('category')->distinct()->whereNotNull('category')->whereNull('deleted_at')->pluck('category');

        return Inertia::render('Items/Index', [
            'items' => $items,
            'filters' => $request->only('search', 'category'),
            'categories' => $categories
        ]);
    }

    public function create()
    {
        return Inertia::render('Items/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku' => 'required|unique:items,sku|max:255',
            'name' => 'required|max:255',
            'category' => 'required|string|max:255',
            'base_price' => 'nullable|numeric|min:0',
            'unit' => 'required|string|max:50',
            'description' => 'nullable|string',
            'low_stock_threshold' => 'required|integer|min:0',
            'photo' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('items', 'public');
        }

        $validated['quantity'] = 0; // Initial quantity is always 0

        Item::create($validated);

        return redirect()->route('items.index')->with('message', 'Master barang berhasil ditambahkan.');
    }

    public function show(Item $item)
    {
        return Inertia::render('Items/Show', [
            'item' => $item
        ]);
    }

    public function edit(Item $item)
    {
        return Inertia::render('Items/Edit', [
            'item' => $item
        ]);
    }

    public function update(Request $request, Item $item)
    {
        $rules = [
            'sku' => 'required|max:255|unique:items,sku,' . $item->id,
            'name' => 'required|max:255',
            'category' => 'required|string|max:255',
            'base_price' => 'nullable|numeric|min:0',
            'unit' => 'required|string|max:50',
            'description' => 'nullable|string',
            'low_stock_threshold' => 'required|integer|min:0',
            'photo' => 'nullable|image|max:2048'
        ];

        // Only Admin or Warehouse Manager can manually adjust quantity
        $canAdjustStock = in_array(auth()->user()->role, ['Admin', 'Warehouse Manager']);
        if ($canAdjustStock) {
            $rules['quantity'] = 'nullable|integer|min:1';
        }

        $validated = $request->validate($rules);

        if ($request->hasFile('photo')) {
            if ($item->photo) {
                Storage::disk('public')->delete($item->photo);
            }
            $validated['photo'] = $request->file('photo')->store('items', 'public');
        }

        $item->update($validated);

        return redirect()->route('items.index')->with('message', 'Data barang berhasil diperbarui.');
    }

    public function destroy(Item $item)
    {
        return back()->withErrors([
            'error' => 'Master barang tidak dapat dihapus. Data barang hanya dapat diarsipkan melalui proses penghapusan stok secara bertahap.'
        ]);
    }
}
