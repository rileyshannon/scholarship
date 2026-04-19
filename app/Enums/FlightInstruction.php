<?php

namespace App\Enums;

enum FlightInstruction: string
{
    case None = 'none';
    case DiscoveryFlight = 'discovery_flight';
    case OneToFiftyTwelveMonths = 'one_to_fifty_twelve_months';
    case OneToFiftySixMonths = 'one_to_fifty_six_months';
    case FiftyPlusTwelveMonths = 'fifty_plus_twelve_months';
    case FiftyPlusSixMonths = 'fifty_plus_six_months';
    case CertificateTwelveMonths = 'certificate_twelve_months';
    case CertificateSixMonths = 'certificate_six_months';

    public function label(): string
    {
        return match($this) {
            self::None => 'No Flight Instruction Received Or Given Within The Previous 12 Months',
            self::DiscoveryFlight => 'Participated In A Discovery Flight Or Similar Within The Previous 6 Months',
            self::OneToFiftyTwelveMonths => 'Received Or Given 1-50 Hours Of Flight Instruction Within The Previous 12 Months',
            self::OneToFiftySixMonths => 'Received Or Given 1-50 Hours Of Flight Instruction Within The Previous 6 Months',
            self::FiftyPlusTwelveMonths => 'Received Or Given 51 Or More Hours Of Flight Instruction Within The Previous 12 Months',
            self::FiftyPlusSixMonths => 'Received Or Given 51 Or More Hours Of Flight Instruction Within The Previous 6 Months',
            self::CertificateTwelveMonths => 'Earned A Flight Certificate Or Rating Within The Previous 12 Months',
            self::CertificateSixMonths => 'Earned A Flight Certificate Or Rating Within The Previous 6 Months',
        };
    }
}
