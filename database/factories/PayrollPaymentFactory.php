<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\PayrollPayment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PayrollPaymentFactory extends Factory
{
    protected $model = PayrollPayment::class;

    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'payment_date' => fake()->dateTimeBetween('-2 months'),
            'amount' => fake()->randomFloat(2, 5000, 30000),
            'payment_type' => fake()->randomElement(['full_salary', 'advance', 'partial']),
            'period_month' => fake()->date('Y-m'),
            'notes' => fake()->optional()->sentence(),
            'created_by' => User::factory(),
        ];
    }
}
