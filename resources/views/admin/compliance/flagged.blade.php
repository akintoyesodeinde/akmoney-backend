@extends('admin.layouts.app')
@section('panel')

{{-- Filters --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Risk Level</label>
                <select name="risk_level" class="form-select">
                    <option value="">All Levels</option>
                    <option value="critical" @selected(request('risk_level') == 'critical')>Critical</option>
                    <option value="high" @selected(request('risk_level') == 'high')>High</option>
                    <option value="medium" @selected(request('risk_level') == 'medium')>Medium</option>
                    <option value="low" @selected(request('risk_level') == 'low')>Low</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="reviewed" class="form-select">
                    <option value="">All</option>
                    <option value="0" @selected(request('reviewed') === '0')>Unreviewed</option>
                    <option value="1" @selected(request('reviewed') === '1')>Reviewed</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Transaction Type</label>
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    <option value="send_money" @selected(request('type') == 'send_money')>Send Money</option>
                    <option value="deposit" @selected(request('type') == 'deposit')>Deposit</option>
                    <option value="withdrawal" @selected(request('type') == 'withdrawal')>Withdrawal</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn--primary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header"><h5 class="mb-0">Flagged Transactions</h5></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table--light style--two mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Type</th>
                        <th>Score</th>
                        <th>Level</th>
                        <th>Risk Factors</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($flags as $flag)
                    <tr>
                        <td>{{ $flag->id }}</td>
                        <td>
                            <a href="{{ route('admin.users.detail', $flag->user_id) }}">
                                {{ optional($flag->user)->fullname }}<br>
                                <small class="text-muted">{{ optional($flag->user)->email }}</small>
                            </a>
                        </td>
                        <td>{{ str_replace('_', ' ', ucfirst($flag->transaction_type ?? '-')) }}</td>
                        <td><strong>{{ $flag->risk_score }}/100</strong></td>
                        <td>{!! $flag->risk_level_badge !!}</td>
                        <td>
                            @if($flag->risk_factors)
                                <ul class="mb-0 ps-3" style="font-size:0.8rem;">
                                    @foreach($flag->risk_factors as $factor)
                                        <li>{{ $factor }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($flag->is_reviewed)
                                <span class="badge badge--success">Reviewed</span>
                            @else
                                <span class="badge badge--warning">Pending</span>
                            @endif
                        </td>
                        <td>{{ $flag->created_at->format('M d, Y H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.compliance.flagged.review', $flag->id) }}" class="btn btn-sm btn--primary">Review</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center py-4">No flagged transactions found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($flags->hasPages())
    <div class="card-footer">
        {{ $flags->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
