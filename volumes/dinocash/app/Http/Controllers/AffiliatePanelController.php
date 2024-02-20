<?php

namespace App\Http\Controllers;

use App\Models\AffiliateHistory;
use App\Models\AffiliateInvoice;
use App\Models\AffiliateWithdraw;
use App\Models\User;
use App\Services\ReferralService;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

use function Pest\Laravel\call;

class AffiliatePanelController extends Controller
{
    public function dashboardAffiliate(Request $request)
    {
        $user = User::find(Auth::user()->id);

        $profitCPA = $user->affiliateHistories->where('type', 'CPA');
        $profitCPAToday = $profitCPA
            ->filter(function ($history) {
                return $history->updated_at->isToday();
            })
            ->sum('amount');
        $profitCPALast30Days = $profitCPA
            ->filter(function ($history) {
                return $history->updated_at->isBetween(now()->subDays(30), now());
            })
            ->sum('amount');
        $profitCPATotal = $profitCPA->sum('amount');

        $countCPA = $profitCPA->count();

        $profitSubCPATotal = $user->affiliateHistories->where('type', 'cpaSub')->sum('amount');

        $profitSubRev = $user->affiliateHistories->where('type', 'revSub')->sum('amount');

        $profitRev = $user->affiliateHistories->whereIn('type', ['win', 'loss']);
        $profitTotal = $profitRev->sum('amount');
        $profitToday = $profitRev
            ->filter(function ($history) {
                return $history->updated_at->isToday();
            })
            ->sum('amount');
        $profitLast30Days =  $profitRev
            ->filter(function ($history) {
                return $history->updated_at->isBetween(now()->subDays(30), now());
            })
            ->sum('amount');

        $countInvited = User::where('affiliateId', $user->id)->count();
        $paymentPending = $user->affiliateHistories->where('invoicedAt', null)->sum('amount');

        return Inertia::render('Affiliates/Dashboard', [
            'profitToday' => $profitToday,
            'profitLast30Days' => $profitLast30Days,
            'profitTotal' => $profitTotal,
            'profitSubRev' => $profitSubRev,
            'countInvited' => $countInvited,
            'profitCPAToday' => $profitCPAToday,
            'profitCPALast30Days' => $profitCPALast30Days,
            'profitCPATotal' => $profitCPATotal,
            'profitSubCPATotal' => $profitSubCPATotal,
            'countCPA' => $countCPA,
            'affiliateLink' => $user->invitation_link,
            'walletAffiliate' => $user->walletAffiliate,
            'revShare' => $user->revShare,
            'CPA' => $user->CPA,
            'revSub' => (int) $user->revSub,
            'cpaSub' => (int) $user->cpaSub,
            'paymentPending' => $paymentPending,
        ]);
    }

    public function dashboardFakeAffiliate(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $profitCPAToday = $user->affiliateHistories
            ->filter(function ($history) {
                return $history->type === 'CPA' && $history->updated_at->isToday();
            })
            ->sum('amount');
        $profitCPALast30Days = $user->affiliateHistories
            ->filter(function ($history) {
                return $history->type === 'CPA' && $history->updated_at->isBetween(now()->subDays(30), now());
            })
            ->sum('amount');
        $profitCPATotal = $user->affiliateHistories->where('type', 'CPA')->sum('amount');
        $countCPA = $user->affiliateHistories->where('type', 'CPA')->count();
        $profitToday = $user->affiliateHistories
            ->filter(function ($history) {
                return $history->type === 'win' && $history->updated_at->isToday();
            })
            ->sum('amount');

        $profitLast30Days = $user->affiliateHistories
            ->filter(function ($history) {
                return $history->type === 'win' && $history->updated_at->isBetween(now()->subDays(30), now());
            })
            ->sum('amount');
        $profitTotal = $user->affiliateHistories->where('type', 'win')->sum('amount');
        $lossToday = $user->affiliateHistories
            ->filter(function ($history) {
                return $history->type === 'loss' && $history->updated_at->isToday();
            })
            ->sum('amount');
        $lossLast30Days = $user->affiliateHistories
            ->filter(function ($history) {
                return $history->type === 'loss' && $history->updated_at->isBetween(now()->subDays(30), now());
            })
            ->sum('amount');
        $lossTotal = $user->affiliateHistories->where('type', 'loss')->sum('amount');
        $countInvited = User::where('affiliateId', $user->id)->count();
        $revShareTotal = $profitTotal - $lossTotal;
        $paymentPending = $user->affiliateHistories->where('invoicedAt', null)->sum('amount');

        return Inertia::render('Affiliates/DashboardFake', [
            'profitToday' => $profitToday,
            'profitLast30Days' => $profitLast30Days,
            'lossLast30Days' => $lossLast30Days,
            'profitTotal' => $profitTotal,
            'countInvited' => $countInvited,
            'lossTotal' => $lossTotal * 1,
            'revShareTotal' => $revShareTotal,
            'profitCPAToday' => $profitCPAToday,
            'profitCPALast30Days' => $profitCPALast30Days,
            'profitCPATotal' => $profitCPATotal,
            'countCPA' => $countCPA,
            'affiliateLink' => $user->invitation_link,
            'walletAffiliate' => $user->walletAffiliate,
            'revShare' => $user->revShare,
            'CPA' => $user->CPA,
            'paymentPending' => $paymentPending,
        ]);
    }

