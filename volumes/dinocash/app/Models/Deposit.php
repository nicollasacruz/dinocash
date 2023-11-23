<?php

namespace App\Models;

use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    use HasFactory, Timestamp;

    protected $fillable = [
        'userId',
        'transactionId',
        'amount',
        'type',
        'approvedAt',
    ];

    // Relacionamento com o usuário
    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    // Método para aprovar um depósito
    public function approve()
    {
        $this->update([
            'type' => 'paid',
            'approvedAt' => now(),
        ]);
    }
}
