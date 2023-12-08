<?php

namespace App\Http\Controllers;

use App\Models\AffiliateHistory;
use App\Models\AffiliateWithdraw;
use App\Models\User;
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
        $paymentPending = AffiliateHistory::where('affiliateId', $user->id)->where('invoicedAt', null)->sum('amount');;
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
        ]);
    }

    public function withdrawsAffiliate(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $email = $request->query('email');

        $affiliateWithdrawsList = AffiliateWithdraw::getAffiliateWithdrawLikeEmail($email);

        $affiliateInvoiceList = $user->invoices;

        $affiliates = User::when($email, function ($query) use ($email) {
            $query->where('email', 'LIKE', '%' . $email . '%');
        })
            ->where('isAffiliate', true)->get();

        $affiliateWithdraws = $affiliateWithdrawsList ? $affiliateWithdrawsList->sum('amount') : 0;
        return Inertia::render('Affiliates/Dashboard', [
            'affiliates' => $affiliates,
            'affiliatesWithdraws' => $affiliateWithdraws,
            'affiliatesWithdrawsList' => $affiliateWithdrawsList,
            'affiliateInvoiceList' => $affiliateInvoiceList,
        ]);
    }

    public function historyAffiliate(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $email = $request->query('email');

        $affiliateWithdrawsList = AffiliateWithdraw::getAffiliateWithdrawLikeEmail($email);

        $affiliateInvoiceList = $user->invoices;

        $affiliates = User::when($email, function ($query) use ($email) {
            $query->where('email', 'LIKE', '%' . $email . '%');
        })
            ->where('isAffiliate', true)->get();

        $affiliateWithdraws = $affiliateWithdrawsList ? $affiliateWithdrawsList->sum('amount') : 0;
        return Inertia::render('Affiliates/Dashboard', [
            'affiliates' => $affiliates,
            'affiliatesWithdraws' => $affiliateWithdraws,
            'affiliatesWithdrawsList' => $affiliateWithdrawsList,
            'affiliateInvoiceList' => $affiliateInvoiceList,
        ]);
    }
}
