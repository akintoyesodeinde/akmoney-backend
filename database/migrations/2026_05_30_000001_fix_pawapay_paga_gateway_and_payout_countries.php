<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. PawaPay: add supported currencies + per-currency parameters ────────
        // gateway_parameters defines what fields appear per-currency in the admin UI
        $pawapayParams = json_encode([
            'correspondent' => [
                'title'  => 'PawaPay Correspondent (e.g. MTN_MOMO_GHA)',
                'global' => false,
                'value'  => '',
            ],
            'country' => [
                'title'  => 'Country ISO-3 Code (e.g. GHA)',
                'global' => false,
                'value'  => '',
            ],
        ]);

        // All currencies PawaPay currently supports for collection + disbursement
        $pawapayCurrencies = json_encode([
            'GHS' => '₵',
            'NGN' => '₦',
            'KES' => 'KSh',
            'UGX' => 'USh',
            'TZS' => 'TSh',
            'ZMW' => 'ZK',
            'RWF' => 'Fr',
            'MWK' => 'MK',
            'XAF' => 'FCFA',
            'XOF' => 'FCFA',
            'ZAR' => 'R',
            'ETB' => 'Br',
            'MZN' => 'MT',
            'GNF' => 'Fr',
            'SLL' => 'Le',
            'MGA' => 'Ar',
            'AOA' => 'Kz',
        ]);

        DB::table('gateways')->where('alias', 'PawaPay')->update([
            'gateway_parameters'   => $pawapayParams,
            'supported_currencies' => $pawapayCurrencies,
        ]);

        // ── 2. Paga: fix supported_currencies format (array → keyed object) ──────
        DB::table('gateways')->where('alias', 'Paga')->update([
            'supported_currencies' => json_encode(['NGN' => '₦']),
        ]);

        // ── 3. Payout countries: assign pawapay / paga providers ─────────────────
        // Countries where PawaPay handles payouts
        $pawapayCountries = [
            'Ghana', 'Nigeria', 'Kenya', 'Uganda', 'Tanzania', 'Zambia',
            'Rwanda', 'Malawi', 'Cameroon', "Cote d'Ivoire", 'Senegal',
            'Burkina Faso', 'Togo', 'Mali', 'Benin', 'Niger', 'Gabon',
            'Chad', 'Central African Republic', 'Equatorial Guinea',
            'Guinea', 'Mozambique', 'Madagascar', 'Angola',
        ];

        DB::table('payout_api_countries')
            ->whereIn('country_name', $pawapayCountries)
            ->update(['payout_provider' => 'pawapay']);

        // Paga handles payouts for Nigeria only
        DB::table('payout_api_countries')
            ->where('country_name', 'Nigeria')
            ->update(['payout_provider' => 'paga']);
    }

    public function down(): void
    {
        DB::table('gateways')->where('alias', 'PawaPay')->update([
            'gateway_parameters'   => json_encode([]),
            'supported_currencies' => json_encode([]),
        ]);

        DB::table('gateways')->where('alias', 'Paga')->update([
            'supported_currencies' => json_encode(['NGN']),
        ]);

        $pawapayCountries = [
            'Ghana', 'Nigeria', 'Kenya', 'Uganda', 'Tanzania', 'Zambia',
            'Rwanda', 'Malawi', "Cote d'Ivoire", 'Senegal',
            'Burkina Faso', 'Togo', 'Mali', 'Benin', 'Niger', 'Gabon',
            'Chad', 'Central African Republic', 'Equatorial Guinea',
            'Guinea', 'Mozambique', 'Madagascar', 'Angola',
        ];

        DB::table('payout_api_countries')
            ->whereIn('country_name', $pawapayCountries)
            ->update(['payout_provider' => 'manual']);

        DB::table('payout_api_countries')
            ->where('country_name', 'Nigeria')
            ->update(['payout_provider' => 'manual']);
    }
};
