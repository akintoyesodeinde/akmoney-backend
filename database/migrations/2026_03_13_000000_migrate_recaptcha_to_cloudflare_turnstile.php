<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('extensions')
            ->where('act', 'google-recaptcha3')
            ->update([
                'act'         => 'cloudflare-turnstile',
                'name'        => 'Cloudflare Turnstile',
                'description' => 'Get your site key and secret key from the Cloudflare Turnstile dashboard.',
                'image'       => 'turnstile.png',
                'support'     => 'turnstile.png',
            ]);
    }

    public function down(): void
    {
        DB::table('extensions')
            ->where('act', 'cloudflare-turnstile')
            ->update([
                'act'         => 'google-recaptcha3',
                'name'        => 'Google Recaptcha 3',
                'description' => 'Key location is shown bellow',
                'image'       => 'recaptcha3.png',
                'support'     => 'recaptcha.png',
            ]);
    }
};
