<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Converts legacy MyISAM tables to InnoDB to enable ACID transactions
     * and row-level pessimistic locking (lockForUpdate) on financial tables.
     */
    public function up(): void
    {
        $tables = [
            'wallets',
            'wallet_exchanges',
            'wallet_transfers',
            'aml_risk_scores',
            'sanctions_screening_results',
            'paga_api_settings',
        ];

        foreach ($tables as $table) {
            DB::statement("ALTER TABLE `{$table}` ENGINE=InnoDB");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reversal intentionally leaves tables as InnoDB for financial integrity
    }
};
