<?php

namespace App\Http\Controllers;

use App\Models\GameHistory;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class GameHistoryController extends Controller {
    public function play(Request $request) {
        $viciosidade = false;
        $settings = Setting::first();
        $gain = (GameHistory::where('type', 'loss')->with('user', function () {
            return User::where('role', 'user')->where('isAffiliate', false);
        })->sum('finalAmount')) * -1;
        $pay = (GameHistory::where('type', 'win')->with('user', function () {
            return User::where('role', 'user')->where('isAffiliate', false);
        })->sum('finalAmount'));
        $total = $gain + $pay;
        $houseHealth = round(($gain * 100 / $total), 1);
        if ($houseHealth < $settings->payout) {
            $viciosidade = true;
            Log::error('Viciosidade ativada.');
        }
        $user = User::find(Auth::user()->id);

        return Inertia::render('User/Play', [
            "viciosidade" => $viciosidade,
            "walletUser" => $user->wallet,
        ]);
    }

    public function store(Request $request) {
        try {
            $this->validate($request, [
                'amount' => ['required', 'numeric', 'min:1', 'max:1000'],
            ]);
            $user = User::find(Auth::user()->id);
            if(($user->wallet < $request->amount)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Não tem saldo na carteira',
                ], 500);
            }
            $gameHistory = $user->gameHistories->where('type', 'pending');
            if($gameHistory->count() > 0) {
                foreach($gameHistory as $gameHistoryItem) {
                    $gameHistoryItem->type = 'loss';
                    $gameHistoryItem->finalAmount = $gameHistoryItem->amount * -1;
                    $gameHistoryItem->save();
                    Log::error('Partida já iniciada. - '.$user->email);
                }
            }
            
            $user->changeWallet($request->amount * -1);
            $user->save();
            
            $gameHistory = GameHistory::create([
                'amount' => number_format($request->amount, 2,'.',''),
                'userId' => $user->id,
                'type' => 'pending',
            ]);
            $hashString = hash('sha256', $gameHistory->id.$user->id.'dinocash');

            return response()->json([
                'status' => 'success',
                'gameHistory' => $gameHistory,
                // 'token' => $hashString,
            ]);
        } catch (\Exception $e) {
            Log::error('STORE GAME HISTORY    -    ' . $e->getMessage().' - '.$e->getFile() .' - ' . $e->getLine() .' - ' . $e->getTraceAsString());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }
    public function update(Request $request) {
        try {
            $request->validate([
                'distance' => ['required', 'integer', 'min:0'],
                'gameId' => ['required', 'integer', 'min:0'],
                'type' => ['required', 'string', 'in:win,loss'],
                'token' => ['required', 'string'],
            ]);

            $hashString = hash('sha256', $request->gameId.Auth::user()->id.'dinocash');
            if(!hash_equals($request->token, $hashString)) {
                Log::error('Token não confirmado.');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Token não confirmado.',
                ]);
            }

            $user = User::find(Auth::user()->id);
            $gameHistory = $user->gameHistories->where('type', 'pending')
                ->where('id', $request->gameId)->first();

            if(!$gameHistory) {
                Log::error('Partida não encontrada.');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Partida não encontrada.',
                ]);
            }

            if(!$request->distance) {
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
            if($request->type === 'win') {
                $finalAmount = (($gameHistory->amount / 500) * $request->distance);
                $user->changeWallet((($gameHistory->amount / 500) * $request->distance));
                $user->save();
            }

            $gameHistory->update([
                'finalAmount' => number_format($request->type === 'win' ? $finalAmount - $gameHistory->amount : $finalAmount, 2,'.',''),
                'type' => $request->type,
                'distance' => $request->distance,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Game finalizado com sucesso.',
            ]);

        } catch (\Exception $e) {
            Log::error('UPDATE GAME HISTORY    -    ' . $e->getMessage().' - '.$e->getFile().' - '.$e->getLine() .' - ' . $e->getTraceAsString());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }
}
