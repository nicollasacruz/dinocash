<?php

namespace App\Observers;

use App\Models\AffiliateHistory;
use App\Models\GameHistory;

class GameHistoryObserver
{
    public function created(GameHistory $gameHistory)
    {
        // Verifica se o usuário associado tem um AffiliateId
        if ($gameHistory->user->affiliateId && $gameHistory->user->affiliate->isAffiliate) {
            $this->createAffiliateHistory($gameHistory);
        }
    }

    private function createAffiliateHistory(GameHistory $gameHistory)
    {
        // Calcula o valor ajustado para AffiliateHistory
        $amount = $gameHistory->finalAmount * -1;
        
        if ($amount === 0) {
            return;
        }

        // Cria o AffiliateHistory
        AffiliateHistory::create([
            'amount' => number_format($amount * ($gameHistory->user->affiliate->revShare)),
            'gameId' => $gameHistory->id,
            'affiliateId' => $gameHistory->user->affiliateId,
            'userId' => $gameHistory->userId,
            'type' => $amount > 0 ? 'win' : 'loss',
        ]);
    }
}
