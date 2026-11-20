@extends($activeTemplate . 'layouts.frontend')

@push('style')
<style>
.cookie-page {
    background: #fff;
    font-family: 'Inter', sans-serif;
}

.cookie-hero {
    padding: 80px 32px 56px;
    background: linear-gradient(108deg, #f4faff 0%, #eaf4ff 55%, #e3f0ff 100%);
    border-bottom: 1px solid #deeaf7;
    position: relative;
    overflow: hidden;
}
.cookie-hero::before {
    content: '';
    position: absolute; inset: 0;
    background-image: radial-gradient(circle, rgba(167,27,71,.06) 1px, transparent 1px);
    background-size: 28px 28px;
    pointer-events: none;
}
.cookie-hero-inner {
    max-width: 860px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}
.cookie-kicker {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 14px;
    border-radius: 999px;
    background: #fff;
    color: #A71B47;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .12em;
    text-transform: uppercase;
    box-shadow: 0 1px 8px rgba(167,27,71,.12);
    margin-bottom: 20px;
}
.cookie-hero h1 {
    font-size: clamp(2rem, 5vw, 3.5rem);
    font-weight: 900;
    color: #071833;
    line-height: 1.05;
    letter-spacing: -.03em;
    margin: 0 0 16px;
}
.cookie-hero p {
    font-size: 16px;
    color: #50617c;
    line-height: 1.75;
    max-width: 600px;
    margin: 0;
}

.cookie-body {
    max-width: 860px;
    margin: 0 auto;
    padding: 56px 32px 96px;
}
.cookie-content {
    font-size: 15px;
    color: #222;
    line-height: 1.85;
}
.cookie-content h2 {
    font-size: 20px;
    font-weight: 800;
    color: #0C0C0C;
    letter-spacing: -.02em;
    margin: 44px 0 16px;
    padding-bottom: 16px;
    border-bottom: 1.5px solid #EBEBEB;
}
.cookie-content h2:first-child { margin-top: 0; }
.cookie-content p { margin-bottom: 14px; }
.cookie-content ul, .cookie-content ol { padding-left: 24px; margin-bottom: 14px; }
.cookie-content li { margin-bottom: 6px; }
.cookie-content strong { font-weight: 700; color: #0C0C0C; }
.cookie-content a { color: #A71B47; text-decoration: underline; }

@media (max-width: 640px) {
    .cookie-hero { padding: 60px 20px 40px; }
    .cookie-body { padding: 40px 20px 72px; }
}
</style>
@endpush

@section('content')
<div class="cookie-page">

    <div class="cookie-hero">
        <div class="cookie-hero-inner">
            <div class="cookie-kicker">
                <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Cookie Policy
            </div>
            <h1>{{ __('Cookie Policy') }}</h1>
            <p>@lang('How') {!! siteNameHtml() !!} @lang('uses cookies and similar technologies to improve your experience.')</p>
        </div>
    </div>

    <div class="cookie-body">
        <div class="cookie-content">
            @php echo $cookie->data_values->description; @endphp
        </div>
    </div>

</div>
@endsection
