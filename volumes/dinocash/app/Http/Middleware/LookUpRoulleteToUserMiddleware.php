<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Auth;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class LookUpRoulleteToUserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(Auth::check() && User::find(Auth::user()->id)->haveRoullete) {
            return Inertia::location(route('user.roleta'));
        }
        return $next($request);
    }
}
