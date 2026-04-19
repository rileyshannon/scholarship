<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScholarshipApplicationGrade extends Model
{
    use HasUlids;

    protected $guarded = [];

    public function application(): BelongsTo
    {
        return $this->belongsTo(ScholarshipApplication::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
