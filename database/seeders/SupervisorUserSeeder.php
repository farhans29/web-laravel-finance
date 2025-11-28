<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SupervisorUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions for Invoice resource
        $permissions = [
            'view_invoice',
            'view_any_invoice',
            'create_invoice',
            'update_invoice',
            'delete_invoice',
            'delete_any_invoice',
            'view_role',
            'view_any_role',
            'create_role',
            'update_role',
            'delete_role',
            'delete_any_role',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create admin role with all permissions
        $adminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $adminRole->givePermissionTo(Permission::all());

        // Assign admin role to existing admin user
        $adminUser = User::where('email', 'admin@mail.com')->first();
        if ($adminUser) {
            $adminUser->assignRole('super_admin');
        }

        // Create supervisor role with limited permissions (only view and update)
        $supervisorRole = Role::firstOrCreate(['name' => 'supervisor']);
        $supervisorRole->syncPermissions([
            'view_invoice',
            'view_any_invoice',
            'update_invoice',
        ]);

        // Create supervisor user
        $supervisor = User::firstOrCreate(
            ['email' => 'supervisor@mail.com'],
            [
                'name' => 'Supervisor',
                'password' => Hash::make('supervisor12345'),
            ]
        );

        $supervisor->assignRole('supervisor');

        // Create user role with full CRUD permissions for invoices
        $userRole = Role::firstOrCreate(['name' => 'user']);
        $userRole->syncPermissions([
            'view_invoice',
            'view_any_invoice',
            'create_invoice',
            'update_invoice',
            'delete_invoice',
            'delete_any_invoice',
        ]);

        // Create regular user
        $regularUser = User::firstOrCreate(
            ['email' => 'user@mail.com'],
            [
                'name' => 'User',
                'password' => Hash::make('user12345'),
            ]
        );

        $regularUser->assignRole('user');
    }
}