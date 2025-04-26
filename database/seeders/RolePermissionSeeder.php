<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Integration permissions
            'create integration',
            'view integration',
            'edit integration',
            'delete integration',

            // Approval permissions
            'approve as app owner',
            'approve as idi',
            'approve as security',
            'approve as infrastructure',
            'return integration',

            // Configuration permissions
            'manage configuration',
            'view configuration',

            // Vendor permissions
            'create vendor',
            'edit vendor',
            'view vendor',
            'delete vendor',

            // Report permissions
            'view reports',
            'export reports',

            // System administration
            'manage users',
            'manage roles',
            'view audit logs',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions

        // 1. Administrator Role
        $adminRole = Role::create(['name' => 'administrator']);
        $adminRole->givePermissionTo(Permission::all());

        // 2. Requester Role
        $requesterRole = Role::create(['name' => 'requester']);
        $requesterRole->givePermissionTo([
            'create integration',
            'view integration',
            'edit integration',
            'view vendor',
        ]);

        // 3. App Owner Role
        $appOwnerRole = Role::create(['name' => 'app_owner']);
        $appOwnerRole->givePermissionTo([
            'view integration',
            'approve as app owner',
            'return integration',
            'view vendor',
            'view reports',
        ]);

        // 4. IDI Team Role
        $idiRole = Role::create(['name' => 'idi_team']);
        $idiRole->givePermissionTo([
            'view integration',
            'approve as idi',
            'return integration',
            'view vendor',
            'view reports',
            'view configuration',
        ]);

        // 5. Security Team Role
        $securityRole = Role::create(['name' => 'security_team']);
        $securityRole->givePermissionTo([
            'view integration',
            'approve as security',
            'return integration',
            'view vendor',
            'view reports',
        ]);

        // 6. Infrastructure Team Role
        $infrastructureRole = Role::create(['name' => 'infrastructure_team']);
        $infrastructureRole->givePermissionTo([
            'view integration',
            'approve as infrastructure',
            'return integration',
            'view vendor',
            'view reports',
        ]);

        // 7. Vendor Manager Role
        $vendorRole = Role::create(['name' => 'vendor_manager']);
        $vendorRole->givePermissionTo([
            'create vendor',
            'edit vendor',
            'view vendor',
            'delete vendor',
            'view integration',
        ]);

        // 8. Report Viewer Role
        $reportRole = Role::create(['name' => 'report_viewer']);
        $reportRole->givePermissionTo([
            'view reports',
            'export reports',
            'view integration',
        ]);

        // Create a default admin user
        $user = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@flex.co.tz',
            'password' => bcrypt('password'),
        ]);

        $user->assignRole('administrator');
    }
}
