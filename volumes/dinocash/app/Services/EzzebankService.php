<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\Deposit;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EzzebankService
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

        $document = $this->checkCPF($user->document, $accessToken, $setting);
        $qrCode = $this->generateQRCode($amount, $uuid, $user, $document, $accessToken, $setting);

        if ($qrCode) {
            return $this->createDepositRecord($user, $amount, $uuid, $qrCode, $hasBonus);
        }

        return null;
    }

    private function getAccessToken($setting): ?string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($setting->ezzebank_ci . ':' . $setting->ezzebank_cs)
        ])->asForm()->post($setting->ezzebank_url . 'oauth/token', [
            'grant_type' => 'client_credentials',
        ]);

        if ($response->successful()) {
            return $response->json('access_token');
        }

        Log::error($response->body() . '  -   Erro no Login Ezzebank');
        return null;
    }

    private function checkCPF($document, $accessToken, $setting): string
    {
        $document = preg_replace("/[^0-9]/", "", $document);
        $response = Http::withToken($accessToken)->get($setting->ezzebank_url . 'services/cpf?docNumber=' . $document);

        if (!$response->successful()) {
            Log::error($response->body() . '  -   Erro no check CPF Ezzebank      -     ' . $document);
            return '09884555605';
        }

        return $document;
    }

    private function generateQRCode($amount, $uuid, $user, $document, $accessToken, $setting): ?string
    {
        $response = Http::withToken($accessToken)->post($setting->ezzebank_url . 'pix/qrcode', [
            'amount' => $amount,
            'payerQuestion' => 'Pagamento referente produto/serviço',
            'external_id' => $uuid,
            'payer' => [
                'name' => $user->name ?? $user->email,
                'document' => $document,
            ],
        ]);

        if ($response->successful()) {
            return $response->json('emvqrcps');
        }

        Log::error($response->body() . '  -   Erro no Gerar QrCode Ezzebank do usuario   -  ' . $user->email);
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
}
