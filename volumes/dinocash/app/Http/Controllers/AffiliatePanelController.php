<?php

namespace App\Http\Controllers;

use App\Models\AffiliateHistory;
use App\Models\AffiliateWithdraw;
use App\Models\User;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AffiliatePanelController extends Controller
{
    public function dashboardAffiliate(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $profitCPA = AffiliateHistory::TotalCPA()->where('affiliateId', $user->id)->sum('amount');
        $countCPA = AffiliateHistory::TotalCPA()->where('affiliateId', $user->id)->count();
        $profitToday = AffiliateHistory::winsToday()->where('affiliateId', $user->id)->sum('amount');
        $profitLast30Days = AffiliateHistory::winsLast30Days()->where('affiliateId', $user->id)->sum('amount');
        $profitTotal = AffiliateHistory::winsTotal()->where('affiliateId', $user->id)->sum('amount');
        $lossTotal = AffiliateHistory::lossesTotal()->where('affiliateId', $user->id)->sum('amount');
        $revShareTotal = $profitToday - $lossTotal;
        $paymentPending = AffiliateHistory::where('affiliateId', $user->id)->where('invoicedAt', null)->sum('amount');
        ;
        return Inertia::render('Affiliates/Dashboard', [
            'profitToday' => $profitToday,
            'profitLast30Days' => $profitLast30Days,
            'profitTotal' => $profitTotal,
            'revShareTotal' => $revShareTotal,
            'profitCPA' => $profitCPA,
            'countCPA' => $countCPA,
            'affiliateLink' => $user->invitation_link,
            'walletAffiliate' => $user->walletAffiliate,
            'revShare' => $user->revShare,
            'CPA' => $user->invitation_link,
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

        $affiliateWithdrawsList = AffiliateWithdraw::when($dateStart && $dateEnd, function ($query) use ($dateStart, $dateEnd) {
            $query->whereRaw('DATE(updated_at) BETWEEN ? AND ?', [$dateStart, $dateEnd]);
        })->where('userId', $user->id)->get();

        return Inertia::render('Affiliates/Withdraws', [
            'affiliatesWithdrawsList' => $affiliateWithdrawsList,
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
        })->where('userId', $user->id)->get();
        
        return Inertia::render('Affiliates/History', [
            'affiliateHistory' => $affiliateHistory,
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

        $affiliatesInvoices = $user->invoices->when($dateStart && $dateEnd, function ($query) use ($dateStart, $dateEnd) {
            $query->whereRaw('DATE(updated_at) BETWEEN ? AND ?', [$dateStart, $dateEnd]);
        })->get();

        return Inertia::render('Affiliates/Withdraws', [
            'affiliatesInvoices' => $affiliatesInvoices,
        ]);
    }
}
