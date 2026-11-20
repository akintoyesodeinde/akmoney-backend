<style>
@keyframes sc-blink  { 0%,100%{opacity:1} 50%{opacity:0.3} }
@keyframes sc-float  { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-6px)} }
@keyframes sc-fadein { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }
@keyframes sc-ticker { from{opacity:0;transform:translateX(-6px)} to{opacity:1;transform:translateX(0)} }
@keyframes sc-pulse  { 0%,100%{box-shadow:0 0 0 0 rgba(167,27,71,0.4)} 50%{box-shadow:0 0 0 8px rgba(167,27,71,0)} }
@keyframes sc-scroll { 0%{transform:translateY(0)} 100%{transform:translateY(-50%)} }

/* ── Page wrapper ── */
.sc-page {
    min-height: 100vh;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #F3F3F3;
    font-family: 'Inter', sans-serif;
    padding: 24px 16px;
    box-sizing: border-box;
}
.sc-card {
    display: flex;
    flex-direction: row;
    width: 100%;
    max-width: 860px;
    border-radius: 22px;
    overflow: hidden;
    box-shadow: 0 40px 100px rgba(0,0,0,0.18);
    min-height: 560px;
    border: 1px solid #E0E0E0;
}

/* ── Brand panel (LEFT) ── */
.sc-brand-panel {
    flex: 0 0 400px;
    position: relative;
    display: flex;
    flex-direction: column;
    padding: 44px 36px 36px;
    overflow: hidden;
    background: #061a3a;
    min-height: 100%;
    font-family: 'Inter', sans-serif;
}
.sc-brand-grid {
    position: absolute;
    inset: 0;
    pointer-events: none;
    background-image:
        linear-gradient(rgba(255,255,255,0.024) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,0.024) 1px, transparent 1px);
    background-size: 32px 32px;
}
.sc-brand-glow-1 {
    position: absolute;
    bottom: -120px;
    left: -120px;
    width: 420px;
    height: 420px;
    border-radius: 50%;
    pointer-events: none;
    background: radial-gradient(circle, rgba(167,27,71,0.22) 0%, transparent 65%);
}
.sc-brand-glow-2 {
    position: absolute;
    top: -80px;
    right: -80px;
    width: 280px;
    height: 280px;
    border-radius: 50%;
    pointer-events: none;
    background: radial-gradient(circle, rgba(167,27,71,0.1) 0%, transparent 70%);
}
.sc-brand-ring-1 {
    position: absolute;
    bottom: -180px; left: -180px;
    width: 360px; height: 360px;
    border-radius: 50%;
    border: 1px solid rgba(167,27,71,0.2);
    pointer-events: none;
}
.sc-brand-ring-2 {
    position: absolute;
    bottom: -250px; left: -250px;
    width: 500px; height: 500px;
    border-radius: 50%;
    border: 1px solid rgba(167,27,71,0.1);
    pointer-events: none;
}
.sc-brand-inner { position: relative; z-index: 1; }
.sc-brand-logo {
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    user-select: none;
    text-decoration: none;
    margin-bottom: 20px;
}
.sc-brand-logo-text {
    font-family: 'Inter', sans-serif;
    font-weight: 800;
    font-size: 22px;
    color: #ffffff;
    letter-spacing: -0.02em;
}
.sc-brand-logo-text span { color: #A71B47; }
.sc-brand-badge {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: rgba(167,27,71,0.12);
    border: 1px solid rgba(167,27,71,0.3);
    border-radius: 20px;
    padding: 5px 13px;
    margin-bottom: 18px;
    font-size: 10px;
    font-weight: 700;
    color: #ffffff;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    font-family: 'Inter', sans-serif;
}
.sc-brand-badge-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #A71B47;
    box-shadow: 0 0 8px rgba(167,27,71,0.7);
    display: inline-block;
    flex-shrink: 0;
    animation: sc-blink 1.6s ease infinite;
}
.sc-brand-heading {
    font-family: 'Inter', sans-serif;
    font-size: 27px;
    font-weight: 800;
    color: #ffffff;
    margin: 0 0 10px;
    line-height: 1.15;
    letter-spacing: -0.025em;
}
.sc-brand-heading span { color: #A71B47; }
.sc-brand-desc {
    font-size: 13px;
    color: rgba(255,255,255,0.55);
    line-height: 1.7;
    margin: 0 0 20px;
    max-width: 280px;
    font-family: 'Inter', sans-serif;
}

/* ── Feature pills ── */
.sc-feat-pills {
    display: flex;
    flex-wrap: wrap;
    gap: 7px;
    margin-bottom: 22px;
}
.sc-feat-pill {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 6px 10px;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.09);
    border-radius: 20px;
    font-size: 11px;
    color: rgba(255,255,255,0.72);
    font-weight: 500;
    white-space: nowrap;
    font-family: 'Inter', sans-serif;
}

