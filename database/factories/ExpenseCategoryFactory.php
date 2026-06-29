<?php

namespace Database\Factories;

use App\Models\ExpenseCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ExpenseCategoryFactory extends Factory
{
    protected $model = ExpenseCategory::class;

    public function definition(): array
    {
        $nameEn = fake()->unique()->words(2, true);

        return [
            'name' => $nameEn,
            'name_en' => $nameEn,
            'name_ps' => 'ازموینه '.$nameEn,
            'description' => fake()->optional()->sentence(),
            'slug' => Str::slug($nameEn),
            'parent_id' => null,
        ];
    }
}
