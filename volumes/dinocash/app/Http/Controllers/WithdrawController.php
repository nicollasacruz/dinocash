<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\User;
use App\Models\Withdraw;
use App\Services\WithdrawService;
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
        $totalAmount = ($depositsAmount - $withdrawsAmount - $walletsAmount - $walletsAfilliateAmount);
        return Inertia::render('Admin/Requests', [
            'withdraws' => $withdraws,
            'totalToday' => $totalToday,
            'totalAmount' => $totalAmount,
        ]);
    }

    public function aprove(Request $request, WithdrawService $withdrawService) {
        $withdraw = $request->withdraw;
        $withdrawService->aprove($withdraw);

        return redirect()->route('admin.saque')->with('success','Saque aprovado com sucesso!');
    }

    public function reject(Request $request, WithdrawService $withdrawService) {
        $withdraw = $request->withdraw;
        $withdrawService->reject($withdraw);

        return redirect()->route('admin.saque')->with('success','Saque rejeitado com sucesso!');
    }
}
