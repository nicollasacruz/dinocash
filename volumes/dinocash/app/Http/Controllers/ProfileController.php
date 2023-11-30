<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
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

    public function listAffiliateHistory(Request $request)
    {
        $user = $request->user();

        $transactions = $user->affiliateHistories;

        return response()->json(['status' => 'success', 'transactions' => $transactions]);
    }

    public function listGameHistory(Request $request)
    {
        $user = $request->user();
        $transactions = $user->gamesHistory;

        return response()->json(['status' => 'success', 'transactions' => $transactions]);
    }

    public function listWithdraws(Request $request)
    {
        $user = $request->user();
        $withdraws = $user->withdraws;

        return response()->json(['status' => 'success', 'withdraws' => $withdraws]);
    }

    public function listDeposits(Request $request)
    {
        $user = $request->user();
        $deposits = $user->deposits;

        return response()->json(['status' => 'success', 'deposits' => $deposits]);
    }
}
