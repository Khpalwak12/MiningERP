<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('financial_years', function (Blueprint $table) {
            $table->boolean('is_all_years')->default(false)->after('status');
        });

        if (! DB::table('financial_years')->where('is_all_years', true)->exists()) {
            DB::table('financial_years')->insert([
                'name' => 'ALL',
                'is_all_years' => true,
                'start_date' => '1900-01-01',
                'end_date' => '2099-12-31',
                'status' => 'closed',
                'notes' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('financial_years')->where('is_all_years', true)->delete();

        Schema::table('financial_years', function (Blueprint $table) {
            $table->dropColumn('is_all_years');
        });
    }
};
