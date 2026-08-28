<?php

namespace App\Http\Middleware;

use App\Models\SystemSetting;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Whitelisted route names and URI patterns during maintenance.
     */
    protected array $exceptRoutes = [
        'login',
        'logout',
        'password.request',
        'password.email',
        'password.reset',
        'password.store',
        'sanctum.csrf-cookie',
        'up',
        'system.status',
    ];

    protected array $exceptPatterns = [
        'login',
        'logout',
        'password/*',
        'sanctum/*',
        'up',
        'system-status',
        'build/*',
        'assets/*',
        '@vite/*',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!SystemSetting::isMaintenanceMode()) {
            return $next($request);
        }

        // Warehouse Manager bypass
        if (auth()->check() && auth()->user()->role === 'Warehouse Manager') {
            return $next($request);
        }

        // Whitelisted routes (login, logout, assets, health)
        if ($this->isWhitelisted($request)) {
            return $next($request);
        }

        $details = SystemSetting::getMaintenanceDetails();

        if ($request->expectsJson() && !$request->header('X-Inertia')) {
            return response()->json([
                'message' => $details['message'],
                'maintenance' => true,
                'details' => $details,
            ], 503);
        }

        return Inertia::render('Maintenance', [
            'details' => $details,
            'user' => auth()->user(),
        ])->toResponse($request)->setStatusCode(503);
    }

    /**
     * Determine if the request has a URI or route that is whitelisted.
     */
    protected function isWhitelisted(Request $request): bool
    {
        foreach ($this->exceptPatterns as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }

        $currentRoute = $request->route()?->getName();
        if ($currentRoute && in_array($currentRoute, $this->exceptRoutes, true)) {
            return true;
        }

        return false;
    }
}
