@push('style')
<style>
:root{
  --ab:#A71B47;
  --abt:#0F4650;
  --abn:#071318;
  --atx:#0d1a14;
  --amu:rgba(13,26,20,0.55);
  --ff:'Work Sans','Segoe UI',Arial,sans-serif;
  --header-safe:96px;
}

.am-about,
.am-about *{box-sizing:border-box;}

.am-about{
  width:100%;
  overflow-x:hidden;
  color:var(--atx);
  font-family:var(--ff);
  background:#fff;
}

.am-about a{ color:inherit; text-decoration:none; }

.am-w{
  width:min(calc(100% - 48px),1200px);
  margin-inline:auto;
}

/* ── HERO ── */
.abt-hero{
  position:relative;
  min-height:100vh;
  display:flex;
  align-items:center;
  overflow:hidden;
}

.abt-hero__bg{
  position:absolute;
  inset:0;
  z-index:0;
}

.abt-hero__img{
  width:100%;
  height:100%;
  object-fit:cover;
  object-position:center;
  display:block;
}

.abt-hero__overlay{
  position:absolute;
  inset:0;
  background:linear-gradient(135deg,rgba(15,70,80,.95) 0%,rgba(10,53,64,.90) 55%,rgba(15,70,80,.95) 100%);
}

.abt-hero__inner{
  position:relative;
  z-index:2;
  width:min(calc(100% - 48px),1000px);
  margin:0 auto;
  padding:calc(60px + var(--header-safe)) 0 80px;
}

.abt-hero__glass{
  background:#fff;
  border:none;
  border-radius:20px;
  padding:48px 52px;
  box-shadow:0 32px 80px rgba(0,0,0,.25);
}

.abt-hero__kicker{
  display:inline-block;
  padding:8px 16px;
  border-radius:8px;
  background:rgba(167,27,71,0.20);
  backdrop-filter:blur(8px);
  color:var(--ab);
  font-size:11px;
  font-weight:800;
  letter-spacing:.22em;
  text-transform:uppercase;
  margin-bottom:24px;
}

.abt-hero__h1{
  margin:0;
  font-size:clamp(40px,6vw,72px);
  font-weight:900;
  line-height:1.05;
  letter-spacing:-1.8px;
  color:#0F4650;
}

.abt-hero__h1 span{ color:var(--ab); }

.abt-hero__copy{
  margin:24px 0 0;
  max-width:680px;
  font-size:17px;
  line-height:1.75;
  color:#475569;
  font-weight:500;
}

.abt-hero__ctas{
  margin-top:36px;
  display:flex;
  flex-wrap:wrap;
  gap:12px;
}

