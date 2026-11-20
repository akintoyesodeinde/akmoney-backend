<?php

namespace App\Services;

use App\Models\PayoutApiLog;
use App\Models\PayoutApiSetting;
use App\Models\SendMoney;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PawaPayPayoutService
{
    private PayoutApiSetting $settings;

    public function __construct()
    {
        $this->settings = PayoutApiSetting::instance();
    }

    public function initiatePayout(SendMoney $sendMoney): bool
    {
        $method = $sendMoney->payoutApiMethod;

        if (!$method) {
            Log::error("PawaPayPayout: no payout method on send_money #{$sendMoney->id}");
            return false;
        }

        $provider = $method->pawapay_provider;
        if (!$provider) {
            Log::error("PawaPayPayout: no pawapay_provider configured for method #{$method->id} ({$method->method_name})");
            return false;
        }

        $remittanceId = (string) Str::uuid();
        $payload      = $this->buildPayload($sendMoney, $provider, $remittanceId);

        PayoutApiLog::record($sendMoney->id, 'initiated', $payload);

        try {
            $response = Http::withToken($this->settings->secret_key)
                ->timeout(30)
                ->post($this->baseUrl() . '/v2/remittances', $payload);

            $body   = $response->json() ?? [];
            $status = $body['status'] ?? null;

            PayoutApiLog::record($sendMoney->id, 'api_response', $payload, $body);

            if (in_array($status, ['ACCEPTED', 'DUPLICATE_IGNORED'])) {
                $sendMoney->api_payout_id    = $remittanceId;
                $sendMoney->api_status       = 'PENDING';
                $sendMoney->api_initiated_at = now();
                $sendMoney->payout_channel   = 'api';
                $sendMoney->save();

                return true;
            }

            Log::error("PawaPayPayout: remittance rejected for send_money #{$sendMoney->id}", ['body' => $body]);
            PayoutApiLog::record($sendMoney->id, 'failed', $payload, $body);
            return false;

        } catch (\Throwable $e) {
            Log::error("PawaPayPayout: exception for send_money #{$sendMoney->id}: " . $e->getMessage());
            PayoutApiLog::record($sendMoney->id, 'exception', $payload, ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function getRemittanceStatus(string $remittanceId): ?array
    {
        try {
            $response = Http::withToken($this->settings->secret_key)
                ->timeout(15)
                ->get($this->baseUrl() . "/v2/remittances/{$remittanceId}");

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Throwable $e) {
            Log::error("PawaPayPayout: getRemittanceStatus exception: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Ask pawaPay to re-fire the webhook for a remittance.
     * Useful when the original callback was missed or failed to deliver.
     */
    public function resendCallback(string $remittanceId): bool
    {
        try {
            $response = Http::withToken($this->settings->secret_key)
                ->timeout(15)
                ->post($this->baseUrl() . "/v2/remittances/resend-callback/{$remittanceId}");

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error("PawaPayPayout: resendCallback exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Force-fail an ENQUEUED remittance (sandbox only — for testing failure flows).
     */
    public function failEnqueued(string $remittanceId): bool
    {
        try {
            $response = Http::withToken($this->settings->secret_key)
                ->timeout(15)
                ->post($this->baseUrl() . "/v2/remittances/fail-enqueued/{$remittanceId}");

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error("PawaPayPayout: failEnqueued exception: " . $e->getMessage());
            return false;
        }
    }

    public static function mapStatus(string $status): string
    {
        return match (strtoupper($status)) {
            'COMPLETED'            => 'SUCCESSFUL',
            'FAILED'               => 'FAILED',
            'ACCEPTED', 'ENQUEUED' => 'PENDING',
            default                => 'PENDING',
        };
    }

    // ── Payload builder ──────────────────────────────────────────────────────

    private function buildPayload(SendMoney $sendMoney, string $provider, string $remittanceId): array
    {
        $recipient = $sendMoney->recipient;
        $sender    = $this->resolveSender($sendMoney);

        [$recipientFirst, $recipientLast] = $this->splitName($this->str($recipient, 'name'));
        [$senderFirst, $senderLast]       = $this->splitName($sender['fullname'] ?? '');

        return [
            'remittanceId' => $remittanceId,
            'recipient'    => [
                'type'             => 'MMO',
                'accountDetails'   => [
                    'phoneNumber' => $this->normalizePhone($this->str($recipient, 'mobile')),
                    'provider'    => $provider,
                ],
                'recipientDetails' => [
                    'firstName' => $recipientFirst,
                    'lastName'  => $recipientLast,
                ],
            ],
            'sender' => [
                'transactionDetails' => [
                    'transactionReference' => $sendMoney->trx,
                    'originalAmount'       => (string) $sendMoney->sending_amount,
                    'originalCurrency'     => $sendMoney->sending_currency,
                    'buyFxRate'            => (string) round($sendMoney->conversion_rate, 6),
                    'senderFees'           => (string) round($sendMoney->sending_charge ?? 0, 2),
                    'purposeOfFunds'       => $this->mapPurpose($sendMoney->sendingPurpose?->name),
                    'sourceOfFunds'        => $this->mapSourceOfFunds($sendMoney->sourceOfFund?->name),
                ],
                'senderDetails' => [
                    'firstName'              => $senderFirst,
                    'lastName'               => $senderLast,
                    'nationality'            => $sender['country'] ?? '',
                    'phoneNumber'            => $this->normalizePhone($sender['mobile'] ?? ''),
                    'address'                => [
                        'addressLine' => $sender['address_line'] ?? '',
                        'postalCode'  => $sender['zip'] ?? '',
                        'city'        => $sender['city'] ?? '',
                        'country'     => $sender['country'] ?? '',
                    ],
                    'identification'         => [
                        'type'   => $sender['id_type'] ?? 'OTHER',
                        'number' => $sender['id_number'] ?? '',
                    ],
                    'gender'                 => $sender['gender'] ?? 'UNKNOWN',
                    'dateOfBirth'            => $sender['dob'] ?? '',
                    'placeOfBirth'           => $sender['country'] ?? '',
                    'occupation'             => $sender['occupation'] ?? 'OTHER',
                    'relationshipRecipient'  => 'OTHER',
                ],
            ],
            'amount'          => number_format((float) $sendMoney->recipient_amount, 2, '.', ''),
            'currency'        => $sendMoney->recipient_currency,
            'customerMessage' => 'Money Transfer',
            'metadata'        => [
                ['orderId' => 'SM-' . $sendMoney->id],
                ['trx'     => $sendMoney->trx, 'isPII' => false],
            ],
        ];
    }

    /**
     * Build a normalised sender map from the user model (user-initiated)
     * or the sendMoney->sender JSON object (agent-initiated).
     */
    private function resolveSender(SendMoney $sendMoney): array
    {
        if ($sendMoney->user_id && $user = $sendMoney->user) {
            $addr = $user->address; // cast object

            // Extract identification from KYC data if available
            $idType   = 'OTHER';
            $idNumber = '';
            if (is_array($user->kyc_data)) {
                foreach ($user->kyc_data as $field) {
                    $fieldName = strtolower($field['name'] ?? '');
                    if (str_contains($fieldName, 'passport') || str_contains($fieldName, 'id number') || str_contains($fieldName, 'document')) {
                        $idType   = str_contains($fieldName, 'passport') ? 'PASSPORT' : 'NATIONAL_ID';
                        $idNumber = $field['value'] ?? '';
                        break;
                    }
                }
            }

            return [
                'fullname'     => $user->firstname . ' ' . $user->lastname,
                'mobile'       => $user->dial_code . $user->mobile,
                'country'      => $user->country_name ?? ($addr->country ?? ''),
                'address_line' => ($addr->address ?? $user->state ?? ''),
                'city'         => $user->city ?? ($addr->city ?? ''),
                'zip'          => $user->zip ?? ($addr->zip ?? ''),
                'dob'          => $user->dob ? date('Y-m-d', strtotime($user->dob)) : '',
                'gender'       => 'UNKNOWN',
                'occupation'   => 'OTHER',
                'id_type'      => $idType,
                'id_number'    => $idNumber,
            ];
        }

        // Agent-initiated: use the sender JSON object stored at booking time
        $s = $sendMoney->sender;
        return [
            'fullname'     => $this->str($s, 'name'),
            'mobile'       => $this->str($s, 'mobile'),
            'country'      => '',
            'address_line' => $this->str($s, 'address'),
            'city'         => '',
            'zip'          => '',
            'dob'          => '',
            'gender'       => 'UNKNOWN',
            'occupation'   => 'OTHER',
            'id_type'      => 'OTHER',
            'id_number'    => '',
        ];
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function baseUrl(): string
    {
        return $this->settings->isLive()
            ? 'https://api.pawapay.io'
            : 'https://api.sandbox.pawapay.io';
    }

    private function normalizePhone(string $phone): string
    {
        // Strip any leading + — pawaPay expects plain digits (E.164 without the plus)
        return ltrim(trim($phone), '+');
    }

    /** Split "First Last Names" into [first, last]. */
    private function splitName(string $name): array
    {
        $parts = array_filter(explode(' ', trim($name)));
        if (count($parts) < 2) {
            return [trim($name), trim($name)];
        }
        $first = array_shift($parts);
        return [$first, implode(' ', $parts)];
    }

    /** Safe string getter from array or object. */
    private function str(mixed $data, string $key): string
    {
        if (is_array($data)) {
            return (string) ($data[$key] ?? '');
        }
        return (string) ($data?->$key ?? '');
    }

    private function mapPurpose(?string $name): string
    {
        if (!$name) return 'OTHER';
        $n = strtolower($name);
        return match (true) {
            str_contains($n, 'family') || str_contains($n, 'support')           => 'FAMILY_SUPPORT',
            str_contains($n, 'education') || str_contains($n, 'school')         => 'EDUCATION_SUPPORT',
            str_contains($n, 'business') || str_contains($n, 'trade')           => 'BUSINESS_PAYMENT',
            str_contains($n, 'medical') || str_contains($n, 'health')           => 'MEDICAL_TREATMENT',
            str_contains($n, 'loan') || str_contains($n, 'repay')               => 'LOAN_REPAYMENT',
            str_contains($n, 'saving')                                           => 'SAVINGS',
            str_contains($n, 'property') || str_contains($n, 'rent')            => 'PROPERTY_PURCHASE',
            str_contains($n, 'gift') || str_contains($n, 'donation')            => 'GIFT',
            default                                                              => 'OTHER',
        };
    }

    private function mapSourceOfFunds(?string $name): string
    {
        if (!$name) return 'OTHER';
        $n = strtolower($name);
        return match (true) {
            str_contains($n, 'salary') || str_contains($n, 'wage') || str_contains($n, 'employment') => 'SALARY',
            str_contains($n, 'business') || str_contains($n, 'self')                                  => 'BUSINESS_INCOME',
            str_contains($n, 'saving')                                                                 => 'SAVINGS',
            str_contains($n, 'invest') || str_contains($n, 'dividend')                                => 'INVESTMENT',
            str_contains($n, 'pension') || str_contains($n, 'retirement')                             => 'PENSION',
            str_contains($n, 'rental') || str_contains($n, 'property')                                => 'RENTAL_INCOME',
            default                                                                                    => 'OTHER',
        };
    }
}
