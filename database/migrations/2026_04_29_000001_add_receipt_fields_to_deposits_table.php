<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->tinyInteger('receipt_required')->default(0)->after('admin_feedback');
            $table->string('receipt_path')->nullable()->after('receipt_required');
            $table->timestamp('receipt_verified_at')->nullable()->after('receipt_path');
        });
    }

    public function down(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->dropColumn(['receipt_required', 'receipt_path', 'receipt_verified_at']);
        });
    }
};
