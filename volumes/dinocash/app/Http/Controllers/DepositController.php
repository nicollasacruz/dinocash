<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\User;
use App\Models\Withdraw;
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
        $walletsAmount = User::where('role','user')->where('isAffiliate', '=', false)->sum('wallet');
        $walletsAfilliateAmount = User::where('role','user')->where('isAffiliate', '=', true)->sum('walletAffiliate');
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
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->document) {
            $user->document = $request->document;
        }
        $user->save();
        $uuid = Uuid::uuid4()->toString();
        $response = Http::withHeaders([
            'ci' => env('SUITPAY_CI'),
            'cs' => env('SUITPAY_CS'),
        ])->post(env('SUITPAY_URL') . 'gateway/request-qrcode', [
                    'requestNumber' => $uuid,
                    'dueDate' => now()->addHours(2),
                    'amount' => $request->amount,
                    'callbackUrl' => env('APP_URL_API') . env('SUITPAY_URL_WEBHOOK'),
                    'client' => [
                        'name' => $user->name,
                        'document' => $user->document,
                        'phoneNumber' => $user->document,
                        'email' => $user->document,
                    ],
                ]);

        $result = $response->json();

        $paymentCode = $result['paymentCode'];

        $user->createDeposit($request->amount, $uuid, $paymentCode);

        return redirect()->route('homepage')->with('success', 'Deposit with success!');
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
        $deposit->delete();
    }
}
