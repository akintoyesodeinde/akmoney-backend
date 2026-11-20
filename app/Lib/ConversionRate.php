<?php

namespace App\Lib;

use App\Models\Country;
use App\Models\CurrencyConversionRate;
use App\Models\GatewayCurrency;
use Exception;

class ConversionRate
{

    private $conversionConfig;
    private $provider;
    private $countries;
    public $exceptions = false;
    public function __construct()
    {
        $gs                     = gs();
        $conversionConfig       = $gs->conversion_rate_api;
        $this->provider         = @$conversionConfig?->provider;
        $this->conversionConfig = $conversionConfig;
        $this->countries = Country::active()->get();
    }
    function synchronizeAll()
    {
        $conversionConfig = $this->conversionConfig;
        if (!@$conversionConfig->status) return;
        $baseCountry = $this->countries->firstWhere('currency', 'CAD');
        if (!$baseCountry) return;
        $this->sync($baseCountry);
    }

    function sync($country)
    {
        // Keep the CAD base country rate at 1 so JS conversions work correctly
        $cadCountry = $this->countries->firstWhere('currency', 'CAD');
        if ($cadCountry && $cadCountry->rate != 1) {
            $cadCountry->rate = 1;
            $cadCountry->save();
        }
        $this->{$this->provider}($country);
    }

    function exchangerate($fromCountry)
    {
        if (!$fromCountry) return;
        $baseCurrency = strtoupper($fromCountry->currency);
        $apiKey       = @$this->conversionConfig?->exchangerate?->api_key;
        $url          = "https://v6.exchangerate-api.com/v6/$apiKey/latest/$baseCurrency";
        $response     = CurlRequest::curlContent($url);
        $response     = json_decode($response ?? '[]');
        if (!@$response->conversion_rates) {
            if ($this->exceptions) {
                $notification = 'Wrong API credentials or currency code. Please verify.';
                if (@$response?->result == "error" && @$response?->{"error-type"}) {
                    $notification =  'API ERROR: ' . keyToTitle(@$response?->{"error-type"});
                }
                throw new Exception($notification);
            }
            return;
        }
        $rates = @$response?->conversion_rates;

        $countries = $this->countries;
        foreach (@$countries?->where('id', "!=", $fromCountry->id) ?? [] as $toCountry) {
            $conversion      = CurrencyConversionRate::where('from_country', $fromCountry->id)->where('to_country', $toCountry->id)->first();
            if (!$conversion) {
                $conversion = new CurrencyConversionRate();
                $conversion->from_country = $fromCountry->id;
                $conversion->to_country = $toCountry->id;
            }
            $toCurrency          = strtoupper($toCountry->currency);
            $adjustedRate        = $this->adjustRates(@$rates?->{$toCurrency});
            $toCountry->rate     = $adjustedRate;
            $conversion->rate    = $adjustedRate;
            $toCountry->save();
            $conversion->save();
            GatewayCurrency::syncRate($toCountry->currency, (float) $adjustedRate);
        }

        $this->syncUncoveredGatewayCurrencies($rates, fn($r, $c) => $r?->{$c} ?? null);
    }
    function currencyapi($fromCountry)
    {
        if (!$fromCountry) return;
        $baseCurrency = strtoupper($fromCountry->currency);

        $apiKey       = @$this->conversionConfig?->currencyapi?->apikey;
        $url          = "https://api.currencyapi.com/v3/latest?apikey=$apiKey&base_currency=$baseCurrency";
        $response     = CurlRequest::curlContent($url);
        $response     = json_decode($response ?? '[]');
        if (!@$response->data) {
            if ($this->exceptions) {
                $notification = 'Wrong API credentials or currency code. Please verify.';
                if (@$response?->errors && @$response?->message) {
                    $notification =  'API ERROR: ' . @$response?->message;
                }
                throw new Exception($notification);
            }
            return;
        }
        $rates = @$response?->data;

        $countries = $this->countries;
        foreach (@$countries?->where('id', "!=", $fromCountry->id) ?? [] as $toCountry) {
            $conversion      = CurrencyConversionRate::where('from_country', $fromCountry->id)->where('to_country', $toCountry->id)->first();
            if (!$conversion) {
                $conversion               = new CurrencyConversionRate();
                $conversion->from_country = $fromCountry->id;
                $conversion->to_country   = $toCountry->id;
            }
            $toCurrency          = strtoupper($toCountry->currency);
            $adjustedRate        = $this->adjustRates(@$rates?->{$toCurrency}->value);
            $toCountry->rate     = $adjustedRate;
            $conversion->rate    = $adjustedRate;
            $toCountry->save();
            $conversion->save();
            GatewayCurrency::syncRate($toCountry->currency, (float) $adjustedRate);
        }

        $this->syncUncoveredGatewayCurrencies($rates, fn($r, $c) => $r?->{$c}->value ?? null);
    }
    function openexchangerates($fromCountry)
    {
        if (!$fromCountry) return;
        $baseCurrency = strtoupper($fromCountry->currency);
        $appId        = @$this->conversionConfig?->openexchangerates?->app_id;
        $url          = "https://openexchangerates.org/api/latest.json?app_id=$appId&base=$baseCurrency";
        $response     = CurlRequest::curlContent($url);
        $response     = json_decode($response ?? '[]');
        if (!@$response?->rates) {
            if ($this->exceptions) {
                $notification = 'Wrong API credentials or currency code. Please verify.';
                if (@$response?->error && @$response?->description) {
                    $notification = @$response?->description;
                }
                throw new Exception($notification);
            }
            return;
        }
        $rates = @$response?->rates;

        $countries = $this->countries;
        foreach (@$countries?->where('id', "!=", $fromCountry->id) ?? [] as $toCountry) {
            $conversion      = CurrencyConversionRate::where('from_country', $fromCountry->id)->where('to_country', $toCountry->id)->first();
            if (!$conversion) {
                $conversion = new CurrencyConversionRate();
                $conversion->from_country = $fromCountry->id;
                $conversion->to_country = $toCountry->id;
            }
            $toCurrency          = strtoupper($toCountry->currency);
            $adjustedRate        = $this->adjustRates(@$rates?->{$toCurrency});
            $toCountry->rate     = $adjustedRate;
            $conversion->rate    = $adjustedRate;
            $toCountry->save();
            $conversion->save();
            GatewayCurrency::syncRate($toCountry->currency, (float) $adjustedRate);
        }

        $this->syncUncoveredGatewayCurrencies($rates, fn($r, $c) => $r?->{$c} ?? null);
    }

