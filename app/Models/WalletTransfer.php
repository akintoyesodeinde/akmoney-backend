<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransfer extends Model
{
    protected $guarded = [];

    const STATUS_COMPLETED = 1;

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function senderWallet()
    {
        return $this->belongsTo(Wallet::class, 'sender_wallet_id');
    }

    public function receiverWallet()
    {
        return $this->belongsTo(Wallet::class, 'receiver_wallet_id');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('sender_id', $userId)->orWhere('receiver_id', $userId);
    }

    public function scopeForSender($query, $userId)
    {
        return $query->where('sender_id', $userId);
    }

    public function scopeForReceiver($query, $userId)
    {
        return $query->where('receiver_id', $userId);
    }
}
