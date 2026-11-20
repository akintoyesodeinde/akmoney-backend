<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSuperAdmin
{
    public function handle(Request $request, Closure $next): mixed
    {
        $admin = Auth::guard('admin')->user();

        if (!$admin instanceof Admin) {
            return to_route('admin.login');
        }

        if (!$admin->isSuperAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden. Super Admin access required.'], 403);
            }
            $notify[] = ['error', 'This section is restricted to Super Admin only.'];
            return back()->withNotify($notify);
        }

        return $next($request);
    }
}
