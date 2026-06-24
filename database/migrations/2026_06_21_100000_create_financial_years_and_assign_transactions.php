<?php

use App\Support\JalaliDate;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $transactionTables = [
        'marble_shipments',
        'customer_payments',
        'sankari_stone_sales',
        'expenses',
        'payroll_payments',
        'inventory_movements',
        'journal_entries',
    ];

    public function up(): void
    {
        Schema::create('financial_years', function (Blueprint $table) {
            $table->id();
            $table->string('name', 10);
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['active', 'closed'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique('name');
        });

        $closedYearId = DB::table('financial_years')->insertGetId([
            'name' => '1404',
            'start_date' => JalaliDate::toGregorian('1404/01/01')->format('Y-m-d'),
            'end_date' => JalaliDate::toGregorian('1404/12/29')->format('Y-m-d'),
            'status' => 'closed',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $activeYearId = DB::table('financial_years')->insertGetId([
            'name' => '1405',
            'start_date' => JalaliDate::toGregorian('1405/01/01')->format('Y-m-d'),
            'end_date' => JalaliDate::toGregorian('1405/12/29')->format('Y-m-d'),
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach ($this->transactionTables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->foreignId('financial_year_id')->nullable()->after('id')->constrained('financial_years');
            });

            DB::table($table)->whereNull('financial_year_id')->update([
                'financial_year_id' => $activeYearId,
            ]);
        }

        unset($closedYearId);
    }

    public function down(): void
    {
        foreach ($this->transactionTables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropConstrainedForeignId('financial_year_id');
            });
        }

        Schema::dropIfExists('financial_years');
    }
};
