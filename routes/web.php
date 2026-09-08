<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::redirect('/', '/login');

Route::get('/system-status', function () {
    $details = \App\Models\SystemSetting::getMaintenanceDetails();
    return response()->json([
        'maintenance' => (bool) $details['is_active'],
        'details' => $details,
    ]);
})->name('system.status');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $lowStock = \App\Models\Item::whereColumn('quantity', '<=', 'low_stock_threshold')->get();
        
        $sevenDaysAgo = now()->subDays(6)->startOfDay();
        $inboundSums = \App\Models\InboundTransaction::selectRaw('DATE(transaction_date) as date, SUM(quantity) as sum')
            ->where('transaction_date', '>=', $sevenDaysAgo)
            ->where('status', 'completed')
            ->groupByRaw('DATE(transaction_date)')
            ->pluck('sum', 'date');
            
        $outboundSums = \App\Models\OutboundTransaction::selectRaw('DATE(transaction_date) as date, SUM(quantity) as sum')
            ->where('transaction_date', '>=', $sevenDaysAgo)
            ->where('status', 'completed')
            ->groupByRaw('DATE(transaction_date)')
            ->pluck('sum', 'date');

        $chartData = collect(range(6, 0))->map(function($daysAgo) use ($inboundSums, $outboundSums) {
            $date = now()->subDays($daysAgo)->format('Y-m-d');
            return [
                'date' => now()->subDays($daysAgo)->format('d M'),
                'inbound' => $inboundSums->get($date, 0),
                'outbound' => $outboundSums->get($date, 0),
            ];
        });

        // Top 5 Most Active Items (Last 30 days)
        $thirtyDaysAgo = now()->subDays(30)->startOfDay();
        
        $activeInbound = \App\Models\InboundTransaction::selectRaw('item_id, SUM(quantity) as total')
            ->where('transaction_date', '>=', $thirtyDaysAgo)
            ->groupBy('item_id');
            
        $activeOutbound = \App\Models\OutboundTransaction::selectRaw('item_id, SUM(quantity) as total')
            ->where('transaction_date', '>=', $thirtyDaysAgo)
            ->groupBy('item_id');
            
        $topItemsRaw = \Illuminate\Support\Facades\DB::query()
            ->selectRaw('item_id, SUM(total) as activity')
            ->fromSub(function ($query) use ($activeInbound, $activeOutbound) {
                $query->from($activeInbound, 'in')
                      ->unionAll($activeOutbound);
            }, 'combined')
            ->groupBy('item_id')
            ->orderByDesc('activity')
            ->limit(5)
            ->get();
            
        $topItemIds = $topItemsRaw->pluck('item_id');
        $items = \App\Models\Item::whereIn('id', $topItemIds)->get()->keyBy('id');
        
        $activeItemsData = $topItemsRaw->map(function ($row) use ($items) {
            return [
                'name' => $items->get($row->item_id)->name ?? 'Unknown',
                'activity' => (int)$row->activity
            ];
        });

        // Dashboard Metrics
        $totalStock = \App\Models\Item::sum('quantity') ?? 0;
        $maxCapacity = 10000;
        $utilization = min(100, round(($totalStock / $maxCapacity) * 100, 1));
        
        $pendingIn = \App\Models\InboundTransaction::where('status', 'pending')->count();
        $pendingOut = \App\Models\OutboundTransaction::where('status', 'pending')->count();

        return Inertia::render('Dashboard', [
            'stats' => [
                'total_stock' => $totalStock,
                'capacity_utilization' => $utilization,
                'inbound_today' => \App\Models\InboundTransaction::whereDate('transaction_date', today())->sum('quantity') ?? 0,
                'outbound_today' => \App\Models\OutboundTransaction::whereDate('transaction_date', today())->sum('quantity') ?? 0,
                'pending_tasks' => $pendingIn + $pendingOut,
            ],
            'lowStock' => $lowStock,
            'chartData' => $chartData,
            'activeItems' => $activeItemsData
        ]);
    })->name('dashboard');

    // Admin & Warehouse Manager bisa full CRUD (termasuk create, store, edit, update, delete)
    Route::middleware('role:Admin,Warehouse Manager')->group(function () {
        Route::resource('items', \App\Http\Controllers\ItemController::class)->except(['index', 'show']);
    });

    Route::resource('items', \App\Http\Controllers\ItemController::class)->only(['index', 'show']);
    
    Route::middleware('role:Admin,Warehouse Manager')->group(function () {
        Route::resource('suppliers', \App\Http\Controllers\SupplierController::class)->except(['index', 'show']);
    });
    Route::resource('suppliers', \App\Http\Controllers\SupplierController::class)->only(['index', 'show']);
    
    Route::resource('inbound', \App\Http\Controllers\InboundTransactionController::class);
    Route::post('inbound/{id}/complete', [\App\Http\Controllers\InboundTransactionController::class, 'markAsCompleted'])->name('inbound.complete');
    
    Route::resource('outbound', \App\Http\Controllers\OutboundTransactionController::class);
    Route::post('outbound/{id}/complete', [\App\Http\Controllers\OutboundTransactionController::class, 'markAsCompleted'])->name('outbound.complete');

    Route::get('/riwayat', [\App\Http\Controllers\RiwayatController::class, 'index'])->name('riwayat.index');

    Route::resource('picking-lists', \App\Http\Controllers\PickingListController::class)->except(['edit', 'update', 'destroy']);
    Route::post('picking-lists/{id}/complete', [\App\Http\Controllers\PickingListController::class, 'markAsCompleted'])->name('picking-lists.complete');
    Route::post('picking-lists/{id}/pick', [\App\Http\Controllers\PickingListController::class, 'markItemPicked'])->name('picking-lists.pick');

    Route::middleware('role:Admin,Warehouse Manager')->group(function () {
        Route::get('/reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
        Route::post('/reports/stock/pdf', [\App\Http\Controllers\ReportController::class, 'exportStock'])->name('reports.stock.pdf');
        Route::post('/reports/mutations/pdf', [\App\Http\Controllers\ReportController::class, 'exportMutations'])->name('reports.mutations.pdf');
        
        Route::resource('locations', \App\Http\Controllers\LocationController::class);
        Route::get('locations/items/all', [\App\Http\Controllers\LocationController::class, 'allItems'])->name('locations.items.all');
        Route::get('locations/{id}/items', [\App\Http\Controllers\LocationController::class, 'items'])->name('locations.items');
        Route::post('locations/{id}/items', [\App\Http\Controllers\LocationController::class, 'addItem'])->name('locations.items.add');
        Route::patch('locations/{id}/items/{itemId}', [\App\Http\Controllers\LocationController::class, 'updateItemQuantity'])->name('locations.items.update');
        Route::delete('locations/{id}/items/{itemId}', [\App\Http\Controllers\LocationController::class, 'removeItem'])->name('locations.items.remove');
    });

    Route::middleware('role:Warehouse Manager')->group(function () {
        Route::resource('users', \App\Http\Controllers\UserController::class);
        Route::get('/maintenance-settings', [\App\Http\Controllers\MaintenanceController::class, 'index'])->name('maintenance.index');
        Route::post('/maintenance-settings', [\App\Http\Controllers\MaintenanceController::class, 'update'])->name('maintenance.update');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
