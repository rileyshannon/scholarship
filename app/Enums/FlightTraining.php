<?php

namespace App\Enums;

enum FlightTraining: string
{
    case NoExperience = 'no_experience';
    case StudentNotSoloed = 'student_not_soloed';
    case StudentSoloed = 'student_soloed';
    case PrivatePilot = 'private_pilot';
    case TrainingInstrument = 'training_instrument';
    case PrivateWithInstrument = 'private_with_instrument';
    case TrainingCommercial = 'training_commercial';
    case CommercialCompleted = 'commercial_completed';
    case CfiCompleted = 'cfi_completed';

    public function label(): string
    {
        return match($this) {
            self::NoExperience => 'No Flight Training Experience',
            self::StudentNotSoloed => 'Student Pilot (Have NOT Completed First Solo)',
            self::StudentSoloed => 'Student Pilot (Have Completed First Solo)',
            self::PrivatePilot => 'Private Pilot Certificate Completed',
            self::TrainingInstrument => 'Training For Instrument Rating',
            self::PrivateWithInstrument => 'Private Pilot Certificate With Instrument Rating Completed',
            self::TrainingCommercial => 'Training For Commercial Pilot Certificate',
            self::CommercialCompleted => 'Commercial Pilot Certificate Completed',
            self::CfiCompleted => 'Training For or Completed CFI/CFII/MEI',
        };
    }
}
