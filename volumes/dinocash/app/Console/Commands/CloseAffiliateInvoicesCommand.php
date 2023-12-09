<?php

namespace App\Console\Commands;

use App\Services\AffiliateInvoiceService;
use App\Services\InvoiceService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CloseAffiliateInvoicesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:close-invoices-ggr';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'CloseAffiliateInvoicesCommand';

    /**
     * Execute the console command.
     */
    public function handle(InvoiceService $invoiceService)
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
