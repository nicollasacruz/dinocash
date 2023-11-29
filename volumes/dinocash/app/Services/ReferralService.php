<?php

namespace App\Services;

use App\Models\User;
use App\Models\GameHistory;

class ReferralService
{
  public function getTopReferralsByProfit()
  {
    return GameHistory::with('user.affiliate')
      ->where('type', 'win')
      ->selectRaw('affiliateId, SUM(finalAmount) as totalProfit')
      ->groupBy('affiliateId')
      ->orderByDesc('totalProfit')
      ->limit(3)
      ->get();
  }

  public function getTopReferralsByLoss()
  {
    return GameHistory::with('user.affiliate')
      ->where('type', 'loss')
      ->selectRaw('affiliateId, SUM(finalAmount) as totalLoss')
      ->groupBy('affiliateId')
      ->orderByDesc('totalLoss')
      ->limit(3)
      ->get();
  }
}
