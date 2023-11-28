<?php

namespace App\Observers;

use App\Models\AffiliateHistory;
use App\Models\Deposit;

class DepositObserver
{
    public function updated(Deposit $deposit)
    {
        // Verifica se o type mudou para 'paid' e era 'pending'
        if ($deposit->isDirty('type') && $deposit->type === 'paid' && $deposit->getOriginal('type') === 'pending') {
            $this->processPaidDeposit($deposit);
        }
    }

    private function processPaidDeposit(Deposit $deposit)
    {
        $user = $deposit->user;

        $user->wallet += $deposit->amount;

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
    }
}
