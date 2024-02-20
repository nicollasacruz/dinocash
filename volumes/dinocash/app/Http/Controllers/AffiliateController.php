<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileAffiliateUpdateRequest;
use App\Models\AffiliateHistory;
use App\Models\AffiliateWithdraw;
use App\Models\GameHistory;
use App\Models\User;
use App\Services\WithdrawAffiliateService;
use Auth;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Response;
use Redirect;

class AffiliateController extends Controller
{
    public function index(Request $request)
    {
        try {
            $email = $request->query('email');
            $status = $request->query('status') != 'all' ?? false;

            dd($request->query('status'));

            $affiliateWithdrawsList = AffiliateWithdraw::with([
                'user' => function ($query) use ($email) {
                    $query
                        ->when($email, function ($query) use ($email) {
                            $query->where('email', 'LIKE', '%' . $email . '%');
                        });
                }
            ])
                ->select('affiliate_withdraws.created_at', 'affiliate_withdraws.amount', 'affiliate_withdraws.pixKey', 'affiliate_withdraws.pixValue', 'affiliate_withdraws.type')
                ->when($status, function ($query) use ($status) {
                    $query->where('affiliate_withdraws.type', $status);
                })
                ->orderBy('affiliate_withdraws.created_at', 'desc')
                ->get()
                // ->toArray()
            ;

            dd($affiliateWithdrawsList);

            $affiliates = User::when($email, function ($query) use ($email) {
                $query->where('email', 'LIKE', '%' . $email . '%');
            })
                ->where('isAffiliate', true)
                ->paginate(10);

            $affiliates->each(function ($affiliate) {
                $affiliate->referralsDepositsCounter = $affiliate->referralsDepositsCounter;
            });

            // $affiliatesWithdrawsToday = $affiliateWithdrawsList ? $affiliateWithdrawsList->sum('amount') : 0;
            $affiliatesWithdrawsToday = AffiliateWithdraw::with([
                'user' => function ($query) use ($email) {
                    $query
                        ->where('isAffiliate', true)
                        ->when($email, function ($query2) use ($email) {
                            $query2->where('email', 'LIKE', '%' . $email . '%');
                        });
                }
            ])
                ->whereDate('updated_at', Carbon::today())
                ->sum('amount');

            return Inertia::render('Admin/Affiliate', [
                'affiliates' => $affiliates,
                'affiliatesWithdrawsToday' => $affiliatesWithdrawsToday,
                'affiliatesWithdrawsList' => $affiliateWithdrawsList,
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage() . '  -  ///   ' . $e->getTraceAsString());
        }
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileAffiliateUpdateRequest $request)
    {
        $request->user()->fill($request->validated());

        $request->user()->save();

        return Redirect::route('admin.afiliados');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request): void
    {
        $user = User::find($request->query('user'));
        $authUser = User::find(auth()->user()->id);
        if ($authUser->role === 'user') {
            $user->delete();
        }
    }

    public function listAffiliateHistory(Request $request)
    {
        $user = User::find($request->query('user'));
        $transactions = $user->affiliateHistories;

        return response()->json(['status' => 'success', 'transactions' => $transactions]);
    }

    public function listGameHistory(Request $request)
    {
        $user = User::find($request->query('user'));
        $transactions = $user->gameHistories;

        return response()->json(['status' => 'success', 'transactions' => $transactions]);
    }

    public function listTransactions(Request $request)
    {
        $user = User::find($request->query('user'));
        $withdraws = $user->affiliateWithdraws;

        return response()->json(['status' => 'success', 'transactions' => $withdraws]);
    }
}
