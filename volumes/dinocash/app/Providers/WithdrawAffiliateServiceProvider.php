<?php

namespace App\Providers;

use App\Services\WithdrawAffiliateService;
use App\Services\WithdrawService;
use Illuminate\Support\ServiceProvider;

class WithdrawAffiliateServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(WithdrawAffiliateService::class, function ($app) {
            return new WithdrawAffiliateService();
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
