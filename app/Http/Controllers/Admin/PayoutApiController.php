<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PagaApiSetting;
use App\Models\PayoutApiCountry;
use App\Models\PayoutApiCountryMethod;
use App\Models\PayoutApiLog;
use App\Models\PayoutApiSetting;
use App\Models\SendMoney;
use App\Services\PawaPayPayoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class PayoutApiController extends Controller
{
    // ── Combined Settings ────────────────────────────────────────────────────

    public function settings()
    {
        $pageTitle  = 'Payout — API Settings';
        $pawapay    = PayoutApiSetting::instance();
        $paga       = PagaApiSetting::instance();
        return view('admin.payout_api.settings', compact('pageTitle', 'pawapay', 'paga'));
    }

    public function saveSettings(Request $request)
    {
        $request->validate([
            // PawaPay
            'pawapay_secret_key'     => 'nullable|string',
            'pawapay_webhook_secret' => 'nullable|string',
            'pawapay_environment'    => 'required|in:sandbox,live',
            // Paga
            'paga_principal'         => 'nullable|string',
            'paga_credentials'       => 'nullable|string',
            'paga_api_key'           => 'nullable|string',
            'paga_environment'       => 'required|in:sandbox,live',
        ]);

        // PawaPay credentials
        $pawapay = PayoutApiSetting::instance();
        if ($request->filled('pawapay_secret_key')) {
            $pawapay->secret_key = $request->pawapay_secret_key;
        }
        $pawapay->webhook_secret = $request->pawapay_webhook_secret;
        $pawapay->environment    = $request->pawapay_environment;
        $pawapay->save();

        // Paga credentials
        $paga = PagaApiSetting::instance();
        if ($request->filled('paga_principal'))   $paga->principal   = $request->paga_principal;
        if ($request->filled('paga_credentials')) $paga->credentials = $request->paga_credentials;
        if ($request->filled('paga_api_key'))     $paga->api_key     = $request->paga_api_key;
        $paga->environment = $request->paga_environment;
        $paga->save();

        $notify[] = ['success', 'Payout API settings saved'];
        return back()->withNotify($notify);
    }

    // ── Countries ─────────────────────────────────────────────────────────────

    public function countries()
    {
        $pageTitle = 'Payout — Countries';

        $search = request('search');
        $payoutCountries = PayoutApiCountry::with('methods')
            ->when($search, fn($q) => $q->where('country_name', 'like', "%{$search}%")
                ->orWhere('currency_code', 'like', "%{$search}%")
                ->orWhere('country_code', 'like', "%{$search}%"))
            ->orderBy('country_name')
            ->paginate(getPaginate())
            ->withQueryString();

        $providers = PayoutApiCountry::PROVIDERS;

        return view('admin.payout_api.countries', compact('pageTitle', 'payoutCountries', 'providers'));
    }

    /**
     * Save the chosen payout provider for a country (by payout_api_countries.id).
     */
    public function saveCountryProvider(Request $request, PayoutApiCountry $payoutCountry)
    {
        $request->validate([
            'payout_provider' => 'required|in:manual,pawapay,paga',
        ]);

        $provider = $request->payout_provider;
        $payoutCountry->payout_provider = $provider;
        $payoutCountry->is_active       = ($provider !== 'manual');
        $payoutCountry->save();

        return response()->json([
            'status'   => 'success',
            'provider' => $provider,
            'label'    => PayoutApiCountry::PROVIDERS[$provider] ?? $provider,
            'message'  => $payoutCountry->country_name . ' payout set to ' . (PayoutApiCountry::PROVIDERS[$provider] ?? $provider),
        ]);
    }

    // ── Legacy toggle (kept for backward compat) ──────────────────────────────

    public function toggleCountry(PayoutApiCountry $country)
    {
        $country->is_active      = !$country->is_active;
        $country->payout_provider = $country->is_active ? PayoutApiCountry::PROVIDER_PAWAPAY : PayoutApiCountry::PROVIDER_MANUAL;
        $country->save();

        return response()->json([
            'status'    => 'success',
            'is_active' => $country->is_active,
            'message'   => $country->country_name . ' payout set to ' . $country->providerLabel(),
        ]);
    }

    public function toggleMethod(PayoutApiCountryMethod $method)
    {
        $method->is_active = !$method->is_active;
        $method->save();

        return response()->json([
            'status'    => 'success',
            'is_active' => $method->is_active,
            'message'   => $method->method_name . ' ' . ($method->is_active ? 'enabled' : 'disabled'),
        ]);
    }

    public function saveMethodProvider(Request $request, PayoutApiCountryMethod $method)
    {
        $request->validate([
            'pawapay_provider' => 'nullable|string|max:100',
        ]);

        $method->pawapay_provider = $request->pawapay_provider;
        $method->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Provider code saved',
        ]);
    }

    // ── Sync PawaPay active-conf ─────────────────────────────────────────────

    public function syncActiveConf()
    {
        try {
            Artisan::call('pawapay:sync-active-conf');
            $notify[] = ['success', 'PawaPay active-conf synced successfully'];
        } catch (\Throwable $e) {
            $notify[] = ['error', 'Sync failed: ' . $e->getMessage()];
        }

        return back()->withNotify($notify);
    }

    // ── Logs ──────────────────────────────────────────────────────────────────

    public function logs(Request $request)
    {
        $pageTitle = 'Payout — Logs';
        $logs = PayoutApiLog::with('sendMoney:id,trx,mtcn_number')
            ->when($request->search, fn($q) => $q->whereHas('sendMoney',
                fn($sq) => $sq->where('trx', $request->search)->orWhere('mtcn_number', $request->search)
            ))
            ->latest()
            ->paginate(getPaginate());

        return view('admin.payout_api.logs', compact('pageTitle', 'logs'));
    }

    // ── Manual retry ─────────────────────────────────────────────────────────

    public function retry(SendMoney $sendMoney)
    {
        if ($sendMoney->payout_channel !== 'api') {
            $notify[] = ['error', 'This transaction has no auto-payout record'];
            return back()->withNotify($notify);
        }

        // Determine which service to use based on the country's current provider
        $payoutCountry = PayoutApiCountry::autoForCurrency($sendMoney->recipient_currency);
        $success = false;

        if ($payoutCountry) {
            $success = match ($payoutCountry->payout_provider) {
                PayoutApiCountry::PROVIDER_PAWAPAY => (new PawaPayPayoutService())->initiatePayout($sendMoney),
                PayoutApiCountry::PROVIDER_PAGA    => (new \App\Services\PagaPayoutService())->initiatePayout($sendMoney),
                default => false,
            };
        }

        $notify[] = $success
            ? ['success', 'Payout re-initiated']
            : ['error', 'Payout re-initiation failed — check the logs'];

        return back()->withNotify($notify);
    }

    // ── Resend callback ───────────────────────────────────────────────────────

    public function resendCallback(SendMoney $sendMoney)
    {
        if (!$sendMoney->api_payout_id) {
            $notify[] = ['error', 'No payout ID on this transaction'];
            return back()->withNotify($notify);
        }

        $success = (new PawaPayPayoutService())->resendCallback($sendMoney->api_payout_id);

        $notify[] = $success
            ? ['success', 'Callback resend requested']
            : ['error', 'Callback resend failed'];

        return back()->withNotify($notify);
    }

    // ── Force-fail enqueued (sandbox only) ───────────────────────────────────

    public function failEnqueued(SendMoney $sendMoney)
    {
        if (PayoutApiSetting::instance()->isLive()) {
            $notify[] = ['error', 'Only available in sandbox mode'];
            return back()->withNotify($notify);
        }

        if (!$sendMoney->api_payout_id) {
            $notify[] = ['error', 'No payout ID on this transaction'];
            return back()->withNotify($notify);
        }

        $success = (new PawaPayPayoutService())->failEnqueued($sendMoney->api_payout_id);

        $notify[] = $success
            ? ['success', 'Remittance force-failed (sandbox)']
            : ['error', 'Force-fail failed'];

        return back()->withNotify($notify);
    }
}
