<?php

namespace App\Http\Controllers;

use App\Models\AffiliateHistory;
use App\Models\Deposit;
use App\Models\GameHistory;
use App\Models\User;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function index(Request $request)
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

        $topWithdraws = Withdraw::when($dateStart && $dateEnd, function ($query) use ($dateStart, $dateEnd) {
            $query->whereBetween('updated_at', [$dateStart, $dateEnd]);
        })
            ->where('type', 'paid')
            ->where('updated_at', $today)
            ->with([
                'user' => function ($query) {
                    $query
                        ->where('isAffiliate', false);
                }
            ])
            ->orderBy('amount', 'desc')
            ->take(3)
            ->get();

        $topDeposits = Deposit::when($dateStart && $dateEnd, function ($query) use ($dateStart, $dateEnd) {
            $query->whereBetween('updated_at', [$dateStart, $dateEnd]);
        })
            ->where('type', 'paid')
            ->where('updated_at', $today)
            ->with([
                'user' => function ($query) {
                    $query
                        ->where('isAffiliate', false);
                }
            ])
            ->orderBy('amount', 'desc')
            ->take(3)
            ->get();

        $topProfitableAffiliates = AffiliateHistory::where('type', 'win')
            ->select('affiliateId', DB::raw('SUM(amount) as totalProfit'))
            ->groupBy('affiliateId')
            ->orderByDesc('totalProfit')
            ->take(3)
            ->get();
        
        $topLossAffiliates = AffiliateHistory::where('type', 'loss')
            ->select('affiliateId', DB::raw('SUM(amount) as totalLoss'))
            ->groupBy('affiliateId')
            ->orderByDesc('totalLoss')
            ->take(3)
            ->get();

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
        ]);
    }
}
