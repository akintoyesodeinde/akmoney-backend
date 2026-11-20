<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $existing = DB::table('payout_api_countries')
            ->pluck('country_code')
            ->map(fn($c) => strtoupper($c))
            ->flip()
            ->toArray();

        $appCountries = DB::table('countries')
            ->get(['country_code', 'name', 'currency']);

        $now = now();

        foreach ($appCountries as $country) {
            $code = strtoupper($country->country_code);

            if (isset($existing[$code])) {
                continue; // already seeded
            }

            $countryId = DB::table('payout_api_countries')->insertGetId([
                'country_code' => $code,
                'country_name' => $country->name,
                'currency_code' => $country->currency,
                'is_active'    => false,
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);

            foreach ($this->methodsFor($code) as $methodCode => $methodName) {
                DB::table('payout_api_country_methods')->insert([
                    'payout_api_country_id' => $countryId,
                    'method_code'           => $methodCode,
                    'method_name'           => $methodName,
                    'is_active'             => false,
                    'pawapay_provider'      => null,
                    'created_at'            => $now,
                    'updated_at'            => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        // Only remove countries added by this migration (not the original 14)
        $original = ['NG','GH','KE','UG','TZ','ZA','RW','ZM','MW','CI','SN','ET','CM','BJ'];

        $toDelete = DB::table('payout_api_countries')
            ->whereNotIn('country_code', $original)
            ->pluck('id');

        DB::table('payout_api_country_methods')
            ->whereIn('payout_api_country_id', $toDelete)
            ->delete();

        DB::table('payout_api_countries')
            ->whereIn('id', $toDelete)
            ->delete();
    }

    private function methodsFor(string $code): array
    {
        // Countries with M-Pesa as primary method
        $mpesa = ['KE', 'TZ', 'CD', 'MZ'];
        if (in_array($code, $mpesa)) {
            return ['mpesa' => 'M-Pesa', 'bank_transfer' => 'Bank Transfer'];
        }

        // Countries typically served by mobile money only
        $mobileOnly = ['BJ','CM','CI','BF','ML','NE','TG','GN','GW','SN','GQ','CF','TD','GA','BI','SO'];
        if (in_array($code, $mobileOnly)) {
            return ['mobile_money' => 'Mobile Money'];
        }

        // Default: both mobile money and bank transfer
        return [
            'mobile_money' => 'Mobile Money',
            'bank_transfer' => 'Bank Transfer',
        ];
    }
};
