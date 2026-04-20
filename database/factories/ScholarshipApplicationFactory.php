<?php

namespace Database\Factories;

use App\Enums\ApplicationStatus;
use App\Enums\EducationLevel;
use App\Enums\FlightInstruction;
use App\Enums\FlightTraining;
use App\Enums\Gender;
use App\Enums\Gpa;
use App\Enums\Reference;
use App\Models\GradingGroup;
use App\Models\Scholarship;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScholarshipApplicationFactory extends Factory
{
    public function definition(): array
    {
        $hasAwards = fake()->boolean(40);
        $isPpotMember = fake()->boolean(60);

        return [
            'scholarship_id'     => Scholarship::factory(),
            'grading_group_id'   => GradingGroup::factory(),
            'name'               => fake()->name(),
            'email'              => fake()->unique()->safeEmail(),
            'phone'              => fake()->phoneNumber(),
            'city'               => fake()->city(),
            'state'              => fake()->stateAbbr(),
            'gender'             => fake()->randomElement(Gender::cases())->value,
            'ppot_member'        => $isPpotMember,
            'ppot_mentor'        => $isPpotMember ? fake()->name() : null,
            'prior_applicant'    => fake()->boolean(30),
            'reference'          => fake()->randomElement(Reference::cases())->value,
            'flight_school'      => fake()->company() . ' Flight Academy',
            'flight_training'    => fake()->randomElement(FlightTraining::cases())->value,
            'total_time'         => fake()->randomElement(['0 - 25 Hours', '26 - 50 Hours', '51 - 100 Hours', '101 - 250 Hours', '251 - 500 Hours']),
            'flight_instruction' => fake()->randomElement(FlightInstruction::cases())->value,
            'education_level'    => fake()->randomElement(EducationLevel::cases())->value,
            'school'             => fake()->company() . ' University',
            'graduation_month'   => fake()->monthName(),
            'graduation_year'    => fake()->numberBetween(2018, 2028),
            'gpa'                => fake()->randomElement(Gpa::cases())->value,
            'academics'          => fake()->paragraph(),
            'short_term_goal'    => fake()->paragraph(),
            'long_term_goal'     => fake()->paragraph(),
            'has_received_awards' => $hasAwards,
            'received_awards'    => $hasAwards ? fake()->paragraph() : null,
            'other_organizations' => fake()->paragraph(),
            'volunteer_events'   => fake()->paragraph(),
            'career_aspirations' => fake()->paragraph(),
            'essay_one'          => fake()->paragraphs(3, true),
            'essay_two'          => fake()->paragraphs(3, true),
            'status'             => ApplicationStatus::Pending->value,
            'auto_score'         => fake()->numberBetween(0, 23),
            'final_score'        => 0,
            'created_at'         => fake()->dateTimeBetween('-60 days', 'now'),
        ];
    }

    public function completed(): static
    {
        return $this->state([
            'status'      => ApplicationStatus::Completed->value,
            'final_score' => fake()->numberBetween(40, 100),
        ]);
    }

    public function flagged(): static
    {
        return $this->state([
            'status'      => ApplicationStatus::Flagged->value,
            'flag_reason' => 'Applicant has 1500+ hours of flight time.',
        ]);
    }
}
