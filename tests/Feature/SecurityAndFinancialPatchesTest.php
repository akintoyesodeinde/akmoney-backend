<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\PasswordReset;
use App\Models\Deposit;
use App\Models\Wallet;
use App\Constants\Status;
use App\Http\Controllers\Gateway\PaymentController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SecurityAndFinancialPatchesTest extends TestCase
{
    /**
     * Test KM-FIN-001: API /api/send-money/save enforces sending_amount > 0 and payment_type.
     */
    public function test_api_send_money_rejects_negative_or_zero_amount()
    {
        $response = $this->postJson('/api/send-money/save', [
            'sending_amount'    => -100,
            'sending_country'   => 1,
            'recipient_country' => 2,
            'payment_type'      => 999, // invalid payment type
            'source_of_funds'   => 1,
            'sending_purpose'   => 1,
            'recipient'         => [
                'name'    => 'John Doe',
                'mobile'  => '+1234567890',
                'address' => '123 Main St',
            ],
            'delivery_method'   => 1,
        ]);

        // It should reject with HTTP 400/422 validation error
        $response->assertStatus(400);
        $json = $response->json();
        $this->assertEquals('error', $json['status'] ?? null);
    }

    /**
     * Test KM-SEC-003: Password reset validates token expiration and invalidates token after reset.
     */
    public function test_password_reset_enforces_expiry_and_invalidation()
    {
        $email = 'test_' . Str::random(8) . '@example.com';
        $user = new User();
        $user->firstname = 'Test';
        $user->lastname  = 'User';
        $user->username  = 'test_' . Str::random(6);
        $user->email     = $email;
        $user->password  = Hash::make('OldPassword123!');
        $user->save();

        // 1. Expired token (created 90 minutes ago)
        $expiredToken = '123456';
        $reset = new PasswordReset();
        $reset->email = $email;
        $reset->token = $expiredToken;
        $reset->created_at = Carbon::now()->subMinutes(90);
        $reset->save();

        $response = $this->post('/user/password/reset', [
            'email'                 => $email,
            'token'                 => $expiredToken,
            'password'              => 'NewSecurePass123!',
            'password_confirmation' => 'NewSecurePass123!',
        ]);

        // Should redirect back with error because token is expired
        $this->assertTrue(PasswordReset::where('email', $email)->count() === 0, 'Expired token should be deleted or rejected');

        // 2. Fresh token
        $freshToken = '654321';
        $reset2 = new PasswordReset();
        $reset2->email = $email;
        $reset2->token = $freshToken;
        $reset2->created_at = Carbon::now();
        $reset2->save();

        $response = $this->post('/user/password/reset', [
            'email'                 => $email,
            'token'                 => $freshToken,
            'password'              => 'NewSecurePass123!',
            'password_confirmation' => 'NewSecurePass123!',
        ]);

        // Token must be deleted after successful use
        $this->assertDatabaseMissing('password_resets', [
            'email' => $email,
            'token' => $freshToken,
        ]);

        // Clean up
        $user->delete();
    }

    /**
     * Test KM-FIN-003: PaymentController::userDataUpdate is idempotent under locks.
     */
    public function test_user_data_update_is_idempotent()
    {
        $user = new User();
        $user->firstname = 'Deposit';
        $user->lastname  = 'Tester';
        $user->username  = 'dep_' . Str::random(6);
        $user->email     = 'dep_' . Str::random(8) . '@example.com';
        $user->password  = Hash::make('Password123!');
        $user->balance   = 0;
        $user->save();

        $wallet = new Wallet();
        $wallet->user_id = $user->id;
        $wallet->currency_code = 'USD';
        $wallet->balance = 50.00;
        $wallet->save();

        $deposit = new Deposit();
        $deposit->user_id = $user->id;
        $deposit->wallet_id = $wallet->id;
        $deposit->method_code = 101;
        $deposit->method_currency = 'USD';
        $deposit->amount = 100.00;
        $deposit->charge = 2.00;
        $deposit->rate = 1.0;
        $deposit->final_amount = 102.00;
        $deposit->trx = getTrx();
        $deposit->status = Status::PAYMENT_INITIATE;
        $deposit->save();

        // First call: should succeed and credit wallet
        $firstResult = PaymentController::userDataUpdate($deposit);
        $this->assertTrue($firstResult);

        $wallet->refresh();
        $this->assertEquals(150.00, (float) $wallet->balance);

        $deposit->refresh();
        $this->assertEquals(Status::PAYMENT_SUCCESS, $deposit->status);

        // Second call (simulating duplicate webhook replay): must NOT double credit
        $secondResult = PaymentController::userDataUpdate($deposit);
        $this->assertTrue($secondResult);

        $wallet->refresh();
        $this->assertEquals(150.00, (float) $wallet->balance, 'Balance must remain 150.00 and not be credited twice');

        // Clean up
        $deposit->delete();
        $wallet->delete();
        $user->delete();
    }
}
