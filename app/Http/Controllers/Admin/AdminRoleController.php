<?php

namespace App\Http\Controllers\Admin;

use App\Constants\AdminRole;
use App\Http\Controllers\Controller;
use App\Models\AdminRolePermission;
use Illuminate\Http\Request;

class AdminRoleController extends Controller
{
    public function index()
    {
        $pageTitle = 'Role Permissions';
        $roles     = AdminRole::LABELS;
        $modules   = AdminRole::MODULES;

        // Load all permissions grouped by role
        $permissions = AdminRolePermission::whereIn('role', array_keys($roles))
            ->get()
            ->groupBy('role')
            ->map(fn($items) => $items->keyBy('module'));

        return view('admin.roles.index', compact('pageTitle', 'roles', 'modules', 'permissions'));
    }

    public function edit(string $role)
    {
        abort_if(!array_key_exists($role, AdminRole::LABELS), 404);
        abort_if($role === AdminRole::SUPER_ADMIN, 403, 'Super Admin permissions cannot be edited.');

        $pageTitle   = 'Edit Permissions — ' . AdminRole::LABELS[$role];
        $modules     = AdminRole::MODULES;
        $permissions = AdminRolePermission::where('role', $role)
            ->get()
            ->keyBy('module');

        return view('admin.roles.edit', compact('pageTitle', 'role', 'modules', 'permissions'));
    }

    public function update(Request $request, string $role)
    {
        abort_if(!array_key_exists($role, AdminRole::LABELS), 404);
        abort_if($role === AdminRole::SUPER_ADMIN, 403);

        $modules = array_keys(AdminRole::MODULES);

        try {
            foreach ($modules as $module) {
                AdminRolePermission::updateOrCreate(
                    ['role' => $role, 'module' => $module],
                    [
                        'can_view'    => (bool) $request->boolean("permissions.{$module}.view"),
                        'can_create'  => (bool) $request->boolean("permissions.{$module}.create"),
                        'can_edit'    => (bool) $request->boolean("permissions.{$module}.edit"),
                        'can_delete'  => (bool) $request->boolean("permissions.{$module}.delete"),
                        'can_approve' => (bool) $request->boolean("permissions.{$module}.approve"),
                    ]
                );
            }
        } catch (\Exception $e) {
            logger()->error('Permission update failed: ' . $e->getMessage(), ['exception' => $e]);
            $notify[] = ['error', 'Failed to update permissions. Please try again.'];
            return back()->withNotify($notify);
        }

        $notify[] = ['success', 'Permissions for ' . AdminRole::LABELS[$role] . ' updated successfully.'];
        return to_route('admin.roles.index')->withNotify($notify);
    }

    public function reset(string $role)
    {
        abort_if(!array_key_exists($role, AdminRole::LABELS), 404);
        abort_if($role === AdminRole::SUPER_ADMIN, 403);

        $defaults = AdminRole::DEFAULT_PERMISSIONS[$role] ?? [];
        $modules  = array_keys(AdminRole::MODULES);

        foreach ($modules as $module) {
            $perms = $defaults[$module] ?? [false, false, false, false, false];
            AdminRolePermission::updateOrCreate(
                ['role' => $role, 'module' => $module],
                [
                    'can_view'    => $perms[0],
                    'can_create'  => $perms[1],
                    'can_edit'    => $perms[2],
                    'can_delete'  => $perms[3],
                    'can_approve' => $perms[4],
                ]
            );
        }

        $notify[] = ['success', 'Permissions for ' . AdminRole::LABELS[$role] . ' reset to defaults.'];
        return to_route('admin.roles.index')->withNotify($notify);
    }
}
