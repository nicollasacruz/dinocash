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
            ->sum('amount');

        $withdrawsAmountPending = Withdraw::where('type', 'pending')->sum('amount');

        $withdrawsAmountAffiliatePaid = AffiliateWithdraw::when($dateStart && $dateEnd, function ($query) use ($dateStart, $dateEnd) {
            $query->whereRaw('DATE(updated_at) BETWEEN ? AND ?', [$dateStart, $dateEnd]);
        })
            ->where('type', 'paid')
            ->sum('amount');

        $walletsAmount = User::where('role', 'user')
            ->where('isAffiliate', false)
            ->sum('wallet');


        $walletsAfilliateAmount = User::where('role', 'user')
            ->where('isAffiliate', true)
            ->sum('walletAffiliate');

        $walletsAfilliatePending = AffiliateHistory::where('invoicedAt', null)->sum('amount');


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

        $caixaDaCasa = $depositsAmountPaid * 0.965 - $withdrawsAmountPaid * 0.98 - $withdrawsAmountAffiliatePaid * 0.98 - $withdrawsAmountPending;

        $lucroTotal = $depositsAmountPaid * 0.965 - $withdrawsAmountPaid * 0.98 - $withdrawsAmountAffiliatePaid * 0.98 - $walletsAmount - $walletsAfilliateAmount - $walletsAfilliatePending;

        $topProfitableAffiliates = $referralService->getTopReferralsByProfit();
        $topLossAffiliates = $referralService->getTopReferralsByLoss();
        $topAffiliatesCPA = $referralService->getTopReferralsByCPA();

        $gain = $depositsAmountPaid ?? 1;
        if (env('APP_GGR_DEPOSIT')) {
            $gain = $gain * ((100 - env('APP_GGR_VALUE') / 100));
        }
        $pay = $withdrawsAmountPaid + $walletsAmount;
        if (!$pay) {
            $houseHealth = 100;
        } else {
            $houseHealth = 100 - round(($pay * 100 / $gain), 1);
        }

        $activeSessions = DB::table(config('session.table'))
            ->distinct()
            ->select(['users.id', 'users.name', 'users.email'])
            ->whereNotNull('user_id')
            ->where('sessions.last_activity', '>', Carbon::now()->subMinutes(5)->getTimestamp())
            ->leftJoin('users', config('session.table') . '.user_id', '=', 'users.id')
            ->count();
        $totalUsers = User::all()->count();
        $totalUsersToday = User::whereDate('created_at', Carbon::today())->count();
        $totalUsersWithDeposits = User::whereHas('deposits', function ($query) {
            $query->where('type', 'paid');
        })->count();
        $totalUsersTodayWithDeposit = User::whereDate('created_at', Carbon::today())->whereHas('deposits', function ($query) {
            $query->where('type', 'paid');
        })->count();

        $chart = DB::table(DB::raw('(
            SELECT 
                DATE(created_at) AS data,
                SUM(amount) AS depositos,
                NULL AS pagamento_afiliado
            FROM deposits
            WHERE type = "paid"
            GROUP BY DATE(created_at)
    
            UNION ALL
    
            SELECT 
                DATE(created_at) AS data,
                NULL AS depositos,
                SUM(amount) AS pagamento_afiliado
            FROM affiliate_histories
            GROUP BY DATE(created_at)
        ) AS result'))
            ->where('data', '>=', now()->subDays(15)->toDateString())
            ->groupBy('data')
            ->selectRaw('
            data,
            FORMAT(SUM(COALESCE(depositos, 0)), 2) AS depositos,
            FORMAT(SUM(COALESCE(pagamento_afiliado, 0)), 2) AS pagamento_afiliado,
            FORMAT(SUM(COALESCE(depositos, 0)) - SUM(COALESCE(pagamento_afiliado, 0)), 2) AS lucro
        ')
            ->orderBy('data', 'asc')
            ->get();

        return Inertia::render("Admin/Finances", [
            'activeSessions' => $activeSessions,
            'totalUsers' => $totalUsers,
            'totalUsersWithDeposits' => $totalUsersWithDeposits,
            'totalUsersToday' => $totalUsersToday,
            'totalUsersTodayWithDeposit' => $totalUsersTodayWithDeposit,
            'balanceAmount' => $caixaDaCasa,
            'depositsAmount' => $depositsAmountPaid * 0.965,
            'depositsAmountToday' => $depositsAmountPaidToday * 0.965,
            'withdrawsAmount' => $withdrawsAmountPaid * 0.98,
            'withdrawsAffiliateAmount' => $withdrawsAmountAffiliatePaid * 0.98,
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
            'chart' => $chart,
        ]);
    }

    public function updatePayout(Request $request)
    {
        $request->validate([
            'payout' => ['required', 'integer', 'max:100'],
        ]);

        Setting::find(1)->update([
            'payout' => $request->payout,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Viciosidade ajustada com sucesso',
            // 'token' => $hashString,
        ]);
    }
}
