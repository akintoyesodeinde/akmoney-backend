<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayoutApiSetting extends Model
{
    protected $fillable = [
        'secret_key',
        'public_key',
        'encryption_key',
        'environment',
        'webhook_secret',
    ];

    protected $hidden = ['secret_key', 'encryption_key', 'webhook_secret'];

    public static function instance(): self
    {
        return static::firstOrCreate([], [
            'environment' => 'sandbox',
        ]);
    }

    public function isLive(): bool
    {
        return $this->environment === 'live';
    }
}
