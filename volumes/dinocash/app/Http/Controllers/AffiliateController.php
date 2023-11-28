<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileAffiliateUpdateRequest;
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

        if ($email = $request->email) {
            $affiliates = User::where('email', 'LIKE', '%' . $email . '%')
                ->where('isAffiliated', true)
                ->with(['withdraws' => function ($query) use ($today) {
                    $query
                    ->where('type', '!=', 'rejected')
                    ->whereDate('updated_at', $today);
                }])
                ->get();
        } else {
            $affiliates = User::where('isAffiliated', true)
                ->with(['withdraws' => function ($query) use ($today) {
                    $query
                    ->where('type', '!=', 'rejected')
                    ->whereDate('updated_at', $today);
                }])
                ->get();
        }
        $affiliatesWithdrawsList = $affiliates->flatMap->withdraws->get();
        $affiliatesWithdraws = $affiliates->flatMap->withdraws->sum('amount');

        return Inertia::render('Affiliates', [
            'affiliates' => $affiliates,
            'affiliatesWithdraws' => $affiliatesWithdraws,
            'affiliatesWithdrawsList' => $affiliatesWithdrawsList

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
