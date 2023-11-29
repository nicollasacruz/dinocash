<?php

namespace App\Http\Controllers;

use App\Models\AffiliateHistory;
use App\Models\Deposit;
use App\Models\GameHistory;
use App\Models\User;
use App\Models\Withdraw;
use App\Services\ReferralService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function index(Request $request, ReferralService $referralService)
    {
        $today = Carbon::today();
        $dateStart = $request->dateStart;
        $dateEnd = $request->dateEnd;
        $depositsAmount = Deposit::when($dateStart && $dateEnd, function ($query) use ($dateStart, $dateEnd) {
            $query->whereBetween('updated_at', [$dateStart, $dateEnd]);
        })
            ->where('type', 'paid')
            ->with([
                'user' => function ($query) {
                    $query
                        ->where('isAffiliate', false);
                }
            ])
            ->sum('amount');
        $withdrawsAmount = Withdraw::when($dateStart && $dateEnd, function ($query) use ($dateStart, $dateEnd) {
            $query->whereBetween('updated_at', [$dateStart, $dateEnd]);
        })
            ->where('type', 'paid')
            ->with([
                'user' => function ($query) {
                    $query
                        ->where('isAffiliate', false);
                }
            ])
            ->sum('amount');
        $totalReceived = GameHistory::when($dateStart && $dateEnd, function ($query) use ($dateStart, $dateEnd) {
            $query->whereBetween('updated_at', [$dateStart, $dateEnd]);
        })
            ->where('type', 'loss')
            ->with([
                'user' => function ($query) {
                    $query
                        ->where('isAffiliate', false);
                }
            ])
            ->sum('finalAmount');
        $totalPaid = GameHistory::when($dateStart && $dateEnd, function ($query) use ($dateStart, $dateEnd) {
            $query->whereBetween('updated_at', [$dateStart, $dateEnd]);
        })
            ->where('type', 'win')
            ->with([
                'user' => function ($query) {
                    $query
                        ->where('isAffiliate', false);
                }
            ])
            ->sum('finalAmount');

        $topWithdraws = Withdraw::where('type', 'paid')
            ->where('withdraws.updated_at', '>=', now()->subDay())
            ->join('users', 'withdraws.userId', '=', 'users.id')
            ->select('users.email', 'withdraws.amount')
            ->orderByDesc('amount')
            ->take(3)
            ->get();

        $topDeposits = Deposit::where('type', 'paid')
            ->where('deposits.updated_at', '>=', now()->subDay())
            ->join('users', 'deposits.userId', '=', 'users.id')
            ->select('users.email', 'deposits.amount')
            ->orderByDesc('amount')
            ->take(3)
            ->get();


        dd($topWithdraws, $topDeposits);

        $topProfitableAffiliates = $referralService->getTopReferralsByProfit();
        $topLossAffiliates = $referralService->getTopReferralsByLoss();

        $withdrawsAmountCaixa = Withdraw::where('type', 'paid')->sum('amount');
        $depositsAmountCaixa = Deposit::where('type', 'paid')->sum('amount');
        $walletsAmountCaixa = User::where('role', 'user')->where('isAffiliate', '=', false)->sum('wallet');
        $walletsAfilliateAmountCaixa = User::where('role', 'user')->where('isAffiliate', '=', true)->sum('walletAffiliate');
        $totalAmount = ($depositsAmountCaixa - $withdrawsAmountCaixa - $walletsAmountCaixa - $walletsAfilliateAmountCaixa);

        return Inertia::render("Admin/Finances", [
            'totalAmount' => $totalAmount,
            'depositsAmount' => $depositsAmount,
            'withdrawsAmount' => $withdrawsAmount,
            'totalReceived' => $totalReceived,
            'totalPaid' => $totalPaid,
            'topWithdraws' => $topWithdraws,
            'topDeposits' => $topDeposits,
            'topProfitableAffiliates' => $topProfitableAffiliates,
            'topLossAffiliates' => $topLossAffiliates,
        ]);
    }
}
