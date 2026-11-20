<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admin_notifications', function (Blueprint $table) {
            // Which module this notification belongs to (null = visible to all / super admin only)
            $table->string('module')->nullable()->after('click_url')->index();
            // Who triggered this action (admin_id, if an admin did it)
            $table->unsignedBigInteger('triggered_by')->nullable()->after('module');
        });
    }

    public function down(): void
    {
        Schema::table('admin_notifications', function (Blueprint $table) {
            $table->dropColumn(['module', 'triggered_by']);
        });
    }
};
