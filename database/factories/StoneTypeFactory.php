<?php

namespace Database\Factories;

use App\Models\StoneType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class StoneTypeFactory extends Factory
{
    protected $model = StoneType::class;

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
