<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller {
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response {
        return Inertia::render('User/ChangePassword', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse {
        $request->user()->fill($request->validated());

        if($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('user.edit');
    }

    /**
     * Update the user's profile information.
     */
    public function modalUserUpdate(Request $request) {

        $user = User::where('email', $request->email)->first(); 
    
        $newUserData = $request->json()->all();
        
        unset($newUserData['email']);

        $user->update($newUserData);
    
        return response()->json(['status' => 'success', 'message' => 'Usuário atualizado com sucesso.']);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function listAffiliateHistory(Request $request) {
        $userId = $request->query('user');
        $history = User::find($userId)->join('affiliate_histories', 'users.id', '=', 'affiliate_histories.affiliateId')->where(
            'affiliate_histories.affiliateId', '=', $userId
        )->get();

        return response()->json(['status' => 'success', 'transactions' => $history]);
    }

    public function listGameHistory(Request $request) {
        $userId = $request->query('user');
        $history = User::find($userId)->join('game_histories', 'users.id', '=', 'game_histories.userId')->where(
            'users.id', '=', $userId
        )->get();
        return response()->json(['status' => 'success', 'history' => $history]);
    }

    public function listTransactions(Request $request) {
        $userId = $request->query('user');
        $withdraws = User::find($userId)->join('withdraws', 'users.id', '=', 'withdraws.userId')->where(
            'users.id', '=', $userId
        )->get();
        
        return response()->json(['status' => 'success', 'history' => $withdraws]);
    }
    
    public function listDeposits(Request $request) {
        $user = $request->user();
        $deposits = $user->deposits;

        return response()->json(['status' => 'success', 'deposits' => $deposits]);
    }

    public function gameHistory(Request $request) {
        $user = User::find(Auth::user()->id);
        $transactions = $user->gameHistories;
        
        return Inertia::render('User/History', ['transactions' => $transactions]);
    }

    public function userWithdrawsAndDeposits(Request $request) {
        $user = User::find(Auth::user()->id);

        $transactions = DB::table('deposits')
            ->where('userId', $user->id)
            ->select('id', 'amount', 'type', 'updated_at', DB::raw("'deposit' as transaction_type"))
            ->unionAll(
                DB::table('withdraws')
                    ->where('userId', $user->id)
                    ->select('id', 'amount', 'type', 'updated_at', DB::raw("'withdraw' as transaction_type"))
            )
            ->orderByDesc('updated_at')
            ->get();


        return Inertia::render('User/Movement', ['transactions' => $transactions]);
    }
}
