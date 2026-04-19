<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreScholarshipApplicationGradeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $application = $this->route('application');

        return $application->graders->contains(auth()->user());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'short_term_goal_grade' => ['required', 'integer', 'min:0', 'max:20'],
            'short_term_goal_comments' => ['nullable', 'string', 'max:1000'],
            'long_term_goal_grade' => ['required', 'integer', 'min:0', 'max:20'],
            'long_term_goal_comments' => ['nullable', 'string', 'max:1000'],
            'received_awards_grade' => ['required', 'integer', 'min:0', 'max:20'],
            'received_awards_comments' => ['nullable', 'string', 'max:1000'],
            'academics_grade' => ['required', 'integer', 'min:0', 'max:20'],
            'academics_comments' => ['nullable', 'string', 'max:1000'],
            'other_organizations_grade' => ['required', 'integer', 'min:0', 'max:20'],
            'other_organizations_comments' => ['nullable', 'string', 'max:1000'],
            'volunteer_events_grade' => ['required', 'integer', 'min:0', 'max:20'],
            'volunteer_events_comments' => ['nullable', 'string', 'max:1000'],
            'career_progression_grade' => ['required', 'integer', 'min:0', 'max:20'],
            'career_progression_comments' => ['nullable', 'string', 'max:1000'],
            'essay_one_grade' => ['required', 'integer', 'min:0', 'max:20'],
            'essay_one_comments' => ['nullable', 'string', 'max:1000'],
            'essay_two_grade' => ['required', 'integer', 'min:0', 'max:20'],
            'essay_two_comments' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
