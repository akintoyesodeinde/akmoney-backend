<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayoutApiCountryMethod extends Model
{
    protected $fillable = ['payout_api_country_id', 'method_code', 'method_name', 'is_active', 'pawapay_provider'];

    protected $casts = ['is_active' => 'boolean'];

    public function country()
    {
        return $this->belongsTo(PayoutApiCountry::class, 'payout_api_country_id');
    }
}
