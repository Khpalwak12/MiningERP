<?php

use App\Support\ErpPermissionSync;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        ErpPermissionSync::sync();
    }

    public function down(): void
    {
        //
    }
};
