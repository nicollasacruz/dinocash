<?php

namespace App\Services;

use App\Models\AffiliateHistory;
use App\Models\User;
use App\Models\GameHistory;

class ReferralService
{
    public function getTopReferralsByProfit()
    {
        return GameHistory::join('users', 'game_histories.userId', '=', 'users.id')
            ->join('users as affiliate', 'users.affiliateId', '=', 'affiliate.id')
            ->where('game_histories.type', 'loss')
            ->selectRaw('affiliate.email, SUM(game_histories.finalAmount) * -1 as totalGain')
            ->groupBy('affiliate.email')
            ->orderByDesc('totalGain')
            ->limit(5)
            ->get();
    }

    public function getTopReferralsByLoss()
    {
        return GameHistory::join('users', 'game_histories.userId', '=', 'users.id')
            ->join('users as affiliate', 'users.affiliateId', '=', 'affiliate.id')
            ->where('game_histories.type', 'win')
            ->selectRaw('affiliate.email, SUM(game_histories.finalAmount) * -1 as totalPayed')
            ->groupBy('affiliate.email')
            ->orderBy('totalPayed', 'asc')
            ->limit(5)
            ->get();
    }
    
    public function getTopReferralsByCPA()
    {
        return AffiliateHistory::join('users as affiliate', 'affiliate_histories.affiliateId', '=', 'affiliate.id')
            ->where('affiliate_histories.type', 'CPA')
            ->selectRaw('affiliate.email, COUNT(affiliate_histories.id) as totalCount')
            ->groupBy('affiliate.email')
            ->orderByDesc('totalCount')
            ->limit(10)
            ->get();
    }
}
