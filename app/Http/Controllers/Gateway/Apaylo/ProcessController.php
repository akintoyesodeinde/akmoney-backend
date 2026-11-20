<?php

namespace App\Http\Controllers\Gateway\Apaylo;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use App\Models\Deposit;
use Illuminate\Http\Request;

class ProcessController extends Controller
{
    /**
     * Apaylo Interac e-Transfer Gateway
     *
     * Flow:
     *  1. process() sends an Interac request email to the customer and shows a pending page.
     *  2. The customer completes payment from their bank app/email link.
     *  3. A cron job (CronController::syncApayloTransactions) polls Apaylo and finalises the deposit.
     *
     * No webhook/IPN endpoint is used — Apaylo does not support push webhooks.
     */
    public static function process($deposit): string
    {
        $config = json_decode($deposit->gatewayCurrency()->gateway_parameter);

        if (empty($config->api_key) || empty($config->secret_key)) {
            $send['error'] = 'Apaylo gateway is not configured. Please contact support.';
            $send['view']  = 'user.payment.Apaylo';
            $send['success'] = false;
            return json_encode($send);
        }

        $client = new ApayloClient(
            $config->api_key,
            $config->secret_key,
            $config->environment ?? 'sandbox'
        );

        $user        = auth()->user() ?? $deposit->user;
        $description = substr('Payment ref ' . $deposit->trx, 0, 200);

        $response = $client->requestInteracEtransfer(
            customerEmail: $user->email,
            customerName:  $user->fullname,
            amount:        (float) $deposit->final_amount,
            description:   $description
        );

        // Apaylo wraps all results in a "Result" object: {"StatusCode":..,"IsError":..,"Result":{...}}
        $result = $response->Result ?? $response;
        $txNumber = $result->TransactionNumber ?? null;

        if (!empty($response->IsError) || empty($txNumber)) {
            $errorMessage = $result->Message ?? $response->Message ?? 'Could not initiate Interac e-Transfer. Please try another payment method.';

            $deposit->detail = ['error' => $errorMessage];
            $deposit->save();

            $send['error']   = $errorMessage;
            $send['view']    = 'user.payment.Apaylo';
            $send['success'] = false;
            return json_encode($send);
        }

        // Store Apaylo's TransactionNumber in btc_wallet (re-used field for gateway tx IDs)
        $deposit->btc_wallet = $txNumber;
        $deposit->status     = Status::PAYMENT_PENDING;
        $deposit->detail     = [
            'transaction_number'   => $txNumber,
            'reference_number'     => $result->ReferenceNumber ?? null,
            'message'              => $result->Message ?? null,
        ];
        $deposit->save();

        $send['success']            = true;
        $send['transaction_number'] = $txNumber;
        $send['reference_number']   = $result->ReferenceNumber ?? null;
        $send['customer_email']     = $user->email;
        $send['view']               = 'user.payment.Apaylo';
        return json_encode($send);
    }

    /**
     * Resume: rebuild the view data from an already-pending deposit (no API call).
     * Called when depositConfirm() is revisited after the deposit is already PAYMENT_PENDING.
     */
    public static function resume($deposit): string
    {
        $user = auth()->user() ?? $deposit->user;

        $send['success']            = true;
        $send['transaction_number'] = $deposit->btc_wallet;
        $send['reference_number']   = $deposit->detail->reference_number ?? null;
        $send['customer_email']     = $user->email;
        $send['view']               = 'user.payment.Apaylo';
        return json_encode($send);
    }

    /**
     * IPN handler — not used (Apaylo has no webhooks).
     * Status sync is handled by the cron job instead.
     */
    public function ipn(Request $request)
    {
        return response()->json(['status' => 'ok']);
    }
}
