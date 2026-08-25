<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $query = Location::query();

        if ($request->has('search')) {
            $search = $request->string('search');
            $query->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('zone_name', 'like', "%{$search}%");
        }

        $locations = $query->orderBy('code')->paginate(10)->withQueryString();

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
}
