<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Role management lanjutan memakai spatie/laravel-permission.
 * Kolom users.role tetap dipertahankan (sesuai brief) dan disinkronkan ke role Spatie.
 */
class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $adminPermissions = [
            'manage-categories', 'manage-items', 'manage-loans',
            'manage-users', 'view-activity-logs', 'export-reports', 'manage-settings',
        ];
        $userPermissions = ['view-catalog', 'request-loans'];

        foreach (array_merge($adminPermissions, $userPermissions) as $name) {
            Permission::findOrCreate($name, 'web');
        }

        Role::findOrCreate('admin', 'web')->syncPermissions($adminPermissions);
        Role::findOrCreate('user', 'web')->syncPermissions($userPermissions);
    }
}
