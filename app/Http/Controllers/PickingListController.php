<?php

namespace App\Http\Controllers;

use App\Models\PickingList;
use App\Models\Item;
use App\Models\Location;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PickingListController extends Controller
{
    public function index(Request $request)
    {
        $query = PickingList::with('items');

        $pickingLists = $query->orderBy('created_at', 'desc')
                              ->paginate(10);

        return Inertia::render('PickingLists/Index', [
            'pickingLists' => $pickingLists
        ]);
    }

    public function create()
    {
        $items = Item::with('locations')->orderBy('name')->get();
        $locations = Location::where('status', 'Active')->orderBy('code')->get();
        
        return Inertia::render('PickingLists/Create', [
            'items' => $items,
            'locations' => $locations
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.location_id' => 'required|exists:locations,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $code = 'PL-' . date('Ymd') . '-' . strtoupper(Str::random(5));

        $pickingList = null;

        DB::transaction(function () use ($validated, &$pickingList, $code) {
            $pickingList = PickingList::create([
                'code' => $code,
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                $pickingList->items()->attach($item['item_id'], [
                    'location_id' => $item['location_id'],
                    'quantity' => $item['quantity'],
                    'status' => 'pending',
                ]);
            }
        });

        return redirect()->route('picking-lists.show', $pickingList->id)->with('message', 'Picking List berhasil dibuat.');
    }

    public function show($id)
    {
        $pickingList = PickingList::with(['items.locations'])->findOrFail($id);
        
        return Inertia::render('PickingLists/Show', [
            'pickingList' => $pickingList
        ]);
    }

    public function markAsCompleted($id)
    {
        DB::transaction(function () use ($id) {
            $pickingList = PickingList::findOrFail($id);
            $pickingList->status = 'completed';
            $pickingList->save();

            // Update all items status to picked
            $pickingList->items()->updateExistingPivot(
                $pickingList->items->pluck('id')->toArray(),
                ['status' => 'picked']
            );
        });

        return redirect()->back()->with('message', 'Picking List berhasil diselesaikan.');
    }

    public function markItemPicked(Request $request, $id)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
        ]);

        DB::transaction(function () use ($id, $validated) {
            $pickingList = PickingList::findOrFail($id);
            
            $pickingList->items()->updateExistingPivot(
                $validated['item_id'],
                ['status' => 'picked']
            );

            // Check if all items are picked
            $unpickedCount = $pickingList->items()
                ->wherePivot('status', 'pending')
                ->count();

            if ($unpickedCount === 0) {
                $pickingList->status = 'completed';
                $pickingList->save();
            }
        });

        return redirect()->back()->with('message', 'Barang berhasil ditandai sebagai diambil.');
    }
}
