<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProfileAuditLog;
use App\Models\User;
use App\Models\UserLogin;
use Illuminate\Http\Request;

class SecurityLogController extends Controller
{
    public function loginHistory(Request $request)
    {
        $pageTitle = 'Login History';

        $query = UserLogin::with('user')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('ip')) {
            $query->where('user_ip', 'like', '%' . $request->ip . '%');
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $logs = $query->paginate(getPaginate());

        return view('admin.security.login_history', compact('pageTitle', 'logs'));
    }

    public function profileAudit(Request $request)
    {
        $pageTitle = 'Profile Audit Log';

        $query = ProfileAuditLog::with('user')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $logs  = $query->paginate(getPaginate());
        $actions = ProfileAuditLog::select('action')->distinct()->pluck('action');

        return view('admin.security.profile_audit', compact('pageTitle', 'logs', 'actions'));
    }
}
