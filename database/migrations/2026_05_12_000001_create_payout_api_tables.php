<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payout_api_settings', function (Blueprint $table) {
            $table->id();
            $table->text('secret_key')->nullable();
            $table->text('public_key')->nullable();
            $table->text('encryption_key')->nullable();
            $table->string('environment', 20)->default('sandbox');
            $table->text('webhook_secret')->nullable();
            $table->timestamps();
        });

        Schema::create('payout_api_countries', function (Blueprint $table) {
            $table->id();
            $table->string('country_code', 3);
            $table->string('country_name', 100);
            $table->string('currency_code', 10);
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::create('payout_api_country_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payout_api_country_id')->constrained()->cascadeOnDelete();
            $table->string('method_code', 50);
            $table->string('method_name', 100);
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::create('payout_api_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('send_money_id');
            $table->string('event', 50);
            $table->json('payload')->nullable();
            $table->json('response')->nullable();
            $table->timestamps();

            $table->index('send_money_id');
        });

        $this->seedCountries();
    }

    private function seedCountries(): void
    {
        $countries = [
            ['code' => 'NG', 'name' => 'Nigeria',       'currency' => 'NGN', 'methods' => ['bank_transfer' => 'Bank Transfer', 'mobile_money' => 'Mobile Money']],
            ['code' => 'GH', 'name' => 'Ghana',         'currency' => 'GHS', 'methods' => ['bank_transfer' => 'Bank Transfer', 'mobile_money' => 'Mobile Money']],
            ['code' => 'KE', 'name' => 'Kenya',         'currency' => 'KES', 'methods' => ['bank_transfer' => 'Bank Transfer', 'mpesa' => 'M-Pesa']],
            ['code' => 'UG', 'name' => 'Uganda',        'currency' => 'UGX', 'methods' => ['bank_transfer' => 'Bank Transfer', 'mobile_money' => 'Mobile Money']],
            ['code' => 'TZ', 'name' => 'Tanzania',      'currency' => 'TZS', 'methods' => ['bank_transfer' => 'Bank Transfer', 'mobile_money' => 'Mobile Money']],
            ['code' => 'ZA', 'name' => 'South Africa',  'currency' => 'ZAR', 'methods' => ['bank_transfer' => 'Bank Transfer']],
            ['code' => 'RW', 'name' => 'Rwanda',        'currency' => 'RWF', 'methods' => ['bank_transfer' => 'Bank Transfer', 'mobile_money' => 'Mobile Money']],
            ['code' => 'ZM', 'name' => 'Zambia',        'currency' => 'ZMW', 'methods' => ['bank_transfer' => 'Bank Transfer', 'mobile_money' => 'Mobile Money']],
            ['code' => 'MW', 'name' => 'Malawi',        'currency' => 'MWK', 'methods' => ['bank_transfer' => 'Bank Transfer', 'mobile_money' => 'Mobile Money']],
            ['code' => 'CI', 'name' => "Cote d'Ivoire", 'currency' => 'XOF', 'methods' => ['mobile_money' => 'Mobile Money']],
            ['code' => 'SN', 'name' => 'Senegal',       'currency' => 'XOF', 'methods' => ['bank_transfer' => 'Bank Transfer', 'mobile_money' => 'Mobile Money']],
            ['code' => 'ET', 'name' => 'Ethiopia',      'currency' => 'ETB', 'methods' => ['bank_transfer' => 'Bank Transfer']],
            ['code' => 'CM', 'name' => 'Cameroon',      'currency' => 'XAF', 'methods' => ['mobile_money' => 'Mobile Money']],
            ['code' => 'BJ', 'name' => 'Benin',         'currency' => 'XOF', 'methods' => ['mobile_money' => 'Mobile Money']],
        ];

        $now = now();

        foreach ($countries as $c) {
            $countryId = DB::table('payout_api_countries')->insertGetId([
                'country_code' => $c['code'],
                'country_name' => $c['name'],
                'currency_code' => $c['currency'],
                'is_active'    => false,
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);

            foreach ($c['methods'] as $code => $name) {
                DB::table('payout_api_country_methods')->insert([
                    'payout_api_country_id' => $countryId,
                    'method_code'           => $code,
                    'method_name'           => $name,
                    'is_active'             => false,
                    'created_at'            => $now,
                    'updated_at'            => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payout_api_logs');
        Schema::dropIfExists('payout_api_country_methods');
        Schema::dropIfExists('payout_api_countries');
        Schema::dropIfExists('payout_api_settings');
    }
};
