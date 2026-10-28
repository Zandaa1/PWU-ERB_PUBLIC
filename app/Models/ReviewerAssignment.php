<?php

namespace App\Models;

use App\Enums\ReviewerAssignmentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewerAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'ethics_request_id',
        'user_id',
        'status',
        'invited_at',
        'responded_at',
        'decline_reason',
        'recommendation',
        'consent_assessment_path',
        'protocol_assessment_path',
        'reviewer_notes',
        'completed_at',
    ];

    protected $casts = [
        'status' => ReviewerAssignmentStatus::class,
        'invited_at' => 'datetime',
        'responded_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function ethicsRequest(): BelongsTo
    {
        return $this->belongsTo(EthicsRequest::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
