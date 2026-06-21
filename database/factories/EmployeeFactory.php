<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'father_name' => fake()->name('male'),
            'phone' => fake()->numerify('07########'),
            'position' => fake()->randomElement(['Driver', 'Operator', 'Mechanic', 'Guard', 'Accountant']),
            'salary' => fake()->randomFloat(2, 15000, 50000),
            'joining_date' => fake()->dateTimeBetween('-2 years'),
            'status' => 'active',
        ];
    }
}
