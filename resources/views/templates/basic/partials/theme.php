<?php
// theme.php - Theme configuration component with Light/Dark mode support
// Admin can modify these color values to change the website theme

// Start session for theme persistence
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Detect theme mode (default to light)
$current_mode = $_SESSION['theme_mode'] ?? 'light';

// Light Mode Colors
$light_brand_teal = "#99244f";
$light_brand_gold = "#ffb000";
$light_brand_orange = "#ff6b35";
$light_brand_orange_light = "#ff8c42";
$light_brand_orange_dark = "#e55a2b";
$light_bg_main = "#f7f9fb";
$light_bg_secondary = "#f1f5f9";
$light_bg_tertiary = "#e2e8f0";
$light_card_white = "#ffffff";
$light_border_gray = "#e6ebef";
$light_border_light = "#f1f5f9";
$light_text_primary = "#1f2937";
$light_text_secondary = "#6b7280";
$light_text_tertiary = "#9ca3af";
$light_text_muted = "#d1d5db";

// Dark Mode Colors
$dark_brand_teal = "#095d77"; // Lighter teal for dark backgrounds
$dark_brand_gold = "#fbbf24"; // Lighter gold for dark backgrounds
$dark_brand_orange = "#fb923c"; // Lighter orange for dark backgrounds
$dark_brand_orange_light = "#fdba74";
$dark_brand_orange_dark = "#f97316";
$dark_bg_main = "#0f172a"; // Very dark blue-gray
$dark_bg_secondary = "#1e293b"; // Dark blue-gray
$dark_bg_tertiary = "#334155"; // Medium dark blue-gray
$dark_card_white = "#1e293b"; // Dark card background
$dark_border_gray = "#475569"; // Dark border
$dark_border_light = "#334155"; // Lighter dark border
$dark_text_primary = "#f1f5f9"; // Light text
$dark_text_secondary = "#cbd5e1"; // Light secondary text
$dark_text_tertiary = "#94a3b8"; // Light tertiary text
$dark_text_muted = "#64748b"; // Muted light text

// Set current theme colors based on mode
if ($current_mode === 'dark') {
    $brand_teal = $dark_brand_teal;
    $brand_gold = $dark_brand_gold;
    $brand_orange = $dark_brand_orange;
    $brand_orange_light = $dark_brand_orange_light;
    $brand_orange_dark = $dark_brand_orange_dark;
    $bg_main = $dark_bg_main;
    $bg_secondary = $dark_bg_secondary;
    $bg_tertiary = $dark_bg_tertiary;
    $card_white = $dark_card_white;
    $border_gray = $dark_border_gray;
    $border_light = $dark_border_light;
    $text_primary = $dark_text_primary;
    $text_secondary = $dark_text_secondary;
    $text_tertiary = $dark_text_tertiary;
    $text_muted = $dark_text_muted;
} else {
    $brand_teal = $light_brand_teal;
    $brand_gold = $light_brand_gold;
    $brand_orange = $light_brand_orange;
    $brand_orange_light = $light_brand_orange_light;
    $brand_orange_dark = $light_brand_orange_dark;
    $bg_main = $light_bg_main;
    $bg_secondary = $light_bg_secondary;
    $bg_tertiary = $light_bg_tertiary;
    $card_white = $light_card_white;
    $border_gray = $light_border_gray;
    $border_light = $light_border_light;
    $text_primary = $light_text_primary;
    $text_secondary = $light_text_secondary;
    $text_tertiary = $light_text_tertiary;
    $text_muted = $light_text_muted;
}

// Theme configuration arrays for easy access
$theme_colors = array(
    "brand-teal" => $brand_teal,
    "brand-gold" => $brand_gold,
    "brand-orange" => $brand_orange,
    "brand-orange-light" => $brand_orange_light,
    "brand-orange-dark" => $brand_orange_dark,
    "bg-main" => $bg_main,
    "bg-secondary" => $bg_secondary,
    "bg-tertiary" => $bg_tertiary,
    "card-white" => $card_white,
    "border-gray" => $border_gray,
    "border-light" => $border_light,
    "text-primary" => $text_primary,
    "text-secondary" => $text_secondary,
    "text-tertiary" => $text_tertiary,
    "text-muted" => $text_muted
);

// Light theme colors array (for admin panel)
$light_theme_colors = array(
    "brand-teal" => $light_brand_teal,
    "brand-gold" => $light_brand_gold,
    "brand-orange" => $light_brand_orange,
    "brand-orange-light" => $light_brand_orange_light,
    "brand-orange-dark" => $light_brand_orange_dark,
    "bg-main" => $light_bg_main,
    "bg-secondary" => $light_bg_secondary,
    "bg-tertiary" => $light_bg_tertiary,
    "card-white" => $light_card_white,
    "border-gray" => $light_border_gray,
    "border-light" => $light_border_light,
    "text-primary" => $light_text_primary,
    "text-secondary" => $light_text_secondary,
    "text-tertiary" => $light_text_tertiary,
    "text-muted" => $light_text_muted
);

// Dark theme colors array (for admin panel)
$dark_theme_colors = array(
    "brand-teal" => $dark_brand_teal,
    "brand-gold" => $dark_brand_gold,
    "brand-orange" => $dark_brand_orange,
    "brand-orange-light" => $dark_brand_orange_light,
    "brand-orange-dark" => $dark_brand_orange_dark,
    "bg-main" => $dark_bg_main,
    "bg-secondary" => $dark_bg_secondary,
    "bg-tertiary" => $dark_bg_tertiary,
    "card-white" => $dark_card_white,
    "border-gray" => $dark_border_gray,
    "border-light" => $dark_border_light,
    "text-primary" => $dark_text_primary,
    "text-secondary" => $dark_text_secondary,
    "text-tertiary" => $dark_text_tertiary,
    "text-muted" => $dark_text_muted
);

// Function to switch theme mode
function setThemeMode($mode) {
    if ($mode === 'light' || $mode === 'dark') {
        $_SESSION['theme_mode'] = $mode;
        return true;
    }
    return false;
}

// Function to get current theme mode
function getThemeMode() {
    return $_SESSION['theme_mode'] ?? 'light';
}
?>
