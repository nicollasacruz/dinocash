<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function indexAdmin(Request $request)
    {
        $users = User::when($request->email, function ($query, $email) {
            $query->where('email', 'LIKE', '%' . $email . '%');
        })
            ->where('isAffiliate', false)->paginate(20);
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
}
