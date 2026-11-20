<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayoutApiLog extends Model
{
    protected $fillable = ['send_money_id', 'event', 'payload', 'response'];

    protected $casts = [
        'payload'  => 'array',
        'response' => 'array',
    ];

    public function sendMoney()
    {
        return $this->belongsTo(SendMoney::class);
    }

    public static function record(int $sendMoneyId, string $event, array $payload = [], array $response = []): void
    {
        static::create([
            'send_money_id' => $sendMoneyId,
            'event'         => $event,
            'payload'       => $payload,
            'response'      => $response,
        ]);
    }
}
