@php
    $recaptchaExtension = \App\Models\Extension::where('act', 'google-recaptcha2')->where('status', \App\Constants\Status::ENABLE)->first();
@endphp
@if($recaptchaExtension)
    <div class="g-recaptcha mb-3" data-sitekey="{{ $recaptchaExtension->shortcode->site_key->value }}"></div>
    @error('captcha')<span class="sc-error" style="display:block;margin-top:-10px;margin-bottom:10px;">{{ $message }}</span>@enderror
    @pushOnce('js-lib')
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endPushOnce
@endif
