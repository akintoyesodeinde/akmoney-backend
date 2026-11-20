<?php

namespace App\Http\Middleware;

use App\Constants\Status;
use App\Models\Language;
use Closure;

class EnsureTransactionPin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = auth()->user();
    
        if (! $user->pin) {
            abort(403, 'Transaction PIN not set');
        }
    
        if (! Hash::check($request->pin, $user->pin)) {
            abort(403, 'Invalid transaction PIN');
        }
    
        return $next($request);
    }
}
