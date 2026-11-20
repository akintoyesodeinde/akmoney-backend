<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt {{ $transfer->mtcn_number }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 13px; color: #0f172a; background: #fff; padding: 32px; }
        .header { text-align: center; margin-bottom: 28px; border-bottom: 2px solid #0d9488; padding-bottom: 16px; }
        .header h1 { font-size: 22px; font-weight: 800; color: #0d9488; }
        .header p { font-size: 12px; color: #64748b; margin-top: 4px; letter-spacing: 0.1em; text-transform: uppercase; }
        .section-group { margin-bottom: 20px; }
        .section-group-title { font-size: 10px; font-weight: 700; color: #0d9488; text-transform: uppercase; letter-spacing: 0.15em; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; margin-bottom: 10px; }
        .row { display: flex; justify-content: space-between; align-items: flex-start; padding: 5px 0; border-bottom: 1px dotted #e2e8f0; }
        .row:last-child { border-bottom: none; }
        .label { color: #64748b; font-size: 11px; }
        .value { font-size: 12px; font-weight: 600; color: #0f172a; text-align: right; max-width: 60%; }
        .amount-block { background: #f0fdfa; border: 1px solid #99f6e4; border-radius: 8px; padding: 14px 16px; text-align: center; margin-bottom: 20px; }
        .amount-block .sent { font-size: 26px; font-weight: 800; color: #0d9488; }
        .amount-block .receives { font-size: 13px; color: #475569; margin-top: 4px; }
        .badge { display: inline-block; padding: 2px 10px; border-radius: 9999px; font-size: 11px; font-weight: 700; }
        .badge-success  { background: #d1fae5; color: #065f46; }
        .badge-warning  { background: #fef3c7; color: #78350f; }
        .badge-danger   { background: #fee2e2; color: #991b1b; }
        .badge-blue     { background: #dbeafe; color: #1e40af; }
        .footer { margin-top: 28px; text-align: center; font-size: 10px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 12px; }
    </style>
</head>
<body>

<div class="header">
    <h1>{!! siteNameHtml() !!}</h1>
    <p>Transaction Receipt</p>
</div>

{{-- Amount summary --}}
<div class="amount-block">
    <div class="sent">{{ $transfer->sending_currency }} {{ showAmount($transfer->sending_amount, currencyFormat: false) }}</div>
    <div class="receives">Recipient receives {{ showAmount($transfer->recipient_amount, currencyFormat: false) }} {{ $transfer->recipient_currency }}</div>
</div>

{{-- Status --}}
@php
    if ($transfer->status == \App\Constants\Status::SEND_MONEY_COMPLETED) {
        $badgeClass = 'badge-success'; $statusText = 'Completed';
    } elseif ($transfer->status == \App\Constants\Status::SEND_MONEY_REFUNDED) {
        $badgeClass = 'badge-danger';  $statusText = 'Refunded';
    } elseif ($transfer->status == \App\Constants\Status::SEND_MONEY_PENDING) {
        $badgeClass = 'badge-blue';    $statusText = 'Processing';
    } elseif ($transfer->payment_status == \App\Constants\Status::PAYMENT_SUCCESS) {
        $badgeClass = 'badge-blue';    $statusText = 'Paid — Awaiting Dispatch';
    } else {
        $badgeClass = 'badge-warning'; $statusText = 'Awaiting Payment';
    }
@endphp

{{-- Transaction details --}}
<div class="section-group">
    <div class="section-group-title">Transaction Details</div>
    <div class="row">
        <span class="label">MTCN</span>
        <span class="value">{{ $transfer->mtcn_number }}</span>
    </div>
    <div class="row">
        <span class="label">Transaction ID</span>
        <span class="value">{{ $transfer->trx }}</span>
    </div>
    <div class="row">
        <span class="label">Date</span>
        <span class="value">{{ showDateTime($transfer->created_at) }}</span>
    </div>
    <div class="row">
        <span class="label">Status</span>
        <span class="value"><span class="badge {{ $badgeClass }}">{{ $statusText }}</span></span>
    </div>
</div>

{{-- Payment information --}}
<div class="section-group">
    <div class="section-group-title">Payment Information</div>
    <div class="row">
        <span class="label">Payment Method</span>
        <span class="value">
            @if ($transfer->deposit && $transfer->deposit->gateway)
                {{ $transfer->deposit->gateway->name }}
            @else
                Wallet Balance
            @endif
        </span>
    </div>
    @if ($transfer->deposit)
    <div class="row">
        <span class="label">Amount Charged</span>
        <span class="value">{{ $transfer->deposit->method_currency }} {{ showAmount($transfer->deposit->final_amount, currencyFormat: false) }}</span>
    </div>
    @if ($transfer->deposit->charge > 0)
    <div class="row">
        <span class="label">Gateway Fee</span>
        <span class="value">{{ $transfer->deposit->method_currency }} {{ showAmount($transfer->deposit->charge, currencyFormat: false) }}</span>
    </div>
    @endif
    @endif
    <div class="row">
        <span class="label">Transfer Fee</span>
        <span class="value">{{ $transfer->sending_currency }} {{ showAmount($transfer->sending_charge, currencyFormat: false) }}</span>
    </div>
    <div class="row">
        <span class="label">Total Paid</span>
        <span class="value">{{ gs('cur_text') }} {{ showAmount($transfer->base_currency_amount + $transfer->base_currency_charge, currencyFormat: false) }}</span>
    </div>
</div>

{{-- Service / Delivery --}}
<div class="section-group">
    <div class="section-group-title">Service Used</div>
    <div class="row">
        <span class="label">Delivery Method</span>
        <span class="value">
            @if ($transfer->countryDeliveryMethod && $transfer->countryDeliveryMethod->deliveryMethod)
                {{ $transfer->countryDeliveryMethod->deliveryMethod->name }}
            @else
                Agent Payout
            @endif
        </span>
    </div>
    @if ($transfer->service)
    <div class="row">
        <span class="label">Service</span>
        <span class="value">{{ $transfer->service->name }}</span>
    </div>
    @endif
    @if ($transfer->sourceOfFund)
    <div class="row">
        <span class="label">Source of Funds</span>
        <span class="value">{{ $transfer->sourceOfFund->name }}</span>
    </div>
    @endif
    @if ($transfer->sendingPurpose)
    <div class="row">
        <span class="label">Sending Purpose</span>
        <span class="value">{{ $transfer->sendingPurpose->name }}</span>
    </div>
    @endif
</div>

{{-- Beneficiary / Destination details --}}
<div class="section-group">
    <div class="section-group-title">Destination Details</div>
    <div class="row">
        <span class="label">Beneficiary Name</span>
        <span class="value">{{ $transfer->recipient->name }}</span>
    </div>
    @if (!empty($transfer->recipient->mobile))
    <div class="row">
        <span class="label">Mobile</span>
        <span class="value">{{ $transfer->recipient->mobile }}</span>
    </div>
    @endif
    @if (!empty($transfer->recipient->email))
    <div class="row">
        <span class="label">Email</span>
        <span class="value">{{ $transfer->recipient->email }}</span>
    </div>
    @endif
    @if (!empty($transfer->recipient->address))
    <div class="row">
        <span class="label">Address</span>
        <span class="value">{{ $transfer->recipient->address }}</span>
    </div>
    @endif
    <div class="row">
        <span class="label">Destination Country</span>
        <span class="value">
            @if ($transfer->recipientCountry)
                {{ $transfer->recipientCountry->name }}
            @endif
        </span>
    </div>
    <div class="row">
        <span class="label">Recipient Currency</span>
        <span class="value">{{ $transfer->recipient_currency }}</span>
    </div>
    @if ($transfer->service_form_data)
        @php $hasAccountFields = collect((array)$transfer->service_form_data)->filter(fn($f) => !empty(is_object($f) ? $f->value : ($f['value'] ?? '')) && (is_object($f) ? $f->type : ($f['type'] ?? '')) !== 'file')->count(); @endphp
        @if($hasAccountFields)
        </div>

        {{-- Account Details --}}
        <div class="section-group">
            <div class="section-group-title">Account Details</div>
            @foreach ($transfer->service_form_data as $field)
                @php
                    $fName  = is_object($field) ? ($field->name  ?? $field->label ?? '') : ($field['name']  ?? $field['label'] ?? '');
                    $fValue = is_object($field) ? ($field->value ?? '') : ($field['value'] ?? '');
                    $fType  = is_object($field) ? ($field->type  ?? 'text') : ($field['type'] ?? 'text');
                @endphp
                @if(!empty($fValue) && $fType !== 'file')
                <div class="row">
                    <span class="label">{{ $fName }}</span>
                    <span class="value">
                        @if(is_array($fValue)) {{ implode(', ', $fValue) }} @else {{ $fValue }} @endif
                    </span>
                </div>
                @endif
            @endforeach
        @endif
    @endif
</div>

{{-- Sender details --}}
<div class="section-group">
    <div class="section-group-title">Sender Details</div>
    <div class="row">
        <span class="label">Sender Name</span>
        <span class="value">{{ @$transfer->senderInfo->name }}</span>
    </div>
    <div class="row">
        <span class="label">Sending Country</span>
        <span class="value">
            @if ($transfer->sendingCountry)
                {{ $transfer->sendingCountry->name }}
            @endif
        </span>
    </div>
    <div class="row">
        <span class="label">Sending Currency</span>
        <span class="value">{{ $transfer->sending_currency }}</span>
    </div>
</div>

<div class="footer">
    This receipt is generated automatically. Please retain for your records.<br>
    {!! siteNameHtml() !!} &mdash; {{ now()->format('d M Y, H:i') }}
</div>

</body>
</html>
