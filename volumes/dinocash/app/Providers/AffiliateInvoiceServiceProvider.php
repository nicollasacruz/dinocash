<?php

namespace App\Providers;

use App\Services\AffiliateInvoiceService;
use App\Services\InvoiceService;
use Illuminate\Support\ServiceProvider;

class AffiliateInvoiceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(AffiliateInvoiceService::class, function ($app) {
            return new AffiliateInvoiceService();
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
