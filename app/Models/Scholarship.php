<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Scholarship extends Model
{
    use HasFactory, HasUlids;

    protected $guarded = [];

    public function applications(): HasMany
    {
        return $this->hasMany(ScholarshipApplication::class);
    }

    protected function casts(): array
    {
        return [
            'opens_at' => 'datetime',
            'closes_at' => 'datetime',
            'award_date' => 'datetime',
            'is_active' => 'boolean',
        ];
    }
}
