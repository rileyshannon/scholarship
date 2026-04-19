<?php

namespace App\Enums;

enum ApplicationStatus: string
{
    case Pending = 'pending';
    case InReview = 'in_review';
    case Completed = 'completed';
    case Flagged = 'flagged';
    case Disqualified = 'disqualified';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::InReview => 'In Review',
            self::Completed => 'Completed',
            self::Flagged => 'Flagged',
            self::Disqualified => 'Disqualified',
        };
    }
}
