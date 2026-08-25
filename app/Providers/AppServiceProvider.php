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
        Vite::prefetch(concurrency: 3);
        \App\Models\InboundTransaction::observe(\App\Observers\InboundTransactionObserver::class);
        \App\Models\OutboundTransaction::observe(\App\Observers\OutboundTransactionObserver::class);
    }
}
