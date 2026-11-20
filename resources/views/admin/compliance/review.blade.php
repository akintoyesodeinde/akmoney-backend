@extends('admin.layouts.app')
@section('panel')

<div class="row">
    <div class="col-lg-8">

        {{-- Risk Score Card --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Risk Assessment #{{ $flag->id }}</h5>
                {!! $flag->risk_level_badge !!}
            </div>
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-sm-4 text-center">
                        <div class="fs-1 fw-bold @if($flag->risk_level == 'critical') text-danger @elseif($flag->risk_level == 'high') text-warning @else text-info @endif">
                            {{ $flag->risk_score }}<span class="fs-6 text-muted">/100</span>
                        </div>
                        <small class="text-muted">Risk Score</small>
                    </div>
                    <div class="col-sm-4 text-center">
                        <div class="fs-4 fw-bold">{{ str_replace('_', ' ', ucfirst($flag->transaction_type ?? '-')) }}</div>
                        <small class="text-muted">Transaction Type</small>
                    </div>
                    <div class="col-sm-4 text-center">
                        <div class="fs-5">{{ $flag->created_at->format('M d, Y H:i') }}</div>
                        <small class="text-muted">Flagged At</small>
                    </div>
                </div>

                <h6>Triggered Risk Factors</h6>
                @if($flag->risk_factors)
                    <ul>
                        @foreach($flag->risk_factors as $factor)
                            <li>{{ $factor }}</li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">No factors recorded.</p>
                @endif

                @if($flag->is_reviewed)
                    <div class="alert alert-success mt-3">
                        <strong>Reviewed</strong> by {{ optional($flag->reviewer)->username ?? 'Admin' }}
                        on {{ $flag->reviewed_at->format('M d, Y H:i') }}
                        @if($flag->notes)
                            <br><em>Note:</em> {{ $flag->notes }}
                        @endif
                    </div>
                @else
                    <form action="{{ route('admin.compliance.flagged.mark', $flag->id) }}" method="POST" class="mt-4">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Compliance Notes (optional)</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="e.g. Verified with customer, legitimate business transaction"></textarea>
                        </div>
                        <button type="submit" class="btn btn--primary">Mark as Reviewed</button>
                        <a href="{{ route('admin.compliance.flagged') }}" class="btn btn-outline-secondary ms-2">Back</a>
                    </form>
                @endif
            </div>
        </div>

        {{-- Transaction Details --}}
        @if($transaction)
        <div class="card mb-4">
            <div class="card-header"><h6 class="mb-0">Transaction Details</h6></div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tbody>
                        @foreach($transaction->toArray() as $key => $value)
                            @if(!in_array($key, ['sender','recipient','service_form_data','kyc_data']))
                            <tr>
                                <th width="35%">{{ str_replace('_', ' ', ucfirst($key)) }}</th>
                                <td>{{ is_array($value) ? json_encode($value) : $value }}</td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    </div>

    <div class="col-lg-4">

        {{-- User Info --}}
        <div class="card mb-4">
            <div class="card-header"><h6 class="mb-0">User Profile</h6></div>
            <div class="card-body">
                @php $user = $flag->user @endphp
                <p class="mb-1"><strong>Name:</strong> {{ optional($user)->fullname }}</p>
                <p class="mb-1"><strong>Email:</strong> {{ optional($user)->email }}</p>
                <p class="mb-1"><strong>Mobile:</strong> {{ optional($user)->mobileNumber }}</p>
                <p class="mb-1"><strong>KYC Status:</strong>
                    @php $kycStatus = optional($user)->getKycStatus() @endphp
                    <span class="badge badge--{{ $kycStatus['badge'] ?? 'secondary' }}">{{ $kycStatus['label'] ?? 'Unknown' }}</span>
                </p>
                <p class="mb-1"><strong>Registered:</strong> {{ optional(optional($user)->created_at)->format('M d, Y') }}</p>
                <a href="{{ route('admin.users.detail', $flag->user_id) }}" class="btn btn-sm btn-outline-primary mt-2 w-100">View Full Profile</a>
            </div>
        </div>

        {{-- Recent Activity --}}
        <div class="card mb-4">
            <div class="card-header"><h6 class="mb-0">Recent Transactions</h6></div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th>TRX</th><th>Amount</th><th>Date</th></tr></thead>
                    <tbody>
                        @forelse($recentActivity as $tx)
                        <tr>
                            <td><small>{{ $tx->trx }}</small></td>
                            <td>{{ $tx->amount }}</td>
                            <td><small>{{ $tx->created_at->format('M d') }}</small></td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center py-2">None</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Past Flags --}}
        @if($userFlags->count())
        <div class="card">
            <div class="card-header"><h6 class="mb-0">Previous Flags</h6></div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Level</th><th>Score</th><th>Date</th></tr></thead>
                    <tbody>
                        @foreach($userFlags as $uf)
                        <tr>
                            <td>{!! $uf->risk_level_badge !!}</td>
                            <td>{{ $uf->risk_score }}</td>
                            <td><small>{{ $uf->created_at->format('M d, Y') }}</small></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
