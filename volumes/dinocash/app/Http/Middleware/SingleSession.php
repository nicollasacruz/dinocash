<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;
use Closure;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\DB;

class SingleSession extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {
        $user = auth()->user();

        $activeSessionsCount = $user->sessions()->where('last_activity', '>', now()->subMinutes(config('auth.session_lifetime')))->count();

        if ($activeSessionsCount >= 1) {
            // Allow only one concurrent session
            return redirect()->route('logout'); // Redirect to logout or another route
        }

        return $next($request);
    }

    protected function getUserSessions($user)
    {
        // Get all sessions for the user
        $allSessions = session()->all();
        $userSessions = [];

        foreach ($allSessions as $sessionId => $sessionData) {
            if ($sessionData['_token']['user_id'] == $user->id) {
                $userSessions[] = ['id' => $sessionId];
            }
        }

        return $userSessions;
    }

    protected function destroySession($sessionId)
    {
        // Destroy the session
        session()->getHandler()->destroy($sessionId);
    }
}
