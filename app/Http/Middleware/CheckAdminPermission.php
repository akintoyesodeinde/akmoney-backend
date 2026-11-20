<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAdminPermission
{
    /**
     * Usage: ->middleware('admin.permission:module_name')
     * Optionally override action: ->middleware('admin.permission:module_name,create')
     *
     * When no action is given, the action is inferred from the HTTP method + URL:
     *   GET / HEAD                                    → view
     *   DELETE                                        → delete
     *   POST/PUT/PATCH with approve/reject/refund URL → approve
     *   POST with store/create URL                    → create
     *   all other POST/PUT/PATCH                      → edit
     */
    public function handle(Request $request, Closure $next, string $module, ?string $action = null): mixed
    {
        $admin = Auth::guard('admin')->user();

        if (!$admin instanceof Admin) {
            return to_route('admin.login');
        }

        if (!$admin->isActive()) {
            Auth::guard('admin')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return to_route('admin.login')->withErrors(['error' => 'Your account has been deactivated.']);
        }

        if ($admin->isSuperAdmin()) {
            return $next($request);
        }

        $resolvedAction = $action ?? $this->inferAction($request);

        if (!$admin->hasPermission($module . '.' . $resolvedAction)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden. You do not have permission to perform this action.'], 403);
            }
            $notify[] = ['error', 'You do not have permission to perform this action.'];
            return back()->withNotify($notify);
        }

        return $next($request);
    }

    private function inferAction(Request $request): string
    {
        $method = $request->method();

        if (in_array($method, ['GET', 'HEAD'])) {
            return 'view';
        }

        if ($method === 'DELETE') {
            return 'delete';
        }

        // POST / PUT / PATCH — check URL and route name for approve-type actions
        $path      = $request->path();
        $routeName = $request->route()?->getName() ?? '';

        $approveTerms = ['approve', 'reject', 'refund', 'pay-receiver', 'mark'];
        foreach ($approveTerms as $term) {
            if (str_contains($path, $term) || str_contains($routeName, $term)) {
                return 'approve';
            }
        }

        $createTerms = ['store', 'create', 'add'];
        foreach ($createTerms as $term) {
            if (str_ends_with(rtrim($path, '/'), $term) || str_contains($routeName, $term)) {
                return 'create';
            }
        }

        return 'edit';
    }
}
