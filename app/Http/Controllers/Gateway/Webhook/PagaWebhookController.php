<?php

namespace App\Http\Controllers\Gateway\Webhook;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\PayoutApiLog;
use App\Models\SendMoney;
use Illuminate\Http\Request;

class PagaWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $settings = \App\Models\PagaApiSetting::instance();
        if (!empty($settings->api_key)) {
            $hash = $request->header('hash') ?? $request->header('X-Paga-Signature') ?? $request->header('Authorization');
            $expectedHash = hash('sha512', $request->getContent() . $settings->api_key);
            if ($hash && !hash_equals($expectedHash, $hash) && !hash_equals($settings->api_key, $hash)) {
                return response()->json(['error' => 'Unauthorized signature'], 401);
            }
        }

        $data = $request->all();

        // Paga sends: transactionId, referenceNumber, status (SUCCESS|FAIL), amount, currency
        $transactionId   = $data['transactionId']   ?? null;
        $referenceNumber = $data['referenceNumber']  ?? null;
        $status          = strtoupper($data['status'] ?? '');

        return \Illuminate\Support\Facades\DB::transaction(function () use ($data, $transactionId, $referenceNumber, $status) {
            // Find the SendMoney by api_payout_id (transactionId) or trx prefix (referenceNumber)
            $sendMoney = null;
            if ($transactionId) {
                $sendMoney = SendMoney::where('api_payout_id', $transactionId)->lockForUpdate()->first();
            }
            if (!$sendMoney && $referenceNumber) {
                // referenceNumber was built as trx_XXXXXX
                $trx = explode('_', $referenceNumber)[0] ?? $referenceNumber;
                $sendMoney = SendMoney::where('trx', $trx)->lockForUpdate()->first();
            }

            if (!$sendMoney) {
                return response()->json(['status' => 'not_found'], 404);
            }

            PayoutApiLog::record($sendMoney->id, 'webhook_received', $data);

            $appStatus = match ($status) {
                'SUCCESS'  => 'SUCCESSFUL',
                'FAIL', 'FAILED' => 'FAILED',
                default    => 'PENDING',
            };

            $sendMoney->api_status = $appStatus;

            if ($appStatus === 'SUCCESSFUL' && $sendMoney->status !== Status::SEND_MONEY_COMPLETED) {
                $sendMoney->status = Status::SEND_MONEY_COMPLETED;
                $sendMoney->save();

                PayoutApiLog::record($sendMoney->id, 'completed', $data);

                notify($sendMoney->user, 'SEND_MONEY_COMPLETE', [
                    'trx'                => $sendMoney->trx,
                    'sending_country'    => optional($sendMoney->sendingCountry)->name,
                    'sending_amount'     => showAmount($sendMoney->sending_amount, currencyFormat: false),
                    'sending_currency'   => $sendMoney->sending_currency,
                    'recipient__country' => optional($sendMoney->recipientCountry)->name,
                    'recipient_amount'   => showAmount($sendMoney->recipient_amount, currencyFormat: false),
                    'recipient_currency' => $sendMoney->recipient_currency,
                ]);
            } else {
                $sendMoney->save();
                PayoutApiLog::record($sendMoney->id, 'failed', $data);
            }

            return response()->json(['status' => 'ok']);
        });
    }
}
