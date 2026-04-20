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
        $grades = collect([
            'short_term_goal_grade',
            'long_term_goal_grade',
            'received_awards_grade',
            'academics_grade',
            'other_organizations_grade',
            'volunteer_events_grade',
            'career_progression_grade',
            'essay_one_grade',
            'essay_two_grade',
        ])->mapWithKeys(fn ($field) => [$field => fake()->numberBetween(0, 20)]);

        return [
            'scholarship_application_id'  => ScholarshipApplication::factory(),
            'user_id'                     => User::factory(),
            'status'                      => GradeStatus::Active->value,
            'short_term_goal_grade'       => $grades['short_term_goal_grade'],
            'short_term_goal_comments'    => fake()->sentence(),
            'long_term_goal_grade'        => $grades['long_term_goal_grade'],
            'long_term_goal_comments'     => fake()->sentence(),
            'received_awards_grade'       => $grades['received_awards_grade'],
            'received_awards_comments'    => fake()->sentence(),
            'academics_grade'             => $grades['academics_grade'],
            'academics_comments'          => fake()->sentence(),
            'other_organizations_grade'   => $grades['other_organizations_grade'],
            'other_organizations_comments' => fake()->sentence(),
            'volunteer_events_grade'      => $grades['volunteer_events_grade'],
            'volunteer_events_comments'   => fake()->sentence(),
            'career_progression_grade'    => $grades['career_progression_grade'],
            'career_progression_comments' => fake()->sentence(),
            'essay_one_grade'             => $grades['essay_one_grade'],
            'essay_one_comments'          => fake()->sentence(),
            'essay_two_grade'             => $grades['essay_two_grade'],
            'essay_two_comments'          => fake()->sentence(),
            'final_score'                 => $grades->sum(),
        ];
    }

    public function superseded(): static
    {
        return $this->state(['status' => GradeStatus::Superseded->value]);
    }
}
