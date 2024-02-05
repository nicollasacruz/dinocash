<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\AffiliateInvoiceService;
use Illuminate\Console\Command;

class CleanSubsHistoryRider extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-subs-history-rider';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $rider = User::find(5820);
        $this->info('Iniciado a limpeza da rede do Rider');

        $subs = $rider->referredUsers->where('isAffiliate', true);
        $subs->each(function ($subAffiliate) {
            $subHistories = $subAffiliate->affiliateHistories->where('subInvoicedAt', '!=', null);
            if (!$subHistories) {
                $this->info('Sem comissões para calcular');
            }
            
            $this->info('Total de historio de afiliados para limpar: ' . $subHistories->count());

            $subHistories->each(function ($history) {
                $history->subInvoicedAt = null;
                $history->save();
            });

            $users = $subAffiliate->referredUsers->filter(function ($user) {
                return !$user->isAffiliate;
            });

            $this->info("Quantidade de Users: " . $users->count());

            $users->each(function ($user) {
                $gameHistories = $user->gameHistories;
                $gameHistories->each(function ($gameHistory) {
                    $gameHistory->subCollectedAt = null;
                    $gameHistory->save();
                });
            });
        });
    }
}
