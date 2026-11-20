<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'two_fa_method')) {
                $table->string('two_fa_method', 10)->nullable()->after('tsc');
            }
            if (!Schema::hasColumn('users', 'force_tfa')) {
                $table->tinyInteger('force_tfa')->default(0)->after('two_fa_method');
            }
        });

        Schema::create('backup_codes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // MyISAM tables don't support FK constraints
            $table->string('code', 64); // SHA-256 hash of the plain-text code
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'used_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backup_codes');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['two_fa_method', 'force_tfa']);
        });
    }
};
