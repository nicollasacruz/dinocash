<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAffiliateMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && (Auth::user()->isAffiliated || Auth::user()->hasRole("admin"))) {
            return $next($request);
        }

        return redirect(route('homepage'));
    }
}