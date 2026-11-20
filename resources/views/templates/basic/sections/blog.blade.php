@php
    $blogPosts = getContent('blog.element', false, 2);

    $staticBlogs = [
        ['tag'=>'Guides','title'=>'How to send money abroad without hidden fees','excerpt'=>'Learn the tricks banks don\'t tell you about international transfers and save up to 80% on fees.','date'=>'May 12, 2025','read'=>'5 min read','img'=>asset('assets/images/kinmoney/money-BEnZESby.jpeg'),'url'=>url('/blog')],
        ['tag'=>'Education','title'=>'Why exchange rates change and how to get the best deal','excerpt'=>'Understanding market movements and timing your transfers for maximum value.','date'=>'May 5, 2025','read'=>'4 min read','img'=>asset('assets/images/kinmoney/transfer-DF6w7EmE.jpeg'),'url'=>url('/blog')],
    ];
@endphp

@push('style')
<style>
.km-blog{padding:80px 0;background:#fff;font-family:'Work Sans',sans-serif;}
.km-blog__cx{width:min(calc(100% - 48px),1280px);margin-inline:auto;}
.km-blog__top{display:flex;align-items:flex-end;justify-content:space-between;gap:24px;margin-bottom:48px;flex-wrap:wrap;}
.km-blog__eyebrow{display:inline-block;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.20em;color:#A71B47;margin-bottom:16px;}
.km-blog__h2{margin:0;font-size:clamp(26px,3vw,44px);font-weight:700;line-height:1.15;letter-spacing:-1px;color:#0F4650;}
.km-blog__h2 em{font-style:italic;color:#A71B47;}
.km-blog__sub{margin:16px 0 0;max-width:640px;font-size:15px;color:#4a5568;}
.km-blog__all{display:inline-flex;align-items:center;gap:8px;padding:12px 24px;border-radius:8px;background:#0F4650;color:#fff;font-size:14px;font-weight:600;text-decoration:none;white-space:nowrap;transition:opacity .2s;}
.km-blog__all:hover{opacity:.88;color:#fff;}
.km-blog__grid{display:flex;gap:24px;align-items:flex-start;}
.km-blog__card{position:relative;border-radius:16px;overflow:hidden;cursor:pointer;text-decoration:none;display:block;}
.km-blog__card--sm{flex:0 0 38%;}
.km-blog__card--lg{flex:1;}
/* Enforce 1245:840 aspect ratio via padding trick so height is proportional to width */
.km-blog__card::before{content:'';display:block;padding-top:calc(840 / 1245 * 100%);}
.km-blog__card img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;transition:transform .7s;}
.km-blog__card:hover img{transform:scale(1.08);}
.km-blog__card .km-blog__overlay{position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.80) 0%,rgba(0,0,0,.30) 50%,transparent 100%);}
.km-blog__card .km-blog__body{position:absolute;bottom:0;left:0;right:0;padding:24px;color:#fff;}
.km-blog__tag{display:inline-block;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;background:#A71B47;color:#fff;padding:3px 10px;border-radius:4px;margin-bottom:12px;}
.km-blog__card h3{margin:0 0 8px;font-size:18px;font-weight:700;line-height:1.3;}
.km-blog__card--lg h3{font-size:22px;}
.km-blog__excerpt{margin:0 0 12px;font-size:13px;color:rgba(255,255,255,.80);line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.km-blog__meta{display:flex;align-items:center;gap:16px;font-size:12px;color:rgba(255,255,255,.60);margin-bottom:12px;}
.km-blog__meta span{display:flex;align-items:center;gap:4px;}
.km-blog__readmore{display:inline-flex;align-items:center;gap:6px;font-size:14px;font-weight:600;color:#fff;}
@media(max-width:1023px){.km-blog__grid{flex-direction:column;}.km-blog__card--sm,.km-blog__card--lg{flex:none;width:100%;}.km-blog{padding:56px 0;}}
</style>
@endpush

<section class="km-blog" id="blog">
    <div class="km-blog__cx">
        <div class="km-blog__top">
            <div>
                <span class="km-blog__eyebrow">— Our Blog</span>
                <h2 class="km-blog__h2">Smarter transfers <em>start here</em></h2>
                <p class="km-blog__sub">Tips, guides, and insights to help you send money across borders with confidence.</p>
            </div>
            <a href="{{ url('/blog') }}" class="km-blog__all">
                View all articles
                <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" width="14" height="14"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
            </a>
        </div>

        <div class="km-blog__grid">
            @if($blogPosts && count($blogPosts) >= 2)
                @php $bp = $blogPosts->values(); @endphp
                <a href="{{ url('/blog/'.($bp[0]->slug ?? '')) }}" class="km-blog__card km-blog__card--sm">
                    <img src="{{ asset('assets/images/frontend/blog/'.(@$bp[0]->data_values->image ?? '')) }}" alt="{{ __(@$bp[0]->data_values->title ?? '') }}" loading="lazy">
                    <div class="km-blog__overlay"></div>
                    <div class="km-blog__body">
                        <span class="km-blog__tag">{{ __(@$bp[0]->data_values->tag ?? 'Blog') }}</span>
                        <h3>{{ __(@$bp[0]->data_values->title ?? '') }}</h3>
                        <div class="km-blog__meta">
                            <span>
                                <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" width="12" height="12"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                {{ \Carbon\Carbon::parse($bp[0]->created_at)->format('M d, Y') }}
                            </span>
                        </div>
                        <span class="km-blog__readmore">Read more <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" width="14" height="14"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg></span>
                    </div>
                </a>
                <a href="{{ url('/blog/'.($bp[1]->slug ?? '')) }}" class="km-blog__card km-blog__card--lg">
                    <img src="{{ asset('assets/images/frontend/blog/'.(@$bp[1]->data_values->image ?? '')) }}" alt="{{ __(@$bp[1]->data_values->title ?? '') }}" loading="lazy">
                    <div class="km-blog__overlay"></div>
                    <div class="km-blog__body">
                        <span class="km-blog__tag">{{ __(@$bp[1]->data_values->tag ?? 'Blog') }}</span>
                        <h3>{{ __(@$bp[1]->data_values->title ?? '') }}</h3>
                        <div class="km-blog__meta">
                            <span>
                                <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" width="12" height="12"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                {{ \Carbon\Carbon::parse($bp[1]->created_at)->format('M d, Y') }}
                            </span>
                        </div>
                        <span class="km-blog__readmore">Read more <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" width="14" height="14"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg></span>
                    </div>
                </a>
            @else
                @foreach($staticBlogs as $i => $b)
                <a href="{{ $b['url'] }}" class="km-blog__card {{ $i === 0 ? 'km-blog__card--sm' : 'km-blog__card--lg' }}">
                    <img src="{{ $b['img'] }}" alt="{{ $b['title'] }}" loading="lazy">
                    <div class="km-blog__overlay"></div>
                    <div class="km-blog__body">
                        <span class="km-blog__tag">{{ $b['tag'] }}</span>
                        <h3>{{ $b['title'] }}</h3>
                        <p class="km-blog__excerpt">{{ $b['excerpt'] }}</p>
                        <div class="km-blog__meta">
                            <span>
                                <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" width="12" height="12"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                {{ $b['date'] }}
                            </span>
                            <span>
                                <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" width="12" height="12"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                {{ $b['read'] }}
                            </span>
                        </div>
                        <span class="km-blog__readmore">Read more <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" width="14" height="14"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg></span>
                    </div>
                </a>
                @endforeach
            @endif
        </div>
    </div>
</section>
