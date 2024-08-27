<?php

namespace App\Models;

use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static first()
 */
class Setting extends Model
{
    use HasFactory, Timestamp;

    protected $fillable = [
        'emailFatura',
        'payout',
        'minWithdraw',
        'maxWithdraw',
        'minAmountPlay',
        'maxAmountPlay',
        'minDeposit',
        'maxDeposit',
        'rollover',
        'rolloverBonus',
        'amountFreeSpin',
        'defaultCPA',
        'defaultRevShare',
        'affiliatePayGGR',
        'autoPayWithdraw',
        'maxAutoPayWithdraw',
        'bonusPercent',
        'maxDepositBonusToUser',
        'maxDepositBonusValue',
        'game_mode',
        'suitpay_url',
        'suitpay_ci',
        'suitpay_cs',
        'suitpay_url_webhook',
        'ezzebank_url',
        'ezzebank_ci',
        'ezzebank_cs',
        'ezzebank_url_webhook',
        'ezzebank_signature_key',
        'ezzebank_auth',
        'bspay_url',
        'bspay_ci',
        'bspay_cs',
        'bspay_url_webhook',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'autoPayWithdraw' => 'boolean',
        'affiliatePayGGR' => 'boolean',
    ];
}
