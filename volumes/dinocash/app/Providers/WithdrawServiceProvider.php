<?php

namespace App\Providers;

use App\Services\WithdrawService;
use Illuminate\Support\ServiceProvider;

class WithdrawServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(WithdrawService::class, function ($app) {
            return new WithdrawService();
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
