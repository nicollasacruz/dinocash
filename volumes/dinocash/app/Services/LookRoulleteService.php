<?php

namespace App\Services;

use App\Models\BonusCampaign;
use App\Models\BonusWalletChange;
use App\Models\Deposit;
use App\Models\Setting;
use Exception;
use Log;
use App\Models\User;
use App\Notifications\PushDemoGGR;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class LookRoulleteService
{
    private BonusService $bonusService;

    public function __construct(BonusService $bonusService)
    {
        $this->bonusService = $bonusService;
    }

    public function addBonusRoullet(User $user, float $value)
    {
        return $this->bonusService->createBonusLooked($user, $value);
    }

    public function optionRoulletReward(User $user, int $option): bool
    {
        return true;
    }
}
