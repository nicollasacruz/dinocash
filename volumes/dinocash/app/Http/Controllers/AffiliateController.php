<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileAffiliateUpdateRequest;
use App\Models\AffiliateHistory;
use App\Models\AffiliateWithdraw;
use App\Models\GameHistory;
use App\Models\User;
use Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Inertia\Response;
use Redirect;

class AffiliateController extends Controller
{
    public function index(Request $request): Response 
    {
        $today = Carbon::today();

        $email = $request->query('email');

        $affiliateWithdrawsList = AffiliateWithdraw::getAffiliateWithdrawLikeEmail($email);
        $affiliates = User::when($email, function ($query) use ($email) {
            $query->where('email', 'LIKE', '%' . $email . '%');
        })
            ->where('isAffiliate', true)->get();

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
    public function update(ProfileAffiliateUpdateRequest $request): RedirectResponse|Redirector
    {
        $request->user()->fill($request->validated());

        $request->user()->save();

        return redirect(route('admin.afiliados'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(User $user): void
    {
        if ($user->role === 'user') {
            $user->delete();
        }
    }

    public function listAffiliateHistory(Request $request): Response
    {
        $user = $request->query('user');
        $transactions = AffiliateHistory::where('userId', $user)->get();

        return response()->json(['status' => 'success', 'transactions' => $transactions]);
    }

    public function listGameHistory(Request $request): Response
    {
        $user = $request->query('user');
        $transactions = GameHistory::where('userId', $user)->get();

        return response()->json(['status' => 'success', 'transactions' => $transactions]);
    }

    public function listTransactions(Request $request): Response
    {
        $user = $request->query('user');
        $withdraws = AffiliateWithdraw::where('userId', $user)->get();

        return response()->json(['status' => 'success', 'transactions' => $withdraws]);
    }
}
