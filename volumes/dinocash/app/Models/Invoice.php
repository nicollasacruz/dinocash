<?php

namespace App\Models;

use App\Services\InvoiceService;
use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory, Timestamp;

    protected $fillable = [
        'amount',
        'amountPayed',
        'invoicedAt',
        'status',
        'payedAt',
    ];

    public function ggrTransactions(): HasMany
    {
        return $this->hasMany(GgrTransaction::class);
    }

    public function ggrPayments(): HasMany
    {
        return $this->hasMany(GgrPayment::class);
    }

    public function getInvoice(): Invoice
    {
        $invoiceService = new InvoiceService();
        return $invoiceService->getInvoice();
    }
}
