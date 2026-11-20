@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('Date')</th>
                                    <th>@lang('TRX / MTCN')</th>
                                    <th>@lang('Event')</th>
                                    <th>@lang('Details')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td>{{ showDateTime($log->created_at) }}</td>
                                        <td>
                                            @if($log->sendMoney)
                                                <a href="{{ route('admin.send.money.details', $log->send_money_id) }}">
                                                    {{ $log->sendMoney->trx }}
                                                </a>
                                                <br><small class="text-muted">{{ $log->sendMoney->mtcn_number }}</small>
                                            @else
                                                <span class="text-muted">#{{ $log->send_money_id }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $badge = match($log->event) {
                                                    'initiated'        => 'badge--info',
                                                    'api_response'     => 'badge--primary',
                                                    'completed'        => 'badge--success',
                                                    'failed','exception' => 'badge--danger',
                                                    'webhook_received' => 'badge--warning',
                                                    default            => 'badge--dark',
                                                };
                                            @endphp
                                            <span class="badge {{ $badge }}">{{ $log->event }}</span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline--info log-detail-btn"
                                                data-payload="{{ json_encode($log->payload) }}"
                                                data-response="{{ json_encode($log->response) }}">
                                                <i class="las la-eye"></i> @lang('View')
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center" colspan="4">@lang('No logs found')</td>
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

    {{-- Log detail modal --}}
    <div class="modal fade" id="logModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Log Detail')</h5>
                    <button class="close" data-bs-dismiss="modal" type="button"><i class="las la-times"></i></button>
                </div>
                <div class="modal-body">
                    <h6>@lang('Payload Sent')</h6>
                    <pre id="logPayload" class="bg-dark text-white p-3 rounded" style="max-height:200px;overflow:auto;font-size:12px;"></pre>
                    <h6 class="mt-3">@lang('Response Received')</h6>
                    <pre id="logResponse" class="bg-dark text-white p-3 rounded" style="max-height:200px;overflow:auto;font-size:12px;"></pre>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Search by TRX / MTCN ..." />
    <a href="{{ route('admin.payout.api.settings') }}" class="btn btn-sm btn-outline--primary">
        <i class="las la-cog"></i> @lang('API Settings')
    </a>
@endpush

@push('script')
<script>
    $(document).on('click', '.log-detail-btn', function () {
        const payload  = $(this).data('payload');
        const response = $(this).data('response');
        $('#logPayload').text(JSON.stringify(payload, null, 2));
        $('#logResponse').text(JSON.stringify(response, null, 2));
        $('#logModal').modal('show');
    });
</script>
@endpush
