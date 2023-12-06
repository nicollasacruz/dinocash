<?php

namespace App\Http\Controllers;

use App\Models\GameHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class GameHistoryController extends Controller
{
    public function play(Request $request)
    {
        return Inertia::render('User/Play');
    }

    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'amount' => ['required', 'numeric', 'min:1', 'max:1000'],
                'user' => ['required', 'integer', 'min:1'],
            ]);

            if ((User::find($request->user)->wallet < $request->amount)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Não tem saldo na carteira',
                ], 500);
            }
            $gameHistory = User::find($request->user)->gameHistory->where('type', 'pending')->count();
            if ($gameHistory > 0) {
                foreach ($gameHistory->get() as $gameHistoryItem) {
                    $gameHistoryItem->type = 'loss';
                    $gameHistoryItem->finalAmount = $gameHistoryItem->amount * -1;
                    $user = $gameHistoryItem->user;
                    $gameHistoryItem->save();
                    Log::error('Partida já iniciada. - ' . $user->email);
                }
                return response()->json([
                    'status' => 'loss',
                    'message' => 'Partida já iniciada.',
                ]);
            }

            $user = User::find($request->user);
            $user->changeWallet($request->amount * -1);
            $user->save();
            $gameHistory = GameHistory::create([
                'amount' => number_format($request->amount, 2),
                'userId' => $request->user,
                'type' => 'pending',
            ]);
            $hashString = hash('sha256', $gameHistory->id . Auth::user()->id . 'dinocash');

            return response()->json([
                'status' => 'success',
                'gameHistory' => $gameHistory,
                // 'token' => $hashString,
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' - ' . $e->getFile() . ' - ' . $e->getLine());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }
    public function update(Request $request)
    {
        try {
            $request->amount = number_format($request->amount, 2, '.', ',');
            $request->validate([
                'distance' => ['required', 'integer', 'min:0'],
                'gameId' => ['required', 'integer', 'min:0'],
                'type' => ['required', 'string', 'in:win,loss'],
                'token' => ['required', 'string'],
            ]);

            $hashString = hash('sha256', $request->gameId . Auth::user()->id . 'dinocash');
            if(!hash_equals($request->token, $hashString)) {
                Log::error('Token não confirmado.');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Token não confirmado.',
                ]);
            }

            $user = User::find($request->user);
            $gameHistory = $user->gameHistory->where('type', 'pending')
                ->where('id', $request->gameId);

            if (!$gameHistory->count()) {
                Log::error('Partida não encontrada.');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Partida não encontrada.',
                ]);
            }

            if ($request->type === 'win') {
                $user = User::find($request->user);
                $user->changeWallet($request->amount);
                $user->save();
            }

            $gameHistory->update([
                'finalAmount' => number_format($request->type === 'win' ? $request->amount : $request->amount * -1, 2),
                'type' => $request->type,
                'distance' => $request->distance,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Game finalizado com sucesso.',
            ]);

        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' - ' . $e->getFile() . ' - ' . $e->getLine());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }
}
