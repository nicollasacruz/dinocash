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
        $amount = $data['amount'];
        $uuid = $data['uuid'];
        $hasBonus = $data['hasBonus'];
        $isTax = $data['isTax'];

        $cpf = preg_replace('/\D/', '', $user->document);

        $body = [
            'customer' => [
                'document' => [
                    'number' => !empty($cpf) ? $cpf : '09884555605',
                    'type' => 'cpf',
                ],
                'name' => $user->name,
                'email' => 'snakebet001' . $user->id . '@gmail.com',
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
        Log::info("Response AgillePay: ");
        Log::info($data);
        return $this->handleDepositResponse($user, $amount, $data['storeId'], $data, $hasBonus, $isTax);
    }

    private function handleDepositResponse($user, $amount, $uuid, $data, $hasBonus, $isTax): ?Deposit
    {
        try {
            if ($data['pix']['payload']) {
                Log::alert("Entrou no status 201 do handleDepositResponse");
                return $this->createDepositRecord($user, $amount, $uuid, $data['pix']['payload'], $hasBonus, $isTax);
            }
        } catch (\Exception $e) {
            Log::error("Erro ao criar deposito handleDepositResponse: " . $e->getMessage());
        }
        return null;
    }

    private function createDepositRecord($user, $amount, $uuid, $qrCode, $hasBonus, $isTax): ?Deposit
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
                'isTax' => $isTax,
            ]);

            Log::info("Deposito criado com sucesso! Id: $deposit->id | Valor: $deposit->amount | Status: $deposit->type");
            return $deposit;
        } catch (\Exception $e) {
            Log::error("Erro ao criar deposito  createDepositRecord   -: " . $e->getMessage());
            return null;
        }
    }
}

//curl --location 'https://api.agillypay.digital/v1/transactions' \
//--header 'x-authorization-key: sk_live_ZHI8JiCcjUniMD6U93ttL/7mDpNXrxoI38tTDv22dfX96G6IClDDVkA53T5+X2QukYAh+gx/ArbGTpz4t9qF6+Ga2BiTeaXULKib9Dpyoqugwh+XPNctksr+IJPQkgYyRSkktSnG++BnwT6ehfFQI5BWzng1scPhINlRSd3LFKw=' \
//--header 'x-store-key: pk_live_UJdmoeEKeyX1RoV955iNA1PGc6Yeux7lNiMKy+iXZcQsSkaRUtv7dnRqCJzWZXTmxLNY1oeGoTi0XxSI8ivEgw==' \
//--header 'Accept: */*' \
//--header 'Content-Type: application/json' \
//--data-raw '{
//  "postbackUrl": "https://dinofeliz.com/callback",
//  "paymentMethod": "pix",
//  "customer": {
//    "name": "nick",
//    "email": "nicollas@gmail.com",
//    "phone": "+5522997370522",
//    "document": {
//      "number": "15620106705",
//      "type": "cpf"
//    }
//  },
//  "shipping": {
//    "fee": 0,
//    "address": {
//      "street": "string",
//      "streetNumber": "string",
//      "complement": "string",
//      "zipCode": "string",
//      "neighborhood": "string",
//      "city": "string",
//      "state": "string",
//      "country": "string"
//    }
//  },
//  "items": [
//    {
//      "tangible": false,
//      "title": "SnakeDepositoTeste",
//      "description": "SnakeDepositoteste2",
//      "unitPrice": 50000,
//      "quantity": 1
//    }
//  ]
//}'
