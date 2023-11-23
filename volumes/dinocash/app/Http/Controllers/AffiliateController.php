<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileAffiliateUpdateRequest;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;

class AffiliateController extends Controller
{
    public function index(Request $request)
    {   
        if ($email = $request->email) {
            $affiliates = User::where('email', 'LIKE', '%' . $email . '%')
                ->where('isAffiliated', true)
                ->get();
        } else {
            $affiliates = User::where('isAffiliated', true)
            ->get();
        }

        return Inertia::render('Affiliates', [
            'affiliates' => $affiliates,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileAffiliateUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('admin.affiliate.edit', );
    }
}
