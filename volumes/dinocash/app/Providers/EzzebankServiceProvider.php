<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class EzzebankServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('ezzebank', function () {
            return new \App\Services\EzzebankService();
        });
    }

    public function boot()
    {
    }
}
