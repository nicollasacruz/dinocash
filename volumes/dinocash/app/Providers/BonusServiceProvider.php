<?php

namespace App\Providers;

use App\Services\BonusService;
use Illuminate\Support\ServiceProvider;

class BonusServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(BonusService::class, function ($app) {
            return new BonusService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
