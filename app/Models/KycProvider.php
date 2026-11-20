<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KycProvider extends Model
{
    use HasFactory;

    protected $table = 'kyc_providers';

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'slug',
        'name',
        'is_enabled',
        'api_key',
        'credential_schema',
        'logo',
        'help_text',
    ];

    /**
     * Casts
     */
    protected $casts = [
        'is_enabled'        => 'boolean',
        'credentials'       => 'array',
        'credential_schema' => 'array',
    ];

    /**
     * Scope: enabled providers
     */
    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    /**
     * Get a credential value safely
     */
    public function getCredential(string $key, $default = null)
    {
        return $this->credentials[$key] ?? $default;
    }

    /**
     * Check if provider requires a credential
     */
    public function requiresCredential(string $key): bool
    {
        if (empty($this->credential_schema)) {
            return false;
        }

        return collect($this->credential_schema)
            ->pluck('key')
            ->contains($key);
    }

    /**
     * Optional: only one provider active at a time
     */
    protected static function booted()
    {
        static::saving(function ($provider) {
            if ($provider->is_enabled) {
                static::where('id', '!=', $provider->id)
                    ->update(['is_enabled' => false]);
            }
        });
    }
}
