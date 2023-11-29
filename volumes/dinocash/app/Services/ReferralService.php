<?php

namespace App\Services;

use App\Models\User;
use App\Models\GameHistory;

class ReferralService
{
  public function getTopReferralsByProfit()
  {
    return GameHistory::with('user')
      ->where('type', 'win')
      ->selectRaw('user.affiliateId, SUM(finalAmount) as totalProfit')
      ->groupBy('user.affiliateId')
      ->orderByDesc('totalProfit')
      ->limit(3)
      ->get();
  }

  public function getTopReferralsByLoss()
  {
    return GameHistory::with('user')
      ->where('type', 'loss')
      ->selectRaw('user.affiliateId, SUM(finalAmount) as totalLoss')
      ->groupBy('user.affiliateId')
      ->orderByDesc('totalLoss')
      ->limit(3)
      ->get();
  }
}
