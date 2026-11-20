@php
    $blogContent  = getContent('blog.content', true);
    $blogElements = getContent('blog.element', false, 3);
@endphp

@push('style')
<style>
.km-blog1{padding:80px 0;background:#fff;font-family:'Work Sans',sans-serif;}
.km-blog1__cx{width:min(calc(100% - 48px),1280px);margin-inline:auto;}
.km-blog1__top{display:flex;align-items:flex-end;justify-content:space-between;gap:24px;margin-bottom:48px;flex-wrap:wrap;}
.km-blog1__eyebrow{display:inline-block;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.20em;color:#A71B47;margin-bottom:16px;}
.km-blog1__h2{margin:0;font-size:clamp(26px,3vw,44px);font-weight:700;line-height:1.15;letter-spacing:-1px;color:#0F4650;}
.km-blog1__h2 em{font-style:italic;color:#A71B47;}
.km-blog1__sub{margin:16px 0 0;max-width:640px;font-size:15px;color:#4a5568;}
.km-blog1__grid{display:grid;grid-template-columns:repeat(3,1fr);gap:24px;}
.km-blog1__card{position:relative;border-radius:16px;overflow:hidden;cursor:pointer;text-decoration:none;display:block;box-shadow:0 4px 20px rgba(0,0,0,0.08);transition:transform .3s ease,box-shadow .3s ease;}
/* Enforce 1245:840 aspect ratio */
.km-blog1__card::before{content:'';display:block;padding-top:calc(840 / 1245 * 100%);}
.km-blog1__card img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;transition:transform .7s;}
.km-blog1__card:hover{transform:translateY(-5px);box-shadow:0 16px 48px rgba(0,0,0,0.14);}
.km-blog1__card:hover img{transform:scale(1.08);}
.km-blog1__overlay{position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.85) 0%,rgba(0,0,0,.35) 55%,transparent 100%);}
.km-blog1__body{position:absolute;bottom:0;left:0;right:0;padding:20px;color:#fff;}
.km-blog1__tag{display:inline-block;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;background:#A71B47;color:#fff;padding:3px 10px;border-radius:4px;margin-bottom:10px;}
.km-blog1__card h3{margin:0 0 8px;font-size:16px;font-weight:700;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.km-blog1__meta{display:flex;align-items:center;gap:12px;font-size:11px;color:rgba(255,255,255,.60);}
.km-blog1__meta span{display:flex;align-items:center;gap:4px;}
@media(max-width:900px){.km-blog1__grid{grid-template-columns:repeat(2,1fr);}}
@media(max-width:580px){.km-blog1__grid{grid-template-columns:1fr;}.km-blog1{padding:56px 0;}}
</style>
@endpush

<section class="km-blog1">
    <div class="km-blog1__cx">
        <div class="km-blog1__top">
            <div>
                <span class="km-blog1__eyebrow">— Our Blog</span>
                <h2 class="km-blog1__h2">{{ __(@$blogContent->data_values->heading ?? 'Latest <em>Articles</em>') }}</h2>
                @if(@$blogContent->data_values->description)
                    <p class="km-blog1__sub">{{ __($blogContent->data_values->description) }}</p>
                @endif
            </div>
            <a href="{{ url('/blog') }}" style="display:inline-flex;align-items:center;gap:8px;padding:12px 24px;border-radius:8px;background:#0F4650;color:#fff;font-size:14px;font-weight:600;text-decoration:none;white-space:nowrap;opacity:1;transition:opacity .2s;">
                View all articles
                <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" width="14" height="14"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
            </a>
        </div>

        <div class="km-blog1__grid">
            @forelse($blogElements ?? [] as $blogElement)
                <a href="{{ route('blog.details', $blogElement->slug) }}" class="km-blog1__card">
                    <img src="{{ asset('assets/images/frontend/blog/' . @$blogElement->data_values->image) }}"
                         alt="{{ __($blogElement->data_values->title ?? '') }}"
                         onerror="this.src='{{ asset('assets/images/frontend/blog/bannerbg.png') }}';this.onerror=null;"
                         loading="lazy">
                    <div class="km-blog1__overlay"></div>
                    <div class="km-blog1__body">
                        <span class="km-blog1__tag">{{ __($blogElement->data_values->tag ?? 'Blog') }}</span>
                        <h3>{{ __($blogElement->data_values->title ?? '') }}</h3>
                        <div class="km-blog1__meta">
                            <span>
                                <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" width="11" height="11"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                {{ $blogElement->created_at->format('M d, Y') }}
                            </span>
                        </div>
                    </div>
                </a>
            @empty
                <p style="color:#94a3b8;grid-column:1/-1;text-align:center;padding:40px 0;">No blog posts yet.</p>
            @endforelse
        </div>
    </div>
</section>
