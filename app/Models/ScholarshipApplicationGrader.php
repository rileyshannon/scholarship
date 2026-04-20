<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ScholarshipApplicationGrader extends Pivot
{
    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
        ];
    }
}
