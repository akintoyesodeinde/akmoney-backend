<?php

namespace App\Http\Controllers\Gateway\Webhook;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\PayoutApiLog;
use App\Models\SendMoney;
use App\Services\PawaPayPayoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayoutWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $settings = \App\Models\PayoutApiSetting::instance();
        if (!empty($settings->webhook_secret)) {
            $signature = $request->header('X-PawaPay-Signature') 
                ?? $request->header('X-Webhook-Signature') 
                ?? $request->header('Authorization');
            $expectedSig = hash_hmac('sha256', $request->getContent(), $settings->webhook_secret);
            if (!$signature || (!hash_equals($expectedSig, $signature) && !hash_equals($settings->webhook_secret, $signature))) {
                Log::warning('PawaPay webhook: signature verification failed');
                return response()->json(['error' => 'Unauthorized signature'], 401);
            }
        }

        $payload = $request->all();

        Log::info('PawaPay payout webhook received', ['payload' => $payload]);

        // pawaPay posts the remittanceId we generated at initiation
        $remittanceId = $payload['remittanceId'] ?? null;

        if (!$remittanceId) {
            Log::warning('PawaPay webhook: missing remittanceId');
            return response()->json(['status' => 'ok']);
        }

        return \Illuminate\Support\Facades\DB::transaction(function () use ($remittanceId, $payload) {
            $sendMoney = SendMoney::where('api_payout_id', $remittanceId)->lockForUpdate()->first();

            if (!$sendMoney) {
                Log::warning("PawaPay webhook: no send_money found for remittanceId {$remittanceId}");
                return response()->json(['status' => 'ok']);
            }

            PayoutApiLog::record($sendMoney->id, 'webhook_received', [], $payload);

            $mapped = PawaPayPayoutService::mapStatus($payload['status'] ?? 'PENDING');

            $sendMoney->api_status = $mapped;
            $sendMoney->save();

            if ($mapped === 'SUCCESSFUL' && $sendMoney->status !== Status::SEND_MONEY_COMPLETED) {
                $sendMoney->status = Status::SEND_MONEY_COMPLETED;
                $sendMoney->save();

                $user = $sendMoney->user ?? $sendMoney->agent;
                if ($user) {
                    notify($user, 'SEND_MONEY_COMPLETE', [
                        'trx'                => $sendMoney->trx,
                        'sending_country'    => optional($sendMoney->sendingCountry)->name,
                        'sending_amount'     => showAmount($sendMoney->sending_amount, currencyFormat: false),
                        'sending_currency'   => $sendMoney->sending_currency,
                        'recipient__country' => optional($sendMoney->recipientCountry)->name,
                        'recipient_amount'   => showAmount($sendMoney->recipient_amount, currencyFormat: false),
                        'recipient_currency' => $sendMoney->recipient_currency,
                    ]);
                }

                PayoutApiLog::record($sendMoney->id, 'completed', [], $payload);

            } elseif ($mapped === 'FAILED') {
                PayoutApiLog::record($sendMoney->id, 'failed', [], $payload);
                Log::error("PawaPay webhook: FAILED for send_money #{$sendMoney->id}");
            }

            return response()->json(['status' => 'ok']);
        });
    }
}
