<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RecaptchaLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RecaptchaLogController extends Controller
{
    public function index(Request $request)
    {
        $pageTitle = 'reCAPTCHA Logs';

        $query = RecaptchaLog::latest();

        if ($request->filled('status')) {
            $query->where('success', $request->status === 'success');
        }

        if ($request->filled('ip')) {
            $query->where('ip', 'like', '%' . $request->ip . '%');
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $logs = $query->paginate(getPaginate());

        // Stats for the summary cards (last 24 h)
        $since = Carbon::now()->subDay();
        $total24h  = RecaptchaLog::where('created_at', '>=', $since)->count();
        $passed24h = RecaptchaLog::successful()->where('created_at', '>=', $since)->count();
        $stats = [
            'total_24h'   => $total24h,
            'passed_24h'  => $passed24h,
            'failed_24h'  => RecaptchaLog::failed()->where('created_at', '>=', $since)->count(),
            'pass_rate'   => $total24h > 0 ? round($passed24h / $total24h * 100, 1) : null,
        ];

        // Hourly breakdown for the last 24 h (for chart)
        $hourly = RecaptchaLog::where('created_at', '>=', $since)
            ->selectRaw('HOUR(created_at) as hour, success, COUNT(*) as total')
            ->groupBy('hour', 'success')
            ->orderBy('hour')
            ->get()
            ->groupBy('hour');

        return view('admin.recaptcha.index', compact('pageTitle', 'logs', 'stats', 'hourly'));
    }

    public function destroy($id)
    {
        RecaptchaLog::findOrFail($id)->delete();
        $notify[] = ['success', 'Log entry deleted'];
        return back()->withNotify($notify);
    }

    public function flush()
    {
        RecaptchaLog::truncate();
        $notify[] = ['success', 'All reCAPTCHA logs cleared'];
        return back()->withNotify($notify);
    }
}
