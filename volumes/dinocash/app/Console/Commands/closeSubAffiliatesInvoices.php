<?php

namespace App\Console\Commands;

use App\Models\AffiliateHistory;
use App\Models\GameHistory;
use App\Models\User;
use App\Services\AffiliateInvoiceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class closeSubAffiliatesInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:close-sub-affiliates-invoices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'fechar rede de experts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $affiliateInvoiceService = new AffiliateInvoiceService;
        Log::info('Iniciando fechamento de sub afiliados');
        $experts = User::where('isAffiliate', true)->whereHas('referredUsers', function ($query) {
            $query->where('isAffiliate', true);
        });
        Log::info('Fechando o pagamento de ' . $experts->count() .' experts.');
        $experts->each(function ($expert) use ($affiliateInvoiceService) {
            Log::info('Fechando o pagamento de ' . $expert->name);
            $revSub = $expert->revSub;
            $cpaSub = $expert->cpaSub;
            $expert->referredUsers->filter(function ($referral) use ($revSub, $cpaSub, $expert, $affiliateInvoiceService) {
                return $referral->isAffiliate === true;
            })->each(function ($sub) use ($revSub, $cpaSub, $expert, $affiliateInvoiceService) {
                Log::info('Fechando o pagamento de ' . $sub->affiliateHistories->sum('amount') .' experts.');
                $sub->affiliateHistories->each(function ($history) use ($revSub, $cpaSub, $expert, $sub, $affiliateInvoiceService) {
                    
                    $amount = $history->affiliate->revShare > 0 ? $history->amount : GameHistory::find($history->gameId)->finalAmount;

                    if (($history->type === 'win' || $history->type === 'loss') && $revSub > 0) {
                        $history->subInvoicedAt = now();
                        $history->save();
                        AffiliateHistory::create([
                            'amount' => number_format($revSub * $amount * 0.8, 2, '.', ''),
                            'affiliateId' => $expert->id,
                            'affiliateInvoiceId' => ($affiliateInvoiceService->getInvoice($expert))->id,
                            'userId' => $sub->id,
                            'type' => 'revSub',
                        ]);
                    }
                    if ($history->type === 'CPA' && $cpaSub > 0) {
                        $history->subInvoicedAt = now();
                        $history->save();
                        AffiliateHistory::create([
                            'amount' => number_format($cpaSub, 2, '.', ''),
                            'affiliateId' => $expert->id,
                            'affiliateInvoiceId' => ($affiliateInvoiceService->getInvoice($expert))->id,
                            'userId' => $sub->id,
                            'type' => 'cpaSub',
                        ]);
                    }
                });
            });
        });
    }
}
