<?php

namespace App\Services;

use App\Jobs\ProcessAutoWithdraw;
use App\Models\WalletTransaction;
use App\Models\Withdraw;
use Exception;
use Illuminate\Support\Facades\Http;
use Log;
use Ramsey\Uuid\Uuid;
use App\Models\User;

class WithdrawService
{
    public function createWithdraw(User $user, $amount)
    {
        try {
            $withdraw = Withdraw::create([
                'userId' => $user->id,
                'transactionId' => Uuid::uuid4()->toString(),
                'amount' => $amount,
                'type' => 'pending',
            ]);

            WalletTransaction::create([
                'userId' => $user->id,
                'oldValue' => $user->wallet,
                'newValue' => $user->wallet - $amount,
                'type' => 'WITHDRAW',
            ]);

            $user->changeWallet($amount * -1);
            $user->save();

            return $withdraw;
        } catch (Exception $e) {
            Log::error("Erro ao criar Withdraw: " . $e->getMessage());
            return false;
        }
    }

    public function autoWithdraw(Withdraw $withdraw)
    {
        $response = Http::withHeaders([
            'ci' => env('SUITPAY_CI'),
            'cs' => env('SUITPAY_CS'),
        ])->post(env('SUITPAY_URL') . 'gateway/pix-payment', [
                    'value' => $withdraw->amount,
                    'key' => $withdraw->user->document,
                    'typeKey' => 'document',
                    'callbackUrl' => env('APP_URL_API') . env('SUITPAY_URL_WEBHOOK_SEND'),
                ]);

        // Obtenha a resposta da requisição
        $data = $response->json();

        if ($data['response'] === 'OK') {
            return true;
        }
        Log::error('RESULTADO AUTOPAY WITHDRAW - ' . json_encode($data));

        return false;
    }

    public function aprove(Withdraw $withdraw, $type): bool
    {
        try {
            if ($type === 'manual') {
                if ($this->autoWithdraw($withdraw)) {
                    $withdraw->update([
                        'type' => 'paid',
                        'approvedAt' => now(),
                    ]);
                } else {
                    return false;
                }

                return true;
            } elseif ($type === 'automatic') {
                // Agendar o job condicionalmente no Laravel Scheduler
                $this->scheduleAutoWithdrawJob($withdraw);
                return true;
            }

            return false;
        } catch (Exception $e) {
            Log::error('Erro ao aprovar o saque: ' . $e->getMessage());
            return false;
        }
    }

    private function scheduleAutoWithdrawJob(Withdraw $withdraw)
    {
        app('Illuminate\Console\Scheduling\Schedule')->job(new ProcessAutoWithdraw($withdraw))->weekdays()->at('12:00');
    }

    public function reject(Withdraw $withdraw): bool
    {
        try {
            $withdraw->update([
                'type' => 'rejected',
                'reprovedAt' => now(),
            ]);

            $user = $withdraw->user;
            $amount = $withdraw->amount;

            WalletTransaction::create([
                'userId' => $user->id,
                'oldValue' => $user->wallet,
                'newValue' => $user->wallet + $amount,
                'type' => 'WITHDRAW REJECTED',
            ]);

            $user->changeWallet($amount);
            $user->save();

            return true;
        } catch (Exception $e) {
            Log::error('Erro ao rejeitar o saque: ' . $e->getMessage());
            return false;
        }
    }
}
