<?php

namespace App\Providers;

use App\Services\SuitPayService;
use Illuminate\Support\ServiceProvider;

class SuitPayServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('suitpay', function () {
            return new SuitPayService();
        });
    }

    public function boot()
    {
    }
}
