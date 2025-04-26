<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test users for each role

        // 1. Administrator (already created in RolePermissionSeeder)
        // Just for reference
        // User::factory()->create([
        //     'name' => 'Admin User',
        //     'email' => 'admin@flex.co.tz',
        //     'password' => bcrypt('password'),
        // ])->assignRole('administrator');

        // 2. Requester
        User::factory()->create([
            'name' => 'Requester User',
            'email' => 'requester@flex.co.tz',
            'password' => bcrypt('password'),
        ])->assignRole('requester');

        // 3. App Owner
        User::factory()->create([
            'name' => 'App Owner User',
            'email' => 'appowner@flex.co.tz',
            'password' => bcrypt('password'),
        ])->assignRole('app_owner');

        // 4. IDI Team
        User::factory()->create([
            'name' => 'IDI Team User',
            'email' => 'idi@flex.co.tz',
            'password' => bcrypt('password'),
        ])->assignRole('idi_team');

        // 5. Security Team
        User::factory()->create([
            'name' => 'Security Team User',
            'email' => 'security@flex.co.tz',
            'password' => bcrypt('password'),
        ])->assignRole('security_team');

        // 6. Infrastructure Team
        User::factory()->create([
            'name' => 'Infrastructure Team User',
            'email' => 'infrastructure@flex.co.tz',
            'password' => bcrypt('password'),
        ])->assignRole('infrastructure_team');

        // 7. Vendor Manager
        User::factory()->create([
            'name' => 'Vendor Manager User',
            'email' => 'vendor@flex.co.tz',
            'password' => bcrypt('password'),
        ])->assignRole('vendor_manager');

        // 8. Report Viewer
        User::factory()->create([
            'name' => 'Report Viewer User',
            'email' => 'reports@flex.co.tz',
            'password' => bcrypt('password'),
        ])->assignRole('report_viewer');
    }
}
