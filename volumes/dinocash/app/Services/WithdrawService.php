<?php

namespace App\Services;

use App\Models\BonusWalletChange;
use App\Models\Withdraw;
use Exception;
use Illuminate\Support\Facades\Http;
use Ramsey\Uuid\Uuid;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class WithdrawService
{
    public function createWithdraw(User $user, $amount, $totalRoll, $rollover)
    {
        try {
            $bonus = $user->bonusCampaings->where('status', 'active')->first();
            $amountRemaning = $amount;
            $amountAvaliableWallet = $totalRoll / $rollover;
            $amountAvaliableBonus = $bonus->amountMovement >= $bonus->rollover * $bonus->amount ? $bonus->amountMovement / $bonus->rollover : 0;

            if ($user->wallet >= $amountRemaning) {
                if ($amountAvaliableWallet >= $amountRemaning) {
                    $user->changeWallet($amountRemaning * -1, 'withdraw');
                    $amountRemaning = 0;
                } else {

                    $user->changeWallet($amountAvaliableWallet * -1, 'withdraw partial');
                    $amountRemaning -= $amountAvaliableWallet;
                }
            }
            if ($amountRemaning > 0) {
                if ($user->bonusWallet >= $amountRemaning && $amountAvaliableBonus >= $amountRemaning) {
                    BonusWalletChange::create([
                        'bonusCampaignId' => $bonus->id,
                        'amountOld' => $user->bonusWallet,
                        'amountNew' => $user->bonusWallet - $amountRemaning,
                        'type' => $amountRemaning === $amount ? 'withdraw' : 'withdraw partial',
                    ]);

                    $user->bonusWallet -= $amountRemaning;
                } else {
                    Log::channel('telegram')->error(('Erro salvar bonus'));
                    return false;
                }
            }
            $user->save();
            if (!$user->isAffiliate) {
                $withdraw = Withdraw::create([
                    'userId' => $user->id,
                    'transactionId' => Uuid::uuid4()->toString(),
                    'amount' => $amount,
                    'type' => 'pending',
                ]);

                return $withdraw;
            }

            return true;
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

        $data = $response->json();

        Log::info('AUTOPAY RESPONSE' . json_encode($data));

        if ($data['response'] === 'OK') {
            return [
                'success' => true,
                'message' => 'Saque pago com sucesso.'
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

        return [
            'success' => false,
            'message' => 'Erro desconhecido'
        ];
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
        } catch (Exception $e) {
            Log::error('Erro ao aprovar o saque: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro interno',
            ];
        }
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

            $user->changeWallet($amount, 'withdraw rejected');
            $user->save();

            return true;
        } catch (Exception $e) {
            Log::error('Erro ao rejeitar o saque: ' . $e->getMessage());
            return false;
        }
    }
}