    public function withdrawsAffiliate(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $dateStart = DateTime::createFromFormat('Y-m-d', $request->dateStart);
        $dateEnd = DateTime::createFromFormat('Y-m-d', $request->dateEnd);
        if ($dateStart) {
            $dateStart->setTime(0, 0, 0);
        }

        if ($dateEnd) {
            $dateEnd->setTime(23, 59, 59);
        }

        $affiliateWithdrawsList = AffiliateWithdraw::where('userId', $user->id)
            ->when($dateStart && $dateEnd, function ($query) use ($dateStart, $dateEnd) {
                $query->whereRaw('DATE(updated_at) BETWEEN ? AND ?', [$dateStart, $dateEnd]);
            })->orderBy('updated_at', 'desc')->get();

        return Inertia::render('Affiliates/Withdraws', [
            'affiliatesWithdrawsList' => $affiliateWithdrawsList,
            'user' => $user,
        ]);
    }

    public function historyAffiliate(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $dateStart = DateTime::createFromFormat('Y-m-d', $request->dateStart);
        $dateEnd = DateTime::createFromFormat('Y-m-d', $request->dateEnd);
        if ($dateStart) {
            $dateStart->setTime(0, 0, 0);
        }

        if ($dateEnd) {
            $dateEnd->setTime(23, 59, 59);
        }

        $affiliateHistory = AffiliateHistory::when($dateStart && $dateEnd, function ($query) use ($dateStart, $dateEnd) {
            $query->whereRaw('DATE(updated_at) BETWEEN ? AND ?', [$dateStart, $dateEnd]);
        })
            ->where('affiliateId', $user->id)
            ->where(function ($query) {
                $query->where('type', 'CPA')
                    ->orWhere('type', 'win')
                    ->orWhere('type', 'loss');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('Affiliates/History', [
            'affiliateHistory' => $affiliateHistory,
        ]);
    }

    public function subHistoryAffiliate(Request $request, ReferralService $referralService)
    {
        $user = User::find(Auth::user()->id);
        $dateStart = DateTime::createFromFormat('Y-m-d', $request->dateStart);
        $dateEnd = DateTime::createFromFormat('Y-m-d', $request->dateEnd);
        if ($dateStart) {
            $dateStart->setTime(0, 0, 0);
        }

        if ($dateEnd) {
            $dateEnd->setTime(23, 59, 59);
        }

        $affiliateHistory = AffiliateHistory::when($dateStart && $dateEnd, function ($query) use ($dateStart, $dateEnd) {
            $query->whereRaw('DATE(updated_at) BETWEEN ? AND ?', [$dateStart, $dateEnd]);
        })
            ->where('affiliateId', $user->id)
            ->where(function ($query) {
                $query
                    ->where('type', 'revSub')
                    ->orWhere('type', 'cpaSub');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        $topAffiliatesCPA = $referralService->getTopSubReferralsByCPA($user);


        return Inertia::render('Affiliates/SubHistory', [
            'affiliateHistory' => $affiliateHistory,
            'topSubAffiliatesCPA' => $topAffiliatesCPA,
        ]);
    }

    public function invoicesAffiliate(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $dateStart = DateTime::createFromFormat('Y-m-d', $request->dateStart);
        $dateEnd = DateTime::createFromFormat('Y-m-d', $request->dateEnd);
        if ($dateStart) {
            $dateStart->setTime(0, 0, 0);
        }

        if ($dateEnd) {
            $dateEnd->setTime(23, 59, 59);
        }

        $affiliatesInvoices = AffiliateInvoice::when($dateStart && $dateEnd, function ($query) use ($dateStart, $dateEnd) {
            $query->whereRaw('DATE(updated_at) BETWEEN ? AND ?', [$dateStart, $dateEnd]);
        })->where('affiliateId', $user->id)->orderBy('updated_at', 'desc')->get();

        return Inertia::render('Affiliates/Invoices', [
            'affiliatesInvoices' => $affiliatesInvoices ?? [],
        ]);
    }

    public function setWalletAffiliate(Request $request)
    { 
        try {
            $user = User::find($request->userId);
            $wallet = $request->wallet;

            $user->wallet = number_format($wallet, 2, '.', '');
            $user->save();
            return response()->json([
                'success' => 'success',
                'message' => 'Carteira atualizada com sucesso.',
            ]);
        } catch (Exception $e) {
            Log::error("Erro ao Salvar a carteira:   ". $e->getMessage() . "  -   " . $e->getTraceAsString());
            return response()->json([
                'success' => 'error',
                'message' => 'Erro ao atualizar a carteira.',
            ]);
        }
    }
}
