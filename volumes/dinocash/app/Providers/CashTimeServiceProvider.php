<?php

namespace App\Providers;

use App\Services\CashTimeService;
use Illuminate\Support\ServiceProvider;

class CashTimeServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('App\Services\CashTimeService', function ($app) {
            return new CashTimeService();
        });
    }

    public function boot()
    {
    }
}
