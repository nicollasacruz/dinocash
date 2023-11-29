<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\User;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class WithdrawController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function indexAdmin(Request $request)
    {
        $email = $request->email;
        $withdraws = Withdraw::with([
            'user' => function ($query) use ($email) {
                $query
                    ->where('isAffiliate', false)
                    ->when($email, function ($query2) use ($email) {
                        $query2->where('email', 'LIKE', '%' . $email . '%');
                    });
            }
        ])->get();
        $totalToday = Withdraw::whereDate('created_at', Carbon::today())->where('type', 'paid')->sum('amount');
        $withdrawsAmount = Withdraw::where('type', 'paid')->sum('amount');
        $depositsAmount = Deposit::where('type', 'paid')->sum('amount');
        $walletsAmount = User::where('role', 'user')->where('isAffiliate', '=', false)->sum('wallet');
        $walletsAfilliateAmount = User::where('role', 'user')->where('isAffiliate', '=', true)->sum('walletAffiliate');
        $totalAmount = ($depositsAmount - $withdrawsAmount - $walletsAmount - $walletsAfilliateAmount) / 100;
        return Inertia::render('Requests', [
            'withdraws' => $withdraws,
            'totalToday' => $totalToday,
            'totalAmount' => $totalAmount,
        ]);
    }

    public function aprove(Withdraw $withdraw) {
        $withdraw->update([
            'type' => 'paid',
            'approvedAt' => now(),
        ]);
    }

    public function reject(Withdraw $withdraw) {
        $withdraw->update([
            'type' => 'rejected',
            'reprovedAt' => now(),
        ]);
    }
}
