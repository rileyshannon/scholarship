<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScholarshipApplicationGrade extends Model
{
    use HasFactory, HasUlids;

    protected $guarded = [];

    public function application(): BelongsTo
    {
        return $this->belongsTo(ScholarshipApplication::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'final_score' => 'integer',
            'short_term_goal_grade' => 'integer',
            'long_term_goal_grade' => 'integer',
            'received_awards_grade' => 'integer',
            'academics_grade' => 'integer',
            'other_organizations_grade' => 'integer',
            'volunteer_events_grade' => 'integer',
            'career_progression_grade' => 'integer',
            'essay_one_grade' => 'integer',
            'essay_two_grade' => 'integer',
        ];
    }
}
