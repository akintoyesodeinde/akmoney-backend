<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AML Provider
    |--------------------------------------------------------------------------
    | Set to 'internal' to use the built-in rule-based engine.
    | External providers (e.g. 'refinitiv', 'complyadvantage') must implement
    | App\Services\Aml\Contracts\AmlProviderInterface and be registered in
    | AmlServiceProvider.
    */
    'provider' => env('AML_PROVIDER', 'internal'),

    /*
    |--------------------------------------------------------------------------
    | Currency & Amount Thresholds (CAD)
    |--------------------------------------------------------------------------
    */
    'currency'   => env('AML_CURRENCY', 'CAD'),
    'thresholds' => [
        'high_amount'   => env('AML_HIGH_AMOUNT', 10000),  // FINTRAC large-cash threshold
        'medium_amount' => env('AML_MEDIUM_AMOUNT', 3000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Velocity Checks
    |--------------------------------------------------------------------------
    */
    'velocity_window_hours' => 24,
    'velocity_limit'        => 5,

    /*
    |--------------------------------------------------------------------------
    | High-Risk Countries (ISO 3166-1 alpha-2)
    | Based on FATF grey/black lists and Canadian sanctions
    |--------------------------------------------------------------------------
    */
    'high_risk_countries' => [
        'IR', // Iran
        'KP', // North Korea
        'SY', // Syria
        'CU', // Cuba
        'SD', // Sudan
        'MM', // Myanmar
        'RU', // Russia (sanctioned)
        'BY', // Belarus
        'YE', // Yemen
        'LY', // Libya
        'SO', // Somalia
        'AF', // Afghanistan
        'HT', // Haiti
        'VE', // Venezuela
        'ZW', // Zimbabwe
        'CD', // Congo DR
        'ML', // Mali
        'BF', // Burkina Faso
        'NI', // Nicaragua
        'PK', // Pakistan (FATF grey)
    ],

    /*
    |--------------------------------------------------------------------------
    | Risk Score Thresholds → Risk Levels
    |--------------------------------------------------------------------------
    */
    'levels' => [
        'low'      => [0, 29],
        'medium'   => [30, 59],
        'high'     => [60, 79],
        'critical' => [80, 100],
    ],

    /*
    |--------------------------------------------------------------------------
    | New Account Age (days) — extra scrutiny window
    |--------------------------------------------------------------------------
    */
    'new_account_days' => 30,
];
