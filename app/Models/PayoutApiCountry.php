<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayoutApiCountry extends Model
{
    const PROVIDER_MANUAL  = 'manual';
    const PROVIDER_PAWAPAY = 'pawapay';
    const PROVIDER_PAGA    = 'paga';

    /** All known auto-payout providers in priority display order. */
    const PROVIDERS = [
        self::PROVIDER_MANUAL  => 'Manual',
        self::PROVIDER_PAWAPAY => 'PawaPay',
        self::PROVIDER_PAGA    => 'Paga',
    ];

    protected $fillable = ['country_code', 'country_name', 'currency_code', 'is_active', 'payout_provider'];

    protected $casts = ['is_active' => 'boolean'];

    // ── Relations ──────────────────────────────────────────────────────────

    public function methods()
    {
        return $this->hasMany(PayoutApiCountryMethod::class);
    }

    public function activeMethods()
    {
        return $this->hasMany(PayoutApiCountryMethod::class)->where('is_active', true);
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    public function isAuto(): bool
    {
        return $this->payout_provider !== self::PROVIDER_MANUAL;
    }

    public function providerLabel(): string
    {
        return self::PROVIDERS[$this->payout_provider] ?? 'Manual';
    }

    // ── Queries ────────────────────────────────────────────────────────────

    /**
     * Find the active auto-payout entry for a given currency code.
     * Used by ProcessSendMoney to determine which service to call.
     */
    public static function autoForCurrency(string $currency): ?self
    {
        return static::where('currency_code', strtoupper($currency))
            ->where('payout_provider', '!=', self::PROVIDER_MANUAL)
            ->where('is_active', true)
            ->first();
    }

    /** @deprecated Use autoForCurrency() — kept for backward compatibility */
    public static function activeForCountryCode(string $code): ?self
    {
        return static::where('country_code', strtoupper($code))
            ->where('is_active', true)
            ->first();
    }
}
