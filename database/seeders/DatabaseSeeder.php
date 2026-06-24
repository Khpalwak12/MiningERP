<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\ExpenseCategory;
use App\Models\InventoryItem;
use App\Models\MarbleType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedPermissions();
        $this->seedRoles();
        $this->seedUsers();
        $this->seedExpenseCategories();
        $this->seedMarbleTypes();
        $this->seedAccounts();
        $this->seedSampleData();
    }

    private function seedPermissions(): void
    {
        $modules = [
            'dashboard', 'users', 'roles', 'customers', 'shipments', 'payments',
            'sankari', 'expenses', 'employees', 'payroll', 'inventory',
            'accounting', 'reports', 'financial-year',
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
    }

    private function seedRoles(): void
    {
        $roles = [
            'Super Admin' => null,
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

        foreach ($roles as $roleName => $permissions) {
            $role = Role::findOrCreate($roleName);
            $role->syncPermissions($permissions ?? Permission::all()->pluck('name')->toArray());
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    private function seedUsers(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@marbleerp.local'],
            ['name' => 'Super Admin', 'password' => Hash::make('password'), 'locale' => 'en']
        );
        $admin->assignRole('Super Admin');
    }

    private function seedExpenseCategories(): void
    {
        $categories = [
            'vehicle_parts' => ['Truck Parts', 'Excavator Parts', 'Generator Parts'],
            'fuel_oil' => ['Diesel', 'Engine Oil', 'Hydraulic Oil'],
            'maintenance' => ['Mechanic Charges', 'Repair Costs'],
            'kitchen' => ['Food', 'Tea', 'Water', 'Cooking Materials'],
            'utilities' => ['Electricity', 'Internet', 'Generator Running Cost'],
            'other' => ['Miscellaneous'],
        ];

        foreach ($categories as $slug => $children) {
            $parent = ExpenseCategory::firstOrCreate(
                ['slug' => $slug],
                ['name' => ucwords(str_replace('_', ' ', $slug))]
            );

            foreach ($children as $child) {
                ExpenseCategory::firstOrCreate(
                    ['slug' => $slug.'_'.str($child)->slug()],
                    ['name' => $child, 'parent_id' => $parent->id]
                );
            }
        }
    }

    private function seedMarbleTypes(): void
    {
        foreach (['White Marble', 'Cream Marble', 'Gray Marble'] as $type) {
            MarbleType::firstOrCreate(['name' => $type]);
        }
    }

    private function seedAccounts(): void
    {
        $accounts = [
            ['1000', 'Cash', 'asset'],
            ['1100', 'Accounts Receivable', 'asset'],
            ['2000', 'Accounts Payable', 'liability'],
            ['3000', 'Owner Equity', 'equity'],
            ['4000', 'Marble Sales Income', 'income'],
            ['4100', 'Sankari Stone Income', 'income'],
            ['5000', 'Operating Expenses', 'expense'],
            ['5100', 'Payroll Expense', 'expense'],
        ];

        foreach ($accounts as [$code, $name, $type]) {
            Account::firstOrCreate(['code' => $code], ['name' => $name, 'type' => $type]);
        }
    }

    private function seedSampleData(): void
    {
        Customer::firstOrCreate(
            ['name' => 'Kabul Marble Factory'],
            ['owner_name' => 'Ahmad Khan', 'phone' => '0700123456', 'address' => 'Kabul', 'status' => 'active']
        );

        Employee::firstOrCreate(
            ['name' => 'Mohammad Ali'],
            ['father_name' => 'Karim', 'phone' => '0700987654', 'position' => 'Driver', 'salary' => 25000, 'status' => 'active']
        );

        InventoryItem::firstOrCreate(
            ['sku' => 'DIESEL-TANK'],
            ['name' => 'Diesel', 'unit' => 'liter', 'category' => 'Fuel', 'min_stock' => 500, 'current_stock' => 1000]
        );
    }
}
