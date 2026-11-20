@php
$faqElements = getContent('faq.element', false, null, true);

$staticFaqs = [
    ['q'=>'How long does a transfer take?','a'=>'Most transfers are completed within minutes depending on the delivery method and destination country.'],
    ['q'=>'What are your fees?','a'=>'Our fees are low and transparent. CA$1.99 for transfers up to CA$250, CA$2.99 up to CA$1,000, CA$4.99 up to CA$5,000. No hidden charges.'],
    ['q'=>'How do I track my transfer?','a'=>'You\'ll receive real-time notifications by email and in-app. Log in to your dashboard at any time to see the exact status of your transfer.'],
    ['q'=>'Is '.gs('site_name').' regulated?','a'=>'Yes. We are a registered Money Services Business (MSB) licensed by FINTRAC in Canada — the federal regulator for financial transactions.'],
    ['q'=>'What payment methods do you accept?','a'=>'We accept bank transfers (EFT/Interac), debit cards, and select credit cards depending on the sending country.'],
    ['q'=>'Can I cancel a transfer?','a'=>'Transfers can be cancelled before they are processed. Once funds have been sent to the recipient, cancellation is not possible. Contact support immediately if needed.'],
];
@endphp

@push('style')
<style>
.km-faq{padding:80px 0;background:#fff;font-family:'Work Sans',sans-serif;overflow:hidden;}
.km-faq__cx{width:min(calc(100% - 48px),1280px);margin-inline:auto;}
.km-faq__grid{display:grid;grid-template-columns:1fr 1.4fr;gap:64px;align-items:start;}
.km-faq__eyebrow{display:inline-block;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.20em;color:#A71B47;margin-bottom:24px;}
.km-faq__h2{margin:0 0 16px;font-size:clamp(26px,3vw,44px);font-weight:700;line-height:1.1;letter-spacing:-1px;color:#0F4650;}
.km-faq__h2 em{font-style:italic;color:#A71B47;}
.km-faq__sub{margin:0 0 32px;font-size:15px;line-height:1.65;color:#6b7280;}
.km-faq__stats{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:32px;}
.km-faq__stat{background:#f5f5f5;border-radius:12px;padding:16px;text-align:center;transition:box-shadow .2s;}
.km-faq__stat:hover{box-shadow:0 8px 24px rgba(0,0,0,.08);}
.km-faq__stat-icon{display:flex;justify-content:center;margin-bottom:8px;}
.km-faq__stat-num{font-size:20px;font-weight:700;margin:0;}
.km-faq__stat-lbl{font-size:12px;color:#9ca3af;margin-top:4px;}
.km-faq__contact{display:inline-flex;align-items:center;gap:8px;padding:12px 24px;border-radius:8px;background:#0F4650;color:#fff;font-size:14px;font-weight:600;text-decoration:none;transition:opacity .2s;}
.km-faq__contact:hover{opacity:.88;color:#fff;}
.km-faq__list{display:flex;flex-direction:column;gap:12px;}
.km-faq__item{background:#f5f5f5;border-radius:12px;overflow:hidden;transition:box-shadow .2s;}
.km-faq__item:hover{box-shadow:0 4px 16px rgba(0,0,0,.08);}
.km-faq__btn{width:100%;display:flex;align-items:center;justify-content:space-between;text-align:left;padding:16px 24px;border:0;background:transparent;cursor:pointer;font-family:'Work Sans',sans-serif;font-size:15px;font-weight:600;color:#0F4650;gap:16px;}
.km-faq__icon{flex-shrink:0;width:28px;height:28px;border-radius:50%;background:#e9e9e9;color:#A71B47;display:flex;align-items:center;justify-content:center;transition:transform .2s,background .2s;}
.km-faq__item.open .km-faq__icon{transform:rotate(45deg);background:#A71B47;color:#fff;}
.km-faq__ans{max-height:0;overflow:hidden;transition:max-height .3s ease;}
.km-faq__item.open .km-faq__ans{max-height:300px;}
.km-faq__ans p{margin:0;padding:0 24px 20px;font-size:14px;color:#6b7280;line-height:1.7;}
@media(max-width:1023px){.km-faq__grid{grid-template-columns:1fr;gap:40px;}.km-faq{padding:56px 0;}}
</style>
@endpush

@push('script')
<script>
function kmFaqToggle(btn){
    var item = btn.closest('.km-faq__item');
    var wasOpen = item.classList.contains('open');
    document.querySelectorAll('.km-faq__item.open').forEach(function(el){ el.classList.remove('open'); });
    if(!wasOpen) item.classList.add('open');
}
</script>
@endpush

<section class="km-faq" id="faq">
    <div class="km-faq__cx">
        <div class="km-faq__grid">
            <div class="km-faq__left">
                <span class="km-faq__eyebrow">— Got questions?</span>
                <h2 class="km-faq__h2">Frequently Asked <em>Questions</em></h2>
                <p class="km-faq__sub">Everything you need to know about sending money with {!! siteNameHtml() !!}</p>
                <div class="km-faq__stats">
                    <div class="km-faq__stat">
                        <div class="km-faq__stat-icon">
                            <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" width="28" height="28" style="color:#A71B47;"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        </div>
                        <p class="km-faq__stat-num" style="color:#A71B47;">4.8</p>
                        <p class="km-faq__stat-lbl">Trustpilot Rating</p>
                    </div>
                    <div class="km-faq__stat">
                        <div class="km-faq__stat-icon">
                            <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" width="28" height="28" style="color:#0F4650;"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/></svg>
                        </div>
                        <p class="km-faq__stat-num" style="color:#0F4650;">50k+</p>
                        <p class="km-faq__stat-lbl">Happy Customers</p>
                    </div>
                </div>
                <a href="{{ url('/contact') }}" class="km-faq__contact">Still have questions? Contact us</a>
            </div>

            <div class="km-faq__list">
                @if($faqElements && count($faqElements) > 0)
                    @foreach($faqElements as $faq)
                    <div class="km-faq__item">
                        <button class="km-faq__btn" onclick="kmFaqToggle(this)" type="button">
                            <span>{{ __($faq->data_values->question ?? '') }}</span>
                            <span class="km-faq__icon">
                                <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            </span>
                        </button>
                        <div class="km-faq__ans"><p>{{ __($faq->data_values->answer ?? '') }}</p></div>
                    </div>
                    @endforeach
                @else
                    @foreach($staticFaqs as $faq)
                    <div class="km-faq__item">
                        <button class="km-faq__btn" onclick="kmFaqToggle(this)" type="button">
                            <span>{{ $faq['q'] }}</span>
                            <span class="km-faq__icon">
                                <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            </span>
                        </button>
                        <div class="km-faq__ans"><p>{{ $faq['a'] }}</p></div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</section>
