<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->boolean('is_shared_with_contractor')->default(false)->after('status');
            $table->decimal('contractor_salary_share_percent', 5, 2)->nullable()->after('is_shared_with_contractor');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['is_shared_with_contractor', 'contractor_salary_share_percent']);
        });
    }
};
