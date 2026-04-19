<?php

namespace App\Enums;

enum GradeStatus: string
{
    case Active = 'active';
    case Superseded = 'superseded';

    public function label(): string
    {
        return match($this) {
            self::Active => 'Active',
            self::Superseded => 'Superseded',
        };
    }
}
