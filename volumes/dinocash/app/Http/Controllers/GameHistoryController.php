<?php

namespace App\Http\Controllers;

use App\Models\AffiliateHistory;
use App\Models\AffiliateWithdraw;
use App\Models\Deposit;
use App\Models\GameHistory;
use App\Models\Setting;
use App\Models\User;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class GameHistoryController extends Controller
{
    public function play(Request $request)
    {
        $viciosidade = false;
        $settings = Setting::first();

        $depositsAmountPaid = Deposit::where('type', 'paid')
            ->sum('amount');

        $withdrawsAmountPaid = Withdraw::where('type', 'paid')
            ->whereHas('user', function ($query) {
                $query->where('isAffiliate', false);
            })
            ->sum('amount');
        $withdrawsAmountAffiliatePaid = AffiliateWithdraw::where('type', 'paid')
            ->whereHas('user', function ($query) {
                $query->where('isAffiliate', false);
            })
            ->sum('amount');

        $walletsAmount = User::where('role', 'user')->where('isAffiliate', false)->sum('wallet');

        $walletsAfilliateAmount = User::where('role', 'user')->where('isAffiliate', true)->sum('walletAffiliate');

        $walletsAfilliatePending = AffiliateHistory::where('invoicedAt', null)->with([
            'affiliate' => function ($query) {
                $query
                    ->where('role', 'user');
            }
        ])->sum('amount');

        $gain = $depositsAmountPaid ?? 1;
        if (env('APP_GGR_DEPOSIT')) {
            $gain = $gain * ((100 - env('APP_GGR_VALUE') / 100));
        }
        $pay = $withdrawsAmountPaid + $walletsAmount;
        if (!$gain || !$pay) {
            Log::info('Vazio ou 0');
            $houseHealth = 100;
        } else {
            $houseHealth = round(($pay * 100 / $gain), 1);
            if ($houseHealth > 100 - $settings->payout) {
                $viciosidade = true;
                Log::error('Viciosidade ativada.');
            }
        }

        $user = User::find(Auth::user()->id);

        $gameHistory = $user->gameHistories->where('type', 'pending');
        if ($gameHistory) {
            foreach ($gameHistory as $gameHistoryItem) {
                $user->update([
                    'wallet' => $user->wallet + ($gameHistoryItem->amount * 1),
                ]);
                $user->save();
                $gameHistoryItem->delete();
                Log::error('Partida já iniciada. - ' . $user->email);
            }
        }

        $gameHistory = $user->gameHistories->where('type', 'gaming');
        if ($gameHistory) {
            foreach ($gameHistory as $gameHistoryItem) {
                $gameHistoryItem->update([
                    'type' => 'loss',
                    'finalAmount' => number_format($gameHistoryItem->amount * -1, 2,'.',''),
                ]);
                Log::error('Partida já iniciada corrigida. - ' . $user->email);
            }
        }

        return Inertia::render('User/Play', [
            "isAffiliate" => $user->isAffiliate,
            "viciosidade" => $viciosidade,
            "walletUser" => $user->wallet,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->amount = (floatval($request->amount));
            $user = User::find(Auth::user()->id);
            if (($user->wallet < $request->amount)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Não tem saldo na carteira',
                ], 500);
            }
            $gameHistories = $user->gameHistories->where('type', 'pending');
            if ($gameHistories) {
                foreach ($gameHistories as $gameHistoryItem) {
                    $user->changeWallet($gameHistoryItem->amount);
                    $gameHistoryItem->delete();
                    Log::error('Partida já iniciada. - ' . $user->email);
                }
            }

            $gameHistories = $user->gameHistories->where('type', 'gaming');

            if ($gameHistories) {
                foreach ($gameHistories as $gameHistoryItem) {
                    $gameHistoryItem->update([
                        'type' => 'loss',
                        'finalAmount' => number_format($gameHistoryItem->amount * -1, 2,'.',''),
                    ]);
                    Log::error('Partida já iniciada corrigida. - ' . $user->email);
                }
            }
            

            $user->changeWallet($request->amount * -1);
            $user->save();

            $gameHistory = GameHistory::create([
                'amount' => number_format($request->amount, 2, '.', ''),
                'userId' => $user->id,
                'type' => 'pending',
            ]);
            $hashString = hash('sha256', $gameHistory->id . $user->id . 'dinocash');

            return response()->json([
                'status' => 'success',
                'gameHistory' => $gameHistory,
                // 'token' => $hashString,
            ]);
        } catch (\Exception $e) {
            Log::error('STORE GAME HISTORY    -    ' . $e->getMessage() . ' - ' . $e->getFile() . ' - ' . $e->getLine() . ' - ' . $e->getTraceAsString());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }
    public function update(Request $request)
    {
        try {
            $request->validate([
                'distance' => ['required', 'integer', 'min:0'],
                'gameId' => ['required', 'integer', 'min:0'],
                'type' => ['required', 'string', 'in:win,loss'],
                'token' => ['required', 'string'],
            ]);

            $hashString = hash('sha256', $request->gameId . Auth::user()->id . 'dinocash');
            if (!hash_equals($request->token, $hashString)) {
                Log::error('Token não confirmado.');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Token não confirmado.',
                ]);
            }

            $user = User::find(Auth::user()->id);
            $gameHistory = $user->gameHistories->where('type', 'gaming')
                ->where('id', $request->gameId)->first();

            if (!$gameHistory) {
                Log::error('Partida não encontrada.');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Partida não encontrada.',
                ]);
            }

            if (!$request->distance) {
                Log::error('Partida zerada erro');
                $user->changeWallet($gameHistory->amount);
                $user->save();
                $gameHistory->delete();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Partida zerada.',
                ]);
            }

            $finalAmount = $gameHistory->amount * -1;
            if ($request->type === 'win') {
                $finalAmount = (($gameHistory->amount / 500) * $request->distance);
                $user->changeWallet((($gameHistory->amount / 500) * $request->distance));
                $user->save();
            }

            $gameHistory->update([
                'finalAmount' => number_format($request->type === 'win' ? $finalAmount - $gameHistory->amount : $finalAmount, 2, '.', ''),
                'type' => $request->type,
                'distance' => $request->distance,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Game finalizado com sucesso.',
            ]);

        } catch (\Exception $e) {
            Log::error('UPDATE GAME HISTORY    -    ' . $e->getMessage() . ' - ' . $e->getFile() . ' - ' . $e->getLine() . ' - ' . $e->getTraceAsString());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }
}
