<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'lowStockItems' => function () use ($request) {
                if ($request->user()) {
                    return \App\Models\Item::whereColumn('quantity', '<=', 'low_stock_threshold')->get();
                }
                return [];
            },
            'systemMaintenance' => function () {
                return \App\Models\SystemSetting::getMaintenanceDetails();
            },
            'splitAuthUsers' => function () {
                return \App\Models\User::latest()->take(3)->get()->map(function ($user) {
                    $words = explode(' ', trim($user->name));
                    $initials = '';
                    foreach (array_slice($words, 0, 2) as $w) {
                        $initials .= mb_strtoupper(mb_substr($w, 0, 1));
                    }
                    return $initials ?: 'U';
                })->toArray();
            },
        ];
    }
}
