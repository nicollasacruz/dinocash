<?php

namespace App\Providers;

use App\Services\BsPayService;
use Illuminate\Support\ServiceProvider;

class BsPayServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind('bspay', function () {
            return new BsPayService();
        });
    }

    public function boot()
    {
    }
}
