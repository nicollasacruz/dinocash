<?php

namespace App\Console\Commands;

use App\Models\AffiliateHistory;
use App\Models\GameHistory;
use App\Models\User;
use App\Services\AffiliateInvoiceService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CloseSubAffiliatesInvoicesByEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:close-sub-affiliates-invoices-by-email {email : Email do Expert}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fechar fatura de sub afiliados para um expert específico por email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $email = $this->argument('email');
            $expert = User::where('email', $email)->first();

            if (!$expert) {
                Log::info("Expert com email {$email} não encontrado.");
                $this->info("Expert com email {$email} não encontrado.");
                return;
            }

            Log::info("Iniciando fechamento de sub-afiliado para o expert: {$expert->name}");

            $this->closePayments($expert);
        } catch (Exception $ex) {
            Log::error("ERROOOOOR {$ex->getMessage()} - Linha: {$ex->getLine()}");
        }
    }

    public function closePayments(User $expert)
    {
        Log::info('Fechando o expert ' . $expert->name);
        $this->info('Fechando o expert ' . $expert->name);

        if (!$expert->cpaSub && !$expert->revSub) {
            Log::info("Expert: {$expert->name} - Fechando sem comissão");
            $this->info("Expert: {$expert->name} - Fechando sem comissão");
            return;
        }

        $subAffiliates = $expert->referredUsers->filter(function ($sub) {
            return $sub->isAffiliate;
        });

        // Log da quantidade de subaffiliates
        Log::info("Quantidade de Subaffiliates: {$subAffiliates->count()}");
        $this->info("Quantidade de Subaffiliates: {$subAffiliates->count()}");

        $subAffiliates->each(function ($sub) use ($expert) {
            $this->closeSubPayments($sub, $expert);
        });
    }

    private function closeSubPayments(User $sub, User $expert)
    {
        $revSub = (float) $expert->revSub;
        $cpaSub = (int) $expert->cpaSub;

        $affiliateInvoiceService = new AffiliateInvoiceService();
        if ($sub->revShare == 0 && $revSub > 0) {
            Log::info("Expert: {$expert->name} - Fechando o pagamento das comissões dos afiliados sem rev: {$sub->name}");
            $this->info("Expert: {$expert->name} - Fechando o pagamento das comissões dos afiliados sem rev: {$sub->name}");
            $users = $sub->referredUsers->filter(function ($user) {
                return $user->isAffiliate === false;
            });

            Log::info("Quantidade de Users: {$users->count()}");
            $this->info("Quantidade de Users: {$users->count()}");

            $users->each(function ($user) use ($revSub, $expert, $affiliateInvoiceService, $sub) {
                $gameHistories = $user->gameHistories->filter(function ($historyUser) use ($revSub, $expert, $affiliateInvoiceService, $sub) {
                    return ($historyUser->type === 'win' || $historyUser->type === 'loss') && $historyUser->subCollectedAt === null;
                });

                Log::info("Quantidade de GameHistories: {$gameHistories->count()}");
                $this->info("Quantidade de GameHistories: {$gameHistories->count()}");
                
                $gameHistories->each(function ($game) use ($revSub, $expert, $affiliateInvoiceService, $sub) {
                    $game = GameHistory::find($game->id);
                    $amount = (float) $game->finalAmount * -1;
                    if ($amount != 0) {
                        $newAmount = number_format($revSub * $amount / 100, 2, '.', '');
                        Log::info("Valor da Comissão: {$revSub}% * {$amount} = {$newAmount}");
                        $this->info("Valor da Comissão: {$revSub}% * {$amount} = {$newAmount}");
                        if ($newAmount != 0) {
                            $revsub = AffiliateHistory::create([
                                'amount' => $newAmount,
                                'affiliateId' => $expert->id,
                                'gameId' => $game->id,
                                'affiliateInvoiceId' => ($affiliateInvoiceService->getInvoice($expert))->id,
                                'userId' => $sub->id,
                                'type' => 'revSub',
                            ]);
                            $revsub->save();
                        }
                    }

                    Log::info("Antes de atualizar subCollectedAt: {$game->subCollectedAt}");
                    $this->info("Antes de atualizar subCollectedAt: {$game->subCollectedAt}");

                    $game->subCollectedAt = now();
                    $game->save();

                    // Log depois de atualizar subCollectedAt
                    Log::info("Depois de atualizar subCollectedAt: {$game->subCollectedAt}");
                    $this->info("Depois de atualizar subCollectedAt: {$game->subCollectedAt}");
                });
            });
        } elseif ($revSub > 0 || $cpaSub > 0) {
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
                    }
                }
                if ($history->type === 'CPA' && $cpaSub > 0) {
                    $subcpa = AffiliateHistory::create([
                        'amount' => number_format($cpaSub, 2, '.', ''),
                        'affiliateId' => $expert->id,
                        'affiliateInvoiceId' => ($affiliateInvoiceService->getInvoice($expert))->id,
                        'userId' => $sub->id,
                        'type' => 'cpaSub',
                    ]);
                    $subcpa->save();
                }
                $history->subInvoicedAt = now();
                $history->save();
            });
        }
    }
}
