<?php

namespace App\Enums;

enum Reference: string
{
    case Website = 'website';
    case Email = 'email';
    case PPOTMentor = 'ppot_mentor';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Website => 'Website',
            self::Email => 'Email',
            self::PPOTMentor => 'PPOT Mentor',
            self::Other => "Other",
        };
    }
}
