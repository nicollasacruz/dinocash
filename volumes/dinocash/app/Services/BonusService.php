<?php

namespace App\Services;

use App\Models\BonusCampaign;
use App\Models\BonusWalletChange;
use App\Models\Deposit;
use App\Models\Setting;
use Exception;
use App\Models\User;
use App\Notifications\PushDemoGGR;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class BonusService
{
    public function createBonusDeposit(Deposit $deposit): bool
    {
        if (!$deposit->hasBonus || $deposit->user->bonusCampaings()->where('status', 'active')->count() >= Setting::first()->maxDepositBonusToUser) {
            return false;
        }

        try {
            $settings = Setting::first();
            $bonusPercent = $settings->bonusPercent;
            $bonusRollover = $settings->rolloverBonus;
            $amount = $deposit->amount;
            $amountBonus = $amount * $bonusPercent / 100;
            $user = $deposit->user;

            $bonus = BonusCampaign::create([
                'amount' => $amountBonus,
                'amountMovement' => 0,
                'bonusPercent' => $bonusPercent,
                'rollover' =>  $bonusRollover,
                'userId' => $user->id,
                'type' => 'deposit',
                'status' => 'active',
                'expireAt' => now()->addDays(30),
            ]);

            BonusWalletChange::create([
                'bonusCampaignId' => $bonus->id,
                'amountOld' => $user->bonusWallet,
                'amountNew' => $user->bonusWallet + $amountBonus,
                'type' => 'credit',
            ]);

            $user->bonusWallet += $amountBonus;
            $user->save();

            return true;
        } catch (Exception $e) {
            Log::error('Erro de notificar - ' . $e->getMessage());
            return false;
        }
    }

    public function createBonusLooked(User $user, $amountBonus): bool
    {
        try {
            $bonus = $this->getBonusCampaingActive($user);
            if ($bonus->type === 'bonus') {
                $bonus->type = 'roulletBonus';
            }

            BonusWalletChange::create([
                'bonusCampaignId' => $bonus->id,
                'amountOld' => $user->bonusWallet,
                'amountNew' => $user->bonusWallet + $amountBonus,
                'type' => 'credit',
            ]);

            $user->bonusWallet += $amountBonus;
            $user->save();
            $bonus->save();
            
            $this->addFreeSpin($user, 20);

            return true;
        } catch (Exception $e) {
            Log::error('Erro de createBonusLooked - ' . $e->getMessage());
            return false;
        }
    }

    public function addFreeSpin(User $user, int $value): bool
    {
        try {
            $bonus = $this->getBonusCampaingActive($user);
            $settingsAmountFreeSpin = Setting::first()->amountFreeSpin;
            if ($bonus->type === 'bonus') {
                $bonus->type = 'freespin';
            }
            $amountFreeSpin = floatval($settingsAmountFreeSpin * $value);
            $bonus->amount += $amountFreeSpin;
            $user->freespin += $value;
            $bonus->save();
            $user->save();

            return true;
        } catch (Exception $e) {
            Log::error('Erro de createFreeSpin - ' . $e->getMessage() . '    -   ' . $e->getTraceAsString());
            return false;
        }
    }

    public function getBonusCampaingActive(User $user): BonusCampaign
    {
        $user = User::find($user->id);

        $bonus = $user->bonusCampaings->where('status', 'active')->first();

        if (!$bonus) {
            $bonusRollover = Setting::first()->rolloverBonus;
            $bonus = BonusCampaign::create([
                'amount' => 0,
                'amountMovement' => 0,
                'bonusPercent' => 0,
                'rollover' =>  $bonusRollover,
                'userId' => $user->id,
                'type' => 'bonus',
                'status' => 'active',
                'expireAt' => now()->addDays(30),
            ]);
        }

        return $bonus;
    }
}
