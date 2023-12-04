<?php

namespace App\Http\Controllers;

use App\Models\AffiliateWithdraw;
use App\Models\Deposit;
use App\Models\Setting;
use App\Models\User;
use App\Models\Withdraw;
use App\Services\WithdrawAffiliateService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
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

    public function store(Request $request, WithdrawAffiliateService $withdrawService)
    {
        $userId = Auth::user()->id;
        $user = User::find($userId);
        $withdraw = $withdrawService->createWithdraw($user, $request->amount);
        $setting = Setting::first();

        return Inertia::render('User/Withdraw', [
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
