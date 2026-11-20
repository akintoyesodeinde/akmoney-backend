<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Skip if already seeded
        if (DB::table('gateways')->where('alias', 'Apaylo')->exists()) {
            return;
        }

        $gatewayId = DB::table('gateways')->insertGetId([
            'form_id'             => 0,
            'code'                => 511,
            'name'                => 'Apaylo (Interac e-Transfer)',
            'alias'               => 'Apaylo',
            'status'              => 1,
            'crypto'              => 0,
            'gateway_parameters'  => json_encode([
                'api_key'     => ['title' => 'API Key',                                    'global' => false, 'value' => ''],
                'secret_key'  => ['title' => 'Secret Key',                                 'global' => false, 'value' => ''],
                'environment' => ['title' => 'Environment (sandbox or production)',         'global' => false, 'value' => 'sandbox'],
            ]),
            'supported_currencies' => json_encode(['CAD' => 'CAD']),
            'description'          => '<p>Pay securely via Interac e-Transfer. You will receive an email with instructions to complete your payment from any Canadian bank account.</p>',
            'created_at'           => now(),
            'updated_at'           => now(),
        ]);

        // Default CAD currency entry (admin must fill in API credentials)
        DB::table('gateway_currencies')->insert([
            'method_code'       => 511,
            'gateway_alias'     => 'Apaylo',
            'name'              => 'Apaylo Interac e-Transfer',
            'currency'          => 'CAD',
            'symbol'            => '$',
            'min_amount'        => '10.00',
            'max_amount'        => '9999.99',
            'percent_charge'    => '0.00',
            'fixed_charge'      => '1.00',
            'rate'              => '1.00',
            'gateway_parameter' => json_encode([
                'api_key'     => '',
                'secret_key'  => '',
                'environment' => 'sandbox',
            ]),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('gateway_currencies')->where('method_code', 511)->delete();
        DB::table('gateways')->where('alias', 'Apaylo')->delete();
    }
};
