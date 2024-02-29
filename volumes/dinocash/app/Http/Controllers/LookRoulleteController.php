<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\LookRoulleteService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class LookRoulleteController extends Controller
{
    public function getRoulleteReward(Request $request)
    {
        // Verifica se o usuário está autenticado
        if (!Auth::check() || !Auth::user()->haveRoullete) {
            return Redirect::back();
        }

        try {
            $lookRoulleteService = new LookRoulleteService();
            $request->validate([
                'user' => 'required|integer',
                'rewardOption' => 'required|integer',
            ]);
            $user = User::find($request->user);

            if ($lookRoulleteService->optionRoulletReward($user, $request->rewardOption)) {
                // Modificado para retornar uma resposta JSON para a requisição AJAX
                return response()->json([
                    'status' => 'success',
                    'message' => 'Bônus recolhido com sucesso',
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Ocorreu um erro ao recolher o bônus'
            ]);
        } catch (Exception $e) {
            Log::error('Error LookRoulletControler getRoulleteReward    ---   ' . $e->getMessage() . '  -  ///   ' . $e->getTraceAsString());
            // Modificado para retornar uma resposta JSON para a requisição AJAX
            return response()->json([
                'status' => 'error',
                'message' => 'Ocorreu um erro ao recolher o bônus'
            ]);
        }
    }

    /**
     * Update the user's profile information.
     */
    public function userRollete(Request $request)
    {
        if (Auth::check()) {
            if (Auth::user()->haveRoullete) {
                return Inertia::render('User/Rollete');
            }
        }

        return Redirect::back();
    }
}
