<?php

namespace App\Services;

use App\Models\AffiliateHistory;
use App\Models\AffiliateInvoice;
use App\Models\Invoice;
use App\Models\GgrPayment;
use App\Models\GgrTransaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Log;

class AffiliateInvoiceService
{
    public function getAllInvoices(): Collection|bool
    {
        try {
            $invoices = AffiliateInvoice::where('status', 'open')->get();
    
            return $invoices;

        } catch (\Exception $e) {
            Log::error("Erro ao pegar todas as AffiliateInvoices: " . $e->getMessage());
            return false;
        }
    }
    public function getInvoice(User $affiliate): AffiliateInvoice|bool
    {
        try {
            $invoice = AffiliateInvoice::where('status', 'open')->where('affiliateId', $affiliate->id)->first();
            if (!$invoice) {
                $invoice = $this->createInvoice($affiliate);
                Log::info("AffiliateInvoice criada com sucesso.");
    
            }
    
            return $invoice;

        } catch (\Exception $e) {
            Log::error("Erro ao pegar a AffiliateInvoice: " . $e->getMessage());
            return false;
        }
    }
    public function createInvoice($affiliate): AffiliateInvoice|bool
    {
        try {
            return AffiliateInvoice::create([
                'status' => 'open',
                'affiliateId' => $affiliate->id,
            ]);
        } catch (\Exception $e) {
            Log::error("Erro ao criar AffiliateInvoice: " . $e->getMessage());
            return false;
        }
    }

    public function closeInvoice(AffiliateInvoice $invoice): AffiliateInvoice|bool
    {
        try {
            $amount = $invoice->affiliateHistories->sum('amount');
            $invoice->update([
                'status' => 'closed',
                'invoicedAt' => now(),
                'amount' => $amount,
            ]);
            $affiliate = User::where('id', $invoice->affiliateId)->first();
            $affiliate->update([
                'walletAffiliate' => number_format(($affiliate->walletAffiliate + $amount), 2,'.','')
            ]);

            foreach ($invoice->affiliateHistories as $transaction) {
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

    public function getTotal(AffiliateInvoice $invoice): float 
    {
        return $invoice->getTotal();
    }
}
