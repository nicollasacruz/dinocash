<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CashTimeService
{

    public function __construct()
    {
    }

    public function createDeposit(array $data): ?Deposit
    {
        $setting = Setting::first();

        $authValue = base64_encode(env('SECRETKEY_CASHTIME') . ':x');
        $endpoint = env('ENDPOINT_CASHTIME') . '/v1/transactions';

        $user = $data['user'];
        $amount = $data['amount'];
        $uuid = $data['uuid'];
        $hasBonus = $data['hasBonus'];

        $cpf = preg_replace('/\D/', '', $user->document);

        $body = [
            'customer' => [
                'document' => [
                    'number' => $cpf ?? '09884555605',
                    'type' => 'cpf',
                ],
                'name' => $user->name,
                'email' => 'snakebet001' . $user->id . '@gmail.com',
            ],
            'amount' => $amount * 100,
            'paymentMethod' => 'pix',
            'items' => [
                [
                    'tangible' => false,
                    'title' => 'SnakeDeposito',
                    'unitPrice' => $amount * 100,
                    'quantity' => 1,
                ],
            ],
            'postbackUrl' => env('APP_URL') . '/callback',
        ];

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . $authValue,
        ])->post($endpoint, $body);

        $data = $response->json();



        if($data['status'] == 400 && $data['message']){
            $body['customer']['document'] = [
                "number" => '09884555605',
                "type" => 'cpf',
            ];
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . $authValue,
            ])->post($endpoint, $body);
            $data = $response->json();
        }
        return $this->handleDepositResponse($user, $amount, $uuid, $data, $hasBonus);
    }

    private function handleDepositResponse($user, $amount, $uuid, $data, $hasBonus): ?Deposit
    {
        try {
            if ($data['pix']['qrcode']) {
                Log::alert("Entrou no status 201 do handleDepositResponse");
                return $this->createDepositRecord($user, $amount, $uuid, $data['pix']['qrcode'], $hasBonus);
            }
        } catch (\Exception $e) {
            Log::error("Erro ao criar deposito handleDepositResponse: " . $e->getMessage());
        }
        return null;
    }

    private function createDepositRecord($user, $amount, $uuid, $qrCode, $hasBonus): ?Deposit
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
            ]);

            Log::info("Deposito criado com sucesso! Id: $deposit->id | Valor: $deposit->amount | Status: $deposit->type");
            return $deposit;
        } catch (\Exception $e) {
            Log::error("Erro ao criar deposito  createDepositRecord   -: " . $e->getMessage());
            return null;
        }
    }
}
