<?php

namespace App\Http\Controllers\Gateway\PawaPay;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use App\Models\Deposit;
use App\Models\PayoutApiSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * PawaPay Mobile Money Collection Gateway
 *
 * Flow:
 *  1. process() calls POST /v1/deposits on PawaPay — returns ACCEPTED.
 *  2. PawaPay pushes a USSD prompt to the customer's phone.
 *  3. Customer confirms on their phone.
 *  4. PawaPay POSTs a webhook to /ipn/pawapay-deposit.
 *  5. ipn() marks the deposit as PAYMENT_SUCCESS.
 *
 * Credentials are shared with the payout service (PayoutApiSetting).
 * Per-currency gateway_parameter must contain:
 *   - correspondent  (e.g. "MTN_MOMO_GHA")
 *   - country        (3-letter ISO, e.g. "GHA")
 */
class ProcessController extends Controller
{
    public static function process($deposit): string
    {
        $config = json_decode($deposit->gatewayCurrency()->gateway_parameter ?? '{}');
        $pawapay = PayoutApiSetting::instance();

        if (empty($pawapay->secret_key) || $pawapay->secret_key === '--------------------') {
            return json_encode(['error' => 'PawaPay is not configured. Please contact support.', 'view' => 'user.payment.PawaPay']);
        }

        $correspondent = $config->correspondent ?? null;
        $country       = $config->country       ?? null;

        if (!$correspondent || !$country) {
            return json_encode(['error' => 'PawaPay gateway is not fully configured for this currency.', 'view' => 'user.payment.PawaPay']);
        }

        $depositId = (string) Str::uuid();
        // If no phone in request, show the phone-entry form first
        $phone = request()->input('payer_phone');
        if (!$phone) {
            return json_encode([
                'needs_phone' => true,
                'correspondent' => $correspondent,
                'currency'     => strtoupper($deposit->method_currency),
                'view'         => 'user.payment.PawaPay',
            ]);
        }

        // Normalise phone: strip leading +
        $phone = ltrim(trim($phone), '+');

        // Convert base-currency amount to gateway currency using the deposit's stored rate
        $gatewayAmount = round($deposit->final_amount * $deposit->rate, 2);

        $payload = [
            'depositId'            => $depositId,
            'amount'               => (string) $gatewayAmount,
            'currency'             => strtoupper($deposit->method_currency),
            'correspondent'        => $correspondent,
            'payer'                => [
                'type'    => 'MSISDN',
                'address' => ['value' => $phone],
            ],
            'customerTimestamp'    => now()->toIso8601String(),
            'statementDescription' => 'Payment ' . $deposit->trx,
            'country'              => strtoupper($country),
            'metadata'             => [
                ['fieldName' => 'orderId',      'fieldValue' => $deposit->trx],
                ['fieldName' => 'depositId',    'fieldValue' => $depositId],
            ],
        ];

        $baseUrl = $pawapay->environment === 'live'
            ? 'https://api.pawapay.cloud'
            : 'https://api.sandbox.pawapay.cloud';

        try {
            $response = Http::withToken($pawapay->secret_key)
                ->timeout(30)
                ->post("{$baseUrl}/v1/deposits", $payload);

            $body   = $response->json() ?? [];
            $status = $body['status'] ?? null;

            if (in_array($status, ['ACCEPTED', 'DUPLICATE_IGNORED'])) {
                $deposit->btc_wallet = $depositId;
                $deposit->status     = Status::PAYMENT_PENDING;
                $deposit->detail     = ['deposit_id' => $depositId, 'phone' => $phone, 'correspondent' => $correspondent];
                $deposit->save();

                return json_encode([
                    'success'      => true,
                    'deposit_id'   => $depositId,
                    'phone'        => $phone,
                    'correspondent'=> $correspondent,
                    'view'         => 'user.payment.PawaPay',
                ]);
            }

            $errorMsg = $body['rejectionReason']['rejectionMessage']
                ?? $body['errorMessage']
                ?? $body['message']
                ?? 'PawaPay did not accept the request. Please try again.';
            Log::warning('PawaPay collection rejected', ['body' => $body, 'trx' => $deposit->trx]);
            return json_encode(['error' => $errorMsg, 'needs_phone' => true, 'correspondent' => $correspondent, 'currency' => strtoupper($deposit->method_currency), 'view' => 'user.payment.PawaPay']);

        } catch (\Throwable $e) {
            Log::error('PawaPay collection exception: ' . $e->getMessage());
            return json_encode(['error' => 'Could not connect to PawaPay. Please try again.', 'view' => 'user.payment.PawaPay']);
        }
    }

    public function ipn(Request $request)
    {
        $depositId = $request->input('depositId');
        $status    = strtoupper($request->input('status') ?? '');

        if (!$depositId) {
            return response()->json(['status' => 'missing_id'], 400);
        }

        $deposit = Deposit::where('btc_wallet', $depositId)->first();
        if (!$deposit) {
            return response()->json(['status' => 'not_found'], 404);
        }

        if ($status === 'COMPLETED') {
            PaymentController::userDataUpdate($deposit);
        } elseif (in_array($status, ['FAILED', 'CANCELLED'])) {
            $deposit->status = Status::PAYMENT_REJECT;
            $deposit->save();
        }

        return response()->json(['status' => 'ok']);
    }
}
