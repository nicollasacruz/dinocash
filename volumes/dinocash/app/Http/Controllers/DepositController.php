<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\User;
use App\Models\Withdraw;
use App\Services\DepositService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Http;
use Ramsey\Uuid\Uuid;


class DepositController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function indexAdmin()
    {
        $deposits = Deposit::with('user')->get();
        $totalToday = Deposit::whereDate('created_at', Carbon::today())->where('type', 'paid')->sum('amount');
        $withdrawsAmount = Withdraw::where('type', 'paid')->sum('amount');
        $depositsAmount = Deposit::where('type', 'paid')->sum('amount');
        $walletsAmount = User::where('role', 'user')->where('isAffiliate', '=', false)->sum('wallet');
        $walletsAfilliateAmount = User::where('role', 'user')->where('isAffiliate', '=', true)->sum('walletAffiliate');
        $totalAmount = ($depositsAmount - $withdrawsAmount - $walletsAmount - $walletsAfilliateAmount);
        return Inertia::render('Deposits', [
            'deposits' => $deposits,
            'totalToday' => $totalToday,
            'totalAmount' => $totalAmount,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function indexUser()
    {
        $deposits = Deposit::where('user', Auth::user()->id)->with('users')->sort(["updatedAt", 'desc'])->paginate(10);
        return Inertia::render('DepositsUser', [
            'deposits' => $deposits,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, DepositService $depositService)
    {
        $userId = Auth::user()->id;
        $user = User::find($userId);
        $deposit = $depositService->createDeposit($user, $request->amount);

        return Inertia::render('DepositsUserQrCode', [
            'deposit' => $deposit,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Deposit $deposit)
    {
        // a fazer
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Deposit $deposit)
    {
        if ($deposit->type === 'pending') {
            $deposit->delete();
        }
    }

    public function webhook(Request $request, DepositService $depositService)
    {
        if ($request->isMethod('post')) {
            $validatedData = $request->validate([
                'idTransaction' => 'required|string',
                'typeTransaction' => 'required|in:BOLETO,PIX,CARD,PIX_CASHOUT',
                'statusTransaction' => 'required|in:PAID_OUT,CANCELED,UNPAID,CHARGEBACK,WAITING_FOR_APPROVAL,PAYMENT_ACCEPT',
            ]);

            $idTransaction = $validatedData['idTransaction'];
            $typeTransaction = $validatedData['typeTransaction'];
            $statusTransaction = $validatedData['statusTransaction'];
            if ($typeTransaction === 'PIX' && $statusTransaction === 'PAYMENT_ACCEPT') {
                $deposit = Deposit::where('$idTransaction', $idTransaction)->first();

                if ($depositService->aproveDeposit($deposit)) {

                    return response()->json(['status' => 'success']);
                }

                return response()->json(['status' => 'error'], 500);

            }
        }

        return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
    }
}
