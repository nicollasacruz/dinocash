<?php

namespace App\Services;

use App\Models\BonusCampaign;
use App\Models\BonusWalletChange;
use App\Models\Deposit;
use App\Models\Setting;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use App\Models\User;
use App\Notifications\PushDemoGGR;
use Illuminate\Support\Facades\Notification;

class DepositService
{
    public function createDeposit(User $user, $amount, bool $hasBonus)
    {
        try {
            if (!$user->document) {
                Log::error("Usuario não tem documento");
            }

            $uuid = Uuid::uuid4()->toString();
            if (env('PAYMENT_SERVICE') == 'SUITPAY') {
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
                        'username' => 'dinocash',
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
                            'username' => 'dinocash',
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
                        'hasBonus' => $hasBonus,
                    ]);

                    Log::info("Deposito criado com sucesso! Id: {$deposit->id} | Valor: {$deposit->amount} | Status: {$deposit->type}");
                    return $deposit;
                }
                Log::error('Erro ao Solicitar o deposito do CPF ' . $user->document);
                Log::error($response->json());

                return null;
            } elseif (env('PAYMENT_SERVICE') == 'EZZEBANK') {

                $response = Http::withHeaders([
                    'Authorization' => 'Basic ' . base64_encode(env('EZZEBANK_CI') . ':' . env('EZZEBANK_CS'))
                ])
                    ->asForm()
                    ->post(env('EZZEBANK_URL') . 'oauth/token', [
                        'grant_type' => 'client_credentials',
                    ]);

                if ($response->successful()) {
                    $accessToken = $response->json('access_token');
                } else {
                    $errorMessage = $response->body();

                    Log::error($errorMessage . '  -   Erro no Login Ezzebank');
                    return null;
                }

                if ($accessToken) {
                    $document = $user->document;
                    // Remove todos os caracteres não numéricos
                    $document = preg_replace("/[^0-9]/", "", $document);

                    $response = Http::withToken($accessToken)
                        ->get(env('EZZEBANK_URL') . 'services/cpf?docNumber=' . $document);

                    if ($response->successful()) {
                        $responseData = $response->json('Status');
                        $status = $responseData['Message'];
                    } else {
                        $errorMessage = $response->body();

                        Log::error($errorMessage . '  -   Erro no check CPF Ezzebank      -     ' . $document);
                        $document = '09884555605';
                    }

                    $response = Http::withToken($accessToken)
                        ->post(env('EZZEBANK_URL') . 'pix/qrcode', [
                            'amount' => $amount,
                            'payerQuestion' => 'Pagamento referente produto/serviço',
                            'external_id' => $uuid,
                            'payer' => [
                                'name' => $user->name ?? $user->email,
                                'document' => $document,
                            ],
                        ]);

                    if ($response->successful()) {
                        $qrCode = $response->json('emvqrcps');

                        $deposit = Deposit::create([
                            'userId' => $user->id,
                            'amount' => $amount,
                            'transactionId' => $response->json('transactionId'),
                            'externalId' => $response->json('external_id'),
                            'type' => 'pending',
                            'paymentCode' => $qrCode,
                            'hasBonus' => $hasBonus,
                        ]);

                        Log::info("Deposito criado com sucesso! Id: {$deposit->id} | Valor: {$deposit->amount} | Status: {$deposit->type}");
                        return $deposit;
                    } else {
                        $errorMessage = $response->body();

                        Log::error($errorMessage . '  -   Erro no Gerar QrCode Ezzebank do usuario   -  ' . $user->email);

                    }
                }
            }
        } catch (Exception $e) {
            Log::error(env('APP_URL') . "  -  Erro ao criar Deposito: " . $e->getMessage() . ' - ' . $e->getFile() . ' - ' . $e->getLine());
            return null;
        }
    }

    public function aproveDeposit(Deposit $deposit): bool
    {
        try {
            $bonusService = new BonusService();
            $user = User::find($deposit->user->id);
            $amount = $deposit->amount;

            $deposit->type = 'paid';
            $deposit->save();
            // $user->changeWallet($amount);
            // $user->save();

            $bonusService->createBonusDeposit($deposit);

            try {
                if (env('APP_GGR_DEPOSIT') && env('APP_GGR_VALUE')) {
                    $ggr = env('APP_GGR_VALUE') * 1 / 100;
                    $value = $deposit->amount * $ggr;
                    Log::alert("PAGAMENTO GGR - {$value}");
                    User::where('role', 'admin')->each(function ($user) use ($deposit, $ggr) {
                        Notification::send($user, new PushDemoGGR('R$ ' . number_format(floatval($deposit->amount * $ggr), 2, ',', '.')));
                    });
                }
            } catch (Exception $e) {
                Log::error('Erro de notificar - ' . $e->getMessage());
            }
            return true;
        } catch (Exception $e) {
            Log::error("Erro ao aprovar depósito: " . $e->getMessage());
            return false;
        }
    }

    public function createBonusDeposit(Deposit $deposit): bool
    {
        try {
            $settings = Setting::first();
            $bonusPercent = $settings->bonusPercent;
            $bonusRollover = $settings->rolloverBonus;
            $amount = $deposit->amount;
            $amountBonus = $amount * $bonusPercent / 100;
            $user = $deposit->user;

            $bonus = BonusCampaign::create([
                'amount' => $amountBonus,
                'amountMovement' => 0,
                'bonusPercent' => $bonusPercent,
                'rollover' =>  $bonusRollover,
                'userId' => $user->id,
                'type' => 'deposit',
                'status' => 'active',
                'expireAt' => now()->addDays(30),
            ]);

            BonusWalletChange::create([
                'bonusCampaignId' => $bonus->id,
                'amountOld' => $user->bonusWallet,
                'amountNew' => $user->bonusWallet + $amountBonus,
                'type' => 'credit',
            ]);

            $user->bonusWallet += $amountBonus;
            $user->save();

            return true;
        } catch (Exception $e) {
            Log::error('Erro de notificar - ' . $e->getMessage());
            return false;
        }
    }
}
