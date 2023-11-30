<?php

namespace App\Models;

use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory, Timestamp;

    protected $fillables = [
        'userId',
        'managerUserId',
        'oldValue',
        'newValue',
        'type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }
}
