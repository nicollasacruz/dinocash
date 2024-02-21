<?php

namespace App\Http\Controllers;

use App\Models\User;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends Controller
{
    public function indexAdmin(Request $request)
    {
        $users = User::when($request->email, function ($query, $email) {
            $query->where('email', 'LIKE', '%' . $email . '%');
        })
            ->select('email', 'wallet', 'bannedAt', 'created_at')
            ->where('isAffiliate', false)
            ->orderByRaw('CAST(wallet AS DECIMAL(10,2)) DESC')
            ->paginate(20);

        return Inertia::render('Admin/Users', [
            'users' => $users,
        ]);
    }


    /**
     * Update the user's profile information.
     */
    public function modalUserUpdate(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        $newUserData = $request->json()->all();

        unset($newUserData['email']);

        $user->update($newUserData);

        return response()->json(['status' => 'success', 'message' => 'Usuário atualizado com sucesso.']);
    }

    public function deleteUser(Request $request)
    {
        $user = User::find($request->params['userId']);
        $user->password = Hash::make('deletado');
        $user->wallet = 0;
        $user->save();

        return response()->json(['status' => 'success', 'message' => 'Usuário apagado com sucesso.']);
    }

    public function banUser(Request $request)
    {
        if (Auth::user()->hasRole('admin')) {
            $user = User::find($request->params['userId']);
            $user->bannedAt = new DateTime();
            $user->wallet = 0;
            $user->save();
            return response()->json(['status' => 'success', 'message' => 'Usuário banido com sucesso.']);
        }
        return response()->json(['status' => 'error', 'message' => 'Não autorizado.']);
    }
}
