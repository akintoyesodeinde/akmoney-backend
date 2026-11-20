<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ gs()->siteName($pageTitle ?? '403 — Access Forbidden') }}</title>
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

        <!-- illustration: shield with lock -->
        <svg class="illus" viewBox="0 0 110 110" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- shield body -->
            <path d="M55 10 L88 24 L88 52 C88 72 72 88 55 97 C38 88 22 72 22 52 L22 24 Z"
                  fill="#1e1e2e" stroke="#2e2e42" stroke-width="1.5" stroke-linejoin="round"/>
            <!-- shield inner -->
            <path d="M55 20 L80 31 L80 52 C80 67 68 79 55 87 C42 79 30 67 30 52 L30 31 Z"
                  fill="#0f0f1c" stroke="#2a2a3e" stroke-width="1"/>
            <!-- padlock body -->
            <rect x="44" y="55" width="22" height="16" rx="4" fill="#E8192C" opacity="0.9"/>
            <!-- padlock shackle -->
            <path d="M48 55 L48 48 C48 43 62 43 62 48 L62 55" stroke="#E8192C" stroke-width="3" stroke-linecap="round" fill="none" opacity="0.9"/>
            <!-- keyhole -->
            <circle cx="55" cy="62" r="3" fill="#0f0f1c"/>
            <rect x="53.5" y="63" width="3" height="4" rx="1" fill="#0f0f1c"/>
        </svg>

        <div class="code">403</div>
    </div>

    <div class="card-body">
        <h1 class="err-title">Access Forbidden</h1>
        <p class="err-desc">You don't have permission to access this page. If you believe this is a mistake, please contact support.</p>
        <a href="{{ route('home') }}" class="btn-home">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
            Back to Home
        </a>
    </div>

</div>

</body>
</html>
