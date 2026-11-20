<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackupCode extends Model
{
    protected $fillable = ['user_id', 'code', 'used_at'];

    protected $casts = ['used_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnused($query)
    {
        return $query->whereNull('used_at');
    }
}
