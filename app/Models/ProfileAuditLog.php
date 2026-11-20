<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileAuditLog extends Model
{
    protected $fillable = [
        'user_id', 'action', 'field', 'old_value', 'new_value', 'ip_address', 'user_agent',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function record(int $userId, string $action, ?string $field = null, $oldValue = null, $newValue = null): void
    {
        static::create([
            'user_id'    => $userId,
            'action'     => $action,
            'field'      => $field,
            'old_value'  => $oldValue,
            'new_value'  => $newValue,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
