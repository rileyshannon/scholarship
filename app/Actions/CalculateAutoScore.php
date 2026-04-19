<?php

namespace App\Actions;

use App\Enums\EducationLevel;
use App\Enums\FlightInstruction;
use App\Enums\Gpa;

class CalculateAutoScore
{
    public function handle(array $data): int
    {
        return $this->sectionOne($data) + $this->sectionTwo($data) + $this->sectionThree($data);
    }

    private function sectionOne(array $data): int
    {
        $score = 0;
        if ($data['ppot_member']) $score += 2;
        if ($data['prior_applicant']) $score += 3;
        return $score;
    }

    private function sectionTwo(array $data): int
    {
        return match($data['flight_instruction']) {
            FlightInstruction::DiscoveryFlight->value => 2,
            FlightInstruction::OneToFiftyTwelveMonths->value => 4,
            FlightInstruction::OneToFiftySixMonths->value => 5,
            FlightInstruction::FiftyPlusTwelveMonths->value,
            FlightInstruction::CertificateTwelveMonths->value => 6,
            FlightInstruction::FiftyPlusSixMonths->value,
            FlightInstruction::CertificateSixMonths->value => 7,
            default => 0,
        };
    }

    private function sectionThree(array $data): int
    {
        return $this->educationScore($data['education_level']) + $this->gpaScore($data['gpa']);
    }

    private function educationScore(string $level): int
    {
        return match($level) {
            EducationLevel::HighSchool->value => 1,
            EducationLevel::TwoYear->value => 2,
            EducationLevel::FourYear->value => 3,
            EducationLevel::Bachelors->value => 4,
            EducationLevel::Masters->value => 5,
            default => 0,
        };
    }

    private function gpaScore(string $gpa): int
    {
        return match($gpa) {
            Gpa::TwoOrLower->value => 0,
            Gpa::TwoToTwoFive->value => 1,
            Gpa::TwoFiveToThree->value => 2,
            Gpa::ThreeToThreeTwoFive->value => 3,
            Gpa::ThreeTwoFiveToThreeFive->value => 4,
            Gpa::ThreeFiveToThreeSevenFive->value => 5,
            Gpa::ThreeSevenFiveToFour->value => 6,
            default => 0,
        };
    }
}
