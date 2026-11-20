@extends('admin.layouts.app')
@section('panel')
<div class="row">

    {{-- Stats Cards --}}
    <div class="col-12 mb-4">
        <div class="row g-3">
            <div class="col-sm-6 col-lg-2">
                <div class="card border-0 bg-danger text-white text-center p-3">
                    <h2 class="mb-0">{{ $stats['critical'] }}</h2>
                    <small>Critical (Unreviewed)</small>
                </div>
            </div>
            <div class="col-sm-6 col-lg-2">
                <div class="card border-0 bg-warning text-dark text-center p-3">
                    <h2 class="mb-0">{{ $stats['high'] }}</h2>
                    <small>High (Unreviewed)</small>
                </div>
            </div>
            <div class="col-sm-6 col-lg-2">
                <div class="card border-0 bg-info text-white text-center p-3">
                    <h2 class="mb-0">{{ $stats['medium'] }}</h2>
                    <small>Medium (Unreviewed)</small>
                </div>
            </div>
            <div class="col-sm-6 col-lg-2">
                <div class="card border-0 bg-success text-white text-center p-3">
                    <h2 class="mb-0">{{ $stats['reviewed'] }}</h2>
                    <small>Reviewed</small>
                </div>
            </div>
            <div class="col-sm-6 col-lg-2">
                <div class="card border-0 bg-secondary text-white text-center p-3">
                    <h2 class="mb-0">{{ $stats['total'] }}</h2>
                    <small>Total Scored</small>
                </div>
            </div>
            <div class="col-sm-6 col-lg-2">
                <div class="card border-0 bg-dark text-white text-center p-3">
                    <h2 class="mb-0">{{ $stats['sanctions'] }}</h2>
                    <small>Sanctions Hits</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Flagged --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent High-Risk Transactions</h5>
                <a href="{{ route('admin.compliance.flagged') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table--light style--two mb-0">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Type</th>
                                <th>Score</th>
                                <th>Level</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentFlags as $flag)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.users.detail', $flag->user_id) }}">
                                        {{ optional($flag->user)->fullname }}
                                    </a>
                                </td>
                                <td>{{ str_replace('_', ' ', ucfirst($flag->transaction_type)) }}</td>
                                <td><strong>{{ $flag->risk_score }}/100</strong></td>
                                <td>{!! $flag->risk_level_badge !!}</td>
                                <td>{{ $flag->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.compliance.flagged.review', $flag->id) }}" class="btn btn-sm btn--primary">Review</a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center py-4">No flagged transactions</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Sanctions Hits --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Sanctions Hits</h5>
                <a href="{{ route('admin.compliance.sanctions') }}" class="btn btn-sm btn-outline-danger">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table--light style--two mb-0">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Type</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sanctionHits as $hit)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.users.detail', $hit->user_id) }}">
                                        {{ optional($hit->user)->fullname }}
                                    </a>
                                </td>
                                <td>{{ strtoupper($hit->screening_type) }}</td>
                                <td>{{ $hit->screened_at->format('M d') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center py-4">No sanctions hits</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Audit Export --}}
        <div class="card mt-4">
            <div class="card-header"><h5 class="mb-0">Export Audit Report</h5></div>
            <div class="card-body">
                <form action="{{ route('admin.compliance.audit.export') }}" method="GET">
                    <div class="mb-3">
                        <label class="form-label">From Date</label>
                        <input type="date" name="from" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">To Date</label>
                        <input type="date" name="to" class="form-control">
                    </div>
                    <button type="submit" class="btn btn--primary w-100">
                        <i class="la la-download"></i> Download CSV
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
