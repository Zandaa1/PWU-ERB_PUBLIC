<?php

namespace App\Models;

use App\Enums\DocumentStatus;
use App\Enums\RequestStatus;
use App\Enums\ResearchDomain;
use App\Enums\ReviewType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Cache;

class EthicsRequest extends Model
{
    protected $fillable = [
        'student_id',
        'title',
        'co_authors',
        'department_id',
        'status',
        'research_domain',
        'is_local',
        'host_country',
        'review_type',
        'plagiarism_similarity_score',
        'ai_similarity_score',
        'plagiarism_verified_at',
        'screened_by',
        'screening_notes',
        'deliberation_notes',
        'host_country_cert_path',
        'control_number',
        'approval_date',
        'expiry_date',
        'certificate_path',
        'last_activity_at',
    ];

    public function getPrincipalInvestigatorDisplayAttribute(): string
    {
        if (! empty($this->co_authors)) {
            return $this->student->name.' et al.';
        }

        return $this->student->name;
    }

    protected $casts = [
        'status' => RequestStatus::class,
        'research_domain' => ResearchDomain::class,
        'review_type' => ReviewType::class,
        'is_local' => 'boolean',
        'plagiarism_similarity_score' => 'decimal:2',
        'ai_similarity_score' => 'decimal:2',
        'plagiarism_verified_at' => 'datetime',
        'approval_date' => 'date',
        'expiry_date' => 'date',
        'last_activity_at' => 'datetime',
    ];

    public function reviewerAssignments(): HasMany
    {
        return $this->hasMany(ReviewerAssignment::class);
    }

    public function researchMonitor(): HasOne
    {
        return $this->hasOne(ResearchMonitor::class);
    }

    public function screenedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'screened_by');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function requestDocuments()
    {
        return $this->hasMany(RequestDocument::class);
    }

    /**
     * Get active/applicable request documents.
     * Includes all required document types + any optional document types that have files uploaded.
     */
    public function getApplicableDocuments()
    {
        return $this->requestDocuments->filter(function ($doc) {
            $isRequired = $doc->documentType?->is_required ?? true;
            if ($isRequired) {
                return true;
            }

            if ($doc->relationLoaded('versions')) {
                return $doc->versions->isNotEmpty();
            }

            return $doc->latestVersion !== null || $doc->versions()->exists();
        });
    }

    public function getApplicableDocumentsCountAttribute(): int
    {
        return $this->getApplicableDocuments()->count();
    }

    public function getApprovedDocumentsCountAttribute(): int
    {
        return $this->getApplicableDocuments()->where('status', DocumentStatus::Approved)->count();
    }

    public function getRevisionDocumentsCountAttribute(): int
    {
        return $this->getApplicableDocuments()->where('status', DocumentStatus::RevisionsRequested)->count();
    }

    public function hasResubmittedDocuments(): bool
    {
        return $this->requestDocuments->contains(function ($doc) {
            return $doc->isResubmission() && $doc->status === DocumentStatus::Pending;
        });
    }

    public function statusLogs()
    {
        return $this->hasMany(StatusLog::class)->latest();
    }

    protected static function booted(): void
    {
        static::saved(function (EthicsRequest $request) {
            Cache::forget("student_dashboard:requests:{$request->student_id}");
            Cache::forget("student_dashboard:popup:{$request->student_id}");
        });

        static::deleted(function (EthicsRequest $request) {
            Cache::forget("student_dashboard:requests:{$request->student_id}");
            Cache::forget("student_dashboard:popup:{$request->student_id}");
        });
    }
}
