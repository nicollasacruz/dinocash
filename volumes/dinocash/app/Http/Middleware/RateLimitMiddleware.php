<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class RateLimitMiddleware
{
    protected $limiter;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    public function handle($request, Closure $next, $key = 'default', $maxAttempts = 20, $decayMinutes = 1)
    {
        $key = $request->ip() . '|' . $key;

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            return response('Too Many Attempts.', Response::HTTP_TOO_MANY_REQUESTS);
        }

        $this->limiter->hit($key, $decayMinutes * 60);

        $response = $next($request);

        return $this->addHeaders(
            $response,
            $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts)
        );
    }

    protected function addHeaders(Response $response, $maxAttempts, $remainingAttempts)
    {
        $response->headers->add([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => $remainingAttempts,
            'X-RateLimit-Reset' => $this->limiter->availableIn('default'),
        ]);

        return $response;
    }

    protected function calculateRemainingAttempts($key, $maxAttempts)
    {
        return max(0, $maxAttempts - $this->limiter->attempts($key));
    }
}
