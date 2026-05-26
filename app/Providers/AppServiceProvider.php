<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // 👈 Importação crucial adicionada

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
        // 🚀 FORÇA O HTTPS EM AMBIENTE DE PRODUÇÃO (RENDER)
        if (config('app.env') === 'production' || env('RENDER')) {
            URL::forceScheme('https');
        }
    }
}
