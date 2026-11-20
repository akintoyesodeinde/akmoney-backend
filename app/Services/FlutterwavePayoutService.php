<?php

namespace App\Services;

use App\Models\PayoutApiLog;
use App\Models\PayoutApiSetting;
use App\Models\SendMoney;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FlutterwavePayoutService
{
    private string $baseUrl = 'https://api.flutterwave.com/v3';
    private PayoutApiSetting $settings;

    public function __construct()
    {
        $this->settings = PayoutApiSetting::instance();
    }

    /**
     * Initiate a payout transfer for a confirmed send money record.
     * Returns true on success, false on failure.
     */
    public function initiatePayout(SendMoney $sendMoney): bool
    {
        $method = $sendMoney->payoutApiMethod;

        if (!$method) {
            Log::error("PayoutAPI: no payout method on send_money #{$sendMoney->id}");
            return false;
        }

        $recipient = $sendMoney->recipient;
        $payload   = $this->buildPayload($sendMoney, $method->method_code, $recipient);

        PayoutApiLog::record($sendMoney->id, 'initiated', $payload);

        try {
            $response = Http::withToken($this->settings->secret_key)
                ->timeout(30)
                ->post("{$this->baseUrl}/transfers", $payload);

            $body = $response->json();

            PayoutApiLog::record($sendMoney->id, 'api_response', $payload, $body ?? []);

            if ($response->successful() && ($body['status'] ?? '') === 'success') {
                $transferId = $body['data']['id'] ?? null;

                $sendMoney->api_payout_id   = $transferId;
                $sendMoney->api_status      = $body['data']['status'] ?? 'NEW';
                $sendMoney->api_initiated_at = now();
                $sendMoney->payout_channel  = 'api';
                $sendMoney->save();

                return true;
            }

            Log::error("PayoutAPI: transfer failed for send_money #{$sendMoney->id}", ['body' => $body]);
            PayoutApiLog::record($sendMoney->id, 'failed', $payload, $body ?? []);
            return false;

        } catch (\Throwable $e) {
            Log::error("PayoutAPI: exception for send_money #{$sendMoney->id}: " . $e->getMessage());
            PayoutApiLog::record($sendMoney->id, 'exception', $payload, ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Fetch current status of a transfer from Flutterwave.
     */
    public function getTransferStatus(string $transferId): ?array
    {
        try {
            $response = Http::withToken($this->settings->secret_key)
                ->timeout(15)
                ->get("{$this->baseUrl}/transfers/{$transferId}");

            if ($response->successful()) {
                return $response->json('data');
            }
        } catch (\Throwable $e) {
            Log::error("PayoutAPI: getTransferStatus exception: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Verify inbound webhook signature.
     * Flutterwave sends the webhook secret in the verif-hash header.
     */
    public function verifyWebhook(string $hashHeader): bool
    {
        $secret = $this->settings->webhook_secret;
        if (!$secret) {
            return false;
        }
        return hash_equals($secret, $hashHeader);
    }

    /**
     * Fetch bank list for a given ISO country code from Flutterwave.
     */
    public function getBanks(string $countryCode): array
    {
        try {
            $response = Http::withToken($this->settings->secret_key)
                ->timeout(15)
                ->get("{$this->baseUrl}/banks/" . strtoupper($countryCode));

            if ($response->successful()) {
                return $response->json('data') ?? [];
            }
        } catch (\Throwable $e) {
            Log::error("PayoutAPI: getBanks exception: " . $e->getMessage());
        }

        return [];
    }

    private function buildPayload(SendMoney $sendMoney, string $methodCode, $recipient): array
    {
        $name  = is_array($recipient) ? ($recipient['name'] ?? '') : ($recipient?->name ?? '');
        $phone = is_array($recipient) ? ($recipient['mobile'] ?? '') : ($recipient?->mobile ?? '');

        // Strip dial code if present — Flutterwave expects plain number or E.164
        $phone = preg_replace('/^\+/', '', $phone);

        $base = [
            'amount'           => (float) $sendMoney->recipient_amount,
            'currency'         => $sendMoney->recipient_currency,
            'narration'        => 'Money transfer — ' . $sendMoney->trx,
            'reference'        => 'SM-' . $sendMoney->id . '-' . time(),
            'beneficiary_name' => $name,
            'meta'             => [[
                'first_name'    => explode(' ', $name)[0] ?? '',
                'last_name'     => implode(' ', array_slice(explode(' ', $name), 1)) ?: $name,
                'mobile_number' => $phone,
                'email'         => is_array($recipient) ? ($recipient['email'] ?? '') : ($recipient?->email ?? ''),
            ]],
        ];

        if ($methodCode === 'mpesa') {
            $base['account_bank']   = 'MPS';
            $base['account_number'] = $phone;
            return $base;
        }

        if ($methodCode === 'mobile_money') {
            // account_bank = network operator code provided by admin/user
            // account_number = phone number
            $base['account_bank']   = is_array($recipient) ? ($recipient['bank_code'] ?? 'MTN') : ($recipient?->bank_code ?? 'MTN');
            $base['account_number'] = $phone;
            return $base;
        }

        // bank_transfer
        $base['account_bank']   = is_array($recipient) ? ($recipient['bank_code'] ?? '') : ($recipient?->bank_code ?? '');
        $base['account_number'] = is_array($recipient) ? ($recipient['account_number'] ?? '') : ($recipient?->account_number ?? '');
        return $base;
    }
}
