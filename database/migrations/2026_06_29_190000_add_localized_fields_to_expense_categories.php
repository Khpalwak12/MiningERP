<?php

use App\Models\ExpenseCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expense_categories', function (Blueprint $table) {
            $table->string('name_en')->nullable()->after('name');
            $table->string('name_ps')->nullable()->after('name_en');
            $table->text('description')->nullable()->after('name_ps');
        });

        $pashtoNames = [
            'kitchen' => 'اشپزخانه',
            'fuel_oil' => 'روغنیات او تیل',
            'vehicle_parts' => 'د وسایطو پرزې',
            'maintenance' => 'ترمیم او مستري',
            'utilities' => 'خدمات',
            'other' => 'نور',
        ];

        ExpenseCategory::query()->each(function (ExpenseCategory $category) use ($pashtoNames) {
            $nameEn = $category->parent_id
                ? $category->name
                : ucwords(str_replace('_', ' ', $category->slug));

            if (! $category->parent_id && isset($pashtoNames[$category->slug])) {
                $nameEn = match ($category->slug) {
                    'kitchen' => 'Kitchen',
                    'fuel_oil' => 'Fuel & Oil',
                    'vehicle_parts' => 'Vehicle Parts',
                    'maintenance' => 'Maintenance',
                    'utilities' => 'Utilities',
                    'other' => 'Other',
                    default => $nameEn,
                };
            }

            $category->update([
                'name_en' => $nameEn,
                'name_ps' => $pashtoNames[$category->slug] ?? ($category->parent_id ? null : $category->name),
                'name' => $nameEn,
            ]);
        });

        $childCategories = ExpenseCategory::query()->whereNotNull('parent_id')->get();
        foreach ($childCategories as $child) {
            DB::table('expenses')
                ->where('expense_category_id', $child->id)
                ->update(['expense_category_id' => $child->parent_id]);
        }

        if ($childCategories->isNotEmpty()) {
            ExpenseCategory::query()->whereNotNull('parent_id')->delete();
        }

        Schema::table('expense_categories', function (Blueprint $table) {
            $table->string('name_en')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('expense_categories', function (Blueprint $table) {
            $table->dropColumn(['name_en', 'name_ps', 'description']);
        });
    }
};
