<?php

namespace App\Services;

use App\Jobs\ProcessAutoWithdraw;
use App\Models\AffiliateHistory;
use App\Models\AffiliateWithdraw;
use App\Models\WalletTransaction;
use App\Models\Withdraw;
use Exception;
use Illuminate\Support\Facades\Http;
use Log;
use Ramsey\Uuid\Uuid;
use App\Models\User;

class WithdrawAffiliateService
{
    public function createWithdraw(User $affiliate, $amount)
    {
        try {
            $withdraw = AffiliateWithdraw::create([
                'userId' => $affiliate->id,
                'transactionId' => Uuid::uuid4()->toString(),
                'amount' => $amount,
                'type' => 'pending',
            ]);

            AffiliateHistory::create([
                'userId' => $affiliate->id,
                'amount' => $amount,
                'type' => 'WITHDRAW',
            ]);

            $affiliate->changeWalletAffiliate($amount * -1);
            $affiliate->save();

            return $withdraw;
        } catch (Exception $e) {
            Log::error("Erro ao criar Withdraw: " . $e->getMessage() . ' | ' . $e->getFile() . ' | ' . $e->getLine());
            return false;
        }
    }

    public function autoWithdraw(AffiliateWithdraw $withdraw)
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

        $data = $response->json();

        if ($data['response'] === 'OK') {
            return true;
        }

        return $data['response'];
    }

    public function aprove(AffiliateWithdraw $withdraw, $type): bool
    {
        try {
            if ($this->autoWithdraw($withdraw)) {
                $withdraw->update([
                    'type' => 'paid',
                    'approvedAt' => now(),
                ]);
            }

            return true;

        } catch (Exception $e) {
            Log::error('Erro ao aprovar o saque: ' . $e->getMessage());
            return false;
        }
    }

    public function reject(AffiliateWithdraw $withdraw): bool
    {
        try {
            $withdraw->update([
                'type' => 'rejected',
                'reprovedAt' => now(),
            ]);

            $user = $withdraw->user;
            $amount = $withdraw->amount;

            AffiliateHistory::create([
                'userId' => $withdraw->user->id,
                'amount' => $amount,
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
