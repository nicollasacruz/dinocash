<?php

namespace App\Models;

use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BonusWalletChange extends Model
{
    use HasFactory, Timestamp;

    protected $fillable = [
        'bonusCampaignId',
        'amountOld',
        'amountNew',
    ];

    public function bonusCampaign(): BelongsTo
    {
        return $this->belongsTo(BonusCampaign::class);
    }
}
