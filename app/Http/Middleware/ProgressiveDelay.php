<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProgressiveDelay
{
    // Seconds to sleep per failed attempt (capped at MAX_DELAY)
    const DELAY_PER_ATTEMPT = 2;
    const MAX_DELAY          = 30;
    const DECAY_MINUTES      = 10;

    public function handle(Request $request, Closure $next)
    {
        $key    = 'delay:' . $request->ip();
        $fails  = (int) Cache::get($key, 0);

        if ($fails > 0) {
            $sleep = min($fails * self::DELAY_PER_ATTEMPT, self::MAX_DELAY);
            sleep($sleep);
        }

        $response = $next($request);

        // On a successful response (redirect to dashboard / 2xx non-redirect),
        // clear the delay counter. On any redirect back (failed login = 302 back),
        // increment it.
        $status = $response->getStatusCode();
        $location = $response->headers->get('Location', '');
        $isFailedAttempt = $status === 302 && str_contains($location, $request->getPathInfo());

        if ($isFailedAttempt) {
            Cache::put($key, $fails + 1, now()->addMinutes(self::DECAY_MINUTES));
        } else {
            Cache::forget($key);
        }

        return $response;
    }
}
