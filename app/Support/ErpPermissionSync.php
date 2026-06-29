<?php

namespace App\Support;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class ErpPermissionSync
{
    public static function sync(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        foreach (self::moduleActions() as $module => $actions) {
            foreach ($actions as $action) {
                Permission::findOrCreate("{$module}.{$action}");
            }
        }

        Role::findOrCreate('Super Admin')->syncPermissions(Permission::all());

        foreach (self::rolePermissions() as $roleName => $permissions) {
            Role::findOrCreate($roleName)->syncPermissions($permissions);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public static function moduleActions(): array
    {
        $defaultActions = ['view', 'create', 'edit', 'delete', 'export'];

        $modules = [
            'dashboard', 'users', 'roles', 'customers', 'shipments', 'payments',
            'sankari', 'expenses', 'expense-categories', 'employees', 'payroll',             'inventory', 'mine-assets', 'personal-accounts',
            'accounting', 'reports', 'financial-year', 'contractor-royalty',
        ];

        $actions = [];

        foreach ($modules as $module) {
            $actions[$module] = match ($module) {
                'financial-year' => ['view', 'create', 'edit', 'close', 'activate'],
                'contractor-royalty' => ['view', 'create', 'edit', 'delete', 'reports'],
                default => $defaultActions,
            };
        }

        return $actions;
    }

    public static function rolePermissions(): array
    {
        return [
            'Accountant' => [
                'dashboard.view', 'customers.view', 'customers.create', 'customers.edit',
                'shipments.view', 'payments.view', 'payments.create', 'payments.edit',
                'sankari.view', 'sankari.create', 'expenses.view', 'expenses.create', 'expenses.edit',
                'expense-categories.view', 'expense-categories.create', 'expense-categories.edit',
                'employees.view', 'payroll.view', 'payroll.create', 'payroll.edit',
                'accounting.view', 'accounting.create', 'reports.view', 'reports.export',
                'contractor-royalty.view', 'contractor-royalty.create', 'contractor-royalty.edit', 'contractor-royalty.reports',
                'mine-assets.view', 'mine-assets.create', 'mine-assets.edit',
                'personal-accounts.view', 'personal-accounts.create', 'personal-accounts.edit',
                'financial-year.view', 'financial-year.create', 'financial-year.edit', 'financial-year.close', 'financial-year.activate',
            ],
            'Manager' => [
                'dashboard.view', 'customers.view', 'shipments.view', 'shipments.create', 'shipments.edit',
                'payments.view', 'sankari.view', 'expenses.view', 'expense-categories.view', 'employees.view', 'payroll.view',
                'inventory.view', 'reports.view', 'reports.export', 'financial-year.view',
                'mine-assets.view',
                'mine-assets.view',
                'contractor-royalty.view', 'contractor-royalty.reports',
            ],
            'Data Entry Operator' => [
                'dashboard.view', 'customers.view', 'customers.create', 'shipments.view', 'shipments.create',
                'sankari.view', 'sankari.create', 'expenses.view', 'expenses.create',
                'expense-categories.view',
                'inventory.view', 'inventory.create',
                'mine-assets.view', 'mine-assets.create',
            ],
        ];
    }
}
