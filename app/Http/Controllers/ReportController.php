<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\InboundTransaction;
use App\Models\OutboundTransaction;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        return Inertia::render('Reports/Index');
    }

    public function exportStock()
    {
        $items = Item::orderBy('name')->get();
        $date = Carbon::now()->isoFormat('D MMMM Y');

        $pdf = Pdf::loadView('reports.stock', [
            'items' => $items,
            'date' => $date
        ]);

        return $pdf->download('Laporan_Stok_Gudang_' . date('Ymd') . '.pdf');
    }

    public function exportMutations(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $inbounds = InboundTransaction::with('item')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->orderBy('transaction_date', 'asc')
            ->get()
            ->map(function ($tx) {
                $tx->type = 'inbound';
                $tx->party = $tx->supplier ?? '-';
                return $tx;
            });

        $outbounds = OutboundTransaction::with('item')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->orderBy('transaction_date', 'asc')
            ->get()
            ->map(function ($tx) {
                $tx->type = 'outbound';
                $tx->party = $tx->destination ? ($tx->recipient ? $tx->recipient . ' (' . $tx->destination . ')' : $tx->destination) : ($tx->recipient ?? '-');
                return $tx;
            });

        $mutations = $inbounds->concat($outbounds)->sortBy('transaction_date');

        $pdf = Pdf::loadView('reports.mutations', [
            'mutations' => $mutations,
            'start_date' => $startDate->isoFormat('D MMMM Y'),
            'end_date' => $endDate->isoFormat('D MMMM Y')
        ]);

        return $pdf->download('Laporan_Mutasi_' . $startDate->format('Ymd') . '-' . $endDate->format('Ymd') . '.pdf');
    }
}
