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
    public function indexAdmin()
    {
        $withdraws = Withdraw::with([
            'user' => function ($query) {
                $query
                    ->where('isAffiliate', false);
            }
        ])->get();
        $totalToday = Withdraw::whereDate('created_at', Carbon::today())->where('type', 'paid')->sum('amount');
        $withdrawsAmount = Withdraw::where('type', 'paid')->sum('amount');
        $depositsAmount = Deposit::where('type', 'paid')->sum('amount');
        $walletsAmount = User::where('role','user')->where('isAffiliate', '=', false)->sum('wallet');
        $walletsAfilliateAmount = User::where('role','user')->where('isAffiliate', '=', true)->sum('walletAffiliate');
        $totalAmount = ($depositsAmount - $withdrawsAmount - $walletsAmount - $walletsAfilliateAmount) / 100;
        return Inertia::render('Requests', [
            'withdraws' => $withdraws,
            'totalToday'=> $totalToday,
            'totalAmount' => $totalAmount,
        ]);
    }
}
