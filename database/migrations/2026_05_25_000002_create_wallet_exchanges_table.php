<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_exchanges', function (Blueprint $table) {
            $table->id();
            $table->string('trx', 40)->unique();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('from_wallet_id');
            $table->unsignedBigInteger('to_wallet_id');
            $table->string('from_currency', 10);
            $table->string('to_currency', 10);
            $table->decimal('from_amount', 28, 8);
            $table->decimal('to_amount', 28, 8);
            $table->decimal('exchange_rate', 28, 8);  // 1 from_currency = X to_currency
            $table->decimal('fee', 28, 8)->default(0);
            // 1=completed, 2=pending, 3=failed
            $table->tinyInteger('status')->default(1);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('from_wallet_id')->references('id')->on('wallets')->onDelete('restrict');
            $table->foreign('to_wallet_id')->references('id')->on('wallets')->onDelete('restrict');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_exchanges');
    }
};
