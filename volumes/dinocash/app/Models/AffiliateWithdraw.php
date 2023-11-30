<?php

namespace App\Models;

use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class AffiliateWithdraw extends Model
{
    use HasFactory, Timestamp, Notifiable;

    protected $table = "affiliate_withdraws";
    
    protected $fillable = [
        'userId',
        'transactionId',
        'amount',
        'type',
        'approvedAt',
        'reprovedAt',
        'managerUserId',
    ];

    // Relacionamento com o usuário que solicitou a retirada
    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    // Relacionamento com o gerente ou usuário que aprovou ou reprovou a retirada
    public function manager()
    {
        return $this->belongsTo(User::class, 'managerUserId');
    }
}
