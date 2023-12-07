<?php

namespace App\Http\Controllers;

use App\Models\AffiliateWithdraw;
use App\Models\Deposit;
use App\Models\Setting;
use App\Models\User;
use App\Models\Withdraw;
use App\Services\WithdrawService;
use Auth;
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

        $depositsAmountCaixa = Deposit::where('type', 'paid')->sum('amount');
        $withdrawsAmountCaixa = Withdraw::where('type', 'paid')->sum('amount');
        $withdrawsAmountAffiliateCaixa = AffiliateWithdraw::where('type', 'paid')->sum('amount');
        $walletsAmountCaixa = User::where('role', 'user')->where('isAffiliate', false)->sum('wallet');
        $walletsAfilliateAmountCaixa = User::where('role', 'user')->where('isAffiliate', true)->sum('walletAffiliate');
        $balanceAmount = ($depositsAmountCaixa - $withdrawsAmountCaixa - $withdrawsAmountAffiliateCaixa - $walletsAmountCaixa - $walletsAfilliateAmountCaixa);
        return Inertia::render('Admin/Requests', [
            'withdraws' => $withdraws,
            'totalToday' => $totalToday,
            'totalAmount' => $balanceAmount,
        ]);
    }

    public function indexUser(Request $request)
    {
        return Inertia::render('User/Withdraw', [
        ]);
    }

    public function store(Request $request, WithdrawService $withdrawService)
    {
        $userId = Auth::user()->id;
        $user = User::find($userId);
        $withdraw = $withdrawService->createWithdraw($user, $request->amount);
        $setting = Setting::first();
        if($withdraw &&  $setting->autoPayWithdraw && (float)$withdraw->amount <= $setting->maxAutoPayWithdraw) {
            $withdrawService->aprove($withdraw, 'automatico');
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Saque realizado com sucesso.',
        ]);
    }

    public function aprove(Request $request, WithdrawService $withdrawService) {
        $withdraw = $request->withdraw;
        $withdrawService->aprove($withdraw, 'manual');

        return redirect()->route('admin.saque')->with('success','Saque aprovado com sucesso!');
    }

    public function reject(Request $request, WithdrawService $withdrawService) {
        $withdraw = $request->withdraw;
        $withdrawService->reject($withdraw);

        return redirect()->route('admin.saque')->with('success','Saque rejeitado com sucesso!');
    }
    public function user(Request $request)
    {
        return Inertia::render('User/Movement');
    }
}
