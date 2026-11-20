@extends($activeTemplate . 'layouts.master')
@section('content')
<div class="max-w-lg mx-auto py-10 px-4">

    @if(!empty($data->error))
    {{-- Error state --}}
    <div class="bg-card-white rounded-2xl border border-red-200 p-8 text-center shadow-sm">
        <div class="w-16 h-16 rounded-full bg-red-50 border border-red-200 flex items-center justify-center mx-auto mb-4">
            <i class="ri-close-circle-line text-red-500 text-2xl"></i>
        </div>
        <h3 class="text-lg font-extrabold text-text-primary mb-2">Payment Request Failed</h3>
        <p class="text-sm text-text-secondary mb-6">{{ $data->error }}</p>
        <a href="{{ route('user.wallets') }}"
            class="inline-flex items-center gap-2 px-6 py-3 bg-brand-teal text-white text-sm font-bold rounded-xl hover:bg-opacity-90 transition">
            <i class="ri-arrow-left-line"></i> Try Again
        </a>
    </div>

    @else
    {{-- Success / pending --}}
    <div class="bg-card-white rounded-2xl border border-border-gray p-8 shadow-sm text-center">

        {{-- Icon --}}
        <div class="w-16 h-16 rounded-full bg-teal-50 border border-teal-200 flex items-center justify-center mx-auto mb-5">
            <i class="ri-mail-send-line text-brand-teal text-2xl"></i>
        </div>

        <h3 class="text-xl font-extrabold text-text-primary mb-1">Check Your Email</h3>
        <p class="text-sm text-text-secondary mb-6">
            An Interac e-Transfer request has been sent to<br>
            <strong class="text-text-primary">{{ $data->customer_email }}</strong>
        </p>

        {{-- Steps --}}
        <div class="bg-gray-50 border border-border-gray rounded-xl p-5 mb-6 text-left">
            <p class="text-[10px] font-bold text-text-secondary uppercase tracking-widest mb-3 text-center">How to complete your payment</p>
            <ol class="space-y-2.5 text-sm text-text-primary">
                <li class="flex items-start gap-2.5">
                    <span class="w-5 h-5 rounded-full bg-brand-teal text-white text-[10px] font-bold flex items-center justify-center flex-shrink-0 mt-0.5">1</span>
                    Open the Interac e-Transfer request from <em>no-reply@interac.ca</em>
                </li>
                <li class="flex items-start gap-2.5">
                    <span class="w-5 h-5 rounded-full bg-brand-teal text-white text-[10px] font-bold flex items-center justify-center flex-shrink-0 mt-0.5">2</span>
                    Click <strong>"Accept Transfer"</strong> in the email
                </li>
                <li class="flex items-start gap-2.5">
                    <span class="w-5 h-5 rounded-full bg-brand-teal text-white text-[10px] font-bold flex items-center justify-center flex-shrink-0 mt-0.5">3</span>
                    Choose your bank account to receive the funds
                </li>
                <li class="flex items-start gap-2.5">
                    <span class="w-5 h-5 rounded-full bg-brand-teal text-white text-[10px] font-bold flex items-center justify-center flex-shrink-0 mt-0.5">4</span>
                    Your account is credited automatically once confirmed
                </li>
            </ol>
        </div>

        {{-- Reference number --}}
        <div class="flex items-center justify-between bg-gray-50 border border-border-gray rounded-xl px-4 py-3 mb-6 text-left">
            <div>
                <p class="text-[10px] font-bold text-text-secondary uppercase tracking-widest mb-0.5">Reference</p>
                <p class="font-mono text-sm font-bold text-text-primary">{{ $data->transaction_number }}</p>
            </div>
            <button class="copy-btn text-text-secondary hover:text-brand-teal transition" data-copy="{{ $data->transaction_number }}" title="Copy">
                <i class="ri-file-copy-line text-lg"></i>
            </button>
        </div>

        <p class="text-xs text-text-secondary mb-6">
            <i class="ri-time-line mr-1"></i>
            Interac e-Transfers typically complete within a few minutes.
        </p>

        <div class="flex gap-3 justify-center flex-wrap">
            <a href="{{ route('user.deposit.history') }}"
                class="px-5 py-2.5 bg-brand-teal text-white text-sm font-bold rounded-xl hover:bg-opacity-90 transition">
                View History
            </a>
            <a href="{{ route('user.home') }}"
                class="px-5 py-2.5 border border-border-gray text-sm text-text-primary font-semibold rounded-xl hover:bg-gray-100 transition">
                Dashboard
            </a>
        </div>
    </div>
    @endif

</div>
@endsection

@push('script')
<script>
document.querySelectorAll('.copy-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
        navigator.clipboard.writeText(this.dataset.copy).then(function () {
            iziToast.success({ message: 'Copied to clipboard', position: 'topRight', timeout: 1500 });
        });
    });
});
</script>
@endpush
