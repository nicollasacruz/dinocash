<?php

namespace App\Services;

use App\Jobs\ProcessAutoWithdraw;
use App\Models\AffiliateHistory;
use App\Models\AffiliateWithdraw;
use App\Models\WalletTransaction;
use App\Models\Withdraw;
use Exception;
use Illuminate\Support\Facades\Http;
use Log;
use Ramsey\Uuid\Uuid;
use App\Models\User;

class WithdrawAffiliateService
{
    public function createWithdraw(User $affiliate, $amount)
    {
        try {
            $withdraw = AffiliateWithdraw::create([
                'userId' => $affiliate->id,
                'transactionId' => Uuid::uuid4()->toString(),
                'amount' => $amount,
                'type' => 'pending',
            ]);

            // AffiliateHistory::create([
            //     'affiliateInvoiceId' => 0,
            //     'affiliateId' => $affiliate->id,
            //     'userId' => $affiliate->id,
            //     'amount' => $amount,
            //     'type' => 'WITHDRAW',
            // ]);

            $affiliate->update([
                'walletAffiliate' => $affiliate->walletAffiliate - $amount
            ]);
            $affiliate->save();

            return $withdraw;
        } catch (Exception $e) {
            Log::error("Erro ao criar Withdraw: " . $e->getMessage() . ' | ' . $e->getFile() . ' | ' . $e->getLine());
            return false;
        }
    }

    public function autoWithdraw(AffiliateWithdraw $withdraw)
    {
        $document = preg_replace('/[^0-9]/', '', $withdraw->user->document);
        $response = Http::withHeaders([
            'ci' => env('SUITPAY_CI'),
            'cs' => env('SUITPAY_CS'),
        ])->post(env('SUITPAY_URL') . 'gateway/pix-payment', [
                    'value' => $withdraw->amount,
                    'key' => $document,
                    'typeKey' => 'document',
                    'callbackUrl' => env('APP_URL_API') . env('SUITPAY_URL_WEBHOOK_SEND'),
                ]);

        $data = $response->json();

        Log::info('AUTOPAY RESPONSE' . json_encode($data));

        if ($data['response'] === 'OK') {
            return [
                'success' => true,
                'message' => 'Saque pago com sucesso.'
            ];
        } elseif ($data['response'] === 'PIX_KEY_NOT_FOUND') {
            Log::error('RESULTADO AUTOPAY WITHDRAW - Chave pix não encontrada');
            return [
                'success' => false,
                'message' => 'Chave Pix não encontrada!'
            ];
        } elseif ($data['response'] === 'NO_FUNDS') {
            Log::error('RESULTADO AUTOPAY WITHDRAW - Sem Saldo na conta');
            return [
                'success' => false,
                'message' => 'Sem Saldo na conta'
            ];
        }

        return [
            'success' => false,
            'message' => 'Erro desconhecido'
        ];
    }

    public function aprove(AffiliateWithdraw $withdraw): array
    {
        try {
            $saque = $this->autoWithdraw($withdraw);
            if ($saque['success']) {
                $withdraw->update([
                    'type' => 'paid',
                    'approvedAt' => now(),
                ]);
            }
            return [
                'success' => $saque['success'],
                'message' => $saque['message'],
            ];

        } catch (Exception $e) {
            Log::error('Erro ao aprovar o saque do afiliado: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro interno',
            ];
        }
    }

    public function reject(AffiliateWithdraw $withdraw): array
    {
        try {

            $withdraw->update([
                'type' => 'rejected',
                'reprovedAt' => now(),
            ]);

            $withdraw->save();

            $user = $withdraw->user;
            $amount = $withdraw->amount;

            // AffiliateHistory::create([
            //     'userId' => $withdraw->user->id,
            //     'affiliateId' => $withdraw->user->id,
            //     'affiliateInvoiceId' => ($affiliateInvoiceService->getInvoice($withdraw->user))->id,
            //     'amount' => $amount,
            //     'type' => 'WITHDRAW REJECTED',
            // ]);
            $user->changeWallet($amount);
            $user->save();

            return [
                'success' => 'sucesso',
                'message' => 'Saque rejeitado com sucesso.',
            ];
        } catch (Exception $e) {
            Log::error('Erro ao rejeitar o saque: ' . $e->getMessage());
            return [
                'success' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }
}
