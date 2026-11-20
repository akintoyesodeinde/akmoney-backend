@php $customCaptcha = loadCustomCaptcha(); @endphp

@if(\App\Lib\Captcha::reCaptchaKey())
    <div class="g-recaptcha mb-3" data-sitekey="{{ \App\Lib\Captcha::reCaptchaKey() }}"></div>
    @error('captcha')<span class="text-danger d-block mb-2">{{ $message }}</span>@enderror
@endif

@if($customCaptcha)
    <div class="form-group">
        <div class="mb-2">
            @php echo $customCaptcha @endphp
        </div>
        <label class="form-label {{ $class ?? '' }}">@lang('Captcha')</label>
        <input type="text" name="captcha" class="form-control form--control" required>
    </div>
@endif
