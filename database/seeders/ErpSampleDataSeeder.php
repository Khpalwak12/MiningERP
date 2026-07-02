<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\InventoryItem;
use Illuminate\Database\Seeder;

class ErpSampleDataSeeder extends Seeder
{
    public function run(): void
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
