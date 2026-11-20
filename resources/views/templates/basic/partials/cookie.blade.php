@php
$cookie = App\Models\Frontend::where('data_keys', 'cookie.data')->first();
@endphp
@if ($cookie->data_values->status == Status::ENABLE && !\Cookie::get('gdpr_cookie'))
    <div id="cookiePopup" class="cookies-card text-center">
        <div class="cookies-card__icon text--base">
            <i class="las la-cookie-bite"></i>
        </div>

        <p class="mt-4 cookies-card__content">
            {{ $cookie->data_values->short_desc }}
            <a href="{{ route('cookie.policy') }}" class="text--primary" target="_blank">
                @lang('learn more')
            </a>
        </p>

        <div class="cookies-card__btn mt-4">
            <button type="button" class="btn btn--xl btn--base w-100 policy" style="background-color:var(--primary-color)">
                @lang('Allow')
            </button>
        </div>
    </div>
@endif

<style>
    .cookies-card {
    position: fixed;
    bottom: 20px;
    right: 20px;
    max-width: 360px;
    width: calc(100% - 40px);
    background: #ffffff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 10px 35px rgba(0, 0, 0, 0.15);
    z-index: 9999;
    animation: slideIn 0.4s ease-out;
}

@keyframes slideIn {
    from {
        transform: translateY(30px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    const allowBtn = document.querySelector('.policy');

    if (allowBtn) {
        allowBtn.addEventListener('click', function () {
            document.cookie = "gdpr_cookie=1; path=/; max-age=" + (60 * 60 * 24 * 365);
            document.getElementById('cookiePopup').remove();
        });
    }
});
</script>