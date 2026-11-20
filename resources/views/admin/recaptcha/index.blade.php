@extends('admin.layouts.app')

@section('panel')

    {{-- ── Summary cards ── --}}
    <div class="row gy-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="widget-three box--shadow2 b-radius--5 bg--white">
                <div class="widget-three__icon b-radius--rounded bg--primary">
                    <i class="las la-shield-alt"></i>
                </div>
                <div class="widget-three__content">
                    <h2 class="numbers">{{ $stats['total_24h'] }}</h2>
                    <p>@lang('Total Checks (24h)')</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="widget-three box--shadow2 b-radius--5 bg--white">
                <div class="widget-three__icon b-radius--rounded bg--success">
                    <i class="las la-user-check"></i>
                </div>
                <div class="widget-three__content">
                    <h2 class="numbers">{{ $stats['passed_24h'] }}</h2>
                    <p>@lang('Passed (24h)')</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="widget-three box--shadow2 b-radius--5 bg--white">
                <div class="widget-three__icon b-radius--rounded bg--danger">
                    <i class="las la-times-circle"></i>
                </div>
                <div class="widget-three__content">
                    <h2 class="numbers">{{ $stats['failed_24h'] }}</h2>
                    <p>@lang('Failed / Blocked (24h)')</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="widget-three box--shadow2 b-radius--5 bg--white">
                <div class="widget-three__icon b-radius--rounded bg--warning">
                    <i class="las la-percentage"></i>
                </div>
                <div class="widget-three__content">
                    <h2 class="numbers">{{ $stats['pass_rate'] !== null ? $stats['pass_rate'] . '%' : '—' }}</h2>
                    <p>@lang('Pass Rate (24h)')</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Chart ── --}}
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">@lang('Hourly Activity — Last 24 Hours')</h5>
                </div>
                <div class="card-body">
                    <canvas id="recaptchaChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Pass Rate (24h)')</h5>
                </div>
                <div class="card-body d-flex flex-column justify-content-center align-items-center gap-3">
                    <div class="text-center">
                        <p class="text-muted mb-1">@lang('Challenges Passed')</p>
                        <h2 class="numbers">{{ $stats['pass_rate'] !== null ? $stats['pass_rate'] . '%' : '—' }}</h2>
                        <small class="text-muted">@lang('100% = all passed · 0% = all blocked')</small>
                    </div>
                    <div class="w-100">
                        <canvas id="passRateGauge" height="140"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Log table ── --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Time')</th>
                                    <th>@lang('IP')</th>
                                    <th>@lang('Result')</th>
                                    <th>@lang('Score')</th>
                                    <th>@lang('Error / Note')</th>
                                    <th>@lang('URL')</th>
                                    <th>@lang('Delete')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td>
                                            {{ showDateTime($log->created_at) }}<br>
                                            <small class="text-muted">{{ diffForHumans($log->created_at) }}</small>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ $log->ip }}</span>
                                        </td>
                                        <td>
                                            @if($log->success)
                                                <span class="badge badge--success">@lang('Passed')</span>
                                            @else
                                                <span class="badge badge--danger">@lang('Blocked')</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($log->score !== null)
                                                @php $s = $log->score; @endphp
                                                <span class="badge {{ $s >= 0.7 ? 'badge--success' : ($s >= 0.5 ? 'badge--warning' : 'badge--danger') }}">
                                                    {{ number_format($s, 1) }}
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $log->error_codes ?? '—' }}</small>
                                        </td>
                                        <td>
                                            <span class="small text-break" style="max-width:200px;display:inline-block;">
                                                {{ Str::limit($log->url, 60) }}
                                            </span>
                                        </td>
                                        <td>
                                            <form method="POST" action="{{ route('admin.recaptcha.log.destroy', $log->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline--danger confirmationBtn"
                                                    data-question="@lang('Are you sure to delete this log entry?')"
                                                    data-action="{{ route('admin.recaptcha.log.destroy', $log->id) }}">
                                                    <i class="la la-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">@lang('No reCAPTCHA logs found')</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($logs->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($logs) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <div class="d-flex flex-wrap gap-2 align-items-center">
        {{-- Filters --}}
        <form class="d-flex flex-wrap gap-2" method="GET">
            <select name="status" class="form-select form-select-sm" style="width:auto;">
                <option value="">@lang('All')</option>
                <option value="success" @selected(request('status') === 'success')>@lang('Passed')</option>
                <option value="failed" @selected(request('status') === 'failed')>@lang('Blocked')</option>
            </select>
            <input type="text" name="ip" class="form-control form-control-sm" placeholder="@lang('Filter by IP')" value="{{ request('ip') }}" style="width:150px;">
            <input type="date" name="date" class="form-control form-control-sm" value="{{ request('date') }}" style="width:160px;">
            <button type="submit" class="btn btn-sm btn--primary">@lang('Filter')</button>
            <a href="{{ route('admin.recaptcha.log.index') }}" class="btn btn-sm btn-outline--secondary">@lang('Reset')</a>
        </form>

        {{-- Flush button --}}
        <form method="POST" action="{{ route('admin.recaptcha.log.flush') }}" class="ms-auto">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline--danger confirmationBtn"
                data-question="@lang('Are you sure you want to delete ALL reCAPTCHA logs? This cannot be undone.')"
                data-action="{{ route('admin.recaptcha.log.flush') }}">
                <i class="la la-trash"></i> @lang('Clear All Logs')
            </button>
        </form>
    </div>
@endpush

@push('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
(function () {
    "use strict";

    // ── Hourly bar chart ──────────────────────────────────────────────
    const hourlyRaw = @json($hourly);
    const labels = Array.from({length: 24}, (_, i) => i + ':00');
    const passed = Array(24).fill(0);
    const blocked = Array(24).fill(0);

    Object.entries(hourlyRaw).forEach(([hour, entries]) => {
        entries.forEach(e => {
            if (e.success) passed[hour] = e.total;
            else blocked[hour] = e.total;
        });
    });

    new Chart(document.getElementById('recaptchaChart'), {
        type: 'bar',
        data: {
            labels,
            datasets: [
                { label: 'Passed',  data: passed,  backgroundColor: 'rgba(40,199,111,0.7)' },
                { label: 'Blocked', data: blocked, backgroundColor: 'rgba(234,84,85,0.7)' },
            ]
        },
        options: {
            responsive: true,
            scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true, ticks: { stepSize: 1 } } },
            plugins: { legend: { position: 'bottom' } }
        }
    });

    // ── Pass rate doughnut ────────────────────────────────────────────
    const passRate = {{ $stats['pass_rate'] ?? 0 }};
    const passRateFraction = passRate / 100;
    new Chart(document.getElementById('passRateGauge'), {
        type: 'doughnut',
        data: {
            labels: ['Passed', 'Blocked'],
            datasets: [{
                data: [passRateFraction, 1 - passRateFraction],
                backgroundColor: [
                    passRateFraction >= 0.7 ? 'rgba(40,199,111,0.8)' : passRateFraction >= 0.5 ? 'rgba(255,159,67,0.8)' : 'rgba(234,84,85,0.8)',
                    'rgba(200,200,200,0.2)'
                ],
                borderWidth: 0
            }]
        },
        options: {
            cutout: '75%',
            plugins: {
                legend: { display: false },
                tooltip: { enabled: false }
            }
        }
    });
})();
</script>
@endpush
