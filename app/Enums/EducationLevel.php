<?php

namespace App\Enums;

enum EducationLevel: string
{
    case HighSchool = 'high_school';
    case TwoYear = 'two_year';
    case FourYear = 'four_year';
    case Bachelors = 'bachelors';
    case Masters = 'masters';

    public function label(): string
    {
        return match($this) {
            self::HighSchool => 'High School Student/High School Diploma/GED',
            self::TwoYear => 'Enrolled in a Two-Year Degree Program',
            self::FourYear => 'Enrolled in a Four-Year Degree Program/Hold an Associate\'s Degree',
            self::Bachelors => 'Hold a Bachelor\'s Degree',
            self::Masters => 'Hold a Master\'s Degree or Higher',
        };
    }
}
