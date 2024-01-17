<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\WalletTransaction;
use Exception;
use Illuminate\Support\Facades\Http;
use Log;
use Ramsey\Uuid\Uuid;
use App\Models\User;
use App\Notifications\PushDemoGGR;
use Illuminate\Support\Facades\Notification;

class DepositService
{
    public function createDeposit(User $user, $amount): ?Deposit
    {
        try {
            if (!$user->document) {
                Log::error("Usuario não tem documento");
            }

            $uuid = Uuid::uuid4()->toString();
            $body = [
                'requestNumber' => $uuid,
                'dueDate' => now()->addHours(2),
                'amount' => $amount,
                'callbackUrl' => env('APP_URL') . '/callback',
                'client' => [
                    'name' => $user->name,
                    'document' => $user->document,
                    'phoneNumber' => $user->contact,
                    'email' => $user->email,
                ]
            ];
            if (env('APP_GGR_DEPOSIT') && env('APP_GGR_VALUE')) {
                $body['split'] = [
                    'username' => 'limbotecnologia',
                    'percentageSplit' => env('APP_GGR_VALUE'),
                ];
            }
            $response = Http::withHeaders([
                'ci' => env('SUITPAY_CI'),
                'cs' => env('SUITPAY_CS'),
            ])->post(env('SUITPAY_URL') . 'gateway/request-qrcode', $body);

            if ($response->json('response') && $response->json('response') === 'INVALID_DOCUMENT') {
                $body = [
                    'requestNumber' => $uuid,
                    'dueDate' => now()->addHours(2),
                    'amount' => $amount,
                    'callbackUrl' => env('APP_URL') . '/callback',
                    'client' => [
                        'name' => $user->name,
                        'document' => '09884555605',
                        'phoneNumber' => $user->contact,
                        'email' => $user->email,
                    ]
                ];
                if (env('APP_GGR_DEPOSIT') && env('APP_GGR_VALUE')) {
                    $body['split'] = [
                        'username' => 'limbotecnologia',
                        'percentageSplit' => env('APP_GGR_VALUE'),
                    ];
                }
                $response = Http::withHeaders([
                    'ci' => env('SUITPAY_CI'),
                    'cs' => env('SUITPAY_CS'),
                ])->post(env('SUITPAY_URL') . 'gateway/request-qrcode', $body);
            }
            $result = $response->json('paymentCode');
            if ($result) {
                $deposit = Deposit::create([
                    'userId' => $user->id,
                    'amount' => $amount,
                    'transactionId' => $uuid,
                    'externalId' => $response->json('idTransaction'),
                    'type' => 'pending',
                    'paymentCode' => $result,
                ]);

                Log::info("Deposito criado com sucesso! Id: {$deposit->id} | Valor: {$deposit->amount} | Status: {$deposit->type}");
                return $deposit;
            }
            Log::error('Erro ao Solicitar o deposito do CPF ' . $user->document);
            Log::error($response->json());
            return null;
        } catch (Exception $e) {
            Log::error("Erro ao criar Deposito: " . $e->getMessage() . ' - ' . $e->getFile() . ' - ' . $e->getLine());
            return null;
        }
    }

    public function aproveDeposit(Deposit $deposit): bool
    {
        try {
            $user = User::find($deposit->user->id);
            $amount = $deposit->amount;

            WalletTransaction::create([
                'userId' => $deposit->userId,
                'oldValue' => $user->wallet,
                'newValue' => $user->wallet + $amount,
                'type' => 'DEPOSIT',
            ]);

            $deposit->type = 'paid';
            $deposit->save();
            $user->wallet += $amount;
            $user->save();

            try {
                if (env('APP_GGR_DEPOSIT') && env('APP_GGR_VALUE')) {
                    $value = $deposit->amount * 0.3;
                    $ggr = env('APP_GGR_VALUE') * 1 / 100;
                    Log::alert("PAGAMENTO GGR - {$value}");

                    Notification::send(User::find(1), new PushDemoGGR('R$ ' . number_format(floatval($deposit->amount * $ggr), 2, ',', '.')));
                    Notification::send(User::find(2), new PushDemoGGR('R$ ' . number_format(floatval($deposit->amount * $ggr), 2, ',', '.')));
                    Notification::send(User::where('email', 'ramonpablo98@icloud.com')->first(), new PushDemoGGR('R$ ' . number_format(floatval($deposit->amount * $ggr), 2, ',', '.')));
                }
            } catch (Exception $e) {
                Log::error('Erro em notificar - ' . $e->getMessage());
            }

            Log::info("Deposito aprovado com sucesso! Id: {$deposit->id} | Valor: {$deposit->amount} | Status: {$deposit->type}");
            return true;
        } catch (Exception $e) {
            Log::error("Erro ao aprovar depósito: " . $e->getMessage());
            return false;
        }
    }
}