@push('style')
<style>
.km-why {
    background: linear-gradient(135deg, #f9f9f7 0%, #fff 100%);
    padding: 88px 0 80px;
    font-family: 'Work Sans', sans-serif;
}

.km-why__w {
    width: min(calc(100% - 48px), 1200px);
    margin: 0 auto;
}

.km-why__eyebrow {
    display: inline-block;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: #A71B47;
    margin-bottom: 22px;
}

.km-why__header {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    margin-bottom: 56px;
    align-items: start;
}

.km-why__h2 {
    margin: 0;
    font-size: clamp(32px, 3.2vw, 48px);
    font-weight: 600;
    line-height: 1.12;
    letter-spacing: -1px;
    color: #0F4650;
}

.km-why__h2 em {
    font-style: italic;
    color: #A71B47;
}

.km-why__desc {
    margin: 0;
    font-size: 15px;
    line-height: 1.75;
    color: #5f718a;
    align-self: center;
}

.km-why__desc strong { color: #0F4650; font-weight: 600; }
.km-why__desc b     { color: #A71B47; font-weight: 600; }

.km-why__grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 16px;
}

.km-why__card {
    background: #fff;
    border: 1px solid #e8eef5;
    border-radius: 12px;
    padding: 28px 20px 24px;
    transition: transform .22s, box-shadow .22s;
}

.km-why__card:hover {
    transform: translateY(-5px);
    box-shadow: 0 18px 44px rgba(15,70,80,.10);
}

.km-why__icon {
    width: 54px;
    height: 54px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
}

.km-why__icon--teal   { background: rgba(15,70,80,.10);   color: #0F4650; }
.km-why__icon--crimson{ background: rgba(167,27,71,.10); color: #A71B47; }
.km-why__icon--amber  { background: rgba(244,162,97,.15); color: #d97706; }
.km-why__icon--teal2  { background: rgba(42,157,143,.10); color: #2a9d8f; }
.km-why__icon--coral  { background: rgba(231,111,81,.10); color: #e76f51; }

.km-why__card h3 {
    margin: 0 0 10px;
    font-size: 14px;
    font-weight: 700;
    color: #111;
    letter-spacing: -.2px;
}

.km-why__card p {
    margin: 0;
    font-size: 13px;
    line-height: 1.65;
    color: #6b7f9a;
}

@media(max-width: 1024px) {
    .km-why__grid { grid-template-columns: repeat(3, 1fr); }
}
@media(max-width: 768px) {
    .km-why__header { grid-template-columns: 1fr; gap: 20px; }
    .km-why__grid   { grid-template-columns: repeat(2, 1fr); }
}
@media(max-width: 500px) {
    .km-why__grid { grid-template-columns: 1fr; }
    .km-why { padding: 56px 0 52px; }
}
</style>
@endpush

<section class="km-why">
  <div class="km-why__w">

    <span class="km-why__eyebrow">— Why Choose Us</span>

    <div class="km-why__header">
      <div>
        <h2 class="km-why__h2">Cross-border transfers <em>made simple</em>.</h2>
      </div>
      <div>
        <p class="km-why__desc">
          Send money across borders with <strong>lightning-fast speed</strong>,
          <b>industry-low fees</b>, and <strong>unbeatable exchange rates</strong>.
          Built for simplicity, powered by <strong>24/7 customer support</strong>
          — AkMoney makes global transfers feel effortless.
        </p>
      </div>
    </div>

    <div class="km-why__grid">

      <div class="km-why__card">
        <div class="km-why__icon km-why__icon--teal">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
          </svg>
        </div>
        <h3>Fast Transfer</h3>
        <p>Your money reaches in minutes, not days. Quick and reliable.</p>
      </div>

      <div class="km-why__card">
        <div class="km-why__icon km-why__icon--crimson">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <line x1="12" y1="1" x2="12" y2="23"/>
            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
          </svg>
        </div>
        <h3>Low Fees</h3>
        <p>We offer competitive rates and transparent fees. No hidden costs.</p>
      </div>

      <div class="km-why__card">
        <div class="km-why__icon km-why__icon--amber">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
            <polyline points="17 6 23 6 23 12"/>
          </svg>
        </div>
        <h3>Great Rates</h3>
        <p>Get the best exchange rates in the market every time you send.</p>
      </div>

      <div class="km-why__card">
        <div class="km-why__icon km-why__icon--teal2">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <circle cx="12" cy="12" r="10"/>
            <path d="M8 14s1.5 2 4 2 4-2 4-2"/>
            <line x1="9" y1="9" x2="9.01" y2="9"/>
            <line x1="15" y1="9" x2="15.01" y2="9"/>
          </svg>
        </div>
        <h3>Ease of Use</h3>
        <p>Simple, intuitive interface designed for first-time and repeat users.</p>
      </div>

      <div class="km-why__card">
        <div class="km-why__icon km-why__icon--coral">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M3 18v-6a9 9 0 0 1 18 0v6"/>
            <path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"/>
          </svg>
        </div>
        <h3>Customer Support</h3>
        <p>Our support team is here to help you anytime, anywhere.</p>
      </div>

    </div>
  </div>
</section>
