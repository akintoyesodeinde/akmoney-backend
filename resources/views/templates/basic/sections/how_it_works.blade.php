@push('style')
<style>
.km-how {
    position: relative;
    overflow: hidden;
    background: #1c6070;
    font-family: 'Work Sans', sans-serif;
}

/* Decorative rings */
.km-how__rings {
    pointer-events: none;
    position: absolute;
    inset-x: 0;
    top: 0;
    display: flex;
    justify-content: center;
    opacity: .15;
}

.km-how__w {
    width: min(calc(100% - 48px), 1200px);
    margin: 0 auto;
}

.km-how__intro {
    position: relative;
    padding: 80px 0 56px;
    text-align: center;
}

.km-how__eyebrow {
    display: inline-block;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.60);
    margin-bottom: 20px;
}

.km-how__h2 {
    margin: 0;
    font-size: clamp(36px, 4vw, 52px);
    font-weight: 600;
    color: #fff;
    letter-spacing: -1.5px;
    line-height: 1.08;
}

.km-how__h2 em {
    font-style: italic;
    color: #fff;
    text-shadow: 0 0 30px rgba(255,255,255,.30), 0 0 60px rgba(255,255,255,.15);
}

.km-how__sub {
    margin: 16px auto 0;
    max-width: 420px;
    font-size: 14px;
    color: rgba(255,255,255,.45);
    line-height: 1.7;
}

/* Step cards grid — flush to bottom */
.km-how__grid {
    position: relative;
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    align-items: end;
    padding-bottom: 0;
}

.km-how__card {
    position: relative;
    display: flex;
    flex-direction: column;
    border-radius: 24px 24px 0 0;
    overflow: hidden;
    background: rgba(255,255,255,.07);
    border: 1px solid rgba(255,255,255,.12);
    border-bottom: none;
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    transition: background .22s;
}

.km-how__card:hover { background: rgba(255,255,255,.10); }

/* Stagger heights so cards look like a skyline */
.km-how__card:nth-child(2) { margin-bottom: 20px; }
.km-how__card:nth-child(3) { margin-bottom: 40px; }
.km-how__card:nth-child(4) { margin-bottom: 60px; }

.km-how__card-body {
    padding: 28px 26px 18px;
}

.km-how__badge {
    display: inline-block;
    margin-bottom: 16px;
    font-size: 10px;
    font-weight: 900;
    letter-spacing: .18em;
    text-transform: uppercase;
    padding: 5px 12px;
    border-radius: 999px;
    background: rgba(255,255,255,.15);
    color: #fff;
}

.km-how__card h3 {
    margin: 0 0 10px;
    font-size: 18px;
    font-weight: 700;
    color: #fff;
    line-height: 1.2;
}

.km-how__card p {
    margin: 0;
    font-size: 13px;
    line-height: 1.65;
    color: rgba(255,255,255,.60);
}

.km-how__card-img {
    margin-top: auto;
    display: flex;
    justify-content: center;
    padding: 0 20px;
}

.km-how__card-img img {
    width: 100%;
    object-fit: contain;
    object-position: bottom;
    border-radius: 12px 12px 0 0;
    opacity: .92;
    max-height: 280px;
}

/* Fallback gradient placeholder when image not yet uploaded */
.km-how__card-placeholder {
    height: 200px;
    background: linear-gradient(180deg, rgba(167,27,71,.08) 0%, rgba(167,27,71,.22) 100%);
    border-radius: 12px 12px 0 0;
    display: flex;
    align-items: center;
    justify-content: center;
    color: rgba(167,27,71,.50);
    font-size: 48px;
    font-weight: 900;
}

@media(max-width: 900px) {
    .km-how__grid {
        grid-template-columns: repeat(2, 1fr);
    }
    .km-how__card:nth-child(2),
    .km-how__card:nth-child(3),
    .km-how__card:nth-child(4) { margin-bottom: 0; }
    .km-how__card { border-radius: 20px; border-bottom: 1px solid rgba(255,255,255,.12); }
}

@media(max-width: 560px) {
    .km-how__grid { grid-template-columns: 1fr; }
    .km-how__intro { padding: 56px 0 40px; }
}
</style>
@endpush

<section class="km-how" id="how-it-works">

    <div class="km-how__rings" aria-hidden="true">
        <svg width="900" height="420" viewBox="0 0 900 420" fill="none">
            <circle cx="450" cy="0" r="200" stroke="white" stroke-width=".8"/>
            <circle cx="450" cy="0" r="300" stroke="white" stroke-width=".6"/>
            <circle cx="450" cy="0" r="400" stroke="white" stroke-width=".4"/>
            <circle cx="450" cy="0" r="500" stroke="white" stroke-width=".3"/>
        </svg>
    </div>

    <div class="km-how__w">

        <div class="km-how__intro">
            <span class="km-how__eyebrow">— How It Works</span>
            <h2 class="km-how__h2">We Made Sending <em>Simple!</em></h2>
            <p class="km-how__sub">Four steps. No drama. A transfer flow that feels fast without feeling careless.</p>
        </div>

        <div class="km-how__grid">

            <div class="km-how__card">
                <div class="km-how__card-body">
                    <span class="km-how__badge">01</span>
                    <h3>Create your account</h3>
                    <p>Sign up in under two minutes. No paperwork, no branch visits — just your phone and a few details.</p>
                </div>
                <div class="km-how__card-img">
                    <img src="{{ asset('assets/images/kinmoney/step1-x1vtd8uL.jpeg') }}" alt="Create your account">
                </div>
            </div>

            <div class="km-how__card">
                <div class="km-how__card-body">
                    <span class="km-how__badge">02</span>
                    <h3>Add a recipient</h3>
                    <p>Enter who you're sending to. We save them for next time so it only gets faster.</p>
                </div>
                <div class="km-how__card-img">
                    <img src="{{ asset('assets/images/kinmoney/step2-CtommfKi.jpeg') }}" alt="Add a recipient">
                </div>
            </div>

            <div class="km-how__card">
                <div class="km-how__card-body">
                    <span class="km-how__badge">03</span>
                    <h3>Pay securely</h3>
                    <p>Choose your payment method and confirm. Your funds are protected end-to-end.</p>
                </div>
                <div class="km-how__card-img">
                    <img src="{{ asset('assets/images/kinmoney/step3-D9Tya3Bm.jpeg') }}" alt="Pay securely">
                </div>
            </div>

            <div class="km-how__card">
                <div class="km-how__card-body">
                    <span class="km-how__badge">04</span>
                    <h3>Recipient gets the money</h3>
                    <p>They receive funds within minutes to their bank, mobile wallet, or cash pickup point.</p>
                </div>
                <div class="km-how__card-img">
                    <img src="{{ asset('assets/images/kinmoney/step4-VY3RDzdf.jpeg') }}" alt="Recipient gets the money">
                </div>
            </div>

        </div>{{-- .km-how__grid --}}
    </div>{{-- .km-how__w --}}
</section>
