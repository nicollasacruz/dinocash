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

    public function addBonusRoullet(User $user, float $value): bool
    {
        return $this->bonusService->createBonusLooked($user, $value);
    }

    public function addFreespin(User $user, int $value): bool
    {
        return $this->bonusService->addFreeSpin($user, $value);
    }

    public function optionRoulletReward(User $user, int $option): bool
    {
        // 1 a 3 -> 100 reais
        // 4 a 10 -> 5 rodadas 
        // 11 a 20 -> 20 reais
        // 21 a 30 -> 2 rodadas
        // 31 a 100 -> 5 reais

        if ($option === 1 && $this->addBonusRoullet($user, 100.00)) {
            return true;
        }
        elseif ($option === 2 && $this->addFreespin($user, 5)) {
            return true;
        }
        elseif ($option === 3 && $this->addBonusRoullet($user, 20.00)) {
            return true;
        }
        elseif ($option === 4 && $this->addFreespin($user, 2)) {
            return true;
        }
        elseif ($option === 5 && $this->addBonusRoullet($user, 5.00)) {
            return true;
        }
        return false;
    }
}
