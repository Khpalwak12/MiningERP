<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerPaymentFactory extends Factory
{
    protected $model = CustomerPayment::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'payment_date' => fake()->dateTimeBetween('-2 months'),
            'amount' => fake()->randomFloat(2, 5000, 100000),
            'receipt_number' => fake()->optional(0.7)->unique()->numerify('RCP-#####'),
            'received_by' => fake()->name(),
            'notes' => fake()->optional()->sentence(),
            'created_by' => User::factory(),
        ];
    }
}