    function frankfurter($fromCountry)
    {
        if (!$fromCountry) return;
        $baseCurrency = strtoupper($fromCountry->currency);
        $url          = "https://api.frankfurter.app/latest?from=$baseCurrency";
        $response     = CurlRequest::curlContent($url);
        $response     = json_decode($response ?? '[]');
        if (!@$response?->rates) {
            if ($this->exceptions) {
                $notification = @$response?->message ?? 'Could not fetch rates from Frankfurter. Check that the base currency is supported.';
                throw new Exception($notification);
            }
            return;
        }
        $rates = @$response?->rates;

        $countries = $this->countries;
        foreach (@$countries?->where('id', "!=", $fromCountry->id) ?? [] as $toCountry) {
            $conversion = CurrencyConversionRate::where('from_country', $fromCountry->id)->where('to_country', $toCountry->id)->first();
            if (!$conversion) {
                $conversion               = new CurrencyConversionRate();
                $conversion->from_country = $fromCountry->id;
                $conversion->to_country   = $toCountry->id;
            }
            $toCurrency          = strtoupper($toCountry->currency);
            $adjustedRate        = $this->adjustRates(@$rates?->{$toCurrency});
            $toCountry->rate     = $adjustedRate;
            $conversion->rate    = $adjustedRate;
            $toCountry->save();
            $conversion->save();
            GatewayCurrency::syncRate($toCountry->currency, (float) $adjustedRate);
        }

        $this->syncUncoveredGatewayCurrencies($rates, fn($r, $c) => $r?->{$c} ?? null);
    }
    function adjustRates($rate)
    {
        $conversionConfig = $this->conversionConfig;
        $fixedCharge      = floatval(@$conversionConfig?->fixed_adjustment ?? 0);
        $percentRate      = floatval(@$conversionConfig?->percent_adjustment ?? 0);
        return $rate + $fixedCharge + $percentRate * $rate / 100;
    }

    /**
     * After the main active-country sync loop, also update gateway currencies
     * whose currency has no active Country row (e.g. KES, ZMW, TZS…).
     *
     * @param object   $apiRates   The raw rates object from the API response
     * @param callable $extract    fn($apiRates, $currency) → raw float rate
     */
    private function syncUncoveredGatewayCurrencies(object $apiRates, callable $extract): void
    {
        $activeCurrencies = $this->countries->pluck('currency')->map('strtoupper')->unique();

        $uncovered = GatewayCurrency::select('currency')
            ->distinct()
            ->get()
            ->pluck('currency')
            ->map('strtoupper')
            ->diff($activeCurrencies);

        foreach ($uncovered as $currency) {
            $raw = $extract($apiRates, $currency);
            if ($raw === null || $raw === false) continue;
            $adjusted = $this->adjustRates((float) $raw);
            if ($adjusted > 0) {
                GatewayCurrency::syncRate($currency, $adjusted);
            }
        }
    }

    public static function providers()
    {
        $providers = [];
        $providers['openexchangerates'] = [
            "name" => "Open Exchange Rates",
            "url"  => "https://openexchangerates.org/"
        ];
        $providers['currencyapi'] = [
            "name" => "Currency Api",
            "url"  => "https://app.currencyapi.com"
        ];
        $providers['exchangerate'] = [
            "name" => "Exchange Rate Api",
            "url"  => "https://www.exchangerate-api.com/"
        ];
        $providers['frankfurter'] = [
            "name" => "Frankfurter (Free – No API Key)",
            "url"  => "https://www.frankfurter.app/"
        ];
        return $providers;
    }
}
