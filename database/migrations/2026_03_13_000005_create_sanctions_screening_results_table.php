<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sanctions_screening_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('screening_type', 20); // ofac, pep, adverse_media
            $table->string('status', 10)->default('clear'); // clear, hit, pending
            $table->string('provider', 30)->default('internal');
            $table->json('match_data')->nullable(); // provider response details
            $table->timestamp('screened_at');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'screening_type']);
            $table->index(['status', 'screening_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sanctions_screening_results');
    }
};
