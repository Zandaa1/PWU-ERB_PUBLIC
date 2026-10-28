<?php

namespace App\Enums;

enum DocumentStatus: string
{
    case Pending = 'pending';
    case UnderReview = 'under_review';
    case RevisionsRequested = 'revisions_requested';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::UnderReview => 'Under Review',
            self::RevisionsRequested => 'Revisions Requested',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
        };
    }
}
