<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecaptchaLog extends Model
{
    protected $fillable = [
        'ip',
        'url',
        'action',
        'score',
        'success',
        'error_codes',
        'user_agent',
    ];

    protected $casts = [
        'success' => 'boolean',
        'score'   => 'float',
    ];

    public function scopeSuccessful($query)
    {
        return $query->where('success', true);
    }

    public function scopeFailed($query)
    {
        return $query->where('success', false);
    }

    public function scopeBot($query)
    {
        // Passed verification but score is suspiciously low (0–0.49)
        return $query->where('success', true)->whereNotNull('score')->where('score', '<', 0.5);
    }
}
