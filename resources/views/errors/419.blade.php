<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ gs()->siteName($pageTitle ?? '419 — Session Expired') }}</title>
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

        .btn-refresh {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: transparent;
            color: #E8192C;
            border: 1.5px solid #E8192C;
            padding: 12px 24px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
            transition: background 0.2s, color 0.2s, transform 0.15s;
        }
        .btn-refresh:hover {
            background: #E8192C;
            color: #fff;
            transform: translateY(-1px);
        }

        .actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }
    </style>
</head>
<body>

<div class="card">

    <div class="card-top">
        <span class="brand"><img src="{{ siteLogo('dark') }}" alt="{{ gs('site_name') }}"></span>

        <!-- illustration: clock with expired X -->
        <svg class="illus" viewBox="0 0 110 110" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- clock face -->
            <circle cx="55" cy="52" r="34" fill="#1e1e2e" stroke="#2e2e42" stroke-width="1.5"/>
            <circle cx="55" cy="52" r="27" fill="#0f0f1c" stroke="#2a2a3e" stroke-width="1"/>
            <!-- clock hands -->
            <line x1="55" y1="52" x2="55" y2="35" stroke="#3a3a5a" stroke-width="2.5" stroke-linecap="round"/>
            <line x1="55" y1="52" x2="68" y2="58" stroke="#3a3a5a" stroke-width="2.5" stroke-linecap="round"/>
            <!-- center dot -->
            <circle cx="55" cy="52" r="3" fill="#E8192C"/>
            <!-- X over clock to indicate expired -->
            <line x1="41" y1="38" x2="69" y2="66" stroke="#E8192C" stroke-width="3" stroke-linecap="round" opacity="0.85"/>
            <line x1="69" y1="38" x2="41" y2="66" stroke="#E8192C" stroke-width="3" stroke-linecap="round" opacity="0.85"/>
            <!-- clock rim ticks -->
            <line x1="55" y1="20" x2="55" y2="25" stroke="#2e2e42" stroke-width="2" stroke-linecap="round"/>
            <line x1="55" y1="79" x2="55" y2="84" stroke="#2e2e42" stroke-width="2" stroke-linecap="round"/>
            <line x1="21" y1="52" x2="26" y2="52" stroke="#2e2e42" stroke-width="2" stroke-linecap="round"/>
            <line x1="84" y1="52" x2="89" y2="52" stroke="#2e2e42" stroke-width="2" stroke-linecap="round"/>
            <!-- refresh arrow below clock -->
            <path d="M44 92 C44 88 47 85 55 85 C63 85 66 88 66 92" stroke="#E8192C" stroke-width="2" stroke-linecap="round" fill="none"/>
            <polyline points="62,89 66,92 62,96" stroke="#E8192C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
        </svg>

        <div class="code">419</div>
    </div>

    <div class="card-body">
        <h1 class="err-title">@lang('Session Expired')</h1>
        <p class="err-desc">@lang('Your session has timed out for security reasons. Please refresh the page or go back to the home page to continue.')</p>
        <div class="actions">
            <button class="btn-refresh" onclick="window.history.back(); setTimeout(function(){ window.location.reload(); }, 300);">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M17.65 6.35A7.958 7.958 0 0 0 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08A5.99 5.99 0 0 1 12 18c-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/></svg>
                @lang('Go Back & Refresh')
            </button>
            <a href="{{ route('home') }}" class="btn-home">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                @lang('Home')
            </a>
        </div>
    </div>

</div>

</body>
</html>
