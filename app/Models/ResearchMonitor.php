<?php

namespace App\Models;

use App\Enums\MonitoringStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResearchMonitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'ethics_request_id',
        'monitoring_status',
        'approved_at',
        'expires_at',
        'last_report_submitted_at',
        'next_report_due_at',
        'notes',
    ];

    protected $casts = [
        'monitoring_status' => MonitoringStatus::class,
        'approved_at' => 'datetime',
        'expires_at' => 'datetime',
        'last_report_submitted_at' => 'datetime',
        'next_report_due_at' => 'datetime',
    ];

    public function ethicsRequest(): BelongsTo
    {
        return $this->belongsTo(EthicsRequest::class);
    }
}
