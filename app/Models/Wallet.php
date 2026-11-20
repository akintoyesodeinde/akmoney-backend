<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $guarded = [];

    // Status constants
    const STATUS_ACTIVE    = 1;
    const STATUS_SUSPENDED = 0;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exchanges()
    {
        return $this->hasMany(WalletExchange::class, 'from_wallet_id')
                    ->orWhere('to_wallet_id', $this->id);
    }

    public function transactions()
    {
        return $this->hasMany(\App\Models\Transaction::class);
    }

    public function deposits()
    {
        return $this->hasMany(\App\Models\Deposit::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeRemittanceEnabled($query)
    {
        return $query->where('remittance_enabled', true);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
