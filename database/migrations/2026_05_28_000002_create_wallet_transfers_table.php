<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('trx', 40)->unique();
            $table->unsignedBigInteger('sender_id');
            $table->unsignedBigInteger('receiver_id');
            $table->unsignedBigInteger('sender_wallet_id');
            $table->unsignedBigInteger('receiver_wallet_id');
            $table->string('currency_code', 10);
            $table->decimal('amount', 18, 8);            // amount sender intends to send
            $table->decimal('charge', 18, 8)->default(0);// fee deducted from sender
            $table->decimal('sender_deducted', 18, 8);   // amount + charge
            $table->decimal('receiver_credited', 18, 8); // amount credited to receiver
            $table->tinyInteger('status')->default(1);   // 1=completed
            $table->timestamps();

            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('sender_wallet_id')->references('id')->on('wallets')->onDelete('cascade');
            $table->foreign('receiver_wallet_id')->references('id')->on('wallets')->onDelete('cascade');

            $table->index(['sender_id', 'created_at']);
            $table->index(['receiver_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transfers');
    }
};
