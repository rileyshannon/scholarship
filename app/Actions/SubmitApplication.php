<?php

namespace App\Actions;

use App\Mail\ApplicationReceived;
use App\Models\Scholarship;
use App\Models\ScholarshipApplication;
use Illuminate\Support\Facades\Mail;

class SubmitApplication
{
    public function handle(array $data): ScholarshipApplication
    {
        $scholarship = Scholarship::where('is_active', true)->firstOrFail();

        $application = ScholarshipApplication::create([
            ...$data,
            'scholarship_id' => $scholarship->id,
            'auto_score' => (new CalculateAutoScore)->handle($data),
        ]);

        foreach ($data['employment_histories'] as $employment) {
            if (!empty($employment['employer_name'])) {
                $application->employmentHistories()->create($employment);
            }
        }

        try {
            (new AssignGradersToApplication)->handle($application);
        } catch (\RuntimeException $e) {
            // TODO: Notify admin?
        }

        Mail::to($application->email)->queue(new ApplicationReceived($application));
    }
}
