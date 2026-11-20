<?php

namespace App\Http\Controllers\Gateway\Apaylo;

use App\Lib\CurlRequest;

class ApayloClient
{
    private string $baseUrl;
    private string $apiKey;
    private string $secretKey;

    // env2 is the standard transactional API environment
    private const PRODUCTION_URL = 'https://api.etransfer.apaylo.com/api.apaylo.com/api';
    private const SANDBOX_URL    = 'https://sandboxapi.apaylo.com/sandboxapi.apaylo.com/api';

    public function __construct(string $apiKey, string $secretKey, string $environment = 'sandbox')
    {
        $this->apiKey     = $apiKey;
        $this->secretKey  = $secretKey;
        $this->baseUrl    = $environment === 'production' ? self::PRODUCTION_URL : self::SANDBOX_URL;
    }

    /**
     * Apaylo signature: base64(SHA-512(apiKey + secretKey + YYYY-MM-DD))
     * Must be recomputed per request because it includes today's UTC date.
     */
    private function generateSignature(): string
    {
        $date = gmdate('Y-m-d');
        return base64_encode(hash('sha512', $this->apiKey . $this->secretKey . $date, true));
    }

    /**
     * Send an Interac e-Transfer request by email to the customer.
     * Endpoint: POST Merchant/RequestInteracEtransferEmail
     */
    public function requestInteracEtransfer(string $customerEmail, string $customerName, float $amount, string $description): object
    {
        $response = $this->post('Merchant/RequestInteracEtransferEmail', [
            'CustomerEmail' => $customerEmail,
            'CustomerName'  => $customerName,
            'Amount'        => number_format($amount, 2, '.', ''),
            'Description'   => $description,
        ]);

        return $response;
    }

    /**
     * Search for Interac request e-Transfers in a date range.
     * Endpoint: POST Merchant/SearchRequestInteracEtransfer
     */
    public function searchRequestTransfers(string $startDate, string $endDate, ?string $transactionNumber = null): object
    {
        $data = [
            'StartDate' => $startDate,
            'EndDate'   => $endDate,
        ];

        if ($transactionNumber) {
            $data['TransactionNumber'] = $transactionNumber;
        }

        return $this->post('Merchant/SearchRequestInteracEtransfer', $data);
    }

    /**
     * Get merchant wallet balance.
     * Endpoint: GET Merchant/GetBalance
     */
    public function getBalance(): object
    {
        return $this->get('Merchant/GetBalance');
    }

    /**
     * Health check.
     * Endpoint: GET Merchant/HealthCheck (env1, no auth required)
     */
    public function healthCheck(): object
    {
        return $this->get('Merchant/HealthCheck');
    }

    // -------------------------------------------------------------------------

    private function headers(): array
    {
        return [
            'Key: ' . $this->apiKey,
            'Signature: ' . $this->generateSignature(),
        ];
    }

    private function post(string $endpoint, array $data): object
    {
        $url      = rtrim($this->baseUrl, '/') . '/' . ltrim($endpoint, '/');
        $body    = json_encode($data);
        $headers = array_merge($this->headers(), ['Content-Type: application/json']);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $raw     = curl_exec($ch);
        curl_close($ch);

        $decoded = json_decode($raw);

        if (!$decoded) {
            logger()->error('Apaylo raw response: ' . $raw);
            return (object) ['IsError' => true, 'Message' => 'Invalid response from Apaylo API', 'raw' => $raw];
        }

        return $decoded;
    }

    private function get(string $endpoint): object
    {
        $url     = rtrim($this->baseUrl, '/') . '/' . ltrim($endpoint, '/');
        $raw     = CurlRequest::curlContent($url, $this->headers());
        $decoded = json_decode($raw);

        if (!$decoded) {
            return (object) ['IsError' => true, 'Message' => 'Invalid response from Apaylo API', 'raw' => $raw];
        }

        return $decoded;
    }
}
