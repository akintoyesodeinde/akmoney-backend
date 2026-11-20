@php
$testimonialElements = getContent('testimonial.element', false, null, true);

$testiList = ($testimonialElements && count($testimonialElements) >= 3)
    ? $testimonialElements->take(3)->map(fn($t) => [
        'quote'   => $t->data_values->description ?? '',
        'name'    => $t->data_values->name ?? '',
        'country' => $t->data_values->designation ?? '',
    ])->values()->all()
    : [
        ['quote'=>'The interface feels calm. I know exactly what I am sending and what my family receives.','name'=>'Amara','country'=>'Lagos'],
        ['quote'=>'The tracking and receipts make the whole transfer feel serious and trustworthy.','name'=>'Daniel','country'=>'Toronto'],
        ['quote'=>'It is fast without looking cheap. That is the part I like most.','name'=>'Maya','country'=>'London'],
    ];
@endphp

@push('style')
<style>
.km-testi{padding:80px 0;background:#1c6070;font-family:'Work Sans',sans-serif;}
.km-testi__cx{width:min(calc(100% - 48px),1280px);margin-inline:auto;}
.km-testi__top{text-align:center;margin-bottom:48px;}
.km-testi__eyebrow{display:inline-block;font-size:11px;font-weight:500;text-transform:uppercase;letter-spacing:.20em;color:#fff;margin-bottom:12px;}
.km-testi__h2{margin:0;font-size:clamp(26px,3vw,44px);font-weight:500;line-height:1.15;letter-spacing:-1px;color:#fff;}
.km-testi__h2 em{font-style:italic;color:#fff;}
.km-testi__sub{margin:16px auto 0;max-width:520px;font-size:15px;color:rgba(255,255,255,.7);}
.km-testi__grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;}
.km-testi__card{border-radius:32px;padding:24px;backdrop-filter:blur(8px);background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.10);transition:transform .3s;}
.km-testi__card:hover{transform:translateY(-4px);}
.km-testi__quote{margin:0;font-size:17px;font-weight:400;line-height:1.7;color:rgba(255,255,255,.9);}
.km-testi__footer{margin-top:32px;display:flex;align-items:center;justify-content:space-between;padding-top:20px;border-top:1px solid rgba(255,255,255,.10);}
.km-testi__name{font-size:15px;font-weight:500;color:#fff;margin:0;}
.km-testi__loc{font-size:13px;font-weight:300;color:rgba(255,255,255,.55);margin:4px 0 0;}
.km-testi__stars{font-size:14px;letter-spacing:.1em;color:#F5A623;}
@media(max-width:1023px){.km-testi__grid{grid-template-columns:repeat(2,1fr);}}
@media(max-width:639px){.km-testi__grid{grid-template-columns:1fr;}.km-testi{padding:56px 0;}}
</style>
@endpush

<section class="km-testi" id="testimonials">
    <div class="km-testi__cx">
        <div class="km-testi__top">
            <span class="km-testi__eyebrow">— Loved by senders</span>
            <h2 class="km-testi__h2">Confidence after <em>every transfer</em>.</h2>
            <p class="km-testi__sub">A remittance brand should feel safe before the customer clicks send.</p>
        </div>
        <div class="km-testi__grid">
            @foreach($testiList as $t)
            <article class="km-testi__card">
                <p class="km-testi__quote">"{{ $t['quote'] }}"</p>
                <div class="km-testi__footer">
                    <div>
                        <p class="km-testi__name">{{ $t['name'] }}</p>
                        <p class="km-testi__loc">{{ $t['country'] }}</p>
                    </div>
                    <span class="km-testi__stars">★★★★★</span>
                </div>
            </article>
            @endforeach
        </div>
    </div>
</section>
