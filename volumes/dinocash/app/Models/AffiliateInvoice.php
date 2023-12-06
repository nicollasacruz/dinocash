<?php

namespace App\Models;

use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AffiliateInvoice extends Model
{
    use HasFactory, Timestamp;

    protected $fillable = [
        'amount',
        'invoicedAt',
        'status',
        'payedAt',
        'affiliateId'
    ];

    public function affiliate(): HasOne
    {
        return $this->hasOne(User::class, 'affiliateId', 'id');
    }

    public function affiliateHistories(): HasMany
    {
        return $this->hasMany(AffiliateHistory::class, 'affiliateInvoiceId', 'id');
    }

    public function getTotal(): float 
    {
        return $this->affiliateHistories->sum('amount');
    }
}
