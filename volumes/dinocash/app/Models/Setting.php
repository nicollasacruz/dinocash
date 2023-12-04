<?php

namespace App\Models;

use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory, Timestamp;

    protected $fillable = [
        'emailFatura',
        'payout',
        'minWithdraw',
        'maxWithdraw',
        'minDeposit',
        'maxDeposit',
        'rollover',
        'defaultCPA',
        'defaultRevShare',
        'autoPayWithdraw',
        'maxAutoPayWithdraw',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'autoPayWithdraw' => 'boolean',
    ];
}
