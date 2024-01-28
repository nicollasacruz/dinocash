<?php

namespace App\Models;

use App\Models\BonusWalletChange;
use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BonusCampaign extends Model
{
    use Timestamp;

    protected $fillable =
    [
        'amount',
        'amountMovement',
        'bonusPercent',
        'rollover',
        'userId',
        'type',
        'status',
        'expireAt'
    ];

    public function bonusWalletChanges(): HasMany
    {
        return $this->hasMany(BonusWalletChange::class);
    }
}
