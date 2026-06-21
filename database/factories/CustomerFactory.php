<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company().' Factory',
            'owner_name' => fake()->name(),
            'phone' => fake()->numerify('07########'),
            'address' => fake()->address(),
            'status' => 'active',
        ];
    }
}
