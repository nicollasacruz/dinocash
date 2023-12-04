<?php

namespace App\Observers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

class UserObserver
{
    /**
     * Handle the User "updating" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updating(User $user)
    {
        // Verifica se cpaCollected foi alterado para true
        if ($user->isDirty('cpaCollected') && $user->cpaCollected) {
            $user->cpaCollectedAt = Carbon::now();
        }

        // Verifica se affiliateId foi alterado
        if ($user->isDirty('affiliateId') && $user->affiliateId) {
            $user->affiliatedAt = Carbon::now();
        }
    }

    public function creating(User $user)
    {
        if ($invitation_link = Session::get('invitation_link')) {
            if ($invitation_link) {
                $referral = User::where('invitation_link', $invitation_link)->first();
                if ($referral && $referral->isAffiliate) {
                    $user->affiliateId = $referral->id;
                    $referral->referralsCounter += 1;
                    $referral->save();
                }
            }
        }
    }    
}
