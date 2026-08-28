<?php

namespace App\Http\Controllers;

use App\Models\InboundTransaction;
use App\Models\OutboundTransaction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RiwayatController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->input('search');
        $type = $request->input('type', '');

        $inbounds = InboundTransaction::with('item')
            ->when($search, function ($query, $search) {
                $query->whereHas('item', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('sku', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('transaction_date', 'desc')
            ->paginate(10)
            ->through(fn($t) => ['type' => 'inbound'] + $t->toArray());

        $outbounds = OutboundTransaction::with('item')
            ->when($search, function ($query, $search) {
                $query->whereHas('item', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('sku', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('transaction_date', 'desc')
            ->paginate(10)
            ->through(fn($t) => ['type' => 'outbound'] + $t->toArray());

        $merged = $inbounds->concat($outbounds)
            ->sortByDesc('transaction_date')
            ->values();

        return Inertia::render('Riwayat', [
            'inbounds' => $inbounds,
            'outbounds' => $outbounds,
            'transactions' => $merged,
            'filters' => $request->only('search', 'type'),
        ]);
    }
}
