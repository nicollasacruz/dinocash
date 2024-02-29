<?php

namespace App\Console\Commands;

use App\Models\AffiliateHistory;
use App\Models\GameHistory;
use App\Models\User;
use App\Services\AffiliateInvoiceService;
use Exception;
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
        try {
            Log::info('Iniciando fechamento de sub afiliados');

            User::where('isAffiliate', true)->where('isExpert', true)
                ->whereHas('referredUsers', function ($query) {
                    $query->where('isAffiliate', true);
                })
                ->each(function ($expert) {
                    Log::info("Expert: {$expert->name} - " . 'Fechando o pagamento de ' . $expert->referredUsers->where('isAffiliate', true)->count() . ' afiliados.');

                    $this->closePayments($expert);
                });
        } catch (Exception $ex) {
            Log::error("ERROOOOOR CloseSubAffiliateInvoices {$ex->getMessage()} - {$ex->getLine()}");
        }
    }

    public function closePayments(User $expert)
    {
        Log::info('Fechando o expert ' . $expert->name);

        if (!$expert->cpaSub && !$expert->revSub) {
            Log::info("Expert: {$expert->name} - Fechando sem comissão");
            return;
        }

        $expert->referredUsers->filter(function ($sub) {
            return $sub->isAffiliate;
        })->each(function ($sub) use ($expert) {
            $this->closeSubPayments($sub, $expert);
        });
    }

    private function closeSubPayments(User $sub, User $expert)
    {
        try {
            $revSub = (float) $expert->revSub;
            $cpaSub = (int) $expert->cpaSub;
            $sub = User::find($sub->id);

            $affiliateInvoiceService = new AffiliateInvoiceService();
            
            if ($sub->revShare === 0 && $revSub > 0) {
                // Log::info("Expert: {$expert->name} - Fechando o pagamento das comissões dos afiliados sem rev: {$sub->name}");
                // $this->info("Expert: {$expert->name} - Fechando o pagamento das comissões dos afiliados sem rev: {$sub->name}");
                // $users = $sub->referredUsers->filter(function ($user) {
                //     return !$user->isAffiliate;
                // });

                // Log::info("Quantidade de Users: {$users->count()}");
                // $this->info("Quantidade de Users: {$users->count()}");

                // $users->each(function ($user) use ($revSub, $expert, $affiliateInvoiceService, $sub) {
                //     $gameHistories = $user->gameHistories->filter(function ($historyUser) use ($revSub, $expert, $affiliateInvoiceService, $sub) {
                //         return ($historyUser->type === 'win' || $historyUser->type === 'loss') && $historyUser->subCollectedAt === null;
                //     });

                //     Log::info("Quantidade de GameHistories: {$gameHistories->count()}");
                //     $this->info("Quantidade de GameHistories: {$gameHistories->count()}");

                //     $gameHistories->each(function ($game) use ($revSub, $expert, $affiliateInvoiceService, $sub) {
                //         $game = GameHistory::find($game->id);
                //         $amount = (float) $game->finalAmount * -1;
                //         if ($amount != 0) {
                //             $newAmount = number_format($revSub * $amount / 100, 2, '.', '');
                //             $this->info("Valor da Comissão: {$revSub}% * {$amount} = {$newAmount}");
                //             if ($newAmount != 0) {
                //                 $revsub = AffiliateHistory::create([
                //                     'amount' => $newAmount,
                //                     'affiliateId' => $expert->id,
                //                     'gameId' => $game->id,
                //                     'affiliateInvoiceId' => ($affiliateInvoiceService->getInvoice($expert))->id,
                //                     'userId' => $sub->id,
                //                     'type' => 'revSub',
                //                 ]);
                //                 $revsub->save();
                //             }
                //         }

                //         $this->info("Antes de atualizar subCollectedAt: {$game->subCollectedAt}");

                //         $game->subCollectedAt = now();
                //         $game->save();

                //         // Log depois de atualizar subCollectedAt
                //         $this->info("Depois de atualizar subCollectedAt: {$game->subCollectedAt}");
                //     });
                // });
            } elseif ($revSub > 0 || $cpaSub > 0) {
                Log::info('Expert: ' . $expert->name . ' - Fechando o pagamento das comissões dos afiliados com rev: ' . $sub->name);
                Log::info('Expert: ' . $expert->name . ' - Fechando o pagamento das comissões dos afiliados com rev: ' . $sub->name);
                $sub->affiliateHistories->whereNull('subInvoicedAt')->each(function ($history) use ($revSub, $cpaSub, $expert, $sub, $affiliateInvoiceService) {
                    if (($history->type === 'win' || $history->type === 'loss') && $revSub > 0) {
                        $amount = $history->affiliate->revShare != 0 ? $history->amount : GameHistory::find($history->gameId)->finalAmount;
                        if ($amount != 0 && $newAmount = number_format($revSub * $amount / 100, 2, '.', '')) {
                            $revsub = AffiliateHistory::create([
                                'amount' => $newAmount,
                                'affiliateId' => $expert->id,
                                'gameId' => $history->gameId,
                                'affiliateInvoiceId' => ($affiliateInvoiceService->getInvoice($expert))->id,
                                'userId' => $sub->id,
                                'type' => 'revSub',
                            ]);
                            $revsub->save();

                            $game = GameHistory::find($history->gameId);
                            $this->info("Antes de atualizar subCollectedAt: {$game->subCollectedAt}");

                            $game->subCollectedAt = now();
                            $game->save();

                            $this->info("Depois de atualizar subCollectedAt: {$game->subCollectedAt}");
                        }
                    }
                    // if ($history->type === 'CPA' && $cpaSub > 0) {
                    //     $subcpa = AffiliateHistory::create([
                    //         'amount' => number_format($cpaSub, 2, '.', ''),
                    //         'affiliateId' => $expert->id,
                    //         'affiliateInvoiceId' => ($affiliateInvoiceService->getInvoice($expert))->id,
                    //         'userId' => $sub->id,
                    //         'type' => 'cpaSub',
                    //     ]);
                    //     $subcpa->save();
                    // }
                    $history->subInvoicedAt = now();
                    $history->save();
                });
            }
        } catch (Exception $ex) {
            Log::error("ERROOOOOR closeSubPayments {$ex->getMessage()} - Linha: {$ex->getLine()}");
            $this->error("ERROOOOOR closeSubPayments {$ex->getMessage()} - Linha: {$ex->getLine()}");
        }
    }
}
