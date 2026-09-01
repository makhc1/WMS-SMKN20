<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query();

        if ($request->has('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('supplier_id', 'like', '%' . $search . '%')
                  ->orWhere('nama_supplier', 'like', '%' . $search . '%');
            });
        }

        $suppliers = $query->orderBy('nama_supplier')->paginate(10)->withQueryString();

        return Inertia::render('Suppliers/Index', [
            'suppliers' => $suppliers,
            'filters' => $request->only('search')
        ]);
    }

    public function create()
    {
        return Inertia::render('Suppliers/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|unique:suppliers,supplier_id|max:255',
            'nama_supplier' => 'required|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:255'
        ]);

        Supplier::create($validated);

        return redirect()->route('suppliers.index')->with('message', 'Supplier berhasil ditambahkan.');
    }

    public function edit(Supplier $supplier)
    {
        return Inertia::render('Suppliers/Edit', [
            'supplier' => $supplier
        ]);
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|max:255|unique:suppliers,supplier_id,' . $supplier->id,
            'nama_supplier' => 'required|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:255'
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')->with('message', 'Supplier berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil dihapus.');
    }
}
