<?php

namespace App\Enums;

enum State: string
{
    case Alabama = 'AL';

    public function label(): string
    {
        return match ($this) {
            self::Alabama => 'Alabama',
        };
    }
}
