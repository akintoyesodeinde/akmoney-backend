<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payout_api_country_methods', function (Blueprint $table) {
            $table->string('pawapay_provider', 100)->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('payout_api_country_methods', function (Blueprint $table) {
            $table->dropColumn('pawapay_provider');
        });
    }
};
