<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileAffiliateUpdateRequest;
use App\Models\AffiliateWithdraw;
use App\Models\User;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Redirect;

class AffiliateController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();

        $email = $request->email;
        $affiliateWithdrawsList = AffiliateWithdraw::with([
            'user' => function ($query) use ($email) {
                $query
                    ->where('isAffiliate', true)
                    ->when($email, function ($query2) use ($email) {
                        $query2->where('email', 'LIKE', '%' . $email . '%');
                    });
            }
        ])->get();

        $affiliates = User::when($email, function ($query) use ($email) {
            $query->where('email', 'LIKE', '%' . $email . '%');
        })
        ->where('isAffiliate', true);

        $affiliateWithdraws = $affiliateWithdrawsList ? $affiliateWithdrawsList->sum('amount') : 0;

        return Inertia::render('Admin/Affiliates', [
            'affiliates' => $affiliates,
            'affiliatesWithdraws' => $affiliateWithdraws,
            'affiliatesWithdrawsList' => $affiliateWithdrawsList

        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileAffiliateUpdateRequest $request)
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('admin.affiliate.edit', );
    }
}