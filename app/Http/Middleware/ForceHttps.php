<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceHttps
{
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();
        $isLocalHost = in_array($host, ['localhost', '127.0.0.1', '::1'], true)
            || str_ends_with($host, '.localhost');

        if (!$isLocalHost && config('app.env') === 'production' && !$request->secure() && $request->header('X-Forwarded-Proto') !== 'https') {
            return redirect()->secure($request->getRequestUri(), 301);
        }

        return $next($request);
    }
}
