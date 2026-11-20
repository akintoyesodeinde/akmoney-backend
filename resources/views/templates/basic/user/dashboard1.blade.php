@extends($activeTemplate . 'layouts.master')
@php
    $kycInstruction = getContent('kyc_instruction_user.content', true);
@endphp

@section('content')
<section class="dashboard-section body-collapse">
    <div class="overlay pt-120">
        <div class="container-fruid">
            <div class="row">
                <!-- Main Content Area -->
                <div class="col-xl-8 col-lg-7">
                    <!-- Dashboard Header Cards -->
                    <div class="row mb-30">
                        <div class="col-md-6">
                            <div class="dashboard-card-wu bg-primary-gradient">
                                <div class="card-icon">
                                    <i class="fas fa-wallet"></i>
                                </div>
                                <div class="card-content">
                                    <h6 class="card-label">Available Balance</h6>
                                    <h2 class="card-amount">{{showAmount(auth()->user()->balance)}}</h2>
                                    <div class="card-subtext">
                                        <i class="fas fa-clock"></i>
                                        <span>Pending: {{ showAmount($widget['payment_pending']) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="dashboard-card-wu bg-success-gradient">
                                <div class="card-icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="card-content">
                                    <h6 class="card-label">Completed Payments</h6>
                                    <h2 class="card-amount">{{ showAmount($widget['send_money_amount']) }}</h2>
                                    <div class="card-subtext">
                                        <i class="fas fa-globe-americas"></i>
                                        <span>Worldwide Transfers</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="quick-actions mb-30">
                        <div class="section-header">
                            <h5>Quick Actions</h5>
                            <p>Send money instantly worldwide</p>
                        </div>
                        <div class="action-buttons">
                            <a href="{{ route('user.send.money.now') }}" class="action-btn send-money-btn">
                                <i class="fas fa-paper-plane"></i>
                                <span>Send Money</span>
                            </a>
                            <a href="{{ route('user.transactions') }}" class="action-btn bill-pay-btn">
                                <i class="fas fa-exchange-alt"></i>
                                <span>Transactions</span></a>
                            <a href="/ticket" class="action-btn recipient-btn">
                                <i class="fas fa-headset"></i>
                                <span>Complaints</span>
                            </a>

                        </div>
                    </div>

                    <!-- Recent Transactions -->
                    <div class="transactions-area mt-40">
                        <div class="section-header">
                            <div class="header-left">
                                <h5>Recent Transactions</h5>
                                <p>Updated every several minutes</p>
                            </div>
                            <div class="header-right">
                                <a href="{{ route('user.send.money.history') }}" class="view-all-link">
                                    View All <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                        
                        <div class="transactions-table mt-30">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>@lang('MTCN')</th>
                                            <th>@lang('Amount')</th>
                                            <th>@lang('Recipient')</th>
                                            <th>@lang('Status')</th>
                                            <th>@lang('Action')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($transfers as $transfer)
                                        <tr class="transaction-row">
                                            <td>
                                                <div class="mtcn-cell">
                                                    <i class="fas fa-receipt text-primary me-2"></i>
                                                    <span class="fw-bold">{{ $transfer->mtcn_number }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="amount-cell">
                                                    <div class="from-amount">
                                                        <strong>{{ showAmount($transfer->sending_amount, currencyFormat: false) }}</strong>
                                                        <small>{{ __($transfer->sending_currency) }}</small>
                                                    </div>
                                                    <i class="fas fa-arrow-right mx-2 text-muted"></i>
                                                    <div class="to-amount">
                                                        <strong>{{ showAmount($transfer->recipient_amount, currencyFormat: false) }}</strong>
                                                        <small>{{ __($transfer->recipient_currency) }}</small>
                                                    </div>
                                                    <div class="charge-badge">
                                                        Fee: {{ showAmount($transfer->sending_charge, currencyFormat: false) }}
                                                        {{ __($transfer->sending_currency) }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="recipient-cell">
                                                    <div class="recipient-name">
                                                        <i class="fas fa-user-circle me-2"></i>
                                                        <strong>{{ $transfer->recipient->name }}</strong>
                                                    </div>
                                                    <div class="recipient-country">
                                                        <i class="fas fa-globe me-2"></i>
                                                        <span>{{ __(@$transfer->recipientCountry->name) }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="status-cell">
                                                    @if ($transfer->status == Status::SEND_MONEY_REFUNDED && $transfer->admin_feedback != null)
                                                        <button class="btn btn-sm btn-info feedback-btn"
                                                            data-admin_feedback="{{ $transfer->admin_feedback }}">
                                                            <i class="fas fa-info-circle"></i> Details
                                                        </button>
                                                    @endif
                                                    @php echo $transfer->statusBadge; @endphp
                                                </div>
                                            </td>
                                            @php
                    $details = $transfer->detail != null ? json_encode($transfer->detail) : null;
                @endphp
                    <td>
                        <button class="btn btn--base btn-sm btn-action detailBtn"
                            data-address="{{ $transfer->recipient->address }}"
                            data-country="{{ @$transfer->recipientCountry->name }}"
                            data-id="{{ encrypt($transfer->id) }}" data-info="{{ $details }}"
                            data-mobile="{{ $transfer->recipient->mobile }}"
                            data-name="{{ $transfer->recipient->name }}"
                            data-payment_status="{{ $transfer->payment_status }}"
                            data-payment_via="{{ $transfer->deposit ? @$transfer->deposit->gateway->name : __('Refunded Wallet') }}"
                            data-recipient_amount="{{ showAmount($transfer->recipient_amount, currencyFormat: false) }} {{ __($transfer->recipient_currency) }}"
                            data-send_money_amount="{{ $transfer->sending_amount }}"
                            data-sending_currency="{{ __($transfer->sending_currency) }}"
                            data-send_amount_in_base_currency="{{ showAmount($transfer->base_currency_amount + $transfer->base_currency_charge, currencyFormat: false) }}"
                            data-total_payable_amount="{{ showAmount($transfer->paidAmount(), currencyFormat: false) }}"
                            data-deposit="{{ json_encode(['charge' => optional($transfer->deposit)->charge ?? 0]) }}"
                            data-delivery_charge="{{ showAmount($transfer->sending_charge, currencyFormat: false) }}"
                            data-including_charge="{{ showAmount($transfer->sending_amount + $transfer->sending_charge, currencyFormat: false) }}"
                            data-status="{{ $transfer->status }}" data-mtcn_number="{{ $transfer->mtcn_number }}"
                            data-trx="{{ $transfer->trx }}"
                            data-conversion_rate="{{ showAmount($transfer->conversion_rate, decimal: 8, exceptZeros: true, currencyFormat: false) }}"
                            data-base_currency_rate="{{ showAmount($transfer->base_currency_rate, decimal: 8, exceptZeros: true, currencyFormat: false) }}"
                            data-recipient_currency="{{ $transfer->recipient_currency }}"
                            data-delivery_method="{{ $transfer->country_delivery_method_id ? __($transfer->countryDeliveryMethod->deliveryMethod->name) : __('Agent') }}"
                            data-sent_at="{{ showDateTime($transfer->created_at) }}">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                    </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-40">
                                                <div class="empty-state">
                                                    <i class="fas fa-exchange-alt fa-3x text-muted mb-3"></i>
                                                    <h6>No transactions yet</h6>
                                                    <p class="text-muted">Your transaction history will appear here</p>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Area -->
<div class="col-xl-4 col-lg-5">
    <div class="sidebar-widgets">

        <!-- My Recipients -->
        <div class="widget recipients-widget">
            <div class="widget-header">
                <h6>My Recipients</h6>
            </div>

            <div class="widget-body">
                <div class="recipients-list">
                    @forelse($rep as $r)
                    <div class="recipient-item">
                        <div class="recipient-avatar">
                            <i class="fas fa-user-circle"></i>
                        </div>

                        <div class="recipient-info">
                            <h6 class="recipient-name">{{ $r->name }}</h6>
                            <p class="recipient-date">
                                <i class="far fa-clock me-1"></i>
                                {{ $r->created_at->diffForHumans() }}
                            </p>
                        </div>

                        <!-- Quick Transfer -->
                        <a href="{{ route('user.send.money.now') }}?recipient={{ $r->id }}"
                           class="recipient-action"
                           title="Quick Transfer">
                            <i class="fas fa-paper-plane"></i>
                        </a>
                    </div>
                    @empty
                    <div class="empty-recipients text-center py-20">
                        <i class="fas fa-user-friends fa-2x text-muted mb-3"></i>
                        <p>No recipients yet</p>
                        <a href="{{ route('user.send.money.now') }}"
                           class="btn btn-sm btn-outline-primary">
                            Send Money to Add Recipient
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>


                        <!-- Exchange Rates -->
                        <div class="widget rates-widget mt-30">
                            <div class="widget-header">
                                <h6>Exchange Rates</h6>
                                <span class="badge bg-success">Live</span>
                            </div>
                            <div class="widget-body" id="rates-container">
                                <!-- Rates will be inserted here -->
                            </div>
                        </div>

                        <!-- Promo Banner -->
<div class="promo-banner mt-30" style="background-color: #1a3a8f;">
    <div class="banner-content">
        <h6 style="color: #ffffff;">Send Money with Zero Fees*</h6>
        <p style="color: #e0e7ff;">Limited time offer for new transfers</p>
        <a href="{{ route('user.send.money.now') }}" class="btn btn-light btn-sm" style="color: #1a3a8f; background-color: #ffffff; border-color: #ffffff;">
            Get Started <i class="fas fa-arrow-right ms-2"></i>
        </a>
    </div>
    <div class="banner-icon">
        <i class="fas fa-gift" style="color: #ffffff;"></i>
    </div>
</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Details MODAL --}}
    <div class="modal custom--modal fade" id="detailsModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Details')</h5>
                    <button aria-label="Close" class="close btn btn--danger btn-sm close-button" data-bs-dismiss="modal"
                        type="button">
                        <i aria-hidden="true" class="la la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="p-1 d-flex flex-column">
                                <small class="text-muted"> <i class="la la-user"></i> @lang('Recipient\'s Name')</small>
                                <h6 class="name"></h6>
                            </div>
                            <div class="p-1 d-flex flex-column">
                                <small class="text-muted"> <i class="la la-mobile"></i> @lang('Recipient\'s Mobile No.')</small>
                                <h6 class="mobile"></h6>
                            </div>

                            <div class="p-1 d-flex flex-column">
                                <small class="text-muted"><i class="la la-map-marker"></i> @lang('Recipient\'s Address')</small>
                                <h6 class="address"></h6>
                            </div>
                            <div class="p-1 d-flex flex-column">
                                <small class="text-muted"> <i class="la la-globe"></i> @lang('Recipient\'s Country')</small>
                                <h6 class="country"></h6>
                            </div>
                            <div class="p-1 d-flex flex-column">
                                <small class="text-muted"><i class="las la-braille"></i> @lang('MTCN')</small>
                                <h6 class="mtcn_number"></h6>
                            </div>
                            <div class="p-1 d-flex flex-column">
                                <small class="text-muted"><i class="las la-random"></i> @lang('Transaction Number')</small>
                                <h6 class="trx"></h6>
                            </div>
                            <div class="p-1 d-flex flex-column">
                                <small class="text-muted"><i class="las la-money-check-alt"></i> @lang('Delivery Method')</small>
                                <h6 class="delivery_method"></h6>
                            </div>
                            <div class="p-1 d-flex flex-column">
                                <small class="text-muted"><i class="las la-money-check-alt"></i> @lang('Payment Via')</small>
                                <h6 class="payment_via"></h6>
                            </div>
                            <div class="p-1 d-flex flex-column">
                                <small class="text-muted"><i class="las la-clock"></i> @lang('Sent At')</small>
                                <h6 class="sent_at"></h6>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="p-1 d-flex flex-column">
                                <small class="text-muted"><i class="las la-file-invoice-dollar"></i>
                                    @lang('Recipient will Get')</small>
                                <h6 class="receivable_amount"></h6>
                            </div>
                            <div class="p-1 d-flex flex-column">
                                <small class="text-muted"><i class="las la-exchange-alt"></i> @lang('Conversion Rate')</small>
                                <h6>1 <span class="sending_currency"></span> = <span class="conversion_rate"></span></h6>
                            </div>
                            <div class="p-1 d-flex flex-column">
                                <small class="text-muted"><i class="las la-hand-holding-usd"></i> @lang('Sent Amount')</small>
                                <h6 class="send_money_amount"></h6>
                            </div>
                            <div class="p-1 d-flex flex-column">
                                <small class="text-muted"><i class="las la-money-bill"></i> @lang('Delivery Method Charge')</small>
                                <h6 class="delivery_charge"></h6>
                            </div>
                            <div class="p-1 d-flex flex-column">
                                <small class="text-muted"><i class="las la-money-bill"></i> @lang('Sent Amount Including Charge')</small>
                                <h6 class="including_charge"></h6>
                            </div>
                            <div class="p-1 d-flex flex-column">
                                <small class="text-muted"><i class="las la-exchange-alt"></i> @lang('Conversion Rate')</small>
                                <h6>{{ showAmount(1) }} = <span class="base_currency_rate"></span></h6>
                            </div>
                            <div class="p-1 d-flex flex-column">
                                <small class="text-muted"><i class="las la-money-bill"></i> @lang('Sent Amount Including Charge In')
                                    {{ __(gs('cur_text')) }}</small>
                                <h6 class="send_amount_in_base_currency"></h6>
                            </div>
                            <div class="p-1 d-flex flex-column">
                                <small class="text-muted"><i class="las la-money-bill"></i> @lang('Gateway Charge')</small>
                                <h6 class="gateway_charge"></h6>
                            </div>
                            <div class="p-1 d-flex flex-column">
                                <small class="text-muted"><i class="las la-money-bill"></i> @lang('Total Payable Amount')</small>
                                <h6 class="total_payable_amount"></h6>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <form action="{{ route('user.send.money.pay') }}" class="w-100 disableSubmission" method="POST">
                        @csrf
                        <input name="id" type="hidden">
                        <button class="btn btn--base w-100 btn--xl">
                            @lang('Pay Now')
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- feedback MODAL --}}
    <div class="modal custom--modal fade" id="feedbackModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Feedback')</h5>
                    <button aria-label="Close" class="close btn btn--danger btn-sm close-button" data-bs-dismiss="modal"
                        type="button">
                        <i aria-hidden="true" class="la la-times"></i>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <span class="admin_feedback"></span>
                </div>
            </div>
        </div>
    </div>

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.detailBtn').on('click', function() {
                var modal = $('#detailsModal');
                var data = $(this).data();

                modal.find('.name').text(data.name);
                modal.find('.mobile').text('+' + data.mobile);
                modal.find('.country').text(data.country);
                modal.find('.address').text(data.address);
                modal.find('.payment_via').text(data.payment_via);
                modal.find('.send_money_amount').text(parseFloat(data.send_money_amount).toFixed(2) + ' ' + data
                    .sending_currency);
                modal.find('.including_charge').text(data.including_charge + ' ' + data.sending_currency);
                modal.find('.conversion_rate').text(data.conversion_rate + ' ' + data.recipient_currency);
                modal.find('.base_currency_rate').text(data.base_currency_rate + ' ' + data.sending_currency);
                modal.find('.sending_currency').text(data.sending_currency);
                modal.find('.send_amount_in_base_currency').text(data.send_amount_in_base_currency +
                    " {{ __(gs('cur_text')) }}");
                modal.find('.receivable_amount').text(data.recipient_amount);
                modal.find('.mtcn_number').text('#' + data.mtcn_number);
                modal.find('.trx').text('#' + data.trx);
                modal.find('.delivery_charge').text(data.delivery_charge + ' ' + data.sending_currency);
                modal.find('.total_payable_amount').text(data.total_payable_amount + " {{ __(gs('cur_text')) }}");
                modal.find('.delivery_method').text(data.delivery_method);
                modal.find('.sent_at').text(data.sent_at);

                modal.find('.gateway_charge').text(parseFloat(data.deposit.charge ?? 0).toFixed(2) +
                    " {{ __(gs('cur_text')) }}");

                if (data.status == 0 && (data.payment_status == 0 || data.payment_status == 3)) {
                    modal.find('.modal-footer form [name=id]').val(data.id);
                    modal.find('.modal-footer form :submit').removeAttr('disabled');
                    modal.find('.modal-footer').show();
                } else {
                    modal.find('.modal-footer form [name=id]').val('');
                    modal.find('.modal-footer').hide();
                }
                modal.modal('show');
            });

            $('.feedbackBtn').on('click', function() {
                var modal = $('#feedbackModal');
                var data = $(this).data();
                modal.find('.admin_feedback').text(data.admin_feedback);
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush

<!-- Modals (Keep existing modals with enhanced styling) -->
<div class="card-popup">
    <!-- ... existing modal code ... -->
</div>

<div class="add-card">
    <!-- ... existing modal code ... -->
</div>

<div class="transactions-popup">
    <!-- ... existing modal code ... -->
</div>

<!-- KYC Notification -->
@if (auth()->user()->kv != 1)
<div class="kyc-notification">
    @if (auth()->user()->kv == Status::KYC_REJECTED)
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close">
            </button>
            <div class="alert-content">
                <i class="fas fa-exclamation-circle me-3"></i>
                <div>
                    <h6>KYC Documents Rejected</h6>
                    <p>{{ __(@$kycInstruction->data_values->reject_instruction) }}</p>
                    <div class="mt-2">
                        <a href="javascript:void(0)" class="btn btn-sm btn-outline-light me-2" data-bs-toggle="modal"
                            data-bs-target="#kycRejectionReason">
                            View Reason
                        </a>
                        <a href="{{ route('user.kyc.form') }}" class="btn btn-sm btn-light">
                            Re-submit Documents
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @elseif (auth()->user()->kv == Status::KYC_UNVERIFIED)
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close">
            </button>
            <div class="alert-content">
                <i class="fas fa-info-circle me-3"></i>
                <div>
                    <h6>KYC Verification Required</h6>
                    <p>{{ __(@$kycInstruction->data_values->verification_instruction) }}</p>
                    <a href="{{ route('user.kyc.form') }}" class="btn btn-sm btn-light mt-2">
                        Start Verification
                    </a>
                </div>
            </div>
        </div>
    @elseif(auth()->user()->kv == Status::KYC_PENDING)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close">
            </button>
            <div class="alert-content">
                <i class="fas fa-clock me-3"></i>
                <div>
                    <h6>KYC Verification Pending</h6>
                    <p>{{ __(@$kycInstruction->data_values->pending_instruction) }}</p>
                    <a href="{{ route('user.kyc.data') }}" class="btn btn-sm btn-light mt-2">
                        View Submitted Info
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endif

<!-- Existing Modals -->
@if (auth()->user()->kv == Status::KYC_REJECTED)
    <div class="modal fade" id="kycRejectionReason">
        <!-- ... existing modal code ... -->
    </div>
@endif

<!-- feedback MODAL -->
<div class="modal custom--modal fade" id="feedbackModal">
    <!-- ... existing modal code ... -->
</div>
@endsection

@push('script')
<script>
    (function($) {
        "use strict";
        $('.feedbackBtn').on('click', function() {
            var modal = $('#feedbackModal');
            modal.find('.admin_feedback').text($(this).data('admin_feedback'));
            modal.modal('show');
        });
    })(jQuery);
    
    const countries = {!! json_encode($countries) !!};
    const to = {!! json_encode($tocountry) !!};

    function fetchRates() {
    
        $('#rates-container').empty();

        countries.forEach(toCurrency => {

                const html = `
                    <div class="rate-item d-flex justify-content-between align-items-center">
                        <div class="currency-pair d-flex align-items-center gap-2">
                           

                            <span>USD</span>
                            <i class="fas fa-arrow-right mx-2"></i>

                            <img src="${toCurrency.image_url}"
                                 alt="${toCurrency.currency}"
                                 class="currency-flag">

                            <span>${toCurrency.currency}</span>
                        </div>

                        <div class="rate-value">
                            ${toCurrency.rate}
                        </div>
                    </div>
                `;

                $('#rates-container').append(html);
        });
    }

    
    // Initial fetch
    fetchRates();
    
    // Refresh every 30 seconds
    setInterval(fetchRates, 30000);
</script>
@endpush

@push('style-lib')
<link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;500;600&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@push('style')
<style>
    /* Enhanced Dashboard Styles - FinTech Theme */
    :root {
        --primary-blue: #0054A4;
        --primary-dark: #003A75;
        --secondary-gold: #FFD100;
        --accent-teal: #00B0B9;
        --accent-orange: #F47B20;
        --light-bg: #F8FAFC;
        --dark-text: #1E293B;
        --medium-text: #64748B;
        --light-text: #94A3B8;
        --success-color: #10B981;
        --warning-color: #F59E0B;
        --danger-color: #EF4444;
        --info-color: #3B82F6;
        --border-color: #E2E8F0;
        --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --hover-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    body {
        font-family: 'Poppins', sans-serif;
        background: var(--light-bg);
        color: var(--dark-text);
        overflow-x: hidden;
    }

    .dashboard-section {
        background: transparent;
        min-height: calc(100vh - 80px);
    }

    .overlay {
        padding-top: 30px !important;
    }

    /* Responsive Container */
    .container-fruid {
        padding-left: 20px;
        padding-right: 20px;
    }

    @media (min-width: 1400px) {
        .container-fruid {
            max-width: 1400px;
            margin: 0 auto;
        }
    }

    /* Dashboard Cards - Enhanced FinTech Style */
    .dashboard-card-wu {
        border-radius: 20px;
        padding: 30px;
        color: white;
        box-shadow: var(--card-shadow);
        border: none;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
        height: 100%;
        min-height: 200px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .dashboard-card-wu::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, var(--secondary-gold) 0%, #FFE566 100%);
    }

    .dashboard-card-wu::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 150px;
        height: 150px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }

    .dashboard-card-wu:hover {
        transform: translateY(-8px);
        box-shadow: var(--hover-shadow);
    }

    .dashboard-card-wu.bg-primary-gradient {
        background: linear-gradient(135deg, var(--primary-blue) 0%, var(--accent-teal) 100%);
    }

    .dashboard-card-wu.bg-success-gradient {
        background: linear-gradient(135deg, var(--accent-orange) 0%, #FF9E4A 100%);
    }

    .card-icon {
        font-size: 40px;
        margin-bottom: 20px;
        opacity: 0.9;
        position: relative;
        z-index: 1;
    }

    .card-label {
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 1px;
        opacity: 0.9;
        margin-bottom: 8px;
        font-weight: 600;
        position: relative;
        z-index: 1;
    }

    .card-amount {
        font-family: 'Rajdhani', sans-serif;
        font-weight: 700;
        font-size: 38px;
        margin-bottom: 15px;
        position: relative;
        z-index: 1;
        letter-spacing: 1px;
    }

    .card-subtext {
        font-size: 14px;
        opacity: 0.9;
        display: flex;
        align-items: center;
        gap: 8px;
        position: relative;
        z-index: 1;
    }

    /* Quick Actions - Modern FinTech */
    .quick-actions {
        background: white;
        border-radius: 20px;
        padding: 30px;
        box-shadow: var(--card-shadow);
        border: 1px solid var(--border-color);
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .section-header h5 {
        font-family: 'Rajdhani', sans-serif;
        font-weight: 700;
        color: var(--primary-dark);
        margin: 0;
        font-size: 22px;
    }

    .section-header p {
        color: var(--medium-text);
        font-size: 15px;
        margin: 8px 0 0 0;
        flex-basis: 100%;
    }

    .action-buttons {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 15px;
        width: 100%;
    }

    .action-btn {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        padding: 20px;
        border-radius: 16px;
        text-decoration: none;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        position: relative;
        overflow: hidden;
        min-height: 80px;
    }

    .action-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(rgba(0,0,0,0.1), rgba(0,0,0,0));
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .action-btn:hover::before {
        opacity: 1;
    }

    .action-btn i:first-child {
        margin-right: 15px;
        font-size: 22px;
        transition: transform 0.3s ease;
    }

    .action-btn:hover i:first-child {
        transform: scale(1.1);
    }

    .send-money-btn {
        background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-dark) 100%);
    }

    .bill-pay-btn {
        background: linear-gradient(135deg, var(--accent-teal) 0%, #00C4CF 100%);
    }

    .recipient-btn {
        background: linear-gradient(135deg, var(--accent-orange) 0%, #FF8C42 100%);
    }

    .action-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0, 84, 164, 0.2);
        color: white;
    }

    /* Recent Transactions - Enhanced */
    .transactions-area {
        background: white;
        border-radius: 20px;
        padding: 30px;
        box-shadow: var(--card-shadow);
        border: 1px solid var(--border-color);
        margin-top: 30px;
    }

    .transactions-table {
        margin-top: 20px !important;
    }

    .transactions-table table {
        margin: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    .transactions-table thead th {
        background: linear-gradient(to bottom, #F1F5F9, #E2E8F0);
        border: none;
        padding: 20px 15px;
        font-weight: 700;
        color: var(--primary-dark);
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.8px;
        border-bottom: 2px solid var(--border-color);
        white-space: nowrap;
    }

    .transactions-table tbody td {
        padding: 25px 15px;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
        transition: background-color 0.3s ease;
    }

    .transactions-table tbody tr:last-child td {
        border-bottom: none;
    }

    .transaction-row:hover td {
        background-color: #F8FAFC;
    }
  
    /* Card-like container */
    .transactions-table {
        height: 380px;          /* adjust as needed */
        display: flex;
        flex-direction: column;
    }
    
    /* This is the scroll zone */
    .transactions-table .table-responsive {
        flex: 1;
        overflow-y: auto;
    }
    
    /* Keep table header visible while scrolling */
    .transactions-table thead th {
        position: sticky;
        top: 0;
        background: #fff;      /* match your theme */
        z-index: 2;
    }
    
    /* Optional scrollbar polish */
    .transactions-table .table-responsive::-webkit-scrollbar {
        width: 6px;
    }
    
    .transactions-table .table-responsive::-webkit-scrollbar-track {
        background: transparent;
    }
    
    .transactions-table .table-responsive::-webkit-scrollbar-thumb {
        background: rgba(255, 55, 6, 0.3);
        border-radius: 4px;
    }

    .mtcn-cell {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .mtcn-cell i {
        color: var(--primary-blue);
        font-size: 18px;
    }

    .amount-cell {
        display: flex;
        flex-direction: column;
        gap: 8px;
        position: relative;
    }

    .amount-row {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .from-amount, .to-amount {
        display: flex;
        align-items: baseline;
        gap: 5px;
    }

    .from-amount strong, .to-amount strong {
        font-family: 'Rajdhani', sans-serif;
        font-weight: 700;
        font-size: 16px;
        color: var(--dark-text);
    }

    .from-amount small, .to-amount small {
        font-size: 12px;
        color: var(--medium-text);
        text-transform: uppercase;
    }

    .fa-arrow-right {
        color: var(--accent-teal);
    }

    .charge-badge {
        background: linear-gradient(135deg, #FEF3C7, #FDE68A);
        color: #92400E;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        width: fit-content;
    }

    .recipient-cell {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .recipient-name {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .recipient-name i {
        color: var(--accent-orange);
    }

    .recipient-name strong {
        font-weight: 600;
    }

    .recipient-country {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: var(--medium-text);
    }

    .recipient-country i {
        color: var(--accent-teal);
    }

    .status-cell {
        display: flex;
        flex-direction: column;
        gap: 8px;
        align-items: flex-start;
    }

    .status-cell .badge {
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: none;
    }

    .btn-action {
        background: white;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 10px 15px;
        color: var(--medium-text);
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
    }

    .btn-action:hover {
        background: var(--primary-blue);
        border-color: var(--primary-blue);
        color: white;
        transform: rotate(90deg);
    }

    .dropdown-menu {
        border-radius: 12px;
        border: 1px solid var(--border-color);
        box-shadow: var(--hover-shadow);
        padding: 10px;
    }

    .dropdown-item {
        padding: 10px 15px;
        border-radius: 8px;
        color: var(--dark-text);
        font-weight: 500;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .dropdown-item:hover {
        background: var(--light-bg);
        color: var(--primary-blue);
    }

    .empty-state {
        padding: 60px 20px;
        text-align: center;
    }

    .empty-state i {
        color: var(--light-text);
        margin-bottom: 20px;
    }

    .empty-state h6 {
        color: var(--dark-text);
        font-weight: 600;
        margin-bottom: 10px;
    }

    /* Sidebar Widgets - Enhanced */
    .sidebar-widgets {
        position: sticky;
        /*top: 100px;*/
    }

    .sidebar-widgets .widget {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: var(--card-shadow);
        border: 1px solid var(--border-color);
        margin-bottom: 25px;
        transition: transform 0.3s ease;
    }

    .widget:hover {
        transform: translateY(-5px);
    }

    .widget-header {
        background: linear-gradient(to right, #F8FAFC, #F1F5F9);
        padding: 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid var(--border-color);
    }

    .widget-header h6 {
        font-family: 'Rajdhani', sans-serif;
        font-weight: 700;
        color: var(--primary-dark);
        margin: 0;
        font-size: 18px;
    }

    .btn-link {
        color: var(--primary-blue);
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .btn-link:hover {
        color: var(--primary-dark);
        text-decoration: underline;
    }

    .widget-body {
        padding: 25px;
    }

    /* Recipients Widget */
    .recipients-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .recipient-item {
        display: flex;
        align-items: center;
        padding: 20px;
        border-radius: 16px;
        background: var(--light-bg);
        transition: all 0.3s ease;
        border: 1px solid transparent;
    }

    .recipient-item:hover {
        background: white;
        border-color: var(--border-color);
        transform: translateX(5px);
    }

    .recipient-avatar {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, var(--primary-blue) 0%, var(--accent-teal) 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 22px;
        margin-right: 20px;
        flex-shrink: 0;
    }

    .recipient-info {
        flex: 1;
        min-width: 0;
    }

    .recipient-name {
        font-weight: 600;
        color: var(--dark-text);
        margin: 0 0 8px 0;
        font-size: 16px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .recipient-date {
        font-size: 13px;
        color: var(--medium-text);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .recipient-action {
        background: white;
        border: 1px solid var(--border-color);
        width: 44px;
        height: 44px;
        border-radius: 12px;
        color: var(--primary-blue);
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-left: 15px;
    }

    .recipient-action:hover {
        background: var(--primary-blue);
        border-color: var(--primary-blue);
        color: white;
        transform: rotate(15deg);
    }

    .empty-recipients {
        padding: 40px 20px;
        text-align: center;
    }

    .empty-recipients i {
        color: var(--light-text);
        margin-bottom: 20px;
    }

    .empty-recipients p {
        color: var(--medium-text);
        margin-bottom: 20px;
    }
    .recipients-widget {
        height: 320px;                 /* adjust to taste */
        display: flex;
        flex-direction: column;
    }
    .recipients-widget .widget-body {
        flex: 1;
        overflow-y: auto;
        padding-right: 6px;
    }
    
    /* Optional scrollbar styling */
    .recipients-widget .widget-body::-webkit-scrollbar {
        width: 6px;
    }
    
    .recipients-widget .widget-body::-webkit-scrollbar-thumb {
        background: rgba(255, 55, 6, 0.3);
        border-radius: 4px;
    }
    
    .recipients-widget .widget-body::-webkit-scrollbar-track {
        background: transparent;
    }
    .rates-widget {
        height: 350px;              /* choose your height */
        display: flex;
        flex-direction: column;
    }

    /* Exchange Rates Widget */
    .rates-widget .widget-body {
        padding: 0;
        flex: 1;
        overflow-y: auto;
        padding-right: 6px;
    }

    .rate-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 25px;
        border-bottom: 1px solid var(--border-color);
        transition: background-color 0.3s ease;
    }

    .rate-item:last-child {
        border-bottom: none;
    }

    .rate-item:hover {
        background-color: #F8FAFC;
    }

    .currency-pair {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .currency-flag {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        position: relative;
        overflow: hidden;
    }

    .currency-flag::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: inherit;
        filter: brightness(0.9);
    }

   .rates-widget .widget-body::-webkit-scrollbar {
        width: 6px;
    }
    
    .rates-widget .widget-body::-webkit-scrollbar-thumb {
        background: rgba(255, 55, 6, 0.3);
        border-radius: 4px;
    }
    
    .rates-widget .widget-body::-webkit-scrollbar-track {
        background: transparent;
    }

    .rate-value {
        font-family: 'Rajdhani', sans-serif;
        font-weight: 700;
        font-size: 22px;
        color: var(--dark-text);
        background: linear-gradient(135deg, #F0F9FF, #E0F2FE);
        padding: 8px 16px;
        border-radius: 12px;
        min-width: 80px;
        text-align: center;
    }

    /* Promo Banner */
    .promo-banner {
        background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-blue) 100%);
        border-radius: 20px;
        padding: 30px;
        color: white;
        position: relative;
        overflow: hidden;
        border: 2px solid transparent;
        background-clip: padding-box;
    }

    .promo-banner::before {
        content: '';
        position: absolute;
        top: -2px;
        left: -2px;
        right: -2px;
        bottom: -2px;
        background: linear-gradient(135deg, var(--secondary-gold) 0%, #FFE566 100%);
        border-radius: inherit;
        z-index: -1;
    }

    .promo-banner::after {
        content: '';
        position: absolute;
        top: -100px;
        right: -100px;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
    }

    .banner-content {
        position: relative;
        z-index: 1;
        max-width: 70%;
    }

    .banner-content h6 {
        font-weight: 700;
        margin-bottom: 12px;
        font-size: 20px;
    }

    .banner-content p {
        opacity: 0.9;
        margin-bottom: 25px;
        font-size: 15px;
        line-height: 1.5;
    }

    .banner-content .btn {
        background: white;
        color: var(--primary-blue);
        border: none;
        padding: 12px 25px;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .banner-content .btn:hover {
        background: var(--secondary-gold);
        color: var(--primary-dark);
        transform: translateX(5px);
    }

    .banner-icon {
        position: absolute;
        right: 30px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 100px;
        opacity: 0.15;
        z-index: 0;
    }

    /* KYC Notification - Positioned Properly */
    .kyc-notification {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 1050;
        max-width: 400px;
        animation: slideInUp 0.5s ease-out;
    }

    @keyframes slideInUp {
        from {
            transform: translateY(100px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .kyc-notification .alert {
        border-radius: 16px;
        border: none;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        border-left: 5px solid;
        padding: 0;
        overflow: hidden;
    }

    .alert-danger {
        border-left-color: var(--danger-color) !important;
    }

    .alert-info {
        border-left-color: var(--info-color) !important;
    }

    .alert-warning {
        border-left-color: var(--warning-color) !important;
    }

    .alert-content {
        display: flex;
        align-items: flex-start;
        padding: 25px;
    }

    .alert-content i {
        font-size: 24px;
        margin-right: 20px;
        flex-shrink: 0;
        margin-top: 5px;
    }

    .alert-content > div {
        flex: 1;
    }

    .alert-content h6 {
        font-weight: 700;
        margin-bottom: 10px;
        color: inherit;
        font-size: 16px;
    }

    .alert-content p {
        margin-bottom: 20px;
        color: inherit;
        opacity: 0.9;
        line-height: 1.5;
        font-size: 14px;
    }

    .alert-content .btn {
        padding: 8px 20px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    /* Responsive Design - Enhanced */
    @media (max-width: 1199px) {
        .container-fruid {
            padding-left: 15px;
            padding-right: 15px;
        }
        
        .dashboard-card-wu {
            padding: 25px;
        }
        
        .action-buttons {
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        }
    }

    @media (max-width: 991px) {
        .col-lg-7, .col-lg-5 {
            width: 100%;
        }
        
        .sidebar-widgets {
            position: static;
            margin-top: 30px;
        }
        
        .kyc-notification {
            left: 20px;
            right: 20px;
            max-width: none;
            bottom: 20px;
        }
    }

    @media (max-width: 768px) {
        .dashboard-section {
            padding: 15px;
        }
        
        .overlay {
            padding-top: 20px !important;
        }
        
        .dashboard-card-wu {
            margin-bottom: 20px;
            min-height: 180px;
        }
        
        .card-amount {
            font-size: 32px;
        }
        
        .section-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }
        
        .transactions-area,
        .quick-actions {
            padding: 20px;
        }
        
        .transactions-table {
            overflow-x: auto;
        }
        
        .transactions-table table {
            min-width: 800px;
        }
        
        .action-buttons {
            grid-template-columns: 1fr;
        }
        
        .action-btn {
            min-height: 70px;
        }
        
        .promo-banner {
            padding: 25px;
        }
        
        .banner-content {
            max-width: 100%;
        }
        
        .banner-icon {
            display: none;
        }
    }

    @media (max-width: 576px) {
        .container-fruid {
            padding-left: 10px;
            padding-right: 10px;
        }
        
        .dashboard-card-wu {
            padding: 20px;
        }
        
        .card-amount {
            font-size: 28px;
        }
        
        .widget-header,
        .widget-body {
            padding: 20px;
        }
        
        .recipient-item {
            padding: 15px;
        }
        
        .alert-content {
            padding: 20px;
        }
        
        .kyc-notification {
            left: 10px;
            right: 10px;
            bottom: 10px;
        }
    }

    /* Animations */
    @keyframes fadeIn {
        from { 
            opacity: 0; 
            transform: translateY(30px); 
        }
        to { 
            opacity: 1; 
            transform: translateY(0); 
        }
    }

    .dashboard-card-wu, .quick-actions, .transactions-area, .widget {
        animation: fadeIn 0.6s ease-out;
    }

    /* Scrollbar Styling */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    ::-webkit-scrollbar-track {
        background: transparent;
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb {
        background: rgba(255, 55, 6, 0.3);
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 55, 6, 0.1);;
    }

    /* Loading States */
    .loading {
        position: relative;
        overflow: hidden;
    }

    .loading::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        animation: loading 1.5s infinite;
    }

    @keyframes loading {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }
</style>
@endpush