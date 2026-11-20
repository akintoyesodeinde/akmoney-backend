<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AmlRiskScore extends Model
{
    protected $fillable = [
        'user_id', 'transaction_id', 'transaction_type',
        'risk_score', 'risk_level', 'risk_factors',
        'is_reviewed', 'reviewed_by', 'reviewed_at', 'notes',
    ];

    protected $casts = [
        'risk_factors' => 'array',
        'reviewed_at'  => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(Admin::class, 'reviewed_by');
    }

    public function scopeHigh($query)
    {
        return $query->whereIn('risk_level', ['high', 'critical']);
    }

    public function scopeUnreviewed($query)
    {
        return $query->where('is_reviewed', 0);
    }

    public function getRiskLevelBadgeAttribute(): string
    {
        return match ($this->risk_level) {
            'critical' => '<span class="badge badge--danger">Critical</span>',
            'high'     => '<span class="badge badge--warning">High</span>',
            'medium'   => '<span class="badge badge--info">Medium</span>',
            default    => '<span class="badge badge--success">Low</span>',
        };
    }
}
