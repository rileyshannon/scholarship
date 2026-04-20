<?php

namespace App\Enums;

enum FaqType: string
{
    case Faq = 'faq';
    case Eligibility = 'eligibility';

    public function label(): string
    {
        return match($this) {
            self::Faq => 'FAQ',
            self::Eligibility => 'Eligibility',
        };
    }
}
