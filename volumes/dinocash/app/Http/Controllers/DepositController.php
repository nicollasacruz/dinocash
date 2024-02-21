<?php

namespace App\Http\Controllers;

use App\Events\PixReceived;
use App\Models\AffiliateWithdraw;
use App\Models\Deposit;
use App\Models\Setting;
use App\Models\User;
use App\Models\Withdraw;
use App\Notifications\PushDemo;
use App\Services\DepositService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use Notification;


class DepositController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function indexAdmin()
    {
        $deposits = Deposit::with('user')->where('type', 'paid')->orderBy('updated_at', 'desc')->paginate(30);

        $totalToday = Deposit::whereDate('created_at', Carbon::today())->where('type', 'paid')->sum('amount');

        $depositsAmountCaixa = Deposit::where('type', 'paid')->sum('amount');
        $withdrawsAmountCaixa = Withdraw::where('type', 'paid')->sum('amount');
        $withdrawsAmountAffiliateCaixa = AffiliateWithdraw::where('type', 'paid')->sum('amount');
        $walletsAmountCaixa = User::where('role', 'user')->where('isAffiliate', false)->sum('wallet');
        $walletsAfilliateAmountCaixa = User::where('role', 'user')->where('isAffiliate', true)->sum('walletAffiliate');
        $balanceAmount = ($depositsAmountCaixa - $withdrawsAmountCaixa - $withdrawsAmountAffiliateCaixa - $walletsAmountCaixa - $walletsAfilliateAmountCaixa);
        return Inertia::render('Admin/Deposits', [
            'deposits' => $deposits,
            'totalToday' => $totalToday,
            'totalAmount' => $balanceAmount,
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, DepositService $depositService)
    {
        $userId = Auth::user()->id;
        $user = User::find($userId);
        $hasBonus = $request->hasBonus ?? false;
        $deposit = $depositService->createDeposit($user, $request->amount, $hasBonus);
        if ($deposit) {
            return response()->json([
                'status' => 'success',
                'message' => 'Deposito gerado com sucesso.',
                'qrCode' => $deposit->paymentCode,
            ]);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Não foi possivel gerar o depósito.',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function index()
    {
        $settings = Setting::first();

        return Inertia::render('User/Deposit', [
            'maxDeposit' => $settings->maxDeposit,
            'minDeposit' => $settings->minDeposit,
        ]);
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
        Log::alert('Entrou no callback');
        if (env('PAYMENT_SERVICE') == 'SUITPAY') {
            Log::alert('Entrou no callback SUITPAY');
            $validatedData = $request->validate([
                'idTransaction' => 'required|string',
                'typeTransaction' => 'required|in:BOLETO,PIX,CARD,PIX_CASHOUT',
                'statusTransaction' => 'required|in:PAID_OUT,CANCELED,UNPAID,CHARGEBACK,WAITING_FOR_APPROVAL,PAYMENT_ACCEPT',
            ]);

            $idTransaction = $request->idTransaction;
            $typeTransaction = $validatedData['typeTransaction'];
            $statusTransaction = $validatedData['statusTransaction'];

            if ($typeTransaction === 'PIX' && $statusTransaction === 'PAID_OUT') {
                $deposit = Deposit::where('externalId', $idTransaction)->where('type', 'pending')->first();
                if ($deposit) {
                    $user = User::find($deposit->user->id);
                    if ($depositService->aproveDeposit($deposit)) {
                        event(new PixReceived($user));
                        try {
                            foreach (User::where('role', 'admin')->get() as $admin) {
                                Notification::send($admin, new PushDemo('R$ ' . number_format(floatval($deposit->amount), 2, ',', '.')));
                            }
                        } catch (Exception $e) {
                            Log::error('Erro de notificar - ' . $e->getMessage());
                        }

                        return response()->json(['status' => 'success', 'message' => 'Deposito aprovado']);
                    }
                }

                return response()->json(['status' => 'error', 'message' => 'Deposito não encontrado'], 500);
            }

            return response()->json(['status' => 'error', 'message' => 'Transação não esperada'], 500);
        } elseif (env('PAYMENT_SERVICE') == 'EZZEBANK') {
            $requestPayload = $request->getContent();
            
            $secretKey = env('EZZEBANK_SIGNATURE_KEY');
            
            $ts = $request->header('Verify-Signature');
            
            $signed_payload = hash_hmac('sha256', $ts . '.' . $requestPayload, $secretKey);
            
            $reqTimestamp = null;
            $reqSignature = null;
            
            if (preg_match('/t=(.*?),/', $ts, $matches)) {
                $reqTimestamp = $matches[1];
            }
            
            if (preg_match('/vsign=(.*?)$/', $ts, $matches)) {
                $reqSignature = $matches[1];
            }
            
            Log::alert('Entrou no callback ezzebank antes ' . hash_equals($reqSignature, $signed_payload));
            if ($reqTimestamp !== null && $reqSignature !== null && hash_equals($reqSignature, $signed_payload)) {
                Log::alert('Entrou dentro do callback ezzebank');
                // Extrair dados da requisição
                $requestData = $request->only(['idTransaction', 'typeTransaction', 'statusTransaction']);

                $idTransaction = $requestData['idTransaction'];
                $typeTransaction = $requestData['typeTransaction'];
                $statusTransaction = $requestData['statusTransaction'];

                if ($typeTransaction === 'PIX' && $statusTransaction === 'PAID_OUT') {
                    $deposit = Deposit::where('externalId', $idTransaction)->where('type', 'pending')->first();
                    if ($deposit) {
                        $user = User::find($deposit->user->id);
                        if ($depositService->aproveDeposit($deposit)) {
                            event(new PixReceived($user));
                            try {
                                foreach (User::where('role', 'admin')->get() as $admin) {
                                    Notification::send($admin, new PushDemo('R$ ' . number_format(floatval($deposit->amount), 2, ',', '.')));
                                }
                            } catch (Exception $e) {
                                Log::error('Erro de notificar - ' . $e->getMessage());
                            }
                            return response()->json(['status' => 'success', 'message' => 'Depósito aprovado']);
                        }
                    }
                    return response()->json(['status' => 'error', 'message' => 'Depósito não encontrado'], 500);
                }
                return response()->json(['status' => 'error', 'message' => 'Transação não esperada'], 500);
            }
        }
    }
}
