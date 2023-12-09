<?php

namespace App\Http\Controllers;

use App\Models\AffiliateHistory;
use App\Models\AffiliateInvoice;
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
        $lossLast30Days = AffiliateHistory::lossesLast30Days()->where('affiliateId', $user->id)->sum('amount');
        $profitTotal = AffiliateHistory::winsTotal()->where('affiliateId', $user->id)->sum('amount');
        $lossTotal = AffiliateHistory::lossesTotal()->where('affiliateId', $user->id)->sum('amount');
        $revShareTotal = $profitToday - $lossTotal;
        $paymentPending = AffiliateHistory::where('affiliateId', $user->id)->where('invoicedAt', null)->sum('amount');
        ;
        return Inertia::render('Affiliates/Dashboard', [
            'profitToday' => $profitToday,
            'profitLast30Days' => $profitLast30Days,
            'lossLast30Days' => $lossLast30Days,
            'profitTotal' => $profitTotal,
            'lossTotal' => $lossTotal * 1,
            'revShareTotal' => $revShareTotal,
            'profitCPA' => $profitCPA,
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

        $affiliateWithdrawsList = AffiliateWithdraw::when($dateStart && $dateEnd, function ($query) use ($dateStart, $dateEnd) {
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
        })->where('affiliateId', $user->id)->orderBy('created_at', 'desc')->get();
        
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

        $affiliatesInvoices = AffiliateInvoice::when($dateStart && $dateEnd, function ($query) use ($dateStart, $dateEnd) {
            $query->whereRaw('DATE(updated_at) BETWEEN ? AND ?', [$dateStart, $dateEnd]);
        })->where('affiliateId', $user->id)->orderBy('updated_at', 'desc')->get();

        return Inertia::render('Affiliates/Invoices', [
            'affiliatesInvoices' => $affiliatesInvoices ?? [],
        ]);
    }
}
