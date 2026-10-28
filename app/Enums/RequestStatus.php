<?php

namespace App\Enums;

enum RequestStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Screening = 'screening';
    case ScreeningRevision = 'screening_revision';
    case ReviewRouting = 'review_routing';
    case FullReviewPending = 'full_review_pending';
    case AssigningReviewers = 'assigning_reviewers';
    case UnderReview = 'under_review';
    case RevisionsRequested = 'revisions_requested';
    case Approved = 'approved';
    case Disapproved = 'disapproved';
    case HostCountryRequired = 'host_country_required';
    case OutOfScope = 'out_of_scope';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Submitted => 'Submitted',
            self::Screening => 'Screening',
            self::ScreeningRevision => 'Screening Revision',
            self::ReviewRouting => 'Review Routing',
            self::FullReviewPending => 'Full Review Pending',
            self::AssigningReviewers => 'Assigning Reviewers',
            self::UnderReview => 'Under Review',
            self::RevisionsRequested => 'Revisions Requested',
            self::Approved => 'Approved',
            self::Disapproved => 'Disapproved',
            self::HostCountryRequired => 'Host Country Required',
            self::OutOfScope => 'Out of Scope',
            self::Archived => 'Archived',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Draft => 'bg-neutral-100 text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300',
            self::Submitted, self::Screening => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
            self::ScreeningRevision, self::RevisionsRequested => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300',
            self::ReviewRouting, self::FullReviewPending, self::AssigningReviewers, self::UnderReview => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
            self::Approved => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300',
            self::Disapproved, self::OutOfScope => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
            self::HostCountryRequired => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
            self::Archived => 'bg-neutral-200 text-neutral-800 dark:bg-neutral-700 dark:text-neutral-300',
        };
    }
}
