<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            // NULL / empty array = all currencies allowed
            $table->json('wallet_allowed_currencies')->nullable()->after('wallet_currency_mode');
        });
    }

    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->dropColumn('wallet_allowed_currencies');
        });
    }
};
