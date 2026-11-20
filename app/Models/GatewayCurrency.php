<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Model;

class GatewayCurrency extends Model
{

    protected $hidden = [
        'gateway_parameter'
    ];

    protected $casts = ['status' => 'boolean'];

    // Relation
    public function method()
    {
        return $this->belongsTo(Gateway::class, 'method_code', 'code');
    }

    public function currencyIdentifier()
    {
        return $this->name ?? $this->method->name . ' ' . $this->currency;
    }

    public function scopeBaseCurrency()
    {
        return $this->method->crypto == Status::ENABLE ? 'CAD' : $this->currency;
    }

    public function scopeBaseSymbol()
    {
        return $this->method->crypto == Status::ENABLE ? 'CA$' : $this->symbol;
    }

    /**
     * Sync the rate for all gateway currency rows matching the given currency code.
     * Called whenever a Country rate is updated (manual edit or API sync).
     */
    public static function syncRate(string $currency, float $rate): void
    {
        if ($rate <= 0) return;
        static::where('currency', strtoupper($currency))->update(['rate' => $rate]);
    }
}
