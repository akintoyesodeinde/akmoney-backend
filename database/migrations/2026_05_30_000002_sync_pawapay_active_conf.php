<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Syncs PawaPay gateway currencies and payout countries to match
 * the active-conf response from the sandbox account (fetched 2026-05-30).
 *
 * Active countries: BEN, BFA, CIV, CMR, COD, COG, GHA, KEN, MOZ,
 *                  MWI, RWA, SEN, SLE, TZA, UGA, ZMB
 * NOT active: NGA (Nigeria) — removed.
 */
return new class extends Migration
{
    // Every unique correspondent that is OPERATIONAL for DEPOSIT + PAYOUT
    // Format: [correspondent, country ISO-3, currency, symbol, min, max]
    private array $activeCorrespondents = [
        ['MOOV_BEN',         'BEN', 'XOF', 'FCFA', 100,    2000000],
        ['MTN_MOMO_BEN',     'BEN', 'XOF', 'FCFA', 100,    2000000],
        ['MOOV_BFA',         'BFA', 'XOF', 'FCFA', 100,    2000000],
        ['MTN_MOMO_CIV',     'CIV', 'XOF', 'FCFA', 100,    2000000],
        ['ORANGE_CIV',       'CIV', 'XOF', 'FCFA', 100,    2000000],
        ['MTN_MOMO_CMR',     'CMR', 'XAF', 'FCFA', 100,    2000000],
        ['ORANGE_CMR',       'CMR', 'XAF', 'FCFA', 100,    2000000],
        ['AIRTEL_COD',       'COD', 'CDF', 'FC',   100,    5000000],
        ['ORANGE_COD',       'COD', 'CDF', 'FC',   100,    5000000],
        ['VODACOM_MPESA_COD','COD', 'CDF', 'FC',   100,    5000000],
        ['AIRTEL_COG',       'COG', 'XAF', 'FCFA', 100,    2000000],
        // GHA already seeded (MTN_MOMO_GHA id=12) — adding the other two providers
        ['AIRTELTIGO_GHA',   'GHA', 'GHS', '₵',   1,      5000],
        ['VODAFONE_GHA',     'GHA', 'GHS', '₵',   1,      5000],
        // KEN already seeded (MPESA_KEN id=14) — skip
        ['VODACOM_MOZ',      'MOZ', 'MZN', 'MT',   10,     50000],
        ['AIRTEL_MWI',       'MWI', 'MWK', 'MK',   100,    150000],
        ['TNM_MWI',          'MWI', 'MWK', 'MK',   100,    150000],
        ['AIRTEL_RWA',       'RWA', 'RWF', 'Fr',   100,    1000000],
        ['MTN_MOMO_RWA',     'RWA', 'RWF', 'Fr',   100,    1000000],
        ['FREE_SEN',         'SEN', 'XOF', 'FCFA', 100,    2000000],
        ['ORANGE_SEN',       'SEN', 'XOF', 'FCFA', 100,    2000000],
        ['ORANGE_SLE',       'SLE', 'SLE', 'Le',   1,      10000],
        ['AIRTEL_TZA',       'TZA', 'TZS', 'TSh',  500,    3000000],
        ['HALOTEL_TZA',      'TZA', 'TZS', 'TSh',  500,    3000000],
        ['TIGO_TZA',         'TZA', 'TZS', 'TSh',  500,    3000000],
        ['AIRTEL_OAPI_UGA',  'UGA', 'UGX', 'USh',  500,    5000000],
        ['MTN_MOMO_UGA',     'UGA', 'UGX', 'USh',  500,    5000000],
        ['AIRTEL_OAPI_ZMB',  'ZMB', 'ZMW', 'ZK',   1,      15000],
        ['MTN_MOMO_ZMB',     'ZMB', 'ZMW', 'ZK',   1,      15000],
        ['ZAMTEL_ZMB',       'ZMB', 'ZMW', 'ZK',   1,      15000],
    ];

    // Active currencies for supported_currencies field (what admin can add)
    private array $supportedCurrencies = [
        'GHS' => '₵',
        'KES' => 'KSh',
        'MWK' => 'MK',
        'MZN' => 'MT',
        'RWF' => 'Fr',
        'SLE' => 'Le',
        'TZS' => 'TSh',
        'UGX' => 'USh',
        'XAF' => 'FCFA',
        'XOF' => 'FCFA',
        'ZMW' => 'ZK',
        'CDF' => 'FC',
    ];

    // Payout country map: ISO-3 country code => country_name in DB
    private array $pawapayPayoutCountries = [
        'BEN' => 'Benin',
        'BFA' => 'Burkina Faso',
        'CIV' => "Cote d'Ivoire",
        'CMR' => 'Cameroon',
        'COD' => 'The Democratic Republic of the Congo',
        'COG' => 'Republic of the Congo',
        'GHA' => 'Ghana',
        'KEN' => 'Kenya',
        'MOZ' => 'Mozambique',
        'MWI' => 'Malawi',
        'RWA' => 'Rwanda',
        'SEN' => 'Senegal',
        'SLE' => 'Sierra Leone',
        'TZA' => 'Tanzania',
        'UGA' => 'Uganda',
        'ZMB' => 'Zambia',
    ];

    public function up(): void
    {
        $now = now();

        // 1. Remove Nigeria — not active on this PawaPay account
        DB::table('gateway_currencies')
            ->where('method_code', 512)
            ->where('currency', 'NGN')
            ->delete();

        // 2. Insert missing correspondents (skip if already exists)
        foreach ($this->activeCorrespondents as [$correspondent, $country, $currency, $symbol, $min, $max]) {
            $exists = DB::table('gateway_currencies')
                ->where('method_code', 512)
                ->whereJsonContains('gateway_parameter->correspondent', $correspondent)
                ->exists();

            if ($exists) continue;

            // Human-readable name: "PawaPay — PROVIDER"
            $name = 'PawaPay — ' . str_replace('_', ' ', $correspondent);

            DB::table('gateway_currencies')->insert([
                'name'              => $name,
                'currency'          => $currency,
                'symbol'            => $symbol,
                'method_code'       => 512,
                'gateway_alias'     => 'PawaPay',
                'min_amount'        => $min,
                'max_amount'        => $max,
                'percent_charge'    => 1.50,
                'fixed_charge'      => 0.00,
                'rate'              => 1.00,
                'gateway_parameter' => json_encode([
                    'correspondent' => $correspondent,
                    'country'       => $country,
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 3. Update supported_currencies to only list what's active
        DB::table('gateways')
            ->where('alias', 'PawaPay')
            ->update(['supported_currencies' => json_encode($this->supportedCurrencies)]);

        // 4. Reset payout countries: set all current pawapay ones to manual first
        DB::table('payout_api_countries')
            ->where('payout_provider', 'pawapay')
            ->update(['payout_provider' => 'manual']);

        // 5. Assign active payout countries to pawapay
        foreach ($this->pawapayPayoutCountries as $iso => $name) {
            DB::table('payout_api_countries')
                ->where('country_name', $name)
                ->update(['payout_provider' => 'pawapay']);
        }

        // 6. Nigeria payout reverts to manual (not active)
        DB::table('payout_api_countries')
            ->where('country_name', 'Nigeria')
            ->where('payout_provider', 'paga')
            ->update(['payout_provider' => 'paga']); // paga still handles Nigeria payout if configured separately
    }

    public function down(): void
    {
        // Re-add NGN
        DB::table('gateway_currencies')->insertOrIgnore([
            'name'              => 'PawaPay Mobile Money',
            'currency'          => 'NGN',
            'symbol'            => '₦',
            'method_code'       => 512,
            'gateway_alias'     => 'PawaPay',
            'min_amount'        => 100.00,
            'max_amount'        => 500000.00,
            'percent_charge'    => 1.50,
            'fixed_charge'      => 0.00,
            'rate'              => 1.00,
            'gateway_parameter' => json_encode(['correspondent' => 'MTN_MOMO_NGA', 'country' => 'NGA']),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        // Remove the newly added correspondents
        foreach ($this->activeCorrespondents as [$correspondent]) {
            DB::table('gateway_currencies')
                ->where('method_code', 512)
                ->whereJsonContains('gateway_parameter->correspondent', $correspondent)
                ->delete();
        }
    }
};
