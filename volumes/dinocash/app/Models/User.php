<?php

namespace App\Models;

use Carbon\Traits\Timestamp;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Ramsey\Uuid\Type\Decimal;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Timestamp;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'contact',
        'document',
        'role',
        'wallet',
        'isAffiliate',
        'affiliateId',
        'affiliatedAt',
        'cpaCollected',
        'cpaCollectedAt',
        'invitation_link',
        'referralsCounter',
        'CPA',
        'revShare',
        'walletAffiliate',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'isAffiliate' => 'boolean',
        'wallet' => 'float',
        'walletAffiliate' => 'float',
        'referrals' => 'array',
        'affiliatedAt' => 'datetime',
        'cpaCollected' => 'boolean',
        'cpaCollectedAt' => 'datetime',
    ];

    public function setInvitationLink($value)
    {
        $this->attributes['invitation_link'] = $value;
        $this->attributes['isAffiliate'] = true;
    }

    public function changeWallet($value)
    {
        $this->wallet = number_format($this->wallet + $value, 2, '.', ',');
    }

    public function changeWalletAffiliate($value)
    {
        $this->walletAffiliate = number_format($this->wallet + $value, 2, '.', ',');
    }

    /**
     * Get the walletTransactions that of the user.
     */ 
    public function walletTransactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class, 'userId');
    }

    /**
     * Get the deposits that of the user.
     */ 
    public function deposits(): HasMany
    {
        return $this->hasMany(Deposit::class, 'userId');
    }

    /**
     * Get the withdraws that of the user.
     */ 
    public function withdraws(): HasMany
    {
        return $this->hasMany(Withdraw::class, 'userId');
    }

    /**
     * Get the managedWithdraws that of the user.
     */ 
        public function managedWithdraws(): HasMany
    {
        return $this->hasMany(Withdraw::class, 'managerUserId');
    }

    /**
     * Get the games that of the user.
     */ 
    public function gamesHistory(): HasMany
    {
        return $this->hasMany(GameHistory::class, 'userId');
    }

    // Relacionamento com os usuários afiliados
    public function referredUsers(): HasMany
    {
        return $this->hasMany(User::class, 'affiliateId');
    }
    
    /**
     * Get the affiliate that owns the user.
     */
    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(User::class, 'affiliateId', 'id');
    }

    // Relacionamento com o histórico de afiliação
    public function affiliateHistories()
    {
        return $this->hasMany(AffiliateHistory::class, 'affiliateId');
    }

    public function createDeposit($amount, $uuid, $paymentCode): Deposit
    {
        $deposit = Deposit::create([
            'userId' => $this->id,
            'amount' => $amount,
            'transactionId' => $uuid,
            'paymentCode' => $paymentCode,
        ]);
        
        return $deposit;
    }

    public function addReferral(User $affiliate)
    {
        $this->affiliateId = $affiliate->id;
        $this->save();
    }

    public function setAffiliate(User $affiliate)
    {
        $this->affiliateId = $affiliate->id;
        $this->save();
    }

    public function hasReferrals()
    {
        return count($this->referrals) > 0;
    }

    public function getWallet(): float
    {
        return $this->wallet;
    }

    public function setWallet($value): void
    {
        $this->wallet = $value * 100;
    }

    /**
     * Approve a withdrawal for the given withdrawal ID.
     *
     * @param int $withdrawalId
     * @return bool
     */
    public function approveWithdraw(int $withdrawalId): bool
    {
        $withdraw = Withdraw::findOrFail($withdrawalId);

        // Verifica se o usuário tem a função 'admin' antes de aprovar
        if ($this->role === 'admin') {
            $withdraw->update([
                'type' => 'paid',
                'approvedAt' => now(),
                'managerUserId' => $this->id,
            ]);

            return true;
        }

        return false;
    }

    /**
     * Reprove a withdrawal for the given withdrawal ID.
     *
     * @param int $withdrawalId
     * @return bool
     */
    public function reproveWithdraw(int $withdrawalId): bool
    {
        $withdraw = Withdraw::findOrFail($withdrawalId);

        // Verifica se o usuário tem a função 'admin' antes de reprovar
        if ($this->role === 'admin') {
            $withdraw->update([
                'type' => 'reproved',
                'reprovedAt' => now(),
                'managerUserId' => $this->id,
            ]);

            return true;
        }

        return false;
    }

    /**
     * Reprove a withdrawal for the given withdrawal ID.
     *
     * @param int $withdrawalId
     * @return bool
     */
    public function promoveToAdmin(int $userId): bool
    {
        $user = User::findOrFail($userId);

        // Verifica se o usuário tem a função 'admin' antes de promover
        if ($this->role === 'admin') {
            $user->update([
                'role' => 'admin',
            ]);

            return true;
        }

        return false;
    }

    public function getInvitationLink(): string
    {
        return \env('APP_URL') . '/ref/' . $this->invitation_link;
    }
}
