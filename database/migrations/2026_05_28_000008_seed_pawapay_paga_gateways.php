<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        // ── PawaPay Collection (code 512) ──────────────────────────────────────
        if (!DB::table('gateways')->where('alias', 'PawaPay')->exists()) {
            DB::table('gateways')->insert([
                'form_id'             => 0,
                'code'                => 512,
                'name'                => 'PawaPay Mobile Money',
                'alias'               => 'PawaPay',
                'status'              => 1,
                'gateway_parameters'  => json_encode([]),
                'supported_currencies'=> json_encode([]),
                'crypto'              => 0,
                'description'         => null,
                'extra'               => null,
                'created_at'          => $now,
                'updated_at'          => $now,
            ]);
        }

        // PawaPay Ghana (GHS) — MTN MoMo
        if (!DB::table('gateway_currencies')->where('method_code', 512)->where('currency', 'GHS')->exists()) {
            DB::table('gateway_currencies')->insert([
                'name'              => 'PawaPay Mobile Money',
                'currency'          => 'GHS',
                'symbol'            => '₵',
                'method_code'       => 512,
                'gateway_alias'     => 'PawaPay',
                'min_amount'        => 1.00,
                'max_amount'        => 5000.00,
                'percent_charge'    => 1.50,
                'fixed_charge'      => 0.00,
                'rate'              => 1.00,
                // correspondent + country stored in gateway_parameter (set in admin panel)
                'gateway_parameter' => json_encode([
                    'correspondent' => 'MTN_MOMO_GHA',
                    'country'       => 'GHA',
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // PawaPay Nigeria (NGN) — MTN
        if (!DB::table('gateway_currencies')->where('method_code', 512)->where('currency', 'NGN')->exists()) {
            DB::table('gateway_currencies')->insert([
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
                'gateway_parameter' => json_encode([
                    'correspondent' => 'MTN_MOMO_NGA',
                    'country'       => 'NGA',
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // PawaPay Kenya (KES) — M-Pesa
        if (!DB::table('gateway_currencies')->where('method_code', 512)->where('currency', 'KES')->exists()) {
            DB::table('gateway_currencies')->insert([
                'name'              => 'PawaPay Mobile Money',
                'currency'          => 'KES',
                'symbol'            => 'KSh',
                'method_code'       => 512,
                'gateway_alias'     => 'PawaPay',
                'min_amount'        => 10.00,
                'max_amount'        => 150000.00,
                'percent_charge'    => 1.50,
                'fixed_charge'      => 0.00,
                'rate'              => 1.00,
                'gateway_parameter' => json_encode([
                    'correspondent' => 'MPESA_KEN',
                    'country'       => 'KEN',
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // ── Paga Collection (code 513) — Nigeria only ──────────────────────────
        if (!DB::table('gateways')->where('alias', 'Paga')->exists()) {
            DB::table('gateways')->insert([
                'form_id'             => 0,
                'code'                => 513,
                'name'                => 'Paga Mobile Payment',
                'alias'               => 'Paga',
                'status'              => 1,
                'gateway_parameters'  => json_encode([]),
                'supported_currencies'=> json_encode(['NGN']),
                'crypto'              => 0,
                'description'         => null,
                'extra'               => null,
                'created_at'          => $now,
                'updated_at'          => $now,
            ]);
        }

        if (!DB::table('gateway_currencies')->where('method_code', 513)->where('currency', 'NGN')->exists()) {
            DB::table('gateway_currencies')->insert([
                'name'              => 'Paga Mobile Payment',
                'currency'          => 'NGN',
                'symbol'            => '₦',
                'method_code'       => 513,
                'gateway_alias'     => 'Paga',
                'min_amount'        => 100.00,
                'max_amount'        => 1000000.00,
                'percent_charge'    => 1.50,
                'fixed_charge'      => 0.00,
                'rate'              => 1.00,
                'gateway_parameter' => json_encode([]),
                'created_at'        => $now,
                'updated_at'        => $now,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('gateway_currencies')->whereIn('method_code', [512, 513])->delete();
        DB::table('gateways')->whereIn('alias', ['PawaPay', 'Paga'])->delete();
    }
};
