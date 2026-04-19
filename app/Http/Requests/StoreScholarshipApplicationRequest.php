<?php

namespace App\Http\Requests;

use App\Enums\EducationLevel;
use App\Enums\FlightInstruction;
use App\Enums\FlightTraining;
use App\Enums\Gender;
use App\Enums\Gpa;
use App\Enums\Reference;
use App\Models\Scholarship;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreScholarshipApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $scholarship = Scholarship::where('is_active', true)->first();

        if (!$scholarship) {
            return false;
        }

        return now()->isBetween($scholarship->opens_at, $scholarship->closes_at);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Personal Info
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'max:2'],
            'gender' => ['required', new Enum(Gender::class)],

            // Membership
            'ppot_member' => ['required', 'boolean'],
            'ppot_mentor' => ['nullable', 'string', 'max:255'],
            'prior_applicant' => ['required', 'boolean'],
            'reference' => ['required', new Enum(Reference::class)],

            // Flight Training
            'flight_school' => ['required', 'string', 'max:255'],
            'flight_training' => ['required', new Enum(FlightTraining::class)],
            'total_time' => ['required', 'string'],
            'flight_instruction' => ['required', new Enum(FlightInstruction::class)],

            // Education
            'education_level' => ['required', new Enum(EducationLevel::class)],
            'school' => ['required', 'string', 'max:255'],
            'graduation_month' => ['required', 'string'],
            'graduation_year' => ['required', 'integer', 'min:1980', 'max:2032'],
            'gpa' => ['required', new Enum(Gpa::class)],
            'academics' => ['required', 'string', 'max:3000'],

            // Goals & Experience
            'short_term_goal' => ['required', 'string', 'max:3000'],
            'long_term_goal' => ['required', 'string', 'max:3000'],
            'career_aspirations' => ['required', 'string', 'max:3000'],
            'has_received_awards' => ['required', 'boolean'],
            'received_awards' => ['nullable', 'required_if:has_received_awards,true', 'string', 'max:3000'],
            'other_organizations' => ['nullable', 'string', 'max:3000'],
            'volunteer_events' => ['nullable', 'string', 'max:3000'],

            // Employment
            'employment_histories' => ['nullable', 'array', 'max:5'],
            'employment_histories.*.employer_name' => ['required_with:employment_histories', 'string', 'max:255'],
            'employment_histories.*.position' => ['required_with:employment_histories', 'string', 'max:255'],
            'employment_histories.*.length' => ['required_with:employment_histories', 'string'],

            // Essays
            'essay_one' => ['required', 'string', 'max:5000'],
            'essay_two' => ['required', 'string', 'max:5000'],
        ];
    }
}
