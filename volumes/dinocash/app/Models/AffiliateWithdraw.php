<?php

namespace App\Models;

use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Collection;
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
        'pixKey',
        'pixValue',
        'approvedAt',
        'reprovedAt',
        'managerUserId',
    ];

    // Relacionamento com o usuário que solicitou a retirada
    public function user()
    {
        return $this->belongsTo(User::class, 'userId', 'id');
    }

    // Relacionamento com o gerente ou usuário que aprovou ou reprovou a retirada
    public function manager()
    {
        return $this->belongsTo(User::class, 'managerUserId');
    }
    
    public static function getAffiliateWithdrawLikeEmail($email): Collection
    {
        return static::with('user')
            ->whereHas('user', function ($query) use ($email) {
                $query->where('isAffiliate', true)->where('email', 'LIKE', '%' . $email . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);
    }
}
