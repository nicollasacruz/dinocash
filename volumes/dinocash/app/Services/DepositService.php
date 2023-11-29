<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\WalletTransaction;
use Exception;
use Illuminate\Support\Facades\Http;
use Ramsey\Uuid\Uuid;
use App\Models\User;

class DepositService
{
    public function createDeposit(User $user, $amount): Deposit
    {
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

        return $user->createDeposit($amount, $uuid, $paymentCode);
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

            $user->wallet += $amount;
            $user->save();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}