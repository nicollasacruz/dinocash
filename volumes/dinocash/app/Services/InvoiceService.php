<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\GgrPayment;
use App\Models\GgrTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Log;

class InvoiceService
{
    public function getAllInvoices()
    {
        return Invoice::where('status', 'closed')->get();
    }

    public function getInvoice(): Invoice
    {
        $invoice = Invoice::where('status', 'open')->first();
        if (!$invoice) {
            $invoice = $this->createInvoice();
        }
        return $invoice;
    }
    public function createInvoice(): Invoice|bool
    {
        try {
            return Invoice::create([
                'status' => 'open',
            ]);
        } catch (\Exception $e) {
            Log::error("Erro ao criar Invoice: " . $e->getMessage());
            return false;
        }
    }

    public function addPayment(Invoice $invoice, $amount): GgrPayment|bool
    {
        try {
            $payment = new GgrPayment([
                'amount' => $amount,
                'status' => 'pending',
                'invoice_id' => $invoice->id,
            ]);

            $invoice->ggrPayments()->save($payment);

            return $payment;
        } catch (\Exception $e) {
            Log::error("Erro ao adicionar pagamento à Invoice: " . $e->getMessage());
            return false;
        }
    }

    public function addTransaction(Invoice $invoice, $amount): GgrTransaction|bool
    {
        try {
            $transaction = new GgrTransaction([
                'invoice_id' => $invoice->id,
                'amount' => $amount,
            ]);

            $invoice->ggrTransactions()->save($transaction);

            return $transaction;
        } catch (\Exception $e) {
            Log::error("Erro ao adicionar transação à Invoice: " . $e->getMessage());
            return false;
        }
    }

    public function closeInvoice(Invoice $invoice): Invoice|bool
    {
        try {
            $invoice->update([
                'status' => 'closed',
                'invoicedAt' => now(),
                'amount' => $invoice->ggrTransactions->sum('amount'),
            ]);
            foreach ($invoice->ggrTransactions as $transaction) {
                $transaction->update([
                    'invoicedAt' => now(),
                ]);
            }
            return $invoice;
        } catch (\Exception $e) {
            Log::error("Erro ao fehcar invoice: " . $e->getMessage());
            return false;
        }
    }

    public function payInvoice(Invoice $invoice, $amount)
    {
        
        $document = env("GGR_KEY_PIX");
        $typeKey = env("GGR_TYPE_KEY_PIX");
        $response = Http::withHeaders([
            'ci' => env('SUITPAY_CI'),
            'cs' => env('SUITPAY_CS'),
        ])->post(env('SUITPAY_URL') . 'gateway/pix-payment', [
                    'value' => $amount,
                    'key' => $document,
                    'typeKey' => $typeKey,
                    'callbackUrl' => env('APP_URL_API') . env('SUITPAY_URL_WEBHOOK_SEND'),
                ]);

        $data = $response->json();

        Log::info('AUTOPAY RESPONSE' . json_encode($data));

        if ($data['response'] === 'OK') {
            GgrPayment::create([
                'amount' => $amount,
                'invoice_id' => $invoice->id,
                'status' => 'paid',
                'payedAt' => now(),
            ]);
            return [
                'success' => true,
                'amount' => $amount,
            ];

        } elseif ($data['response'] === 'PIX_KEY_NOT_FOUND') {
            Log::error('RESULTADO AUTOPAY WITHDRAW - Chave pix não encontrada');

            return [
                'success' => false,
                'message' => 'Chave Pix não encontrada!'
            ];

        } elseif ($data['response'] === 'NO_FUNDS') {
            $response = Http::withHeaders([
                'ci' => env('SUITPAY_CI'),
                'cs' => env('SUITPAY_CS'),
            ])->post(env('SUITPAY_URL') . 'gateway/pix-payment', [
                        'value' => $amount / 2,
                        'key' => $document,
                        'typeKey' => 'randomKey',
                        'callbackUrl' => env('APP_URL_API') . env('SUITPAY_URL_WEBHOOK_SEND'),
                    ]);

            $data = $response->json();

            Log::info('AUTOPAY RESPONSE' . json_encode($data));

            if ($data['response'] === 'OK') {
                GgrPayment::create([
                    'amount' => $amount,
                    'invoice_id' => $invoice->id,
                    'status' => 'paid',
                    'payedAt' => now(),
                ]);
                return [
                    'success' => true,
                    'amount' => $amount / 2,
                ];

            } else {
                Log::error('RESULTADO AUTOPAY WITHDRAW - Sem Saldo na conta');
            }

            return [
                'success' => false,
                'message' => 'Sem Saldo na conta'
            ];
        }
    }
}
