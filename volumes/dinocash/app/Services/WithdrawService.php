<?php

namespace App\Services;

use App\Events\WalletChanged;
use App\Models\BonusWalletChange;
use App\Models\Setting;
use App\Models\Withdraw;
use Exception;
use Illuminate\Support\Facades\Http;
use Ramsey\Uuid\Uuid;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Log;

class WithdrawService
{
    public function createWithdraw(User $user, $amount): array
    {
        try {
            $onlyWallet = false;
            $onlyBonus = false;
            $setting = Setting::first();
            if ($amount < $setting->minWithdraw) {
                return [
                    'success' => 'error',
                    'message' => 'Saque mínimo de R$ ' . number_format($setting->minWithdraw, 2, ',', '.'),
                ];
            }
            if ($amount > $setting->maxWithdraw) {
                return [
                    'success' => 'error',
                    'message' => 'Saque máximo de R$ ' . number_format($setting->maxWithdraw, 2, ',', '.'),
                ];
            }
            if ($amount > $user->wallet + $user->bonusWallet || $amount < 0) {
                return [
                    'success' => 'error',
                    'message' => 'Você não possui esse valor para saque',
                ];
            }

            if ($amount <= $user->wallet) {
                $onlyWallet = true;
            } elseif ($user->wallet == 0) {
                $onlyBonus = true;
            }

            $totalDeposits = $user->deposits->where('type', 'paid')->sum('amount') ?? 1;

            $totalRoll = $user->gameHistories->where('type', '!=', 'locked')->where('amountType', 'real')->sum('amount');
            $hasWIthdrawToday = $user->withdraws
                ->filter(function ($withdraw) {
                    return $withdraw->updated_at->isToday();
                })->first();

            if ($hasWIthdrawToday) {
                return response()->json([
                    'success' => 'error',
                    'message' => 'Só é possível fazer um saque por dia.',
                ]);
            }

            $amountAvaliableWallet = 0;
            $amountAvaliableBonus = 0;


            if ($onlyBonus || (!$onlyWallet && !$onlyBonus)) {
                $bonus = $user->bonusCampaings->where('status', 'active')->first();
                $amountAvaliableBonus = $bonus->amountMovement >= $bonus->rollover * $bonus->amount ? $user->bonusWallet : 0;
                $amountAvaliableWallet = $totalRoll >= $totalDeposits * $setting->rollover ? $user->wallet : 0;
                $amountAvaliable =  $amountAvaliableBonus + $amountAvaliableWallet;
                if ($onlyBonus) {
                    $amountAvaliable =  $amountAvaliableBonus;
                }
            } elseif ($onlyWallet) {
                $amountAvaliableWallet = $totalRoll >= $totalDeposits * $setting->rollover ? $user->wallet : 0;
                $amountAvaliable = $amountAvaliableWallet;
            }

            if (!$user->isAffiliate) {
                Log::info("Total ROLL       $totalRoll     -    $user->email");
            }
            
            if ($amount > $amountAvaliable) {
                return [
                    'success' => 'error',
                    'message' => "Valor indisponível para saque, você precisa movimentar mais para sacar.",
                ];
            }

            $amountRemaning = $amount;

            if ($onlyWallet) {
                WalletTransaction::create([
                    'userId' => $user->id,
                    'oldValue' => $user->wallet,
                    'newValue' => ($user->wallet * 1) - $amountRemaning,
                    'type' => 'withdraw',
                ]);
                $user->wallet = number_format(($user->wallet * 1) - $amountRemaning, 2, '.', '');
                $amountRemaning = 0;
            } elseif ($onlyBonus || (!$onlyWallet && !$onlyBonus)) {
                if (!$onlyBonus) {
                    WalletTransaction::create([
                        'userId' => $user->id,
                        'oldValue' => $user->wallet,
                        'newValue' => ($user->wallet * 1) - $amountAvaliableWallet,
                        'type' => 'withdraw partial',
                    ]);
                    $user->wallet = number_format(0, 2, '.', '');
                    $amountRemaning -= $amountAvaliableWallet;
                }
                if ($amountRemaning > 0) {
                    BonusWalletChange::create([
                        'bonusCampaignId' => $bonus->id,
                        'amountOld' => $user->bonusWallet,
                        'amountNew' => $user->bonusWallet - $amountRemaning,
                        'type' => $amountRemaning === $amount ? 'withdraw' : 'withdraw partial',
                    ]);

                    $user->bonusWallet -= $amountRemaning;
                }
            }

            $user->save();

            $message = [
                "id" => $user->id,
                "wallet" => $user->wallet * 1,
                "bonus" => $user->bonusWallet * 1,
            ];

            event(new WalletChanged($message));

            if (!$user->isAffiliate) {
                $withdraw = Withdraw::create([
                    'userId' => $user->id,
                    'transactionId' => Uuid::uuid4()->toString(),
                    'amount' => $amount,
                    'type' => 'pending',
                ]);

                if ($setting->autoPayWithdraw && (float) $withdraw->amount <= $setting->maxAutoPayWithdraw) {
                    $autoPay = $this->aprove($withdraw);
                };
            }

            return [
                'success' => 'success',
                'message' => 'Saque realizado com successo.',
            ];
        } catch (Exception $e) {
            Log::error("Erro ao criar Withdraw: " . $e->getMessage());
            return [
                'success' => 'error',
                'message' => 'Saque não pode realizado.',
            ];
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
