<?php

namespace App\Observers;

use App\Models\AffiliateHistory;
use App\Models\GameHistory;
use App\Models\Setting;
use App\Services\AffiliateInvoiceService;
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
            }
            if (env('APP_GGR')) {
                $this->createGgrHistory($gameHistory);
            }
        }
    }

    private function createAffiliateHistory(GameHistory $gameHistory)
    {
        try {
            $affiliateInvoiceService = new AffiliateInvoiceService();

            $amount = $gameHistory->finalAmount * -1;

            if ($amount === 0) {
                Log::info("AffiliateHistory não criou porque o amount é: {$amount}.");
                return;
            }
            $affiliate = $gameHistory->user->affiliate;
            if (!$gameHistory->user->isAffiliate) {
                AffiliateHistory::create([
                    'amount' => number_format($amount * ($gameHistory->user->affiliate->revShare) / 100, 2, '.', ''),
                    'gameId' => $gameHistory->id,
                    'affiliateId' => $gameHistory->user->affiliateId,
                    'affiliateInvoiceId' => ($affiliateInvoiceService->getInvoice($gameHistory->user->affiliate))->id,
                    'userId' => $gameHistory->userId,
                    'type' => $amount > 0 ? 'win' : 'loss',
                ]);
            }
            Log::info("AffiliateHistory criado com sucesso.");

        } catch (\Exception $e) {
            Log::error("Erro ao criar AffiliateHistory: " . $e->getMessage() . " - " . $e->getFile() . " - " . $e->getLine());
        }
    }

    public function createGgrHistory(GameHistory $gameHistory): void
    {
        try {
            $setting = Setting::first();
            $amount = $gameHistory->finalAmount * -1;
            if ($amount === 0) {
                Log::info("GGR - createGgrHistory não criou porque o amount é: {$amount}.");
                return;
            }
            if (!$gameHistory->user->isAffiliate || $setting->affiliatePayGGR) {

                $invoiceService = new InvoiceService();

                $invoice = $invoiceService->getInvoice();

                $invoiceService->addTransaction($invoice, $amount);

                Log::info("GGR - Transação adicionada com sucesso à Invoice {$invoice->id}.");
            }
        } catch (\Exception $e) {
            Log::error("GGR - Erro ao processar função createGgrHistory da Invoice {$invoice->id}: " . $e->getMessage());
        }
    }
}
