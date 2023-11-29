<?php

namespace App\Providers;

use App\Services\DepositService;
use Illuminate\Support\ServiceProvider;

class DepositServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(DepositService::class, function ($app) {
            return new DepositService();
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
