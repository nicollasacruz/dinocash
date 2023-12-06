<?php

namespace App\Console;

use App\Jobs\CloseAffiliateInvoice;
use App\Jobs\CloseInvoiceGgr;
use App\Jobs\ProcessAutoWithdraw;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->job(new CloseInvoiceGgr())->mondays()->at('03:30');
        $schedule->job(new CloseAffiliateInvoice())->mondays()->at('00:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
