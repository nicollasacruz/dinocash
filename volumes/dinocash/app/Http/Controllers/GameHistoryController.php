<?php

namespace App\Http\Controllers;

use App\Events\WalletChanged;
use App\Models\BonusWalletChange;
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

        $walletsAmount = User::where('role', 'user')->where('isAffiliate', false)->sum('wallet');

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
        if ($user) {
            $gameHistory = $user->gameHistories->where('type', 'pending');
            if ($gameHistory) {
                foreach ($gameHistory as $gameHistoryItem) {
                    if ($gameHistory->amountType !== 'bonus') {
                        $user->wallet = (($user->wallet * 1) + ($gameHistoryItem->amount * 1));
                    } else {
                        $bonus = $user->bonus->where('status', 'active')->first();
                        BonusWalletChange::create([
                            'bonusCampaignId' => $bonus->id,
                            'amountOld' => $user->bonusWallet,
                            'amountNew' => (($user->bonusWallet * 1) + ($gameHistoryItem->amount * 1)),
                            'type' => 'game pending error',
                        ]);
                        $bonus->amountMovement -= ($gameHistoryItem->amount * 1);
                        $bonus->save();
                        $user->bonusWallet = (($user->bonusWallet * 1) + ($gameHistoryItem->amount * 1));
                    }
                    $user->save();
                    $gameHistoryItem->affiliateHistories->each(function ($affiliateHistory) {
                        $affiliateHistory->delete();
                    });
                    $gameHistoryItem->delete();
                    $message = [
                        "id" => $user->id,
                        "wallet" => $user->wallet
                    ];

                    event(new WalletChanged($message));

                    Log::error('Partida já iniciada. - ' . $user->email);
                }
            }
        }

        $gameHistory = $user->gameHistories->where('type', 'gaming');
        if ($gameHistory) {
            foreach ($gameHistory as $gameHistoryItem) {

                if ($gameHistory->amountType !== 'bonus') {
                    $user->wallet = (($user->wallet * 1) + ($gameHistoryItem->amount * 1));
                } else {
                    $bonus = $user->bonus->where('status', 'active')->first();
                    BonusWalletChange::create([
                        'bonusCampaignId' => $bonus->id,
                        'amountOld' => $user->bonusWallet,
                        'amountNew' => (($user->bonusWallet * 1) + ($gameHistoryItem->amount * 1)),
                        'type' => 'game gaming error',
                    ]);
                    $bonus->amountMovement -= ($gameHistoryItem->amount * 1);
                    $bonus->save();
                    $user->bonusWallet = (($user->bonusWallet * 1) + ($gameHistoryItem->amount * 1));
                }
                $user->save();
                $gameHistoryItem->affiliateHistories->each(function ($affiliateHistory) {
                    $affiliateHistory->delete();
                });
                $gameHistoryItem->delete();
                $message = [
                    "id" => $user->id,
                    "wallet" => $user->wallet + $user->bonusWallet
                ];

                event(new WalletChanged($message));

                Log::error('Partida já iniciada. - ' . $user->email);
            }
        }

        return Inertia::render('User/Play', [
            "isAffiliate" => $user->isAffiliate,
            "viciosidade" => $viciosidade,
            "walletUser" => $user->wallet + $user->bonusWallet,
            "maxAmmount" => $settings->maxAmountPlay
        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->amount = (floatval($request->amount));
            if ($request->amount <= 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Aposta não pode ser menor que 0',
                ], 500);
            }
            $user = User::find(Auth::user()->id);
            if (($user->wallet + $user->bonusWallet < $request->amount)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Não tem saldo na carteira',
                ], 500);
            }
            if (($user->wallet + $user->bonusWallet >= $request->amount && $user->wallet > 0 && $user->wallet < $request->amount)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Use primeiro o saldo da carteira antes de usar o bônus',
                ], 500);
            }
            if ($user->wallet > 0) {
                $user->changeWallet($request->amount * -1, 'game store');
            } else {
                if ($request->amount > Setting::first()->maxAmountPlayBonus) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Não é possível apostar mais que R$' . number_format(Setting::first()->maxAmountPlayBonus, 2, ',', '.') . ' com o bônus',
                    ], 500);
                }
                $bonus = $user->bonusCampaings->where('status', 'active')->first();
                BonusWalletChange::create([
                    'bonusCampaignId' => $bonus->id,
                    'amountOld' => $user->bonusWallet,
                    'amountNew' => $user->bonusWallet - $request->amount,
                    'type' => 'game start',
                ]);
                $bonus->amountMovement += $request->amount;
                $bonus->save();
                $user->bonusWallet -= $request->amount;
            }
            $user->save();

            $message = [
                "id" => $user->id,
                "wallet" => $user->wallet + $user->bonusWallet
            ];

            event(new WalletChanged($message));
            $amountType = $user->isAffiliate ? 'fake' : ($user->wallet >= $request->amount ? 'real' : 'bonus');
            $gameHistory = GameHistory::create([
                'amount' => number_format($request->amount, 2, '.', ''),
                'userId' => $user->id,
                'type' => 'pending',
                'amountType' => $amountType,
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
                'type' => ['required', 'string', 'in:win,loss,locked'],
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

            if ($request->type === 'locked') {
                Log::error('Partida com bug / ' . $user->email);
                if ($gameHistory->amountType !== 'bonus') {
                    $user->wallet = (($user->wallet * 1) + ($gameHistory->amount * 1));
                } else {
                    $bonus = $user->bonus->where('status', 'active')->first();
                    BonusWalletChange::create([
                        'bonusCampaignId' => $bonus->id,
                        'amountOld' => $user->bonusWallet,
                        'amountNew' => $user->bonusWallet + $gameHistory->amount,
                        'type' => 'game refunded',
                    ]);
                    $bonus->amountMovement -= ($gameHistory->amount * 1);
                    $bonus->save();
                    $user->bonusWallet = (($user->bonusWallet * 1) + ($gameHistory->amount * 1));
                }
                $user->save();
                $gameHistory->type = 'locked';
                $gameHistory->finalAmount = 0;
                $gameHistory->save();

                return response()->json([
                    'status' => 'error',
                    'message' => 'Partida bloqueada por uso de economia de energia.',
                    'errors' => [
                        'locked' => 'Partida bloqueada por uso de economia de energia.',
                    ],
                ]);
            }


            if (!$request->distance) {
                Log::error('Partida zerada erro');
                if ($gameHistory->amountType !== 'bonus') {
                    $user->wallet = (($user->wallet * 1) + ($gameHistory->amount * 1));
                } else {
                    $bonus = $user->bonus->where('status', 'active')->first();
                    BonusWalletChange::create([
                        'bonusCampaignId' => $bonus->id,
                        'amountOld' => $user->bonusWallet,
                        'amountNew' => $user->bonusWallet + $gameHistory->amount,
                        'type' => 'game zero error',
                    ]);
                    $bonus->amountMovement -= ($gameHistory->amount * 1);
                    $bonus->save();
                    $user->bonusWallet = (($user->bonusWallet * 1) + ($gameHistory->amount * 1));
                }
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
                if ($gameHistory->amountType !== 'bonus') {
                    $user->changeWallet((($gameHistory->amount / 500) * $request->distance), 'game win');
                } else {
                    $bonus = $user->bonus->where('status', 'active')->first();
                    BonusWalletChange::create([
                        'bonusCampaignId' => $bonus->id,
                        'amountOld' => $user->bonusWallet,
                        'amountNew' => $user->bonusWallet + $finalAmount,
                        'type' => 'game ended',
                    ]);
                    $user->bonusWallet += $finalAmount;
                }
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
                'lookRoullet' => $request->type === 'win' ? false : self::getLookRoullet(),
            ]);
        } catch (\Exception $e) {
            Log::error('UPDATE GAME HISTORY    -    ' . $e->getMessage() . ' - ' . $e->getFile() . ' - ' . $e->getLine() . ' - ' . $e->getTraceAsString());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'lookRoullet' => false,
            ]);
        }
    }

    public static function getLookRoullet(): bool
    {
        if (Auth::user()->isAffiliate) {
            return rand(1, 100) <= 40;
        }
        return rand(1, 100) <= 5;
    }
}
