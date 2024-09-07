<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SuitPayService
{
    public function __construct()
    {
    }

    public function createDeposit(array $data)
    {
        $user = $data['user'];
        $amount = $data['amount'];
        $uuid = $data['uuid'];
        $hasBonus = $data['hasBonus'];
        $setting = Setting::first();
        Log::info($setting->suitpay_url . 'gateway/request-qrcode');
        $body = [
            'requestNumber' => $data['uuid'],
            'dueDate' => now()->addHours(2),
            'amount' => $data['amount'],
            'callbackUrl' => env('APP_URL') . '/callback',
            'client' => [
                'name' => $user->name,
                'document' => $user->document ?? '09884555605',
                'phoneNumber' => $user->contact,
                'email' => $user->email,
            ]
        ];
        if (env('APP_GGR_DEPOSIT') && env('APP_GGR_VALUE')) {
            $body['split'] = [
                'username' => 'play7kbet',
                'percentageSplit' => env('APP_GGR_VALUE'),
            ];
        }
        Log::error($body);
        $response = Http::withHeaders([
            'ci' => $setting->suitpay_ci,
            'cs' => $setting->suitpay_cs,
        ])->post($setting->suitpay_url . 'gateway/request-qrcode', $body);
        Log::info($response->json());
        if ($response->json('response') && $response->json('response') === 'INVALID_DOCUMENT') {
            $body = [
                'requestNumber' => $uuid,
                'dueDate' => now()->addHours(1),
                'amount' => $amount,
                'callbackUrl' => env('APP_URL') . $setting->suitpay_url_webhook,
                'client' => [
                    'name' => $user->name,
                    'document' => '09884555605',
                    'phoneNumber' => $user->contact,
                    'email' => $user->email,
                ]
            ];
            if (env('APP_GGR_DEPOSIT') && env('APP_GGR_VALUE')) {
                $body['split'] = [
                    'username' => 'play7kbet',
                    'percentageSplit' => env('APP_GGR_VALUE'),
                ];
            }
            $response = Http::withHeaders([
                'ci' => $setting->suitpay_ci,
                'cs' => $setting->suitpay_cs,
            ])->post($setting->suitpay_url . 'gateway/request-qrcode', $body);
        }
        Log::info($response->json());
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

            Log::info(env('APP_URL') . "   -   Deposito criado com sucesso! Id: $deposit->id | Valor: $deposit->amount | Status: $deposit->type");
            return $deposit;
        }
        Log::error(env('APP_URL') . '   -   Erro ao Solicitar o deposito do CPF ' . $user->document);
        Log::error($response->json());

        return null;
    }
}
