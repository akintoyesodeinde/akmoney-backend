<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SanctionsScreeningResult extends Model
{
    protected $fillable = [
        'user_id', 'screening_type', 'status',
        'provider', 'match_data', 'screened_at',
    ];

    protected $casts = [
        'match_data'  => 'array',
        'screened_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeHits($query)
    {
        return $query->where('status', 'hit');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('screening_type', $type);
    }
}
