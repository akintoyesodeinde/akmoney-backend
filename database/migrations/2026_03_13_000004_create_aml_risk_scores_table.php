<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aml_risk_scores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->string('transaction_type', 30)->nullable(); // send_money, deposit, withdrawal
            $table->unsignedTinyInteger('risk_score')->default(0); // 0-100
            $table->string('risk_level', 10)->default('low');     // low, medium, high, critical
            $table->json('risk_factors')->nullable();              // array of triggered rule labels
            $table->tinyInteger('is_reviewed')->default(0);
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'risk_level', 'created_at']);
            $table->index(['is_reviewed', 'risk_level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aml_risk_scores');
    }
};
