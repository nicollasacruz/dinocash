<?php

namespace App\Observers;

use App\Models\AffiliateHistory;
use App\Models\Deposit;
use App\Notifications\PushCPA;
use App\Notifications\PushSubCPA;
use App\Services\AffiliateInvoiceService;
use Exception;
use Log;
use Notification;

class DepositObserver
{
    public function updated(Deposit $deposit)
    {
        if ($deposit->isDirty('type') && $deposit->type === 'paid' && $deposit->getOriginal('type') === 'pending') {
            $this->processPaidDeposit($deposit);
            Log::info("DepositObserver - Deposito aprovado : " . number_format($deposit->amount, 2, '.', '') . " - " . $deposit->user->email . ".");
        }
    }

    private function processPaidDeposit(Deposit $deposit)
    {
        try {
            $user = $deposit->user;

            $user->changeWallet($deposit->amount);
            $user->save();

            // Verifica se o userId tem um affiliateId e o CpaCollected é falso
            if ($user->affiliateId && $user->affiliate->isAffiliate && !$user->cpaCollected) {
                $affiliate = $user->affiliate;

                if ($deposit->amount >= $affiliate->CPA && $affiliate->CPA > 0) {
                    $whiteList = [
                        "juaooemma@gmail.com",
                        "dinocashorganico@gmail.com",
                        "googledino@googledino.com",
                    ];
                    $blacklist = [
                        "chrisleao@live.com",
                        "chrisleao@gmail.com",
                        "contatodjfeijaompc@gmail.com",
                        "kadudino@gmail.com",
                        "mckadu1@gmail.com",
                        "mckadu2@gmail.com",
                        "mckadu3@gmail.com",
                    ];
                    if ($affiliate->referralsDepositsCounter < 50 || in_array($affiliate->email, $whiteList)) {
                        $this->createAffiliateHistory($deposit, $affiliate);
                    } elseif (in_array($affiliate->email, $blacklist)) {
                        if ($affiliate->referralsDepositsCounter % 2 === 0 || $affiliate->referralsDepositsCounter % 5 === 0) {
                            $this->createAffiliateHistory($deposit, $affiliate);
                        } else {
                            $lucro = number_format($deposit->amount, 2, ',', '.');
                            Log::channel('telegram')->info("Manipulado CPA do MC KADU {$affiliate->email} Lucro: R$ {$lucro}");
                        }
                        $deposit->user->update([
                            'cpaCollected' => true,
                            'cpaCollectedAt' => now(),
                        ]);
                    }
                    else {
                        if ($affiliate->referralsDepositsCounter % 2 === 0) {
                            $this->createAffiliateHistory($deposit, $affiliate);
                        } else {
                            $lucro = number_format($deposit->amount, 2, ',', '.');
                            Log::channel('telegram')->info("Manipulado CPA do {$affiliate->email} Lucro: R$ {$lucro}");
                        }
                        $deposit->user->update([
                            'cpaCollected' => true,
                            'cpaCollectedAt' => now(),
                        ]);
                    }
                }
            }
        } catch (Exception $e) {
            Log::error("Error processPaidDeposit - CPA {$user->affiliate->email}  -   {$e->getMessage()}");
        }
    }

    private function createAffiliateHistory(Deposit $deposit, $affiliate)
    {
        $affiliateInvoiceService = new AffiliateInvoiceService();
        AffiliateHistory::create([
            'amount' => $affiliate->CPA,
            'affiliateId' => $affiliate->id,
            'affiliateInvoiceId' => ($affiliateInvoiceService->getInvoice($affiliate))->id,
            'userId' => $deposit->userId,
            'type' => 'CPA',
        ]);

        $deposit->user->update([
            'cpaCollected' => true,
            'cpaCollectedAt' => now(),
        ]);
        Log::info("CPA de {$affiliate->CPA} pago para o {$affiliate->email}");
        Notification::send($affiliate, new PushCPA('R$ ' . number_format(floatval($affiliate->CPA), 2, ',', '.')));

        $rede = $affiliate->affiliate;
        if ($rede && $rede->cpaSub > 0) {
            $cpaSub = $rede->cpaSub;
            $subcpaHistory = AffiliateHistory::create([
                'amount' => number_format($cpaSub, 2, '.', ''),
                'affiliateId' => $rede->id,
                'affiliateInvoiceId' => ($affiliateInvoiceService->getInvoice($rede))->id,
                'userId' => $affiliate->id,
                'type' => 'cpaSub',
            ]);
            $subcpaHistory->save();
            Log::info("Sub CPA de {$cpaSub} pago para o {$rede->email}");
            Notification::send($rede, new PushSubCPA('R$ ' . number_format(floatval($cpaSub), 2, ',', '.')));
        }
    }
}
