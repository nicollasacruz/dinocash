<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\Deposit;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BsPayService
{
    public function __construct()
    {
    }

    public function createDeposit(array $data): ?Deposit
    {
        $user = $data['user'];
        $amount = $data['amount'];
        $uuid = $data['uuid'];
        $hasBonus = $data['hasBonus'];
        $setting = Setting::first();

        $accessToken = $this->getAccessToken($setting);

        if (!$accessToken) {
            Log::error('Erro ao pegar o token de acesso');
            return null;
        }

        $document = preg_replace("/[^0-9]/", "", $user->document);

        $body = [
            'amount' => $amount,
            'payerQuestion' => 'Deposito CobraJogo',
            'external_id' => $uuid,
            'postbackUrl' => env('APP_URL') . $setting->bspay_url_webhook,
            'payer' => [
                'name' => $user->name,
                'document' => $document,
                'email' => $user->name . 'snake@gmail.com',
            ],
        ];


        $response = Http::withToken($accessToken)->post($setting->bspay_url . '/pix/qrcode', $body);

        $data = $response->json();
        if (isset($data['statusCode']) && $data['statusCode'] === 400 && $data['message'] == 'Erro: Documento inválido.') {
            $body['payer']['name'] = 'Ana Julia Alvez';
            $body['payer']['document'] = '52501982878';
            $response = Http::withHeaders(['Authorization' => 'Bearer '. $accessToken])->post(env('ENDPOINT_BSPAY').'/pix/qrcode',$body);
            $data = $response->json();
            if ($response->successful()) {
                return $this->createDepositRecord($user, $amount, $uuid, $data['qrCode'], $hasBonus);
            }
            return null;
        }

        if ($response->successful()) {
            return $this->createDepositRecord($user, $amount, $uuid, $data['qrCode'], $hasBonus);
        }

        return null;
    }

    private function createDepositRecord($user, $amount, $uuid, $qrCode, $hasBonus): Deposit
    {
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
    }

    private function getAccessToken(Setting $setting)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($setting->bspay_ci . ':' . $setting->bspay_cs)
        ])->post($setting->bspay_url . '/oauth/token');

        $data = $response->json();

        if(isset($data['access_token'])){
            return $data['access_token'];
        }
        return false;
    }
}
