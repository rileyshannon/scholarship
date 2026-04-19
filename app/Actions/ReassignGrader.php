<?php

namespace App\Actions;

use App\Enums\GradeStatus;
use App\Models\ScholarshipApplication;
use App\Models\User;

class ReassignGrader
{
    public function handle(ScholarshipApplication $application, User $oldGrader, User $newGrader): void
    {
        $application->grades()
            ->where('user_id', $oldGrader->id)
            ->where('status', GradeStatus::Active)
            ->update(['status' => GradeStatus::Superseded]);

        $application->graders()->detach($oldGrader->id);
        $application->graders()->attach($newGrader->id, ['assigned_at' => now()]);
    }
}
