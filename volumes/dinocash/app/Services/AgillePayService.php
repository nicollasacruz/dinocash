<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AgillePayService
{

    public function createDeposit(array $data): ?Deposit
    {
        $setting = Setting::first();

        $secret = env('SECRET_AGIILEPAY');
        $public = env('PUBLIC_AGIILEPAY');
        $endpoint = env('ENDPOINT_AGILLEPAY') . '/v1/transactions';

        $user = $data['user'];
        $user->email = str_replace(' ', '', $user->email);
        $user->save();
        $amount = $data['amount'];
        $uuid = $data['uuid'];
        $hasBonus = $data['hasBonus'];
        $isTax = $data['isTax'];
        $utm = $data['utmData'];

        $cpf = preg_replace('/\D/', '', $user->document);

        $body = [
            'customer' => [
                'document' => [
                    'number' => !empty($cpf) ? $cpf : '11534113690',
                    'type' => 'cpf',
                ],
                'name' => $user->name,
                'email' => str_replace(' ', '', $user->email),
                'phone' => $user->contact,
            ],
            'externalCode' => $uuid,
            'amount' => $amount * 100,
            'paymentMethod' => 'pix',
            'items' => [
                [
                    'tangible' => false,
                    'title' => 'SnakeDeposito',
                    'description' => 'SnakeDeposito',
                    'unitPrice' => $amount * 100,
                    'quantity' => 1,
                ],
            ],
            'shipping' => [
                'fee' => 0,
                'address' => [
                    'street' => 'string',
                    'streetNumber' => 'string',
                    'complement' => 'string',
                    'zipCode' => 'string',
                    'neighborhood' => 'string',
                    'city' => 'string',
                    'state' => 'string',
                    'country' => 'string',
                ],
            ],
            'postbackUrl' => env('APP_URL') . '/callback',
        ];

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'x-authorization-key' => $secret,
            'x-store-key' => $public,
        ])->post($endpoint, $body);

        $data = $response->json();
        if (!empty($data['id'])) {
            return $this->handleDepositResponse($user, $amount, $data['id'], $data, $hasBonus, $isTax, $utm);
        }
        return null;
    }

    private function handleDepositResponse($user, $amount, $uuid, $data, $hasBonus, $isTax, array|null|int $utm): ?Deposit
    {
        try {
            if (!empty($data['pix'])) {
                return $this->createDepositRecord($user, $amount, $uuid, $data['pix']['payload'], $hasBonus, $isTax, $utm);
            }
        } catch (\Exception $e) {
            Log::error("Erro ao criar deposito handleDepositResponse: " . $e->getMessage());
        }
        return null;
    }

    private function createDepositRecord($user, $amount, $uuid, $qrCode, $hasBonus, $isTax, array|null|int $utm): ?Deposit
    {
        try {
            $deposit = Deposit::create([
                'userId' => $user->id,
                'amount' => $amount,
                'transactionId' => $uuid,
                'externalId' => $uuid,
                'type' => 'pending',
                'paymentCode' => $qrCode,
                'hasBonus' => $hasBonus,
                'isTax' => $isTax
            ]);
//            try {
//                if (!empty($utm) && env('APP_URL') == 'https://dinofeliz.com') {
//                    $bodyNemo = [
//                        "name" => !$deposit->isTax ? 'Deposito' : 'Taxa',
//                        'transactionId' => $deposit->transactionId,
//                        'netValue' => $deposit->amount - 1 - ($deposit->amount * 0.03),
//                        'grossValue' => $deposit->amount,
//                        'status' => 'waiting_payment',
//                        'quantity' => 1,
//                        'paymentType' => 'pix',
//                        'utm_source' => $utm['source'] ?? '',
//                        'utm_medium' => $utm['medium'] ?? '',
//                        'utm_campaign' => $utm['campaign'] ?? '',
//                        'utm_content' => $utm['content'] ?? '',
//                        'utm_term' => $utm['term'] ?? '',
//                        'customerName' => $user->name,
//                        'customerEmail' => fake()->email,
//                        'customerPhone' => $user->contact,
//                        'date' => $deposit->updated_at->toDate()
//                    ];
//                    $response = Http::withHeaders([
//                        'authorization' => 'FZB6ZFj3VwfyhyKAFxR63j7q0xbG8bp9',
//                        'content-type' => 'application/json',
//                    ])->post('https://developers.nemu.com.br/api/v1/sales', $bodyNemo);
//                }
//            } catch (\Exception $e) {
//                Log::error("Erro ao criar deposito na nemo: ");
//                Log::error($e->getMessage());
//            }

            Log::info("Deposito criado com sucesso! Id: $deposit->id | Valor: $deposit->amount | Status: $deposit->type");
            return $deposit;
        } catch (\Exception $e) {
            Log::error("Erro ao criar deposito  createDepositRecord   -: " . $e->getMessage());
            return null;
        }
    }
}
