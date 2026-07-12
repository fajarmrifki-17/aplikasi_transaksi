<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Create Permissions
        $permissions = [
            'submission.create',
            'submission.read',
            'submission.update',
            'submission.delete',
            'approval.spv',
            'approval.manager',
            'approval.director',
            'finance.payment',
            'reports.view',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // 2. Create Roles and Assign Permissions
        
        // Staff
        $staff = Role::findOrCreate('Staff');
        $staff->givePermissionTo([
            'submission.create',
            'submission.read',
            'submission.update',
            'submission.delete',
        ]);

        // Supervisor
        $supervisor = Role::findOrCreate('Supervisor');
        $supervisor->givePermissionTo([
            'submission.read',
            'approval.spv',
        ]);

        // Manager
        $manager = Role::findOrCreate('Manager');
        $manager->givePermissionTo([
            'submission.read',
            'approval.manager',
            'reports.view',
        ]);

        // Director
        $director = Role::findOrCreate('Director');
        $director->givePermissionTo([
            'submission.read',
            'approval.director',
            'reports.view',
        ]);

        // Finance
        $finance = Role::findOrCreate('Finance');
        $finance->givePermissionTo([
            'submission.read',
            'finance.payment',
            'reports.view',
        ]);
    }
}
