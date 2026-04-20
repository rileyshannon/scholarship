<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class GradingGroupFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Group ' . fake()->unique()->randomLetter(),
        ];
    }
}
