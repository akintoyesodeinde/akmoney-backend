<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paga_api_settings', function (Blueprint $table) {
            $table->id();
            $table->string('principal')->nullable();
            $table->string('credentials')->nullable();
            $table->string('api_key')->nullable();
            $table->string('environment', 10)->default('sandbox'); // sandbox | live
            $table->timestamps();
        });

        // Seed one row so instance() always finds a record
        DB::table('paga_api_settings')->insert([
            'principal'   => null,
            'credentials' => null,
            'api_key'     => null,
            'environment' => 'sandbox',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('paga_api_settings');
    }
};
