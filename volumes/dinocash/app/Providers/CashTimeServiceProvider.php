<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class CashTimeServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('App\Services\CashTimeService', function ($app) {
            return new \App\Services\CashTimeService();
        });
    }

    public function boot()
    {
    }
}
