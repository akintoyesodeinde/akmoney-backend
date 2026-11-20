<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminRolePermission extends Model
{
    protected $fillable = [
        'role',
        'module',
        'can_view',
        'can_create',
        'can_edit',
        'can_delete',
        'can_approve',
    ];

    protected $casts = [
        'can_view'    => 'boolean',
        'can_create'  => 'boolean',
        'can_edit'    => 'boolean',
        'can_delete'  => 'boolean',
        'can_approve' => 'boolean',
    ];

    public static function permissionsForRole(string $role): \Illuminate\Support\Collection
    {
        return static::where('role', $role)->get()->keyBy('module');
    }
}
