<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class UpdateUserActivity
{
  public function handle($request, Closure $next)
  {
    if (Auth::check()) {
      auth()->user()->update(['last_activity' => now()]);
    }

    return $next($request);
  }
}
