<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Ensure secretary role exists
        $secretaryRole = Role::firstOrCreate(['name' => 'secretary', 'guard_name' => 'web']);

        // Ensure and assign permissions
        $permissions = [
            'view-requests',
            'screen-requests',
            'view-monitoring',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
        }

        $secretaryRole->givePermissionTo($permissions);
    }

    public function down(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $secretaryRole = Role::where('name', 'secretary')->where('guard_name', 'web')->first();
        if ($secretaryRole) {
            $secretaryRole->revokePermissionTo([
                'view-requests',
                'screen-requests',
                'view-monitoring',
            ]);
        }
    }
};
