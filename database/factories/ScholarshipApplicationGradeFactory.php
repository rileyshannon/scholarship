<?php

namespace Database\Factories;

use App\Enums\GradeStatus;
use App\Models\ScholarshipApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScholarshipApplicationGradeFactory extends Factory
{
    public function definition(): array
    {
        $shortTermGoal      = fake()->numberBetween(0, 2);
        $longTermGoal       = fake()->numberBetween(0, 1);
        $receivedAwards     = fake()->numberBetween(0, 5);
        $academics          = fake()->numberBetween(0, 4);
        $otherOrganizations = fake()->numberBetween(0, 5);
        $volunteerEvents    = fake()->numberBetween(0, 10);
        $careerProgression  = fake()->numberBetween(0, 10);
        $essayOne           = fake()->numberBetween(0, 20);
        $essayTwo           = fake()->numberBetween(0, 20);

        return [
            'scholarship_application_id'   => ScholarshipApplication::factory(),
            'user_id'                      => User::factory(),
            'status'                       => GradeStatus::Active->value,
            'short_term_goal_grade'        => $shortTermGoal,
            'short_term_goal_comments'     => fake()->sentence(),
            'long_term_goal_grade'         => $longTermGoal,
            'long_term_goal_comments'      => fake()->sentence(),
            'received_awards_grade'        => $receivedAwards,
            'received_awards_comments'     => fake()->sentence(),
            'academics_grade'              => $academics,
            'academics_comments'           => fake()->sentence(),
            'other_organizations_grade'    => $otherOrganizations,
            'other_organizations_comments' => fake()->sentence(),
            'volunteer_events_grade'       => $volunteerEvents,
            'volunteer_events_comments'    => fake()->sentence(),
            'career_progression_grade'     => $careerProgression,
            'career_progression_comments'  => fake()->sentence(),
            'essay_one_grade'              => $essayOne,
            'essay_one_comments'           => fake()->sentence(),
            'essay_two_grade'              => $essayTwo,
            'essay_two_comments'           => fake()->sentence(),
            'final_score'                  => $shortTermGoal + $longTermGoal + $receivedAwards + $academics + $otherOrganizations + $volunteerEvents + $careerProgression + $essayOne + $essayTwo,
        ];
    }

    public function superseded(): static
    {
        return $this->state(['status' => GradeStatus::Superseded->value]);
    }
}
