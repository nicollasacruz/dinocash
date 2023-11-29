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


    /**
     * Accessor for the 'oldValue' attribute.
     *
     * @param  mixed  $value
     * @return float
     */
    public function getOldValueAttribute($value): float
    {
        return $value / 100;
    }

    /**
     * Mutator for the 'oldValue' attribute.
     *
     * @param  mixed  $value
     * @return void
     */
    public function setOldValueAttribute($value): void
    {
        $this->attributes['oldValue'] = $value * 100;
    }

    /**
     * Accessor for the 'newValue' attribute.
     *
     * @param  mixed  $value
     * @return float
     */
    public function getNewValueAttribute($value): float
    {
        return $value / 100;
    }

    /**
     * Mutator for the 'newValue' attribute.
     *
     * @param  mixed  $value
     * @return void
     */
    public function setNewValueAttribute($value): void
    {
        $this->attributes['newValue'] = $value * 100;
    }
}
