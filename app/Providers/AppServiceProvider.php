<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
        // Garante HTTPS nas URLs geradas (assets, rotas, redirects) em
        // produção, independente da detecção de esquema do proxy.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
