<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('currency_code', 10);  // e.g. USD, NGN, GBP
            $table->string('currency_name', 60);
            $table->string('currency_symbol', 10)->default('');
            $table->decimal('balance', 28, 8)->default(0);
            $table->boolean('is_default')->default(false);
            // marks this wallet's currency as usable for outbound remittance payments
            $table->boolean('remittance_enabled')->default(false);
            $table->tinyInteger('status')->default(1); // 1=active, 0=suspended
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['user_id', 'currency_code']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
