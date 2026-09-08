<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Item;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LocationController extends Controller
{

    public function index(Request $request)
    {
        $query = Location::query();

        if ($request->has('search')) {
            $search = $request->string('search');
            $query->where('code', 'like', '%' . $search . '%')
                  ->orWhere('name', 'like', '%' . $search . '%')
                  ->orWhere('zone_name', 'like', '%' . $search . '%');
        }

        $locations = $query->withCount('items')->orderBy('code')->paginate(10)->withQueryString();

        return Inertia::render('Locations/Index', [
            'locations' => $locations,
            'filters' => $request->only('search')
        ]);
    }

    public function create()
    {
        return Inertia::render('Locations/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:locations,code',
            'name' => 'required|string|max:255',
            'zone_name' => 'nullable|string|max:255',
            'storage_type' => 'required|string|in:Dry,Cold Storage,Hazardous',
            'capacity_percentage' => 'required|integer|min:0|max:100',
            'status' => 'required|string|in:Active,Under Maintenance',
        ]);

        Location::create($validated);

        return redirect()->route('locations.index')->with('message', 'Lokasi/Rak berhasil ditambahkan.');
    }

    public function edit(Location $location)
    {
        return Inertia::render('Locations/Edit', [
            'location' => $location
        ]);
    }

    public function update(Request $request, Location $location)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:locations,code,' . $location->id,
            'name' => 'required|string|max:255',
            'zone_name' => 'nullable|string|max:255',
            'storage_type' => 'required|string|in:Dry,Cold Storage,Hazardous',
            'capacity_percentage' => 'required|integer|min:0|max:100',
            'status' => 'required|string|in:Active,Under Maintenance',
        ]);

        $location->update($validated);

        return redirect()->route('locations.index')->with('message', 'Lokasi/Rak berhasil diperbarui.');
    }

    public function destroy(Location $location)
    {
        $location->delete();
        return redirect()->route('locations.index')->with('message', 'Lokasi/Rak berhasil dihapus.');
    }

    public function items($id)
    {
        $location = Location::with('items')->findOrFail($id);
        
        return response()->json([
            'location' => $location,
            'items' => $location->items
        ]);
    }

    public function allItems(Request $request)
    {
        $query = Item::query();

        if ($request->has('search') && $request->search !== '') {
            $search = $request->string('search')->toString();
            $query->where(function ($q) use ($search) {
                $q->where('sku', 'like', '%' . $search . '%')
                  ->orWhere('name', 'like', '%' . $search . '%');
            });
        }

        $items = $query->orderBy('name')->limit(30)->get();

        return response()->json(['items' => $items]);
    }

    public function addItem(Request $request, $id)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $location = Location::findOrFail($id);
        $item = Item::findOrFail($validated['item_id']);

        $pivot = $item->locations()->where('location_id', $location->id)->first();
        $existingQty = $pivot ? (int) $pivot->pivot->quantity : 0;

        if ($existingQty > 0) {
            $item->locations()->updateExistingPivot($location->id, [
                'quantity' => $existingQty + $validated['quantity']
            ]);
        } else {
            $item->locations()->attach($location->id, ['quantity' => $validated['quantity']]);
        }

        return response()->json(['message' => 'Barang berhasil ditambahkan ke rak.']);
    }

    public function updateItemQuantity(Request $request, $id, $itemId)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $location = Location::findOrFail($id);
        $item = Item::findOrFail($itemId);

        if ((int) $validated['quantity'] === 0) {
            $item->locations()->detach($location->id);
        } else {
            $item->locations()->syncWithoutDetaching([$location->id => ['quantity' => $validated['quantity']]]);
        }

        return response()->json(['message' => 'Kuantitas barang di rak berhasil diperbarui.']);
    }

    public function removeItem($id, $itemId)
    {
        $location = Location::findOrFail($id);
        $item = Item::findOrFail($itemId);

        $item->locations()->detach($location->id);

        return response()->json(['message' => 'Barang berhasil dihapus dari rak.']);
    }
}
