<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $studentRole = Role::firstOrCreate(['name' => 'student']);
        $secretaryRole = Role::firstOrCreate(['name' => 'secretary']);
        $committeeRole = Role::firstOrCreate(['name' => 'committee']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Create permissions
        $permissions = [
            'view-requests',
            'create-request',
            'review-requests',
            'approve-request',
            'manage-users',
            'manage-departments',
            'manage-document-types',
            'view-admin',
            'view-developer',
            'screen-requests',
            'route-review-type',
            'assign-reviewers',
            'submit-evaluations',
            'view-monitoring',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions
        $studentRole->syncPermissions(['view-requests', 'create-request']);
        $secretaryRole->syncPermissions(['view-requests', 'screen-requests', 'view-monitoring']);
        $committeeRole->syncPermissions(['view-requests', 'review-requests', 'submit-evaluations', 'view-monitoring']);
        $adminRole->syncPermissions($permissions);
    }
}
