@extends($activeTemplate . 'layouts.app')
@section('panel')
    @include($activeTemplate . 'partials.header')

    @yield('content')

        @include($activeTemplate . 'sections.footer')

    @include($activeTemplate . 'partials.cookie')
@endsection

@push('script')
@php $rcSiteKey = \App\Lib\Captcha::reCaptchaKey(); @endphp
@if($rcSiteKey)
<script src="https://www.google.com/recaptcha/api.js?onload=_initRecaptcha&render=explicit" async defer></script>
<script>
var _recaptchaSiteKey = '{{ $rcSiteKey }}';

function _initRecaptcha() {
    document.querySelectorAll('form').forEach(function(form) {
        var input = form.querySelector('[name="g-recaptcha-response"]');
        if (!input) return;

        var container = document.createElement('div');
        container.style.cssText = 'position:absolute;left:-9999px;top:-9999px;';
        form.appendChild(container);

        var widgetId = grecaptcha.render(container, {
            sitekey: _recaptchaSiteKey,
            size: 'invisible',
            callback: function(token) {
                input.value = token;
                form.submit();
            },
            'expired-callback': function() {
                input.value = '';
                grecaptcha.reset(widgetId);
            },
        });

        form.addEventListener('submit', function(e) {
            if (input.value) return;
            e.preventDefault();
            grecaptcha.execute(widgetId);
        });
    });
}
</script>
@endif
    <script>
        (function($) {
            "use strict";

            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', event => {
                matched = event.matches;
                if (matched) {
                    $('body').addClass('dark-mode');
                    $('.navbar').addClass('navbar-dark');
                } else {
                    $('body').removeClass('dark-mode');
                    $('.navbar').removeClass('navbar-dark');    
                }
            });

            let matched = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (matched) {
                $('body').addClass('dark-mode');
                $('.navbar').addClass('navbar-dark');
            } else {
                $('body').removeClass('dark-mode');
                $('.navbar').removeClass('navbar-dark');
            }
        })(jQuery);
    </script>
@endpush
