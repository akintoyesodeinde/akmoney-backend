<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ gs()->siteName($pageTitle ?? '404 — Page Not Found') }}</title>
    <link rel="icon" type="image/png" href="{{ siteFavicon() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: #0a0a12;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .card {
            background: #fff;
            border-radius: 28px;
            overflow: hidden;
            width: 100%;
            max-width: 460px;
            box-shadow: 0 40px 100px rgba(0, 0, 0, 0.55);
        }

        /* ── top dark panel ── */
        .card-top {
            background: #0f0f1c;
            padding: 44px 40px 36px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }

        .brand {
            align-self: flex-start;
        }
        .brand img {
            max-height: 36px;
            width: auto;
            max-width: 160px;
            object-fit: contain;
        }

        .illus {
            width: 110px;
            height: 110px;
        }

        .code {
            font-size: 88px;
            font-weight: 800;
            color: #E8192C;
            line-height: 1;
            letter-spacing: -5px;
        }

        /* ── bottom white panel ── */
        .card-body {
            padding: 36px 40px 40px;
            text-align: center;
        }

        .err-title {
            font-size: 21px;
            font-weight: 700;
            color: #0a0a12;
            margin-bottom: 10px;
        }

        .err-desc {
            font-size: 14.5px;
            color: #6b7280;
            line-height: 1.65;
            margin-bottom: 28px;
        }

        .btn-home {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #E8192C;
            color: #fff;
            text-decoration: none;
            padding: 13px 26px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            transition: background 0.2s, transform 0.15s;
        }
        .btn-home:hover {
            background: #c8121f;
            transform: translateY(-1px);
            color: #fff;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="card">

    <div class="card-top">
        <span class="brand"><img src="{{ siteLogo('dark') }}" alt="{{ gs('site_name') }}"></span>

        <!-- illustration: magnifying glass over empty document -->
        <svg class="illus" viewBox="0 0 110 110" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="18" y="14" width="54" height="68" rx="7" fill="#1e1e2e" stroke="#2e2e42" stroke-width="1.5"/>
            <rect x="26" y="28" width="30" height="4" rx="2" fill="#2e2e42"/>
            <rect x="26" y="38" width="22" height="4" rx="2" fill="#2e2e42"/>
            <rect x="26" y="48" width="26" height="4" rx="2" fill="#2e2e42"/>
            <!-- magnifying glass -->
            <circle cx="70" cy="72" r="20" fill="#0f0f1c" stroke="#E8192C" stroke-width="3"/>
            <circle cx="70" cy="72" r="12" fill="#1a1a2e" stroke="#3a3a5a" stroke-width="1.5"/>
            <text x="66" y="77" font-family="DM Sans, sans-serif" font-size="12" font-weight="700" fill="#E8192C">?</text>
            <line x1="83" y1="85" x2="94" y2="96" stroke="#E8192C" stroke-width="3.5" stroke-linecap="round"/>
        </svg>

        <div class="code">404</div>
    </div>

    <div class="card-body">
        <h1 class="err-title">Page Not Found</h1>
        <p class="err-desc">We can't seem to find the page you're looking for. It may have been moved, deleted, or never existed.</p>
        <a href="{{ route('home') }}" class="btn-home">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
            Back to Home
        </a>
    </div>

</div>

</body>
</html>
