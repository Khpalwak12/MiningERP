<?php

namespace Database\Factories;

use App\Models\MarbleType;
use Illuminate\Database\Eloquent\Factories\Factory;

class MarbleTypeFactory extends Factory
{
    protected $model = MarbleType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true).' Marble',
            'status' => 'active',
        ];
    }
}
