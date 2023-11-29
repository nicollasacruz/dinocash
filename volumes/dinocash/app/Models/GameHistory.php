<?php

namespace App\Models;

use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameHistory extends Model
{
    use HasFactory, Timestamp;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'userId',
        'amount',
        'finalAmount',
        'type',
        'distance',
    ];

    /**
     * Get the user that owns the gameHistory.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userId', 'id');
    }

    /**
     * Accessor for the 'amount' attribute.
     *
     * @param  mixed  $amount
     * @return float
     */
    public function getAmountAttribute($amount): float
    {
        return $amount / 100;
    }

    /**
     * Mutator for the 'amount' attribute.
     *
     * @param  mixed  $amount
     * @return void
     */
    public function setAmountAttribute($amount): void
    {
        $this->attributes['amount'] = $amount * 100;
    }

    /**
     * Accessor for the 'finalAmount' attribute.
     *
     * @param  mixed  $finalAmount
     * @return float
     */
    public function getFinalAmountAttribute($finalAmount): float
    {
        return $finalAmount / 100;
    }

    /**
     * Mutator for the 'finalAmount' attribute.
     *
     * @param  mixed  $finalAmount
     * @return void
     */
    public function setFinalAmountAttribute($finalAmount): void
    {
        $this->attributes['finalAmount'] = $finalAmount * 100;
    }
}

