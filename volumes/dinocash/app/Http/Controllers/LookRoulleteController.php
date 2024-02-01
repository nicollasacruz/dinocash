<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\LookRoulleteService;
use Illuminate\Http\Request;

class LookRoulleteController extends Controller
{
    public function getRoulleteReward(Request $request, LookRoulleteService $lookRoulleteService)
    {   
        $request->validate([
            'user' => 'required|integer',
            'rewardOption' => 'required|integer',
        ]);
        $user = User::find($request->user);

        if ($lookRoulleteService->optionRoulletReward($user, $request->rewardOption)) {
            return response()->json([
                'status' => 'success',
                'message' => 'Prêmio recolhido com sucesso'
            ]);
        }
        return response()->json(
            [
                'status' => 'error',
                'message' => 'Ocorreu um erro ao recolher o bônus'
            ]
        );
    }
}
