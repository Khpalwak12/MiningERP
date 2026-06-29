<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\ExpenseCategory;
use App\Models\InventoryItem;
use App\Models\MarbleType;
use App\Models\User;
use App\Support\ErpPermissionSync;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
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
        ErpPermissionSync::sync();
    }

    private function seedRoles(): void
    {
        // Role permissions are synced in seedPermissions().
        Role::findOrCreate('Super Admin');
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
            [
                'slug' => 'vehicle_parts',
                'name_en' => 'Vehicle Parts',
                'name_ps' => 'د وسایطو پرزې',
                'description' => 'Truck, excavator and generator parts',
            ],
            [
                'slug' => 'fuel_oil',
                'name_en' => 'Fuel & Oil',
                'name_ps' => 'روغنیات او تیل',
                'description' => 'Diesel, engine oil and hydraulic oil',
            ],
            [
                'slug' => 'maintenance',
                'name_en' => 'Maintenance',
                'name_ps' => 'ترمیم او مستري',
                'description' => 'Mechanic and repair costs',
            ],
            [
                'slug' => 'kitchen',
                'name_en' => 'Kitchen',
                'name_ps' => 'اشپزخانه',
                'description' => 'Food, tea, water and cooking supplies',
            ],
            [
                'slug' => 'utilities',
                'name_en' => 'Utilities',
                'name_ps' => 'خدمات',
                'description' => 'Electricity, internet and generator running costs',
            ],
            [
                'slug' => 'other',
                'name_en' => 'Other',
                'name_ps' => 'نور',
                'description' => 'Miscellaneous expenses',
            ],
        ];

        foreach ($categories as $data) {
            ExpenseCategory::updateOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, ['name' => $data['name_en']])
            );
        }

        ExpenseCategory::query()->whereNotNull('parent_id')->delete();
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
