<?php

namespace App\Observers;

use App\Events\WalletChanged;
use App\Models\User;
use App\Notifications\PushNewInvited;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
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
            Notification::send(User::find($user->affiliateId), new PushNewInvited);
        }
    
    }
    public function updated(User $user)
    {
        try {
            if ($user->isDirty('wallet') || $user->isDirty('bonusWallet')) {
                $message = [
                    "id" => $user->id,
                    "wallet" => $user->wallet * 1,
                    "bonus" => $user->bonusWallet * 1,
                ];

                event(new WalletChanged($message));

            }
        } catch (\Exception $e) {
            Log::error('UserObserver   -   ' . $e->getMessage() . '    -   ' . $e->getFile() . '  -   '. $e->getLine());
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
