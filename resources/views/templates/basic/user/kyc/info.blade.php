@extends($activeTemplate . 'layouts.master')

@section('content')
<div class="py-16">
    <div class="max-w-4xl mx-auto px-4">
        <div class="bg-bg-main rounded-xl shadow-sm border border-gray-200">

            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200">
                <h5 class="text-lg font-semibold text-text-primary">
                    {{ __($pageTitle) }}
                </h5>
            </div>

            <!-- Body -->
            <div class="p-6">

                {{-- KYC under review --}}
                @if ($user->kv == 2)
                    <h4 class="text-xl font-semibold text-text-primary mb-3">
                        @lang('Your KYC is Under Review')
                    </h4>

                    <p class="text-text-primary mb-4">
                        @lang('Thank you for submitting your verification details. Our team is currently reviewing your documents. This process usually takes 24–48 hours.')
                    </p>

                    <div class="mb-6 rounded-lg border border-brand-gold/10 bg-brand-gold/5 text-brand-gold px-4 py-3 text-sm dark:border-yellow-500/40 dark:bg-yellow-500/10 dark:text-yellow-300">
                        @lang('You will be notified once your verification is approved or if further information is required.')
                    </div>
                @endif
                
                {{-- KYC rejected --}}
                @if ($user->kv == \App\Constants\Status::KYC_REJECTED)
                    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-6 py-4">
                        <h4 class="text-lg font-semibold text-red-700 mb-2">
                            @lang('Your KYC Was Rejected')
                        </h4>
                
                        <p class="text-sm text-red-700 mb-4">
                            {{ __($user->kyc_rejection_reason) }}
                        </p>
                
                        {{-- Rejection attachments --}}
                        @if (!empty($user->kyc_rejection_attachments))
                            <div class="mt-4">
                                <h6 class="text-sm font-semibold text-red-700 mb-2">
                                    @lang('Attached Notes / Documents')
                                </h6>
                
                                <ul class="space-y-2">
                                    @foreach ($user->kyc_rejection_attachments as $file)
                                        <li class="flex items-center justify-between bg-white rounded-lg px-4 py-2 border border-red-100">
                                            <span class="text-sm text-text-primary truncate">
                                                {{ basename($file) }}
                                            </span>
                
                                            <div class="flex items-center gap-2">
                                                {{-- View --}}
                                                <a
                                                    href="{{ route('user.view.attachment', encrypt(getFilePath('verify') . '/' . $file)) }}"
                                                    target="_blank"
                                                    class="inline-flex items-center gap-1 text-sm font-semibold text-brand-teal hover:underline"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                                         viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              d="M2.458 12C3.732 7.943 7.523 5 12 5
                                                                 c4.478 0 8.268 2.943 9.542 7
                                                                 -1.274 4.057-5.064 7-9.542 7
                                                                 -4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                    @lang('View')
                                                </a>
                
                                                {{-- Download --}}
                                                <a
                                                    href="{{ route('user.download.attachment', encrypt(getFilePath('verify') . '/' . $file)) }}"
                                                    class="inline-flex items-center gap-1 text-sm font-semibold text-brand-teal hover:underline"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                                         viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              d="M12 4v16m8-8H4"/>
                                                    </svg>
                                                    @lang('Download')
                                                </a>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                
                        <div class="mt-4 text-sm text-text-primary">
                            @lang('Please review the feedback above and submit updated documents.')
                        </div>
                        <a href="{{ route('user.kyc.form') }}" class="inline-flex items-center gap-1 px-3 text-sm font-medium bg-brand-gold hover:bg-brand-gold/90 text-brand-teal font-bold py-3 rounded-xl transition-all duration-300 hover:shadow-lg hover:shadow-brand-gold/20">
                            Re-submit Documents
                        </a>
                    </div>
                @endif
                
                {{-- KYC approved --}}
                @if ($user->kv == \App\Constants\Status::KYC_VERIFIED && $user->kyc_approve_reason)
                    <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-6 py-4">
                        <h4 class="text-lg font-semibold text-green-700 mb-2">
                            @lang('Your KYC Has Been Approved')
                        </h4>
                
                        @if ($user->kyc_approve_reason)
                            <p class="text-sm text-green-700 mb-4">
                                {{ __($user->kyc_approve_reason) }}
                            </p>
                        @endif
                
                        {{-- Approval attachments --}}
                        @if (!empty($user->kyc_approve_attachments))
                            <div class="mt-4">
                                <h6 class="text-sm font-semibold text-green-700 mb-2">
                                    @lang('Approval Documents')
                                </h6>
                
                                <ul class="space-y-2">
                                    @foreach ($user->kyc_approve_attachments as $file)
                                        <li class="flex items-center justify-between bg-white rounded-lg px-4 py-2 border border-green-100">
                                            <span class="text-sm text-brand-teal truncate">
                                                {{ basename($file) }}
                                            </span>
                
                                            <div class="flex items-center gap-2">
                                                {{-- View --}}
                                                <a
                                                    href="{{ route('user.view.attachment', encrypt(getFilePath('verify') . '/' . $file)) }}"
                                                    target="_blank"
                                                    class="inline-flex items-center gap-1 text-sm font-semibold text-brand-teal hover:underline"
                                                >
                                                    @lang('View')
                                                </a>
                
                                                {{-- Download --}}
                                                <a
                                                    href="{{ route('user.download.attachment', encrypt(getFilePath('verify') . '/' . $file)) }}"
                                                    class="inline-flex items-center gap-1 text-sm font-semibold text-brand-teal hover:underline"
                                                >
                                                    @lang('Download')
                                                </a>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                
                        <div class="mt-4 text-sm text-text-primary">
                            @lang('You now have full access to all platform features.')
                        </div>
                    </div>
                @endif


                <h5 class="text-sm font-semibold uppercase tracking-wide text-text-primary mb-4">
                    @lang('Submitted data')
                </h5>

                @if ($user->kyc_data)
                    <ul class="divide-y divide-gray-200">
                        @foreach ($user->kyc_data as $val)
                            @continue(!$val->value)

                            <li class="flex items-center justify-between py-3">
                                <span class="text-sm font-medium text-text-primary">
                                    {{ __($val->name) }}
                                </span>

                                <span class="text-sm text-text-primary text-right">
                                    @if ($val->type == 'checkbox')
                                        {{ implode(', ', $val->value) }}

                                    @elseif ($val->type == 'file')
                                    <div class="flex items-center gap-3 justify-end">
                                        {{-- View --}}
                                        <a
                                            href="{{ route('user.view.attachment', encrypt(getFilePath('verify') . '/' . $val->value)) }}"
                                            target="_blank"
                                            class="inline-flex items-center gap-1 px-3 text-sm font-medium bg-brand-gold hover:bg-brand-gold/90 text-brand-teal font-bold py-3 rounded-xl transition-all duration-300 hover:shadow-lg hover:shadow-brand-gold/20" 
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M2.458 12C3.732 7.943 7.523 5 12 5
                                                         c4.478 0 8.268 2.943 9.542 7
                                                         -1.274 4.057-5.064 7-9.542 7
                                                         -4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            @lang('View')
                                        </a>
                                
                                        {{-- Download --}}
                                        <a
                                            href="{{ route('user.download.attachment', encrypt(getFilePath('verify') . '/' . $val->value)) }}"
                                            class="inline-flex items-center gap-1 px-3 text-sm font-medium bg-brand-gold hover:bg-brand-gold/90 text-brand-teal font-bold py-3 rounded-xl transition-all duration-300 hover:shadow-lg hover:shadow-brand-gold/20"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M12 4v16m8-8H4"/>
                                            </svg>
                                            @lang('Download Attachment')
                                        </a>
                                    </div

                                    @else
                                        <span>{{ __($val->value) }}</span>
                                    @endif
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <h5 class="text-center text-gray-500 dark:text-gray-400">
                        @lang('KYC data not found')
                    </h5>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection
