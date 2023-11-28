<?php

namespace App\Models;

use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateHistory extends Model
{
    use HasFactory, Timestamp;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'amount',
        'gameId',
        'affiliateId',
        'userId',
        'type',
        'invoicedAt',
        'payedAt',
    ];

    /**
     * Accessor for the 'amount' attribute.
     *
     * @param  mixed  $value
     * @return float
     */
    public function getAmountAttribute($value): float
    {
        return $value / 100;
    }

    /**
     * Mutator for the 'amount' attribute.
     *
     * @param  mixed  $value
     * @return void
     */
    public function setAmountAttribute($value): void
    {
        $this->attributes['amount'] = $value * 100;
    }

    /**
     * Define the "user" relationship.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userId', 'id');
    }

    /**
     * Define the "affiliate" relationship.
     *
     * @return BelongsTo
     */
    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(User::class, 'affiliateId', 'id');
    }
}

