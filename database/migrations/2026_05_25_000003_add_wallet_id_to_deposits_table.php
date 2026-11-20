<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            // null = send-money payment; set = wallet top-up
            $table->unsignedBigInteger('wallet_id')->nullable()->after('send_money_id');
            $table->foreign('wallet_id')->references('id')->on('wallets')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->dropForeign(['wallet_id']);
            $table->dropColumn('wallet_id');
        });
    }
};
