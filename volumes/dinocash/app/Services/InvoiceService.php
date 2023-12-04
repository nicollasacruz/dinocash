<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\GgrPayment;
use App\Models\GgrTransaction;
use Carbon\Carbon;
use Log;

class InvoiceService
{
    public function getInvoice(): Invoice
    {
        $invoice = Invoice::where('status', 'open')->first();
        if (!$invoice) {
            $invoice = $this->createInvoice();
            Log::info("Invoice criada com sucesso.");

        }
        return $invoice;
    }
    public function createInvoice(): Invoice|bool
    {
        try {
            return Invoice::create([
                'status' => 'pending',
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
                'invoiced_at' => now(),
            ]);
            foreach ($invoice->ggrTransactions as $transaction) {
                $transaction->update([
                    'invoiced_at' => now(),
                ]);
            }
            return $invoice;
        } catch (\Exception $e) {
            Log::error("Erro ao fehcar invoice: " . $e->getMessage());
            return false;
        }
    }
}
