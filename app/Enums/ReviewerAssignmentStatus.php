<?php

namespace App\Enums;

enum ReviewerAssignmentStatus: string
{
    case Invited = 'invited';
    case Accepted = 'accepted';
    case Declined = 'declined';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Invited => 'Invited',
            self::Accepted => 'Accepted',
            self::Declined => 'Declined',
            self::Completed => 'Completed',
        };
    }
}