/* ── Live transfer card ── */
.sc-transfer-card {
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 14px;
    padding: 14px 16px;
    margin-bottom: 16px;
    animation: sc-float 4.5s ease-in-out infinite;
}
.sc-transfer-card-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}
.sc-transfer-card-label {
    font-size: 10px;
    font-weight: 700;
    color: rgba(255,255,255,0.35);
    letter-spacing: 0.1em;
    text-transform: uppercase;
    font-family: 'Inter', sans-serif;
}
.sc-transfer-card-live {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 10px;
    font-weight: 700;
    color: #4ade80;
    background: rgba(74,222,128,0.1);
    border: 1px solid rgba(74,222,128,0.22);
    border-radius: 20px;
    padding: 2px 8px;
    animation: sc-pulse 2s ease infinite;
}
.sc-transfer-card-live-dot {
    width: 4px;
    height: 4px;
    border-radius: 50%;
    background: #4ade80;
    display: inline-block;
}
.sc-transfer-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.sc-transfer-flag {
    width: 18px;
    height: 13px;
    object-fit: cover;
    border-radius: 2px;
}
.sc-transfer-amount-main {
    font-size: 17px;
    font-weight: 800;
    color: #fff;
    font-family: 'Inter', sans-serif;
    letter-spacing: -0.02em;
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 2px;
}
.sc-transfer-amount-main.red { color: #A71B47; }
.sc-transfer-from-label {
    font-size: 10px;
    color: rgba(255,255,255,0.35);
    font-family: 'Inter', sans-serif;
}
.sc-transfer-mid {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 2px;
}
.sc-transfer-rate-label {
    font-size: 9px;
    color: #A71B47;
    font-weight: 700;
    text-align: center;
    font-family: 'Inter', sans-serif;
}

/* ── Exchange rate ticker ── */
.sc-ticker-box {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.07);
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 16px;
    height: 92px;
}
.sc-ticker-header {
    padding: 8px 14px 6px;
    border-bottom: 1px solid rgba(255,255,255,0.06);
    display: flex;
    justify-content: space-between;
}
.sc-ticker-header span {
    font-size: 9px;
    font-weight: 700;
    color: rgba(255,255,255,0.3);
    letter-spacing: 0.1em;
    text-transform: uppercase;
    font-family: 'Inter', sans-serif;
}
.sc-ticker-scroll-wrap { overflow: hidden; height: 68px; position: relative; }
.sc-ticker-scroll { animation: sc-scroll 12s linear infinite; }
.sc-ticker-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 5px 14px;
}
.sc-ticker-pair { font-size: 11px; font-weight: 700; color: rgba(255,255,255,0.7); font-family: monospace; }
.sc-ticker-val  { font-size: 11px; color: rgba(255,255,255,0.5); font-family: monospace; }
.sc-ticker-up   { font-size: 10px; font-weight: 700; color: #4ade80; font-family: monospace; }
.sc-ticker-dn   { font-size: 10px; font-weight: 700; color: #f87171; font-family: monospace; }

/* ── Review card ── */
.sc-review-card {
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 12px;
    padding: 14px 16px;
    margin-bottom: 18px;
    animation: sc-fadein 0.5s ease forwards;
}
.sc-review-stars { display: flex; gap: 2px; margin-bottom: 8px; }
.sc-review-quote {
    font-size: 12px;
    color: rgba(255,255,255,0.65);
    line-height: 1.6;
    font-style: italic;
    margin: 0 0 8px;
    font-family: 'Inter', sans-serif;
}
.sc-review-author {
    display: flex;
    align-items: center;
    gap: 6px;
}
.sc-review-avatar {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: rgba(167,27,71,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
}
.sc-review-name  { font-size: 11px; font-weight: 700; color: rgba(255,255,255,0.5); font-family: 'Inter', sans-serif; }
.sc-review-dot   { width: 3px; height: 3px; border-radius: 50%; background: rgba(255,255,255,0.2); display: inline-block; }
.sc-review-badge { font-size: 10px; color: rgba(255,255,255,0.3); font-family: 'Inter', sans-serif; }

/* ── Stats row ── */
.sc-brand-stats {
    position: relative;
    z-index: 1;
    display: flex;
    gap: 8px;
    margin-top: auto;
}
.sc-brand-stat {
    flex: 1;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.09);
    border-radius: 10px;
    padding: 10px 8px;
    text-align: center;
}
.sc-brand-stat-num {
    font-family: 'Inter', sans-serif;
    font-size: 15px;
    font-weight: 800;
    color: #ffffff;
}
.sc-brand-stat-label {
    font-size: 9px;
    color: rgba(255,255,255,0.35);
    margin-top: 1px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    font-family: 'Inter', sans-serif;
}

/* ── Form panel (RIGHT) ── */
.sc-form-panel {
    flex: 1;
    background: #ffffff;
    padding: 40px 48px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    overflow-y: auto;
    font-family: 'Inter', sans-serif;
}
.sc-form-logo {
    display: none; /* logo shown in brand panel */
}
.sc-form-title {
    font-family: 'Inter', sans-serif;
    font-size: 24px;
    font-weight: 800;
    color: #0C0C0C;
    margin: 0 0 4px;
    letter-spacing: -0.02em;
}
.sc-form-subtitle {
    font-size: 13px;
    color: #888;
    margin: 0 0 0;
    font-family: 'Inter', sans-serif;
    line-height: 1.6;
}
.sc-form-subtitle a { color: #A71B47; text-decoration: none; font-weight: 600; }

/* ── Section eyebrow labels ── */
.sc-section-label {
    font-size: 10px;
    font-weight: 700;
    color: #A71B47;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    margin: 20px 0 12px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-family: 'Inter', sans-serif;
}
.sc-section-label-line {
    height: 1px;
    width: 18px;
    background: #A71B47;
    display: inline-block;
    flex-shrink: 0;
}

/* ── Fields ── */
.sc-field { margin-bottom: 10px; }
.sc-label {
    display: block;
    font-size: 11px;
    font-weight: 700;
    color: #555;
    letter-spacing: 0.07em;
    text-transform: uppercase;
    margin-bottom: 5px;
    font-family: 'Inter', sans-serif;
}
.sc-input-wrap { position: relative; }
.sc-input-icon {
    position: absolute;
    left: 11px;
    top: 50%;
    transform: translateY(-50%);
    color: #0C0C0C;
    opacity: 0.35;
    display: flex;
    pointer-events: none;
    font-size: 14px;
}
.sc-input {
    width: 100%;
    box-sizing: border-box;
    padding: 10px 10px 10px 34px;
    font-size: 13px;
    font-family: 'Inter', sans-serif;
    border: 1.5px solid #E8E8E8;
    border-radius: 9px;
    background: #FAFAFA;
    color: #0C0C0C;
    outline: none;
    transition: border-color 0.15s, background 0.15s;
    -webkit-appearance: none;
    appearance: none;
    display: block;
}
.sc-input:focus {
    border-color: #A71B47;
    background: #fff;
}
.sc-input-eye-wrap { position: relative; }
.sc-input-eye-wrap .sc-input { padding-right: 34px; }
.sc-eye-btn {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    color: #0C0C0C;
    opacity: 0.35;
    padding: 0;
    display: flex;
    font-size: 14px;
    line-height: 1;
}

/* ── Phone field ── */
.sc-phone-wrap {
    display: flex;
    border: 1.5px solid #E8E8E8;
    border-radius: 9px;
    overflow: hidden;
    background: #FAFAFA;
    height: 42px;
}
.sc-phone-code {
    flex-shrink: 0;
    min-width: 72px;
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 0 10px;
    background: #F0F0F0;
    border-right: 1.5px solid #E8E8E8;
    font-size: 13px;
    font-weight: 600;
    color: #0C0C0C;
    font-family: 'Inter', sans-serif;
}
.sc-phone-input {
    flex: 1;
    border: none;
    background: transparent;
    padding: 10px 12px;
    font-size: 13px;
    font-family: 'Inter', sans-serif;
    color: #0C0C0C;
    outline: none;
}

/* ── Options row ── */
.sc-options-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 22px;
}
.sc-remember {
    display: flex;
    align-items: center;
    gap: 7px;
    font-size: 13px;
    color: #666;
    cursor: pointer;
    font-family: 'Inter', sans-serif;
}
.sc-remember input[type="checkbox"] {
    accent-color: #A71B47;
    width: 14px;
    height: 14px;
    cursor: pointer;
}
.sc-forgot {
    font-size: 13px;
    color: #A71B47;
    text-decoration: none;
    font-family: 'Inter', sans-serif;
}
.sc-forgot:hover { text-decoration: underline; color: #A71B47; }

/* ── Buttons ── */
.sc-btn-primary {
    width: 100%;
    padding: 14px;
    background: #A71B47;
    color: #fff;
    border: none;
    border-radius: 9px;
    font-size: 15px;
    font-weight: 700;
    font-family: 'Inter', sans-serif;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    text-decoration: none;
    transition: background 0.15s;
    box-sizing: border-box;
    margin-bottom: 14px;
}
.sc-btn-primary:hover { background: #8a1539; color: #fff; }
.sc-btn-google {
    width: 100%;
    padding: 13px;
    background: #fff;
    border: 1.5px solid #E8E8E8;
    border-radius: 9px;
    font-size: 14px;
    font-weight: 600;
    font-family: 'Inter', sans-serif;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    color: #0C0C0C;
    transition: border-color 0.15s, box-shadow 0.15s;
    margin-bottom: 16px;
    box-sizing: border-box;
}
.sc-btn-google:hover { border-color: #ccc; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }

/* ── OR divider ── */
.sc-or-divider {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 4px 0 12px;
}
.sc-or-divider::before, .sc-or-divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: #EFEFEF;
}
.sc-or-divider span { font-size: 12px; color: #AAA; font-weight: 500; font-family: 'Inter', sans-serif; }

/* ── Footer text ── */
.sc-footer-text {
    text-align: center;
    font-size: 13px;
    color: #888;
    margin: 0;
    font-family: 'Inter', sans-serif;
}
.sc-footer-text a, .sc-footer-text button {
    color: #A71B47;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    background: none;
    border: none;
    cursor: pointer;
    padding: 0;
    font-family: 'Inter', sans-serif;
}

/* ── Error ── */
.sc-error {
    display: block;
    font-size: 12px;
    color: #A71B47;
    margin-top: 4px;
    font-family: 'Inter', sans-serif;
}

/* ── Check label ── */
.sc-check-label {
    display: flex;
    align-items: flex-start;
    gap: 9px;
    font-size: 13px;
    color: #666;
    cursor: pointer;
    font-family: 'Inter', sans-serif;
    line-height: 1.6;
    margin: 16px 0 14px;
}
.sc-check-label input[type="checkbox"] {
    accent-color: #A71B47;
    margin-top: 3px;
    flex-shrink: 0;
}
.sc-check-label a, .sc-check-label span.red { color: #A71B47; font-weight: 600; text-decoration: none; }

/* ── Divider (OR) ── */
.sc-divider {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 18px 0;
}
.sc-divider::before, .sc-divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: #e8e8e8;
}
.sc-divider span { font-size: 12px; color: #aaa; font-family: 'Inter', sans-serif; }

/* ── Code inputs (6-digit) ── */
.sc-code-inputs {
    display: flex;
    gap: 8px;
    margin-bottom: 22px;
}
.sc-code-inputs input {
    flex: 1;
    text-align: center;
    font-size: 22px;
    font-weight: 700;
    font-family: 'Inter', sans-serif;
    border: 1.5px solid #E8E8E8;
    border-radius: 9px;
    padding: 14px 4px;
    background: #FAFAFA;
    color: #0C0C0C;
    outline: none;
    transition: border-color 0.15s;
    max-width: 56px;
}
.sc-code-inputs input:focus { border-color: #A71B47; background: #fff; }

/* ── Info box ── */
.sc-info-box {
    background: #F8F7F5;
    border-radius: 12px;
    padding: 14px 16px;
    margin-bottom: 24px;
    font-size: 13px;
    color: #666;
    line-height: 1.7;
    border: 1px solid #EEE;
    font-family: 'Inter', sans-serif;
}

/* ── Italic / font bleed reset ── */
.sc-page, .sc-page * { font-style: normal; }
.sc-review-quote { font-style: italic !important; }

/* ── Responsive ── */
@media (max-width: 860px) {
    .sc-brand-panel { flex: 0 0 280px; }
}
@media (max-width: 640px) {
    .sc-page { padding: 0; align-items: flex-start; }
    .sc-card { flex-direction: column; border-radius: 0; box-shadow: none; min-height: 100vh; border: none; overflow: visible; }
    .sc-brand-panel { flex: none; width: 100%; padding: 28px 24px 28px; }
    .sc-brand-heading { font-size: 22px; }
    .sc-brand-desc { max-width: 100%; }
    .sc-transfer-card { animation: none; }
    .sc-form-panel { padding: 32px 24px 48px; overflow-y: visible; flex: 1; }
}
</style>
