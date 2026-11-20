@extends($activeTemplate . 'layouts.frontend')

@push('style')
<style>
/* ─── Override global style.css element rules inside blog detail ─── */
.bd-page a                { text-decoration: none !important; color: inherit !important; }
.bd-page a:hover          { text-decoration: none !important; color: inherit !important; }
.bd-page p                { color: inherit !important; margin-bottom: 0 !important; line-height: inherit !important; }
.bd-page h1, .bd-page h2,
.bd-page h3, .bd-page h4,
.bd-page h5, .bd-page h6  { font-family: inherit !important; text-transform: none !important; margin: 0 !important; color: inherit !important; font-weight: inherit !important; line-height: inherit !important; }
.bd-page img              { max-width: 100% !important; height: auto; }

/* ─── Shared ─── */
.bd-wrap { max-width: 72rem; margin: 0 auto; padding: 0 1.5rem; }
.bd-ey   { font-size: .68rem; font-weight: 800; letter-spacing: .18em; text-transform: uppercase; color: #A71B47; }

/* ─── Hero grid ─── */
.bd-hero-g {
  display: grid;
  grid-template-columns: 1fr 290px;
  gap: 2rem; align-items: end;
}
@media (max-width: 860px) { .bd-hero-g { grid-template-columns: 1fr; } }

/* ─── Back ─── */
.bd-back {
  display: inline-flex; align-items: center; gap: .4rem;
  font-size: .8rem; font-weight: 800; letter-spacing: .04em; text-transform: uppercase;
  color: #A71B47; text-decoration: none;
  transition: gap .2s;
}
.bd-back:hover { gap: .65rem; }

/* ─── Badge ─── */
.bd-badge {
  display: inline-flex; align-items: center; gap: .45rem;
  background: #fff; border: 1px solid rgba(167,27,71,.12);
  border-radius: 100px; padding: .35rem 1rem;
  font-size: .72rem; font-weight: 800; letter-spacing: .07em; text-transform: uppercase;
  color: #A71B47; box-shadow: 0 2px 12px rgba(167,27,71,.07);
}
.bd-badge-dot { width: 6px; height: 6px; border-radius: 50%; background: #A71B47; flex-shrink: 0; }

/* ─── Brief card (dark) ─── */
.bd-brief {
  border-radius: 1.5rem; background: #060c20;
  padding: 1.6rem; box-shadow: 0 8px 40px rgba(0,20,80,.22);
}
.bd-brief-ey { font-size: .68rem; font-weight: 800; letter-spacing: .18em; text-transform: uppercase; color: #60a5fa; }
.bd-brief-row {
  display: flex; justify-content: space-between; align-items: baseline;
  gap: 1rem; padding: .8rem 0;
  border-bottom: 1px solid rgba(255,255,255,.07);
  font-size: .86rem;
}
.bd-brief-row:last-child { border-bottom: none; padding-bottom: 0; }
.bd-brief-lbl { color: rgba(255,255,255,.38); font-weight: 600; }
.bd-brief-val { color: #fff; font-weight: 800; text-align: right; }

/* ─── Hero image ─── */
.bd-hero-img {
  border-radius: 1.75rem; overflow: hidden;
  box-shadow: 0 20px 60px rgba(0,30,120,.13);
  position: relative;
}
/* Maintain 1245:840 ratio so the hero image always shows in full proportions */
.bd-hero-img::before {
  content: '';
  display: block;
  padding-top: calc(840 / 1245 * 100%);
}
.bd-hero-img img {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

/* ─── Body grid ─── */
.bd-body-g {
  display: grid;
  grid-template-columns: 200px 1fr;
  gap: 2rem; align-items: start;
}
@media (max-width: 1000px) { .bd-body-g { grid-template-columns: 1fr; } }

/* ─── TOC ─── */
.bd-toc {
  position: sticky; top: 6.5rem;
  border-radius: 1.4rem; background: #fff;
  border: 1px solid rgba(167,27,71,.09);
  padding: 1.4rem; box-shadow: 0 4px 20px rgba(167,27,71,.05);
}
.bd-toc-ey { font-size: .68rem; font-weight: 800; letter-spacing: .18em; text-transform: uppercase; color: #A71B47; }
.bd-toc ul  { margin-top: .9rem; list-style: none; padding: 0; display: flex; flex-direction: column; gap: .05rem; }
.bd-toc li a {
  display: flex; align-items: center; gap: .55rem;
  font-size: .85rem; font-weight: 700; color: #64748b;
  text-decoration: none; padding: .45rem .55rem;
  border-radius: .55rem; transition: all .18s;
}
.bd-toc li a::before {
  content: ''; width: 5px; height: 5px; border-radius: 50%;
  background: #cbd5e1; flex-shrink: 0; transition: background .18s;
}
.bd-toc li a:hover { color: #A71B47; background: rgba(167,27,71,.05); }
.bd-toc li a:hover::before { background: #A71B47; }

/* ─── Article card ─── */
.bd-article {
  border-radius: 1.75rem; background: #fff;
  border: 1px solid rgba(167,27,71,.09);
  padding: 2.25rem; box-shadow: 0 4px 24px rgba(167,27,71,.05);
}
@media (min-width: 640px) { .bd-article { padding: 2.75rem 3.25rem; } }

.prose-content { color: #475569; font-size: 1.03rem; line-height: 1.9; }
.prose-content h2 {
  font-size: clamp(1.35rem, 2.2vw, 1.75rem);
  font-weight: 900; color: #0f172a;
  margin-top: 2.25rem; margin-bottom: .8rem;
  letter-spacing: -.03em; line-height: 1.15;
}
.prose-content h2:first-child { margin-top: 0; }
.prose-content p { margin-bottom: 1.2rem; }
.prose-content p:last-child { margin-bottom: 0; }
.prose-content p:first-of-type { font-size: 1.1rem; color: #334155; }
.prose-content ul, .prose-content ol { padding-left: 1.5rem; margin-bottom: 1.2rem; }
.prose-content li { margin-bottom: .5rem; }
.prose-content strong { font-weight: 700; color: #0f172a; }
.prose-content a { color: #A71B47; text-decoration: underline; }

/* ─── Inline CTA ─── */
.bd-icta {
  margin-top: 2.25rem; border-radius: 1.4rem;
  background: linear-gradient(135deg, #eef5ff 0%, #dbeafe 100%);
  border: 1px solid rgba(167,27,71,.12);
  padding: 1.6rem 1.8rem;
  display: flex; flex-direction: column; gap: 1.1rem;
}
@media (min-width: 560px) { .bd-icta { flex-direction: row; align-items: center; justify-content: space-between; } }
.bd-icta-ey  { font-size: .68rem; font-weight: 800; letter-spacing: .18em; text-transform: uppercase; color: #A71B47; }
.bd-icta-ttl { margin-top: .35rem; font-size: 1.2rem; font-weight: 900; color: #0f172a; line-height: 1.25; }
.bd-icta-sub { margin-top: .3rem; color: #64748b; font-size: .87rem; }
.bd-icta-btn {
  flex-shrink: 0; display: inline-block;
  padding: .82rem 1.6rem; border-radius: .8rem;
  background: #A71B47; color: #fff;
  font-weight: 900; font-size: .86rem; text-decoration: none; white-space: nowrap;
  transition: opacity .2s;
}
.bd-icta-btn:hover { opacity: .88; }

/* ─── Related articles ─── */
.bd-rel-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
}
@media (max-width: 860px) { .bd-rel-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 540px) { .bd-rel-grid { grid-template-columns: 1fr; } }

.rel-card {
  position: relative; border-radius: 1.4rem;
  overflow: hidden; display: block; text-decoration: none;
  background: #0a0f1e; aspect-ratio: 3/2;
  transition: transform .45s cubic-bezier(.22,1,.36,1);
}
.rel-card img {
  position: absolute; inset: 0;
  width: 100%; height: 100%; object-fit: cover;
  transition: transform .65s cubic-bezier(.4,0,.2,1);
}
.rel-card-ov {
  position: absolute; inset: 0;
  background: linear-gradient(to top,
    rgba(4,8,24,.94) 0%,
    rgba(4,8,24,.48) 50%,
    rgba(4,8,24,.08) 100%);
  transition: opacity .3s;
}
.rel-card:hover img          { transform: scale(1.06); }
.rel-card:hover .rel-card-ov { opacity: .85; }
.rel-card:hover              { transform: translateY(-4px); }
.rel-card-body {
  position: relative; z-index: 2;
  height: 100%; display: flex; flex-direction: column; justify-content: flex-end;
  padding: 1.3rem;
}
.rel-cat {
  display: inline-flex; align-items: center; gap: .3rem;
  background: rgba(167,27,71,.8); backdrop-filter: blur(6px);
  border-radius: 100px; padding: .2rem .65rem;
  font-size: .64rem; font-weight: 800; letter-spacing: .08em; text-transform: uppercase;
  color: #fff; width: fit-content; margin-bottom: .5rem;
}
.rel-cat-dot { width: 4px; height: 4px; border-radius: 50%; background: #93c5fd; flex-shrink: 0; }
.rel-ttl {
  font-size: .92rem; font-weight: 900; color: #fff;
  line-height: 1.25; letter-spacing: -.015em;
  transition: opacity .2s;
}
.rel-card:hover .rel-ttl { opacity: .85; }
.rel-foot {
  margin-top: .6rem; padding-top: .6rem;
  border-top: 1px solid rgba(255,255,255,.1);
  display: flex; align-items: center; justify-content: space-between;
}
.rel-date { font-size: .75rem; font-weight: 700; color: rgba(255,255,255,.38); }
.rel-arrow {
  width: 28px; height: 28px; border-radius: 50%;
  background: #fff; color: #A71B47;
  display: grid; place-items: center;
  font-size: .78rem; font-weight: 900;
  flex-shrink: 0; transition: transform .2s;
}
.rel-card:hover .rel-arrow { transform: translateX(3px); }

/* ─── Page ─── */
.bd-page { background: #f4f7fe; }
.bd-dotbg {
  background-image: radial-gradient(rgba(167,27,71,.065) 1px, transparent 1px);
  background-size: 26px 26px;
}
</style>
@endpush

@section('content')
<main class="bd-page overflow-hidden">

  {{-- Hero --}}
  {{-- 78px fixed header + 2.25rem breathing room --}}
  <section class="bd-dotbg" style="padding: calc(78px + 2.25rem) 0 1.5rem; position: relative;">
    <div style="position:absolute;top:0;right:0;width:480px;height:480px;border-radius:50%;background:rgba(147,197,253,.2);filter:blur(110px);transform:translate(30%,-30%);pointer-events:none;"></div>
    <div class="bd-wrap" style="position: relative;">
      <a href="{{ route('blog') }}" class="bd-back">← Back to Blog</a>

      <div class="bd-hero-g" style="margin-top: 1.75rem;">
        <div>
          <div class="bd-badge">
            <div class="bd-badge-dot"></div>
            {{ __('Blog') }} &middot; {{ $blog->created_at->format('M Y') }}
          </div>
          <h1 style="margin-top:1rem;font-size:clamp(1.9rem,4.2vw,3.2rem);font-weight:900;letter-spacing:-.04em;color:#0f172a;line-height:1.06;">
            {{ __($blog->data_values->title ?? '') }}
          </h1>
          @php
            $desc    = $blog->data_values->description ?? '';
            $excerpt = \Illuminate\Support\Str::limit(strip_tags($desc), 200);
          @endphp
          <p style="margin-top:.9rem;font-size:1rem;color:#64748b;line-height:1.8;max-width:36rem;">
            {{ __($excerpt) }}
          </p>
        </div>

        <div class="bd-brief">
          <p class="bd-brief-ey">Article Brief</p>
          <div style="margin-top:.85rem;">
            <div class="bd-brief-row">
              <span class="bd-brief-lbl">Category</span>
              <span class="bd-brief-val">{{ __('Blog') }}</span>
            </div>
            <div class="bd-brief-row">
              <span class="bd-brief-lbl">Published</span>
              <span class="bd-brief-val">{{ $blog->created_at->format('M j, Y') }}</span>
            </div>
            <div class="bd-brief-row">
              <span class="bd-brief-lbl">Topic</span>
              <span class="bd-brief-val">{{ __('Global Transfers') }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="bd-hero-img" style="margin-top: 1.75rem;">
        <img src="{{ asset('assets/images/frontend/blog/' . @$blog->data_values->image) }}"
             alt="{{ __($blog->data_values->title ?? '') }}"
             onerror="this.src='{{ asset('assets/images/frontend/blog/bannerbg.png') }}';this.onerror=null;">
      </div>
    </div>
  </section>

  {{-- Article body --}}
  <section style="padding: 2rem 0 0;">
    <div class="bd-wrap">
      <div class="bd-body-g">

        {{-- TOC --}}
        <aside class="hidden lg:block">
          <div class="bd-toc">
            <p class="bd-toc-ey">In This Article</p>
            <ul id="bd-toc-list">
              <li><a href="#bd-article">Overview</a></li>
              <li><a href="#bd-article">Key Details</a></li>
              <li><a href="#bd-article">Transfer Tips</a></li>
              <li><a href="#bd-article">Conclusion</a></li>
            </ul>
          </div>
        </aside>

        <article class="bd-article" id="bd-article">
          <div class="prose-content">
            {!! $desc !!}
          </div>
          <div class="bd-icta">
            <div>
              <p class="bd-icta-ey">{!! siteNameHtml() !!}</p>
              <h2 class="bd-icta-ttl">Ready to send money globally?</h2>
              <p class="bd-icta-sub">Start a secure international transfer with confidence.</p>
            </div>
            <a href="{{ route('user.login') }}" class="bd-icta-btn">Send Money Now</a>
          </div>
        </article>
      </div>
    </div>
  </section>

  {{-- Meta strip --}}
  <div style="max-width:72rem;margin:0 auto;padding:1.5rem 1.5rem;display:flex;align-items:center;justify-content:space-between;border-top:1px solid #e2e8f0;flex-wrap:wrap;gap:12px;">
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
      <span style="display:inline-flex;align-items:center;font-size:12px;font-weight:600;padding:5px 14px;border-radius:100px;border:1.5px solid #e2e8f0;color:#555;">Blog</span>
      <span style="display:inline-flex;align-items:center;font-size:12px;font-weight:600;padding:5px 14px;border-radius:100px;border:1.5px solid #e2e8f0;color:#555;">5 min read</span>
      <span style="display:inline-flex;align-items:center;font-size:12px;font-weight:600;padding:5px 14px;border-radius:100px;border:1.5px solid #e2e8f0;color:#555;">{{ $blog->created_at->format('M Y') }}</span>
    </div>
    <div style="display:flex;align-items:center;gap:8px;font-size:13px;font-weight:600;color:#555;">
      <span>Share:</span>
      <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}" target="_blank" style="background:#f3f4f6;border:none;border-radius:8px;padding:6px 12px;font-size:12px;font-weight:600;color:#333;text-decoration:none;">Twitter</a>
      <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url()->current()) }}" target="_blank" style="background:#f3f4f6;border:none;border-radius:8px;padding:6px 12px;font-size:12px;font-weight:600;color:#333;text-decoration:none;">LinkedIn</a>
      <button onclick="navigator.clipboard.writeText(window.location.href)" style="background:#f3f4f6;border:none;border-radius:8px;padding:6px 12px;font-size:12px;font-weight:600;color:#333;cursor:pointer;font-family:inherit;">Copy link</button>
    </div>
  </div>

  {{-- Related articles --}}
  @if(isset($recentBlogs) && $recentBlogs->count())
  <section style="padding: 3rem 0 0;">
    <div class="bd-wrap">
      <div style="margin-bottom: 1.1rem;">
        <p class="bd-ey">Keep Reading</p>
        <h2 style="margin-top:.4rem;font-size:clamp(1.5rem,2.8vw,2.2rem);font-weight:900;color:#0f172a;letter-spacing:-.03em;line-height:1.1;">Related Articles</h2>
      </div>
      <div class="bd-rel-grid">
        @foreach($recentBlogs->take(3) as $item)
          <a href="{{ route('blog.details', $item->slug) }}" class="rel-card">
            <img src="{{ asset('assets/images/frontend/blog/' . @$item->data_values->image) }}"
                 alt="{{ __($item->data_values->title ?? '') }}"
                 onerror="this.src='{{ asset('assets/images/frontend/blog/bannerbg.png') }}';this.onerror=null;">
            <div class="rel-card-ov"></div>
            <div class="rel-card-body">
              <div class="rel-cat"><span class="rel-cat-dot"></span>{{ __('Blog') }}</div>
              <h3 class="rel-ttl">{{ __($item->data_values->title ?? '') }}</h3>
              <div class="rel-foot">
                <span class="rel-date">{{ $item->created_at->format('M Y') }}</span>
                <div class="rel-arrow">→</div>
              </div>
            </div>
          </a>
        @endforeach
      </div>
    </div>
  </section>
  @endif

  <div style="height: 5rem;"></div>

</main>
@endsection

@push('fbComment')
    @php echo loadExtension('fb-comment') @endphp
@endpush
