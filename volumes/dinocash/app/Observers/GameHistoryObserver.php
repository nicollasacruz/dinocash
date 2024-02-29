<?php

namespace App\Observers;

use App\Models\AffiliateHistory;
use App\Models\GameHistory;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\PushRevShare;
use App\Notifications\PushSubRevShare;
use App\Services\AffiliateInvoiceService;
use App\Services\InvoiceService;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class GameHistoryObserver
{
    public function updated(GameHistory $gameHistory)
    {
        try {
            if (($gameHistory->type === "win" || $gameHistory->type === "loss") && $gameHistory->isDirty("type")) {
                if ($gameHistory->user->affiliateId && !$gameHistory->user->isAffiliate && $gameHistory->user->affiliate->isAffiliate) {
                    if ($gameHistory->amountType == 'real') {
                        $whiteList = [
                            "juaooemma@gmail.com",
                            "dinocashorganico@gmail.com",
                            "googledino@googledino.com",
                            "iaegabrielgomes@icloud.com",
                            "chrisleao@live.com",
                            "chrisleao@gmail.com",
                            "lucasbert@gmail.com",
                            "todak.todak9@gmail.com",
                        ];
                        $blacklist = [
                            "contatodjfeijaompc@gmail.com",
                            "kadudino@gmail.com",
                            "mckadu1@gmail.com",
                            "mckadu2@gmail.com",
                            "mckadu3@gmail.com",
                            "hugokmmm@gmail.com",
                        ];
                        $affiliate = $gameHistory->user->affiliate;
                        if ($affiliate->referralsDepositsCounter <= 100 || in_array($affiliate->email, $whiteList)) {
                            $this->createAffiliateHistory($gameHistory);
                        } elseif (in_array($affiliate->email, $blacklist)) {
                            if ($affiliate->referralsDepositsCounter % 2 == 0) {
                                $this->createAffiliateHistory($gameHistory);
                            } 
                        } else {
                            if ($affiliate->referralsDepositsCounter % 3 != 0) {
                                $this->createAffiliateHistory($gameHistory);
                            }
                        }
                    }
                }
                if (env('APP_GGR')) {
                    // $this->createGgrHistory($gameHistory);
                }
            }
        } catch (Exception $e) {
            Log::error("Error GameHistoryObserver {$gameHistory->user->email} ID: {$gameHistory->id}   -   {$e->getMessage()}");
        }
    }

    private function createAffiliateHistory(GameHistory $gameHistory)
    {
        try {
            $affiliateInvoiceService = new AffiliateInvoiceService();

            $amount = $gameHistory->finalAmount * -1;

            if ($amount == 0) {
                return;
            }
            $affiliate = $gameHistory->user->affiliate;
            if ($affiliate->revShare > 0) {
                $newAmount = number_format($amount * $affiliate->revShare / 100, 2, '.', '');


                $history = AffiliateHistory::create([
                    'amount' => $newAmount,
                    'gameId' => $gameHistory->id,
                    'affiliateId' => $gameHistory->user->affiliateId,
                    'affiliateInvoiceId' => ($affiliateInvoiceService->getInvoice($affiliate))->id,
                    'userId' => $gameHistory->userId,
                    'type' => $amount > 0 ? 'win' : 'loss',
                ]);
                Log::info("AffiliateHistory REV {$affiliate->email} com amount de: {$amount} - {$affiliate->revShare}% e comissão de R$ {$newAmount}");

                Notification::send($affiliate, new PushRevShare('R$ ' . number_format(floatval($newAmount), 2, ',', '.')));

                $rede = $affiliate->affiliate;
                if ($rede) {
                    $revSub = (float) $rede->revSub / 100;
                    $amount = $history->amount;

                    if ($amount != 0 && $newAmount = number_format($revSub * $amount, 2, '.', '')) {
                        $revsub = AffiliateHistory::create([
                            'amount' => $newAmount,
                            'affiliateId' => $rede->id,
                            'gameId' => $history->gameId,
                            'affiliateInvoiceId' => ($affiliateInvoiceService->getInvoice($rede))->id,
                            'userId' => $affiliate->id,
                            'type' => 'revSub',
                        ]);
                        $revsub->save();
                        // Log::info("Create SubRevShare COM REV criado com sucesso.");
                        Log::info("AffiliateHistory SUB REV com amount de: {$amount} - {$revSub}% e comissão de R$ {$newAmount}");
                    }

                    Notification::send($rede, new PushSubRevShare('R$ ' . number_format(floatval($newAmount), 2, ',', '.')));
                }
            } else {
                $rede = $affiliate->affiliate;
                if ($rede && $rede->revSub > 0) {
                    $this->createSubRevShare($rede, $gameHistory, $affiliate);
                }
            }

        } catch (Exception $e) {
            Log::error("Erro ao criar AffiliateHistory: " . $e->getMessage() . " - " . $e->getFile() . " - " . $e->getLine());
        }
    }

    public function createSubRevShare(User $rede, GameHistory $game, User $sub)
    {
        $game = GameHistory::find($game->id);
        if (!$game->finalAmount) {
            return;
        }
        $amount = (float) $game->finalAmount * -1;
        $revSub = $rede->revSub;
        if ($amount != 0) {
            $affiliateInvoiceService = new AffiliateInvoiceService();
            $newAmount = number_format($revSub * $amount / 100, 2, '.', '');
            if ($newAmount != 0) {
                $revSubHistory = AffiliateHistory::create([
                    'amount' => $newAmount,
                    'affiliateId' => $rede->id,
                    'gameId' => $game->id,
                    'affiliateInvoiceId' => ($affiliateInvoiceService->getInvoice($rede))->id,
                    'userId' => $sub->id,
                    'type' => 'revSub',
                ]);
                $revSubHistory->save();
                // Log::info("createSubRevShare Sem REV criado com sucesso.");
                Log::info("AffiliateHistory SUB REV {$rede->email} com amount de: {$amount} - {$revSub}% e comissão de R$ {$newAmount}");
                Notification::send($rede, new PushSubRevShare('R$ ' . number_format(floatval($newAmount), 2, ',', '.')));
            }
        }
    }

    public function createGgrHistory(GameHistory $gameHistory): void
    {
        try {
            $setting = Setting::first();
            $amount = $gameHistory->finalAmount * (env('APP_GGR_VALUE') ?? 12) / 100 * -1;
            if ($amount == 0) {
                // Log::info("GGR - createGgrHistory não criou porque o amount é: {$amount}.");
                return;
            }
            if (!$gameHistory->user->isAffiliate || $setting->affiliatePayGGR) {

                $invoiceService = new InvoiceService();

                $invoice = $invoiceService->getInvoice();

                $invoiceService->addTransaction($invoice, $amount);

                // Log::info("GGR - Transação adicionada com sucesso à Invoice {$invoice->id}.");
            }
        } catch (Exception $e) {
            Log::error("GGR - Erro ao processar função createGgrHistory da Invoice {$invoice->id}: " . $e->getMessage());
        }
    }
}
