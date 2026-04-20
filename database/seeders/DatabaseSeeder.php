<?php

namespace Database\Seeders;

use App\Actions\CalculateFinalScore;
use App\Models\FaqItem;
use App\Models\GradingGroup;
use App\Models\Scholarship;
use App\Models\ScholarshipApplication;
use App\Models\ScholarshipApplicationGrade;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create active scholarship
        $scholarship = Scholarship::factory()->create();

        // Create admin
        User::factory()->admin()->create([
            'name'  => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        // Create 4 grading groups with 3 graders each
        $groups = GradingGroup::factory(4)->create();

        $groups->each(function ($group) {
            User::factory(3)->inGroup($group)->create();
        });

        // Create 50 applications spread across groups
        $groups->each(function ($group) use ($scholarship) {
            $graders = $group->users;

            ScholarshipApplication::factory(12)
                ->create([
                    'scholarship_id'   => $scholarship->id,
                    'grading_group_id' => $group->id,
                ])
                ->each(function ($application) use ($graders) {
                    // Attach all 3 graders via pivot
                    $application->graders()->attach(
                        $graders->mapWithKeys(fn ($grader) => [
                            $grader->id => ['assigned_at' => now()]
                        ])->all()
                    );

                    // 70% chance all 3 graders have submitted grades
                    if (fake()->boolean(70)) {
                        $graders->each(function ($grader) use ($application) {
                            ScholarshipApplicationGrade::factory()->create([
                                'scholarship_application_id' => $application->id,
                                'user_id'                    => $grader->id,
                            ]);
                        });

                        (new CalculateFinalScore)->handle($application);
                    }
                });
        });

        $this->call(FaqSeeder::class);
    }
}
