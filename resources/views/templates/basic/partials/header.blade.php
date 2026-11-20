@push('style')
<style>
/* ── AkMoney Header – White ── */
.km-header {
    position: fixed;
    top: 0; left: 0; right: 0;
    z-index: 999;
    height: 78px;
    background: rgba(255,255,255,0.96);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    border-bottom: 1px solid rgba(0,0,0,0.07);
    padding-top: env(safe-area-inset-top);
    box-shadow: 0 1px 20px rgba(0,0,0,0.06);
    transition: box-shadow .25s ease;
}

.km-header.scrolled {
    box-shadow: 0 2px 28px rgba(0,0,0,0.10);
}

.km-header__inner {
    width: min(calc(100% - 48px), 1120px);
    height: 78px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 28px;
}

.km-header__logo {
    display: flex;
    align-items: center;
    flex: 0 0 auto;
    text-decoration: none;
}

.km-header__logo img {
    height: 36px;
    width: auto;
    display: block;
}

.km-header__nav {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 38px;
    flex: 1;
    list-style: none;
    margin: 0;
    padding: 0;
    font-family: 'Work Sans', sans-serif;
}

.km-header__nav a {
    display: inline-flex;
    align-items: center;
    color: rgba(13,26,20,0.65);
    font-size: 15px;
    font-weight: 600;
    text-decoration: none;
    transition: color .18s ease;
    white-space: nowrap;
    position: relative;
}

.km-header__nav a::after {
    content: '';
    position: absolute;
    left: 0; bottom: -8px;
    width: 0; height: 2px;
    background: #A71B47;
    transition: width .2s ease;
}

