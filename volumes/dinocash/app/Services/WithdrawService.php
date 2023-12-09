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
            if (!$user->isAffiliate) {
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
            }

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
        $document = preg_replace('/[^0-9]/', '', $withdraw->user->document);
        $response = Http::withHeaders([
            'ci' => env('SUITPAY_CI'),
            'cs' => env('SUITPAY_CS'),
        ])->post(env('SUITPAY_URL') . 'gateway/pix-payment', [
                    'value' => $withdraw->amount,
                    'key' => $document,
                    'typeKey' => 'document',
                    'callbackUrl' => env('APP_URL_API') . env('SUITPAY_URL_WEBHOOK_SEND'),
                ]);

        // Obtenha a resposta da requisição
        $data = $response->json();

        Log::info('AUTOPAY RESPONSE' . json_encode($data));

        if ($data['response'] === 'OK') {
            return [
                'success' => true
            ];
        } elseif ($data['response'] === 'PIX_KEY_NOT_FOUND') {
            Log::error('RESULTADO AUTOPAY WITHDRAW - Chave pix não encontrada');
            return [
                'success' => false,
                'message' => 'Chave Pix não encontrada!'
            ];
        } elseif ($data['response'] === 'NO_FUNDS') {
            Log::error('RESULTADO AUTOPAY WITHDRAW - Sem Saldo na conta');
            return [
                'success' => false,
                'message' => 'Sem Saldo na conta'
            ];
        }

        return false;
    }

    public function aprove(Withdraw $withdraw): array
    {
        try {
            $saque = $this->autoWithdraw($withdraw);
            if ($saque['success']) {
                $withdraw->update([
                    'type' => 'paid',
                    'approvedAt' => now(),
                ]);
            }
            return [
                'success' => $saque['success'],
                'message' => $saque['message'],
            ];
            ;

        } catch (Exception $e) {
            Log::error('Erro ao aprovar o saque: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro interno',
            ];
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
