<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Services\AffiliateInvoiceService;
use App\Services\InvoiceService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class payGgrPendingCommand extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:pay-ggr-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pay Ggr Invoice Command';

    /**
     * Execute the console command.
     */
    public function handle(InvoiceService $invoiceService) {
        try {
            $unpaidInvoices = Invoice::unpaid()->get();

            if(!$unpaidInvoices) {
                Log::error('Não há Invoices não pagas');
                return;
            }
            foreach($unpaidInvoices as $invoice) {
                $amount = $invoice->amountPayed - $invoice->amount;
                Log::info("Tentando pagar o valor de:  R$" . $amount . '   da fatura: ' . $invoice->id);

                $result = $invoiceService->payInvoice($invoice, $amount);

                if($result['success']) {
                    $invoice->update(['amountPayed' => $result['amount']]);
                    Log::info("Pago o valor de:  R$" . $result['amount'] . '   da fatura: ' . $invoice->id);
                }
            }

        } catch (Exception $exception) {
            Log::error('Erro ao fechar as AffiliateInvoice | ERRO: '.$exception->getMessage());
        }
    }
}
