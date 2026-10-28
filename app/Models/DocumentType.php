<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

/**
 * DocumentType model defining requirements, templates, and sort orders for ethics protocols.
 *
 * Invariants:
 * - 8 institutional core documents are permanently locked from deletion.
 * - Raw attribute array hydration caching ensures multi-process isolation and zero unserialization faults.
 */
class DocumentType extends Model
{
    public const CORE_TYPES = [
        'Study Protocol',
        'Data Collection Forms (questionnaires) & CV of PI',
        'Official Receipt of ethics review fee',
        'Proof of Anti-Plagiarism Report and AI Content Report',
        'Certification of Approved Title and Compliance of Revisions after Proposal Defense',
        'Informed Consent Form (English)',
        'Informed Consent Form (Tagalog)',
        'Additional / Supplementary Thesis File (optional)',
    ];

    protected $fillable = [
        'name',
        'is_required',
        'allow_multiple',
        'is_active',
        'template_file_path',
        'sort_order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'allow_multiple' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $appends = [
        'template_url',
    ];

    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('document_types:active');
            Cache::forget('document_types:required');
        });

        static::deleted(function () {
            Cache::forget('document_types:active');
            Cache::forget('document_types:required');
        });
    }

    /**
     * Determine if this document type is an institutional core requirement (locked).
     */
    public function isCore(): bool
    {
        return in_array($this->name, self::CORE_TYPES, true);
    }

    /**
     * Get the accessible URL for the template (Google Docs URL or local storage URL).
     */
    public function getTemplateUrlAttribute(): ?string
    {
        if (! $this->template_file_path) {
            return null;
        }

        if (str_starts_with($this->template_file_path, 'http://') || str_starts_with($this->template_file_path, 'https://')) {
            return $this->template_file_path;
        }

        return Storage::url($this->template_file_path);
    }

    /**
     * Check if the template is a Google Docs / Drive link.
     */
    public function getIsGoogleDocAttribute(): bool
    {
        return $this->template_file_path && (
            str_starts_with($this->template_file_path, 'http://') || str_starts_with($this->template_file_path, 'https://')
        );
    }

    /**
     * Retrieve all active document types ordered by display hierarchy.
     *
     * @return Collection<int, static>
     */
    public static function getActive(): Collection
    {
        $attributes = Cache::remember('document_types:active', now()->addDay(), function () {
            return static::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get()->map(fn ($d) => $d->getAttributes())->all();
        });

        if (! is_array($attributes)) {
            Cache::forget('document_types:active');
            $attributes = static::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get()->map(fn ($d) => $d->getAttributes())->all();
        }

        return static::hydrate($attributes);
    }

    /**
     * Retrieve only mandatory active document types.
     *
     * @return Collection<int, static>
     */
    public static function getRequired(): Collection
    {
        $attributes = Cache::remember('document_types:required', now()->addDay(), function () {
            return static::where('is_active', true)->where('is_required', true)->orderBy('sort_order')->get()->map(fn ($d) => $d->getAttributes())->all();
        });

        if (! is_array($attributes)) {
            Cache::forget('document_types:required');
            $attributes = static::where('is_active', true)->where('is_required', true)->orderBy('sort_order')->get()->map(fn ($d) => $d->getAttributes())->all();
        }

        return static::hydrate($attributes);
    }

    public function requestDocuments(): HasMany
    {
        return $this->hasMany(RequestDocument::class);
    }
}
