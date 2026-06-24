<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $modules = [
            'dashboard', 'users', 'roles', 'customers', 'shipments', 'payments',
            'sankari', 'expenses', 'employees', 'payroll', 'inventory',
            'accounting', 'reports', 'activity-logs', 'financial-year',
        ];

        $actions = ['view', 'create', 'edit', 'delete', 'export'];

        foreach ($modules as $module) {
            $moduleActions = $module === 'financial-year'
                ? ['view', 'create', 'edit', 'close', 'activate']
                : $actions;

            foreach ($moduleActions as $action) {
                Permission::findOrCreate("{$module}.{$action}");
            }
        }

        $superAdmin = Role::findOrCreate('Super Admin');
        $superAdmin->syncPermissions(Permission::all());

        $rolePermissions = [
            'Accountant' => [
                'dashboard.view', 'customers.view', 'customers.create', 'customers.edit',
                'shipments.view', 'payments.view', 'payments.create', 'payments.edit',
                'sankari.view', 'sankari.create', 'expenses.view', 'expenses.create', 'expenses.edit',
                'employees.view', 'payroll.view', 'payroll.create', 'payroll.edit',
                'accounting.view', 'accounting.create', 'reports.view', 'reports.export',
                'financial-year.view', 'financial-year.create', 'financial-year.edit', 'financial-year.close', 'financial-year.activate',
            ],
            'Manager' => [
                'dashboard.view', 'customers.view', 'shipments.view', 'shipments.create', 'shipments.edit',
                'payments.view', 'sankari.view', 'expenses.view', 'employees.view', 'payroll.view',
                'inventory.view', 'reports.view', 'reports.export', 'financial-year.view',
            ],
            'Data Entry Operator' => [
                'dashboard.view', 'customers.view', 'customers.create', 'shipments.view', 'shipments.create',
                'sankari.view', 'sankari.create', 'expenses.view', 'expenses.create',
                'inventory.view', 'inventory.create',
            ],
        ];

        foreach ($rolePermissions as $roleName => $permissions) {
            Role::findOrCreate($roleName)->syncPermissions($permissions);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
