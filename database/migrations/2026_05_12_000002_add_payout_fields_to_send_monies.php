<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('send_money', function (Blueprint $table) {
            $table->string('payout_channel', 10)->nullable()->after('payout_by');
            $table->unsignedBigInteger('payout_api_method_id')->nullable()->after('payout_channel');
            $table->string('api_payout_id')->nullable()->after('payout_api_method_id');
            $table->string('api_status', 50)->nullable()->after('api_payout_id');
            $table->timestamp('api_initiated_at')->nullable()->after('api_status');
        });
    }

    public function down(): void
    {
        Schema::table('send_money', function (Blueprint $table) {
            $table->dropColumn(['payout_channel', 'payout_api_method_id', 'api_payout_id', 'api_status', 'api_initiated_at']);
        });
    }
};
