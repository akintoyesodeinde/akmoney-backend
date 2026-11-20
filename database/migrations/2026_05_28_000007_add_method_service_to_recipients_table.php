<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recipients', function (Blueprint $table) {
            if (!Schema::hasColumn('recipients', 'country_id')) {
                $table->unsignedBigInteger('country_id')->nullable()->after('address');
            }
            if (!Schema::hasColumn('recipients', 'delivery_method_id')) {
                $table->unsignedBigInteger('delivery_method_id')->nullable()->after('country_id');
            }
            if (!Schema::hasColumn('recipients', 'service_id')) {
                $table->unsignedBigInteger('service_id')->nullable()->after('delivery_method_id');
            }
            if (!Schema::hasColumn('recipients', 'service_form_data')) {
                $table->json('service_form_data')->nullable()->after('service_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('recipients', function (Blueprint $table) {
            $table->dropColumn(array_filter([
                Schema::hasColumn('recipients', 'country_id')         ? 'country_id'         : null,
                Schema::hasColumn('recipients', 'delivery_method_id') ? 'delivery_method_id' : null,
                Schema::hasColumn('recipients', 'service_id')         ? 'service_id'         : null,
                Schema::hasColumn('recipients', 'service_form_data')  ? 'service_form_data'  : null,
            ]));
        });
    }
};
