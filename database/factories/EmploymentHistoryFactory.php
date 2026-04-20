<?php

namespace Database\Factories;

use App\Enums\EmploymentLength;
use App\Models\ScholarshipApplication;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmploymentHistoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'scholarship_application_id' => ScholarshipApplication::factory(),
            'employer_name'              => fake()->company(),
            'position'                   => fake()->jobTitle(),
            'length'                     => fake()->randomElement(EmploymentLength::cases())->value,
        ];
    }
}
