<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletExchange extends Model
{
    protected $guarded = [];

    const STATUS_COMPLETED = 1;
    const STATUS_PENDING   = 2;
    const STATUS_FAILED    = 3;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fromWallet()
    {
        return $this->belongsTo(Wallet::class, 'from_wallet_id');
    }

    public function toWallet()
    {
        return $this->belongsTo(Wallet::class, 'to_wallet_id');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
