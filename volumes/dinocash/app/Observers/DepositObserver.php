<?php

namespace App\Observers;

use App\Models\AffiliateHistory;
use App\Models\Deposit;
use App\Notifications\PushCPA;
use App\Services\AffiliateInvoiceService;
use Log;
use Notification;

class DepositObserver
{
    public function updated(Deposit $deposit)
    {
        if ($deposit->isDirty('type') && $deposit->type === 'paid' && $deposit->getOriginal('type') === 'pending') {
            $this->processPaidDeposit($deposit);
            Log::info("DepositObserver - Deposito aprovado : ". number_format($deposit->amount, 2, '.', '') . " - " . $deposit->user->email . ".");
        }
    }

    private function processPaidDeposit(Deposit $deposit)
    {
        $user = $deposit->user;

        $user->changeWallet($deposit->amount);
        $user->save();

        // Verifica se o userId tem um affiliateId e o CpaCollected é falso
        if ($user->affiliateId && $user->affiliate->isAffiliate && !$user->cpaCollected) {
            $affiliate = $user->affiliate;

            // Verifica se o amount é igual ou maior que o CPA do afiliado
            if ($deposit->amount >= $affiliate->CPA && $affiliate->CPA > 0) {
                $createdAtPlusDay = $user->created_at->addDay(1);

                // Verifica se hoje é menor ou igual a data de criação do usuário + 1 dia
                if (now()->lte($createdAtPlusDay)) {
                    $this->createAffiliateHistory($deposit, $affiliate);
                }
            }
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

        Notification::send($affiliate, new PushCPA('R$ ' . number_format(floatval($affiliate->CPA), 2, ',', '.')));
    }
}
