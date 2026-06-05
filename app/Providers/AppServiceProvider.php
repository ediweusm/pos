<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\PembelianDetail;
use App\Observers\PembelianDetailObserver;

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
        PembelianDetail::observe(PembelianDetailObserver::class);
    }
}
