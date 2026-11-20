@extends($activeTemplate . 'layouts.master')
@section('content')
<div class="max-w-lg mx-auto py-10 px-4">

    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 rounded-xl bg-amber-500 flex items-center justify-center flex-shrink-0">
            <i class="ri-currency-line text-white text-lg"></i>
        </div>
        <div>
            <h2 class="text-lg font-extrabold text-text-primary">{{ __($pageTitle) }}</h2>
            <p class="text-xs text-text-secondary">Send the exact amount to the address below</p>
        </div>
    </div>

    <div class="bg-card-white rounded-2xl border border-border-gray p-8 shadow-sm text-center">

        {{-- Amount --}}
        <div class="bg-amber-50 border border-amber-200 rounded-xl px-5 py-4 mb-6">
            <p class="text-[10px] font-bold text-amber-600 uppercase tracking-widest mb-1">Send exactly</p>
            <p class="text-3xl font-extrabold text-amber-700">
                {{ $data->amount }}
                <span class="text-lg font-semibold">{{ $data->currency }}</span>
            </p>
        </div>

        {{-- QR code --}}
        @if(!empty($data->img))
        <div class="mb-4">
            <p class="text-xs text-text-secondary mb-3">Scan to send</p>
            <img src="{{ $data->img }}" alt="QR Code" class="w-48 h-48 mx-auto rounded-xl border border-border-gray">
        </div>
        @endif

        {{-- Address --}}
        <div class="flex items-center justify-between bg-gray-50 border border-border-gray rounded-xl px-4 py-3 mb-6 text-left">
            <div class="min-w-0 flex-1">
                <p class="text-[10px] font-bold text-text-secondary uppercase tracking-widest mb-0.5">Send to address</p>
                <p class="font-mono text-xs font-bold text-text-primary break-all">{{ $data->sendto }}</p>
            </div>
            <button class="copy-btn ml-3 text-text-secondary hover:text-brand-teal transition flex-shrink-0" data-copy="{{ $data->sendto }}" title="Copy address">
                <i class="ri-file-copy-line text-lg"></i>
            </button>
        </div>

        <div class="bg-amber-50 border border-amber-100 rounded-xl px-4 py-3 text-xs text-amber-700 text-left">
            <i class="ri-error-warning-line mr-1"></i>
            Send <strong>exactly {{ $data->amount }} {{ $data->currency }}</strong>. Sending any other amount may result in loss of funds or a failed transaction.
        </div>
    </div>

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
