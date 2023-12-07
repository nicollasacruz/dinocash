<?php

namespace App\Jobs;

use App\Models\AffiliateInvoice;
use App\Services\AffiliateInvoiceService;
use App\Services\InvoiceService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CloseAffiliateInvoice implements ShouldQueue
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
    public function handle(AffiliateInvoiceService $invoiceService): void
    {
        try {
            $invoices = $invoiceService->getAllInvoices();

            if (!$invoices) {
                Log::error('Não há AffiliateInvoice para fechar');
                return;
            }
            foreach ($invoices as $invoice) {
            Log::info("Começando a fechar a AffiliateInvoice {$invoice->id} as " . now() . " com o total de {$invoice->count} transações e {$invoice->gameHistories->sum('amount')}");
            
            $invoiceService->closeInvoice($invoice);
        }

        } catch (Exception $exception) {
            Log::error('Erro ao fechar as AffiliateInvoice | ERRO: ' . $exception->getMessage());
        }
    }
}
