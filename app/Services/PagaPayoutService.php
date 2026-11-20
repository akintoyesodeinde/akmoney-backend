<?php

namespace App\Services;

use App\Models\PagaApiSetting;
use App\Models\PayoutApiLog;
use App\Models\SendMoney;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PagaPayoutService
{
    private PagaApiSetting $config;

    public function __construct()
    {
        $this->config = PagaApiSetting::instance();
    }

    // ── Public entry point ─────────────────────────────────────────────────

    public function initiatePayout(SendMoney $sendMoney): bool
    {
        try {
            $ref         = $sendMoney->trx . '_' . Str::random(6);
            $amount      = (string) $sendMoney->recipient_amount;
            $currency    = $sendMoney->recipient_currency ?? 'NGN';

            // Bank details from service_form_data (collected on send-money form)
            $formData    = $sendMoney->service_form_data ?? [];
            $bankUuid    = $formData['bank_uuid']       ?? ($formData['bank_name'] ?? null);
            $accountNum  = $formData['account_number']  ?? ($formData['bank_account'] ?? null);

            if (!$bankUuid || !$accountNum) {
                PayoutApiLog::record($sendMoney->id, 'failed', [], [
                    'error' => 'Missing bank_uuid or account_number in service_form_data',
                ]);
                return false;
            }

            $hash    = $this->buildHash($ref, $amount, $currency, $bankUuid, $accountNum);
            $payload = [
                'referenceNumber'            => $ref,
                'amount'                     => $amount,
                'currency'                   => $currency,
                'destinationBankUUID'        => $bankUuid,
                'destinationBankAccountNumber' => $accountNum,
                'remarks'                    => 'Remittance from ' . ($sendMoney->sendingCountry->name ?? ''),
                'statusCallbackUrl'          => route('ipn.paga'),
            ];

            PayoutApiLog::record($sendMoney->id, 'initiated', $payload);

            $response = Http::withHeaders($this->authHeaders($hash))
                ->post($this->config->baseUrl() . '/paga-webservices/business-rest/secured/depositToBank', $payload);

            $body = $response->json() ?? [];
            PayoutApiLog::record($sendMoney->id, 'api_response', $payload, $body);

            if (($body['responseCode'] ?? -1) === 0) {
                $sendMoney->payout_channel      = 'api';
                $sendMoney->api_payout_id       = $body['transactionId'] ?? $ref;
                $sendMoney->api_status          = 'PENDING';
                $sendMoney->api_initiated_at    = now();
                $sendMoney->save();
                return true;
            }

            Log::warning('Paga payout non-zero responseCode', ['body' => $body, 'trx' => $sendMoney->trx]);
            PayoutApiLog::record($sendMoney->id, 'failed', $payload, $body);
            return false;

        } catch (\Throwable $e) {
            Log::error('PagaPayoutService::initiatePayout exception: ' . $e->getMessage(), ['trx' => $sendMoney->trx]);
            PayoutApiLog::record($sendMoney->id, 'exception', [], ['error' => $e->getMessage()]);
            return false;
        }
    }

    // ── Authentication ─────────────────────────────────────────────────────

    private function authHeaders(string $hash): array
    {
        return [
            'principal'    => $this->config->principal,
            'credentials'  => $this->config->credentials,
            'hash'         => $hash,
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Hash = SHA-512(referenceNumber + amount + currency + destinationBankUUID
     *                + destinationBankAccountNumber + apiKey)
     */
    private function buildHash(
        string $ref,
        string $amount,
        string $currency,
        string $bankUuid,
        string $accountNum
    ): string {
        $raw = $ref . $amount . $currency . $bankUuid . $accountNum . $this->config->api_key;
        return hash('sha512', $raw);
    }
}
