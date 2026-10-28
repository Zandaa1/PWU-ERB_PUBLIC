<?php

namespace App\Enums;

enum ResearchDomain: string
{
    case Health = 'health';
    case Social = 'social';
    case ArtsMusic = 'arts_music';
    case EngineeringScience = 'engineering_science';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Health => 'Medical / Health',
            self::Social => 'Social Research',
            self::ArtsMusic => 'Arts / Music',
            self::EngineeringScience => 'Engineering / Science',
            self::Other => 'Other / Non-covered',
        };
    }
}
