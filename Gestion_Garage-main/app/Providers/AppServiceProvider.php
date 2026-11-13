<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
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
        Paginator::useBootstrap(); // For Bootstrap 5

        // Forcer HTTPS sur l'environnement de production ou si l'APP_URL est en HTTPS
        if (str_starts_with((string) config('app.url'), 'https://') || app()->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
