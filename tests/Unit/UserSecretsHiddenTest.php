<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Agent;

class UserSecretsHiddenTest extends TestCase
{
    /**
     * Test that critical security secrets are hidden from User serialization (KM-SEC-002).
     */
    public function test_user_secrets_are_hidden_from_serialization()
    {
        $user = new User([
            'username'             => 'testuser',
            'email'                => 'test@example.com',
            'password'             => 'hashed_password',
            'remember_token'       => 'token_123',
            'ver_code'             => '123456',
            'tsc'                  => '2FA_SECRET_KEY_XYZ',
            'pin'                  => '9999',
            'security_answer_hash' => 'hash_answer',
            'profile_change_code'  => 'change_code_123',
            'two_fa_code'          => '654321',
        ]);

        $array = $user->toArray();
        $json  = json_encode($user);

        $sensitiveFields = [
            'password',
            'remember_token',
            'ver_code',
            'tsc',
            'pin',
            'security_answer_hash',
            'profile_change_code',
            'two_fa_code',
        ];

        foreach ($sensitiveFields as $field) {
            $this->assertArrayNotHasKey($field, $array, "Field {$field} should be hidden from User array serialization");
            $this->assertStringNotContainsString("\"{$field}\"", $json, "Field {$field} should be hidden from User JSON serialization");
        }
    }

    /**
     * Test that Agent secrets are hidden from serialization.
     */
    public function test_agent_secrets_are_hidden_from_serialization()
    {
        $agent = new Agent([
            'username'       => 'testagent',
            'email'          => 'agent@example.com',
            'password'       => 'agent_password',
            'remember_token' => 'remember_token',
            'ver_code'       => '654321',
            'tsc'            => 'AGENT_2FA_SECRET',
        ]);

        $array = $agent->toArray();
        $json  = json_encode($agent);

        $sensitiveFields = ['password', 'remember_token', 'ver_code', 'tsc'];

        foreach ($sensitiveFields as $field) {
            $this->assertArrayNotHasKey($field, $array, "Field {$field} should be hidden from Agent array serialization");
            $this->assertStringNotContainsString("\"{$field}\"", $json, "Field {$field} should be hidden from Agent JSON serialization");
        }
    }
}
