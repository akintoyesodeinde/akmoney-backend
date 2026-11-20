<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── Google Translate ─────────────────────────────────────────────────
        // Stores the Google Cloud Translation API key used by PageTranslator.
        // When enabled with a valid key, PageTranslator switches to the official
        // batch API (array queries) which is more reliable than the free tier.
        if (!DB::table('extensions')->where('act', 'google-translate')->exists()) {
            DB::table('extensions')->insert([
                'act'         => 'google-translate',
                'name'        => 'Google Translate',
                'description' => 'Provide your Google Cloud Translation API key to enable server-side page translation with higher rate limits and better accuracy. Leave blank to use the free (unofficial) tier. Obtain your key at: Google Cloud Console → APIs & Services → Credentials → Create API Key (enable the Cloud Translation API).',
                'image'       => 'google_translate.svg',
                'support'     => 'na',
                'status'      => 0,
                'shortcode'   => json_encode([
                    'api_key' => [
                        'title' => 'API Key',
                        'value' => '',
                    ],
                ]),
                'script'      => '',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        // ── Google OAuth 2.0 ─────────────────────────────────────────────────
        // Stores the OAuth 2.0 Client ID & Secret for "Sign in with Google".
        // Obtain credentials at: Google Cloud Console → APIs & Services →
        // Credentials → Create OAuth 2.0 Client ID (Web application).
        if (!DB::table('extensions')->where('act', 'google-auth')->exists()) {
            DB::table('extensions')->insert([
                'act'         => 'google-auth',
                'name'        => 'Google OAuth',
                'description' => 'Enable Sign in with Google for your users. Obtain your Client ID and Client Secret from Google Cloud Console → APIs & Services → Credentials → OAuth 2.0 Client IDs. Set your Authorised redirect URI to: ' . url('/auth/google/callback'),
                'image'       => 'google_auth.svg',
                'support'     => 'na',
                'status'      => 0,
                'shortcode'   => json_encode([
                    'client_id' => [
                        'title' => 'Client ID',
                        'value' => '',
                    ],
                    'client_secret' => [
                        'title' => 'Client Secret',
                        'value' => '',
                    ],
                ]),
                'script'      => '',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('extensions')->whereIn('act', ['google-translate', 'google-auth'])->delete();
    }
};
