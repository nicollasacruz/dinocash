<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Ramsey\Uuid\Type\Decimal;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

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
        'referrals' => 'array',
        'affiliatedAt' => 'datetime',
        'cpaCollected' => 'boolean',
        'cpaCollectedAt' => 'datetime',
    ];

    // Relacionamento com o afiliado (referenciando outro usuário)
    public function affiliate()
    {
        return $this->belongsTo(User::class, 'affiliateId');
    }

    // Relacionamento com os usuários afiliados
    public function referredUsers()
    {
        return $this->hasMany(User::class, 'affiliateId');
    }

    // Adiciona um usuário à lista de afiliados
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
        return $this->wallet / 100;
    }

    public function setWallet($value): void
    {
        $this->wallet = $value * 100;
    }
}
