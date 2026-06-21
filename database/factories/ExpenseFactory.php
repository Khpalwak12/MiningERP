<?php

namespace Database\Factories;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    public function definition(): array
    {
        $category = ExpenseCategory::query()->whereNull('parent_id')->inRandomOrder()->first()
            ?? ExpenseCategory::factory()->create();

        return [
            'expense_category_id' => $category->id,
            'subcategory' => fake()->optional(0.8)->words(2, true),
            'bill_number' => fake()->optional(0.6)->numerify('BILL-####'),
            'expense_date' => fake()->dateTimeBetween('-2 months'),
            'amount' => fake()->randomFloat(2, 500, 50000),
            'description' => fake()->sentence(),
            'created_by' => User::factory(),
        ];
    }
}
