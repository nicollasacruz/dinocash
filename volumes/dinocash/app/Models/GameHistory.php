<?php

namespace App\Models;

use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'amountType',
        'type',
        'distance',
        'amountType',
        'subCollectedAt',
    ];

    /**
     * Get the user that owns the gameHistory.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userId', 'id');
    }

    /**
     * Get the affiliateHistories that GameHistory have.
     */
    public function affiliateHistories(): HasMany
    {
        return $this->hasMany(AffiliateHistory::class, 'gameId');
    }

    public function scopeWins($query)
    {
        return $query->where('type', 'win');
    }

    public function scopeLosses($query)
    {
        return $query->where('type', 'loss');
    }

    // Adicionando um escopo local para agrupar os registros por data e obter a soma do finalAmount
    public function scopeDateGroupWin($query)
    {
        return $query->selectRaw('DATE_FORMAT(updated_at, "%d/%m/%Y") as formatted_date, (SUM(finalAmount) * -1) as totalFinalAmount')
            ->where('type', 'win')
            ->with([
                'user' => function ($query) {
                    $query
                        ->where('isAffiliate', false)
                        ->where('role', 'user');
                }
            ])
            ->groupBy('formatted_date')
            ->orderBy('formatted_date', 'asc');
    }

    // Adicionando um escopo local para agrupar os registros por data
    public function scopeDateGroupLoss($query)
    {
        return $query->selectRaw('DATE_FORMAT(updated_at, "%d/%m/%Y") as formatted_date, (SUM(finalAmount) * -1) as totalFinalAmount')
            ->where('type', 'loss')
            ->with([
                'user' => function ($query) {
                    $query
                        ->where('isAffiliate', false)
                        ->where('role', 'user');
                }
            ])
            ->groupBy('formatted_date')
            ->orderBy('formatted_date', 'asc');
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
        return $query->losses()->whereDate('updated_at', now());
    }

    // Escopo para filtrar os registros do tipo 'loss' hoje
    public function scopeLossesToday($query)
    {
        return $query->wins()->whereDate('updated_at', now());
    }

    // Escopo para filtrar todos os registros do tipo 'win'
    public function scopeWinsTotal($query)
    {
        return $query->losses();
    }

    // Escopo para filtrar todos os registros do tipo 'loss'
    public function scopeLossesTotal($query)
    {
        return $query->wins();
    }
}

