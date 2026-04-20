<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScholarshipApplication extends Model
{
    use HasFactory, HasUlids;

    protected $guarded = [];

    public function scholarship(): BelongsTo
    {
        return $this->belongsTo(Scholarship::class);
    }

    public function gradingGroup(): BelongsTo
    {
        return $this->belongsTo(GradingGroup::class);
    }

    public function employmentHistories(): HasMany
    {
        return $this->hasMany(EmploymentHistory::class);
    }

    public function graders(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'scholarship_application_grader')
            ->withPivot('assigned_at')
            ->using(ScholarshipApplicationGrader::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(ScholarshipApplicationGrade::class);
    }

    protected function casts(): array
    {
        return [
            'ppot_member' => 'boolean',
            'prior_applicant' => 'boolean',
            'has_received_awards' => 'boolean',
            'graduation_year' => 'integer',
            'auto_score' => 'integer',
            'final_score' => 'integer',
        ];
    }
}
