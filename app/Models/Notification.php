<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'model_type',
        'model_id',
        'action',
        'title',
        'message',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];
}
