<?php

namespace App\Services;

use App\Models\BonusCampaign;
use App\Models\BonusWalletChange;
use App\Models\Deposit;
use App\Models\Setting;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use App\Models\User;
use App\Notifications\PushDemoGGR;
use Illuminate\Support\Facades\Notification;

class DepositService
{
    /**
     * @param User $user
     * @param $amount
     * @param bool $hasBonus
     * @return Deposit|null
     */
    public function createDeposit(User $user, $amount, bool $hasBonus): ?Deposit
    {
        try {
            if (!$user->document) {
                Log::error("Usuario não tem documento");
            }
            $data = [
                'uuid' => Uuid::uuid4()->toString(),
                'user' => $user,
                'amount' => $amount,
                'type' => 'pending',
                'hasBonus' => $hasBonus,
            ];
            $settings = Setting::first();
            if ($settings->payment_service == 'SUITPAY') {
                return (new SuitPayService)->createDeposit($data);
            }
            elseif ($settings->payment_service == 'EZZEBANK') {
                return (new EzzebankService)->createDeposit($data);
            }
            elseif ($settings->payment_service == 'BSPAY') {
                return (new BsPayService)->createDeposit($data);
            }
            elseif ($settings->payment_service == 'CASHTIME') {
                return (new CashTimeService())->createDeposit($data);
            }
            elseif ($settings->payment_service == 'AGILLEPAY') {
                return (new AgillePayService)->createDeposit($data);
            }
            else {
                Log::error("Serviço de pagamento não encontrado");
                return null;
            }
        } catch (Exception $e) {

            Log::error(env('APP_URL') . "  -  Erro ao criar Deposito: " . $e->getMessage() . ' - ' . $e->getFile() . ' - ' . $e->getLine());
            return null;
        }
    }

    public function aproveDeposit(Deposit $deposit): bool
    {
        try {
            $bonusService = new BonusService();
            $user = User::find($deposit->user->id);
            $amount = $deposit->amount;

            $deposit->type = 'paid';
            $deposit->save();
            // $user->changeWallet($amount);
            // $user->save();

            $bonusService->createBonusDeposit($deposit);

            try {
                if (env('APP_GGR_DEPOSIT') && env('APP_GGR_VALUE')) {
                    $ggr = env('APP_GGR_VALUE') * 1 / 100;
                    $value = $deposit->amount * $ggr;
                    Log::alert("PAGAMENTO GGR - {$value}");
                    User::where('role', 'admin')->each(function ($user) use ($deposit, $ggr) {
                        Notification::send($user, new PushDemoGGR('R$ ' . number_format(floatval($deposit->amount * $ggr), 2, ',', '.')));
                    });
                }
            } catch (Exception $e) {
                Log::error('Erro de notificar - ' . $e->getMessage());
            }
            return true;
        } catch (Exception $e) {
            Log::error("Erro ao aprovar depósito: " . $e->getMessage());
            return false;
        }
    }

    public function createBonusDeposit(Deposit $deposit): bool
    {
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
}
