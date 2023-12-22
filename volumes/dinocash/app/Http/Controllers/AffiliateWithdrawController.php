<?php

namespace App\Http\Controllers;

use App\Models\AffiliateWithdraw;
use App\Models\Deposit;
use App\Models\Setting;
use App\Models\User;
use App\Models\Withdraw;
use App\Services\WithdrawAffiliateService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class AffiliateWithdrawController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function indexAdmin(Request $request)
    {
        $email = $request->email;
        $affiliateWithdraws = AffiliateWithdraw::with([
            'user' => function ($query) use ($email) {
                $query
                    ->where('isAffiliate', true)
                    ->when($email, function ($query2) use ($email) {
                        $query2->where('email', 'LIKE', '%' . $email . '%');
                    });
            }
        ])->get();

        return Inertia::render('Requests', [
            'affiliateWithdraws' => $affiliateWithdraws,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $withdrawService = new WithdrawAffiliateService();
            $userId = Auth::user()->id;
            $user = User::find($userId);
            $withdraw = $withdrawService->createWithdraw($user, $request->amount, $request->pixType, $request->pixKey);
            if ($withdraw) {
                if ($withdraw->amount < 1000) {
                    $withdrawService->aprove($withdraw);
                }
                return response()->json([
                    'success' => 'success',
                    'message' => 'Saque solicitado com sucesso.'
                ]);
            }
            return response()->json([
                'success' => 'error',
                'message' => 'Não possui saldo disponivel suficiente.'
            ]);
        } catch (Exception $e) {
            Log::error('Erro ao solicitar o saque de afiliado.  -   ' . $e->getMessage());
            return response()->json([
                'success' => 'error',
                'message' => 'Erro ao solicitar o saque.'
            ]);
        }
    }

    public function aprove(Request $request)
    {
        try {
            $withdrawService = new WithdrawAffiliateService();

            $withdraw = AffiliateWithdraw::find($request->withdraw);
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
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                Log::error('Erro ao aprovar o o saque do afiliado  - ' . $e->getMessage()),
                'success' => 'error',
                'message' => 'Erro interno!'
            ], 500);
        }
    }

    public function reject(Request $request)
    {
        try {
            $withdrawService = new WithdrawAffiliateService();
            $withdraw = AffiliateWithdraw::find($request->withdraw);
            if ($withdraw->type === 'pending') {
                $response = $withdrawService->reject($withdraw);
                return response()->json([
                    'success' => $response['success'],
                    'message' => $response['message']
                ], 500);
            }
            return response()->json([
                Log::error('Erro ao aprovar o o saque do afiliado.'),
                'success' => 'error',
                'message' => 'Saque já foi rejeitado.'
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                Log::error('Erro ao aprovar o o saque do afiliado  - ' . $e->getMessage()),
                'success' => 'error',
                'message' => 'Erro interno!'
            ], 500);
        }
    }
}
