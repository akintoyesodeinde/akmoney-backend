<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo isset($pageTitle) ? 'Dashboard | ' . htmlspecialchars($pageTitle) : 'Dashboard'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
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
    <script src="scripts/sidebar.js"></script>
    <style>
        body {
            font-family: "Inter", sans-serif;
            scroll-behavior: smooth;
        }

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
                    fontFamily: {
                        sans: ["Inter", "ui-sans-serif", "system-ui", "sans-serif"],
                    },
                    colors: {
                        "brand-teal": "<?php echo $brand_teal; ?>",
                        "brand-gold": "<?php echo $brand_gold; ?>",
                        "brand-orange": "<?php echo $brand_orange; ?>",
                        "brand-orange-light": "<?php echo $brand_orange_light; ?>",
                        "brand-orange-dark": "<?php echo $brand_orange_dark; ?>",
                        "bg-main": "<?php echo $bg_main; ?>",
                        "bg-secondary": "<?php echo $bg_secondary; ?>",
                        "bg-tertiary": "<?php echo $bg_tertiary; ?>",
                        "card-white": "<?php echo $card_white; ?>",
                        "border-gray": "<?php echo $border_gray; ?>",
                        "border-light": "<?php echo $border_light; ?>",
                        "text-primary": "<?php echo $text_primary; ?>",
                        "text-secondary": "<?php echo $text_secondary; ?>",
                        "text-tertiary": "<?php echo $text_tertiary; ?>",
                        "text-muted": "<?php echo $text_muted; ?>",
                        /* Brand tokens */
                        "air-blue": "#A71B47",
                        "air-blue-dark": "#8a1539",
                        "air-navy": "#071318",
                        "air-soft": "#eef7ff",
                    },
                },
            },
        };
    </script>
</head>
