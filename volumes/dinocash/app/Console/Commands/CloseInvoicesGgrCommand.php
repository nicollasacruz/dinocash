<?php

namespace App\Console\Commands;

use App\Services\AffiliateInvoiceService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CloseGGRInvoicesCommand extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:close-affiliate-invoices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(AffiliateInvoiceService $invoiceService) {
        try {
            $invoices = $invoiceService->getAllInvoices();

            if(!$invoices) {
                Log::error('Não há AffiliateInvoice para fechar');
                return;
            }
            foreach($invoices as $invoice) {
                Log::info("Começando a fechar a AffiliateInvoice {$invoice->id} as ".now()." com o total de {$invoice->affiliateHistories->count()} transações e {$invoice->affiliateHistories->sum('amount')}");

                $invoiceService->closeInvoice($invoice);
            }

        } catch (Exception $exception) {
            Log::error('Erro ao fechar as AffiliateInvoice | ERRO: '.$exception->getMessage());
        }
    }
}
