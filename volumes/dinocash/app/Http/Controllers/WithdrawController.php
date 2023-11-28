<?php

namespace App\Http\Controllers;

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
        $withdraws = Withdraw::with('user')->get();
        $totalToday = Withdraw::whereDate('created_at', Carbon::today())->where('type', 'paid')->sum('amount');
        
        return Inertia::render('Requests', [
            'withdraws' => $withdraws,
            'totalToday'=> $totalToday
        ]);
    }
}
