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
        'value',
        'finalValue',
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
     * Accessor for the 'value' attribute.
     *
     * @param  mixed  $value
     * @return float
     */
    public function getValueAttribute($value): float
    {
        return $value / 100;
    }

    /**
     * Mutator for the 'value' attribute.
     *
     * @param  mixed  $value
     * @return void
     */
    public function setValueAttribute($value): void
    {
        $this->attributes['value'] = $value * 100;
    }

    /**
     * Accessor for the 'finalValue' attribute.
     *
     * @param  mixed  $finalValue
     * @return float
     */
    public function getFinalValueAttribute($finalValue): float
    {
        return $finalValue / 100;
    }

    /**
     * Mutator for the 'finalValue' attribute.
     *
     * @param  mixed  $finalValue
     * @return void
     */
    public function setFinalValueAttribute($finalValue): void
    {
        $this->attributes['finalValue'] = $finalValue * 100;
    }
}

