<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\GgrPayment;
use App\Models\GgrTransaction;
use Carbon\Carbon;

class InvoiceService
{
    public function createInvoice(): Invoice|bool
    {
        try {
            return Invoice::create([
                'status' => 'pending',
            ]);
        } catch (\Exception $e) {
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
            return false;
        }
    }
}
