<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $operator = Role::firstOrCreate(['name' => 'operator']);
        $verificator = Role::firstOrCreate(['name' => 'verificator']);
        $auditor = Role::firstOrCreate(['name' => 'auditor']);

        // Define permissions for Operator (Invoice and Cash In CRUD)
        $operatorPermissions = [
            // Invoice permissions
            'view_invoice',
            'view_any_invoice',
            'create_invoice',
            'update_invoice',
            'delete_invoice',
            'delete_any_invoice',

            // Cash In permissions
            'view_cash::in',
            'view_any_cash::in',
            'create_cash::in',
            'update_cash::in',
            'delete_cash::in',
            'delete_any_cash::in',
        ];

        // Define permissions for Verificator (Approval access)
        $verificatorPermissions = [
            // Invoice Approval permissions
            'view_approval',
            'view_any_approval',
            'update_approval',

            // Cash In Approval permissions
            'view_cash::in::approval',
            'view_any_cash::in::approval',
            'update_cash::in::approval',
        ];

        // Define permissions for Auditor (View only approval)
        $auditorPermissions = [
            // Invoice Approval view only
            'view_approval',
            'view_any_approval',

            // Cash In Approval view only
            'view_cash::in::approval',
            'view_any_cash::in::approval',
        ];

        // Create permissions and assign to Operator
        foreach ($operatorPermissions as $permission) {
            $perm = Permission::firstOrCreate(['name' => $permission]);
            $operator->givePermissionTo($perm);
        }

        // Create permissions and assign to Verificator
        foreach ($verificatorPermissions as $permission) {
            $perm = Permission::firstOrCreate(['name' => $permission]);
            $verificator->givePermissionTo($perm);
        }

        // Create permissions and assign to Auditor
        foreach ($auditorPermissions as $permission) {
            $perm = Permission::firstOrCreate(['name' => $permission]);
            $auditor->givePermissionTo($perm);
        }

        // Super Admin gets all permissions (this is handled by the hasRole check in resources)
        $this->command->info('Roles and permissions have been set up successfully!');
        $this->command->info('');
        $this->command->info('Role Summary:');
        $this->command->info('- Super Admin: Full access to all features + Role/User management');
        $this->command->info('- Operator: Create, Edit, Delete Invoice and Cash In');
        $this->command->info('- Verificator: Approve or Reject via Invoice Approval and Cash In Approval');
        $this->command->info('- Auditor: View only Invoice Approval and Cash In Approval');
    }
}