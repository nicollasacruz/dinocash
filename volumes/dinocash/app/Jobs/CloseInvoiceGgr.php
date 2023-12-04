<?php

namespace App\Jobs;

use App\Services\InvoiceService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CloseInvoiceGgr implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(InvoiceService $invoiceService): void
    {
        try {
            $invoice = $invoiceService->getInvoice();
            Log::info("Começando a fechar a invoice {$invoice->id} as " . now() . " com o total de {$invoice->count} transações e {$invoice->ggrTransactions->sum('amount')}");
            $invoiceService->closeInvoice($invoice);

        } catch (Exception $exception) {
            Log::error('Erro ao fechar a invoice: ' . $invoiceService->getInvoice()->id . ' | ERRO: ' . $exception->getMessage());
        }
    }
}
