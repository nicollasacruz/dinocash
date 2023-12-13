<?php

namespace App\Http\Controllers;

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
            $setting = Setting::first();
            if (!$user->isAffiliate || !$user->hasRole('admin')) {
                $totalDeposits = $user->deposits->where('type', 'paid')->sum('amount');
                $totalRoll = $user->gameHistories->sum('amount');
                $hasWIthdrawToday = $user->withdraws
                ->filter(function ($withdraw) {
                    return $withdraw->type === 'paid' && $withdraw->updated_at->isToday();
                })->first();

                if ($hasWIthdrawToday) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Só é possível fazer um saque por dia.',
                    ]);
                }
                
                $totalNeeded = ($totalDeposits * $setting->rollover) - $totalRoll;

                if ($totalRoll < $totalDeposits * $setting->rollover) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Você precisa movimentar mais R$' . number_format($totalNeeded) . ' para conseguir sacar',
                    ]);
                }
            }

            if ($user->wallet < $request->amount || $request->amount < 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Valor não disponivel para saque.',
                ]);
            }

            $withdraw = $withdrawService->createWithdraw($user, round($request->amount, 2));
            if (is_bool($withdraw)) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Saque realizado com sucesso.',
                ]);
            }
            if (!!$withdraw && $setting->autoPayWithdraw && (float) $withdraw->amount <= $setting->maxAutoPayWithdraw) {
                Log::info('entrou no auto saque');
                $response = $withdrawService->aprove($withdraw);
                if (!$response['success']) {
                    Log::info('Criado mas não foi pago');
                    Log::info($response['message']);
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Saque realizado com sucesso.',
            ]);
        } catch (Exception $e) {
            Log::error('ERROR CRIAR WITHDRAW   - ' . (Auth::user())->id . '  -  ' . $e->getMessage() . '  -  ' . $e->getTraceAsString());
            return response()->json([
                'status' => 'error',
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
