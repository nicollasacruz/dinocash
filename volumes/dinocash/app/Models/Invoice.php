<?php

namespace App\Models;

use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory, Timestamp;

    protected $fillable = [
        'amount',
        'amountPayed',
        'invoicedAt',
        'status',
    ];

    public function ggrTransactions()
    {
        return $this->hasMany(GgrTransaction::class);
    }

    public function ggrPayments()
    {
        return $this->hasMany(GgrPayment::class);
    }
}
