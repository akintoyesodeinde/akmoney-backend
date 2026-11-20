<!DOCTYPE html>
<html lang="{{ config('app.locale') }}" class="theme-<?php echo auth()->user()->current_mode; ?>" itemscope itemtype="http://schema.org/WebPage">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ gs()->siteName(__($pageTitle)) }}</title>
    @include('partials.seo')
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Geist:wght@600&display=swap"
        rel="stylesheet" />
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.5.0/remixicon.min.css" />
    <script src="/assets/sidebar.js"></script>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ siteFavicon() }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ siteFavicon() }}">
    <style>
        :root { --nav-active-bg: #{{ gs('base_color') }}; }
        body {
            font-family: "Poppins", sans-serif;
            scroll-behavior: smooth;
        }
        /* Unified heading style: Poppins everywhere, sizes halved vs theme defaults */
        h1, h2, h3, h4, h5, h6,
        .h1, .h2, .h3, .h4, .h5, .h6 {
            font-family: "Poppins", sans-serif !important;
            font-style: normal !important;
        }
        .section-title h2 { font-size: 18px !important; }
        h1 { font-size: 18px !important; }
        h2 { font-size: 16px !important; }
        h3 { font-size: 14px !important; }
        h4 { font-size: 13px !important; }
        h5 { font-size: 12px !important; }
        h6 { font-size: 11px !important; }

        /* Slide out animation for KYC notification */
        @keyframes slideOutRight {
            from {
                opacity: 1;
                transform: translateX(0);
            }

            to {
                opacity: 0;
                transform: translateX(400px);
            }
        }

        /* Slide in animation for KYC notification */
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(400px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        #kycNotification {
            animation: slideInRight 0.5s ease-out forwards;
        }

        /* Premium fade-in animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Subtle scale animation */
        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* Smooth glow effect */
        @keyframes glow {

            0%,
            100% {
                box-shadow: 0 0 10px rgba(8, 75, 95, 0.1);
            }

            50% {
                box-shadow: 0 0 20px rgba(8, 75, 95, 0.2);
            }
        }

        /* Floating animation */
        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-8px);
            }
        }

        /* Smooth entrance from left */
        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Smooth entrance from right */
        @keyframes slideInRightSmooth {
            from {
                opacity: 0;
                transform: translateX(20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Bounce animation for buttons */
        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-3px);
            }
        }

        /* Premium card shadow animation */
        @keyframes shadowPulse {

            0%,
            100% {
                box-shadow: 0 20px 40px -15px rgba(8, 75, 95, 0.2);
            }

            50% {
                box-shadow: 0 20px 50px -10px rgba(8, 75, 95, 0.3);
            }
        }

        /* Apply animations to elements */
        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        .animate-scale-in {
            animation: scaleIn 0.5s ease-out forwards;
        }

        .animate-glow {
            animation: glow 3s ease-in-out infinite;
        }

        .animate-float {
            animation: float 3s ease-in-out infinite;
        }

        .animate-slide-in-left {
            animation: slideInLeft 0.6s ease-out forwards;
        }

        .animate-slide-in-right {
            animation: slideInRightSmooth 0.6s ease-out forwards;
        }

        .animate-bounce-light {
            animation: bounce 1s ease-in-out infinite;
        }

        .animate-shadow-pulse {
            animation: shadowPulse 2s ease-in-out infinite;
        }

        /* Staggered animations */
        .stagger-1 {
            animation-delay: 0.1s;
        }

        .stagger-2 {
            animation-delay: 0.2s;
        }

        .stagger-3 {
            animation-delay: 0.3s;
        }

        .stagger-4 {
            animation-delay: 0.4s;
        }

        .stagger-5 {
            animation-delay: 0.5s;
        }
    </style>

    <!-- Theme transition styles -->
    <style>
        /* Smooth theme transitions */
        * {
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }

        /* Theme-specific styles */
        html.theme-light {
            color-scheme: light;
        }

        html.theme-dark {
            color-scheme: dark;
        }

        /* Override transition for theme toggle button icons */
        #theme-toggle svg {
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
    </style>

        <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        // Brand colors
                        "brand-gold": @json($theme['brand-gold']),
                        "brand-teal": @json($theme['brand-teal']),
                    
                        // Accent oranges
                        "brand-orange": @json($theme['brand-orange']),
                        "brand-orange-light": @json($theme['brand-orange-light']),
                        "brand-orange-dark": @json($theme['brand-orange-dark']),
                    
                        // Backgrounds
                        "bg-main": @json($theme['bg-main']),
                        "bg-secondary": @json($theme['bg-secondary']),
                        "bg-tertiary": @json($theme['bg-tertiary']),
                    
                        // Cards & surfaces
                        "card-white": @json($theme['card-white']),
                    
                        // Borders
                        "border-gray": @json($theme['border-gray']),
                        "border-light": @json($theme['border-light']),
                    
                        // Text
                        "text-primary": @json($theme['text-primary']),
                        "text-secondary": @json($theme['text-secondary']),
                        "text-tertiary": @json($theme['text-tertiary']),
                        "text-muted": @json($theme['text-muted']),
                    },

                },
            },
        };
    </script>
<!-- jQuery first -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

@stack('style')
</head>

<body class="bg-bg-main max-w-9xl mx-auto {{ auth()->check() && auth()->user()->current_mode === 'dark' ? 'dark' : '' }}" data-current-page="@yield('activePage', 'dashboard')">
    <div class="flex min-h-screen  w-full flex-col md:flex-row">
        <div class="w-36 md:w-56 md:fixed md:left-0 md:top-0 md:h-screen">
            @include($activeTemplate . 'partials.sidebar')
        </div>
        <div class="flex-1 min-w-0 md:ml-56 flex flex-col min-h-screen">
            @include($activeTemplate . 'partials.header1')
            @include('partials.notify')

            <main class="p-3 md:p-6 w-full">
                <!-- KYC Verification Notification -->
                @include($activeTemplate . 'partials.kyc-notification')
                @yield('content')
            </main>
            @include($activeTemplate . 'partials.user_footer')
            @include($activeTemplate . 'partials.add-recipient-modal')
        </div>
    </div>

@stack('script')
</body>

</html>



