<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (! Schema::hasColumn('expenses', 'subcategory')) {
                $table->string('subcategory')->nullable()->after('expense_category_id');
            }

            if (! Schema::hasColumn('expenses', 'bill_number')) {
                $table->string('bill_number')->nullable()->after('subcategory');
            }
        });

        if (Schema::hasColumn('expenses', 'expense_subcategory_id') && Schema::hasColumn('expenses', 'subcategory')) {
            $pairs = DB::table('expenses')
                ->join('expense_categories', 'expenses.expense_subcategory_id', '=', 'expense_categories.id')
                ->whereNotNull('expenses.expense_subcategory_id')
                ->pluck('expense_categories.name', 'expenses.id');

            foreach ($pairs as $expenseId => $subcategoryName) {
                DB::table('expenses')->where('id', $expenseId)->update(['subcategory' => $subcategoryName]);
            }
        }

        Schema::table('expenses', function (Blueprint $table) {
            if (Schema::hasColumn('expenses', 'expense_subcategory_id')) {
                try {
                    $table->dropForeign(['expense_subcategory_id']);
                } catch (\Throwable) {
                    // Ignore if constraint name differs or missing.
                }

                $table->dropColumn('expense_subcategory_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (! Schema::hasColumn('expenses', 'expense_subcategory_id')) {
                $table->foreignId('expense_subcategory_id')->nullable()->after('expense_category_id')->constrained('expense_categories')->nullOnDelete();
            }
        });

        Schema::table('expenses', function (Blueprint $table) {
            if (Schema::hasColumn('expenses', 'bill_number')) {
                $table->dropColumn('bill_number');
            }

            if (Schema::hasColumn('expenses', 'subcategory')) {
                $table->dropColumn('subcategory');
            }
        });
    }
};
