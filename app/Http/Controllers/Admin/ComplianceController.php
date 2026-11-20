<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AmlRiskScore;
use App\Models\ProfileAuditLog;
use App\Models\SanctionsScreeningResult;
use App\Models\SendMoney;
use App\Models\Deposit;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplianceController extends Controller
{
    public function dashboard()
    {
        $pageTitle = 'Compliance Dashboard';

        $stats = [
            'critical'   => AmlRiskScore::where('risk_level', 'critical')->unreviewed()->count(),
            'high'       => AmlRiskScore::where('risk_level', 'high')->unreviewed()->count(),
            'medium'     => AmlRiskScore::where('risk_level', 'medium')->unreviewed()->count(),
            'total'      => AmlRiskScore::count(),
            'reviewed'   => AmlRiskScore::where('is_reviewed', 1)->count(),
            'sanctions'  => SanctionsScreeningResult::hits()->count(),
        ];

        $recentFlags = AmlRiskScore::high()
            ->unreviewed()
            ->with('user')
            ->latest()
            ->limit(10)
            ->get();

        $sanctionHits = SanctionsScreeningResult::hits()
            ->with('user')
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.compliance.dashboard', compact('pageTitle', 'stats', 'recentFlags', 'sanctionHits'));
    }

    public function flaggedTransactions(Request $request)
    {
        $pageTitle = 'Flagged Transactions';

        $query = AmlRiskScore::with('user')->latest();

        if ($request->risk_level) {
            $query->where('risk_level', $request->risk_level);
        }
        if ($request->reviewed !== null && $request->reviewed !== '') {
            $query->where('is_reviewed', $request->reviewed);
        }
        if ($request->type) {
            $query->where('transaction_type', $request->type);
        }

        $flags = $query->paginate(25);

        return view('admin.compliance.flagged', compact('pageTitle', 'flags'));
    }

    public function reviewTransaction($id)
    {
        $pageTitle = 'Review Flagged Transaction';

        $flag = AmlRiskScore::with(['user', 'reviewer'])->findOrFail($id);

        // Load the original transaction record for context
        $transaction = null;
        if ($flag->transaction_id) {
            $transaction = match ($flag->transaction_type) {
                'send_money' => SendMoney::find($flag->transaction_id),
                'deposit'    => Deposit::find($flag->transaction_id),
                default      => null,
            };
        }

        // Recent transactions by this user
        $recentActivity = Transaction::where('user_id', $flag->user_id)
            ->latest()
            ->limit(10)
            ->get();

        // Past risk scores for this user
        $userFlags = AmlRiskScore::where('user_id', $flag->user_id)
            ->where('id', '!=', $flag->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.compliance.review', compact('pageTitle', 'flag', 'transaction', 'recentActivity', 'userFlags'));
    }

    public function markReviewed(Request $request, $id)
    {
        $request->validate(['notes' => 'nullable|string|max:2000']);

        $flag = AmlRiskScore::findOrFail($id);
        $flag->is_reviewed  = 1;
        $flag->reviewed_by  = Auth::guard('admin')->id();
        $flag->reviewed_at  = now();
        $flag->notes        = $request->notes;
        $flag->save();

        $notify[] = ['success', 'Transaction marked as reviewed'];
        return back()->withNotify($notify);
    }

    public function sanctionsHits(Request $request)
    {
        $pageTitle = 'Sanctions Screening Hits';

        $hits = SanctionsScreeningResult::hits()
            ->with('user')
            ->latest()
            ->paginate(25);

        return view('admin.compliance.sanctions', compact('pageTitle', 'hits'));
    }

    public function exportAuditReport(Request $request)
    {
        $request->validate([
            'from' => 'nullable|date',
            'to'   => 'nullable|date|after_or_equal:from',
            'user_id' => 'nullable|integer|exists:users,id',
        ]);

        $query = ProfileAuditLog::with('user')->latest();

        if ($request->from) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->to) {
            $query->whereDate('created_at', '<=', $request->to);
        }
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        $logs = $query->limit(10000)->get();

        $filename = 'audit_report_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($logs) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'User ID', 'Email', 'Action', 'Field', 'Old Value', 'New Value', 'IP Address', 'User Agent', 'Date']);

            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->id,
                    $log->user_id,
                    optional($log->user)->email,
                    $log->action,
                    $log->field,
                    $log->old_value,
                    $log->new_value,
                    $log->ip_address,
                    $log->user_agent,
                    $log->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
