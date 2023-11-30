<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileAffiliateUpdateRequest;
use App\Models\AffiliateHistory;
use App\Models\AffiliateWithdraw;
use App\Models\GameHistory;
use App\Models\User;
use Auth;
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
    public function update(ProfileAffiliateUpdateRequest $request)
    {
        $request->user()->fill($request->validated());

        $request->user()->save();

        return Redirect::route('admin.afiliados', );
    }

        /**
     * Remove the specified resource from storage.
     */
    public function delete(User $user)
    {
        if ($user->role === 'user') {
            $user->delete();
        }
    }

    public function listAffiliateHistory(Request $request)
    {
        $user = $request->query('user');
        $transactions = AffiliateHistory::where('userId', $user)->get();
        
        return response()->json(['status' => 'success', 'transactions' => $transactions]);
    }

    public function listGameHistory(Request $request)
    {
        $user = $request->query('user');
        $transactions = GameHistory::where('userId', $user)->get();

        return response()->json(['status' => 'success', 'transactions' => $transactions]);
    }

    public function listTransactions(Request $request)
    {
        $user = $request->query('user');
        $withdraws = AffiliateWithdraw::where('userId', $user)->get();

        return response()->json(['status' => 'success', 'transactions' => $withdraws]);
    }
}
