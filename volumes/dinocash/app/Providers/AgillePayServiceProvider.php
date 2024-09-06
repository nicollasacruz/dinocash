<?php

namespace App\Providers;

use App\Services\AgillePayService;
use Illuminate\Support\ServiceProvider;

class AgillePayServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AgillePayService::class, function ($app) {
            return new AgillePayService();
        });
    }

    public function boot(): void
    {
    }
}