.km-header__nav a.active { color: #A71B47; }
.km-header__nav a:hover { color: #0d1a14; }
.km-header__nav a.active::after,
.km-header__nav a:hover::after { width: 100%; }

.km-header__actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 0 0 auto;
}

/* Language dropdown */
.km-lang { position: relative; }

.km-lang__btn {
    height: 38px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    border: 1px solid rgba(13,26,20,0.14);
    border-radius: 8px;
    background: transparent;
    color: rgba(13,26,20,0.65);
    padding: 0 10px;
    font-size: 13px;
    font-weight: 600;
    font-family: 'Work Sans', sans-serif;
    cursor: pointer;
    transition: background .18s, border-color .18s;
}
.km-lang__btn:hover { background: rgba(13,26,20,0.05); border-color: rgba(13,26,20,0.22); }

.km-lang__globe { width: 14px; height: 14px; }
.km-lang__chev { width: 11px; height: 11px; transition: transform .18s ease; }
.km-lang:hover .km-lang__chev,
.km-lang.open .km-lang__chev { transform: rotate(180deg); }

.km-lang__menu {
    position: absolute;
    top: 46px; right: 0;
    width: 158px;
    background: #fff;
    border: 1px solid rgba(13,26,20,0.10);
    border-radius: 12px;
    box-shadow: 0 18px 44px rgba(0,0,0,.14);
    padding: 6px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(8px);
    transition: all .18s ease;
    z-index: 9999;
}

.km-lang:hover .km-lang__menu,
.km-lang.open .km-lang__menu {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.km-lang__menu a {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 9px 11px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    color: rgba(13,26,20,0.78);
    text-decoration: none;
    transition: background .15s, color .15s;
}
.km-lang__menu a:hover { background: rgba(13,26,20,0.05); color: #0d1a14; }
.km-lang__menu a.active { background: rgba(167,27,71,0.08); color: #A71B47; }
.km-lang__menu img { width: 20px; height: 14px; border-radius: 2px; object-fit: cover; flex-shrink: 0; }

/* Auth buttons */
.km-btn-nav {
    height: 40px;
    min-width: 90px;
    display: none;
    align-items: center;
    justify-content: center;
    border-radius: 9px;
    font-size: 14px;
    font-weight: 700;
    font-family: 'Work Sans', sans-serif;
    text-decoration: none;
    transition: all .18s ease;
    white-space: nowrap;
}
@media(min-width: 920px) { .km-btn-nav { display: inline-flex; } }

.km-btn-nav--login {
    color: #fff;
    background: #0F4650;
    border: 1.5px solid #0F4650;
}
.km-btn-nav--login:hover {
    background: #0a323b;
    border-color: #0a323b;
    color: #fff;
}

.km-btn-nav--signup {
    color: #fff;
    background: #A71B47;
    border: 1.5px solid #A71B47;
    box-shadow: 0 4px 14px rgba(167,27,71,.22);
}
.km-btn-nav--signup:hover {
    background: #8a1539;
    border-color: #8a1539;
    color: #fff;
}

.km-btn-nav--dashboard {
    color: #fff;
    background: #0F4650;
    border: 1.5px solid #0F4650;
    box-shadow: 0 4px 14px rgba(15,70,80,.20);
}
.km-btn-nav--dashboard:hover {
    background: #0a323b;
    color: #fff;
}

/* Hamburger */
.km-menu-btn {
    display: none;
    width: 40px; height: 40px;
    border: 1px solid rgba(13,26,20,0.14);
    border-radius: 9px;
    background: transparent;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    gap: 4px;
    cursor: pointer;
}
.km-menu-btn span {
    width: 17px; height: 2px;
    border-radius: 999px;
    background: rgba(13,26,20,0.75);
    display: block;
}

/* Mobile nav */
.km-mobile-nav {
    display: none;
    position: absolute;
    top: 78px; left: 12px; right: 12px;
    flex-direction: column;
    gap: 3px;
    background: #fff;
    border: 1px solid rgba(13,26,20,0.09);
    border-radius: 16px;
    padding: 12px;
    box-shadow: 0 20px 50px rgba(0,0,0,.12);
    z-index: 998;
}

.km-mobile-nav a {
    padding: 13px 14px;
    border-radius: 9px;
    color: rgba(13,26,20,0.70);
    font-size: 15px;
    font-weight: 600;
    text-decoration: none;
    display: block;
    transition: background .15s, color .15s;
}
.km-mobile-nav a:hover,
.km-mobile-nav a.active {
    background: rgba(13,26,20,0.05);
    color: #0d1a14;
}

.km-mobile-nav .km-mob-divider {
    height: 1px;
    background: rgba(13,26,20,0.08);
    margin: 5px 0;
}

.km-mobile-nav .km-mob-actions {
    display: flex;
    gap: 8px;
    padding-top: 3px;
}

.km-mobile-nav .km-mob-actions a {
    flex: 1;
    text-align: center;
    padding: 12px;
    border-radius: 9px;
    font-size: 14px;
    font-weight: 700;
}

@media(max-width: 919px) {
    .km-header__nav { display: none; }
    .km-menu-btn { display: flex; }
    .km-header__inner { width: min(calc(100% - 24px), 1120px); }
}

@media(max-width: 430px) {
    .km-header, .km-header__inner { height: 70px; }
    .km-header__logo img { height: 32px; }
    .km-mobile-nav { top: 70px; }
}

body { padding-top: 78px; }
@media(max-width: 430px) { body { padding-top: 70px; } }

body.km-mobile-open .km-mobile-nav { display: flex; }
</style>
@endpush

<header class="km-header" id="km-header">
    <div class="km-header__inner">

        <a href="{{ url('/') }}" class="km-header__logo" aria-label="{{ gs('site_name') }} home">
            <img src="{{ siteLogo() }}" alt="{{ gs('site_name') }}" style="max-height:36px;width:auto;max-width:160px;object-fit:contain;">
        </a>

        <nav class="km-header__nav" aria-label="Main navigation">
            <a href="{{ url('/') }}"        class="{{ request()->is('/') ? 'active' : '' }}">Home</a>
            <a href="{{ url('/about') }}"   class="{{ request()->is('about') ? 'active' : '' }}">About</a>
            <a href="{{ url('/blog') }}"    class="{{ request()->is('blog*') ? 'active' : '' }}">Blog</a>
            <a href="{{ url('/contact') }}" class="{{ request()->is('contact') ? 'active' : '' }}">Contact</a>
        </nav>

        @php
            $headerLanguages  = App\Models\Language::all();
            $headerActiveLang = $headerLanguages->where('code', config('app.locale'))->first();
        @endphp

        <div class="km-header__actions">

            {{-- Language picker --}}
            <div class="km-lang" id="km-lang-wrap">
                <button type="button" class="km-lang__btn" id="km-lang-toggle" aria-label="Select language">
                    <svg class="km-lang__globe" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/>
                        <path d="M3.4 12h17.2M12 3c2.25 2.35 3.45 5.35 3.45 9S14.25 18.65 12 21c-2.25-2.35-3.45-5.35-3.45-9S9.75 5.35 12 3Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                    <span>{{ strtoupper($headerActiveLang->code ?? 'EN') }}</span>
                    <svg class="km-lang__chev" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                        <path d="M5 7.5l5 5 5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <div class="km-lang__menu" id="km-lang-dropdown">
                    @foreach($headerLanguages as $lang)
                    <a href="{{ route('home') }}/change/{{ $lang->code }}"
                       class="{{ config('app.locale') === $lang->code ? 'active' : '' }}">
                        @if($lang->image)
                            <img src="{{ getImage(getFilePath('language').'/'.$lang->image, getFileSize('language')) }}" alt="{{ $lang->name }}">
                        @endif
                        {{ $lang->name }}
                    </a>
                    @endforeach
                </div>
            </div>

            @guest
            <a href="{{ route('user.login') }}"    class="km-btn-nav km-btn-nav--login">Log In</a>
            <a href="{{ route('user.register') }}" class="km-btn-nav km-btn-nav--signup">Get Started</a>
            @else
            <a href="{{ route('user.home') }}"     class="km-btn-nav km-btn-nav--dashboard">Dashboard</a>
            @endguest

            <button class="km-menu-btn" type="button"
                    id="km-menu-toggle"
                    onclick="document.body.classList.toggle('km-mobile-open')"
                    aria-label="Open menu">
                <span></span><span></span><span></span>
            </button>
        </div>

    </div>

    {{-- Mobile menu --}}
    <div class="km-mobile-nav">
        <a href="{{ url('/') }}"        class="{{ request()->is('/') ? 'active' : '' }}">Home</a>
        <a href="{{ url('/about') }}"   class="{{ request()->is('about') ? 'active' : '' }}">About</a>
        <a href="{{ url('/blog') }}"    class="{{ request()->is('blog*') ? 'active' : '' }}">Blog</a>
        <a href="{{ url('/contact') }}" class="{{ request()->is('contact') ? 'active' : '' }}">Contact</a>
        <div class="km-mob-divider"></div>
        <div class="km-mob-actions">
            @guest
            <a href="{{ route('user.login') }}"    style="background:#0F4650;color:#fff;">Log In</a>
            <a href="{{ route('user.register') }}" style="background:#A71B47;color:#fff;">Get Started</a>
            @else
            <a href="{{ route('user.home') }}"     style="background:#0F4650;color:#fff;">Dashboard</a>
            @endguest
        </div>
    </div>
</header>

<script>
(function(){
    var langWrap   = document.getElementById('km-lang-wrap');
    var langToggle = document.getElementById('km-lang-toggle');
    var header     = document.getElementById('km-header');

    if (langWrap && langToggle) {
        langToggle.addEventListener('click', function(e){
            e.stopPropagation();
            langWrap.classList.toggle('open');
        });
        document.addEventListener('click', function(){
            langWrap.classList.remove('open');
        });
        document.addEventListener('keydown', function(e){
            if (e.key === 'Escape') {
                langWrap.classList.remove('open');
                document.body.classList.remove('km-mobile-open');
            }
        });
    }

    if (header) {
        window.addEventListener('scroll', function(){
            header.classList.toggle('scrolled', window.scrollY > 40);
        }, { passive: true });
    }
})();
</script>
