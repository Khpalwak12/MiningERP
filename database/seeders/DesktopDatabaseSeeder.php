<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DesktopDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(ErpSystemSeeder::class);
    }
}
