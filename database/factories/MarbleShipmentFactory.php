<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\MarbleShipment;
use App\Models\MineType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MarbleShipmentFactory extends Factory
{
    protected $model = MarbleShipment::class;

    public function definition(): array
    {
        $quantity = fake()->randomFloat(3, 10, 30);
        $price = fake()->randomFloat(2, 1000, 3000);

        return [
            'customer_id' => Customer::factory(),
            'mine_type_id' => MineType::factory(),
            'shipment_date' => fake()->dateTimeBetween('-3 months'),
            'driver_name' => fake()->name(),
            'quantity_ton' => $quantity,
            'price_per_ton' => $price,
            'total_amount' => round($quantity * $price, 2),
            'notes' => fake()->optional()->sentence(),
            'status' => MarbleShipment::STATUS_COMPLETED,
            'created_by' => User::factory(),
        ];
    }
}
