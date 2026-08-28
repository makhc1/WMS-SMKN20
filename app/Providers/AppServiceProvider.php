<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (request()->server('HTTP_X_FORWARDED_PROTO') === 'https' || str_contains(request()->getHost(), 'trycloudflare.com') || str_contains(request()->getHost(), 'localtunnel.me')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        Vite::prefetch(concurrency: 3);
        \App\Models\InboundTransaction::observe(\App\Observers\InboundTransactionObserver::class);
        \App\Models\OutboundTransaction::observe(\App\Observers\OutboundTransactionObserver::class);
    }
}
