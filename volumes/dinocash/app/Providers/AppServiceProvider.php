<?php

namespace App\Providers;

use App\Models\Deposit;
use App\Models\GameHistory;
use App\Models\User;
use App\Models\Withdraw;
use App\Observers\DepositObserver;
use App\Observers\GameHistoryObserver;
use App\Observers\UserObserver;
use App\Observers\WithdrawObserver;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->singleton('locale', function () {
            return app()->getLocale();
        });
        
        if($this->app->environment('production')) {
            URL::forceScheme('https');
        }
        User::observe(UserObserver::class);
        GameHistory::observe(GameHistoryObserver::class);
        Withdraw::observe(WithdrawObserver::class);
        Deposit::observe(DepositObserver::class);
    }
}
