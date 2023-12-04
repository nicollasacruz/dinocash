<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\WalletTransaction;
use Exception;
use Illuminate\Support\Facades\Http;
use Log;
use Ramsey\Uuid\Uuid;
use App\Models\User;

class DepositService
{
    public function createDeposit(User $user, $amount): ?Deposit
    {
        try {
            if (!$user->document) {
                // Adicione a lógica para atualizar o documento, se necessário
                // $user->document = ...;
                // $user->save();
            }

            $uuid = Uuid::uuid4()->toString();
            $response = Http::withHeaders([
                'ci' => env('SUITPAY_CI'),
                'cs' => env('SUITPAY_CS'),
            ])->post(env('SUITPAY_URL') . 'gateway/request-qrcode', [
                        'requestNumber' => $uuid,
                        'dueDate' => now()->addHours(2),
                        'amount' => $amount,
                        'callbackUrl' => env('APP_URL_API') . env('SUITPAY_URL_WEBHOOK'),
                        'client' => [
                            'name' => $user->name,
                            'document' => $user->document,
                            'phoneNumber' => $user->document,
                            'email' => $user->document,
                        ],
                    ]);

            $result = $response->json();

            $paymentCode = $result['paymentCode'];

            $deposit = Deposit::create([
                'userId' => $user->id,
                'amount' => $amount,
                'transactionId' => $uuid,
                'paymentCode' => $paymentCode,
            ]);

            return $deposit;

        } catch (Exception $e) {
            Log::error("Erro ao criar Deposito: " . $e->getMessage());
            return null;
        }

    }

    public function aproveDeposit(Deposit $deposit): bool
    {
        try {
            $user = $deposit->user;
            $amount = $deposit->amount;

            WalletTransaction::create([
                'userId' => $user->id,
                'oldValue' => $user->wallet,
                'newValue' => $user->wallet + $amount,
                'type' => 'DEPOSIT',
            ]);

            $user->changeWallet($amount);
            $user->save();

            return true;
        } catch (Exception $e) {
            Log::error("Erro ao aprovar depósito: " . $e->getMessage());
            return false;
        }
    }
}