@php
    $blogContent = getContent('blog.content', true);
    $categories  = ['All Articles'];
    foreach($blogs as $b) {
        $cat = $b->data_values->category ?? 'General';
        if (!in_array($cat, $categories)) $categories[] = $cat;
    }
    // popular = first 4
    $popularPosts = $blogs->take(4);
@endphp

@extends($activeTemplate . 'layouts.frontend')

@push('style')
<style>
/* ── Reset: override global style.css element rules inside .kmb ── */
.kmb *, .kmb *::before, .kmb *::after { box-sizing: border-box; }
.kmb { font-family: 'Work Sans','Segoe UI',Arial,sans-serif !important; background: #f8f3f1; color: #0d1a14; }
.kmb a { text-decoration: none !important; color: inherit !important; transition: none !important; }
.kmb a:hover { text-decoration: none !important; color: inherit !important; }
.kmb p { color: inherit !important; margin-bottom: 0 !important; line-height: inherit !important; }
.kmb h1, .kmb h2, .kmb h3, .kmb h4, .kmb h5, .kmb h6 {
    font-family: 'Work Sans','Segoe UI',Arial,sans-serif !important;
    font-weight: inherit !important;
    line-height: inherit !important;
    color: inherit !important;
    text-transform: none !important;
    margin: 0 !important;
}
.kmb img { max-width: none !important; height: auto; }
.kmb-cx { width: min(calc(100% - 48px), 1400px); margin-inline: auto; }

/* ── Hero Card ── */
/* 78px = fixed header height; extra 12px breathing room */
.kmb-hero-wrap { background: #f8f3f1; padding: 90px 0 0; }

.kmb-hero-card {
    overflow: hidden;
    border-radius: 36px;
    background: #f6f1ef;
}

.kmb-hero-inner {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    align-items: center;
    padding: 48px 56px;
}

.kmb-hero-eyebrow { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .28em; color: #fff; background: #0F4650; display: inline-block; padding: 6px 14px; border-radius: 8px; margin-bottom: 20px; }

.kmb-hero-h1 {
    margin: 0 0 24px;
    font-size: clamp(36px, 4.5vw, 58px);
    font-weight: 700;
    letter-spacing: -1.5px;
    line-height: 1.05;
    color: #0F4650;
    max-width: 460px;
}

.kmb-hero-sub {
    margin: 0 0 36px;
    font-size: 17px;
    line-height: 1.75;
    color: #475569;
    max-width: 440px;
}

.kmb-search {
    display: flex;
    height: 60px;
    max-width: 440px;
    overflow: hidden;
    border-radius: 16px;
    border: 1px solid #eadfdb;
    background: #fff;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
}

.kmb-search input {
    flex: 1;
    border: none;
    background: transparent;
    padding: 0 20px;
    font-size: 14px;
    font-family: 'Work Sans', sans-serif;
    color: #0d1a14;
    outline: none;
}
.kmb-search input::placeholder { color: #94a3b8; }

.kmb-search button {
    width: 58px;
    height: 100%;
    background: #A71B47;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    transition: opacity .18s;
    flex-shrink: 0;
}
.kmb-search button:hover { opacity: .88; }

/* Hero image side */
.kmb-hero-img-wrap { position: relative; }
.kmb-hero-blob {
    position: absolute;
    right: 0; top: 60px;
    width: 300px; height: 300px;
    border-radius: 50%;
    background: rgba(167,27,71,0.28);
    filter: blur(2px);
    pointer-events: none;
    z-index: 0;
}
.kmb-hero-img {
    position: relative;
    z-index: 1;
    width: 100%;
    border-radius: 16px;
    object-fit: contain;
    display: block;
}

/* ── Category Tabs ── */
.kmb-tabs-wrap {
    margin-top: 24px;
    overflow-x: auto;
    border-radius: 24px;
    padding: 16px;
    background: rgba(255,255,255,0.70);
    border: 1px solid rgba(167,27,71,0.18);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    scrollbar-width: none;
}
.kmb-tabs-wrap::-webkit-scrollbar { display: none; }
.kmb-tabs-inner { display: flex; gap: 10px; min-width: max-content; }

.kmb-tab {
    display: flex;
    align-items: center;
    gap: 7px;
    padding: 10px 18px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 700;
    font-family: 'Work Sans', sans-serif;
    cursor: pointer;
    border: none;
    background: rgba(255,255,255,0.50);
    color: #5d6470;
    transition: all .22s;
    white-space: nowrap;
}
.kmb-tab:hover { background: rgba(255,255,255,0.75); }
.kmb-tab.active { background: #A71B47; color: #fff; }
.kmb-tab svg { flex-shrink: 0; }

/* ── Content + Sidebar ── */
.kmb-content-wrap {
    margin-top: 48px;
    padding-bottom: 80px;
}

.kmb-grid { display: grid; grid-template-columns: 1fr 340px; gap: 32px; }

.kmb-articles-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 28px;
}
.kmb-articles-head h2 { margin: 0; font-size: 22px; font-weight: 700; color: #0F4650; letter-spacing: -.5px; }

/* Article card */
.kmb-articles { display: grid; grid-template-columns: repeat(2, 1fr); gap: 22px; }

.kmb-card {
    position: relative;
    overflow: hidden;
    border-radius: 26px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: transform .3s ease, box-shadow .3s ease;
    display: block;
}
/* Maintain 1245:840 aspect ratio so images are never aggressively cropped */
.kmb-card::before {
    content: '';
    display: block;
    padding-top: calc(840 / 1245 * 100%);
}
.kmb-card:hover { transform: translateY(-5px); box-shadow: 0 16px 48px rgba(0,0,0,0.14); }

.kmb-card img {
    position: absolute;
    inset: 0;
    width: 100%; height: 100%;
    object-fit: cover;
    transition: transform .7s ease;
    display: block;
}
.kmb-card:hover img { transform: scale(1.06); }

.kmb-card-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.90) 0%, rgba(0,0,0,0.50) 50%, transparent 100%);
}

.kmb-card-body {
    position: absolute;
    bottom: 0; left: 0; right: 0;
    padding: 24px;
    color: #fff;
}

.kmb-card-cat {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 999px;
    background: #A71B47;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    margin-bottom: 10px;
    color: #fff;
}

.kmb-card-title {
    font-size: 17px;
    font-weight: 700;
    line-height: 1.35;
    color: #fff;
    margin: 0 0 8px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.kmb-card-desc {
    font-size: 13px;
    color: rgba(255,255,255,0.75);
    line-height: 1.6;
    margin: 0 0 12px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.kmb-card-meta {
    display: flex;
    align-items: center;
    gap: 14px;
    font-size: 12px;
    color: rgba(255,255,255,0.55);
}
.kmb-card-meta span { display: flex; align-items: center; gap: 5px; }

/* ── Sidebar ── */
.kmb-sidebar { display: flex; flex-direction: column; gap: 24px; }

.kmb-popular {
    border-radius: 28px;
    padding: 24px;
    background: #0F4650;
    color: #fff;
}
.kmb-popular h3 { margin: 0 0 24px; font-size: 20px; font-weight: 700; }
.kmb-popular-list { display: flex; flex-direction: column; gap: 16px; }

.kmb-popular-item {
    display: flex;
    gap: 14px;
    border-radius: 14px;
    padding: 8px;
    transition: background .18s;
    color: #fff;
}
.kmb-popular-item:hover { background: rgba(255,255,255,0.10); }
.kmb-popular-item img { width: 72px; height: 72px; border-radius: 12px; object-fit: cover; flex-shrink: 0; }
.kmb-popular-item h4 { margin: 0 0 6px; font-size: 13px; font-weight: 700; line-height: 1.4; color: #fff; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
.kmb-popular-item time { font-size: 11px; color: rgba(255,255,255,0.50); }

.kmb-newsletter {
    border-radius: 28px;
    background: #0F4650;
    padding: 32px 28px 28px;
}
.kmb-newsletter-icon {
    width: 48px; height: 48px;
    border-radius: 12px;
    background: rgba(255,255,255,0.12);
    border: 1px solid rgba(255,255,255,0.18);
    display: flex; align-items: center; justify-content: center;
    color: #fff !important;
    margin-bottom: 20px;
}
.kmb-newsletter h3 { margin: 0 0 10px; font-size: 18px; font-weight: 700; color: #fff !important; line-height: 1.3; }
.kmb-newsletter p { margin: 0 0 24px; font-size: 13px; color: rgba(255,255,255,0.70) !important; line-height: 1.70; }
.kmb-nl-form { display: flex; flex-direction: column; gap: 10px; }
.kmb-nl-form input {
    width: 100%; border: 1px solid rgba(255,255,255,0.20);
    border-radius: 10px;
    padding: 12px 16px; font-size: 13px;
    font-family: 'Work Sans', sans-serif;
    color: #fff !important; background: rgba(255,255,255,0.10);
    outline: none;
}
.kmb-nl-form input::placeholder { color: rgba(255,255,255,0.45); }
.kmb-nl-form input:focus { border-color: rgba(255,255,255,0.40); background: rgba(255,255,255,0.15); }
.kmb-nl-form button {
    width: 100%; background: #fff; border: none;
    border-radius: 10px; padding: 12px 16px;
    color: #0F4650 !important; font-size: 13px; font-weight: 700;
    cursor: pointer; font-family: 'Work Sans', sans-serif;
    transition: opacity .18s;
}
.kmb-nl-form button:hover { opacity: .88; }

/* No posts placeholder */
.kmb-empty {
    grid-column: 1 / -1;
    text-align: center;
    padding: 80px 20px;
    color: #94a3b8;
    font-size: 16px;
}

/* Responsive */
@media(max-width: 1100px) {
    .kmb-grid { grid-template-columns: 1fr 280px; }
}
@media(max-width: 900px) {
    .kmb-grid { grid-template-columns: 1fr; }
    .kmb-sidebar { flex-direction: row; flex-wrap: wrap; }
    .kmb-popular, .kmb-newsletter { flex: 1; min-width: 280px; }
}
@media(max-width: 768px) {
    .kmb-hero-inner { grid-template-columns: 1fr; padding: 36px 28px; }
    .kmb-hero-img-wrap { display: none; }
}
@media(max-width: 540px) {
    .kmb-hero-inner { padding: 28px 20px; }
    .kmb-articles { grid-template-columns: 1fr; }
    .kmb-sidebar { flex-direction: column; }
    .kmb-hero-card { border-radius: 20px; }
}
</style>
@endpush

@section('content')
<div class="kmb">

  {{-- ── Hero Card ── --}}
  <div class="kmb-hero-wrap">
    <div class="kmb-cx">

      <div class="kmb-hero-card">
        <div class="kmb-hero-inner">
          <div>
            <span class="kmb-hero-eyebrow">Our Blog</span>
            <h1 class="kmb-hero-h1">Insights that move you forward</h1>
            <p class="kmb-hero-sub">Helpful guides, money tips, and the latest updates to help you send smarter and care more.</p>

            <form class="kmb-search" method="GET" action="{{ url()->current() }}">
              <input type="text" name="search" placeholder="Search articles..." value="{{ request('search') }}">
              <button type="submit" aria-label="Search">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
              </button>
            </form>
          </div>

          <div class="kmb-hero-img-wrap">
            <div class="kmb-hero-blob" aria-hidden="true"></div>
            <img src="{{ asset('assets/images/kinmoney/money-BEnZESby.jpeg') }}" alt="AkMoney blog" class="kmb-hero-img">
          </div>
        </div>
      </div>

      {{-- Category Tabs --}}
      <div class="kmb-tabs-wrap">
        <div class="kmb-tabs-inner">
          @foreach($categories as $cat)
          <button type="button" class="kmb-tab" data-cat="{{ $cat }}">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 7v14"/><path d="M3 18a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h5a4 4 0 0 1 4 4 4 4 0 0 1 4-4h5a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1h-6a3 3 0 0 0-3 3 3 3 0 0 0-3-3z"/></svg>
            {{ $cat }}
          </button>
          @endforeach
        </div>
      </div>

    </div>
  </div>

  {{-- ── Content + Sidebar ── --}}
  <div class="kmb-content-wrap">
    <div class="kmb-cx">
      <div class="kmb-grid">

        {{-- Articles --}}
        <div>
          <div class="kmb-articles-head">
            <h2>Latest Articles</h2>
          </div>

          <div class="kmb-articles" id="kmb-articles">
            @forelse($blogs as $post)
            <a href="{{ route('blog.details', $post->slug ?? 'blog') }}"
               class="kmb-card kmb-item"
               data-cat="{{ $post->data_values->category ?? 'General' }}">
              <img src="{{ asset('assets/images/frontend/blog/' . @$post->data_values->image) }}"
                   alt="{{ __($post->data_values->title ?? '') }}"
                   onerror="this.src='{{ asset('assets/images/kinmoney/money-BEnZESby.jpeg') }}';this.onerror=null;"
                   loading="lazy">
              <div class="kmb-card-overlay"></div>
              <div class="kmb-card-body">
                <div class="kmb-card-cat">{{ $post->data_values->category ?? 'Blog' }}</div>
                <h3 class="kmb-card-title">{{ __($post->data_values->title ?? '') }}</h3>
                <p class="kmb-card-desc">{{ \Illuminate\Support\Str::limit(strip_tags(__($post->data_values->description ?? '')), 100) }}</p>
                <div class="kmb-card-meta">
                  <span>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    {{ $post->created_at->format('M j, Y') }}
                  </span>
                  <span>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    {{ max(1, (int)(str_word_count(strip_tags(__($post->data_values->description ?? ''))) / 200)) }} min read
                  </span>
                </div>
              </div>
            </a>
            @empty
            <div class="kmb-empty">
              <p>No articles yet — check back soon!</p>
            </div>
            @endforelse
          </div>
        </div>

        {{-- Sidebar --}}
        <aside class="kmb-sidebar">

          {{-- Popular Articles --}}
          <div class="kmb-popular">
            <h3>Popular Articles</h3>
            <div class="kmb-popular-list">
              @forelse($popularPosts as $pop)
              <a href="{{ route('blog.details', $pop->slug ?? 'blog') }}" class="kmb-popular-item">
                <img src="{{ asset('assets/images/frontend/blog/' . @$pop->data_values->image) }}"
                     alt="{{ __($pop->data_values->title ?? '') }}"
                     onerror="this.src='{{ asset('assets/images/kinmoney/transfer-DF6w7EmE.jpeg') }}';this.onerror=null;"
                     loading="lazy">
                <div>
                  <h4>{{ __($pop->data_values->title ?? '') }}</h4>
                  <time>{{ $pop->created_at->format('M j, Y') }}</time>
                </div>
              </a>
              @empty
              @foreach([
                ['title'=>'How Long Does an International Transfer Take?','img'=>'transfer-DF6w7EmE.jpeg','date'=>'May 12, 2025'],
                ['title'=>'5 Ways to Get the Best Exchange Rate','img'=>'money-BEnZESby.jpeg','date'=>'Apr 28, 2025'],
                ['title'=>'Why AkMoney is FINTRAC Compliant','img'=>'mobile-PJkhsLpI.jpeg','date'=>'Apr 14, 2025'],
              ] as $ph)
              <a href="{{ url('/blog') }}" class="kmb-popular-item">
                <img src="{{ asset('assets/images/kinmoney/' . $ph['img']) }}" alt="{{ $ph['title'] }}" loading="lazy">
                <div>
                  <h4>{{ $ph['title'] }}</h4>
                  <time>{{ $ph['date'] }}</time>
                </div>
              </a>
              @endforeach
              @endforelse
            </div>
          </div>

          {{-- Newsletter --}}
          <div class="kmb-newsletter">
            <div class="kmb-newsletter-icon">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            </div>
            <h3>Never miss an update</h3>
            <p>Subscribe to get the latest insights, tips, and offers straight to your inbox.</p>
            <form class="kmb-nl-form" onsubmit="return false;">
              <input type="email" placeholder="Enter your email address">
              <button type="submit">Subscribe</button>
            </form>
          </div>

        </aside>

      </div>
    </div>
  </div>

</div>
@endsection

@push('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
  var tabs  = document.querySelectorAll('.kmb-tab');
  var items = document.querySelectorAll('.kmb-item');

  function filterCat(cat) {
    items.forEach(function(i) {
      i.style.display = (cat === 'All Articles' || i.dataset.cat === cat) ? '' : 'none';
    });
  }

  tabs.forEach(function(tab) {
    tab.addEventListener('click', function() {
      tabs.forEach(function(t) { t.classList.remove('active'); });
      tab.classList.add('active');
      filterCat(tab.dataset.cat);
    });
  });

  if (tabs[0]) tabs[0].click();
});
</script>
@endpush
