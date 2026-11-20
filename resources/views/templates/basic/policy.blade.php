@extends($activeTemplate . 'layouts.frontend')

@push('style')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    .pp-wrap *, .pp-wrap *::before, .pp-wrap *::after { box-sizing: border-box; margin: 0; padding: 0; }
    .pp-wrap {
      font-family: 'Plus Jakarta Sans', sans-serif;
      background: #fff;
      color: #0C0C0C;
    }
    .pp-wrap h1,.pp-wrap h2,.pp-wrap h3,.pp-wrap p,.pp-wrap span,
    .pp-wrap a,.pp-wrap div,.pp-wrap li,.pp-wrap td,.pp-wrap th {
      font-family: 'Plus Jakarta Sans', sans-serif !important;
    }

    /* Hero */
    .pp-hero {
      padding: 100px 32px 64px;
      border-bottom: 1px solid #EBEBEB;
      position: relative; overflow: hidden;
      background: #fff;
    }
    .pp-hero::before {
      content: ''; position: absolute; inset: 0;
      background-image: radial-gradient(circle, #E5E7EB 1px, transparent 1px);
      background-size: 28px 28px; opacity: 0.5; pointer-events: none;
    }
    .pp-hero-glow {
      position: absolute; top: -120px; right: -120px;
      width: 400px; height: 400px; border-radius: 50%; pointer-events: none;
      background: radial-gradient(circle, rgba(167,27,71,0.06) 0%, transparent 70%);
    }
    .pp-hero-inner {
      max-width: 1200px; margin: 0 auto;
      position: relative; z-index: 1;
    }
    .pp-hero-badge {
      display: inline-flex; align-items: center; gap: 8px;
      background: rgba(167,27,71,0.06);
      border: 1px solid rgba(167,27,71,0.2);
      border-radius: 99px; padding: 5px 14px;
      margin-bottom: 24px;
      font-size: 11px; font-weight: 700; color: #A71B47;
      letter-spacing: 0.12em; text-transform: uppercase;
    }
    .pp-hero-badge-dot {
      width: 6px; height: 6px; border-radius: 50%;
      background: #A71B47; display: inline-block;
    }
    .pp-hero-grid {
      display: grid; grid-template-columns: 1fr auto;
      gap: 48px; align-items: end;
    }
    .pp-hero h1 {
      font-size: clamp(36px, 5vw, 64px);
      font-weight: 800; line-height: 1.05;
      margin: 0 0 16px; letter-spacing: -0.03em; color: #0C0C0C;
    }
    .pp-hero h1 span { color: #A71B47; }
    .pp-hero-desc {
      font-size: 16px; color: #444; max-width: 600px;
      line-height: 1.75; margin: 0 0 28px; font-weight: 400;
    }
    .pp-hero-meta {
      display: flex; align-items: center; gap: 20px; flex-wrap: wrap;
    }
    .pp-hero-meta-item { font-size: 13px; font-weight: 600; color: #333; }
    .pp-hero-meta-sep {
      width: 1px; height: 14px; background: #D1D5DB; display: inline-block;
    }
    .pp-hero-stats { display: flex; flex-direction: column; gap: 12px; flex-shrink: 0; }
    .pp-stat-card {
      background: #F7F7F7; border: 1px solid #EBEBEB;
      border-radius: 12px; padding: 12px 20px;
      min-width: 120px; text-align: center;
    }
    .pp-stat-num { font-size: 22px; font-weight: 800; color: #0C0C0C; letter-spacing: -0.03em; }
    .pp-stat-label {
      font-size: 11px; font-weight: 600; color: #777;
      text-transform: uppercase; letter-spacing: 0.1em; margin-top: 2px;
    }

    /* Layout */
    .pp-body {
      max-width: 1200px; margin: 0 auto;
      padding: 64px 32px 120px;
      display: grid;
      grid-template-columns: 220px 1fr;
      gap: 64px; align-items: start;
    }

    /* Sidebar */
    .pp-sidebar { position: sticky; top: 88px; }
    .pp-toc-label {
      font-size: 10px; font-weight: 700; color: #777;
      letter-spacing: 0.14em; text-transform: uppercase;
      margin-bottom: 12px; margin-left: 12px;
    }
    .pp-toc-nav { display: flex; flex-direction: column; gap: 2px; }
    .pp-toc-link {
      display: block; text-decoration: none;
      padding: 9px 12px; border-radius: 8px;
      font-size: 13px; font-weight: 500; color: #444;
      border-left: 3px solid transparent;
      transition: all 0.15s; line-height: 1.4;
    }
    .pp-toc-link:hover { color: #0C0C0C; background: #F5F5F5; }
    .pp-toc-link.active {
      background: #FFF1F2; border-left-color: #A71B47;
      color: #A71B47; font-weight: 700;
    }
    .pp-sidebar-meta {
      margin-top: 28px; padding: 14px 16px;
      background: #F7F7F7; border-radius: 12px;
      border: 1px solid #EBEBEB;
    }
    .pp-sidebar-meta p { font-size: 13px; color: #333; font-weight: 500; }
    .pp-sidebar-meta .pp-sidebar-meta-label {
      font-size: 11px; font-weight: 700; color: #777;
      text-transform: uppercase; letter-spacing: 0.1em;
      margin-bottom: 4px;
    }

    /* Main content */
    .pp-main { display: flex; flex-direction: column; gap: 0; }

    /* Policy content styles */
    .pp-content { font-size: 15px; color: #222; line-height: 1.85; }
    .pp-content h2 {
      font-size: 22px; font-weight: 800; color: #0C0C0C;
      letter-spacing: -0.02em; margin: 56px 0 20px;
      padding-bottom: 20px;
      border-bottom: 1.5px solid #EBEBEB;
      display: flex; align-items: center; gap: 14px;
    }
    .pp-content h2:first-child { margin-top: 0; }
    .pp-content h3 {
      font-size: 14px; font-weight: 700; color: #A71B47;
      text-transform: uppercase; letter-spacing: 0.12em;
      margin: 0 0 8px;
    }
    .pp-content p { margin-bottom: 16px; }
    .pp-content ul, .pp-content ol { padding-left: 24px; margin-bottom: 16px; }
    .pp-content li { margin-bottom: 8px; }
    .pp-content strong { font-weight: 700; color: #0C0C0C; }
    .pp-content a { color: #A71B47; text-decoration: underline; }

    /* Info card style for paragraphs under h3 */
    .pp-card {
      background: #FFFFFF;
      border: 1.5px solid #EBEBEB;
      border-radius: 14px;
      padding: 24px 28px;
      position: relative;
      margin-bottom: 14px;
      transition: border-color 0.2s, box-shadow 0.2s;
    }
    .pp-card:hover {
      border-color: rgba(167,27,71,0.25);
      box-shadow: 0 4px 24px rgba(167,27,71,0.06);
    }
    .pp-card::before {
      content: ''; position: absolute;
      left: 0; top: 16px; bottom: 16px; width: 3px;
      background: #A71B47; border-radius: 0 3px 3px 0;
    }

    /* Contact box */
    .pp-contact-box {
      background: #0C0C0C; border-radius: 20px;
      padding: 40px 44px; position: relative; overflow: hidden;
      margin-top: 64px;
    }
    .pp-contact-box-glow {
      position: absolute; top: -60px; right: -60px;
      width: 240px; height: 240px; border-radius: 50%;
      background: radial-gradient(circle, rgba(167,27,71,0.2) 0%, transparent 70%);
      pointer-events: none;
    }
    .pp-contact-box-dots {
      position: absolute; inset: 0;
      background-image: radial-gradient(circle, rgba(255,255,255,0.04) 1px, transparent 1px);
      background-size: 24px 24px; pointer-events: none;
    }
    .pp-contact-box-inner { position: relative; z-index: 1; }
    .pp-contact-box-badge {
      display: inline-flex; align-items: center; gap: 6px;
      background: rgba(167,27,71,0.15);
      border: 1px solid rgba(167,27,71,0.3);
      border-radius: 99px; padding: 4px 12px;
      margin-bottom: 16px;
      font-size: 11px; font-weight: 700; color: #A71B47;
      letter-spacing: 0.1em; text-transform: uppercase;
    }
    .pp-contact-box h3 {
      font-size: 22px; font-weight: 800; color: #fff;
      letter-spacing: -0.02em; margin-bottom: 10px;
    }
    .pp-contact-box p {
      font-size: 15px; color: rgba(255,255,255,0.75);
      margin-bottom: 24px; line-height: 1.75;
    }
    .pp-contact-box p a { color: #A71B47; font-weight: 600; text-decoration: none; }
    .pp-related-links { display: flex; gap: 10px; flex-wrap: wrap; }
    .pp-related-link {
      font-size: 13px; font-weight: 600; color: #fff;
      text-decoration: none; padding: 8px 16px;
      border: 1px solid rgba(255,255,255,0.2); border-radius: 8px;
      transition: all 0.15s;
    }
    .pp-related-link:hover { background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.4); color: #fff; }

    /* Footer note */
    .pp-footnote {
      font-size: 13px; color: #555; line-height: 1.7;
      padding: 20px 0; border-top: 1px solid #EBEBEB;
      margin-top: 32px;
    }

    @media (max-width: 1023px) {
      .pp-body { grid-template-columns: 1fr; }
      .pp-sidebar { display: none; }
      .pp-hero-grid { grid-template-columns: 1fr; }
      .pp-hero-stats { flex-direction: row; }
    }
    @media (max-width: 640px) {
      .pp-hero { padding: 80px 20px 48px; }
      .pp-body { padding: 40px 20px 80px; }
      .pp-contact-box { padding: 28px 20px; }
      .pp-hero-meta { flex-direction: column; gap: 8px; }
      .pp-hero-meta-sep { display: none; }
    }
</style>
@endpush

@section('content')
<div class="pp-wrap">

    {{-- Hero --}}
    <div class="pp-hero">
        <div class="pp-hero-glow"></div>
        <div class="pp-hero-inner">
            <div class="pp-hero-badge">
                <span class="pp-hero-badge-dot"></span>
                {{ __('Legal Document') }}
            </div>
            <div class="pp-hero-grid">
                <div>
                    <h1>{{ __($policy->data_values->title ?? $pageTitle) }}</h1>
                    @if(@$policy->data_values->sub_heading)
                    <p class="pp-hero-desc">{{ __($policy->data_values->sub_heading) }}</p>
                    @endif
                    <div class="pp-hero-meta">
                        <span class="pp-hero-meta-item">{{ __('Effective: January 1, 2025') }}</span>
                        <span class="pp-hero-meta-sep"></span>
                        <span class="pp-hero-meta-item">{{ __('PIPEDA Compliant') }}</span>
                        <span class="pp-hero-meta-sep"></span>
                        <span class="pp-hero-meta-item">{{ __('FINTRAC MSB Registered') }}</span>
                    </div>
                </div>
                <div class="pp-hero-stats">
                    <div class="pp-stat-card">
                        <div class="pp-stat-num">7 yrs</div>
                        <div class="pp-stat-label">Retention</div>
                    </div>
                    <div class="pp-stat-card">
                        <div class="pp-stat-num">30d</div>
                        <div class="pp-stat-label">Response SLA</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Body --}}
    <div class="pp-body">

        {{-- Sidebar --}}
        <aside class="pp-sidebar">
            <p class="pp-toc-label">{{ __('Contents') }}</p>
            <nav class="pp-toc-nav" id="pp-toc"></nav>
            <div class="pp-sidebar-meta">
                <p class="pp-sidebar-meta-label">{{ __('Last Updated') }}</p>
                <p>{{ __('January 1, 2025') }}</p>
            </div>
        </aside>

        {{-- Main --}}
        <main class="pp-main">
            <div class="pp-content" id="pp-content">
                {!! $policy->data_values->details !!}
            </div>

            <p class="pp-footnote">
                {!! __('This policy is governed by the <strong>Personal Information Protection and Electronic Documents Act (PIPEDA)</strong> and applicable provincial privacy legislation.') !!}
            </p>
        </main>
    </div>

</div>

@push('script')
<script>
// Auto-build TOC from h2 headings in the content
(function() {
    var content = document.getElementById('pp-content');
    var toc = document.getElementById('pp-toc');
    if (!content || !toc) return;
    var headings = content.querySelectorAll('h2');
    headings.forEach(function(h, i) {
        var id = 'pp-section-' + i;
        h.id = id;
        var a = document.createElement('a');
        a.href = '#' + id;
        a.className = 'pp-toc-link';
        a.textContent = h.textContent.trim();
        a.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById(id).scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
        toc.appendChild(a);
    });

    // Highlight active section on scroll
    var links = toc.querySelectorAll('.pp-toc-link');
    window.addEventListener('scroll', function() {
        var fromTop = window.scrollY + 110;
        var current = null;
        headings.forEach(function(h) {
            if (h.offsetTop <= fromTop) current = h.id;
        });
        links.forEach(function(a) {
            a.classList.toggle('active', a.getAttribute('href') === '#' + current);
        });
    });
})();
</script>
@endpush

@endsection
