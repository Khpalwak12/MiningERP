<?php

namespace Database\Factories;

use App\Models\MineType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MineTypeFactory extends Factory
{
    protected $model = MineType::class;

    public function definition(): array
    {
        $nameEn = fake()->unique()->words(2, true);

        return [
            'name' => $nameEn,
            'name_en' => $nameEn,
            'name_ps' => fake()->words(2, true),
            'description' => fake()->optional()->sentence(),
            'slug' => Str::slug($nameEn),
        ];
    }
}
