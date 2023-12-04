<?php

namespace App\Observers;

use App\Models\AffiliateHistory;
use App\Models\Deposit;
use Log;

class DepositObserver
{
    public function updated(Deposit $deposit)
    {
        if ($deposit->isDirty('type') && $deposit->type === 'paid' && $deposit->getOriginal('type') === 'pending') {
            $this->processPaidDeposit($deposit);
            Log::info("DepositObserver - Deposito aprovado : ". number_format($deposit->amount) . " - " . $deposit->user->email . ".");
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
            if ($deposit->amount >= $affiliate->CPA) {
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
        AffiliateHistory::create([
            'amount' => $affiliate->CPA,
            'affiliateId' => $affiliate->id,
            'userId' => $deposit->userId,
            'type' => 'CPA',
        ]);

        $deposit->user->update([
            'cpaCollected' => true,
            'cpaCollectedAt' => now(),
        ]);
        $affiliate->changeWalletAffiliate($affiliate->CPA);    
        $affiliate->save();
    }
}
