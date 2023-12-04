<?php

namespace App\Observers;

use App\Models\AffiliateHistory;
use App\Models\GameHistory;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Support\Facades\Log;

class GameHistoryObserver
{
    public function updated(GameHistory $gameHistory)
    {

        if ($gameHistory->type !== "pending") {
            Log::info("Iniciando update GameHistory.");
            if ($gameHistory->user->affiliateId && $gameHistory->user->affiliate->isAffiliate) {
                $this->createAffiliateHistory($gameHistory);
            } else {
                Log::info("Não tem referral.");
            }
            if (env('APP_GGR')) {
                $this->createGgrHistory($gameHistory);
            } else {
                Log::info("GGR não ativado.");
            }
        }
    }

    private function createAffiliateHistory(GameHistory $gameHistory)
    {
        try {
            $amount = $gameHistory->finalAmount * -1;

            if ($amount === 0) {
                return;
            }

            AffiliateHistory::create([
                'amount' => number_format($amount * ($gameHistory->user->affiliate->revShare)),
                'gameId' => $gameHistory->id,
                'affiliateId' => $gameHistory->user->affiliateId,
                'userId' => $gameHistory->userId,
                'type' => $amount > 0 ? 'win' : 'loss',
            ]);

            Log::info("AffiliateHistory criado com sucesso.");

        } catch (\Exception $e) {
            Log::error("Erro ao criar AffiliateHistory: " . $e->getMessage());
        }
    }

    public function createGgrHistory(GameHistory $gameHistory): void
    {
        try {
            $amount = $gameHistory->finalAmount * -1;
            if ($amount === 0) {
                return;
            }

            $invoiceService = new InvoiceService();

            $invoice = $invoiceService->getInvoice();

            $invoiceService->addTransaction($invoice, $amount);

            Log::info("Transação adicionada com sucesso à Invoice {$invoice->id}.");
        } catch (\Exception $e) {
            Log::error("Erro ao processar função createGgrHistory da Invoice {$invoice->id}: " . $e->getMessage());
        }
    }
}
