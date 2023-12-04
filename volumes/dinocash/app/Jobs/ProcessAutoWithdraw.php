<?php

namespace App\Jobs;

use App\Models\Withdraw;
use App\Services\WithdrawService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessAutoWithdraw implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Withdraw$withdraw;

    /**
     * Create a new job instance.
     */
    public function __construct(Withdraw $withdraw)
    {
        $this->withdraw = $withdraw;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $withdrawService = new WithdrawService();
        if ($withdrawService->autoWithdraw($this->withdraw )) {
            $this->withdraw ->update([
                'type' => 'paid',
                'approvedAt' => now(),
            ]);
        }
    }
}
