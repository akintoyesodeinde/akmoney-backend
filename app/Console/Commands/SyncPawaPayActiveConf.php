<?php

namespace App\Console\Commands;

use App\Models\GatewayCurrency;
use App\Models\Gateway;
use App\Models\PayoutApiCountry;
use App\Models\PayoutApiSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncPawaPayActiveConf extends Command
{
    protected $signature   = 'pawapay:sync-active-conf {--dry-run : Print changes without saving}';
    protected $description = 'Sync PawaPay active-conf API → gateway currencies and payout country methods';

    // ISO-3 (PawaPay) → ISO-2 (our DB)
    private array $iso3ToIso2 = [
        'BEN' => 'BJ', 'BFA' => 'BF', 'CIV' => 'CI', 'CMR' => 'CM',
        'COD' => 'CD', 'COG' => 'CG', 'GHA' => 'GH', 'KEN' => 'KE',
        'MOZ' => 'MZ', 'MWI' => 'MW', 'RWA' => 'RW', 'SEN' => 'SN',
        'SLE' => 'SL', 'TZA' => 'TZ', 'UGA' => 'UG', 'ZMB' => 'ZM',
    ];

    private array $currencyMeta = [
        'GHS' => ['₵',    1,    5000],
        'KES' => ['KSh',  10,   150000],
        'MWK' => ['MK',   100,  150000],
        'MZN' => ['MT',   10,   50000],
        'RWF' => ['Fr',   100,  1000000],
        'SLE' => ['Le',   1,    10000],
        'TZS' => ['TSh',  500,  3000000],
        'UGX' => ['USh',  500,  5000000],
        'XAF' => ['FCFA', 100,  2000000],
        'XOF' => ['FCFA', 100,  2000000],
        'ZMW' => ['ZK',   1,    15000],
        'CDF' => ['FC',   100,  5000000],
        'USD' => ['$',    1,    10000],
    ];

    public function handle(): int
    {
        $settings = PayoutApiSetting::instance();
        $baseUrl  = $settings->isLive()
            ? 'https://api.pawapay.io'
            : 'https://api.sandbox.pawapay.io';

        $this->info('Fetching active-conf from ' . $baseUrl . '…');

        try {
            $response = Http::withToken($settings->secret_key)
                ->timeout(30)
                ->get("{$baseUrl}/v2/active-conf");

            if (!$response->successful()) {
                $this->error('API returned HTTP ' . $response->status());
                Log::error('SyncPawaPayActiveConf: API error', ['status' => $response->status(), 'body' => $response->body()]);
                return 1;
            }
        } catch (\Throwable $e) {
            $this->error('API request failed: ' . $e->getMessage());
            Log::error('SyncPawaPayActiveConf: exception', ['error' => $e->getMessage()]);
            return 1;
        }

        $data      = $response->json();
        $countries = $data['countries'] ?? [];
        $dry       = $this->option('dry-run');

        $activeCorrespondents = [];   // [iso3 => [correspondent => currency]]

        foreach ($countries as $countryData) {
            $iso3 = $countryData['country'];
            foreach ($countryData['providers'] ?? [] as $provider) {
                $correspondent = $provider['provider'];
                foreach ($provider['currencies'] ?? [] as $curData) {
                    $ops        = $curData['operationTypes'] ?? [];
                    $depStatus  = $ops['DEPOSIT']['status']  ?? null;
                    $payStatus  = $ops['PAYOUT']['status']   ?? null;
                    $currency   = $curData['currency'];

                    if ($depStatus === 'OPERATIONAL' || $payStatus === 'OPERATIONAL') {
                        $activeCorrespondents[$iso3][$correspondent] = [
                            'currency'    => $currency,
                            'dep_active'  => $depStatus === 'OPERATIONAL',
                            'pay_active'  => $payStatus === 'OPERATIONAL',
                            'display'     => $provider['displayName'] ?? $correspondent,
                            'min_deposit' => $ops['DEPOSIT']['minAmount'] ?? null,
                            'max_deposit' => $ops['DEPOSIT']['maxAmount'] ?? null,
                        ];
                    }
                }
            }
        }

        $this->info('Active countries: ' . implode(', ', array_keys($activeCorrespondents)));

        // ── 1. Sync payout_api_country_methods ────────────────────────────────
        $this->info('');
        $this->info('Syncing payout country methods…');

        $methodChanges = 0;

        foreach ($activeCorrespondents as $iso3 => $correspondents) {
            $iso2 = $this->iso3ToIso2[$iso3] ?? null;
            if (!$iso2) continue;

            // Find the country row — try ISO-2, then ISO-3 (our DB has mixed formats)
            $payoutCountry = PayoutApiCountry::where('country_code', $iso2)
                ->orWhere('country_code', $iso3)
                ->where('payout_provider', 'pawapay')
                ->first();

            if (!$payoutCountry) {
                $this->line("  <comment>Skipped {$iso3} — no PawaPay payout_api_countries row</comment>");
                continue;
            }

            $existingMethods = $payoutCountry->methods->keyBy('method_code');

            foreach ($correspondents as $correspondent => $info) {
                $displayName = $this->humanName($correspondent, $info['display']);

                if ($existingMethods->has($correspondent)) {
                    $this->line("  <info>Exists:</info> {$payoutCountry->country_name} / {$correspondent}");
                    continue;
                }

                $this->line("  <info>Add:</info> {$payoutCountry->country_name} / {$correspondent} ({$displayName})");
                $methodChanges++;

                if (!$dry) {
                    $payoutCountry->methods()->create([
                        'method_code'      => $correspondent,
                        'method_name'      => $displayName,
                        'is_active'        => false,
                        'pawapay_provider' => $correspondent,
                    ]);
                }
            }

            // Remove methods that are no longer active in the API
            foreach ($existingMethods as $code => $method) {
                if (!isset($correspondents[$code])) {
                    $this->line("  <comment>Remove:</comment> {$payoutCountry->country_name} / {$code} (no longer active)");
                    $methodChanges++;
                    if (!$dry) $method->delete();
                }
            }
        }

        // ── 2. Sync gateway currencies (deposit side) ─────────────────────────
        $this->info('');
        $this->info('Syncing deposit gateway currencies…');

        $gatewayChanges = 0;
        $gateway        = Gateway::where('alias', 'PawaPay')->first();

        if ($gateway) {
            $existingCurrencies = GatewayCurrency::where('method_code', $gateway->code)
                ->get()
                ->keyBy(fn($gc) => json_decode($gc->gateway_parameter)->correspondent ?? '');

            // Collect all active deposit correspondents
            $allActiveDeposit = [];
            foreach ($activeCorrespondents as $iso3 => $correspondents) {
                foreach ($correspondents as $correspondent => $info) {
                    if ($info['dep_active']) {
                        $allActiveDeposit[$correspondent] = array_merge($info, ['iso3' => $iso3]);
                    }
                }
            }

            // Add missing
            foreach ($allActiveDeposit as $correspondent => $info) {
                if ($existingCurrencies->has($correspondent)) continue;

                $currency = $info['currency'];
                $meta     = $this->currencyMeta[$currency] ?? ['?', 1, 100000];
                $name     = 'PawaPay — ' . str_replace('_', ' ', $correspondent);

                $this->line("  <info>Add gateway currency:</info> {$correspondent} ({$currency})");
                $gatewayChanges++;

                if (!$dry) {
                    DB::table('gateway_currencies')->insert([
                        'name'              => $name,
                        'currency'          => $currency,
                        'symbol'            => $meta[0],
                        'method_code'       => $gateway->code,
                        'gateway_alias'     => 'PawaPay',
                        'min_amount'        => $info['min_deposit'] ?? $meta[1],
                        'max_amount'        => $info['max_deposit'] ?? $meta[2],
                        'percent_charge'    => 1.50,
                        'fixed_charge'      => 0.00,
                        'rate'              => 1.00,
                        'gateway_parameter' => json_encode([
                            'correspondent' => $correspondent,
                            'country'       => $info['iso3'],
                        ]),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Report currencies no longer active
            foreach ($existingCurrencies as $correspondent => $gc) {
                if ($correspondent && !isset($allActiveDeposit[$correspondent])) {
                    $this->line("  <comment>No longer active in API:</comment> {$correspondent} — consider removing manually");
                }
            }

            // Update supported_currencies to match active currencies
            $activeCurrencies = [];
            foreach ($allActiveDeposit as $info) {
                $currency = $info['currency'];
                $meta     = $this->currencyMeta[$currency] ?? null;
                if ($meta) $activeCurrencies[$currency] = $meta[0];
            }

            if (!$dry && !empty($activeCurrencies)) {
                DB::table('gateways')->where('alias', 'PawaPay')
                    ->update(['supported_currencies' => json_encode($activeCurrencies)]);
            }
        }

        // ── Summary ───────────────────────────────────────────────────────────
        $this->info('');
        $this->info('Done.' . ($dry ? ' (dry-run — nothing saved)' : ''));
        $this->info("Method changes: {$methodChanges} | Gateway currency changes: {$gatewayChanges}");

        Log::info('SyncPawaPayActiveConf completed', [
            'method_changes'  => $methodChanges,
            'gateway_changes' => $gatewayChanges,
            'dry_run'         => $dry,
        ]);

        return 0;
    }

    private function humanName(string $correspondent, string $apiDisplayName): string
    {
        $prefixes = [
            'MTN_MOMO'      => 'MTN MoMo',
            'AIRTEL_OAPI'   => 'Airtel Money',
            'AIRTELTIGO'    => 'AirtelTigo',    // must be before AIRTEL
            'AIRTEL'        => 'Airtel Money',
            'VODACOM_MPESA' => 'M-Pesa',        // must be before VODACOM
            'VODACOM'       => 'Vodacom Money',
            'MPESA'         => 'M-Pesa',
            'VODAFONE'      => 'Vodafone Cash',
            'ORANGE'        => 'Orange Money',
            'FREE'          => 'Free Money',
            'MOOV'          => 'Moov Money',
            'HALOTEL'       => 'Halotel Halopesa',
            'TIGO'          => 'Tigo Pesa',
            'ZAMTEL'        => 'Zamtel Kwacha',
            'TNM'           => 'TNM Mpamba',
        ];

        foreach ($prefixes as $prefix => $label) {
            if (str_starts_with($correspondent, $prefix)) {
                return $label;
            }
        }

        return $apiDisplayName ?: str_replace('_', ' ', $correspondent);
    }
}
