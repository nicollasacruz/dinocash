<?php

namespace App\Http\Controllers;

use App\Models\GameHistory;
use App\Models\User;
use Illuminate\Http\Request;
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
            $gameHistory = GameHistory::where('type', 'pending');
            if ($gameHistory->count() > 0) {
                foreach ($gameHistory as $gameHistoryItem) {
                    $gameHistoryItem->type = 'error';
                    $gameHistoryItem->finalAmount = 0;
                    $user = $gameHistoryItem->user;
                    $user->changeWallet($gameHistoryItem->amount);
                    $user->save();
                    $gameHistoryItem->save();
                    Log::error('Partida já iniciada. - ' . $user->email);
                }
                return response()->json([
                    'status' => 'error',
                    'message' => 'Partida já iniciada.',
                ]);
            }

            $user = User::find($request->user);
            $user->changeWallet($request->amount * -1);
            $user->save();
            $gameHistory = GameHistory::create([
                'amount' => number_format($request->amount, 2),
                'userId' => $request->user,
            ]);

            return response()->json([
                'status' => 'success',
                'amount' => $gameHistory->amount,
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
            $validated = $request->validate([
                'amount' => ['required', 'numeric', 'min:1', 'max:100000'],
                'user' => ['required', 'integer', 'min:1'],
                'distance' => ['required', 'integer', 'min:0'],
                'type' => ['required', 'string', 'in:win,loss'],
            ]);
            $user = User::find($request->user);
            $gameHistory = GameHistory::where('type', 'pending')
                ->where('userId', $user->id);

            if (!$gameHistory->count()) {
                Log::error('Partida não encontrada.');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Partida não encontrada.',
                ]);
            }
            if ($gameHistory->count() !== 1) {
                foreach ($gameHistory as $gameHistoryItem) {
                    $gameHistoryItem->type = 'error';
                    $gameHistoryItem->finalAmount = 0;
                    $user = $gameHistoryItem->user;
                    $user->changeWallet($gameHistoryItem->amount);
                    $user->save();
                    $gameHistoryItem->save();
                    Log::error('Partida já iniciada. - ' . $user->email);
                }
                return response()->json([
                    'status' => 'error',
                    'message' => 'Partida já iniciada.',
                ]);
            }

            if ($request->type === 'win') {
                $user = User::find($request->user);
                $user->changeWallet($request->amount);
                $user->save();
            }
            $gameHistory = GameHistory::find($gameHistory->first()->id);
            $gameHistory->update([
                'finalAmount' => number_format($request->type === 'win' ? $request->amount : $request->amount * -1, 2),
                'type' => $request->type,
                'distance' => $request->distance,
            ]);

            return response()->json([
                'status' => 'success',
                'amount' => $gameHistory->finalAmount,
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
