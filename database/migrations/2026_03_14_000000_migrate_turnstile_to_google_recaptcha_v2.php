<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('extensions')
            ->where('act', 'cloudflare-turnstile')
            ->update([
                'act'         => 'google-recaptcha2',
                'name'        => 'Google reCAPTCHA v2',
                'description' => 'Get your site key and secret key from the Google reCAPTCHA Admin Console (v2 Invisible or v2 Checkbox).',
            ]);
    }

    public function down(): void
    {
        DB::table('extensions')
            ->where('act', 'google-recaptcha2')
            ->update([
                'act'         => 'cloudflare-turnstile',
                'name'        => 'Cloudflare Turnstile',
                'description' => 'Get your site key and secret key from the Cloudflare Turnstile dashboard.',
            ]);
    }
};
