<?php

namespace App\Console\Commands;

use App\Models\AffiliateHistory;
use App\Models\GameHistory;
use App\Models\User;
use App\Services\AffiliateInvoiceService;
use Illuminate\Console\Command;

class UpdateRedeRiderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-rede-rider-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for update the sub affiliate rider';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $rider = User::find(5820);
        $affiliateInvoiceService = new AffiliateInvoiceService();
        $rider->referredUsers->where('isAffiliate', true)->each(function ($subAffiliate) use ($affiliateInvoiceService, $rider) {
            // SubAfiliados com revshare menor ou igual ao do rider
            if ($subAffiliate->revShare > 0 && $subAffiliate->reshare <= $rider->revShare) {
                $subHistories = $subAffiliate->affiliateHistories->whereNull('subInvoicedAt');
                if (!$subHistories) {
                    $this->info('Sem comissões para calcular');
                }
                $subHistories->each(function ($history) use ($affiliateInvoiceService, $rider, $subAffiliate) {
                    if (in_array($history->type, ['win', 'loss'])) {
                        $onePercentAmount = $history->amount / $subAffiliate->revShare;
                        $amount = $rider->revShare == $subAffiliate->revShare ? $onePercentAmount * 5 : ($onePercentAmount * $rider->revShare) - $history->amount;
                        AffiliateHistory::create([
                            'amount' => $amount,
                            'affiliateId' => $rider->id,
                            'gameId' => $history->gameId,
                            'affiliateInvoiceId' => ($affiliateInvoiceService->getInvoice($rider))->id,
                            'userId' => $subAffiliate->id,
                            'type' => 'revSub',
                        ]);
                    }
                    if ($history->type === 'CPA') {
                        $amount = $history->amount == $rider->CPA ? 5 : $rider->CPA - $history->amount;
                        AffiliateHistory::create([
                            'amount' => $amount,
                            'affiliateId' => $rider->id,
                            'affiliateInvoiceId' => ($affiliateInvoiceService->getInvoice($rider))->id,
                            'userId' => $subAffiliate->id,
                            'type' => 'cpaSub',
                        ]);
                    }
                    $history->subInvoicedAt = now();
                    $history->save();
                });
            } elseif ($subAffiliate->revShare == 0) {
                $subHistories = $subAffiliate->affiliateHistories->whereNull('subInvoicedAt')->where('type', 'CPA');
                if (!$subHistories) {
                    $this->info('Sem comissões para calcular');
                }
                $subHistories->each(function ($history) use ($affiliateInvoiceService, $rider, $subAffiliate) {
                    $amount = $history->amount == $rider->CPA ? 5 : $rider->CPA - $history->amount;
                    AffiliateHistory::create([
                        'amount' => $amount,
                        'affiliateId' => $rider->id,
                        'affiliateInvoiceId' => ($affiliateInvoiceService->getInvoice($rider))->id,
                        'userId' => $subAffiliate->id,
                        'type' => 'cpaSub',
                    ]);
                    $history->subInvoicedAt = now();
                    $history->save();
                });

                $users = $subAffiliate->referredUsers->filter(function ($user) {
                    return !$user->isAffiliate;
                });

                $this->info("Quantidade de Users: {$users->count()}");

                $users->each(function ($user) use ($rider, $subAffiliate, $affiliateInvoiceService) {
                    $gameHistories = $user->gameHistories->filter(function ($historyUser) use ($rider, $subAffiliate, $affiliateInvoiceService) {
                        return ($historyUser->type === 'win' || $historyUser->type === 'loss') && $historyUser->subCollectedAt === null;
                    });
                    if (!$gameHistories) {
                        $this->info('Sem comissões para calcular');
                    }

                    $this->info("Quantidade de GameHistories: {$gameHistories->count()}");

                    $gameHistories->each(function ($game) use ($rider, $subAffiliate, $affiliateInvoiceService) {
                        $game = GameHistory::find($game->id);
                        $amount = (float) $game->finalAmount * -1;
                        if ($amount != 0) {
                            $newAmount = number_format(($rider->revShare / 100) * $amount, 2, '.', '');
                            $this->info("Valor da Comissão: {$rider->revShare}% * {$amount} = {$newAmount}");
                            if ($newAmount != 0) {
                                $revsub = AffiliateHistory::create([
                                    'amount' => $newAmount,
                                    'affiliateId' => $rider->id,
                                    'gameId' => $game->id,
                                    'affiliateInvoiceId' => ($affiliateInvoiceService->getInvoice($rider))->id,
                                    'userId' => $subAffiliate->id,
                                    'type' => 'revSub',
                                ]);
                                $revsub->save();
                            }
                        }

                        $this->info("Antes de atualizar subCollectedAt: {$game->subCollectedAt}");

                        $game->subCollectedAt = now();
                        $game->save();

                        // Log depois de atualizar subCollectedAt
                        $this->info("Depois de atualizar subCollectedAt: {$game->subCollectedAt}");
                    });
                });
            }
        });
    }
}
