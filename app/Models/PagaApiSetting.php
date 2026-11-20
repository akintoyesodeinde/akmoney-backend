<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagaApiSetting extends Model
{
    protected $fillable = ['principal', 'credentials', 'api_key', 'environment'];

    protected $hidden = ['credentials', 'api_key'];

    public static function instance(): self
    {
        return static::firstOrCreate([], [
            'principal'   => null,
            'credentials' => null,
            'api_key'     => null,
            'environment' => 'sandbox',
        ]);
    }

    public function isLive(): bool
    {
        return $this->environment === 'live';
    }

    public function baseUrl(): string
    {
        return $this->isLive()
            ? 'https://www.mypaga.com'
            : 'https://beta.mypaga.com';
    }
}
