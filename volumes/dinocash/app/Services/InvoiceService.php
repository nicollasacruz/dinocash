<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\GgrPayment;
use App\Models\GgrTransaction;
use Carbon\Carbon;

class InvoiceService
{
    public function createInvoice()
    {
        return Invoice::create([
            'status' => 'Pending',
        ]);
    }

    public function addPayment(Invoice $invoice)
    {
        $payment = new GgrPayment([
            'amount' => 0, // Defina o valor apropriado
            'status' => 'Pending', // Ou qualquer outro status desejado
        ]);

        $invoice->ggrPayments()->save($payment);

        return $payment;
    }

    public function addTransaction(Invoice $invoice)
    {
        $transaction = new GgrTransaction([
            'amount' => 0, // Defina o valor apropriado
            'invoiced_at' => now(),
        ]);

        $invoice->ggrTransactions()->save($transaction);

        return $transaction;
    }

    public function closeInvoice(Invoice $invoice)
    {
        $invoice->update([
            'status' => 'Closed',
            'invoiced_at' => now(),
        ]);
    }
}
