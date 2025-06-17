<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions for fresh installation.
        DB::table('permissions')->insert([
            // User Permissions
            ['name' => 'View User', 'guard_name' => 'web'],
            ['name' => 'Add User', 'guard_name' => 'web'],
            ['name' => 'Edit User', 'guard_name' => 'web'],
            ['name' => 'Delete User', 'guard_name' => 'web'],
            // Role Permissions
            ['name' => 'View Role', 'guard_name' => 'web'],
            ['name' => 'Add Role', 'guard_name' => 'web'],
            ['name' => 'Edit Role', 'guard_name' => 'web'],
            ['name' => 'Delete Role', 'guard_name' => 'web'],
            // Expert Permissions
            ['name' => 'View Expert', 'guard_name' => 'web'],
            ['name' => 'Add Expert', 'guard_name' => 'web'],
            ['name' => 'Edit Expert', 'guard_name' => 'web'],
            ['name' => 'Delete Expert', 'guard_name' => 'web'],
            // Car Permissions
            ['name' => 'View Car', 'guard_name' => 'web'],
            ['name' => 'Add Car', 'guard_name' => 'web'],
            ['name' => 'Edit Car', 'guard_name' => 'web'],
            ['name' => 'Delete Car', 'guard_name' => 'web'],
            // Problem Permissions
            ['name' => 'View Problem', 'guard_name' => 'web'],
            ['name' => 'Add Problem', 'guard_name' => 'web'],
            ['name' => 'Edit Problem', 'guard_name' => 'web'],
            ['name' => 'Delete Problem', 'guard_name' => 'web'],
            // Solution Permissions
            ['name' => 'View Solution', 'guard_name' => 'web'],
            ['name' => 'Add Solution', 'guard_name' => 'web'],
            ['name' => 'Edit Solution', 'guard_name' => 'web'],
            ['name' => 'Delete Solution', 'guard_name' => 'web'],
            // Step Permissions
            ['name' => 'View Step', 'guard_name' => 'web'],
            ['name' => 'Add Step', 'guard_name' => 'web'],
            ['name' => 'Edit Step', 'guard_name' => 'web'],
            ['name' => 'Delete Step', 'guard_name' => 'web'],

        ]);
        $clientRole = Role::create
        ([
            'name' => 'Client',
        ]);
         $expertRole = Role::create
        ([
            'name' => 'Expert',
        ]);
        $expertRole->givePermissionTo(['View Problem','View Solution' ,'Add Solution', 'Edit Solution', 'Delete Solution',
            'View Step', 'Add Step', 'Edit Step', 'Delete Step','View Car']);

        // Create admin role and assign all permissions.
        $adminRole = Role::create([
            'name' => 'Admin',
        ]);
        $adminRole->givePermissionTo(Permission::all());

        User::create([
            'name' => 'Rehan',
            'email' => 'rehan7@gmail.com',
            'password' => \Hash::make('Rehan123'),
            'email_verified_at' => now(),
        ]);

        // Assign permission to admin
        $adminUser = User::where('email', 'rehan7@gmail.com')->first();
        $adminUser->assignRole($adminRole);
    }
}
