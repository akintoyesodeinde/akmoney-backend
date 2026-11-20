<?php

namespace App\Http\Controllers\Gateway\Paga;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use App\Models\Deposit;
use App\Models\PagaApiSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Paga Mobile Money Collection Gateway (Nigeria — NGN)
 *
 * Flow:
 *  1. process() calls POST .../requestPayment on Paga API.
 *  2. Paga sends a payment request notification to the customer's phone.
 *  3. Customer approves via Paga app or USSD.
 *  4. Paga POSTs a callback to /ipn/paga-collect.
 *  5. ipn() marks the deposit as PAYMENT_SUCCESS.
 *
 * Credentials are shared with the payout service (PagaApiSetting).
 */
class ProcessController extends Controller
{
    public static function process($deposit): string
    {
        $paga = PagaApiSetting::instance();

        if (empty($paga->principal) || empty($paga->credentials) || empty($paga->api_key)) {
            return json_encode(['error' => 'Paga is not configured. Please contact support.', 'view' => 'user.payment.Paga']);
        }

        // If no phone in request, show the phone-entry form first
        $phone = request()->input('payer_phone');
        if (!$phone) {
            return json_encode([
                'needs_phone' => true,
                'currency'    => strtoupper($deposit->method_currency),
                'view'        => 'user.payment.Paga',
            ]);
        }
        $phone = ltrim(trim($phone), '+');

        $ref      = $deposit->trx;
        $amount   = (string) round($deposit->final_amount, 2);
        $currency = strtoupper($deposit->method_currency);
        $callback = route('ipn.paga-collect');

        // Hash = SHA-512(referenceNumber + amount + currency + payer + callbackUrl + apiKey)
        $hash = hash('sha512', $ref . $amount . $currency . $phone . $callback . $paga->api_key);

        $baseUrl = $paga->isLive()
            ? 'https://www.mypaga.com'
            : 'https://beta.mypaga.com';

        $payload = [
            'referenceNumber'   => $ref,
            'amount'            => $amount,
            'currency'          => $currency,
            'payer'             => $phone,
            'payerDisplayName'  => optional($deposit->user)->fullname ?? 'Customer',
            'message'           => 'Payment for ' . $ref,
            'hasFees'           => false,
            'isMerchantPayment' => true,
            'callbackUrl'       => $callback,
        ];

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'principal'    => $paga->principal,
                'credentials'  => $paga->credentials,
                'hash'         => $hash,
            ])->timeout(30)
              ->post("{$baseUrl}/paga-webservices/business-rest/secured/requestPayment", $payload);

            $body = $response->json() ?? [];
            $code = (int) ($body['responseCode'] ?? -1);

            if ($code === 0) {
                $deposit->btc_wallet = $body['transactionId'] ?? $ref;
                $deposit->status     = Status::PAYMENT_PENDING;
                $deposit->detail     = ['transaction_id' => $body['transactionId'] ?? null, 'phone' => $phone];
                $deposit->save();

                return json_encode([
                    'success'        => true,
                    'transaction_id' => $body['transactionId'] ?? null,
                    'phone'          => $phone,
                    'view'           => 'user.payment.Paga',
                ]);
            }

            $errorMsg = $body['message'] ?? ('Paga error code: ' . $code);
            Log::warning('Paga collection rejected', ['body' => $body, 'trx' => $deposit->trx]);
            return json_encode(['error' => $errorMsg, 'view' => 'user.payment.Paga']);

        } catch (\Throwable $e) {
            Log::error('Paga collection exception: ' . $e->getMessage());
            return json_encode(['error' => 'Could not connect to Paga. Please try again.', 'view' => 'user.payment.Paga']);
        }
    }

    public function ipn(Request $request)
    {
        // Paga sends: referenceNumber, transactionId, status (SUCCESS / FAIL)
        $ref    = $request->input('referenceNumber');
        $txId   = $request->input('transactionId');
        $status = strtoupper($request->input('status') ?? '');

        $deposit = null;
        if ($txId)  $deposit = Deposit::where('btc_wallet', $txId)->first();
        if (!$deposit && $ref) $deposit = Deposit::where('trx', $ref)->first();

        if (!$deposit) {
            return response()->json(['status' => 'not_found'], 404);
        }

        if ($status === 'SUCCESS') {
            PaymentController::userDataUpdate($deposit);
        } elseif ($status === 'FAIL' || $status === 'FAILED') {
            $deposit->status = Status::PAYMENT_REJECT;
            $deposit->save();
        }

        return response()->json(['status' => 'ok']);
    }
}
