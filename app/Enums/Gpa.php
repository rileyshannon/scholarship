<?php

namespace App\Enums;

enum Gpa: string
{
    case TwoOrLower = 'two_or_lower';
    case TwoToTwoFive = 'two_to_two_five';
    case TwoFiveToThree = 'two_five_to_three';
    case ThreeToThreeTwoFive = 'three_to_three_two_five';
    case ThreeTwoFiveToThreeFive = 'three_two_five_to_three_five';
    case ThreeFiveToThreeSevenFive = 'three_five_to_three_seven_five';
    case ThreeSevenFiveToFour = 'three_seven_five_to_four';

    public function label(): string
    {
        return match($this) {
            self::TwoOrLower => '2.000 or Lower',
            self::TwoToTwoFive => '2.001 - 2.500',
            self::TwoFiveToThree => '2.501 - 3.000',
            self::ThreeToThreeTwoFive => '3.001 - 3.250',
            self::ThreeTwoFiveToThreeFive => '3.251 - 3.500',
            self::ThreeFiveToThreeSevenFive => '3.501 - 3.750',
            self::ThreeSevenFiveToFour => '3.751 - 4.000',
        };
    }
}
