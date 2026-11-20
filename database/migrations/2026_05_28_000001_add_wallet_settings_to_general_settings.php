<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->tinyInteger('wallet_module')->default(1)->after('agent_module');
            $table->tinyInteger('wallet_transfer')->default(1)->after('wallet_module');
            $table->json('wallet_transfer_methods')->nullable()->after('wallet_transfer');
            $table->decimal('wallet_transfer_fixed_charge', 10, 2)->default(0)->after('wallet_transfer_methods');
            $table->decimal('wallet_transfer_percent_charge', 5, 2)->default(0)->after('wallet_transfer_fixed_charge');
            $table->decimal('wallet_exchange_fixed_charge', 10, 2)->default(0)->after('wallet_transfer_percent_charge');
            $table->decimal('wallet_exchange_percent_charge', 5, 2)->default(0)->after('wallet_exchange_fixed_charge');
        });

        // Seed sensible defaults
        DB::table('general_settings')->update([
            'wallet_transfer_methods' => json_encode(['username', 'email', 'mobile']),
        ]);
    }

    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->dropColumn([
                'wallet_module',
                'wallet_transfer',
                'wallet_transfer_methods',
                'wallet_transfer_fixed_charge',
                'wallet_transfer_percent_charge',
                'wallet_exchange_fixed_charge',
                'wallet_exchange_percent_charge',
            ]);
        });
    }
};
