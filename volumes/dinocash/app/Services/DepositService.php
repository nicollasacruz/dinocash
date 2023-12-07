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
                Log::error("Usuario não tem documento");
            }

            $uuid = Uuid::uuid4()->toString();
            $response = Http::withHeaders([
                'ci' => env('SUITPAY_CI'),
                'cs' => env('SUITPAY_CS'),
            ])->post(env('SUITPAY_URL') . 'gateway/request-qrcode', [
                        'requestNumber' => $uuid,
                        'dueDate' => now()->addHours(2),
                        'amount' => $amount,
                        'callbackUrl' => env('APP_URL') . env('SUITPAY_URL_WEBHOOK'),
                        'client' => [
                            'name' => $user->name,
                            'document' => $user->document,
                            'phoneNumber' => $user->document,
                            'email' => $user->document,
                        ],
                    ]);
            $result = $response->json('paymentCode');
            Log::alert($result);
            $deposit = Deposit::create([
                'userId' => $user->id,
                'amount' => $amount,
                'transactionId' => $uuid,
                'type' => 'pending',
                'paymentCode' => $result,
            ]);

            Log::info("Deposito criado com sucesso! Id: {$deposit->id} | Valor: {$deposit->amount} | Status: {$deposit->type}");
            return $deposit;

        } catch (Exception $e) {
            Log::error("Erro ao criar Deposito: " . $e->getMessage() . ' - '. $e->getFile() .' - '. $e->getLine());
            return null;
        }

    }

    public function aproveDeposit(Deposit $deposit): bool
    {
        try {
            $user = $deposit->user;
            $amount = $deposit->amount;

            WalletTransaction::create([
                'userId' => $deposit->userId,
                'oldValue' => $user->wallet,
                'newValue' => $user->wallet + $amount,
                'type' => 'DEPOSIT',
            ]);

            $deposit->type = 'paid';
            $deposit->save();

            Log::info("Deposito aprovado com sucesso! Id: {$deposit->id} | Valor: {$deposit->amount} | Status: {$deposit->type}");
            return true;
        } catch (Exception $e) {
            Log::error("Erro ao aprovar depósito: " . $e->getMessage());
            return false;
        }
    }
}