<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\ExpenseCategory;
use App\Models\MineType;
use App\Models\StoneType;
use App\Models\User;
use App\Support\ErpPermissionSync;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class ErpSystemSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedPermissions();
        $this->seedRoles();
        $this->seedUsers();
        $this->seedExpenseCategories();
        $this->seedMineTypes();
        $this->seedStoneTypes();
        $this->seedAccounts();
    }

    private function seedPermissions(): void
    {
        ErpPermissionSync::sync();
    }

    private function seedRoles(): void
    {
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

    private function seedMineTypes(): void
    {
        $types = [
            [
                'slug' => 'white-marble',
                'name_en' => 'White Marble',
                'name_ps' => 'سپین مرمر',
                'description' => 'White marble from the quarry',
            ],
            [
                'slug' => 'cream-marble',
                'name_en' => 'Cream Marble',
                'name_ps' => 'کریم مرمر',
                'description' => 'Cream colored marble',
            ],
            [
                'slug' => 'gray-marble',
                'name_en' => 'Gray Marble',
                'name_ps' => 'خړ مرمر',
                'description' => 'Gray marble variety',
            ],
        ];

        foreach ($types as $data) {
            MineType::query()->updateOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, ['name' => $data['name_en']]),
            );
        }
    }

    private function seedStoneTypes(): void
    {
        $types = [
            [
                'slug' => 'block',
                'name_en' => 'Block',
                'name_ps' => 'بلاک',
                'description' => 'Stone block',
            ],
            [
                'slug' => 'slab',
                'name_en' => 'Slab',
                'name_ps' => 'سلیب',
                'description' => 'Stone slab',
            ],
            [
                'slug' => 'crushed',
                'name_en' => 'Crushed',
                'name_ps' => 'ماته تیږه',
                'description' => 'Crushed stone',
            ],
        ];

        foreach ($types as $data) {
            StoneType::query()->updateOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, ['name' => $data['name_en']]),
            );
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
}
