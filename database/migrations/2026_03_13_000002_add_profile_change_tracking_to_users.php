<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('pending_email')->nullable()->after('email');
            $table->string('pending_mobile')->nullable()->after('mobile');
            $table->string('pending_mobile_code')->nullable()->after('pending_mobile');
            $table->string('profile_change_code', 6)->nullable()->after('ver_code');
            $table->timestamp('profile_change_code_sent_at')->nullable()->after('profile_change_code');
            $table->timestamp('email_change_at')->nullable()->after('profile_change_code_sent_at');
            $table->timestamp('mobile_change_at')->nullable()->after('email_change_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'pending_email',
                'pending_mobile',
                'pending_mobile_code',
                'profile_change_code',
                'profile_change_code_sent_at',
                'email_change_at',
                'mobile_change_at',
            ]);
        });
    }
};
