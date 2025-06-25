<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // User Permissions
            'View User', 'Add User', 'Edit User', 'Delete User',

            // Role Permissions
            'View Role', 'Add Role', 'Edit Role', 'Delete Role',

            // Expert Permissions
            'View Expert', 'Add Expert', 'Edit Expert', 'Delete Expert',

            // Car Permissions
            'View Car', 'Add Car', 'Edit Car', 'Delete Car',

            // Problem Permissions
            'View Problem', 'Add Problem', 'Edit Problem', 'Delete Problem', 'Assign Problem',

            // Solution Permissions
            'View Solution', 'Add Solution', 'Edit Solution', 'Delete Solution',

            // Step Permissions
            'View Step', 'Add Step', 'Edit Step', 'Delete Step',

            // Client Car Permissions
            'View Client Car', 'Add Client Car', 'Delete Client Car',

            // Feedback Permissions
            'View feedback', 'Add feedback', 'Edit feedback', 'Delete feedback',
        ];

        // ✅ Create permissions if they don't exist
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission],
                ['guard_name' => 'web']
            );
        }

        // ✅ Create & sync Client role
        $clientRole = Role::firstOrCreate(['name' => 'Client']);
        $clientRole->syncPermissions([
            'View Client Car', 'Add Client Car', 'Delete Client Car',
            'View Car', 'View Problem', 'View Solution', 'View Step',
            'View feedback', 'Add feedback', 'Edit feedback', 'Delete feedback',
            'Add Problem',
        ]);

        // ✅ Create & sync Expert role
        $expertRole = Role::firstOrCreate(['name' => 'Expert']);
        $expertRole->syncPermissions([
            'View Problem', 'View Solution', 'Add Solution', 'Edit Solution', 'Delete Solution',
            'View Step', 'Add Step', 'Edit Step', 'Delete Step',
            'View Car',
        ]);

        // ✅ Create & sync Admin role with all permissions
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminRole->syncPermissions(Permission::all());

        // ✅ Create Admin User if not exists
        $adminUser = User::firstOrCreate(
            ['email' => 'rehan7@gmail.com'],
            [
                'name' => 'Rehan',
                'password' => Hash::make('Rehan123'),
                'email_verified_at' => now(),
            ]
        );

        // ✅ Assign Admin role to user if not already assigned
        if (!$adminUser->hasRole('Admin')) {
            $adminUser->assignRole($adminRole);
        }
    }
}
