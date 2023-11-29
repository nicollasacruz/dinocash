<?php

namespace App\Services;

use App\Models\User;
use App\Models\GameHistory;

class ReferralService
{
    public function getTopReferralsByProfit()
    {
        return GameHistory::join('users', 'game_histories.userId', '=', 'users.id')
            ->where('game_histories.type', 'win')
            ->selectRaw('users.affiliateId, SUM(game_histories.finalAmount) as totalProfit')
            ->groupBy('users.affiliateId')
            ->orderByDesc('totalProfit')
            ->limit(3)
            ->get();
    }

    public function getTopReferralsByLoss()
    {
        return GameHistory::join('users', 'game_histories.userId', '=', 'users.id')
            ->where('game_histories.type', 'loss')
            ->selectRaw('users.affiliateId, SUM(game_histories.finalAmount) as totalLoss')
            ->groupBy('users.affiliateId')
            ->orderByDesc('totalLoss')
            ->limit(3)
            ->get();
    }
}
