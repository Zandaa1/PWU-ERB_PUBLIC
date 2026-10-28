<?php

namespace App\Enums;

enum ReviewType: string
{
    case FullReview = 'full_review';
    case ExpeditedReview = 'expedited_review';

    public function label(): string
    {
        return match ($this) {
            self::FullReview => 'Full Review',
            self::ExpeditedReview => 'Expedited Review',
        };
    }
}
