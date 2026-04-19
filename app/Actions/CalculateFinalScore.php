<?php

namespace App\Actions;

use App\Models\ScholarshipApplication;

class CalculateFinalScore
{
    public function handle(ScholarshipApplication $application): void
    {
        $grades = $application->grades;

        if ($grades->count() < 3) {
            return;
        }

        $final = $grades->average(
            fn($grade) => $grade->final_score + $application->auto_score
        );
        
        $application->update(['final_score' => (int) round($final)]);
    }
}
