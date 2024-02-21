<?php

namespace App\Http\Controllers;

use App\Events\WalletChanged;
use App\Models\AffiliateWithdraw;
use App\Models\Deposit;
use App\Models\Setting;
use App\Models\User;
use App\Models\Withdraw;
use App\Services\WithdrawService;
use Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class WithdrawController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function indexAdmin(Request $request)
    {
        $email = $request->email;
        $status = $request->query('status') != 'all' ? $request->query('status') : false;

        $withdraws = DB::table('withdraws')
            ->select(
                'withdraws.updated_at',
                'withdraws.amount',
                'withdraws.pixKey',
                'withdraws.pixValue',
                'withdraws.type',
                'users.email'
            )
            ->leftJoin('users', 'withdraws.userId', '=', 'users.id')
            ->when($email, function ($query) use ($email) {
                $query->where('users.email', 'LIKE', '%' . $email . '%');
            })
            ->when($status, function ($query) use ($status) {
                $query->where('withdraws.type', $status);
            })
            ->orderBy('withdraws.updated_at', 'desc')
            ->paginate(20);

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
        $user = User::find(Auth::user()->id);
        $settings = Setting::first();
        return Inertia::render('User/Withdraw', [
            'minWithdraw' => $settings->minWithdraw,
            'maxWithdraw' => $settings->maxWithdraw,
            'walletUser' => number_format($user->wallet, 2, '.', ''),
        ]);
    }

    public function store(Request $request, WithdrawService $withdrawService)
    {
        try {
            $userId = Auth::user()->id;
            $user = User::find($userId);

            $response = $withdrawService->createWithdraw($user, round($request->amount, 2));

            return response()->json([
                'success' => $response['success'],
                'message' => $response['message'],
            ]);
        } catch (Exception $e) {
            Log::error('ERROR CRIAR WITHDRAW CONTROLLER  - ' . (Auth::user())->id . '  -  ' . $e->getMessage() . '  -  ' . $e->getTraceAsString());
            return response()->json([
                'success' => 'error',
                'message' => 'Erro ao realizar o saque.',
            ]);
        }
    }

    public function aprove(Request $request)
    {
        try {
            $withdrawService = new WithdrawService();

            $withdraw = Withdraw::find($request->withdraw);
            $response = $withdrawService->aprove($withdraw);
            if ($response['success']) {
                return response()->json([
                    'success' => 'success',
                    'message' => 'Saque aprovado com sucesso!'
                ]);
            }
            return response()->json([
                'success' => 'error',
                'message' => $response['message']
            ]);
        } catch (Exception $e) {
            return redirect()->route('admin.saque')->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request)
    {
        $withdrawService = new WithdrawService();
        $withdraw = Withdraw::find($request->withdraw);
        $withdrawService->reject($withdraw);

        return redirect()->route('admin.saque')->with('success', 'Saque rejeitado com sucesso!');
    }
}
