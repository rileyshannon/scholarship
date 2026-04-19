<?php

namespace App\Actions;

use App\Models\GradingGroup;
use App\Models\ScholarshipApplication;

class AssignGradersToApplication
{
    public function handle(ScholarshipApplication $application): void
    {
        $group = $this->nextAvailableGroup();

        if (!$group) {
            throw new \RuntimeException('No grading groups with enough graders available.');
        }

        $graders = $group->users()->inRandomOrder()->take(3)->get();

        $application->update(['grading_group_id' => $group->id]);

        $application->graders()->attach(
            $graders->mapWithKeys(fn ($grader) => [
                $grader->id => ['assigned_at' => now()]
            ])->all()
        );
    }

    private function nextAvailableGroup(): ?GradingGroup
    {
        return GradingGroup::whereHas('users', fn($q) => $q, '>=', 3)
            ->withCount('applications')
            ->orderBy('applications_count')
            ->first();
    }
}
