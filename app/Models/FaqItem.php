<?php

namespace App\Models;

use App\Enums\FaqType;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class FaqItem extends Model
{
    use HasUlids;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'type' => FaqType::class,
        ];
    }
}
