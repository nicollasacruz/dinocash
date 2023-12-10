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
        'affiliateInvoiceId',
        'userId',
        'type',
        'invoicedAt',
        'payedAt',
    ];

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

    public function afilliateInvoice(): BelongsTo
    {
        return $this->belongsTo(AffiliateInvoice::class, 'affiliateInvoiceId', 'id');
    }

    public function scopeCPA($query)
    {
        return $query->where('type', 'CPA');
    }

    // Adicionando um escopo local para filtrar os registros do tipo 'win'
    public function scopeWins($query)
    {
        return $query->where('type', 'win');
    }

    // Adicionando um escopo local para filtrar os registros do tipo 'loss'
    public function scopeLosses($query)
    {
        return $query->where('type', 'loss');
    }

    // Escopo para filtrar os registros do tipo 'win' nos últimos 30 dias
    public function scopeWinsLast30Days($query)
    {
        return $query->losses()->whereBetween('updated_at', [now()->subDays(30), now()]);
    }

    // Escopo para filtrar os registros do tipo 'loss' nos últimos 30 dias
    public function scopeLossesLast30Days($query)
    {
        return $query->wins()->whereBetween('updated_at', [now()->subDays(30), now()]);
    }

    // Escopo para filtrar os registros do tipo 'win' hoje
    public function scopeWinsToday($query)
    {
        return $query->wins()->whereDate('updated_at', now());
    }
    
    // Escopo para filtrar os registros do tipo 'loss' hoje
    public function scopeLossesToday($query)
    {
        return $query->losses()->whereDate('updated_at', now());
    }

    // Escopo para filtrar todos os registros do tipo 'win'
    public function scopeWinsTotal($query)
    {
        return $query->wins();
    }

    // Escopo para filtrar todos os registros do tipo 'loss'
    public function scopeLossesTotal($query)
    {
        return $query->losses();
    }

    // Escopo para filtrar todos os registros do tipo 'loss'
    public function scopeTotalCPA($query)
    {
        return $query->CPA();
    }
}

