<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\Setting;
use App\Models\WalletTransaction;
use App\Models\Withdraw;
use Exception;
use Illuminate\Support\Facades\Http;
use Ramsey\Uuid\Uuid;
use App\Models\User;

class WithdrawService
{
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

        // Faça algo com os dados, por exemplo:
        dd($data);
    }

    public function createWithdraw(User $user, $amount)
    {
        try {
            Withdraw::create([
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

            return true;

        } catch (Exception $e) {
            return false;
        }
    }

    public function aprove(Withdraw $withdraw): bool
    {
        if ($this->autoWithdraw($withdraw)) {
            $withdraw->update([
                'type' => 'paid',
                'approvedAt' => now(),
            ]);

            return true;
        }
        return false;
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

            return false;
        }
    }
}