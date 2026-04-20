<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ScholarshipFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'       => fake()->year() . ' PPOT Scholarship',
            'opens_at'   => now()->subMonths(2),
            'closes_at'  => now()->addMonths(1),
            'award_date' => now()->addMonths(3),
            'is_active'  => true,
        ];
    }
}
