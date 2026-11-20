<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payout_api_countries', function (Blueprint $table) {
            // 'manual' | 'pawapay' | 'paga'  (default manual — safest)
            $table->string('payout_provider', 20)->default('manual')->after('is_active');
        });

        // Migrate existing data: rows already marked active were using PawaPay
        DB::table('payout_api_countries')
            ->where('is_active', true)
            ->update(['payout_provider' => 'pawapay']);
    }

    public function down(): void
    {
        Schema::table('payout_api_countries', function (Blueprint $table) {
            $table->dropColumn('payout_provider');
        });
    }
};
