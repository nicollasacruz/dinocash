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
        $user = Auth::user();

        if ($user) {
            $currentSessionId = session()->getId();

            $userSessions = DB::table('sessions')->where('user_id', $user->id)->where('id', '!=', $currentSessionId)->get();

            foreach ($userSessions as $session) {
                if ($session->id !== $currentSessionId) {
                    $this->destroySession($session->id);
                }
            }
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
