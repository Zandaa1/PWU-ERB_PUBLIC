<?php

namespace App\Data;

use Illuminate\Http\UploadedFile;

final readonly class SubmissionInput
{
    /**
     * @param  array<int, UploadedFile|null>  $uploads  keyed by document_type_id
     */
    public function __construct(
        public string $title,
        public ?string $coAuthors,
        public int $departmentId,
        public int $studentId,
        public array $uploads,
        public bool $privacyConsentAccepted,
        public ?string $researchDomain = null,
        public bool $isLocal = true,
        public ?string $hostCountry = null,
    ) {}

    public static function fromLivewire(object $component, int $studentId): self
    {
        $uploads = $component->uploads ?? [];
        for ($i = 1; $i <= 30; $i++) {
            if (isset($component->{"doc_{$i}"}) && $component->{"doc_{$i}"} !== null) {
                $uploads[$i] = $component->{"doc_{$i}"};
            }
        }

        $privacyConsentAccepted = (bool) ($component->privacyConsentAccepted ?? false);

        return new self(
            title: $component->title,
            coAuthors: $component->co_authors ?: null,
            departmentId: (int) $component->department_id,
            studentId: $studentId,
            uploads: $uploads,
            privacyConsentAccepted: $privacyConsentAccepted,
            researchDomain: $component->research_domain ?: null,
            isLocal: (bool) ($component->is_local ?? true),
            hostCountry: $component->host_country ?: null,
        );
    }
}
