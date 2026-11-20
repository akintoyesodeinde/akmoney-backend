<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ gs()->siteName($pageTitle ?? '500 — Server Error') }}</title>
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
    </style>
</head>
<body>

<div class="card">

    <div class="card-top">
        <span class="brand"><img src="{{ siteLogo('dark') }}" alt="{{ gs('site_name') }}"></span>

        <!-- illustration: broken gear -->
        <svg class="illus" viewBox="0 0 110 110" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- outer gear teeth -->
            <path d="M55 8 L60 18 L70 14 L72 25 L83 25 L81 36 L91 41 L85 50 L92 58 L83 63 L84 74 L73 74 L68 84 L58 79 L55 90 L52 79 L42 84 L37 74 L26 74 L27 63 L18 58 L25 50 L19 41 L29 36 L27 25 L38 25 L40 14 L50 18 Z"
                  fill="#1e1e2e" stroke="#2e2e42" stroke-width="1.5" stroke-linejoin="round"/>
            <!-- inner circle -->
            <circle cx="55" cy="55" r="18" fill="#0f0f1c" stroke="#2e2e42" stroke-width="1.5"/>
            <!-- X mark in center -->
            <line x1="47" y1="47" x2="63" y2="63" stroke="#E8192C" stroke-width="3" stroke-linecap="round"/>
            <line x1="63" y1="47" x2="47" y2="63" stroke="#E8192C" stroke-width="3" stroke-linecap="round"/>
            <!-- crack line across gear -->
            <path d="M38 30 L46 48 L40 55 L52 72" stroke="#E8192C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" opacity="0.6"/>
        </svg>

        <div class="code">500</div>
    </div>

    <div class="card-body">
        <h1 class="err-title">@lang('Internal Server Error')</h1>
        <p class="err-desc">@lang("Something went wrong on our end. We're already working on fixing it — please try again in a moment.")</p>
        <a href="{{ route('home') }}" class="btn-home">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
            @lang('Back to Home')
        </a>
    </div>

</div>

</body>
</html>