.abt-btn-primary{
  display:inline-flex;
  align-items:center;
  gap:8px;
  padding:14px 32px;
  border-radius:10px;
  background:var(--ab);
  color:#fff;
  font-size:14px;
  font-weight:700;
  text-decoration:none;
  box-shadow:0 8px 24px rgba(167,27,71,.35);
  transition:opacity .18s;
}
.abt-btn-primary:hover{ opacity:.88; color:#fff; }

.abt-btn-secondary{
  display:inline-flex;
  align-items:center;
  gap:8px;
  padding:14px 32px;
  border-radius:10px;
  border:none;
  background:#0F4650;
  color:#fff;
  font-size:14px;
  font-weight:600;
  text-decoration:none;
  transition:background .18s;
}
.abt-btn-secondary:hover{ background:#1c6070; color:#fff; }

@media(max-width:640px){
  .abt-hero__glass{ padding:32px 24px; }
}

/* ── COMPARISON TABLE ── */
.abt-compare{
  padding:80px 0;
  background:linear-gradient(180deg,#fff 0%,#FEF8F5 100%);
}

.abt-compare__head{
  text-align:center;
  margin-bottom:40px;
}

.abt-compare__eyebrow{
  font-size:11px;
  font-weight:800;
  text-transform:uppercase;
  letter-spacing:.20em;
  color:var(--ab);
  display:block;
  margin-bottom:12px;
}

.abt-compare__h2{
  margin:0;
  font-size:clamp(28px,3.5vw,40px);
  font-weight:700;
  color:var(--abt);
  letter-spacing:-.8px;
}

.abt-compare__wrap{
  overflow-x:auto;
}

.abt-compare__table{
  width:100%;
  border-collapse:collapse;
}

.abt-compare__table thead tr{
  border-bottom:2px solid #E2E8EA;
}

.abt-compare__table th{
  padding:16px;
  font-size:14px;
}

.abt-compare__table th:first-child{
  text-align:left;
  color:rgba(15,70,80,0.70);
}

.abt-compare__table th.col-trad{
  text-align:center;
  color:var(--ab);
  font-weight:700;
}

.abt-compare__table th.col-kin{
  text-align:center;
  color:var(--abt);
  font-weight:700;
  background:rgba(15,70,80,0.05);
  border-radius:12px 12px 0 0;
}

.abt-compare__table tbody tr{
  border-bottom:1px solid #E2E8EA;
}

.abt-compare__table td{
  padding:16px;
}

.abt-compare__table td:first-child{
  font-weight:700;
  color:var(--abt);
}

.abt-compare__table td.col-trad{
  text-align:center;
  color:#94a3b8;
}

.abt-compare__table td.col-kin{
  text-align:center;
  font-weight:600;
  color:var(--ab);
  background:rgba(167,27,71,0.05);
}

/* ── CORE PILLARS ── */
.abt-pillars{
  padding:80px 0;
  background:#fff;
}

.abt-pillars__head{
  text-align:center;
  margin-bottom:40px;
}

.abt-pillars__eyebrow{
  font-size:11px;
  font-weight:800;
  text-transform:uppercase;
  letter-spacing:.20em;
  color:var(--ab);
  display:block;
  margin-bottom:12px;
}

.abt-pillars__h2{
  margin:0;
  font-size:clamp(28px,3.5vw,40px);
  font-weight:700;
  color:var(--abt);
  letter-spacing:-.8px;
}

.abt-pillars__sub{
  margin:12px auto 0;
  max-width:560px;
  color:#64748b;
  font-size:15px;
  line-height:1.7;
}

.abt-pillars__grid{
  display:grid;
  grid-template-columns:repeat(4,1fr);
  gap:20px;
}

.abt-pillar{
  background:#fff;
  border:1px solid #E2E8EA;
  border-radius:14px;
  padding:24px;
  box-shadow:0 4px 16px rgba(0,0,0,.04);
  transition:box-shadow .22s,transform .22s;
}

.abt-pillar:hover{
  box-shadow:0 10px 32px rgba(0,0,0,.08);
  transform:translateY(-3px);
}

.abt-pillar__icon{
  width:48px;
  height:48px;
  border-radius:10px;
  background:rgba(167,27,71,0.10);
  display:flex;
  align-items:center;
  justify-content:center;
  color:var(--ab);
  margin-bottom:16px;
  transition:transform .22s;
}

.abt-pillar:hover .abt-pillar__icon{ transform:scale(1.10); }

.abt-pillar h3{
  margin:0 0 8px;
  font-size:16px;
  font-weight:700;
  color:var(--abt);
}

.abt-pillar p{
  margin:0;
  font-size:13px;
  color:#64748b;
  line-height:1.65;
}

/* ── INFRASTRUCTURE STATS ── */
.abt-infra{
  padding:80px 0;
  background:#0A1A1F;
  position:relative;
  overflow:hidden;
  text-align:center;
}

.abt-infra__bg{
  position:absolute;
  inset:0;
  z-index:0;
  opacity:.10;
}

.abt-infra__bg img{
  width:100%;
  height:100%;
  object-fit:cover;
}

.abt-infra__inner{
  position:relative;
  z-index:1;
}

.abt-infra__h2{
  margin:0;
  font-size:clamp(28px,3.5vw,40px);
  font-weight:700;
  color:#fff;
  letter-spacing:-.8px;
}

.abt-infra__sub{
  margin:14px auto 0;
  max-width:560px;
  color:rgba(255,255,255,0.55);
  font-size:15px;
  line-height:1.7;
}

.abt-infra__grid{
  display:grid;
  grid-template-columns:repeat(3,1fr);
  gap:24px;
  margin-top:48px;
}

.abt-infra__card{
  background:rgba(255,255,255,0.05);
  backdrop-filter:blur(12px);
  -webkit-backdrop-filter:blur(12px);
  border:1px solid rgba(255,255,255,0.10);
  border-radius:14px;
  padding:32px 24px;
}

.abt-infra__card-icon{
  width:48px;
  height:48px;
  margin:0 auto 16px;
  color:var(--ab);
}

.abt-infra__num{
  font-size:40px;
  font-weight:900;
  color:#fff;
  letter-spacing:-1.5px;
}

.abt-infra__label{
  margin-top:8px;
  color:rgba(255,255,255,0.50);
  font-size:14px;
}

/* ── VALUES ── */
.abt-values{
  padding:80px 0;
  background:linear-gradient(135deg,#fff 0%,#FEF8F5 55%,#fff 100%);
}

.abt-values__head{
  text-align:center;
  margin-bottom:40px;
}

.abt-values__eyebrow{
  font-size:11px;
  font-weight:800;
  text-transform:uppercase;
  letter-spacing:.20em;
  color:var(--ab);
  display:block;
  margin-bottom:12px;
}

.abt-values__h2{
  margin:0;
  font-size:clamp(28px,3.5vw,40px);
  font-weight:700;
  color:var(--abt);
  letter-spacing:-.8px;
}

.abt-values__sub{
  margin:12px auto 0;
  max-width:560px;
  color:#64748b;
  font-size:15px;
  line-height:1.7;
}

.abt-values__grid{
  display:grid;
  grid-template-columns:repeat(3,1fr);
  gap:20px;
}

.abt-value{
  background:#fff;
  border:1px solid #E2E8EA;
  border-radius:14px;
  padding:32px 24px;
  box-shadow:0 4px 16px rgba(0,0,0,.04);
  transition:box-shadow .22s;
}

.abt-value:hover{ box-shadow:0 12px 36px rgba(0,0,0,.09); }

.abt-value__icon{
  width:48px;
  height:48px;
  border-radius:10px;
  background:rgba(167,27,71,0.10);
  display:flex;
  align-items:center;
  justify-content:center;
  color:var(--ab);
  margin-bottom:20px;
}

.abt-value h3{
  margin:0 0 12px;
  font-size:18px;
  font-weight:700;
  color:var(--abt);
}

.abt-value p{
  margin:0;
  font-size:14px;
  color:#64748b;
  line-height:1.7;
}

/* ── CTA BLOCK ── */
.abt-cta-wrap{
  padding:80px 0;
  background:#fff;
  position:relative;
  overflow:hidden;
}

.abt-cta-wrap::before{
  content:'';
  position:absolute;
  top:0; right:0;
  width:350px; height:350px;
  border-radius:50%;
  background:rgba(167,27,71,0.05);
  filter:blur(60px);
  pointer-events:none;
}

.abt-cta-wrap::after{
  content:'';
  position:absolute;
  bottom:0; left:0;
  width:300px; height:300px;
  border-radius:50%;
  background:rgba(15,70,80,0.05);
  filter:blur(60px);
  pointer-events:none;
}

.abt-cta-card{
  position:relative;
  background:var(--abt);
  border-radius:20px;
  padding:56px 48px;
  text-align:center;
  box-shadow:0 24px 60px rgba(15,70,80,.30);
  overflow:hidden;
}

.abt-cta-card::before{
  content:'';
  position:absolute;
  top:0; right:0;
  width:260px; height:260px;
  border-radius:50%;
  background:rgba(167,27,71,0.10);
  filter:blur(50px);
}

.abt-cta-card::after{
  content:'';
  position:absolute;
  bottom:0; left:0;
  width:200px; height:200px;
  border-radius:50%;
  background:rgba(255,255,255,0.05);
  filter:blur(40px);
}

.abt-cta-card__inner{
  position:relative;
  z-index:1;
}

.abt-cta-badge{
  display:inline-flex;
  align-items:center;
  gap:8px;
  background:rgba(167,27,71,0.20);
  padding:8px 16px;
  border-radius:8px;
  color:var(--ab);
  font-size:12px;
  font-weight:700;
  margin-bottom:24px;
}

.abt-cta-card__h2{
  margin:0;
  font-size:clamp(28px,3.5vw,40px);
  font-weight:700;
  color:#fff;
  letter-spacing:-.8px;
}

.abt-cta-card__sub{
  margin:14px auto 0;
  max-width:440px;
  color:rgba(255,255,255,0.60);
  font-size:15px;
  line-height:1.7;
}

.abt-cta-card__btns{
  display:flex;
  flex-wrap:wrap;
  gap:12px;
  justify-content:center;
  margin-top:32px;
}

.abt-cta-card__btns a{
  display:inline-flex;
  align-items:center;
  padding:14px 36px;
  border-radius:10px;
  font-size:14px;
  font-weight:700;
  text-decoration:none;
  transition:all .18s;
}

.abt-cta-card__btns a.primary{
  background:var(--ab);
  color:#fff;
  box-shadow:0 8px 24px rgba(167,27,71,.40);
}

.abt-cta-card__btns a.primary:hover{ background:#8a1539; color:#fff; }

.abt-cta-card__btns a.secondary{
  background:rgba(255,255,255,0.10);
  border:1px solid rgba(255,255,255,0.30);
  color:#fff;
}

.abt-cta-card__btns a.secondary:hover{ background:rgba(255,255,255,0.18); color:#fff; }

.abt-cta-card__note{
  margin:20px 0 0;
  color:rgba(255,255,255,0.30);
  font-size:13px;
}

/* ── IDENTITY / STATS ── */
.abt-identity{
  padding:80px 0;
  background:linear-gradient(135deg,#fff 0%,#FEF8F5 55%,#fff 100%);
  position:relative;
  overflow:hidden;
}

.abt-identity::after{
  content:'';
  position:absolute;
  top:40px;
  right:0;
  width:350px;
  height:350px;
  border-radius:50%;
  background:rgba(167,27,71,0.04);
  filter:blur(60px);
  pointer-events:none;
}

.abt-identity__grid{
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:60px;
  align-items:center;
}

.abt-identity__eyebrow{
  font-size:11px;
  font-weight:800;
  text-transform:uppercase;
  letter-spacing:.20em;
  color:var(--ab);
  margin-bottom:14px;
  display:block;
}

.abt-identity__h2{
  margin:0;
  font-size:clamp(32px,3.5vw,46px);
  font-weight:700;
  color:var(--abt);
  letter-spacing:-1px;
  line-height:1.1;
}

.abt-identity__copy{
  margin:18px 0 0;
  color:#475569;
  font-size:15px;
  line-height:1.8;
}

.abt-identity__quote{
  margin-top:28px;
  padding:20px 24px;
  background:linear-gradient(135deg,rgba(15,70,80,0.05),rgba(167,27,71,0.05));
  border-left:4px solid var(--ab);
  border-radius:0 12px 12px 0;
}

.abt-identity__quote p{
  margin:0;
  font-style:italic;
  color:var(--abt);
  font-weight:600;
  font-size:15px;
  line-height:1.7;
}

.abt-identity__quote-meta{
  display:flex;
  align-items:center;
  gap:12px;
  margin-top:14px;
}

.abt-identity__quote-icon{
  width:38px;
  height:38px;
  border-radius:10px;
  background:var(--ab);
  display:flex;
  align-items:center;
  justify-content:center;
  color:#fff;
  font-weight:800;
  font-size:13px;
}

.abt-identity__quote-name{
  color:var(--abt);
  font-weight:700;
  font-size:14px;
}

.abt-identity__quote-sub{
  color:#94a3b8;
  font-size:12px;
}

.abt-stats-grid{
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:16px;
}

.abt-stat{
  background:#fff;
  border:1px solid #E2E8EA;
  border-radius:14px;
  padding:20px;
  box-shadow:0 4px 16px rgba(0,0,0,0.04);
  transition:box-shadow .22s ease;
}

.abt-stat:hover{ box-shadow:0 8px 28px rgba(0,0,0,0.08); }

.abt-stat__icon{
  width:32px;
  height:32px;
  color:var(--ab);
  margin-bottom:12px;
}

.abt-stat__num{
  font-size:28px;
  font-weight:900;
  color:var(--abt);
  letter-spacing:-1px;
}

.abt-stat__label{
  color:#94a3b8;
  font-size:12px;
  margin-top:4px;
}

/* ── WHY WE EXIST ── */
.abt-why{
  padding:80px 0;
  background:var(--abn);
}

.abt-why__head{
  text-align:center;
  max-width:600px;
  margin:0 auto 48px;
}

.abt-why__eyebrow{
  font-size:11px;
  font-weight:800;
  text-transform:uppercase;
  letter-spacing:.20em;
  color:var(--ab);
  display:block;
  margin-bottom:14px;
}

.abt-why__h2{
  margin:0;
  font-size:clamp(30px,3.5vw,44px);
  font-weight:700;
  color:#fff;
  letter-spacing:-.8px;
}

.abt-why__sub{
  margin:14px 0 0;
  color:rgba(255,255,255,0.55);
  font-size:15px;
  line-height:1.7;
}

.abt-why__grid{
  display:grid;
  grid-template-columns:repeat(4,1fr);
  gap:16px;
}

.abt-why__card{
  background:rgba(255,255,255,0.04);
  border:1px solid rgba(255,255,255,0.08);
  border-radius:16px;
  padding:24px;
  transition:background .22s;
}

.abt-why__card:hover{ background:rgba(255,255,255,0.08); }

.abt-why__card-icon{
  width:40px;
  height:40px;
  border-radius:10px;
  background:rgba(167,27,71,0.15);
  display:flex;
  align-items:center;
  justify-content:center;
  color:var(--ab);
  margin-bottom:16px;
}

.abt-why__card h3{
  margin:0 0 10px;
  font-size:15px;
  font-weight:700;
  color:#fff;
}

.abt-why__card p{
  margin:0;
  font-size:13px;
  color:rgba(255,255,255,0.50);
  line-height:1.65;
}

/* ── CORRIDORS ── */
.abt-corridors{
  padding:80px 0;
  background:linear-gradient(135deg,#f7f5f0,#eef8f2 54%,#f7f5f0);
}

.abt-corridors__head{
  max-width:720px;
  margin-bottom:36px;
}

.abt-corridors__eyebrow{
  font-size:11px;
  font-weight:800;
  text-transform:uppercase;
  letter-spacing:.20em;
  color:var(--ab);
  display:block;
  margin-bottom:12px;
}

.abt-corridors__h2{
  margin:0;
  font-size:clamp(30px,3.5vw,44px);
  font-weight:700;
  color:var(--atx);
  letter-spacing:-.8px;
}

.abt-corridors__sub{
  margin:14px 0 0;
  color:var(--amu);
  font-size:15px;
  line-height:1.75;
}

.abt-corridors__grid{
  display:grid;
  grid-template-columns:repeat(3,1fr);
  gap:14px;
}

.abt-corridor{
  border:1px solid rgba(15,70,80,0.12);
  border-radius:16px;
  background:#fff;
  padding:16px 18px;
  box-shadow:0 4px 16px rgba(167,27,71,.05);
  transition:transform .22s ease,box-shadow .22s ease;
}

.abt-corridor:hover{
  transform:translateY(-4px);
  box-shadow:0 14px 36px rgba(167,27,71,.10);
}

.abt-corridor__route{
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:10px;
}

.abt-corridor__country{
  display:flex;
  align-items:center;
  gap:8px;
}

.abt-corridor__country img{
  width:32px;
  height:22px;
  border-radius:4px;
  object-fit:cover;
}

.abt-corridor__country span{
  font-size:13px;
  font-weight:800;
  color:var(--atx);
}

.abt-corridor__arrow{
  color:var(--ab);
  font-size:16px;
  font-weight:900;
}

.abt-corridor__status{
  margin-top:12px;
  display:flex;
  align-items:center;
  justify-content:space-between;
  padding-top:12px;
  border-top:1px solid rgba(15,70,80,0.08);
  color:var(--amu);
  font-size:12px;
}

.abt-corridor__status b{
  background:#ecfdf5;
  color:#047857;
  border:1px solid #a7f3d0;
  border-radius:999px;
  padding:4px 10px;
  font-size:10px;
  font-weight:800;
}

.abt-corridors__cta{
  margin-top:24px;
  padding:24px 28px;
  border-radius:18px;
  background:var(--abn);
  color:#fff;
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:20px;
}

.abt-corridors__cta h3{
  margin:0;
  font-size:18px;
  font-weight:800;
}

.abt-corridors__cta p{
  margin:6px 0 0;
  color:rgba(255,255,255,.60);
  font-size:13px;
  line-height:1.6;
}

.abt-corridors__cta a{
  display:inline-flex;
  align-items:center;
  gap:8px;
  padding:12px 22px;
  border-radius:10px;
  background:#fff;
  color:var(--ab);
  font-size:13px;
  font-weight:800;
  white-space:nowrap;
  text-decoration:none;
}

/* ── BLOG PREVIEW ── */
.abt-blog{
  padding:80px 0;
  background:#fff;
}

.abt-blog__head{
  text-align:center;
  max-width:560px;
  margin:0 auto 40px;
}

.abt-blog__eyebrow{
  font-size:11px;
  font-weight:800;
  text-transform:uppercase;
  letter-spacing:.20em;
  color:var(--ab);
  display:block;
  margin-bottom:12px;
}

.abt-blog__h2{
  margin:0;
  font-size:clamp(28px,3vw,40px);
  font-weight:700;
  color:var(--atx);
  letter-spacing:-.6px;
}

.abt-blog__grid{
  display:grid;
  grid-template-columns:repeat(3,1fr);
  gap:20px;
}

.abt-blog__card{
  position:relative;
  overflow:hidden;
  height:360px;
  border-radius:24px;
  box-shadow:0 10px 36px rgba(0,0,0,.12);
  transition:transform .28s ease,box-shadow .28s ease;
}

.abt-blog__card:hover{
  transform:translateY(-6px);
  box-shadow:0 20px 56px rgba(0,0,0,.18);
}

.abt-blog__card img{
  width:100%;
  height:100%;
  object-fit:cover;
  transition:transform .7s ease;
}

.abt-blog__card:hover img{ transform:scale(1.06); }

.abt-blog__card::after{
  content:'';
  position:absolute;
  inset:0;
  background:linear-gradient(to top,rgba(0,0,0,.88) 0%,rgba(0,0,0,.45) 50%,transparent 100%);
}

.abt-blog__body{
  position:absolute;
  bottom:0;
  left:0;
  right:0;
  z-index:2;
  padding:24px;
  color:#fff;
}

.abt-blog__cat{
  display:inline-block;
  padding:4px 10px;
  border-radius:999px;
  background:var(--ab);
  color:#fff;
  font-size:10px;
  font-weight:800;
  letter-spacing:.08em;
  text-transform:uppercase;
  margin-bottom:10px;
}

.abt-blog__body h3{
  margin:0 0 8px;
  font-size:17px;
  font-weight:700;
  color:#fff;
  line-height:1.35;
}

.abt-blog__body p{
  margin:0;
  font-size:13px;
  color:rgba(255,255,255,0.70);
  line-height:1.6;
}

.abt-blog__link{
  display:inline-flex;
  align-items:center;
  gap:6px;
  margin-top:14px;
  font-size:12px;
  font-weight:700;
  color:rgba(255,255,255,0.80);
  text-decoration:none;
  border-bottom:1px solid rgba(255,255,255,0.40);
  padding-bottom:1px;
}

/* ── MOTION ── */
.am-about .am-motion{
  opacity:0;
  transform:translateY(24px);
  transition:opacity .75s ease,transform .75s ease;
}
.am-about .am-motion.am-show{
  opacity:1;
  transform:translateY(0);
}

/* ── RESPONSIVE ── */
@media(max-width:900px){
  .abt-identity__grid,.abt-why__grid{
    grid-template-columns:1fr 1fr;
  }
  .abt-corridors__grid{
    grid-template-columns:1fr 1fr;
  }
  .abt-blog__grid{
    grid-template-columns:1fr 1fr;
  }
  .abt-pillars__grid{
    grid-template-columns:1fr 1fr;
  }
  .abt-infra__grid{
    grid-template-columns:1fr 1fr;
  }
  .abt-values__grid{
    grid-template-columns:1fr;
  }
}

@media(max-width:640px){
  .abt-hero__inner{ padding-top:calc(40px + var(--header-safe)); }
  .abt-identity__grid{ grid-template-columns:1fr; }
  .abt-stats-grid{ grid-template-columns:1fr 1fr; }
  .abt-why__grid{ grid-template-columns:1fr 1fr; }
  .abt-corridors__grid{ grid-template-columns:1fr; }
  .abt-blog__grid{ grid-template-columns:1fr; }
  .abt-corridors__cta{ flex-direction:column; }
  .abt-corridors__cta a{ width:100%; justify-content:center; }
  .abt-pillars__grid{ grid-template-columns:1fr 1fr; }
  .abt-infra__grid{ grid-template-columns:1fr; }
  .abt-cta-card{ padding:40px 24px; }
  .abt-cta-card__btns a{ flex:1; justify-content:center; }
}

@media(max-width:480px){
  .abt-why__grid{ grid-template-columns:1fr; }
  .abt-stats-grid{ grid-template-columns:1fr 1fr; }
  .abt-pillars__grid{ grid-template-columns:1fr; }
}
</style>
@endpush

<div class="am-about">

  {{-- ── HERO ── --}}
  <section class="abt-hero">
    <div class="abt-hero__bg">
      <img src="{{ asset('assets/images/kinmoney/about-D53uqXLX.jpeg') }}" alt="AkMoney global transfer" class="abt-hero__img">
      <div class="abt-hero__overlay"></div>
    </div>

    <div class="abt-hero__inner am-w">
      <div class="abt-hero__glass">
        <span class="abt-hero__kicker">The AkMoney Manifesto</span>

        <h1 class="abt-hero__h1">
          We build what others
          <span>only talk about.</span>
        </h1>

        <p class="abt-hero__copy">
          {!! siteNameHtml() !!} is a remittance infrastructure company focused on building transparent, fast, and human-centric money transfer systems. We operate at the intersection of fintech, customer trust, and operational excellence.
        </p>

        <div class="abt-hero__ctas">
          <a href="{{ route('user.register') }}" class="abt-btn-primary" style="color:#fff;">Start Sending</a>
          <a href="{{ url('/contact') }}" class="abt-btn-secondary" style="color:#fff;">Watch story →</a>
        </div>
      </div>
    </div>
  </section>

  {{-- ── IDENTITY / STATS ── --}}
  <section class="abt-identity">
    <div class="am-w">
      <div class="abt-identity__grid">

        <div class="am-motion">
          <span class="abt-identity__eyebrow">The Identity</span>
          <h2 class="abt-identity__h2">What {!! siteNameHtml() !!} Is.</h2>
          <p class="abt-identity__copy">
            {!! siteNameHtml() !!} is not just another remittance app. We are a <strong style="color:var(--ab);">remittance infrastructure company</strong> built to serve people who deserve better — faster delivery, transparent pricing, and real compliance you can trust.
          </p>
          <div class="abt-identity__quote">
            <p>"We don't just give you a lower fee. We give you the infrastructure, the confidence, and the speed your family deserves."</p>
            <div class="abt-identity__quote-meta">
              <div class="abt-identity__quote-icon">KM</div>
              <div>
                <div class="abt-identity__quote-name">{!! siteNameHtml() !!} Promise</div>
                <div class="abt-identity__quote-sub">Est. 2020 · FINTRAC Registered</div>
              </div>
            </div>
          </div>
        </div>

        <div class="abt-stats-grid am-motion">
          <div class="abt-stat">
            <div class="abt-stat__icon">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15 15 0 0 1 0 20M12 2a15 15 0 0 0 0 20"/></svg>
            </div>
            <div class="abt-stat__num">180+</div>
            <div class="abt-stat__label">Countries Served</div>
          </div>
          <div class="abt-stat">
            <div class="abt-stat__icon">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 1 0 0 7h5a3.5 3.5 0 1 1 0 7H6"/></svg>
            </div>
            <div class="abt-stat__num">$200M+</div>
            <div class="abt-stat__label">Annual Volume</div>
          </div>
          <div class="abt-stat">
            <div class="abt-stat__icon">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            </div>
            <div class="abt-stat__num">4.9/5</div>
            <div class="abt-stat__label">Customer Rating</div>
          </div>
          <div class="abt-stat">
            <div class="abt-stat__icon">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div class="abt-stat__num">&lt; 2 min</div>
            <div class="abt-stat__label">Avg Speed</div>
          </div>
        </div>

      </div>
    </div>
  </section>

  {{-- ── WHY WE EXIST ── --}}
  <section class="abt-why">
    <div class="am-w">
      <div class="abt-why__head am-motion">
        <span class="abt-why__eyebrow">Execution over promises</span>
        <h2 class="abt-why__h2">Why {!! siteNameHtml() !!} Exists</h2>
        <p class="abt-why__sub">Across the globe, the need for fair remittances is massive, but the reality has been poor exchange rates and hidden fees. We built the solution.</p>
      </div>

      <div class="abt-why__grid">
        <div class="abt-why__card am-motion">
          <div class="abt-why__card-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
          </div>
          <h3>Hidden Fees</h3>
          <p>No more opaque pricing. We show you exactly what you pay before you send.</p>
        </div>

        <div class="abt-why__card am-motion">
          <div class="abt-why__card-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          </div>
          <h3>Slow Transfers</h3>
          <p>Money moved in minutes, not days. Speed is a feature, not a premium.</p>
        </div>

        <div class="abt-why__card am-motion">
          <div class="abt-why__card-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          </div>
          <h3>Trust Deficit</h3>
          <p>FINTRAC regulated and fully compliant. Your security is our legal obligation.</p>
        </div>

        <div class="abt-why__card am-motion">
          <div class="abt-why__card-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15 15 0 0 1 0 20M12 2a15 15 0 0 0 0 20"/></svg>
          </div>
          <h3>Limited Access</h3>
          <p>Expanding corridors to every community that needs better financial access.</p>
        </div>
      </div>
    </div>
  </section>

  {{-- ── COMPARISON TABLE ── --}}
  <section class="abt-compare">
    <div class="am-w">
      <div class="abt-compare__head am-motion">
        <span class="abt-compare__eyebrow">The AkMoney Edge</span>
        <h2 class="abt-compare__h2">Transparency Over Everything.</h2>
      </div>

      <div class="abt-compare__wrap am-motion">
        <table class="abt-compare__table">
          <thead>
            <tr>
              <th></th>
              <th class="col-trad">Traditional Remittance</th>
              <th class="col-kin">{!! siteNameHtml() !!} (The Infrastructure)</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Pricing</td>
              <td class="col-trad">Hidden markups + fees</td>
              <td class="col-kin">Real exchange rate, low flat fee</td>
            </tr>
            <tr>
              <td>Speed</td>
              <td class="col-trad">1–5 business days</td>
              <td class="col-kin">Minutes, sometimes seconds</td>
            </tr>
            <tr>
              <td>Support</td>
              <td class="col-trad">FAQ bots + email tickets</td>
              <td class="col-kin">Real humans, live chat, phone</td>
            </tr>
            <tr>
              <td>Transparency</td>
              <td class="col-trad">Fine print surprises</td>
              <td class="col-kin">Every cost upfront, always</td>
            </tr>
            <tr>
              <td>Delivery Guarantee</td>
              <td class="col-trad">No guarantees</td>
              <td class="col-kin">100% delivery assurance</td>
            </tr>
            <tr>
              <td>Mobile Experience</td>
              <td class="col-trad">Clunky apps</td>
              <td class="col-kin">Buttery smooth, intuitive</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </section>

  {{-- ── CORE PILLARS ── --}}
  <section class="abt-pillars">
    <div class="am-w">
      <div class="abt-pillars__head am-motion">
        <span class="abt-pillars__eyebrow">Proprietary Framework</span>
        <h2 class="abt-pillars__h2">A System, Not a Service.</h2>
        <p class="abt-pillars__sub">{!! siteNameHtml() !!} operates through a structured model that integrates four core pillars into one seamless system — eliminating fragmentation and accelerating every transfer.</p>
      </div>

      <div class="abt-pillars__grid">
        <div class="abt-pillar am-motion">
          <div class="abt-pillar__icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><circle cx="15" cy="15" r="2"/><path d="M9 15L15 9"/></svg>
          </div>
          <h3>Fair Pricing</h3>
          <p>Engineered for transparency. Real rates, no games, no surprises.</p>
        </div>

        <div class="abt-pillar am-motion">
          <div class="abt-pillar__icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
          </div>
          <h3>Fast Delivery</h3>
          <p>Optimized routing and settlement infrastructure across 180+ countries.</p>
        </div>

        <div class="abt-pillar am-motion">
          <div class="abt-pillar__icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.8M16 3.1a4 4 0 0 1 0 7.8"/></svg>
          </div>
          <h3>Human Support</h3>
          <p>Support systems that actually put people first. 24/7 real humans.</p>
        </div>

        <div class="abt-pillar am-motion">
          <div class="abt-pillar__icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2L3 7v7c0 5.5 9 8 9 8s9-2.5 9-8V7l-9-5z"/></svg>
          </div>
          <h3>Trust &amp; Security</h3>
          <p>Bank-grade security with human accountability. Your money is safe.</p>
        </div>
      </div>
    </div>
  </section>

  {{-- ── INFRASTRUCTURE STATS ── --}}
  <section class="abt-infra">
    <div class="abt-infra__bg">
      <img src="{{ asset('assets/images/kinmoney/about-D53uqXLX.jpeg') }}" alt="">
    </div>
    <div class="am-w abt-infra__inner">
      <h2 class="abt-infra__h2 am-motion">Built on Core Infrastructure.</h2>
      <p class="abt-infra__sub am-motion">We've built the systems, partnerships, and technology to move money reliably across borders — at scale, with care.</p>

      <div class="abt-infra__grid">
        <div class="abt-infra__card am-motion">
          <div class="abt-infra__card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.8M16 3.1a4 4 0 0 1 0 7.8"/></svg>
          </div>
          <div class="abt-infra__num">500K+</div>
          <div class="abt-infra__label">Customers Served</div>
        </div>

        <div class="abt-infra__card am-motion">
          <div class="abt-infra__card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
          </div>
          <div class="abt-infra__num">180+</div>
          <div class="abt-infra__label">Countries &amp; Territories</div>
        </div>

        <div class="abt-infra__card am-motion">
          <div class="abt-infra__card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L3 7v7c0 5.5 9 8 9 8s9-2.5 9-8V7l-9-5z"/></svg>
          </div>
          <div class="abt-infra__num">99.9%</div>
          <div class="abt-infra__label">Delivery Success Rate</div>
        </div>
      </div>
    </div>
  </section>

  {{-- ── VALUES ── --}}
  <section class="abt-values">
    <div class="am-w">
      <div class="abt-values__head am-motion">
        <span class="abt-values__eyebrow">Our Values</span>
        <h2 class="abt-values__h2">What guides us every day</h2>
        <p class="abt-values__sub">These core principles shape every decision we make, every product we build, and every customer we serve.</p>
      </div>

      <div class="abt-values__grid">
        <div class="abt-value am-motion">
          <div class="abt-value__icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M2 12L22 12M6 8L4 12L6 16M18 8L20 12L18 16M12 4L12 20M8 6L12 4L16 6M8 18L12 20L16 18"/></svg>
          </div>
          <h3>Radical Transparency</h3>
          <p>We show you exactly how we make money. No surprises, ever.</p>
        </div>

        <div class="abt-value am-motion">
          <div class="abt-value__icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          </div>
          <h3>Speed Without Sacrifice</h3>
          <p>Fast transfers that never compromise security or accuracy.</p>
        </div>

        <div class="abt-value am-motion">
          <div class="abt-value__icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1 7.8 7.8 7.8-7.8 1-1a5.5 5.5 0 0 0 0-7.8z"/></svg>
          </div>
          <h3>Customer-First Culture</h3>
          <p>Every decision starts with "what's best for the person sending?"</p>
        </div>
      </div>
    </div>
  </section>

  {{-- ── CTA BLOCK ── --}}
  <section class="abt-cta-wrap">
    <div class="am-w">
      <div class="abt-cta-card am-motion">
        <div class="abt-cta-card__inner">
          <div class="abt-cta-badge">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2L13.5 8.5 20 10 13.5 11.5 12 18 10.5 11.5 4 10 10.5 8.5 12 2z"/></svg>
            Join the movement
          </div>
          <h2 class="abt-cta-card__h2">Ready to send with confidence?</h2>
          <p class="abt-cta-card__sub">Join thousands who already trust {!! siteNameHtml() !!} for fast, transparent, and human-centric transfers.</p>
          <div class="abt-cta-card__btns">
            <a href="{{ route('user.register') }}" class="primary">Start sending today</a>
            <a href="{{ url('/contact') }}" class="secondary">Contact Us</a>
          </div>
          <p class="abt-cta-card__note">Built for people who send with care. Always transparent, always fast.</p>
        </div>
      </div>
    </div>
  </section>

  {{-- ── CORRIDORS ── --}}
  <section class="abt-corridors">
    <div class="am-w">
      <div class="abt-corridors__head am-motion">
        <span class="abt-corridors__eyebrow">Our Corridors</span>
        <h2 class="abt-corridors__h2">Where we operate</h2>
        <p class="abt-corridors__sub">
          {!! siteNameHtml() !!} supports key transfer corridors from Canada to Africa, the Middle East, and the United States, with a focus on reliability, compliance, and better user experience.
        </p>
      </div>

      <div class="abt-corridors__grid">
        @php
        $corridors = [
          ['from'=>'ca','to'=>'gh','fromCode'=>'CAD','toCode'=>'GHS','label'=>'Canada to Ghana'],
          ['from'=>'ca','to'=>'td','fromCode'=>'CAD','toCode'=>'XAF','label'=>'Canada to Chad'],
          ['from'=>'ca','to'=>'eg','fromCode'=>'CAD','toCode'=>'EGP','label'=>'Canada to Egypt'],
          ['from'=>'ca','to'=>'sa','fromCode'=>'CAD','toCode'=>'SAR','label'=>'Canada to Saudi Arabia'],
          ['from'=>'ca','to'=>'sd','fromCode'=>'CAD','toCode'=>'SDG','label'=>'Canada to Sudan'],
          ['from'=>'ca','to'=>'ae','fromCode'=>'CAD','toCode'=>'AED','label'=>'Canada to UAE'],
          ['from'=>'ca','to'=>'us','fromCode'=>'CAD','toCode'=>'USD','label'=>'Canada to United States'],
          ['from'=>'ca','to'=>'ng','fromCode'=>'CAD','toCode'=>'NGN','label'=>'Canada to Nigeria'],
          ['from'=>'ca','to'=>'ke','fromCode'=>'CAD','toCode'=>'KES','label'=>'Canada to Kenya'],
        ];
        @endphp
        @foreach($corridors as $c)
        <div class="abt-corridor am-motion">
          <div class="abt-corridor__route">
            <div class="abt-corridor__country">
              <img src="https://flagcdn.com/{{ $c['from'] }}.svg" alt="{{ $c['fromCode'] }}">
              <span>{{ $c['fromCode'] }}</span>
            </div>
            <span class="abt-corridor__arrow">→</span>
            <div class="abt-corridor__country">
              <img src="https://flagcdn.com/{{ $c['to'] }}.svg" alt="{{ $c['toCode'] }}">
              <span>{{ $c['toCode'] }}</span>
            </div>
          </div>
          <div class="abt-corridor__status">
            <span>{{ $c['label'] }}</span>
            <b>Available</b>
          </div>
        </div>
        @endforeach
      </div>

      <div class="abt-corridors__cta am-motion">
        <div>
          <h3>More corridors coming soon</h3>
          <p>We are expanding. Sign up to get notified when your country goes live.</p>
        </div>
        <a href="{{ route('user.register') }}">
          Get notified
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </a>
      </div>
    </div>
  </section>

  {{-- ── BLOG PREVIEW ── --}}
  <section class="abt-blog">
    <div class="am-w">
      <div class="abt-blog__head am-motion">
        <span class="abt-blog__eyebrow">From the Blog</span>
        <h2 class="abt-blog__h2">Insights & Updates</h2>
      </div>

      <div class="abt-blog__grid">
        <a href="{{ url('/blog') }}" class="abt-blog__card am-motion">
          <img src="{{ asset('assets/images/kinmoney/money-BEnZESby.jpeg') }}" alt="Smart money tips">
          <div class="abt-blog__body">
            <span class="abt-blog__cat">Money Tips</span>
            <h3>5 Smart Ways to Save on International Transfers</h3>
            <p>Simple tips that can help you maximize what your family receives.</p>
            <span class="abt-blog__link">Read More →</span>
          </div>
        </a>

        <a href="{{ url('/blog') }}" class="abt-blog__card am-motion">
          <img src="{{ asset('assets/images/kinmoney/transfer-DF6w7EmE.jpeg') }}" alt="Exchange rates">
          <div class="abt-blog__body">
            <span class="abt-blog__cat">Finance</span>
            <h3>How Exchange Rates Work and What to Watch For</h3>
            <p>Understand exchange rates and time your transfers for maximum value.</p>
            <span class="abt-blog__link">Read More →</span>
          </div>
        </a>

        <a href="{{ url('/blog') }}" class="abt-blog__card am-motion">
          <img src="{{ asset('assets/images/kinmoney/mobile-PJkhsLpI.jpeg') }}" alt="Security">
          <div class="abt-blog__body">
            <span class="abt-blog__cat">Security</span>
            <h3>The Importance of Security in International Money Transfers</h3>
            <p>How {!! siteNameHtml() !!} keeps your transfers encrypted and compliant.</p>
            <span class="abt-blog__link">Read More →</span>
          </div>
        </a>
      </div>
    </div>
  </section>

</div>

@push('script')
<script>
document.addEventListener('DOMContentLoaded', function(){
  var items = document.querySelectorAll('.am-about .am-motion');
  var observer = new IntersectionObserver(function(entries){
    entries.forEach(function(entry){
      if(entry.isIntersecting){
        entry.target.classList.add('am-show');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.12 });
  items.forEach(function(item){ observer.observe(item); });
});
</script>
@endpush
