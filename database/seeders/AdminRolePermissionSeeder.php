<?php

namespace Database\Seeders;

use App\Constants\AdminRole;
use App\Models\AdminRolePermission;
use Illuminate\Database\Seeder;

class AdminRolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $modules = array_keys(AdminRole::MODULES);

        foreach (AdminRole::DEFAULT_PERMISSIONS as $role => $roleModules) {
            foreach ($modules as $module) {
                $perms = $roleModules[$module] ?? [false, false, false, false, false];
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
        }
    }
}
