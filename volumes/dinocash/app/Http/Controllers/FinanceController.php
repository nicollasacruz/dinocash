<?php

namespace App\Http\Controllers;

use App\Models\AffiliateHistory;
use App\Models\AffiliateWithdraw;
use App\Models\Deposit;
use App\Models\GameHistory;
use App\Models\Setting;
use App\Models\User;
use App\Models\Withdraw;
use App\Services\ReferralService;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FinanceController extends Controller
{
    public function index(Request $request, ReferralService $referralService)
    {
        $dateStart = DateTime::createFromFormat('Y-m-d', $request->dateStart);
        $dateEnd = DateTime::createFromFormat('Y-m-d', $request->dateEnd);
        if ($dateStart) {
            $dateStart->setTime(0, 0, 0);
        }

        if ($dateEnd) {
            $dateEnd->setTime(23, 59, 59);
        }
        $depositsAmount = Deposit::when($dateStart && $dateEnd, function ($query) use ($dateStart, $dateEnd) {
            $query->whereRaw('DATE(updated_at) BETWEEN ? AND ?', [$dateStart, $dateEnd]);
        })
            ->where('type', 'paid')
            ->whereHas('user', function ($query) {
                $query->where('isAffiliate', false);
            })
            ->sum('amount');


        $withdrawsAmount = Withdraw::when($dateStart && $dateEnd, function ($query) use ($dateStart, $dateEnd) {
            $query->whereRaw('DATE(updated_at) BETWEEN ? AND ?', [$dateStart, $dateEnd]);
        })
            ->where('type', 'paid')
            ->whereHas('user', function ($query) {
                $query->where('isAffiliate', false);
            })
            ->sum('amount');
        $withdrawsAmountAffiliate = AffiliateWithdraw::when($dateStart && $dateEnd, function ($query) use ($dateStart, $dateEnd) {
            $query->whereRaw('DATE(updated_at) BETWEEN ? AND ?', [$dateStart, $dateEnd]);
        })
            ->where('type', 'paid')
            ->whereHas('user', function ($query) {
                $query->where('isAffiliate', false);
            })
            ->sum('amount');
            $totalReceived = GameHistory::when($dateStart && $dateEnd, function ($query) use ($dateStart, $dateEnd) {
                $query->whereRaw('DATE(updated_at) BETWEEN ? AND ?', [$dateStart, $dateEnd]);
            })
                ->where('type', 'loss')
                ->whereHas('user', function ($query) {
                    $query->where('isAffiliate', false);
                })
                ->sum('finalAmount');
            
        $totalPaid = GameHistory::when($dateStart && $dateEnd, function ($query) use ($dateStart, $dateEnd) {
            $query->whereRaw('DATE(updated_at) BETWEEN ? AND ?', [$dateStart, $dateEnd]);
        })
            ->where('type', 'win')
            ->whereHas('user', function ($query) {
                $query->where('isAffiliate', false);
            })
            ->sum('finalAmount');

        $walletsAmount = User::where('role', 'user')->where('isAffiliate', false)->when($dateStart && $dateEnd, function ($query) use ($dateStart, $dateEnd) {
            $query->whereRaw('DATE(updated_at) BETWEEN ? AND ?', [$dateStart, $dateEnd]);
        })->sum('wallet');

        $walletsAfilliateAmount = User::where('role', 'user')->where('isAffiliate', true)->when($dateStart && $dateEnd, function ($query) use ($dateStart, $dateEnd) {
            $query->whereRaw('DATE(updated_at) BETWEEN ? AND ?', [$dateStart, $dateEnd]);
        })->sum('walletAffiliate');

        $walletsAfilliatePending = AffiliateHistory::where('invoicedAt', null)->when($dateStart && $dateEnd, function ($query) use ($dateStart, $dateEnd) {
            $query->whereRaw('DATE(updated_at) BETWEEN ? AND ?', [$dateStart, $dateEnd]);
        })->with([
                    'affiliate' => function ($query) {
                        $query
                            ->where('role', 'user');
                    }
                ])->sum('amount');

        $topWithdraws = Withdraw::where('type', 'paid')
            ->where('withdraws.updated_at', '>=', now()->subDay())
            ->join('users', 'withdraws.userId', '=', 'users.id')
            ->select('users.email', 'withdraws.amount')
            ->orderByDesc('amount')
            ->take(3)
            ->get()
            ->map(function ($withdraw) {
                return [
                    'amount' => $withdraw->amount,
                    'user_email' => $withdraw->email,
                ];
            });

        $topDeposits = Deposit::where('type', 'paid')
            ->where('deposits.updated_at', '>=', now()->subDay())
            ->join('users', 'deposits.userId', '=', 'users.id')
            ->select('users.email', 'deposits.amount')
            ->orderByDesc('amount')
            ->take(3)
            ->get()
            ->map(function ($deposit) {
                return [
                    'amount' => $deposit->amount,
                    'user_email' => $deposit->email,
                ];
            });

        $topProfitableAffiliates = $referralService->getTopReferralsByProfit();
        $topLossAffiliates = $referralService->getTopReferralsByLoss();

        $depositsAmountCaixa = Deposit::where('type', 'paid')->sum('amount');
        $withdrawsAmountCaixa = Withdraw::where('type', 'paid')->sum('amount');
        $withdrawsAmountAffiliateCaixa = AffiliateWithdraw::where('type', 'paid')->sum('amount');
        $walletsAmountCaixa = User::where('role', 'user')->where('isAffiliate', false)->sum('wallet');
        $walletsAfilliateAmountCaixa = User::where('role', 'user')->where('isAffiliate', true)->sum('walletAffiliate');
        $walletsAfilliatePendingCaixa = AffiliateHistory::where('invoicedAt', null)->sum('amount');
        
        $gain = (GameHistory::where('type', 'loss')->with('user', function () {
            return User::where('role', 'user')->where('isAffiliate', false);
        })->sum('finalAmount')) * -1;
        $pay = (GameHistory::where('type', 'win')->with('user', function () {
            return User::where('role', 'user')->where('isAffiliate', false);
        })->sum('finalAmount'));
        $total = $gain + $pay;
        if (!$total || !$gain) {
            Log::info('Vazio ou 0');
            $houseHealth = 100;
        } else {
            $houseHealth = round(($gain * 100 / $total), 1);
        }
        
        $balanceAmount = ($depositsAmountCaixa - $withdrawsAmountCaixa - $withdrawsAmountAffiliateCaixa - $walletsAmountCaixa - $walletsAfilliateAmountCaixa - $walletsAfilliatePendingCaixa);
        
        return Inertia::render("Admin/Finances", [
            'balanceAmount' => $balanceAmount,
            'depositsAmount' => $depositsAmount,
            'withdrawsAmount' => $withdrawsAmount,
            'withdrawsAffiliateAmount' => $withdrawsAmountAffiliate,
            'walletAmount' => $walletsAmount,
            'walletAffiliateAmount' => $walletsAfilliateAmount,
            'walletsAfilliatePending' => $walletsAfilliatePending,
            'totalReceived' => ($totalReceived * -1),
            'totalPaid' => $totalPaid * -1,
            'topWithdraws' => $topWithdraws,
            'topDeposits' => $topDeposits,
            'topProfitableAffiliates' => $topProfitableAffiliates,
            'topLossAffiliates' => $topLossAffiliates,
            'payout' => Setting::first('payout'),
            'houseHealth' => $houseHealth * 1,
        ]);
    }

    public function updatePayout(Request $request)
    {
        $request->validate([
            'payout' => ['required', 'integer', 'max:100'],
        ]);

        Setting::update([
            'payout' => $request->payout,
        ]);
    }
}
