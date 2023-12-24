<?php

namespace App\Http\Controllers;

use App\Models\AffiliateHistory;
use App\Models\AffiliateWithdraw;
use App\Models\Deposit;
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


        $depositsAmountPaid = Deposit::when($dateStart && $dateEnd, function ($query) use ($dateStart, $dateEnd) {
            $query->whereRaw('DATE(updated_at) BETWEEN ? AND ?', [$dateStart, $dateEnd]);
        })
            ->where('type', 'paid')
            ->sum('amount');

        $depositsAmountPaidToday = Deposit::where('type', 'paid')->get()->filter(function ($deposit) {
            return $deposit->updated_at->isToday();
        })->sum('amount');

        $withdrawsAmountPaid = Withdraw::when($dateStart && $dateEnd, function ($query) use ($dateStart, $dateEnd) {
            $query->whereRaw('DATE(updated_at) BETWEEN ? AND ?', [$dateStart, $dateEnd]);
        })
            ->where('type', 'paid')
            ->whereHas('user', function ($query) {
                $query->where('isAffiliate', false)
                    ->where('role', 'user');
            })
            ->sum('amount');
        $withdrawsAmountPending = Withdraw::when($dateStart && $dateEnd, function ($query) use ($dateStart, $dateEnd) {
                $query->whereRaw('DATE(updated_at) BETWEEN ? AND ?', [$dateStart, $dateEnd]);
            })
                ->where('type', 'pending')
                ->whereHas('user', function ($query) {
                    $query->where('isAffiliate', false)
                        ->where('role', 'user');
                })
                ->sum('amount');
        $withdrawsAmountAffiliatePaid = AffiliateWithdraw::when($dateStart && $dateEnd, function ($query) use ($dateStart, $dateEnd) {
            $query->whereRaw('DATE(updated_at) BETWEEN ? AND ?', [$dateStart, $dateEnd]);
        })
            ->where('type', 'paid')
            ->whereHas('user', function ($query) {
                $query->where('isAffiliate', true)
                    ->where('role', 'user');
            })
            ->sum('amount');

        $walletsAmount = User::where('role', 'user')
            ->where('isAffiliate', false)
            ->when($dateStart && $dateEnd, function ($query) use ($dateStart, $dateEnd) {
                $query->whereRaw('DATE(updated_at) BETWEEN ? AND ?', [$dateStart, $dateEnd]);
            })->sum('wallet');


        $walletsAfilliateAmount = User::where('role', 'user')
            ->where('isAffiliate', true)
            ->when($dateStart && $dateEnd, function ($query) use ($dateStart, $dateEnd) {
                $query->whereRaw('DATE(updated_at) BETWEEN ? AND ?', [$dateStart, $dateEnd]);
            })->sum('walletAffiliate');

        $walletsAfilliatePending = AffiliateHistory::where('invoicedAt', null)
            ->when($dateStart && $dateEnd, function ($query) use ($dateStart, $dateEnd) {
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

        $caixaDaCasa = $depositsAmountPaid - $withdrawsAmountPaid - $withdrawsAmountAffiliatePaid - $withdrawsAmountPending;

        $lucroTotal = $depositsAmountPaid - $withdrawsAmountPaid - $withdrawsAmountAffiliatePaid - $walletsAmount - $walletsAfilliateAmount - $walletsAfilliatePending;

        $topProfitableAffiliates = $referralService->getTopReferralsByProfit();
        $topLossAffiliates = $referralService->getTopReferralsByLoss();
        $topAffiliatesCPA = $referralService->getTopReferralsByCPA();
        $gain = $depositsAmountPaid ?? 1;
        if (env('APP_GGR_DEPOSIT')) {
            $gain = $gain * ((100 - env('APP_GGR_VALUE') / 100));
        }
        $pay = $withdrawsAmountPaid + $walletsAmount;
        if (!$pay) {
            Log::info('Vazio ou 0');
            $houseHealth = 100;
        } else {
            $houseHealth = 100 - round(($pay * 100 / $gain), 1);
        }

        $activeSessions = DB::table('sessions')
            ->where('last_activity', '>=', now()->subMinutes(5))
            ->count();
        $totalUsers = User::all()->count();
        $totalUsersToday = User::whereDate('created_at', Carbon::today())->count();
        $totalUsersTodayWithDeposit = User::whereDate('created_at', Carbon::today())->whereHas('deposits', function ($query) {
            $query->where('type', 'paid');
        })->count();

        return Inertia::render("Admin/Finances", [
            'activeSessions' => $activeSessions,
            'totalUsers' => $totalUsers,
            'totalUsersToday' => $totalUsersToday,
            'totalUsersTodayWithDeposit' => $totalUsersTodayWithDeposit,
            'balanceAmount' => $caixaDaCasa,
            'depositsAmount' => $depositsAmountPaid,
            'depositsAmountToday' => $depositsAmountPaidToday,
            'withdrawsAmount' => $withdrawsAmountPaid,
            'withdrawsAffiliateAmount' => $withdrawsAmountAffiliatePaid,
            'walletAmount' => $walletsAmount,
            'walletAffiliateAmount' => $walletsAfilliateAmount,
            'walletsAfilliatePending' => $walletsAfilliatePending,
            'lucroTotal' => $lucroTotal,
            'topWithdraws' => $topWithdraws,
            'topDeposits' => $topDeposits,
            'topProfitableAffiliates' => $topProfitableAffiliates,
            'topAffiliatesCPA' => $topAffiliatesCPA,
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
