<?php

namespace App\Http\Controllers;

use App\Models\AffiliateWithdraw;
use App\Models\Deposit;
use App\Models\User;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class AffiliateWithdrawController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function indexAdmin(Request $request)
    {
        $email = $request->email;
        $affiliateWithdraws = AffiliateWithdraw::with([
            'user' => function ($query) use ($email) {
                $query
                    ->where('isAffiliate', true)
                    ->when($email, function ($query2) use ($email) {
                        $query2->where('email', 'LIKE', '%' . $email . '%');
                    });
            }
        ])->get();

        return Inertia::render('Requests', [
            'affiliateWithdraws' => $affiliateWithdraws,
        ]);
    }

    public function aprove(AffiliateWithdraw $affiliateWithdraw) {
        $affiliateWithdraw->update([
            'type' => 'paid',
            'approvedAt' => now(),
        ]);
    }

    public function reject(AffiliateWithdraw $affiliateWithdraw) {
        $affiliateWithdraw->update([
            'type' => 'rejected',
            'reprovedAt' => now(),
        ]);
    }
}
