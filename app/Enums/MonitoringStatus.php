<?php

namespace App\Enums;

enum MonitoringStatus: string
{
    case Active = 'active';
    case RenewalDue = 'renewal_due';
    case ProgressReportDue = 'progress_report_due';
    case HostCountryPending = 'host_country_pending';
    case Completed = 'completed';
    case Terminated = 'terminated';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::RenewalDue => 'Renewal Due',
            self::ProgressReportDue => 'Progress Report Due',
            self::HostCountryPending => 'Host Country Clearance Pending',
            self::Completed => 'Completed',
            self::Terminated => 'Terminated',
        };
    }
}
