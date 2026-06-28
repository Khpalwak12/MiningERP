<?php

namespace Database\Seeders;

use App\Support\ErpPermissionSync;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        ErpPermissionSync::sync();
    }
}
