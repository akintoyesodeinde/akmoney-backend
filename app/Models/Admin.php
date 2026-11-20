<?php

namespace App\Models;

use App\Constants\AdminRole;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function rolePermissions()
    {
        return $this->hasMany(AdminRolePermission::class, 'role', 'role');
    }

    // ─── Role helpers ─────────────────────────────────────────────────────────

    public function isSuperAdmin(): bool
    {
        return $this->role === AdminRole::SUPER_ADMIN;
    }

    public function isActive(): bool
    {
        return $this->status == 1;
    }

    public function getRoleLabelAttribute(): string
    {
        return AdminRole::LABELS[$this->role] ?? ucfirst(str_replace('_', ' ', $this->role));
    }

    public function getFullnameAttribute(): string
    {
        return $this->name;
    }

    // ─── Permission helpers ───────────────────────────────────────────────────

    public function hasPermission(string $ability): bool
    {
        // Super admin bypasses all permission checks
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Parse "module.action" format
        [$module, $action] = array_pad(explode('.', $ability, 2), 2, 'view');

        $permission = AdminRolePermission::where('role', $this->role)
            ->where('module', $module)
            ->first();

        if (!$permission) {
            return false;
        }

        return match ($action) {
            'view'    => (bool) $permission->can_view,
            'create'  => (bool) $permission->can_create,
            'edit'    => (bool) $permission->can_edit,
            'delete'  => (bool) $permission->can_delete,
            'approve' => (bool) $permission->can_approve,
            default   => false,
        };
    }

    public function hasModuleAccess(string $module): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return AdminRolePermission::where('role', $this->role)
            ->where('module', $module)
            ->where('can_view', true)
            ->exists();
    }
}
